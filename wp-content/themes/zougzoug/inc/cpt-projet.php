<?php
/**
 * Custom Post Type — Projet + Taxonomy + Meta
 */

add_action('init', function () {
	// CPT Projet
	register_post_type('projet', [
		'labels' => [
			'name'               => 'Projets',
			'singular_name'      => 'Projet',
			'add_new'            => 'Ajouter un projet',
			'add_new_item'       => 'Ajouter un nouveau projet',
			'edit_item'          => 'Modifier le projet',
			'view_item'          => 'Voir le projet',
			'all_items'          => 'Tous les projets',
			'search_items'       => 'Rechercher un projet',
			'not_found'          => 'Aucun projet trouve',
			'not_found_in_trash' => 'Aucun projet dans la corbeille',
		],
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => ['title', 'thumbnail'],
		'rewrite'      => ['slug' => 'collaborations'],
		'show_in_rest' => true,
	]);

	// Taxonomy categorie_projet
	register_taxonomy('categorie_projet', 'projet', [
		'labels' => [
			'name'          => 'Categories de projet',
			'singular_name' => 'Categorie de projet',
			'add_new_item'  => 'Ajouter une categorie',
			'edit_item'     => 'Modifier la categorie',
			'all_items'     => 'Toutes les categories',
			'search_items'  => 'Rechercher une categorie',
		],
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => ['slug' => 'categorie-projet'],
		'show_in_rest' => true,
	]);

	// Meta fields
	$meta_fields = [
		'_projet_client'     => 'string',
		'_projet_year'       => 'string',
		'_projet_location'   => 'string',
		'_projet_description' => 'string',
		'_projet_short_desc' => 'string',
	];

	foreach ($meta_fields as $key => $type) {
		register_post_meta('projet', $key, [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => $type,
		]);
	}

	register_post_meta('projet', '_projet_gallery', [
		'show_in_rest' => [
			'schema' => [
				'type'  => 'array',
				'items' => ['type' => 'integer'],
			],
		],
		'single' => true,
		'type'   => 'array',
	]);
});

// Creer les termes par defaut
add_action('init', function () {
	$terms = [
		'art-de-la-table' => 'Art de la table',
		'luminaires'      => 'Luminaires',
		'accessoires'     => 'Accessoires',
	];
	foreach ($terms as $slug => $name) {
		if (!term_exists($slug, 'categorie_projet')) {
			wp_insert_term($name, 'categorie_projet', ['slug' => $slug]);
		}
	}
}, 20);
