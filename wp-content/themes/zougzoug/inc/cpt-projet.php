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

/**
 * Metabox custom pour les champs projet
 */
add_action('add_meta_boxes', function () {
	add_meta_box(
		'zz_projet_details',
		'Details du projet',
		'zz_projet_metabox_render',
		'projet',
		'normal',
		'high'
	);
});

function zz_projet_metabox_render($post) {
	wp_nonce_field('zz_projet_save', 'zz_projet_nonce');

	$fields = [
		'_projet_client'     => ['label' => 'Client', 'type' => 'text'],
		'_projet_year'       => ['label' => 'Annee', 'type' => 'text'],
		'_projet_location'   => ['label' => 'Lieu / Adresse', 'type' => 'text'],
		'_projet_short_desc' => ['label' => 'Description courte (carte)', 'type' => 'text'],
		'_projet_description' => ['label' => 'Description longue', 'type' => 'textarea'],
	];

	echo '<table class="form-table"><tbody>';
	foreach ($fields as $key => $field) {
		$value = get_post_meta($post->ID, $key, true);
		echo '<tr>';
		echo '<th><label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label></th>';
		if ($field['type'] === 'textarea') {
			echo '<td><textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="4" class="large-text">' . esc_textarea($value) . '</textarea></td>';
		} else {
			echo '<td><input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text"></td>';
		}
		echo '</tr>';
	}

	// Gallery
	$gallery = get_post_meta($post->ID, '_projet_gallery', true);
	$gallery_str = is_array($gallery) ? implode("\n", $gallery) : '';
	echo '<tr>';
	echo '<th><label for="_projet_gallery">Galerie (chemins images)</label></th>';
	echo '<td><textarea id="_projet_gallery" name="_projet_gallery" rows="6" class="large-text">' . esc_textarea($gallery_str) . '</textarea>';
	echo '<p class="description">Un chemin par ligne (ex: becquetance/DSCF3613.webp). La premiere image sert de cover.</p></td>';
	echo '</tr>';

	echo '</tbody></table>';
}

/**
 * Sauvegarder les meta fields projet
 */
add_action('save_post_projet', function ($post_id) {
	if (!isset($_POST['zz_projet_nonce']) || !wp_verify_nonce($_POST['zz_projet_nonce'], 'zz_projet_save')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	$text_fields = ['_projet_client', '_projet_year', '_projet_location', '_projet_short_desc'];
	foreach ($text_fields as $key) {
		if (isset($_POST[$key])) {
			update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
		}
	}

	if (isset($_POST['_projet_description'])) {
		update_post_meta($post_id, '_projet_description', sanitize_textarea_field($_POST['_projet_description']));
	}

	if (isset($_POST['_projet_gallery'])) {
		$lines = explode("\n", $_POST['_projet_gallery']);
		$gallery = array_values(array_filter(array_map('trim', $lines)));
		update_post_meta($post_id, '_projet_gallery', $gallery);
	}
});

/**
 * Preparer les donnees projets pour le front (wp_localize_script)
 */
function zz_get_projets_data() {
	$uri = get_template_directory_uri();

	// Category slug → filter value mapping
	$cat_filter_map = [
		'art-de-la-table' => 'table',
		'luminaires'      => 'luminaires',
		'accessoires'     => 'accessoires',
	];

	$query = new WP_Query([
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	]);

	$projets = [];

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$id = get_the_ID();

			// Category
			$terms = wp_get_post_terms($id, 'categorie_projet');
			$cat_slug = !empty($terms) ? $terms[0]->slug : '';
			$cat_label = !empty($terms) ? $terms[0]->name : '';
			$cat_filter = isset($cat_filter_map[$cat_slug]) ? $cat_filter_map[$cat_slug] : $cat_slug;

			// Gallery
			$gallery = get_post_meta($id, '_projet_gallery', true);
			if (!is_array($gallery)) $gallery = [];

			// Cover = first gallery image
			$cover = !empty($gallery) ? $gallery[0] : '';

			// Details
			$details = [
				'client'   => get_post_meta($id, '_projet_client', true),
				'year'     => get_post_meta($id, '_projet_year', true),
				'location' => get_post_meta($id, '_projet_location', true),
				'texte'    => get_post_meta($id, '_projet_description', true),
			];

			$projets[] = [
				'id'       => $id,
				'name'     => get_the_title(),
				'category' => $cat_filter,
				'catLabel' => $cat_label,
				'desc'     => get_post_meta($id, '_projet_short_desc', true),
				'cover'    => $cover,
				'medias'   => $gallery,
				'details'  => $details,
			];
		}
		wp_reset_postdata();
	}

	return [
		'imgBase' => $uri . '/assets/img/',
		'projets' => $projets,
	];
}
