<?php
/**
 * Nettoyage médiathèque — Supprime les attachments non utilisés
 *
 * Usage WP-CLI :
 *   wp eval-file wp-content/themes/zougzoug/inc/admin/cleanup-unused-media.php audit
 *   wp eval-file wp-content/themes/zougzoug/inc/admin/cleanup-unused-media.php delete
 */

// En WP-CLI, $args contient les arguments positionnels
$mode = 'audit';
if ( defined('WP_CLI') && WP_CLI && isset($args) && ! empty($args[0]) ) {
	$mode = $args[0];
} elseif ( isset($_GET['cleanup_media']) ) {
	$mode = sanitize_text_field($_GET['cleanup_media']);
}

// Sécurité hors CLI
if ( ! defined('WP_CLI') && ! current_user_can('manage_options') ) {
	wp_die('Accès refusé.');
}

$is_cli = defined('WP_CLI') && WP_CLI;
$nl = $is_cli ? "\n" : "\n";

if ( ! $is_cli ) {
	header('Content-Type: text/html; charset=utf-8');
	echo '<pre style="font-family:monospace; font-size:14px; line-height:1.6; max-width:1200px; margin:40px auto;">';
}

echo "=== NETTOYAGE MÉDIATHÈQUE — ATELIER ZOUGZOUG ==={$nl}";
echo "Mode : " . strtoupper($mode) . "{$nl}{$nl}";

// ─────────────────────────────────────────────
// 1. Collecter les attachment IDs utilisés
// ─────────────────────────────────────────────

$used_ids = [];
$used_paths = []; // Chemins src directs (wp-content/uploads/...)

// --- 1a. IDs depuis les fichiers JSON ---
$json_dir = get_template_directory() . '/data/';
$json_files = ['home.json', 'about.json', 'contact.json', 'cours.json', 'revendeurs.json', 'global.json'];

function zz_extract_attachment_ids($data, &$used_ids, &$used_paths, $source, $path = '') {
	if ( ! is_array($data) ) return;

	foreach ($data as $key => $value) {
		$current_path = $path ? "{$path}.{$key}" : $key;

		if ( is_array($value) ) {
			zz_extract_attachment_ids($value, $used_ids, $used_paths, $source, $current_path);
		} elseif ( is_int($value) && $value > 0 ) {
			$id_keys = [
				'attachment_id', 'left', 'right', 'portrait', 'photo',
				'image', 'img_main', 'img_secondary'
			];
			if ( in_array($key, $id_keys) ) {
				$used_ids[$value] = "{$source} → {$current_path}";
			}
		} elseif ( is_string($value) && $key === 'src' && strpos($value, 'wp-content/uploads/') !== false ) {
			$used_paths[] = $value;
		}
	}
}

function zz_format_bytes($bytes) {
	if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
	if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
	return $bytes . ' B';
}

foreach ($json_files as $json_file) {
	$path = $json_dir . $json_file;
	if ( ! file_exists($path) ) continue;

	$content = file_get_contents($path);
	$data = json_decode($content, true);
	if ( ! $data ) continue;

	zz_extract_attachment_ids($data, $used_ids, $used_paths, $json_file);
}

// Résoudre les chemins src en attachment IDs
if ( ! empty($used_paths) ) {
	global $wpdb;
	foreach ($used_paths as $src_path) {
		// Trouver l'attachment par son fichier _wp_attached_file
		$filename = basename($src_path);
		$att_id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
			'%' . $wpdb->esc_like($filename)
		));
		if ( $att_id ) {
			$used_ids[intval($att_id)] = "JSON src → {$src_path}";
		}
	}
}

// --- 1b. IDs depuis les CPT Projets (galeries + thumbnails) ---
$projets = get_posts([
	'post_type'      => 'projet',
	'posts_per_page' => -1,
	'post_status'    => 'any',
]);

foreach ($projets as $projet) {
	$thumb_id = get_post_thumbnail_id($projet->ID);
	if ($thumb_id) {
		$used_ids[intval($thumb_id)] = "Projet '{$projet->post_title}' — thumbnail";
	}

	$gallery = get_post_meta($projet->ID, '_projet_gallery', true);
	if ( is_array($gallery) ) {
		foreach ($gallery as $att_id) {
			$att_id = intval($att_id);
			if ($att_id > 0) {
				$used_ids[$att_id] = "Projet '{$projet->post_title}' — galerie";
			}
		}
	}
}

// --- 1c. IDs depuis les CPT Evenements (thumbnails) ---
$events = get_posts([
	'post_type'      => 'evenement',
	'posts_per_page' => -1,
	'post_status'    => 'any',
]);

