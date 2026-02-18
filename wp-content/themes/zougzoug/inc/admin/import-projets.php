<?php
/**
 * Import des 13 projets dans le CPT depuis les donnees hardcodees
 * Lancer via : wp eval-file wp-content/themes/zougzoug/inc/admin/import-projets.php
 * ou via l'admin (bouton dans le dashboard)
 */

if (!defined('ABSPATH')) {
	// Si execute via WP CLI
	$wp_load = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
	if (file_exists($wp_load)) {
		require_once $wp_load;
	} else {
		die('Cannot load WordPress.');
	}
}

if (!current_user_can('manage_options') && !defined('WP_CLI')) {
	die('Permission denied.');
}

$projets = [
	[
		'title'      => 'Becquetance',
		'category'   => 'art-de-la-table',
		'client'     => 'Becquetance',
		'year'       => '2021',
		'location'   => '67 Rue de Menilmontant, 75020 Paris',
		'short_desc' => 'Neo-bistrot dedie aux vins natures, Paris 20e. 150 pieces — 8 modeles en gres chamotte, emaux artisanaux.',
		'description' => "Creation sur mesure des formes et des couleurs d'un service complet d'assiettes, bols et petites verseuses pour l'ouverture d'un neo-bistrot dedie aux vins natures.\nHuit formats concus et proteges en collaboration avec la cheffe et son associe.\nTerres et emaux developpes specifiquement pour s'integrer a l'identite visuelle et architecturale du lieu.",
		'folder'     => 'becquetance',
		'medias'     => [
			'DSCF3613.webp','DSCF3624-r.webp','DSCF3667.webp','DSCF3668.webp','DSCF3681.webp',
			'downloadgram.org_269877960_620223445689368_3036371776655481487_n.webp',
			'downloadgram.org_269878437_115744217469727_3292068785033428973_n.webp',
			'downloadgram.org_269888768_141442848242651_7679837260499242259_n.webp',
			'downloadgram.org_274599237_153171277076956_719021004122873990_n.webp',
			'downloadgram.org_274777683_662625885156758_2440117904076109296_n.webp',
			'downloadgram.org_274839839_483172770125836_4603103867106299023_n.webp',
			'IMG_9677.webp','IMG_9678.webp',
		],
	],
	[
		'title'      => 'Benoit Castel',
		'category'   => 'art-de-la-table',
		'client'     => 'Benoit Castel',
		'year'       => '2022',
		'location'   => '11 rue Sorbier 75020 Paris',
		'short_desc' => "Assiettes a brunch co-signees — 30 pieces, gres blanc chamotte. Paris 20e.",
		'description' => "Modele d'assiette co-signee avec le logo/emporte piece emblematique de ces patisseries-boulangeries. A retrouver a la table de la toute derniere adresse de Benoit Castel dans laquelle il devoile sa version tres personnelle du Coffee Shop a la francaise.",
		'folder'     => 'benoit-castel',
		'medias'     => [
			'217-_J0A0441.webp',
			'downloadgram.org_275311568_482117150225384_3332949142901984635_n.webp',
			'IMG_1832.webp','IMG_1837.webp','IMG_1956.webp',
		],
	],
	[
		'title'      => 'Creme — Table',
		'category'   => 'art-de-la-table',
		'client'     => 'Creme',
		'year'       => '',
		'location'   => '',
		'short_desc' => '90 pieces de vaisselle de service — 3 modeles. Assiettes et bols sur mesure.',
		'description' => '',
		'folder'     => 'creme-table',
		'medias'     => [
			'DSCF6069.webp','DSCF6074.webp','DSCF6077.webp','DSCF6080.webp','DSCF6091.webp',
			'CA0CB4F7-E7AB-4EBE-B89E-57A580901EAB.webp',
			'downloadgram.org_278342992_163511689375525_5306329141465571146_n.webp',
			'IMG_4402 2.webp','IMG_4420.webp','IMG_4421.webp','IMG_4424.webp',
		],
	],
	[
		'title'      => 'Maison Fragile',
		'category'   => 'art-de-la-table',
		'client'     => 'Maison Fragile',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Collaboration avec la maison Fragile — pieces en ceramique artisanale.',
		'description' => '',
		'folder'     => 'maison-fragile',
		'medias'     => [
			'downloadgram.org_278112606_139870021900017_2569186848507632771_n.webp',
			'downloadgram.org_278133694_692956411827525_4348149551125668260_n.webp',
			'downloadgram.org_278167726_683434382887994_5082999112652012459_n.webp',
			'downloadgram.org_278349632_144242204779161_7011056578157915950_n.webp',
			'downloadgram.org_278377937_695546144963527_6780521512057319355_n.webp',
		],
	],
	[
		'title'      => 'Petite Marmelade',
		'category'   => 'art-de-la-table',
		'client'     => 'Petite Marmelade',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Vaisselle artisanale pour Petite Marmelade — boulangerie et patisserie.',
		'description' => '',
		'folder'     => 'petite-marmelade',
		'medias'     => [
			'downloadgram.org_278183442_163766626089792_5475291044375204893_n.webp',
			'downloadgram.org_278226924_1060796477835505_7990820990676377329_n.webp',
			'downloadgram.org_278232310_120994683872668_684337859033220325_n.webp',
			'downloadgram.org_278287079_293641392973312_1554758205585481807_n.webp',
			'downloadgram.org_278289491_3341579206123170_6905602061411080257_n.webp',
			'downloadgram.org_278386459_3165055947044008_5123505100292474_n.webp',
			'downloadgram.org_278469068_512459050587497_7733611645726987305_n.webp',
		],
	],
	[
		'title'      => "Pilo's",
		'category'   => 'art-de-la-table',
		'client'     => "Pilo's",
		'year'       => '',
		'location'   => '',
		'short_desc' => "Vaisselle sur mesure pour le restaurant Pilo's.",
		'description' => '',
		'folder'     => 'pilos',
		'medias'     => [
			'412645938_18314908273185983_7989985121437337618_n.webp',
			'529198980_18394179391185983_3871492673526886026_n.webp',
			'downloadgram.org_307107129_414228080769927_879674985408518752_n.webp',
			'downloadgram.org_307271850_470658438277100_8190698992983714980_n.webp',
			'downloadgram.org_307306848_456348879887106_2932342839040979089_n.webp',
		],
	],
	[
		'title'      => 'Verre a Pied',
		'category'   => 'art-de-la-table',
		'client'     => 'Verre a Pied',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Coupes, verres et pichets en ceramique — pieces uniques tournees a la main.',
		'description' => '',
		'folder'     => 'verre-a-pied',
		'medias'     => [
			'trio-verre-pichet-gargoulette_2.webp',
			'coupe-verre-1.webp','coupe-verre-2.webp',
			'fete-1.webp','fete-2.webp','fete-3.webp',
			'clay-market-1.webp','clay-market-2.webp','clay-market-3.webp',
		],
	],
	[
		'title'      => 'Adele',
		'category'   => 'luminaires',
		'client'     => 'Adele',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Collection de lampes a poser en ceramique — formes sculpturales, finitions mates.',
		'description' => '',
		'folder'     => 'adele',
		'medias'     => [
			'Lampe-01.webp','Lampe-02-1.webp','Lampe-03.webp','Lampe-04.webp','Lampe-05.webp',
			'Lampe-06.webp','Lampe-07.webp','Lampe-08.webp','Lampe-09.webp',
			'downloadgram.org_318260502_2195010077338371_6055511546855892423_n.webp',
		],
	],
	[
		'title'      => 'Creme — Luminaires',
		'category'   => 'luminaires',
		'client'     => 'Creme',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Luminaires sur mesure en ceramique pour Creme — suspensions et appliques.',
		'description' => '',
		'folder'     => 'creme-luminaires',
		'medias'     => [
			'downloadgram.org_246023987_974080243487790_4370924502546696389_n.webp',
			'downloadgram.org_246524759_1027256991443448_5315601361393949503_n.webp',
			'downloadgram.org_246533635_286159300027685_2623138344812117477_n.webp',
			'downloadgram.org_246655977_836252637062664_6648278962588396934_n.webp',
			'downloadgram.org_246795442_4733866956623943_4726270182518974905_n.webp',
			'downloadgram.org_247031921_400068685156543_6581474505444348177_n.webp',
			'downloadgram.org_247039914_948072805804464_2995768135556174987_n.webp',
			'downloadgram.org_247080272_835913547098414_5645448423898254945_n.webp',
			'downloadgram.org_274905528_1350751368726386_2008200217404557015_n.webp',
			'downloadgram.org_274966698_1594616027592442_4943759295318654_n.webp',
			'downloadgram.org_277977131_1014215875868053_2902263390976114550_n.webp',
			'downloadgram.org_278002422_158094919962767_942882727081660497_n.webp',
			'downloadgram.org_278012736_121741367128333_759898904684442410_n.webp',
			'downloadgram.org_278128178_304424378481282_1759763784188816268_n.webp',
		],
	],
	[
		'title'      => 'Padam Hotel / Artefak',
		'category'   => 'luminaires',
		'client'     => 'Padam Hotel / Artefak',
		'year'       => '',
		'location'   => '',
		'short_desc' => "Luminaires sur mesure pour l'hotel Le Padam — en collaboration avec Artefak.",
		'description' => '',
		'folder'     => 'padam',
		'medias'     => [
			'678fa52ec38497f293f81d20_20240412-Edith@SimonDetraz-146.webp',
			'hotel-le-padam-chambres-173470-2400-2400-auto.webp',
			'15.webp','16.webp','17.webp',
			'IMG_7109.webp',
		],
	],
	[
		'title'      => 'Sella St Barth',
		'category'   => 'luminaires',
		'client'     => 'Sella St Barth',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Luminaires en ceramique pour Sella, St Barthelemy.',
		'description' => '',
		'folder'     => 'sella',
		'medias'     => [
			'downloadgram.org_303222843_1094195011457419_847084932020638827_n.webp',
			'downloadgram.org_303314357_3339080109700925_1349332588085582290_n.webp',
			'downloadgram.org_304749384_1559345817828781_8285200381504553_n.webp',
			'downloadgram.org_304916644_932748734781962_1064384184609911015_n.webp',
		],
	],
	[
		'title'      => 'Carhartt WIP',
		'category'   => 'accessoires',
		'client'     => 'Carhartt WIP',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Collaboration avec Carhartt WIP — pieces en ceramique en edition limitee.',
		'description' => '',
		'folder'     => 'carhartt',
		'medias'     => [
			'DSCF4348.webp','DSCF4355.webp','DSCF4385.webp',
			'downloadgram.org_259833148_2791308511167393_780146704438046472_n.webp',
		],
	],
	[
		'title'      => 'Conscience Parfums',
		'category'   => 'accessoires',
		'client'     => 'Conscience Parfums',
		'year'       => '',
		'location'   => '',
		'short_desc' => 'Brule-parfums en ceramique artisanale pour Conscience Parfums.',
		'description' => '',
		'folder'     => 'conscience-parfums',
		'medias'     => [
			'downloadgram.org_316121412_658212345903385_1973795775866386581_n.webp',
			'downloadgram.org_316492279_2351276768361833_7397725631778115059_n.webp',
			'downloadgram.org_316513660_3264108260505191_7294030422919262910_n.webp',
			'downloadgram.org_316579918_3322300788031820_4210834688253571744_n.webp',
			'downloadgram.org_316597716_3248452098734910_8257732803228761756_n.webp',
			'downloadgram.org_316747418_3196936940557976_7578205646431124361_n.webp',
		],
	],
];

