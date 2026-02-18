<?php
/**
 * Generation de l'image OG mosaique pour la page Collaborations
 *
 * Usage CLI : wp eval-file wp-content/themes/zougzoug/inc/admin/generate-og-mosaic.php
 * Usage auto : Se declenche via hook save_post_projet / delete_post (voir seo.php)
 *
 * Cree une grille de thumbnails projets (4 colonnes x 2 lignes) + overlay + texte
 */

/**
 * Genere l'image OG mosaique Collaborations
 *
 * @param bool $verbose  Afficher les logs (true en CLI, false en auto)
 * @return int|false     Attachment ID ou false en cas d'erreur
 */
function zz_generate_og_mosaic($verbose = false) {
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

	// Recuperer les projets avec thumbnails
	$projets = get_posts([
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]);

	$images = [];
	foreach ($projets as $p) {
		$thumb_id = get_post_thumbnail_id($p->ID);
		if (!$thumb_id) continue;
		$path = get_attached_file($thumb_id);
		if ($path && file_exists($path)) {
			$images[] = $path;
		}
	}

	$nb_projets = count($projets);
	if ($verbose) echo $nb_projets . " projets, " . count($images) . " images trouvees\n";

	if (count($images) < 4) {
		if ($verbose) echo "ERREUR: pas assez d'images (minimum 4)\n";
		return false;
	}

	// Grille : 4 colonnes x 2 lignes = 8 images
	$cols = 4;
	$rows = 2;
	$cell_w = $OG_WIDTH / $cols;
	$cell_h = $OG_HEIGHT / $rows;
	$total_cells = $cols * $rows;

	// Prendre 8 images reparties uniformement
	$selected = [];
	$step = count($images) / $total_cells;
	for ($i = 0; $i < $total_cells; $i++) {
		$idx = min(intval($i * $step), count($images) - 1);
		$selected[] = $images[$idx];
	}

	try {
		// Canvas
		$canvas = new Imagick();
		$canvas->newImage($OG_WIDTH, $OG_HEIGHT, new ImagickPixel('#1A1A1A'));
		$canvas->setImageFormat('webp');

		// Placer chaque image dans la grille
		for ($i = 0; $i < count($selected); $i++) {
			$col = $i % $cols;
			$row = intval($i / $cols);
			$x = intval($col * $cell_w);
			$y = intval($row * $cell_h);

			$cell = new Imagick($selected[$i]);
			$cell->setImageGravity(Imagick::GRAVITY_CENTER);
			$cell->cropThumbnailImage(intval($cell_w), intval($cell_h));
			$cell->setImagePage(intval($cell_w), intval($cell_h), 0, 0);

			$canvas->compositeImage($cell, Imagick::COMPOSITE_OVER, $x, $y);
			$cell->destroy();

			if ($verbose) echo "  Cell [{$row},{$col}]: " . basename($selected[$i]) . "\n";
		}

		// Overlay degrade noir fort (pour lisibilite du texte)
		$overlay = new Imagick();
		$overlay->newPseudoImage($OG_WIDTH, $OG_HEIGHT, 'gradient:rgba(0,0,0,0.8)-rgba(0,0,0,0.3)');
		$overlay->flipImage();
		$overlay->setImagePage($OG_WIDTH, $OG_HEIGHT, 0, 0);
		$canvas->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);
		$overlay->destroy();

		// Texte
		$draw = new ImagickDraw();

		// "ATELIER  ZOUGZOUG" en haut
		$draw->setFont($FONT);
		$draw->setFontSize(18);
		$draw->setFillColor(new ImagickPixel('rgba(255,255,255,0.6)'));
		$draw->setTextAlignment(Imagick::ALIGN_LEFT);
		$canvas->annotateImage($draw, 60, 50, 0, 'ATELIER  ZOUGZOUG');

		// Titre
		$draw->setFont($FONT_BOLD);
		$draw->setFontSize(52);
		$draw->setFillColor(new ImagickPixel('#FFFFFF'));
		$draw->setFontWeight(700);
		$canvas->annotateImage($draw, 60, $OG_HEIGHT - 130, 0, 'Collaborations');

		// Sous-titre dynamique
		$subtitle = $nb_projets . ' projets céramique sur mesure';
		$draw->setFont($FONT);
		$draw->setFontSize(28);
		$draw->setFillColor(new ImagickPixel('rgba(255,255,255,0.8)'));
		$draw->setFontWeight(400);
		$canvas->annotateImage($draw, 60, $OG_HEIGHT - 85, 0, $subtitle);

		// Ligne
		$line = new ImagickDraw();
		$line->setStrokeColor(new ImagickPixel('rgba(255,255,255,0.3)'));
		$line->setStrokeWidth(1);
		$line->line(60, $OG_HEIGHT - 60, $OG_WIDTH - 60, $OG_HEIGHT - 60);
		$canvas->drawImage($line);

		$draw->destroy();
		$line->destroy();

		// Sauvegarder
		$dest = $og_dir . '/og-collaborations.webp';
		$canvas->setImageCompressionQuality($QUALITY);
		$canvas->writeImage($dest);
		$canvas->destroy();

		$kb = round(filesize($dest) / 1024);
		if ($verbose) echo "\nOK: {$dest} ({$OG_WIDTH}x{$OG_HEIGHT}, {$kb}KB)\n";

		// Creer/mettre a jour l'attachment
		$existing = get_posts([
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'title'          => 'og-collaborations',
			'posts_per_page' => 1,
		]);

		if ($existing) {
			$att_id = $existing[0]->ID;
			update_attached_file($att_id, $dest);
			if ($verbose) echo "Attachment mis a jour: #{$att_id}\n";
		} else {
			$att_data = [
				'post_title'     => 'og-collaborations',
				'post_mime_type' => 'image/webp',
				'post_status'    => 'inherit',
			];
			$att_id = wp_insert_attachment($att_data, $dest);
			if ($verbose) echo "Attachment cree: #{$att_id}\n";
		}

		update_post_meta($att_id, '_wp_attached_file', 'site/og/og-collaborations.webp');
		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$meta = wp_generate_attachment_metadata($att_id, $dest);
		wp_update_attachment_metadata($att_id, $meta);

		// Mettre a jour collaborations.json
		$json_file = get_template_directory() . '/data/collaborations.json';
		if (file_exists($json_file)) {
			$json_data = json_decode(file_get_contents($json_file), true);
			$json_data['seo']['og_image'] = $att_id;
			$json_data['seo']['canonical'] = home_url('/collaborations/');
			file_put_contents(
				$json_file,
				json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
			);
			if ($verbose) echo "collaborations.json mis a jour: og_image = #{$att_id}\n";
		}

		return $att_id;

	} catch (Exception $e) {
		if ($verbose) echo "ERREUR: " . $e->getMessage() . "\n";
		return false;
	}
}

// Execution CLI directe
if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

if (php_sapi_name() === 'cli' || defined('WP_CLI')) {
	zz_generate_og_mosaic(true);
}
