<?php
/**
 * Re-traitement hero cours — Q80 / 1920px
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/reprocess-cours-hero.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$MAX_WIDTH = 1920;
$QUALITY   = 80;

$backup    = get_template_directory() . '/assets/img/_backup/DSCF6050.webp';
$upload_dir = wp_upload_dir();
$dest      = $upload_dir['basedir'] . '/site/cours-hero.webp';

if (!file_exists($backup)) {
	echo "MANQUANT: $backup\n";
	exit;
}

copy($backup, $dest);

$editor = wp_get_image_editor($dest);
if (is_wp_error($editor)) {
	echo "ERREUR editeur\n";
	exit;
}

$size = $editor->get_size();

if ($size['width'] > $MAX_WIDTH) {
	$editor->resize($MAX_WIDTH, null);
}

$editor->set_quality($QUALITY);
$result = $editor->save($dest, 'image/webp');

if (is_wp_error($result)) {
	echo "ERREUR save: " . $result->get_error_message() . "\n";
	exit;
}

// Le save peut créer un fichier avec dimensions dans le nom
if ($result['path'] !== $dest) {
	if (file_exists($dest)) unlink($dest);
	rename($result['path'], $dest);
}

// Créer attachment
$att_data = [
	'post_title'     => 'cours-hero',
	'post_mime_type' => 'image/webp',
	'post_status'    => 'inherit',
];

$att_id = wp_insert_attachment($att_data, $dest);
update_post_meta($att_id, '_wp_attached_file', 'site/cours-hero.webp');

$meta = wp_generate_attachment_metadata($att_id, $dest);
wp_update_attachment_metadata($att_id, $meta);

$new_size = $editor->get_size();
$kb = round(filesize($dest) / 1024);
echo "OK: #{$att_id} ({$new_size['width']}x{$new_size['height']}, {$kb}KB, Q{$QUALITY})\n";

// Mettre à jour cours.json
$cours_file = get_template_directory() . '/data/cours.json';
$cours = json_decode(file_get_contents($cours_file), true);
$cours['hero']['image'] = $att_id;
file_put_contents(
	$cours_file,
	json_encode($cours, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

echo "cours.json mis à jour.\n";
