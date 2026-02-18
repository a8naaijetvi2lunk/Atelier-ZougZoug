<?php
/**
 * Copie les vidéos RETOURS vers le thème, génère les thumbnails, met à jour les galeries.
 * Prérequis : ffmpeg installé (sudo apt install -y ffmpeg)
 *
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/sync-videos.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

// Vérifier ffmpeg (binaire statique local ou système)
$ffmpeg = trim(shell_exec('which ffmpeg 2>/dev/null'));
if (empty($ffmpeg) || !is_executable($ffmpeg)) {
	// Fallback : binaire local
	$ffmpeg = '/home/zougzoug/.local/bin/ffmpeg';
}
if (!is_executable($ffmpeg)) {
	echo "ERREUR : ffmpeg n'est pas installé.\n";
	exit(1);
}
echo "ffmpeg trouvé : $ffmpeg\n\n";

$theme_dir    = get_template_directory();
$img_base     = $theme_dir . '/assets/img/projets/';
$retours_base = '/home/zougzoug/htdocs/zougzoug.lan/DOCUMENTATIONS/RETOURS-180226/PROJETS_COLLAB B2B/';

// Mapping : slug WP → dossier thème → videos RETOURS (chemin relatif dans RETOURS)
$projets_videos = [
	[
		'slug'   => 'benoit-castel',
		'folder' => 'benoit-castel',
		'videos' => [
			'1-ART-DE-LA-TABLE/BENOIT-CASTEL/downloadgram.org_AQPFdw8xsbqq8qP1zWhFSR_OIhV2Bbf1coe1m7VLlmBaOwiSfAqxl3svQzJ4AkJ7dD36DLHLZSgEsnXpTlbQkvKu4aiF_Xr7Z7yueq8.mp4' => 'video-1.mp4',
		],
	],
	[
		'slug'   => 'verre-a-pied',
		'folder' => 'verre-a-pied',
		'videos' => [
			'1-ART-DE-LA-TABLE/VERRE-A-PIED/downloadgram.org_AQMxTmbfJGcEZae0vgxkCe31diHuGMH-bURR-0thsSudashS2wI2sx4WglN-fdOY0X3eP-rByhajRjTgn1Ezmg49RRZ9i_gRLlwUnLA.mp4' => 'video-1.mp4',
		],
	],
	[
		'slug'   => 'padam-hotel-artefak',
		'folder' => 'padam',
		'videos' => [
			'2-LUMINAIRES/PADAM-HOTEL-ARTEFAK/downloadgram.org_AQM6Kgr_Li3hwcpiZKFjnIkV1D5zBWjJdTndU5iOIv9VWETGhkm0j9BwAsys0hSQrPZNaHSvJ7B8S9DqiQ8LO4Kh.mp4' => 'video-1.mp4',
			'2-LUMINAIRES/PADAM-HOTEL-ARTEFAK/PADAM_video-process.mp4' => 'video-2.mp4',
		],
	],
	[
		'slug'   => 'sella-st-barth',
		'folder' => 'sella',
		'videos' => [
			'2-LUMINAIRES/SELLA-StBARTH/downloadgram.org_AQPft-vEKHe698XxLTJc9-EizAwJ0c0dmM-bXeQPjyopUYgyoUCUYUHzi3VGCy5UCJVlm1l5_mDGFg1yu6ZJ44-WKehU70aZ8xSCzK4.mp4' => 'video-1.mp4',
		],
	],
	[
		'slug'   => 'conscience-parfums',
		'folder' => 'conscience-parfums',
		'videos' => [
			'3-ACCESSOIRES-AMBIANCE/CONSCIENCE PARFUMS/downloadgram.org_AQN_X0kXfwI8mDjKdL7pFnzaNHXPLb6gVymKpPP0AkSa8mbCruU4XQgBs6NnbpjQuP-XGzsNQ8KHkRUMyS8BqlZ2.mp4' => 'video-1.mp4',
		],
	],
];

$total_copied = 0;
$total_posters = 0;

foreach ($projets_videos as $p) {
	echo "=== {$p['slug']} ===\n";

	// Trouver le post
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

	$dest_dir = $img_base . $p['folder'] . '/';
	if (!is_dir($dest_dir)) {
		echo "  ERREUR : Dossier thème non trouvé : {$p['folder']}\n";
		continue;
	}

	// Galerie actuelle
	$gallery = get_post_meta($post_id, '_projet_gallery', true);
	if (!is_array($gallery)) $gallery = [];

	foreach ($p['videos'] as $retours_rel => $dest_name) {
		$src_path  = $retours_base . $retours_rel;
		$dest_path = $dest_dir . $dest_name;
		$gallery_entry = $p['folder'] . '/' . $dest_name;

		// 1. Copier la vidéo
		if (!file_exists($src_path)) {
			echo "  ERREUR : Fichier source non trouvé : $retours_rel\n";
			continue;
		}

		if (!file_exists($dest_path)) {
			copy($src_path, $dest_path);
			echo "  Vidéo copiée : $dest_name (" . round(filesize($dest_path) / 1024) . " KB)\n";
			$total_copied++;
		} else {
			echo "  Vidéo existe déjà : $dest_name\n";
		}

		// 2. Générer le poster (thumbnail)
		$poster_name = str_replace('.mp4', '-poster.jpg', $dest_name);
		$poster_path = $dest_dir . $poster_name;

		if (!file_exists($poster_path)) {
			$cmd = sprintf(
				'%s -i %s -ss 00:00:01 -vframes 1 -q:v 2 %s 2>&1',
				escapeshellarg($ffmpeg),
				escapeshellarg($dest_path),
				escapeshellarg($poster_path)
			);
			$output = shell_exec($cmd);

			if (file_exists($poster_path)) {
				echo "  Poster généré : $poster_name (" . round(filesize($poster_path) / 1024) . " KB)\n";
				$total_posters++;
			} else {
				// Essayer à 0 seconde si la vidéo est très courte
				$cmd2 = sprintf(
					'%s -i %s -ss 00:00:00 -vframes 1 -q:v 2 %s 2>&1',
					escapeshellarg($ffmpeg),
					escapeshellarg($dest_path),
					escapeshellarg($poster_path)
				);
				shell_exec($cmd2);

				if (file_exists($poster_path)) {
					echo "  Poster généré (frame 0) : $poster_name (" . round(filesize($poster_path) / 1024) . " KB)\n";
					$total_posters++;
				} else {
					echo "  ERREUR : Impossible de générer le poster pour $dest_name\n";
				}
			}
		} else {
			echo "  Poster existe déjà : $poster_name\n";
		}

		// 3. Ajouter à la galerie si pas déjà présent
		if (!in_array($gallery_entry, $gallery)) {
			$gallery[] = $gallery_entry;
			echo "  Ajouté à la galerie : $gallery_entry\n";
		} else {
			echo "  Déjà dans la galerie : $gallery_entry\n";
		}
	}

	// Mettre à jour la galerie
	update_post_meta($post_id, '_projet_gallery', $gallery);
	echo "  Galerie totale : " . count($gallery) . " médias\n";
}

echo "\n========================================\n";
echo "Terminé !\n";
echo "  Vidéos copiées : $total_copied\n";
echo "  Posters générés : $total_posters\n";
