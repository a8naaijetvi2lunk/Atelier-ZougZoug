<?php
/**
 * Synchronise les galeries CPT Projet avec la sélection RETOURS de Charlotte.
 * Supprime les images non retenues (fichiers physiques + entrées galerie).
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/sync-galeries.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

$theme_dir    = get_template_directory();
$img_base     = $theme_dir . '/assets/img/projets/';
$retours_base = '/home/zougzoug/htdocs/zougzoug.lan/DOCUMENTATIONS/RETOURS-180226/PROJETS_COLLAB B2B/';

if (!is_dir($retours_base)) {
	echo "ERREUR : Dossier RETOURS non trouvé : $retours_base\n";
	exit(1);
}

// Mapping : slug WP → dossier thème → dossier RETOURS
$projets = [
	['slug' => 'becquetance',       'folder' => 'becquetance',       'retours' => '1-ART-DE-LA-TABLE/BECQUETANCE'],
	['slug' => 'benoit-castel',     'folder' => 'benoit-castel',     'retours' => '1-ART-DE-LA-TABLE/BENOIT-CASTEL'],
	['slug' => 'creme-table',       'folder' => 'creme-table',       'retours' => '1-ART-DE-LA-TABLE/CREME'],
	['slug' => 'maison-fragile',    'folder' => 'maison-fragile',    'retours' => '1-ART-DE-LA-TABLE/MAISON-FRAGILE'],
	['slug' => 'petite-marmelade',  'folder' => 'petite-marmelade',  'retours' => '1-ART-DE-LA-TABLE/PETITE-MARMELADE'],
	['slug' => 'pilos',             'folder' => 'pilos',             'retours' => "1-ART-DE-LA-TABLE/PILO'S"],
	['slug' => 'verre-a-pied',     'folder' => 'verre-a-pied',     'retours' => '1-ART-DE-LA-TABLE/VERRE-A-PIED'],
	['slug' => 'creme-luminaires',  'folder' => 'creme-luminaires',  'retours' => '2-LUMINAIRES/CREME'],
	['slug' => 'padam-hotel-artefak', 'folder' => 'padam',          'retours' => '2-LUMINAIRES/PADAM-HOTEL-ARTEFAK'],
	['slug' => 'sella-st-barth',    'folder' => 'sella',            'retours' => '2-LUMINAIRES/SELLA-StBARTH'],
	['slug' => 'adele',             'folder' => 'adele',             'retours' => '2-LUMINAIRES/ADELE'],
	['slug' => 'carhartt-wip',      'folder' => 'carhartt',          'retours' => '3-ACCESSOIRES-AMBIANCE/CARHARTT'],
	['slug' => 'conscience-parfums','folder' => 'conscience-parfums','retours' => '3-ACCESSOIRES-AMBIANCE/CONSCIENCE PARFUMS'],
];

$total_deleted = 0;
$total_kept    = 0;

foreach ($projets as $p) {
	echo "\n=== {$p['slug']} ===\n";

	// 1. Trouver le post
	$posts = get_posts([
		'post_type'   => 'projet',
		'name'        => $p['slug'],
		'numberposts' => 1,
	]);
	if (empty($posts)) {
		echo "  ERREUR : Post non trouvé pour slug '{$p['slug']}'\n";
		continue;
	}
	$post_id = $posts[0]->ID;

	// 2. Lister les fichiers RETOURS (seulement images, pas vidéos)
	$retours_dir = $retours_base . $p['retours'] . '/';
	if (!is_dir($retours_dir)) {
		echo "  ERREUR : Dossier RETOURS non trouvé : {$p['retours']}\n";
		continue;
	}

	$retours_files = scandir($retours_dir);
	$retours_basenames = []; // base name sans extension
	$image_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

	foreach ($retours_files as $file) {
		if ($file === '.' || $file === '..') continue;
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if (in_array($ext, $image_exts)) {
			// Stocker le basename sans extension pour pouvoir matcher .webp
			$base = pathinfo($file, PATHINFO_FILENAME);
			$retours_basenames[$base] = $file;
		}
	}

	echo "  RETOURS : " . count($retours_basenames) . " images retenues par Charlotte\n";
	foreach ($retours_basenames as $base => $file) {
		echo "    ✓ $file\n";
	}

	// 3. Scanner le dossier thème
	$theme_dir_path = $img_base . $p['folder'] . '/';
	if (!is_dir($theme_dir_path)) {
		echo "  ERREUR : Dossier thème non trouvé : {$p['folder']}\n";
		continue;
	}

	$theme_files = scandir($theme_dir_path);
	$to_keep   = [];
	$to_delete = [];

	foreach ($theme_files as $file) {
		if ($file === '.' || $file === '..') continue;
		$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if (!in_array($ext, $image_exts)) continue;

		$base = pathinfo($file, PATHINFO_FILENAME);

		// L'image est gardée si son basename (sans extension) est dans RETOURS
		if (isset($retours_basenames[$base])) {
			$to_keep[] = $file;
		} else {
			$to_delete[] = $file;
		}
	}

	echo "  Thème : " . count($to_keep) . " à garder, " . count($to_delete) . " à supprimer\n";

	// 4. Supprimer les fichiers physiques
	foreach ($to_delete as $file) {
		$filepath = $theme_dir_path . $file;
		if (unlink($filepath)) {
			echo "    ✗ Supprimé : $file\n";
			$total_deleted++;
		} else {
			echo "    ERREUR : Impossible de supprimer $file\n";
		}
	}

	// 5. Reconstruire la galerie
	// Cover (0.*) en premier, puis le reste en webp si dispo, sinon original
	$new_gallery = [];
	$cover_entry = null;
	$other_entries = [];

	foreach ($to_keep as $file) {
		$base = pathinfo($file, PATHINFO_FILENAME);
		$ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		$entry = $p['folder'] . '/' . $file;

		if ($base === '0') {
			// Préférer webp pour la cover si disponible
			if ($cover_entry === null || $ext === 'webp') {
				$cover_entry = $entry;
			}
		} else {
			// Préférer webp pour les autres
			$existing_key = null;
			foreach ($other_entries as $k => $e) {
				if (pathinfo(basename($e), PATHINFO_FILENAME) === $base) {
					$existing_key = $k;
					break;
				}
			}
			if ($existing_key !== null) {
				// On a déjà une version, préférer webp
				if ($ext === 'webp') {
					$other_entries[$existing_key] = $entry;
				}
			} else {
				$other_entries[] = $entry;
			}
		}
	}

	// Assembler : cover en premier
	if ($cover_entry) {
		$new_gallery[] = $cover_entry;
	}
	$new_gallery = array_merge($new_gallery, $other_entries);
	$total_kept += count($new_gallery);

	// Mettre à jour la galerie WordPress
	update_post_meta($post_id, '_projet_gallery', $new_gallery);
	echo "  Galerie mise à jour : " . count($new_gallery) . " images\n";
	foreach ($new_gallery as $entry) {
		echo "    → $entry\n";
	}
}

echo "\n========================================\n";
echo "Terminé !\n";
echo "  Images supprimées : $total_deleted\n";
echo "  Images conservées : $total_kept\n";
