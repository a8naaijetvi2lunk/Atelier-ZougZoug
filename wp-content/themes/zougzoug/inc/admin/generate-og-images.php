<?php
/**
 * Generation des images OG (1200x630) pour chaque page
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/generate-og-images.php
 *
 * Compose : image principale (crop) + overlay degrade noir + nom page + "Atelier ZougZoug"
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

$OG_WIDTH  = 1200;
$OG_HEIGHT = 630;
$QUALITY   = 85;
$FONT      = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
$FONT_BOLD = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';

$upload_dir = wp_upload_dir();
$og_dir = $upload_dir['basedir'] . '/site/og';
if (!is_dir($og_dir)) {
	wp_mkdir_p($og_dir);
}

/**
 * Pages et leurs images sources + titres
 */
$pages = [
	'home' => [
		'title'    => 'Charlotte Auroux',
		'subtitle' => 'Céramiste à Brioude',
		'image_key' => function ($data) {
			// Premier slide hero, image gauche
			return $data['hero']['slides'][0]['left'] ?? null;
		},
	],
	'about' => [
		'title'    => 'À propos',
		'subtitle' => 'Charlotte Auroux, céramiste',
		'image_key' => function ($data) {
			return $data['hero']['portrait'] ?? null;
		},
	],
	'contact' => [
		'title'    => 'Contact',
		'subtitle' => 'Atelier ZougZoug, Brioude',
		'image_key' => function ($data) {
			return $data['info']['photo'] ?? null;
		},
	],
	'cours' => [
		'title'    => 'Cours de céramique',
		'subtitle' => 'À Brioude, en Auvergne',
		'image_key' => function ($data) {
			return $data['hero']['image'] ?? null;
		},
	],
	'revendeurs' => [
		'title'    => 'Revendeurs & Évènements',
		'subtitle' => 'Boutiques et marchés',
		'image_key' => function ($data) {
			$images = $data['photos']['images'] ?? [];
			return $images[0]['attachment_id'] ?? null;
		},
	],
];

