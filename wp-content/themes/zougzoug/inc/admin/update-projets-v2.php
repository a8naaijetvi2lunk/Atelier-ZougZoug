<?php
/**
 * Mise à jour v2 : ajouter nature, matériaux, instagram, website, collaborateur
 * Usage : wp eval-file wp-content/themes/zougzoug/inc/admin/update-projets-v2.php
 */

if (!defined('ABSPATH')) {
	require_once dirname(__FILE__) . '/../../../../../wp-load.php';
}

$projets = [

	// ===== ART DE LA TABLE =====

	[
		'slug'          => 'becquetance',
		'nature'        => "Set d'assiettes, bols et verseuses : 150 pièces de vaisselle de service, 8 modèles différents.\nPots à couverts : Trio de pots à couverts.",
		'materiaux'     => "Grès noir et blanc chamottés, grès pyrité, émaux artisanaux de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '@becquetance_paris',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Becquetance',
		'location'      => '67 Rue de Ménilmontant, 75020 Paris',
		'year'          => '2021',
		'description'   => "Création sur mesure des formes et des couleurs d'un service complet d'assiettes, bols et petites verseuses pour l'ouverture d'un néo-bistrot dédié aux vins natures.\nHuit formats conçus et prototypés en collaboration avec la cheffe et son associé.\nTerres et émaux développés spécifiquement pour s'intégrer à l'identité visuelle et architecturale du lieu.",
	],
	[
		'slug'          => 'benoit-castel',
		'nature'        => "Set d'assiettes : 30 pièces d'assiettes à brunch.",
		'materiaux'     => "Grès blanc chamotté, émail satiné de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '@benoitcastel',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Benoît Castel Sorbier',
		'location'      => '11 rue Sorbier, 75020 Paris',
		'year'          => '2022',
		'description'   => "Modèle d'assiette co-signée avec le logo/emporte pièce emblématique de ces pâtisseries-boulangeries. À retrouver à la table de la toute dernière adresse de Benoît Castel dans laquelle il dévoile sa version très personnelle du Coffee Shop à la française. Classée en 1914, ce coffee shop remet au goût du jour le petit-déjeuner avec des pancakes salés ou sucrés servis tout au long de la journée et redonne envie de prendre son temps et de savourer pleinement un petit-déjeuner gourmand et équilibré ou tout autre instant plaisir en journée, en intérieur ou en extérieur, selon la saison, sous les arbres, au soleil...",
	],
	[
		'slug'          => 'creme-table',
		'nature'        => "Set d'assiettes et bols : 90 pièces de vaisselle de service, 3 modèles différents.",
		'materiaux'     => "Grès noir et porcelaine, émaux artisanaux de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '@creme_restaurant',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Crème Restaurant',
		'location'      => '18 Rue Eugène Sue, 75018 Paris',
		'year'          => '2022',
		'description'   => "Après la fabrication de tous les luminaires de ce restaurant, Maxime et Camille ont une nouvelle fois accordé leur confiance à l'atelier pour compléter leur gamme de vaisselle de service.\nNous avons co-créé une nouvelle série de bols, petites et grandes assiettes. Des lignes sobres, du noir et blanc et une alternance de terre brute et d'émail satiné. L'émail blanc des grandes assiettes, a décidé de s'exprimer dans des variations spectaculaires et incontrôlées de la voie lactée... Passée la première surprise, ce résultat inattendu nous a conquis.",
	],
	[
		'slug'          => 'maison-fragile',
		'nature'        => "Set de coquetiers en série limitée",
		'materiaux'     => "Grès blanc et roux, émaux artisanaux de haute température.\nTournage et déformations. Émaillage à la main.",
		'instagram'     => '@maisonfragileparis',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Maison Fragile',
		'location'      => '4 Rue de Jarente, 75004 Paris',
		'year'          => '2022',
		'description'   => "Marie Castel la fondatrice de Maison Fragile fait de la fragilité une force. Quoi de plus fragile et tout à la fois incroyablement fort qu'un œuf?\nCoquetiers en série ultra limitée inspirés des « bulles rochers » de la boutique dessinées par le @studio_ggsv",
	],
	[
		'slug'          => 'petite-marmelade',
		'nature'        => "Set de plats à cake, babka et présentoirs à tartelettes",
		'materiaux'     => "Grès blanc et roux, émaux artisanaux de haute température.\nTournage et découpes. Émaillage à la main.",
		'instagram'     => '@petite_marmelade',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Petite Marmelade',
		'location'      => 'Yvelines',
		'year'          => '2022',
		'description'   => "Set de plats à cake, brioche et présentoirs à tartelettes pour ce food truck itinérant de pâtisseries artisanales, réalisées avec des produits de saison.",
	],
	[
		'slug'          => 'pilos',
		'nature'        => "Gobelets à expresso empilables. 40 pièces",
		'materiaux'     => "Grès roux, émail artisan de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '@pilos_patisserie',
		'website'       => '',
		'collaborateur' => '',
		'client'        => "Pilo's",
		'location'      => "1 Av. du Père Lachaise, 75020 Paris",
		'year'          => '2022',
		'description'   => "Petit format de gobelets à café courts, ils sont empilables pour se stocker aisément et au couleur du comptoir de ce petit coffee shop pâtissier.\nPilar D'Amuri, pâtissière argentine et ancienne voisine de l'atelier a installé son cosy Pilo's dans le 20e arrondissement, un salon de thé où les places sont prisées.\nDerrière le comptoir, des cafés parigots de Mick, des thés Happy Blue Tea et les sucreries sud-américaines (medialunas, alfajor) fricotent avec les classiques français parfaitement maîtrisés.",
	],
	[
		'slug'          => 'verre-a-pied',
		'nature'        => "Verres à cocktail, série limitées de 30 pièces.",
		'materiaux'     => "Grès roux, émail artisan de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '',
		'website'       => '',
		'collaborateur' => '',
		'client'        => '',
		'location'      => '',
		'year'          => '2022',
		'description'   => "Façonné dans la contrainte et la précision, ce verre à cocktail en céramique transforme le défi technique en langage esthétique.",
	],

	// ===== LUMINAIRES =====

	[
		'slug'          => 'creme-luminaires',
		'nature'        => "Suspensions et appliques en grès tourné",
		'materiaux'     => "Grès blanc chamotté et vert teinté dans la masse.\nTournage et façonnage à la main. Électrification et matériel de suspension.",
		'instagram'     => '@creme_restaurant',
		'website'       => '',
		'collaborateur' => "Architecte Maurine Maccagno (https://www.maurinemaccagno.fr/)",
		'client'        => 'Crème Restaurant',
		'location'      => '18 Rue Eugène Sue, 75018 Paris',
		'year'          => '2021',
		'description'   => "Luminaires aux multiples défis techniques conçus pour l'ouverture du restaurant Crème, en collaboration avec l'architecte Maurine Maccagno.\n\nLe projet s'appuie sur un registre de formes simples — trois modules de cônes et un rectangle aux angles arrondis — assemblés pour créer des compositions de suspensions de tailles variables, générant rythme et mouvement dans l'espace.\n\nLa terre utilisée est un grès beige, parfois teinté dans la masse en vert, offrant une texture brute et chaleureuse, en dialogue avec l'atmosphère du lieu.",
	],
	[
		'slug'          => 'padam-hotel-artefak',
		'nature'        => "Pied de lampe de chevet. 2 modèles, 80 unités.",
		'materiaux'     => "Grès blanc, émaux artisanaux de haute température.\nCoulage et émaillage à la main, électrification.",
		'instagram'     => '@padamhotel',
		'website'       => 'https://www.padam-hotel.com/',
		'collaborateur' => "Agence Artefak\nTissu : @lamaisonpierrefrey\nAbat-jour : @heriosfrance",
		'client'        => 'Padam hôtel 4*',
		'location'      => '9 Rue Jean Giraudoux, 75116 Paris',
		'year'          => '2023',
		'description'   => "En collaboration avec l'agence Artefak, ce pied de lampe a été conçu pour les suites et chambres de l'hôtel quatre étoiles Padam (Paris 16ᵉ). Réalisé en grès coulé dans un moule en plâtre, il présente une forme complexe et une recherche chromatique sur mesure, aboutissant à deux coloris distincts : un pour les suites, un pour les chambres.",
	],
	[
		'slug'          => 'sella-st-barth',
		'nature'        => "Lustre cloche grand format, 5 pièces.\nComposition de suspensions 15 modèles uniques.\nAppliques murales, 6 pièces.",
		'materiaux'     => "Faïence rouge chamottée.\nTournage et façonnage à la main.\nRésolutions techniques : électrification, suspensions avec armatures et filins métalliques.",
		'instagram'     => '@sellastbarth',
		'website'       => 'https://sellasaintbarth.com/fr/',
		'collaborateur' => "Cabinet d'architecte Cent15 Architecture (https://cent15architecture.com/fr)\nIsabelle Grangier au tournage\nCrédits photos : @legacyfilms_agency",
		'client'        => 'Sella',
		'location'      => 'Plage de Public, Saint-Barthélemy, 97133',
		'year'          => '2022',
		'description'   => "Suspensions et appliques murales très grand format en terra-cotta entièrement tournées à la main pour habiller de lumière le restaurant barthéloméen Sella.\nChaque luminaire est pensé comme un objet unique, il conjugue contraintes techniques et recherches esthétiques. Fabriquée sur mesure, ces pièces en terre brute révèlent le savoir-faire artisanal et l'exigence du haut de gamme, au service d'espaces singuliers et raffinés.\n\nCes luminaires en céramique sont le fruit d'une collaboration avec le cabinet d'architecte Cent15 Architecture.\nUn projet aux multiples superlatifs, réalisé à quatre bras avec l'aide précieuse d'Isabelle Grangier au tournage avec moi. Il aura fallu 300kg d'une magnifique faïence rouge, tournée et façonnée à la main ainsi que la réalisation d'une structure métallique capable de supporter le poids des lustres. Plusieurs mois de séchage, de cuisson et de voyage en bateau ont été nécessaire pour concrétiser ce projet.",
	],
	[
		'slug'          => 'adele',
		'nature'        => "Pied de lampe à poser.",
		'materiaux'     => "Grès blanc, roux et pyrité, émaux artisanaux de haute température.\nTournage et émaillage à la main, électrification.",
		'instagram'     => '',
		'website'       => '',
		'collaborateur' => "Fabrication de l'abat-jour : Laurie Goutelle @tapissiere_en_beaujolais\nTissu : Maison Thevenon @maisonthevenon",
		'client'        => '',
		'location'      => '',
		'year'          => '2022',
		'description'   => "Ces lampes sont le fruit d'une collaboration entre deux artisanes dans l'envie de travailler ensemble.\nLe piètement tourné en grès roux, puis texturé, est émaillé en trois teintes exclusives, disponibles en quantités très limitées.\nL'abat-jour, quant à lui, a été cousu sur mesure par la tapissière Laurie Goutelle, elle apporte son savoir-faire textile en utilisant un velours français de la Maison Thevenon.\n\nUne véritable rencontre entre matières, textures et savoir-faire.",
	],

	// ===== ACCESSOIRES =====

	[
		'slug'          => 'carhartt-wip',
		'nature'        => "Coupelle porte encens et support gel hydroalcoolique, 20 unités.",
		'materiaux'     => "Grès noir, roux et pyrité, émaux artisanaux de haute température.\nTournage et émaillage à la main.",
		'instagram'     => '@carharttwip_fr',
		'website'       => '',
		'collaborateur' => '',
		'client'        => 'Carhartt WIP',
		'location'      => 'Toutes les boutiques françaises Carhartt',
		'year'          => '2021',
		'description'   => "Coupelles et supports pour gel hydroalcoolique réalisés pour les boutiques françaises de la marque Carhartt. Ces soucoupes polyvalentes peuvent également se transformer en porte-encens.\nChaque pièce est unique, à découvrir directement en magasin.\nRéalisées en grès noir, roux et pyrité, avec des émaux artisanaux cuits à haute température.\nTournage et émaillage entièrement faits à la main.",
	],
	[
		'slug'          => 'conscience-parfums',
		'nature'        => "Diffuseurs de parfum, 2 tailles.",
		'materiaux'     => "Grès roux et faïence rouge, émaux artisanaux de haute température.\nTournage, modelage et émaillage à la main.",
		'instagram'     => '@conscience.parfums',
		'website'       => 'https://www.conscience-parfums.fr/',
		'collaborateur' => '',
		'client'        => 'Conscience - Parfumerie Engagée',
		'location'      => '371 Rue des Pyrénées, 75020 Paris',
		'year'          => '2022',
		'description'   => "Parfum et terre brute.\nDiffuseurs de parfums en céramique, fruits d'une collaboration naturelle et engagée, comme la sélection de Claire la fondatrice.\n\nConscience est une parfumerie alternative installée à Paris dans le quartier de Belleville et qui propose des parfums de niche et des produits parfumés plus clean, naturels et éco-responsables.\n\nAdaptés à la diffusion d'huiles essentielles ou d'hydrolats, les sphères de terra-cotta absorbent le produit à cœur pour une diffusion lente et douce.\nLes supports sont en grès roux émaillés donc lessivables et adaptés aussi pour la combustion d'encens ou de bois et sauge.\n\nLes deux modèles sont en vente dès maintenant et en exclusivité chez Conscience - Parfumerie Engagée.",
	],
];

