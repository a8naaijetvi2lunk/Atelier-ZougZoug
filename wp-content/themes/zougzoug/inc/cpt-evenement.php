<?php
/**
 * Custom Post Type — Evenement + Taxonomy + Meta
 */

add_action('init', function () {
	// CPT Evenement
	register_post_type('evenement', [
		'labels' => [
			'name'               => 'Agenda',
			'singular_name'      => 'Evenement',
			'add_new'            => 'Ajouter un evenement',
			'add_new_item'       => 'Ajouter un nouvel evenement',
			'edit_item'          => 'Modifier l\'evenement',
			'view_item'          => 'Voir l\'evenement',
			'all_items'          => 'Tous les evenements',
			'search_items'       => 'Rechercher un evenement',
			'not_found'          => 'Aucun evenement trouve',
			'not_found_in_trash' => 'Aucun evenement dans la corbeille',
		],
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => ['title'],
		'rewrite'      => ['slug' => 'agenda'],
		'show_in_rest' => true,
	]);

	// Taxonomy type_evenement
	register_taxonomy('type_evenement', 'evenement', [
		'labels' => [
			'name'          => 'Types d\'evenement',
			'singular_name' => 'Type d\'evenement',
			'add_new_item'  => 'Ajouter un type',
			'edit_item'     => 'Modifier le type',
			'all_items'     => 'Tous les types',
			'search_items'  => 'Rechercher un type',
		],
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => ['slug' => 'type-evenement'],
		'show_in_rest' => true,
	]);

	// Meta fields
	$meta_fields = [
		'_event_date_start' => 'string',
		'_event_date_end'   => 'string',
		'_event_lieu'       => 'string',
		'_event_ville'      => 'string',
		'_event_url'        => 'string',
		'_event_url_label'  => 'string',
		'_event_passed'     => 'boolean',
	];

	foreach ($meta_fields as $key => $type) {
		register_post_meta('evenement', $key, [
			'show_in_rest' => true,
			'single'       => true,
			'type'         => $type,
		]);
	}
});

// Creer les termes par defaut
add_action('init', function () {
	$terms = [
		'marche'     => 'Marche',
		'salon'      => 'Salon',
		'exposition' => 'Exposition',
	];
	foreach ($terms as $slug => $name) {
		if (!term_exists($slug, 'type_evenement')) {
			wp_insert_term($name, 'type_evenement', ['slug' => $slug]);
		}
	}
}, 20);

/**
 * Metabox custom pour les champs evenement
 */
add_action('add_meta_boxes', function () {
	add_meta_box(
		'zz_event_details',
		'Details de l\'evenement',
		'zz_event_metabox_render',
		'evenement',
		'normal',
		'high'
	);
});

function zz_event_metabox_render($post) {
	wp_nonce_field('zz_event_save', 'zz_event_nonce');

	$fields = [
		'_event_date_start' => ['label' => 'Date de debut', 'type' => 'date'],
		'_event_date_end'   => ['label' => 'Date de fin (optionnel)', 'type' => 'date'],
		'_event_lieu'       => ['label' => 'Nom du lieu', 'type' => 'text'],
		'_event_ville'      => ['label' => 'Ville + departement', 'type' => 'text'],
		'_event_url'        => ['label' => 'Lien externe (URL)', 'type' => 'url'],
		'_event_url_label'  => ['label' => 'Texte du lien', 'type' => 'text'],
	];

	echo '<table class="form-table"><tbody>';
	foreach ($fields as $key => $field) {
		$value = get_post_meta($post->ID, $key, true);
		echo '<tr>';
		echo '<th><label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label></th>';
		echo '<td><input type="' . esc_attr($field['type']) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text"></td>';
		echo '</tr>';
	}

	// Checkbox "Passe"
	$passed = get_post_meta($post->ID, '_event_passed', true);
	echo '<tr>';
	echo '<th><label for="_event_passed">Evenement passe</label></th>';
	echo '<td><label><input type="checkbox" id="_event_passed" name="_event_passed" value="1"' . checked($passed, '1', false) . '> Forcer comme passe</label>';
	echo '<p class="description">Si non coche, le statut est calcule automatiquement selon la date.</p></td>';
	echo '</tr>';

	echo '</tbody></table>';
}

/**
 * Sauvegarder les meta fields
 */
add_action('save_post_evenement', function ($post_id) {
	if (!isset($_POST['zz_event_nonce']) || !wp_verify_nonce($_POST['zz_event_nonce'], 'zz_event_save')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	$text_fields = ['_event_date_start', '_event_date_end', '_event_lieu', '_event_ville', '_event_url_label'];
	foreach ($text_fields as $key) {
		if (isset($_POST[$key])) {
			update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
		}
	}

	if (isset($_POST['_event_url'])) {
		update_post_meta($post_id, '_event_url', esc_url_raw($_POST['_event_url']));
	}

	$passed = isset($_POST['_event_passed']) ? '1' : '';
	update_post_meta($post_id, '_event_passed', $passed);
});

/**
 * Colonnes personnalisees dans la liste admin
 */
add_filter('manage_evenement_posts_columns', function ($columns) {
	$new = [];
	$new['cb'] = $columns['cb'];
	$new['title'] = $columns['title'];
	$new['event_date'] = 'Date';
	$new['event_lieu'] = 'Lieu';
	$new['event_ville'] = 'Ville';
	$new['event_status'] = 'Statut';
	$new['taxonomy-type_evenement'] = 'Type';
	return $new;
});

add_action('manage_evenement_posts_custom_column', function ($column, $post_id) {
	switch ($column) {
		case 'event_date':
			$start = get_post_meta($post_id, '_event_date_start', true);
			$end = get_post_meta($post_id, '_event_date_end', true);
			if ($start) {
				echo esc_html(date_i18n('j M Y', strtotime($start)));
				if ($end) {
					echo ' — ' . esc_html(date_i18n('j M Y', strtotime($end)));
				}
			} else {
				echo '—';
			}
			break;
		case 'event_lieu':
			echo esc_html(get_post_meta($post_id, '_event_lieu', true) ?: '—');
			break;
		case 'event_ville':
			echo esc_html(get_post_meta($post_id, '_event_ville', true) ?: '—');
			break;
		case 'event_status':
			$passed = get_post_meta($post_id, '_event_passed', true);
			$start = get_post_meta($post_id, '_event_date_start', true);
			$is_past = $passed || ($start && strtotime($start) < time());
			echo $is_past ? '<span style="color:#999;">Passe</span>' : '<span style="color:#B8956A;font-weight:600;">A venir</span>';
			break;
	}
}, 10, 2);

add_filter('manage_edit-evenement_sortable_columns', function ($columns) {
	$columns['event_date'] = '_event_date_start';
	return $columns;
});

add_action('pre_get_posts', function ($query) {
	if (!is_admin() || !$query->is_main_query()) return;
	if ($query->get('post_type') !== 'evenement') return;
	if ($query->get('orderby') === '_event_date_start') {
		$query->set('meta_key', '_event_date_start');
		$query->set('orderby', 'meta_value');
	}
});