foreach ($events as $event) {
	$thumb_id = get_post_thumbnail_id($event->ID);
	if ($thumb_id) {
		$used_ids[intval($thumb_id)] = "Événement '{$event->post_title}' — thumbnail";
	}
}

// --- 1d. IDs depuis les pages WP (featured images) ---
$pages = get_posts([
	'post_type'      => 'page',
	'posts_per_page' => -1,
	'post_status'    => 'any',
]);

foreach ($pages as $page) {
	$thumb_id = get_post_thumbnail_id($page->ID);
	if ($thumb_id) {
		$used_ids[intval($thumb_id)] = "Page '{$page->post_title}' — thumbnail";
	}
}

// --- 1e. IDs depuis les options WP ---
$site_icon = get_option('site_icon');
if ($site_icon) {
	$used_ids[intval($site_icon)] = "Option WP — site_icon";
}

$custom_logo = get_theme_mod('custom_logo');
if ($custom_logo) {
	$used_ids[intval($custom_logo)] = "Option WP — custom_logo";
}

echo "── ATTACHMENT IDs UTILISÉS (" . count($used_ids) . ") ──{$nl}";
ksort($used_ids);
foreach ($used_ids as $id => $source) {
	$url = wp_get_attachment_url($id);
	$filename = $url ? basename($url) : '(URL introuvable)';
	echo sprintf("  ID %-4d | %-45s | %s{$nl}", $id, $filename, $source);
}

// ─────────────────────────────────────────────
// 2. Lister TOUS les attachments
// ─────────────────────────────────────────────

$all_attachments = get_posts([
	'post_type'      => 'attachment',
	'posts_per_page' => -1,
	'post_status'    => 'any',
	'fields'         => 'ids',
]);

echo "{$nl}── TOTAL ATTACHMENTS EN MÉDIATHÈQUE : " . count($all_attachments) . " ──{$nl}";

// ─────────────────────────────────────────────
// 3. Identifier les non utilisés
// ─────────────────────────────────────────────

$unused_ids = array_diff($all_attachments, array_keys($used_ids));

echo "{$nl}── ATTACHMENTS NON UTILISÉS (" . count($unused_ids) . ") ──{$nl}";

if ( empty($unused_ids) ) {
	echo "  Aucune image non utilisée. Médiathèque propre !{$nl}";
} else {
	$total_size = 0;

	foreach ($unused_ids as $id) {
		$url = wp_get_attachment_url($id);
		$file = get_attached_file($id);
		$filename = $url ? basename($url) : '(inconnu)';
		$mime = get_post_mime_type($id);
		$filesize = $file && file_exists($file) ? filesize($file) : 0;
		$total_size += $filesize;

		$metadata = wp_get_attachment_metadata($id);
		$thumb_size = 0;
		if ( $metadata && isset($metadata['sizes']) && $file ) {
			$upload_dir = dirname($file);
			foreach ($metadata['sizes'] as $size_info) {
				$thumb_path = $upload_dir . '/' . $size_info['file'];
				if (file_exists($thumb_path)) {
					$thumb_size += filesize($thumb_path);
				}
			}
		}
		$total_size += $thumb_size;

		echo sprintf("  ID %-4d | %-50s | %-15s | %s{$nl}",
			$id, $filename, $mime, zz_format_bytes($filesize + $thumb_size)
		);
	}

	echo "{$nl}  TOTAL ESPACE RÉCUPÉRABLE : " . zz_format_bytes($total_size) . "{$nl}";
}

// ─────────────────────────────────────────────
// 4. Suppression (mode delete)
// ─────────────────────────────────────────────

if ( $mode === 'delete' && ! empty($unused_ids) ) {
	echo "{$nl}── SUPPRESSION EN COURS ──{$nl}";

	$deleted = 0;
	$errors = 0;

	foreach ($unused_ids as $id) {
		$filename = basename(wp_get_attachment_url($id) ?: '');
		$result = wp_delete_attachment($id, true);

		if ($result) {
			echo "  OK  ID {$id} ({$filename}){$nl}";
			$deleted++;
		} else {
			echo "  ERR ID {$id} ({$filename}){$nl}";
			$errors++;
		}
	}

	echo "{$nl}── RÉSULTAT ──{$nl}";
	echo "  Supprimés : {$deleted}{$nl}";
	echo "  Erreurs   : {$errors}{$nl}";
}

if ( $mode === 'audit' && ! empty($unused_ids) ) {
	echo "{$nl}── PROCHAINE ÉTAPE ──{$nl}";
	echo "  Pour supprimer : wp eval-file wp-content/themes/zougzoug/inc/admin/cleanup-unused-media.php delete{$nl}";
}

echo "{$nl}=== FIN ==={$nl}";

if ( ! $is_cli ) {
	echo '</pre>';
}
