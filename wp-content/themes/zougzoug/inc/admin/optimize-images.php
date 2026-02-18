<?php
/**
 * Optimisation des images projets :
 *   1. Convertir les JPEG/PNG actifs en WebP (85%, max 1600px)
 *   2. Redimensionner les WebP actifs si > 1600px
 *   3. Supprimer les attachments orphelins (JPEG doublons), garder fichiers en backup
 *   4. Régénérer les métadonnées
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/optimize-images.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$MAX_WIDTH = 1600;
$QUALITY   = 85;

$upload_dir  = wp_upload_dir();
$upload_base = $upload_dir['basedir'];

echo "=== Optimisation images projets ===\n";
echo "Format : WebP | Qualité : {$QUALITY}% | Largeur max : {$MAX_WIDTH}px\n\n";

// ──────────────────────────────────────────────────────
// Collecter les IDs en galerie (actifs)
// ──────────────────────────────────────────────────────

$in_gallery = [];
$projects = get_posts(['post_type' => 'projet', 'posts_per_page' => -1]);
foreach ($projects as $p) {
	$gal = get_post_meta($p->ID, '_projet_gallery', true);
	if (is_array($gal)) {
		foreach ($gal as $id) $in_gallery[intval($id)] = true;
	}
}

// Tous les attachments projets/
$all_atts = get_posts([
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => -1,
	'meta_query'     => [['key' => '_wp_attached_file', 'value' => 'projets/', 'compare' => 'LIKE']],
]);

$stats = [
	'converted'   => 0,
	'resized'     => 0,
	'skipped'     => 0,
	'orphans_del' => 0,
	'errors'      => 0,
];

// ──────────────────────────────────────────────────────
// Étape 1 : Traiter les attachments actifs
// ──────────────────────────────────────────────────────

echo "--- Étape 1 : Conversion & redimensionnement ---\n\n";

foreach ($all_atts as $att) {
	$att_id = $att->ID;
	$mime   = $att->post_mime_type;
	$active = isset($in_gallery[$att_id]);

	// Ignorer les vidéos
	if (strpos($mime, 'video/') === 0) continue;

	// Ignorer les non-images
	if (strpos($mime, 'image/') !== 0) continue;

	// Ignorer les orphelins (traités à l'étape 2)
	if (!$active) continue;

	$file_path = get_attached_file($att_id);
	if (!$file_path || !file_exists($file_path)) {
		echo "  #{$att_id} : FICHIER MANQUANT\n";
		$stats['errors']++;
		continue;
	}

	$file_rel = get_post_meta($att_id, '_wp_attached_file', true);
	$filename = basename($file_path);
	$dir      = dirname($file_path);

	// Charger l'image avec l'éditeur WP
	$editor = wp_get_image_editor($file_path);
	if (is_wp_error($editor)) {
		echo "  #{$att_id} $filename : ERREUR éditeur — " . $editor->get_error_message() . "\n";
		$stats['errors']++;
		continue;
	}

	$size    = $editor->get_size();
	$width   = $size['width'];
	$is_webp = ($mime === 'image/webp');

	// Déterminer si une action est nécessaire
	$needs_resize  = ($width > $MAX_WIDTH);
	$needs_convert = !$is_webp;

	if (!$needs_resize && !$needs_convert) {
		echo "  #{$att_id} $filename : OK ({$width}px, WebP)\n";
		$stats['skipped']++;
		continue;
	}

	// Redimensionner si nécessaire
	if ($needs_resize) {
		$editor->resize($MAX_WIDTH, null);
		$new_size = $editor->get_size();
		echo "  #{$att_id} $filename : redimensionné {$width}px → {$new_size['width']}px";
	} else {
		echo "  #{$att_id} $filename : ";
	}

	// Définir la qualité
	$editor->set_quality($QUALITY);

	if ($needs_convert) {
		// Convertir en WebP
		$webp_filename = preg_replace('/\.(jpg|jpeg|png|gif|avif)$/i', '.webp', $filename);
		// Gérer le cas où le nom contenait -scaled
		$webp_filename = str_replace('-scaled.webp', '.webp', $webp_filename);
		$webp_path     = $dir . '/' . $webp_filename;
		$webp_rel      = preg_replace('/\.(jpg|jpeg|png|gif|avif)$/i', '.webp', $file_rel);
		$webp_rel      = str_replace('-scaled.webp', '.webp', $webp_rel);

		$result = $editor->save($webp_path, 'image/webp');

		if (is_wp_error($result)) {
			echo " ERREUR conversion — " . $result->get_error_message() . "\n";
			$stats['errors']++;
			continue;
		}

		// Backup l'original
		$backup_dir = $dir . '/_backup';
		if (!is_dir($backup_dir)) {
			wp_mkdir_p($backup_dir);
		}
		rename($file_path, $backup_dir . '/' . $filename);

		// Supprimer les anciennes tailles WordPress générées (-300x300, -768x768, etc.)
		$old_meta = wp_get_attachment_metadata($att_id);
		if (!empty($old_meta['sizes'])) {
			foreach ($old_meta['sizes'] as $size_data) {
				$old_size_file = $dir . '/' . $size_data['file'];
				if (file_exists($old_size_file)) {
					unlink($old_size_file);
				}
			}
		}
		// Supprimer aussi le fichier -scaled s'il existe et n'est pas l'original
		if ($file_path !== $dir . '/' . $filename) {
			// Le fichier original (non-scaled) aussi en backup
			$original_name = str_replace('-scaled', '', $filename);
			$original_path = $dir . '/' . $original_name;
			if (file_exists($original_path) && $original_path !== $file_path) {
				rename($original_path, $backup_dir . '/' . $original_name);
			}
		}

		// Mettre à jour l'attachment
		wp_update_post([
			'ID'             => $att_id,
			'post_mime_type' => 'image/webp',
		]);
		update_post_meta($att_id, '_wp_attached_file', $webp_rel);

		// Régénérer les métadonnées
		$new_meta = wp_generate_attachment_metadata($att_id, $webp_path);
		wp_update_attachment_metadata($att_id, $new_meta);

		$old_size_kb = round(filesize($backup_dir . '/' . $filename) / 1024);
		$new_size_kb = round(filesize($webp_path) / 1024);
		echo "converti → WebP ({$old_size_kb} KB → {$new_size_kb} KB)\n";
		$stats['converted']++;

	} else {
		// Déjà WebP, juste redimensionner
		$result = $editor->save($file_path, 'image/webp');

		if (is_wp_error($result)) {
			echo " ERREUR resize — " . $result->get_error_message() . "\n";
			$stats['errors']++;
			continue;
		}

		// Le save peut avoir créé un nouveau fichier avec dimensions dans le nom
		// On doit s'assurer que le résultat remplace l'original
		if ($result['path'] !== $file_path) {
			if (file_exists($file_path)) {
				// Backup de l'original
				$backup_dir = $dir . '/_backup';
				if (!is_dir($backup_dir)) wp_mkdir_p($backup_dir);
				rename($file_path, $backup_dir . '/' . $filename);
			}
			rename($result['path'], $file_path);
		}

		// Supprimer les anciennes tailles et régénérer
		$old_meta = wp_get_attachment_metadata($att_id);
		if (!empty($old_meta['sizes'])) {
			foreach ($old_meta['sizes'] as $size_data) {
				$old_size_file = $dir . '/' . $size_data['file'];
				if (file_exists($old_size_file)) {
					unlink($old_size_file);
				}
			}
		}

		$new_meta = wp_generate_attachment_metadata($att_id, $file_path);
		wp_update_attachment_metadata($att_id, $new_meta);

		$new_size = $editor->get_size();
		echo "\n";
		$stats['resized']++;
	}
}

// ──────────────────────────────────────────────────────
// Étape 2 : Supprimer les orphelins de la médiathèque
// ──────────────────────────────────────────────────────

echo "\n--- Étape 2 : Nettoyage orphelins ---\n\n";

foreach ($all_atts as $att) {
	$att_id = $att->ID;
	$mime   = $att->post_mime_type;
	$active = isset($in_gallery[$att_id]);

	// Garder les actifs
	if ($active) continue;

	// Garder les vidéos
	if (strpos($mime, 'video/') === 0) continue;

	// Ignorer les non-images
	if (strpos($mime, 'image/') !== 0) continue;

	$file_path = get_attached_file($att_id);
	$filename  = $file_path ? basename($file_path) : '?';

	// Backup le fichier avant de supprimer l'attachment
	if ($file_path && file_exists($file_path)) {
		$dir        = dirname($file_path);
		$backup_dir = $dir . '/_backup';
		if (!is_dir($backup_dir)) wp_mkdir_p($backup_dir);

		// Déplacer le fichier principal
		rename($file_path, $backup_dir . '/' . $filename);

		// Déplacer aussi les tailles générées
		$meta = wp_get_attachment_metadata($att_id);
		if (!empty($meta['sizes'])) {
			foreach ($meta['sizes'] as $size_data) {
				$size_file = $dir . '/' . $size_data['file'];
				if (file_exists($size_file)) {
					rename($size_file, $backup_dir . '/' . $size_data['file']);
				}
			}
		}

		// Fichier original (non-scaled) si différent
		if (!empty($meta['original_image'])) {
			$orig_file = $dir . '/' . $meta['original_image'];
			if (file_exists($orig_file)) {
				rename($orig_file, $backup_dir . '/' . $meta['original_image']);
			}
		}
	}

	// Supprimer l'attachment de la BDD (pas les fichiers, déjà déplacés)
	wp_delete_attachment($att_id, true);

	echo "  #{$att_id} $filename : supprimé de la médiathèque (backup conservé)\n";
	$stats['orphans_del']++;
}

// ──────────────────────────────────────────────────────
// Résumé
// ──────────────────────────────────────────────────────

echo "\n========================================\n";
echo "Résumé :\n";
echo "  Convertis JPEG→WebP : {$stats['converted']}\n";
echo "  Redimensionnés      : {$stats['resized']}\n";
echo "  Déjà OK (skipped)   : {$stats['skipped']}\n";
echo "  Orphelins supprimés : {$stats['orphans_del']}\n";
echo "  Erreurs             : {$stats['errors']}\n";
echo "========================================\n";
