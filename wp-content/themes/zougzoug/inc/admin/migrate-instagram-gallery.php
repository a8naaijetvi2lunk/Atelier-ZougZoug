<?php
/**
 * Migration : galerie Instagram (home.json)
 *   - Copie les 20 images galerie-*.webp vers uploads/galerie/
 *   - Optimise : max 1600px, WebP 85%
 *   - Réutilise les doublons existants dans la médiathèque
 *   - Met à jour home.json avec les IDs d'attachment
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/migrate-instagram-gallery.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$MAX_WIDTH = 1600;
$QUALITY   = 85;

$theme_dir   = get_template_directory();
$img_dir     = $theme_dir . '/assets/img/';
$upload_dir  = wp_upload_dir();
$upload_base = $upload_dir['basedir'];
$dest_dir    = $upload_base . '/galerie';

echo "=== Migration galerie Instagram → médiathèque ===\n\n";

// Créer le dossier destination
if (!is_dir($dest_dir)) {
	wp_mkdir_p($dest_dir);
	echo "Dossier créé : uploads/galerie/\n";
}

// Charger home.json
$json_path = $theme_dir . '/data/home.json';
$data = json_decode(file_get_contents($json_path), true);
if (!$data || empty($data['instagram']['images'])) {
	echo "ERREUR : home.json introuvable ou pas de section instagram\n";
	exit(1);
}

// Construire le hash map des images existantes dans la médiathèque
$existing_atts = get_posts([
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => -1,
	'post_mime_type' => 'image',
]);

$hash_map = [];
foreach ($existing_atts as $a) {
	$path = get_attached_file($a->ID);
	if ($path && file_exists($path)) {
		$hash_map[md5_file($path)] = $a->ID;
	}
}

echo "Images existantes en médiathèque : " . count($hash_map) . "\n\n";

$stats = [
	'migrated'  => 0,
	'reused'    => 0,
	'optimized' => 0,
	'errors'    => 0,
];

// Traiter chaque image
foreach ($data['instagram']['images'] as $idx => &$img) {
	$src      = $img['src'];
	$alt      = $img['alt'] ?? '';
	$src_path = $img_dir . $src;

	if (!file_exists($src_path)) {
		echo "  ERREUR : $src introuvable\n";
		$stats['errors']++;
		continue;
	}

	$hash = md5_file($src_path);

	// Vérifier si doublon avec une image existante
	if (isset($hash_map[$hash])) {
		$att_id = $hash_map[$hash];
		$img['attachment_id'] = $att_id;
		unset($img['src']);
		echo "  $src → DOUBLON → attachment #{$att_id}\n";
		$stats['reused']++;
		continue;
	}

	// Copier vers uploads/galerie/
	$dest_path = $dest_dir . '/' . $src;
	copy($src_path, $dest_path);

	// Optimiser si nécessaire (resize + qualité)
	$size_info = getimagesize($dest_path);
	$width     = $size_info[0];
	$needs_optimize = ($width > $MAX_WIDTH);

	if ($needs_optimize) {
		$editor = wp_get_image_editor($dest_path);
		if (!is_wp_error($editor)) {
			$editor->resize($MAX_WIDTH, null);
			$editor->set_quality($QUALITY);
			$result = $editor->save($dest_path, 'image/webp');
			if (!is_wp_error($result)) {
				// Le save peut créer un fichier avec dimensions dans le nom
				if ($result['path'] !== $dest_path) {
					if (file_exists($dest_path)) unlink($dest_path);
					rename($result['path'], $dest_path);
				}
				$new_size = $result['width'] . 'x' . $result['height'];
				$old_kb = round(filesize($src_path) / 1024);
				$new_kb = round(filesize($dest_path) / 1024);
				echo "  $src : {$width}px → {$result['width']}px ({$old_kb}KB → {$new_kb}KB)";
				$stats['optimized']++;
			} else {
				echo "  $src : ERREUR optimisation — " . $result->get_error_message();
			}
		}
	} else {
		$old_kb = round(filesize($src_path) / 1024);
		echo "  $src : OK ({$width}px, {$old_kb}KB)";
	}

	// Enregistrer comme attachment WordPress
	$file_rel  = 'galerie/' . $src;
	$att_data  = [
		'post_title'     => sanitize_file_name(pathinfo($src, PATHINFO_FILENAME)),
		'post_mime_type' => 'image/webp',
		'post_status'    => 'inherit',
	];

	$att_id = wp_insert_attachment($att_data, $dest_path);
	if (is_wp_error($att_id)) {
		echo " → ERREUR insertion\n";
		$stats['errors']++;
		continue;
	}

	// Mettre à jour le chemin relatif
	update_post_meta($att_id, '_wp_attached_file', $file_rel);

	// Générer les métadonnées (thumbnails etc.)
	$meta = wp_generate_attachment_metadata($att_id, $dest_path);
	wp_update_attachment_metadata($att_id, $meta);

	// Mettre à jour l'alt text
	if ($alt) {
		update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
	}

	$img['attachment_id'] = $att_id;
	unset($img['src']);
	echo " → attachment #{$att_id}\n";
	$stats['migrated']++;

	// Ajouter au hash map pour détecter les doublons entre galerie-*.webp elles-mêmes
	$hash_map[$hash] = $att_id;
}
unset($img);

// Sauvegarder home.json mis à jour
file_put_contents($json_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "\n========================================\n";
echo "Résumé :\n";
echo "  Migrés (nouveaux)    : {$stats['migrated']}\n";
echo "  Réutilisés (doublons): {$stats['reused']}\n";
echo "  Optimisés (resized)  : {$stats['optimized']}\n";
echo "  Erreurs              : {$stats['errors']}\n";
echo "========================================\n";
echo "\nhome.json mis à jour avec les IDs d'attachment.\n";