$updated = 0;
$errors  = [];

foreach ($projets as $p) {
	$posts = get_posts([
		'post_type'   => 'projet',
		'name'        => $p['slug'],
		'numberposts' => 1,
	]);
	if (empty($posts)) {
		$errors[] = $p['slug'];
		echo "✗ {$p['slug']} : NON TROUVÉ\n";
		continue;
	}

	$post_id = $posts[0]->ID;

	// Mettre à jour tous les champs
	update_post_meta($post_id, '_projet_nature', $p['nature']);
	update_post_meta($post_id, '_projet_materiaux', $p['materiaux']);
	update_post_meta($post_id, '_projet_instagram', $p['instagram']);
	update_post_meta($post_id, '_projet_website', $p['website']);
	update_post_meta($post_id, '_projet_collaborateur', $p['collaborateur']);

	// Aussi mettre à jour client, location, year, description avec les données Charlotte
	update_post_meta($post_id, '_projet_client', $p['client']);
	update_post_meta($post_id, '_projet_location', $p['location']);
	update_post_meta($post_id, '_projet_year', $p['year']);
	update_post_meta($post_id, '_projet_description', $p['description']);

	echo "✓ {$p['slug']} : mis à jour (nature, matériaux, instagram, website, collaborateur, description)\n";
	$updated++;
}

echo "\n========================================\n";
echo "Terminé : $updated projets mis à jour.\n";
if (!empty($errors)) {
	echo "Non trouvés : " . implode(', ', $errors) . "\n";
}