// Category slug mapping
$cat_map = [
	'art-de-la-table' => 'table',
	'luminaires'      => 'luminaires',
	'accessoires'     => 'accessoires',
];

$theme_dir = get_template_directory();
$imported = 0;
$errors = [];

foreach ($projets as $p) {
	// Check if already imported
	$existing = get_posts([
		'post_type'  => 'projet',
		'title'      => $p['title'],
		'numberposts' => 1,
	]);

	if (!empty($existing)) {
		continue;
	}

	// Create post
	$post_id = wp_insert_post([
		'post_type'   => 'projet',
		'post_title'  => $p['title'],
		'post_status' => 'publish',
	]);

	if (is_wp_error($post_id)) {
		$errors[] = 'Failed to create: ' . $p['title'];
		continue;
	}

	// Assign taxonomy
	wp_set_object_terms($post_id, $p['category'], 'categorie_projet');

	// Meta fields
	update_post_meta($post_id, '_projet_client', $p['client']);
	update_post_meta($post_id, '_projet_year', $p['year']);
	update_post_meta($post_id, '_projet_location', $p['location']);
	update_post_meta($post_id, '_projet_short_desc', $p['short_desc']);
	update_post_meta($post_id, '_projet_description', $p['description']);

	// Gallery — store filenames with folder as reference
	// (real media import would need sideloading each file)
	$gallery_paths = [];
	foreach ($p['medias'] as $media) {
		$gallery_paths[] = $p['folder'] . '/' . $media;
	}
	update_post_meta($post_id, '_projet_gallery', $gallery_paths);

	// Set first image as featured image (attempt to find in media library)
	$first_img = $theme_dir . '/assets/img/projets/' . $p['folder'] . '/' . $p['medias'][0];
	if (file_exists($first_img)) {
		// Sideload the first image as featured
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$filetype = wp_check_filetype($first_img);
		$upload_dir = wp_upload_dir();

		$filename = wp_unique_filename($upload_dir['path'], basename($first_img));
		$new_file = $upload_dir['path'] . '/' . $filename;

		if (copy($first_img, $new_file)) {
			$attachment_id = wp_insert_attachment([
				'post_mime_type' => $filetype['type'],
				'post_title'    => sanitize_file_name(basename($first_img)),
				'post_content'  => '',
				'post_status'   => 'inherit',
			], $new_file, $post_id);

			if (!is_wp_error($attachment_id)) {
				$metadata = wp_generate_attachment_metadata($attachment_id, $new_file);
				wp_update_attachment_metadata($attachment_id, $metadata);
				set_post_thumbnail($post_id, $attachment_id);
			}
		}
	}

	$imported++;
}

$msg = "Import termine : $imported projets importes.";
if (!empty($errors)) {
	$msg .= ' Erreurs : ' . implode(', ', $errors);
}

if (defined('WP_CLI')) {
	WP_CLI::success($msg);
} else {
	echo '<div class="notice notice-success zz-notice"><p>' . esc_html($msg) . '</p></div>';
}
