<?php
/**
 * Migration complète : TOUTES les images du thème → médiathèque WordPress
 *
 *   - Copie les images de assets/img/ vers uploads/site/
 *   - Optimise : max 1600px, WebP 85%
 *   - Détecte les doublons (réutilise les attachments existants)
 *   - Met à jour TOUS les JSON (home, about, contact, cours, revendeurs, global)
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/migrate-all-images.php
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
$dest_dir    = $upload_base . '/site';

echo "=== Migration complète images thème → médiathèque ===\n\n";

// Créer le dossier destination
if (!is_dir($dest_dir)) {
	wp_mkdir_p($dest_dir);
}

// ──────────────────────────────────────────────────────
// Étape 1 : Construire le hash map de la médiathèque existante
// ──────────────────────────────────────────────────────

echo "--- Étape 1 : Indexation médiathèque existante ---\n";

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
echo "  " . count($hash_map) . " images existantes indexées.\n\n";

// ──────────────────────────────────────────────────────
// Étape 2 : Collecter toutes les images référencées
// ──────────────────────────────────────────────────────

echo "--- Étape 2 : Collecte des images à migrer ---\n";

// Liste des fichiers à migrer (uniques)
$files_to_migrate = [];

// home.json
$home = json_decode(file_get_contents($theme_dir . '/data/home.json'), true);

// Hero slides
foreach ($home['hero']['slides'] as $slide) {
	$files_to_migrate[$slide['left']] = true;
	$files_to_migrate[$slide['right']] = true;
}

// Statement images (desktop)
foreach ($home['statement']['images'] as $img) {
	$files_to_migrate[$img['src']] = true;
}

// Statement mobile images (sauf celles déjà dans uploads)
foreach (($home['statement']['mobile_images'] ?? []) as $img) {
	if (!empty($img['src']) && strpos($img['src'], 'wp-content/uploads/') === false) {
		$files_to_migrate[$img['src']] = true;
	}
}

// Showcases
foreach ($home['showcases'] as $sc) {
	if (!empty($sc['img_main'])) $files_to_migrate[$sc['img_main']] = true;
	if (!empty($sc['img_secondary'])) $files_to_migrate[$sc['img_secondary']] = true;
}

// about.json
$about = json_decode(file_get_contents($theme_dir . '/data/about.json'), true);

// Portrait (sauf si déjà dans uploads)
if (!empty($about['hero']['portrait']) && strpos($about['hero']['portrait'], 'wp-content/uploads/') === false) {
	$files_to_migrate[$about['hero']['portrait']] = true;
}

// Blocs
foreach ($about['blocs'] as $bloc) {
	if (!empty($bloc['image'])) $files_to_migrate[$bloc['image']] = true;
}

// contact.json
$contact = json_decode(file_get_contents($theme_dir . '/data/contact.json'), true);
if (!empty($contact['info']['photo'])) $files_to_migrate[$contact['info']['photo']] = true;
foreach ($contact['photos'] as $photo) {
	$files_to_migrate[$photo['src']] = true;
}

// cours.json
$cours = json_decode(file_get_contents($theme_dir . '/data/cours.json'), true);
if (!empty($cours['hero']['image'])) $files_to_migrate[$cours['hero']['image']] = true;
foreach ($cours['galerie']['images'] as $img) {
	$files_to_migrate[$img['src']] = true;
}

// revendeurs.json
$revendeurs = json_decode(file_get_contents($theme_dir . '/data/revendeurs.json'), true);
foreach ($revendeurs['photos']['images'] as $img) {
	$files_to_migrate[$img['src']] = true;
}

// global.json
$global = json_decode(file_get_contents($theme_dir . '/data/global.json'), true);
if (!empty($global['meta']['og_image'])) $files_to_migrate[$global['meta']['og_image']] = true;

echo "  " . count($files_to_migrate) . " fichiers uniques à migrer.\n\n";

// ──────────────────────────────────────────────────────
// Étape 3 : Migrer chaque image
// ──────────────────────────────────────────────────────

echo "--- Étape 3 : Migration ---\n\n";

$mapping = []; // filename → attachment_id
$stats = ['migrated' => 0, 'reused' => 0, 'optimized' => 0, 'missing' => 0, 'errors' => 0];

foreach (array_keys($files_to_migrate) as $filename) {
	$src_path = $img_dir . $filename;

	if (!file_exists($src_path)) {
		echo "  MANQUANT : $filename\n";
		$stats['missing']++;
		continue;
	}

	$hash = md5_file($src_path);

	// Doublon avec image existante ?
	if (isset($hash_map[$hash])) {
		$att_id = $hash_map[$hash];
		$mapping[$filename] = $att_id;
		echo "  $filename → DOUBLON → attachment #{$att_id}\n";
		$stats['reused']++;
		continue;
	}

	// Copier vers uploads/site/
	$dest_path = $dest_dir . '/' . $filename;
	copy($src_path, $dest_path);

	// Optimiser si > 1600px
	$size_info = @getimagesize($dest_path);
	$width = $size_info ? $size_info[0] : 0;

	if ($width > $MAX_WIDTH) {
		$editor = wp_get_image_editor($dest_path);
		if (!is_wp_error($editor)) {
			$editor->resize($MAX_WIDTH, null);
			$editor->set_quality($QUALITY);
			$result = $editor->save($dest_path, 'image/webp');
			if (!is_wp_error($result)) {
				if ($result['path'] !== $dest_path) {
					if (file_exists($dest_path)) unlink($dest_path);
					rename($result['path'], $dest_path);
				}
				$old_kb = round(filesize($src_path) / 1024);
				$new_kb = round(filesize($dest_path) / 1024);
				echo "  $filename : {$width}px → {$result['width']}px ({$old_kb}KB → {$new_kb}KB)";
				$stats['optimized']++;
			} else {
				echo "  $filename : ERREUR resize — " . $result->get_error_message();
			}
		}
	} else {
		$fsize = round(filesize($src_path) / 1024);
		echo "  $filename : OK ({$width}px, {$fsize}KB)";
	}

	// Déterminer le MIME
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	$mime_map = ['webp' => 'image/webp', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'svg' => 'image/svg+xml'];
	$mime = $mime_map[$ext] ?? 'image/webp';

	// Enregistrer comme attachment WordPress
	$att_data = [
		'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
		'post_mime_type' => $mime,
		'post_status'    => 'inherit',
	];

	$att_id = wp_insert_attachment($att_data, $dest_path);
	if (is_wp_error($att_id)) {
		echo " → ERREUR insertion\n";
		$stats['errors']++;
		continue;
	}

	update_post_meta($att_id, '_wp_attached_file', 'site/' . $filename);

	$meta = wp_generate_attachment_metadata($att_id, $dest_path);
	wp_update_attachment_metadata($att_id, $meta);

	$mapping[$filename] = $att_id;
	$hash_map[$hash] = $att_id;
	echo " → attachment #{$att_id}\n";
	$stats['migrated']++;
}

echo "\n";

// ──────────────────────────────────────────────────────
// Étape 4 : Mettre à jour les JSON avec attachment_ids
// ──────────────────────────────────────────────────────

echo "--- Étape 4 : Mise à jour des JSON ---\n\n";

// --- home.json ---
// Hero slides
foreach ($home['hero']['slides'] as &$slide) {
	if (isset($mapping[$slide['left']])) {
		$slide['left'] = $mapping[$slide['left']];
	}
	if (isset($mapping[$slide['right']])) {
		$slide['right'] = $mapping[$slide['right']];
	}
}
unset($slide);

// Statement images (desktop)
foreach ($home['statement']['images'] as &$img) {
	if (isset($mapping[$img['src']])) {
		$img['attachment_id'] = $mapping[$img['src']];
		unset($img['src']);
	}
}
unset($img);

// Statement mobile images
foreach (($home['statement']['mobile_images'] ?? []) as &$img) {
	if (!empty($img['src']) && isset($mapping[$img['src']])) {
		$img['attachment_id'] = $mapping[$img['src']];
		unset($img['src']);
	} elseif (!empty($img['src']) && strpos($img['src'], 'wp-content/uploads/') !== false) {
		// Déjà dans uploads — trouver l'attachment ID
		$rel_path = str_replace('wp-content/uploads/', '', $img['src']);
		$att = $wpdb ?? null;
		// Chercher par chemin relatif
		$found = get_posts([
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'posts_per_page' => 1,
			'meta_query' => [['key' => '_wp_attached_file', 'value' => $rel_path, 'compare' => 'LIKE']],
		]);
		if ($found) {
			$img['attachment_id'] = $found[0]->ID;
			unset($img['src']);
		}
	}
}
unset($img);

// Showcases
foreach ($home['showcases'] as &$sc) {
	if (!empty($sc['img_main']) && isset($mapping[$sc['img_main']])) {
		$sc['img_main'] = $mapping[$sc['img_main']];
	}
	if (!empty($sc['img_secondary']) && isset($mapping[$sc['img_secondary']])) {
		$sc['img_secondary'] = $mapping[$sc['img_secondary']];
	}
}
unset($sc);

file_put_contents($theme_dir . '/data/home.json', json_encode($home, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  home.json ✓\n";

// --- about.json ---
// Portrait (si dans uploads, trouver l'ID)
if (!empty($about['hero']['portrait']) && strpos($about['hero']['portrait'], 'wp-content/uploads/') !== false) {
	$rel_path = str_replace('wp-content/uploads/', '', $about['hero']['portrait']);
	$found = get_posts([
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => 1,
		'meta_query' => [['key' => '_wp_attached_file', 'value' => $rel_path, 'compare' => 'LIKE']],
	]);
	if ($found) {
		$about['hero']['portrait'] = $found[0]->ID;
	}
} elseif (isset($mapping[$about['hero']['portrait']])) {
	$about['hero']['portrait'] = $mapping[$about['hero']['portrait']];
}

// Blocs
foreach ($about['blocs'] as &$bloc) {
	if (!empty($bloc['image']) && isset($mapping[$bloc['image']])) {
		$bloc['image'] = $mapping[$bloc['image']];
	}
}
unset($bloc);

file_put_contents($theme_dir . '/data/about.json', json_encode($about, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  about.json ✓\n";

// --- contact.json ---
if (!empty($contact['info']['photo']) && isset($mapping[$contact['info']['photo']])) {
	$contact['info']['photo'] = $mapping[$contact['info']['photo']];
}
foreach ($contact['photos'] as &$photo) {
	if (isset($mapping[$photo['src']])) {
		$photo['attachment_id'] = $mapping[$photo['src']];
		unset($photo['src']);
	}
}
unset($photo);

file_put_contents($theme_dir . '/data/contact.json', json_encode($contact, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  contact.json ✓\n";

// --- cours.json ---
if (!empty($cours['hero']['image']) && isset($mapping[$cours['hero']['image']])) {
	$cours['hero']['image'] = $mapping[$cours['hero']['image']];
}
foreach ($cours['galerie']['images'] as &$img) {
	if (isset($mapping[$img['src']])) {
		$img['attachment_id'] = $mapping[$img['src']];
		unset($img['src']);
	}
}
unset($img);

file_put_contents($theme_dir . '/data/cours.json', json_encode($cours, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  cours.json ✓\n";

// --- revendeurs.json ---
foreach ($revendeurs['photos']['images'] as &$img) {
	if (isset($mapping[$img['src']])) {
		$img['attachment_id'] = $mapping[$img['src']];
		unset($img['src']);
	}
}
unset($img);

file_put_contents($theme_dir . '/data/revendeurs.json', json_encode($revendeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  revendeurs.json ✓\n";

// --- global.json ---
if (!empty($global['meta']['og_image']) && isset($mapping[$global['meta']['og_image']])) {
	$global['meta']['og_image'] = $mapping[$global['meta']['og_image']];
}

file_put_contents($theme_dir . '/data/global.json', json_encode($global, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  global.json ✓\n";

// ──────────────────────────────────────────────────────
// Résumé
// ──────────────────────────────────────────────────────

echo "\n========================================\n";
echo "Résumé :\n";
echo "  Migrés (nouveaux)    : {$stats['migrated']}\n";
echo "  Réutilisés (doublons): {$stats['reused']}\n";
echo "  Optimisés (resized)  : {$stats['optimized']}\n";
echo "  Manquants            : {$stats['missing']}\n";
echo "  Erreurs              : {$stats['errors']}\n";
echo "========================================\n";

echo "\nMapping complet :\n";
foreach ($mapping as $file => $id) {
	echo "  $file → #{$id}\n";
}
