<?php
/**
 * Auto-conversion des images uploadées :
 *   - Convertit JPEG/PNG/GIF → WebP (85% qualité)
 *   - Redimensionne à 1600px max de largeur
 *   - Conserve l'original en backup dans _backup/
 */

/**
 * Limiter la taille max des images à 1600px (au lieu de 2560px par défaut)
 */
add_filter('big_image_size_threshold', function () {
	return 1600;
});

/**
 * Après l'upload, convertir l'image en WebP et garder l'original en backup.
 * Se déclenche après que WP a traité le fichier (y compris le scaling big_image).
 */
add_filter('wp_generate_attachment_metadata', function ($metadata, $att_id) {
	// Guard anti-récursion (on rappelle wp_generate_attachment_metadata en interne)
	static $processing = [];
	if (isset($processing[$att_id])) return $metadata;

	$mime = get_post_mime_type($att_id);

	// Ignorer les non-images et les WebP
	if (strpos($mime, 'image/') !== 0) return $metadata;
	if ($mime === 'image/webp') return $metadata;
	if ($mime === 'image/svg+xml') return $metadata;

	$file_path = get_attached_file($att_id);
	if (!$file_path || !file_exists($file_path)) return $metadata;

	$dir      = dirname($file_path);
	$filename = basename($file_path);

	// Charger l'image avec l'éditeur WP
	$editor = wp_get_image_editor($file_path);
	if (is_wp_error($editor)) return $metadata;

	// Redimensionner si > 1600px
	$size = $editor->get_size();
	if ($size['width'] > 1600) {
		$editor->resize(1600, null);
	}

	// Qualité 85%
	$editor->set_quality(85);

	// Nom du fichier WebP
	$webp_filename = preg_replace('/\.(jpg|jpeg|png|gif|avif)$/i', '.webp', $filename);
	$webp_path     = $dir . '/' . $webp_filename;

	// Sauvegarder en WebP
	$result = $editor->save($webp_path, 'image/webp');
	if (is_wp_error($result)) return $metadata;

	// Backup l'original
	$backup_dir = $dir . '/_backup';
	if (!is_dir($backup_dir)) {
		wp_mkdir_p($backup_dir);
	}
	rename($file_path, $backup_dir . '/' . $filename);

	// Backup de l'original non-scaled si existe
	if (!empty($metadata['original_image'])) {
		$orig_path = $dir . '/' . $metadata['original_image'];
		if (file_exists($orig_path)) {
			rename($orig_path, $backup_dir . '/' . $metadata['original_image']);
		}
	}

	// Supprimer les anciennes tailles WordPress (thumbnails JPEG)
	if (!empty($metadata['sizes'])) {
		foreach ($metadata['sizes'] as $size_data) {
			$old_size_file = $dir . '/' . $size_data['file'];
			if (file_exists($old_size_file)) {
				@unlink($old_size_file);
			}
		}
	}

	// Mettre à jour l'attachment pour pointer vers le WebP
	wp_update_post([
		'ID'             => $att_id,
		'post_mime_type' => 'image/webp',
	]);

	// Mettre à jour le chemin du fichier attaché
	$file_rel     = get_post_meta($att_id, '_wp_attached_file', true);
	$webp_rel     = preg_replace('/\.(jpg|jpeg|png|gif|avif)$/i', '.webp', $file_rel);
	update_post_meta($att_id, '_wp_attached_file', $webp_rel);

	// Régénérer les métadonnées avec le nouveau fichier WebP
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$processing[$att_id] = true;
	$new_metadata = wp_generate_attachment_metadata($att_id, $webp_path);
	unset($processing[$att_id]);

	return $new_metadata;
}, 10, 2);

/**
 * S'assurer que WordPress accepte les uploads WebP
 */
add_filter('upload_mimes', function ($mimes) {
	$mimes['webp'] = 'image/webp';
	return $mimes;
});
