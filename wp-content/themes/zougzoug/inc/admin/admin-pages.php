<?php
/**
 * Admin Pages — Menu "Contenu du site" + sous-menus par page
 */

add_action('admin_menu', function () {
	// Menu principal
	add_menu_page(
		'Contenu du site',
		'Contenu du site',
		'manage_options',
		'zz-contenu',
		'zz_admin_page_render',
		'dashicons-edit-page',
		25
	);

	// Sous-menus par page
	$pages = [
		'home'       => 'Accueil',
		'about'      => 'À propos',
		'contact'    => 'Contact',
		'cours'      => 'Cours',
		'revendeurs'     => 'Revendeurs',
		'collaborations' => 'Collaborations',
		'mentions'       => 'Mentions légales',
		'global'     => 'Paramètres globaux',
	];

	foreach ($pages as $slug => $label) {
		add_submenu_page(
			'zz-contenu',
			$label . ' — Édition',
			$label,
			'manage_options',
			'zz-contenu-' . $slug,
			'zz_admin_page_render'
		);
	}

	// Supprimer le sous-menu doublon "Contenu du site > Contenu du site"
	remove_submenu_page('zz-contenu', 'zz-contenu');
});

/**
 * Enqueue editeur assets sur les pages admin ZZ
 */
add_action('admin_enqueue_scripts', function ($hook) {
	if (strpos($hook, 'zz-contenu') === false) return;

	$uri = get_template_directory_uri();

	// Styles
	wp_enqueue_style('zz-admin-editor', $uri . '/inc/admin/admin-editor.css', ['zz-admin-global'], ZZ_VERSION);

	// WP Media (pour le picker d'images)
	wp_enqueue_media();

	// WP Editor (TinyMCE pour les champs richtext)
	wp_enqueue_editor();

	// Script editeur
	wp_enqueue_script('zz-admin-editor', $uri . '/inc/admin/admin-editor.js', ['jquery'], ZZ_VERSION, true);
	wp_localize_script('zz-admin-editor', 'zzEditor', [
		'restUrl'  => rest_url('zougzoug/v1/page/'),
		'nonce'    => wp_create_nonce('wp_rest'),
		'siteUrl'  => home_url('/'),
		'themeUrl' => $uri,
	]);
});

/**
 * Render de la page admin
 */
function zz_admin_page_render() {
	// Detecter la page courante
	$screen = get_current_screen();
	$page_slug = str_replace('contenu-du-site_page_zz-contenu-', '', $screen->id);

	// Mapping slug admin → slug JSON + slug URL front
	$pages_map = [
		'home'       => ['json' => 'home',       'url' => '/',              'label' => 'Accueil',        'icon' => 'dashicons-admin-home'],
		'about'      => ['json' => 'about',      'url' => '/a-propos/',     'label' => 'À propos',       'icon' => 'dashicons-admin-users'],
		'contact'    => ['json' => 'contact',     'url' => '/contact/',      'label' => 'Contact',        'icon' => 'dashicons-email-alt'],
		'cours'      => ['json' => 'cours',       'url' => '/cours/',        'label' => 'Cours',          'icon' => 'dashicons-welcome-learn-more'],
		'revendeurs'     => ['json' => 'revendeurs',     'url' => '/revendeurs/',      'label' => 'Revendeurs',      'icon' => 'dashicons-store'],
		'collaborations' => ['json' => 'collaborations', 'url' => '/collaborations/',  'label' => 'Collaborations',  'icon' => 'dashicons-portfolio'],
		'mentions'       => ['json' => 'mentions',       'url' => '/mentions-legales/', 'label' => 'Mentions légales', 'icon' => 'dashicons-shield'],
		'global'     => ['json' => 'global',      'url' => '/',              'label' => 'Paramètres',     'icon' => 'dashicons-admin-settings'],
	];

	$config = isset($pages_map[$page_slug]) ? $pages_map[$page_slug] : $pages_map['home'];

	?>
	<div class="wrap zz-editor-wrap" data-page="<?php echo esc_attr($config['json']); ?>" data-front-url="<?php echo esc_url(home_url($config['url'])); ?>">
		<div class="zz-editor-header">
			<span class="dashicons <?php echo esc_attr($config['icon']); ?>"></span>
			<h1><?php echo esc_html($config['label']); ?></h1>
			<div class="zz-editor-status" id="zz-editor-status"></div>
		</div>
		<div class="zz-editor-layout">
			<div class="zz-editor-form" id="zz-editor-form">
				<!-- Genere par admin-editor.js -->
				<div class="zz-editor-loading">
					<div class="zz-editor-spinner"></div>
					<span>Chargement...</span>
				</div>
			</div>
			<div class="zz-editor-divider" id="zz-editor-divider"></div>
			<div class="zz-editor-preview">
				<iframe id="zz-editor-iframe" src="<?php echo esc_url(add_query_arg('zz_preview', '1', home_url($config['url']))); ?>"></iframe>
			</div>
		</div>
	</div>
	<?php
}
