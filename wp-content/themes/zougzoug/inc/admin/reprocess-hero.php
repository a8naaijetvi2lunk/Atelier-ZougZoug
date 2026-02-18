<?php
/**
 * Re-traitement des images hero à 100% qualité
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/reprocess-hero.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$MAX_WIDTH = 1920;
$QUALITY   = 80;

$theme_dir  = get_template_directory();
$backup_dir = $theme_dir . '/assets/img/_backup';
$upload_dir = wp_upload_dir();
$dest_dir   = $upload_dir['basedir'] . '/hero';

if (!is_dir($dest_dir)) wp_mkdir_p($dest_dir);

$home = json_decode(file_get_contents($theme_dir . '/data/home.json'), true);

// Fichiers originaux du hero par slide
$hero_files = [
	['7773c8ee265a8201793c73b9a9d0ca70210f4ee2.webp', '863126567bfcfdb621a42d72d4b513cb31b157a6.webp'],
	['slide-tasses.webp', 'slide-trio.webp'],
	['slide-assiette-table.webp', 'slide-pichet.webp'],
	['slide-boites.webp', 'slide-img5253.webp'],
];

echo "=== Re-traitement hero à 100% qualité ===\n\n";

foreach ($hero_files as $si => $pair) {
	$keys = ['left', 'right'];
	foreach ($keys as $ki => $key) {
		$filename  = $pair[$ki];
		$src_path  = $backup_dir . '/' . $filename;
		$dest_path = $dest_dir . '/' . $filename;

		if (!file_exists($src_path)) {
			echo "  MANQUANT : $filename\n";
			continue;
		}

		copy($src_path, $dest_path);

		$editor = wp_get_image_editor($dest_path);
		if (is_wp_error($editor)) {
			echo "  ERREUR éditeur : $filename\n";
			continue;
		}

		$size  = $editor->get_size();
		$width = $size['width'];

		if ($width > $MAX_WIDTH) {
			$editor->resize($MAX_WIDTH, null);
		}

		$editor->set_quality($QUALITY);

		$result = $editor->save($dest_path, 'image/webp');
		if (is_wp_error($result)) {
			echo "  ERREUR save : $filename — " . $result->get_error_message() . "\n";
			continue;
		}

		// Le save peut créer un fichier avec dimensions dans le nom
		if ($result['path'] !== $dest_path) {
			if (file_exists($dest_path)) unlink($dest_path);
			rename($result['path'], $dest_path);
		}

		// Créer attachment
		$att_data = [
			'post_title'     => 'hero-' . sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
			'post_mime_type' => 'image/webp',
			'post_status'    => 'inherit',
		];

		$att_id = wp_insert_attachment($att_data, $dest_path);
		update_post_meta($att_id, '_wp_attached_file', 'hero/' . $filename);

		$meta = wp_generate_attachment_metadata($att_id, $dest_path);
		wp_update_attachment_metadata($att_id, $meta);

		// Mettre à jour le JSON
		$home['hero']['slides'][$si][$key] = $att_id;

		$new_kb   = round(filesize($dest_path) / 1024);
		$new_size = $editor->get_size();
		echo "  Slide " . ($si + 1) . " $key : $filename → #{$att_id} ({$new_size['width']}x{$new_size['height']}, {$new_kb}KB, Q100)\n";
	}
}

file_put_contents(
	$theme_dir . '/data/home.json',
	json_encode($home, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

echo "\nhome.json mis à jour avec les nouveaux IDs hero.\n";
