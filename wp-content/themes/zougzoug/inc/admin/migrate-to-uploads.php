<?php
/**
 * Migration : Transférer les images projets vers WordPress Media Library
 *
 * - Copie tous les fichiers de assets/img/projets/ vers wp-content/uploads/projets/
 * - Enregistre chaque fichier (sauf posters) comme attachment WordPress
 * - Met à jour _projet_gallery avec les IDs d'attachment
 * - Définit la thumbnail du post depuis la cover (1er item)
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/migrate-to-uploads.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$theme_dir   = get_template_directory();
$src_base    = $theme_dir . '/assets/img/projets/';
$upload_dir  = wp_upload_dir();
$upload_base = $upload_dir['basedir'];
$upload_url  = $upload_dir['baseurl'];
$dest_base   = $upload_base . '/projets/';

if (!is_dir($dest_base)) {
	wp_mkdir_p($dest_base);
}

echo "=== Migration des images projets vers Media Library ===\n\n";
echo "Source      : $src_base\n";
echo "Destination : $dest_base\n\n";

// ──────────────────────────────────────────────────────
// Étape 1 : Copier TOUS les fichiers + créer attachments
// ──────────────────────────────────────────────────────

$path_to_id      = [];  // relative_path => attachment_id
$total_files     = 0;
$total_created   = 0;
$total_existing  = 0;

$folders = glob($src_base . '*', GLOB_ONLYDIR);

foreach ($folders as $folder) {
	$folder_name = basename($folder);
	$dest_folder = $dest_base . $folder_name . '/';

	if (!is_dir($dest_folder)) {
		wp_mkdir_p($dest_folder);
	}

	echo "--- $folder_name ---\n";

	$files = glob($folder . '/*.*');
	foreach ($files as $file) {
		$filename      = basename($file);
		$relative_path = $folder_name . '/' . $filename;
		$dest_path     = $dest_folder . $filename;

		// 1a. Copier le fichier
		if (!file_exists($dest_path)) {
			copy($file, $dest_path);
			echo "  Copié : $filename (" . round(filesize($dest_path) / 1024) . " KB)\n";
		} else {
			echo "  Existe : $filename\n";
		}
		$total_files++;

		// 1b. Ne PAS créer d'attachment pour les posters (fichiers auxiliaires)
		if (preg_match('/-poster\.(jpg|jpeg|png|webp)$/i', $filename)) {
			echo "    → poster, pas d'attachment\n";
			continue;
		}

		// 1c. Vérifier si l'attachment existe déjà
		$wp_relative = 'projets/' . $relative_path;
		$existing = get_posts([
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $wp_relative,
			'posts_per_page' => 1,
		]);

		if (!empty($existing)) {
			$att_id = $existing[0]->ID;
			$path_to_id[$relative_path] = $att_id;
			$total_existing++;
			echo "    → attachment existant #$att_id\n";
			continue;
		}

		// 1d. Détecter le type MIME
		$filetype = wp_check_filetype($filename);
		$mime_type = $filetype['type'];

		// Fallback pour les types non reconnus
		if (empty($mime_type)) {
			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			$fallback = [
				'webp' => 'image/webp',
				'avif' => 'image/avif',
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'png'  => 'image/png',
				'mp4'  => 'video/mp4',
				'webm' => 'video/webm',
				'mov'  => 'video/quicktime',
			];
			$mime_type = isset($fallback[$ext]) ? $fallback[$ext] : 'application/octet-stream';
		}

		// 1e. Créer l'attachment
		$clean_name = sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME));
		$att_data = [
			'post_title'     => $clean_name,
			'post_mime_type' => $mime_type,
			'post_status'    => 'inherit',
			'post_content'   => '',
		];

		$att_id = wp_insert_attachment($att_data, $dest_path);

		if (is_wp_error($att_id)) {
			echo "    ERREUR : " . $att_id->get_error_message() . "\n";
			continue;
		}

		// Forcer le chemin relatif correct
		update_post_meta($att_id, '_wp_attached_file', $wp_relative);

		// Générer les métadonnées (dimensions, thumbnails pour les images)
		if (strpos($mime_type, 'image/') === 0) {
			$metadata = wp_generate_attachment_metadata($att_id, $dest_path);
			wp_update_attachment_metadata($att_id, $metadata);
		}

		$path_to_id[$relative_path] = $att_id;
		$total_created++;
		echo "    → attachment #$att_id créé ($mime_type)\n";
	}
}

echo "\n========================================\n";
echo "Fichiers copiés    : $total_files\n";
echo "Attachments créés  : $total_created\n";
echo "Attachments existants : $total_existing\n";
echo "Mapping total      : " . count($path_to_id) . " entrées\n\n";

// ──────────────────────────────────────────────────────
// Étape 2 : Mettre à jour les galeries avec les IDs
// ──────────────────────────────────────────────────────

echo "=== Mise à jour des galeries ===\n\n";

$projects = get_posts([
	'post_type'      => 'projet',
	'posts_per_page' => -1,
	'post_status'    => 'any',
]);

$migrated = 0;

foreach ($projects as $project) {
	$gallery = get_post_meta($project->ID, '_projet_gallery', true);
	if (!is_array($gallery) || empty($gallery)) {
		echo "{$project->post_title} : pas de galerie\n";
		continue;
	}

	// Vérifier si déjà migré (premiers éléments sont des entiers)
	if (is_numeric($gallery[0]) && intval($gallery[0]) > 0) {
		echo "{$project->post_title} : déjà migré (IDs)\n";
		continue;
	}

	$new_gallery = [];
	$missing     = [];

	foreach ($gallery as $path) {
		if (isset($path_to_id[$path])) {
			$new_gallery[] = $path_to_id[$path];
		} else {
			$missing[] = $path;
		}
	}

	if (!empty($missing)) {
		echo "{$project->post_title} : MANQUANTS : " . implode(', ', $missing) . "\n";
	}

	// Sauvegarder la galerie avec les IDs
	update_post_meta($project->ID, '_projet_gallery', $new_gallery);

	// Définir la thumbnail du post depuis la cover (1er item image)
	if (!empty($new_gallery)) {
		$thumb_id = null;
		foreach ($new_gallery as $att_id) {
			$mime = get_post_mime_type($att_id);
			if (strpos($mime, 'image/') === 0) {
				$thumb_id = $att_id;
				break;
			}
		}
		if ($thumb_id) {
			set_post_thumbnail($project->ID, $thumb_id);
		}
	}

	$id_list = implode(', ', $new_gallery);
	echo "{$project->post_title} : " . count($new_gallery) . " médias → [$id_list]\n";
	$migrated++;
}

echo "\n========================================\n";
echo "Migration terminée !\n";
echo "Projets migrés : $migrated\n";
echo "\nProchaine étape : mettre à jour cpt-projet.php pour travailler avec les IDs d'attachment.\n";