foreach ($pages as $slug => $config) {
	echo "=== {$slug} ===\n";

	// Charger les donnees JSON
	$data = zz_get_data($slug);
	if (empty($data)) {
		echo "  SKIP: pas de donnees\n";
		continue;
	}

	// Recuperer l'image source
	$image_id = ($config['image_key'])($data);
	if (!$image_id || !is_numeric($image_id)) {
		echo "  SKIP: pas d'image source (value: " . var_export($image_id, true) . ")\n";
		continue;
	}

	$source_path = get_attached_file(intval($image_id));
	if (!$source_path || !file_exists($source_path)) {
		echo "  SKIP: fichier source introuvable (ID #{$image_id})\n";
		continue;
	}

	echo "  Source: {$source_path}\n";

	try {
		// Charger l'image source
		$img = new Imagick($source_path);

		// Crop centré en 1200x630
		$img->setImageGravity(Imagick::GRAVITY_CENTER);
		$img->cropThumbnailImage($OG_WIDTH, $OG_HEIGHT);
		$img->setImagePage($OG_WIDTH, $OG_HEIGHT, 0, 0);

		// Overlay degrade noir (bas → haut)
		$overlay = new Imagick();
		$overlay->newPseudoImage($OG_WIDTH, $OG_HEIGHT, 'gradient:rgba(0,0,0,0.7)-rgba(0,0,0,0.1)');
		// Le gradient va du noir en haut au transparent en bas — on le flip
		$overlay->flipImage();
		$overlay->setImagePage($OG_WIDTH, $OG_HEIGHT, 0, 0);

		$img->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);
		$overlay->destroy();

		// Texte
		$draw = new ImagickDraw();

		// Titre principal (page name) — blanc, gros
		$draw->setFont($FONT_BOLD);
		$draw->setFontSize(52);
		$draw->setFillColor(new ImagickPixel('#FFFFFF'));
		$draw->setTextAlignment(Imagick::ALIGN_LEFT);
		$draw->setFontWeight(700);

		$img->annotateImage($draw, 60, $OG_HEIGHT - 130, 0, $config['title']);

		// Sous-titre — blanc transparent, plus petit
		$draw->setFont($FONT);
		$draw->setFontSize(28);
		$draw->setFillColor(new ImagickPixel('rgba(255,255,255,0.8)'));
		$draw->setFontWeight(400);

		$img->annotateImage($draw, 60, $OG_HEIGHT - 85, 0, $config['subtitle']);

		// "Atelier ZougZoug" — en haut a gauche, petit
		$draw->setFont($FONT);
		$draw->setFontSize(18);
		$draw->setFillColor(new ImagickPixel('rgba(255,255,255,0.6)'));
		$draw->setTextAlignment(Imagick::ALIGN_LEFT);
		$draw->setFontWeight(400);

		// Lettre-spacing simulé via espacement
		$img->annotateImage($draw, 60, 50, 0, 'ATELIER  ZOUGZOUG');

		// Ligne fine blanche en bas
		$line = new ImagickDraw();
		$line->setStrokeColor(new ImagickPixel('rgba(255,255,255,0.3)'));
		$line->setStrokeWidth(1);
		$line->line(60, $OG_HEIGHT - 60, $OG_WIDTH - 60, $OG_HEIGHT - 60);
		$img->drawImage($line);

		$draw->destroy();
		$line->destroy();

		// Sauvegarder
		$dest = $og_dir . '/og-' . $slug . '.webp';
		$img->setImageFormat('webp');
		$img->setImageCompressionQuality($QUALITY);
		$img->writeImage($dest);

		$kb = round(filesize($dest) / 1024);
		echo "  OK: {$dest} ({$OG_WIDTH}x{$OG_HEIGHT}, {$kb}KB)\n";

		// Creer/mettre a jour l'attachment WordPress
		$att_data = [
			'post_title'     => 'og-' . $slug,
			'post_mime_type' => 'image/webp',
			'post_status'    => 'inherit',
		];

		// Chercher un attachment existant
		$existing = get_posts([
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'title'          => 'og-' . $slug,
			'posts_per_page' => 1,
		]);

		if ($existing) {
			$att_id = $existing[0]->ID;
			update_attached_file($att_id, $dest);
			echo "  Attachment mis a jour: #{$att_id}\n";
		} else {
			$att_id = wp_insert_attachment($att_data, $dest);
			echo "  Attachment cree: #{$att_id}\n";
		}

		update_post_meta($att_id, '_wp_attached_file', 'site/og/og-' . $slug . '.webp');
		$meta = wp_generate_attachment_metadata($att_id, $dest);
		wp_update_attachment_metadata($att_id, $meta);

		// Mettre a jour le JSON avec l'attachment ID
		$json_file = get_template_directory() . '/data/' . $slug . '.json';
		if (file_exists($json_file)) {
			$json_data = json_decode(file_get_contents($json_file), true);
			if (isset($json_data['seo'])) {
				$json_data['seo']['og_image'] = $att_id;
				file_put_contents(
					$json_file,
					json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
				);
				echo "  JSON mis a jour: seo.og_image = #{$att_id}\n";
			}
		}

		$img->destroy();

	} catch (Exception $e) {
		echo "  ERREUR: " . $e->getMessage() . "\n";
	}
}

// Mettre a jour les canoniques automatiquement
echo "\n=== Mise a jour des canoniques ===\n";
$canonical_map = [
	'home'       => '/',
	'about'      => '/a-propos/',
	'contact'    => '/contact/',
	'cours'      => '/cours/',
	'revendeurs' => '/revendeurs/',
];

foreach ($canonical_map as $slug => $path) {
	$json_file = get_template_directory() . '/data/' . $slug . '.json';
	if (!file_exists($json_file)) continue;

	$json_data = json_decode(file_get_contents($json_file), true);
	if (!isset($json_data['seo'])) continue;

	$canonical = home_url($path);
	$json_data['seo']['canonical'] = $canonical;
	file_put_contents(
		$json_file,
		json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
	);
	echo "  {$slug}: {$canonical}\n";
}

echo "\nTermine.\n";
