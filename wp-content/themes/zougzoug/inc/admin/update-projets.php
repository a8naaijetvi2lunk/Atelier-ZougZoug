<?php
/**
 * Mise à jour des projets CPT depuis PROJETS-COLLABORATIONS.md
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/update-projets.php
 */

if (!defined('ABSPATH')) {
	// WP CLI context
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

$theme_dir = get_template_directory();
$img_base = $theme_dir . '/assets/img/projets/';
$retours_base = ABSPATH . '../DOCUMENTATIONS/RETOURS-180226/PROJETS_COLLAB B2B/';

// Vérifier que le dossier RETOURS existe
if (!is_dir($retours_base)) {
	$retours_base = dirname(ABSPATH) . '/DOCUMENTATIONS/RETOURS-180226/PROJETS_COLLAB B2B/';
}
if (!is_dir($retours_base)) {
	// Essai chemin absolu
	$retours_base = '/home/zougzoug/htdocs/zougzoug.lan/DOCUMENTATIONS/RETOURS-180226/PROJETS_COLLAB B2B/';
}

echo "Retours base: $retours_base\n";
echo "Images base: $img_base\n\n";

// === DONNÉES DES 13 PROJETS ===
$projets = [

	// ===== ART DE LA TABLE =====
	[
		'slug'        => 'becquetance',
		'title'       => 'Becquetance',
		'category'    => 'art-de-la-table',
		'client'      => 'Becquetance',
		'year'        => '2021',
		'location'    => '67 Rue de Ménilmontant, 75020 Paris',
		'short_desc'  => 'Set d\'assiettes, bols et verseuses — 150 pièces de vaisselle de service, 8 modèles différents.',
		'description' => 'Création sur mesure des formes et des couleurs d\'un service complet d\'assiettes, bols et petites verseuses pour l\'ouverture d\'un néo-bistrot dédié aux vins natures.
Huit formats conçus et protégés en collaboration avec la cheffe et son associé.
Terres et émaux développés spécifiquement pour s\'intégrer à l\'identité visuelle et architecturale du lieu.',
		'folder'      => 'becquetance',
		'retours_dir' => '1-ART-DE-LA-TABLE/BECQUETANCE',
	],
	[
		'slug'        => 'benoit-castel',
		'title'       => 'Benoît Castel',
		'category'    => 'art-de-la-table',
		'client'      => 'Benoît Castel Sorbier',
		'year'        => '2022',
		'location'    => '11 rue Sorbier, 75020 Paris',
		'short_desc'  => 'Assiettes à brunch co-signées — 30 pièces, grès blanc chamotté.',
		'description' => 'Modèle d\'assiette co-signée avec le logo/emporte-pièce emblématique de ces pâtisseries-boulangeries. À retrouver à la table de la toute dernière adresse de Benoît Castel dans laquelle il dévoile sa version très personnelle du Coffee Shop à la française.
Classée en 1914, ce coffee shop remet au goût du jour le petit-déjeuner avec des pancakes salés ou sucrés servis tout au long de la journée et redonne envie de prendre son temps et de savourer pleinement un petit-déjeuner gourmand et équilibré ou tout autre instant plaisir en journée, en intérieur ou en extérieur, selon la saison, sous les arbres, au soleil…',
		'folder'      => 'benoit-castel',
		'retours_dir' => '1-ART-DE-LA-TABLE/BENOIT-CASTEL',
	],
	[
		'slug'        => 'creme-table',
		'title'       => 'Crème — Table',
		'category'    => 'art-de-la-table',
		'client'      => 'Crème Restaurant',
		'year'        => '2022',
		'location'    => '18 Rue Eugène Sue, 75018 Paris',
		'short_desc'  => 'Set d\'assiettes et bols — 90 pièces de vaisselle de service, 3 modèles différents.',
		'description' => 'Après la fabrication de tous les luminaires de ce restaurant, Maxime et Camille ont une nouvelle fois accordé leur confiance à l\'atelier pour compléter leur gamme de vaisselle de service.
Nous avons co-créé une nouvelle série de bols, petites et grandes assiettes. Des lignes sobres, du noir et blanc et une alternance de terre brute et d\'émail satiné. L\'émail blanc des grandes assiettes, a décidé de s\'exprimer dans des variations spectaculaires et incontrôlées de la voie lactée… Passée la première surprise, ce résultat inattendu nous a conquis.',
		'folder'      => 'creme-table',
		'retours_dir' => '1-ART-DE-LA-TABLE/CREME',
	],
	[
		'slug'        => 'maison-fragile',
		'title'       => 'Maison Fragile',
		'category'    => 'art-de-la-table',
		'client'      => 'Maison Fragile',
		'year'        => '2022',
		'location'    => '4 Rue de Jarente, 75004 Paris',
		'short_desc'  => 'Set de coquetiers en série limitée — grès blanc et roux, émaux artisanaux.',
		'description' => 'Marie Castel la fondatrice de Maison Fragile fait de la fragilité une force. Quoi de plus fragile et tout à la fois incroyablement fort qu\'un œuf ?
Coquetiers en série ultra limitée inspirés des "bulles rochers" de la boutique dessinées par le Studio GGSV.',
		'folder'      => 'maison-fragile',
		'retours_dir' => '1-ART-DE-LA-TABLE/MAISON-FRAGILE',
	],
	[
		'slug'        => 'petite-marmelade',
		'title'       => 'Petite Marmelade',
		'category'    => 'art-de-la-table',
		'client'      => 'Petite Marmelade',
		'year'        => '2022',
		'location'    => 'Yvelines',
		'short_desc'  => 'Plats à cake, babka et présentoirs à tartelettes — pâtisserie artisanale itinérante.',
		'description' => 'Set de plats à cake, brioche et présentoirs à tartelettes pour ce food truck itinérant de pâtisseries artisanales, réalisées avec des produits de saison.',
		'folder'      => 'petite-marmelade',
		'retours_dir' => '1-ART-DE-LA-TABLE/PETITE-MARMELADE',
	],
	[
		'slug'        => 'pilos',
		'title'       => 'Pilo\'s',
		'category'    => 'art-de-la-table',
		'client'      => 'Pilo\'s',
		'year'        => '2022',
		'location'    => '1 Av. du Père Lachaise, 75020 Paris',
		'short_desc'  => 'Gobelets à expresso empilables — 40 pièces en grès roux émaillé.',
		'description' => 'Petit format de gobelets à café courts, ils sont empilables pour se stocker aisément et aux couleurs du comptoir de ce petit coffee shop pâtissier.
Pilar D\'Amuri, pâtissière argentine et ancienne voisine de l\'atelier a installé son cosy Pilo\'s dans le 20e arrondissement, un salon de thé où les places sont prisées.
Derrière le comptoir, des cafés parigots de Mick, des thés Happy Blue Tea et les sucreries sud-américaines (medialunas, alfajor) fricotent avec les classiques français parfaitement maîtrisés.',
		'folder'      => 'pilos',
		'retours_dir' => "1-ART-DE-LA-TABLE/PILO'S",
	],
	[
		'slug'        => 'verre-a-pied',
		'title'       => 'Verre à Pied',
		'category'    => 'art-de-la-table',
		'client'      => '— (création propre)',
		'year'        => '2022',
		'location'    => '',
		'short_desc'  => 'Verres à cocktail en céramique — série limitée de 30 pièces.',
		'description' => 'Façonné dans la contrainte et la précision, ce verre à cocktail en céramique transforme le défi technique en langage esthétique.',
		'folder'      => 'verre-a-pied',
		'retours_dir' => '1-ART-DE-LA-TABLE/VERRE-A-PIED',
	],

	// ===== LUMINAIRES =====
	[
		'slug'        => 'creme-luminaires',
		'title'       => 'Crème — Luminaires',
		'category'    => 'luminaires',
		'client'      => 'Crème Restaurant',
		'year'        => '2021',
		'location'    => '18 Rue Eugène Sue, 75018 Paris',
		'short_desc'  => 'Suspensions et appliques en grès tourné — collaboration avec l\'architecte Maurine Maccagno.',
		'description' => 'Luminaires aux multiples défis techniques conçus pour l\'ouverture du restaurant Crème, en collaboration avec l\'architecte Maurine Maccagno.

Le projet s\'appuie sur un registre de formes simples — trois modules de cônes et un rectangle aux angles arrondis — assemblés pour créer des compositions de suspensions de tailles variables, générant rythme et mouvement dans l\'espace.

La terre utilisée est un grès beige, parfois teinté dans la masse en vert, offrant une texture brute et chaleureuse, en dialogue avec l\'atmosphère du lieu.',
		'folder'      => 'creme-luminaires',
		'retours_dir' => '2-LUMINAIRES/CREME',
	],
	[
		'slug'        => 'padam-hotel-artefak',
		'title'       => 'Padam Hotel / Artefak',
		'category'    => 'luminaires',
		'client'      => 'Padam Hôtel 4*',
		'year'        => '2023',
		'location'    => '9 Rue Jean Giraudoux, 75116 Paris',
		'short_desc'  => 'Pied de lampe de chevet — 2 modèles, 80 unités, grès coulé et émaillé.',
		'description' => 'En collaboration avec l\'agence Artefak, ce pied de lampe a été conçu pour les suites et chambres de l\'hôtel quatre étoiles Padam (Paris 16e). Réalisé en grès coulé dans un moule en plâtre, il présente une forme complexe et une recherche chromatique sur mesure, aboutissant à deux coloris distincts : un pour les suites, un pour les chambres.',
		'folder'      => 'padam',
		'retours_dir' => '2-LUMINAIRES/PADAM-HOTEL-ARTEFAK',
	],
	[
		'slug'        => 'sella-saint-barth',
		'title'       => 'Sella Saint Barth',
		'category'    => 'luminaires',
		'client'      => 'Sella',
		'year'        => '2022',
		'location'    => 'Plage de Public, Saint Barthélemy, 97133',
		'short_desc'  => 'Lustre cloche, suspensions et appliques murales en terra-cotta — grand format.',
		'description' => 'Suspensions et appliques murales très grand format en terra-cotta entièrement tournées à la main pour habiller de lumière le restaurant barthéloméen Sella.
Chaque luminaire est pensé comme un objet unique, il conjugue contraintes techniques et recherches esthétiques. Fabriquées sur mesure, ces pièces en terre brute révèlent le savoir-faire artisanal et l\'exigence du haut de gamme, au service d\'espaces singuliers et raffinés.

Ces luminaires en céramique sont le fruit d\'une collaboration avec le cabinet d\'architecte Cent15 Architecture.
Un projet aux multiples superlatifs, réalisé à quatre bras avec l\'aide précieuse d\'Isabelle Grangier au tournage. Il aura fallu 300 kg d\'une magnifique faïence rouge, tournée et façonnée à la main ainsi que la réalisation d\'une structure métallique capable de supporter le poids des lustres. Plusieurs mois de séchage, de cuisson et de voyage en bateau ont été nécessaires pour concrétiser ce projet.

Crédits photos : @legacyfilms_agency',
		'folder'      => 'sella',
		'retours_dir' => '2-LUMINAIRES/SELLA-StBARTH',
	],
	[
		'slug'        => 'adele',
		'title'       => 'Lampe Adèle',
		'category'    => 'luminaires',
		'client'      => '— (création propre, collaboration artisanes)',
		'year'        => '2022',
		'location'    => '',
		'short_desc'  => 'Pied de lampe à poser — grès tourné et texturé, abat-jour velours français.',
		'description' => 'Ces lampes sont le fruit d\'une collaboration entre deux artisanes dans l\'envie de travailler ensemble.
Le piètement tourné en grès roux, puis texturé, est émaillé en trois teintes exclusives, disponibles en quantités très limitées.
L\'abat-jour, quant à lui, a été cousu sur mesure par la tapissière Laurie Goutelle, elle apporte son savoir-faire textile en utilisant un velours français de la Maison Thévenon.

Une véritable rencontre entre matières, textures et savoir-faire.',
		'folder'      => 'adele',
		'retours_dir' => '2-LUMINAIRES/ADELE',
	],

	// ===== ACCESSOIRES =====
	[
		'slug'        => 'carhartt-wip',
		'title'       => 'Carhartt WIP',
		'category'    => 'accessoires',
		'client'      => 'Carhartt WIP',
		'year'        => '2021',
		'location'    => 'Toutes les boutiques françaises Carhartt',
		'short_desc'  => 'Coupelles porte-encens et supports gel — 20 unités, pièces uniques en grès.',
		'description' => 'Coupelles et supports pour gel hydroalcoolique réalisés pour les boutiques françaises de la marque Carhartt. Ces soucoupes polyvalentes peuvent également se transformer en porte-encens.
Chaque pièce est unique, à découvrir directement en magasin.
Réalisées en grès noir, roux et pyrite, avec des émaux artisanaux cuits à haute température.
Tournage et émaillage entièrement faits à la main.',
		'folder'      => 'carhartt',
		'retours_dir' => '3-ACCESSOIRES-AMBIANCE/CARHARTT',
	],
	[
		'slug'        => 'conscience-parfums',
		'title'       => 'Conscience Parfums',
		'category'    => 'accessoires',
		'client'      => 'Conscience — Parfumerie Engagée',
		'year'        => '2022',
		'location'    => '371 Rue des Pyrénées, 75020 Paris',
		'short_desc'  => 'Diffuseurs de parfum en céramique — 2 tailles, terra-cotta et grès émaillé.',
		'description' => 'Parfum et terre brute.
Diffuseurs de parfums en céramique, fruits d\'une collaboration naturelle et engagée, comme la sélection de Claire la fondatrice.

Conscience est une parfumerie alternative installée à Paris dans le quartier de Belleville et qui propose des parfums de niche et des produits parfumés plus clean, naturels et éco-responsables.

Adaptés à la diffusion d\'huiles essentielles ou d\'hydrolats, les sphères de terra-cotta absorbent le produit à cœur pour une diffusion lente et douce.
Les supports sont en grès roux émaillés donc lessivables et adaptés aussi pour la combustion d\'encens ou de bois et sauge.

Les deux modèles sont en vente dès maintenant et en exclusivité chez Conscience — Parfumerie Engagée.',
		'folder'      => 'conscience-parfums',
		'retours_dir' => '3-ACCESSOIRES-AMBIANCE/CONSCIENCE PARFUMS',
	],
];

$updated = 0;
$errors = [];

foreach ($projets as $p) {
	// Trouver le post par slug
	$posts = get_posts([
		'post_type'   => 'projet',
		'name'        => $p['slug'],
		'numberposts' => 1,
		'post_status' => 'any',
	]);

	if (empty($posts)) {
		echo "SKIP: {$p['slug']} — non trouvé en base\n";
		$errors[] = $p['slug'];
		continue;
	}

	$post_id = $posts[0]->ID;
	echo "\n=== Mise à jour : {$p['title']} (ID $post_id) ===\n";

	// 1. Titre
	wp_update_post([
		'ID'         => $post_id,
		'post_title' => $p['title'],
	]);

	// 2. Meta fields
	update_post_meta($post_id, '_projet_client', $p['client']);
	update_post_meta($post_id, '_projet_year', $p['year']);
	update_post_meta($post_id, '_projet_location', $p['location']);
	update_post_meta($post_id, '_projet_short_desc', $p['short_desc']);
	update_post_meta($post_id, '_projet_description', $p['description']);
	echo "  Meta mis à jour\n";

	// 3. Catégorie
	$term = get_term_by('slug', $p['category'], 'categorie_projet');
	if ($term) {
		wp_set_post_terms($post_id, [$term->term_id], 'categorie_projet');
		echo "  Catégorie : {$term->name}\n";
	}

	// 4. Cover : copier 0.* depuis RETOURS
	$retours_path = $retours_base . $p['retours_dir'] . '/';
	$dest_path = $img_base . $p['folder'] . '/';

	if (is_dir($retours_path)) {
		$covers = glob($retours_path . '0.*');
		foreach ($covers as $cover_src) {
			$ext = pathinfo($cover_src, PATHINFO_EXTENSION);
			$cover_dest = $dest_path . '0.' . $ext;
			if (!file_exists($cover_dest)) {
				copy($cover_src, $cover_dest);
				echo "  Cover copiée : 0.$ext\n";
			} else {
				echo "  Cover existe déjà : 0.$ext\n";
			}
		}
	} else {
		echo "  WARN: Dossier RETOURS non trouvé : {$p['retours_dir']}\n";
	}

	// 5. Galerie : reconstruire avec cover en premier
	$gallery = get_post_meta($post_id, '_projet_gallery', true);
	if (!is_array($gallery)) $gallery = [];

	// Scanner le dossier pour trouver les covers 0.*
	$cover_files = glob($dest_path . '0.*');
	$cover_names = [];
	foreach ($cover_files as $cf) {
		$name = $p['folder'] . '/' . basename($cf);
		$cover_names[] = $name;
	}

	// Retirer les anciens 0.* de la galerie
	$gallery = array_filter($gallery, function ($item) {
		return !preg_match('/\/0\.\w+$/', $item);
	});

	// Préfixer avec les covers (webp prioritaire)
	usort($cover_names, function ($a, $b) {
		$a_webp = str_ends_with($a, '.webp') ? 0 : 1;
		$b_webp = str_ends_with($b, '.webp') ? 0 : 1;
		return $a_webp - $b_webp;
	});

	// Convertir cover en webp si pas déjà fait
	$has_webp_cover = false;
	foreach ($cover_names as $cn) {
		if (str_ends_with($cn, '.webp')) $has_webp_cover = true;
	}

	// On prend la première cover disponible (webp si existe, sinon jpg/jpeg)
	$final_cover = !empty($cover_names) ? $cover_names[0] : null;

	if ($final_cover) {
		// Mettre la cover en premier, puis le reste
		$new_gallery = array_merge([$final_cover], array_values($gallery));
		// Dédoublonner
		$new_gallery = array_unique($new_gallery);
		$new_gallery = array_values($new_gallery);
	} else {
		$new_gallery = array_values($gallery);
	}

	update_post_meta($post_id, '_projet_gallery', $new_gallery);
	echo "  Galerie : " . count($new_gallery) . " images (cover: " . ($final_cover ?: 'aucune') . ")\n";

	$updated++;
}

echo "\n========================================\n";
echo "Terminé : $updated projets mis à jour.\n";
if (!empty($errors)) {
	echo "Non trouvés : " . implode(', ', $errors) . "\n";
}
