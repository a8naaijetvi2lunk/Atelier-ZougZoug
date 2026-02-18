<?php
/**
 * Helper — charger les fichiers JSON de data/
 */

function zz_get_data(string $page): array {
	$file = get_template_directory() . '/data/' . $page . '.json';
	if (!file_exists($file)) return [];
	$json = file_get_contents($file);
	return json_decode($json, true) ?: [];
}

/**
 * Resoudre un chemin image : attachment ID, URL complete ou chemin relatif
 */
function zz_img($src): string {
	if (!$src && $src !== 0) return '';
	// Attachment ID (entier ou string numérique)
	if (is_numeric($src)) {
		return wp_get_attachment_url(intval($src)) ?: '';
	}
	if (strpos($src, 'http') === 0 || strpos($src, '//') === 0) return $src;
	if (strpos($src, 'wp-content/uploads/') !== false) return home_url('/' . ltrim($src, '/'));
	return get_template_directory_uri() . '/assets/img/' . $src;
}
