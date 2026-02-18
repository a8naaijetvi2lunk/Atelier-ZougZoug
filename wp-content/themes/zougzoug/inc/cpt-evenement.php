<?php
/**
 * Custom Post Type — Evenement + Taxonomy + Meta
 */

add_action('init', function () {
	// CPT Evenement
	register_post_type('evenement', [
		'labels' => [
			'name'               => 'Agenda',
			'singular_name'      => 'Événement',
			'add_new'            => 'Ajouter un événement',
			'add_new_item'       => 'Ajouter un nouvel événement',
			'edit_item'          => 'Modifier l\'événement',
			'view_item'          => 'Voir l\'événement',
			'all_items'          => 'Tous les événements',
			'search_items'       => 'Rechercher un événement',
			'not_found'          => 'Aucun événement trouvé',
			'not_found_in_trash' => 'Aucun événement dans la corbeille',
		],
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => ['title', 'page-attributes'],
		'rewrite'      => ['slug' => 'agenda'],
		'show_in_rest' => true,
	]);

	// Taxonomy type_evenement
	register_taxonomy('type_evenement', 'evenement', [
		'labels' => [
			'name'          => 'Types d\'événement',
			'singular_name' => 'Type d\'événement',
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

// Créer les termes par défaut
add_action('init', function () {
	$terms = [
		'marche'     => 'Marché',
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
		'Détails de l\'événement',
		'zz_event_metabox_render',
		'evenement',
		'normal',
		'high'
	);
});

function zz_event_metabox_render($post) {
	wp_nonce_field('zz_event_save', 'zz_event_nonce');

	$fields = [
		'_event_date_start' => ['label' => 'Date de début', 'type' => 'date'],
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
	echo '<th><label for="_event_passed">Événement passé</label></th>';
	echo '<td><label><input type="checkbox" id="_event_passed" name="_event_passed" value="1"' . checked($passed, '1', false) . '> Forcer comme passé</label>';
	echo '<p class="description">Si non coché, le statut est calculé automatiquement selon la date.</p></td>';
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
	$new['event_type'] = 'Type';
	$new['event_date'] = 'Date';
	$new['event_lieu'] = 'Lieu';
	$new['event_status'] = 'Statut';
	return $new;
});

add_action('manage_evenement_posts_custom_column', function ($column, $post_id) {
	switch ($column) {
		case 'event_type':
			$terms = wp_get_post_terms($post_id, 'type_evenement');
			if (!empty($terms)) {
				$slug = $terms[0]->slug;
				$name = $terms[0]->name;
				echo '<span class="zz-type-badge zz-type-badge--' . esc_attr($slug) . '">' . esc_html($name) . '</span>';
			} else {
				echo '—';
			}
			break;
		case 'event_date':
			$start = get_post_meta($post_id, '_event_date_start', true);
			$end = get_post_meta($post_id, '_event_date_end', true);
			if ($start) {
				echo '<strong>' . esc_html(date_i18n('j M Y', strtotime($start))) . '</strong>';
				if ($end) {
					echo '<span style="color:#a0a5aa;"> — ' . esc_html(date_i18n('j M Y', strtotime($end))) . '</span>';
				}
			} else {
				echo '<span style="color:#ccc;">—</span>';
			}
			break;
		case 'event_lieu':
			$lieu = get_post_meta($post_id, '_event_lieu', true);
			$ville = get_post_meta($post_id, '_event_ville', true);
			if ($lieu || $ville) {
				if ($lieu) echo esc_html($lieu);
				if ($lieu && $ville) echo '<br>';
				if ($ville) echo '<span style="color:#a0a5aa;font-size:12px;">' . esc_html($ville) . '</span>';
			} else {
				echo '<span style="color:#ccc;">—</span>';
			}
			break;
		case 'event_status':
			$passed = get_post_meta($post_id, '_event_passed', true);
			$start = get_post_meta($post_id, '_event_date_start', true);
			$is_past = $passed || ($start && strtotime($start) < time());
			echo $is_past
				? '<span class="zz-event-badge zz-event-badge--past">Passé</span>'
				: '<span class="zz-event-badge zz-event-badge--upcoming">À venir</span>';
			break;
	}
}, 10, 2);

add_filter('manage_edit-evenement_sortable_columns', function ($columns) {
	$columns['event_date'] = '_event_date_start';
	return $columns;
});

/**
 * Tri par défaut : date descendante (plus récents en premier)
 */
add_action('pre_get_posts', function ($query) {
	if (!is_admin() || !$query->is_main_query()) return;
	if ($query->get('post_type') !== 'evenement') return;
	$orderby = $query->get('orderby');
	if ($orderby === '_event_date_start') {
		$query->set('meta_key', '_event_date_start');
		$query->set('orderby', 'meta_value');
	} elseif (empty($orderby) || $orderby === 'menu_order title') {
		$query->set('meta_key', '_event_date_start');
		$query->set('orderby', 'meta_value');
		$query->set('order', 'DESC');
	}
});

/**
 * Admin : styles pour la liste des événements
 */
add_action('admin_head', function () {
	$screen = get_current_screen();
	if (!$screen || $screen->id !== 'edit-evenement') return;
	?>
	<style>
		/* ===== Page wrapper ===== */
		.post-type-evenement .wrap {
			max-width: 1400px;
			margin: 0 auto;
			padding: 20px 20px 40px;
		}

		/* ===== Hero — titre + bouton ===== */
		.post-type-evenement .wrap > h1.wp-heading-inline {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 600;
			font-size: 26px;
			color: #1A1A1A;
			letter-spacing: -0.01em;
		}
		.post-type-evenement .wrap > .page-title-action {
			background: #1A1A1A;
			color: #fff;
			border: none;
			border-radius: 6px;
			padding: 6px 16px;
			font-size: 13px;
			font-weight: 500;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			text-decoration: none;
			transition: background 0.15s;
		}
		.post-type-evenement .wrap > .page-title-action:hover {
			background: #333;
			color: #fff;
		}

		/* ===== Sous-sous links ===== */
		.post-type-evenement .subsubsub {
			font-size: 13px;
			margin: 8px 0 0;
		}
		.post-type-evenement .subsubsub a {
			color: #646970;
			text-decoration: none;
			font-weight: 400;
		}
		.post-type-evenement .subsubsub a:hover,
		.post-type-evenement .subsubsub .current a {
			color: #1A1A1A;
			font-weight: 500;
		}
		.post-type-evenement .subsubsub .count {
			color: #a0a5aa;
		}

		/* ===== Toolbar filtres ===== */
		.post-type-evenement .tablenav.top {
			display: flex;
			align-items: center;
			gap: 8px;
			padding: 12px 0;
			border-bottom: 1px solid rgba(26,26,26,0.08);
			margin-bottom: 0;
		}
		.post-type-evenement .tablenav .actions {
			display: flex;
			align-items: center;
			gap: 6px;
			padding: 0;
		}
		.post-type-evenement .tablenav .actions select {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 8px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #1A1A1A;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.post-type-evenement .tablenav .actions .button {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 12px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #646970;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			cursor: pointer;
			transition: border-color 0.15s, color 0.15s;
		}
		.post-type-evenement .tablenav .actions .button:hover {
			border-color: #1A1A1A;
			color: #1A1A1A;
		}

		/* ===== Search box ===== */
		.post-type-evenement .search-box {
			display: flex;
			align-items: center;
			gap: 6px;
			margin-left: auto !important;
		}
		.post-type-evenement .search-box input[type="search"] {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 10px;
			font-size: 13px;
			min-height: 32px;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.post-type-evenement .search-box input[type="search"]:focus {
			border-color: #1A1A1A;
			box-shadow: 0 0 0 1px #1A1A1A;
			outline: none;
		}
		.post-type-evenement .search-box .button {
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 4px 12px;
			font-size: 13px;
			min-height: 32px;
			background: #fff;
			color: #646970;
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			cursor: pointer;
			transition: border-color 0.15s, color 0.15s;
		}
		.post-type-evenement .search-box .button:hover {
			border-color: #1A1A1A;
			color: #1A1A1A;
		}

		/* ===== Compteur ===== */
		.post-type-evenement .tablenav .displaying-num {
			font-size: 12px;
			color: #a0a5aa;
			font-style: normal;
		}

		/* ===== Table ===== */
		.post-type-evenement .wp-list-table {
			border-collapse: separate;
			border-spacing: 0;
			border: none;
		}
		.post-type-evenement .wp-list-table thead th,
		.post-type-evenement .wp-list-table tfoot th {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 500;
			font-size: 11px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			color: #a0a5aa;
			border-bottom: 1px solid rgba(26,26,26,0.08);
			padding: 10px 10px;
			background: transparent;
		}
		.post-type-evenement .wp-list-table thead th a,
		.post-type-evenement .wp-list-table tfoot th a {
			color: #a0a5aa;
			text-decoration: none;
		}
		.post-type-evenement .wp-list-table thead th a:hover,
		.post-type-evenement .wp-list-table tfoot th a:hover {
			color: #1A1A1A;
		}
		.post-type-evenement .wp-list-table thead th.sorted a {
			color: #1A1A1A;
		}

		/* ===== Rows ===== */
		.post-type-evenement .wp-list-table tbody tr td {
			vertical-align: middle;
			padding: 10px;
			border-bottom: 1px solid rgba(26,26,26,0.05);
			background: transparent;
		}
		.post-type-evenement .wp-list-table tbody tr:hover td {
			background: rgba(26,26,26,0.015);
		}
		.post-type-evenement .wp-list-table tbody tr .column-title strong a {
			font-family: "General Sans", -apple-system, BlinkMacSystemFont, sans-serif;
			font-weight: 500;
			font-size: 14px;
			color: #1A1A1A;
			text-decoration: none;
		}
		.post-type-evenement .wp-list-table tbody tr .column-title strong a:hover {
			color: #555;
		}
		.post-type-evenement .wp-list-table tbody tr .row-actions a {
			color: #646970;
			font-size: 12px;
		}

		/* ===== Past event rows — dimmed ===== */
		.post-type-evenement .wp-list-table tbody tr.zz-row-past td {
			opacity: 0.5;
		}
		.post-type-evenement .wp-list-table tbody tr.zz-row-past:hover td {
			opacity: 0.8;
		}

		/* ===== Type badges ===== */
		.zz-type-badge {
			display: inline-block;
			padding: 3px 10px;
			border-radius: 20px;
			font-size: 11px;
			font-weight: 600;
			letter-spacing: 0.03em;
			text-transform: uppercase;
			white-space: nowrap;
		}
		.zz-type-badge--marche {
			background: #fef3e2;
			color: #b8860b;
		}
		.zz-type-badge--salon {
			background: #e8f0fe;
			color: #1a56db;
		}
		.zz-type-badge--exposition {
			background: #f3e8ff;
			color: #7c3aed;
		}

		/* ===== Status badge ===== */
		.zz-event-badge {
			display: inline-block;
			padding: 3px 10px;
			border-radius: 20px;
			font-size: 11px;
			font-weight: 600;
			letter-spacing: 0.03em;
			text-transform: uppercase;
			white-space: nowrap;
		}
		.zz-event-badge--upcoming {
			background: #e7f5e7;
			color: #1e7e1e;
		}
		.zz-event-badge--past {
			background: #f0f0f0;
			color: #999;
		}

		/* ===== Column widths ===== */
		.column-event_type { width: 110px; }
		.column-event_status { width: 100px; }
		.column-event_date { width: 200px; }

		/* ===== Bottom tablenav ===== */
		.post-type-evenement .tablenav.bottom {
			border-top: 1px solid rgba(26,26,26,0.08);
			margin-top: 0;
			padding-top: 12px;
		}
		.post-type-evenement .wp-list-table,
		.post-type-evenement .wp-list-table tr:last-child td {
			border-bottom: none;
		}
	</style>
	<script>
	jQuery(function($) {
		// Dim past event rows
		$('.post-type-evenement .wp-list-table tbody tr').each(function() {
			if ($(this).find('.zz-event-badge--past').length) {
				$(this).addClass('zz-row-past');
			}
		});
	});
	</script>
	<?php
});
