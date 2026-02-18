<?php
/**
 * Admin Customization — Menu simplifie, admin bar, login page, styles globaux
 */

/**
 * ============================================================
 * 1. MENU ADMIN SIMPLIFIE
 * ============================================================
 */
add_action('admin_menu', function () {
	// Masquer les menus inutiles pour Charlotte
	$remove = [
		'edit.php',              // Articles
		'edit-comments.php',     // Commentaires
		'tools.php',             // Outils
		'themes.php',            // Apparence (Personnaliser)
	];
	foreach ($remove as $menu) {
		remove_menu_page($menu);
	}

	// Masquer les sous-menus Reglages inutiles
	$settings_remove = [
		'options-writing.php',
		'options-reading.php',
		'options-discussion.php',
		'options-media.php',
		'options-permalink.php',
		'options-privacy.php',
	];
	foreach ($settings_remove as $sub) {
		remove_submenu_page('options-general.php', $sub);
	}
}, 999);

/**
 * Reordonner le menu admin
 */
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', function ($menu_order) {
	return [
		'index.php',                    // Dashboard
		'separator1',
		'edit.php?post_type=projet',    // Projets
		'edit.php?post_type=evenement', // Agenda
		'zz-contenu',                   // Contenu du site
		'separator2',
		'upload.php',                   // Medias
		'wpcf7',                        // Contact Form 7
		'separator-last',
		'options-general.php',          // Reglages
	];
});

/**
 * ============================================================
 * 2. ADMIN BAR — Branding ZZ
 * ============================================================
 */
// Logo ZZ en premier dans la barre (priorite 1 = avant site-name qui est a 31)
add_action('admin_bar_menu', function ($wp_admin_bar) {
	$wp_admin_bar->add_node([
		'id'    => 'zz-logo',
		'title' => '<svg width="24" height="23" viewBox="0 0 50 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;margin-top:-2px;">
			<path d="M10.0033 23.6486C10.6751 23.0327 11.4176 22.4925 12.2082 22.0422L12.2102 22.0351C17.133 19.2261 23 14.2279 23 6.36224V0H1.54637V6.17241H16.7397V6.36325C16.7397 10.3173 14.1601 13.7938 9.0724 16.6957L9.07035 16.7028C7.87626 17.3843 6.75694 18.1992 5.74309 19.1281C1.77167 22.7661 0 26.8636 0 32.414V48H18.5176C18.5176 48 21.3492 45.3525 21.3492 41.042C21.3492 37.0284 14.795 33.6903 15.4248 29.9392C15.8918 27.1604 20.1489 27.2624 22.2575 27.4179V21.3687C16.0516 20.3569 10.9854 23.1498 10.5092 28.9809C9.97974 35.4623 16.0157 38.5975 17.3173 40.4675C17.4566 40.6826 17.5058 40.7936 17.5058 41.0754C17.5058 41.4247 17.1371 41.8276 16.666 41.8276H6.26025V32.414C6.26025 27.9854 7.75644 25.7074 10.0033 23.6496V23.6486Z" fill="currentColor"/>
			<path d="M36.1645 37.0448C34.4271 37.0448 32.7813 37.4365 31.3125 38.1343V30.9412C31.3125 25.4939 34.5836 22.4264 37.2123 20.3151C44.8969 14.1418 50 11.4419 50 4.69106C50 1.86491 48.1514 0.0171662 45.3528 0.0171662C39.5076 0.0181759 39.1659 8.68037 35.3215 8.68037C31.4772 8.68037 32.84 0 32.84 0C32.84 0 27.2829 0 25.949 0C25.949 0 23.7896 12.6788 32.6681 13.2927C41.8801 13.9298 43.0339 4.17106 45.2036 4.17106C45.8098 4.17106 46.061 4.49416 46.061 5.15854C46.061 9.91624 38.6904 12.914 33.1354 17.1588C29.0368 20.2909 25.0226 23.7228 25.0226 31.7297V47.3326C25.0093 47.5537 25 47.7748 25 47.999H25.0226H31.2919H31.3146V47.5446C31.5493 45.1203 33.6346 43.2181 36.1666 43.2181C38.854 43.2181 41.0412 45.3627 41.0412 48H47.3332C47.3332 41.96 42.3247 37.0468 36.1676 37.0468L36.1645 37.0448Z" fill="currentColor"/>
		</svg>',
		'href'  => admin_url(),
		'meta'  => ['class' => 'zz-admin-bar-logo'],
	]);
}, 1);

// Nettoyage admin bar + renommer site-name
add_action('wp_before_admin_bar_render', function () {
	global $wp_admin_bar;

	// Masquer les elements inutiles
	$wp_admin_bar->remove_node('wp-logo');
	$wp_admin_bar->remove_node('updates');
	$wp_admin_bar->remove_node('comments');
	$wp_admin_bar->remove_node('new-content');
	$wp_admin_bar->remove_node('search');
	$wp_admin_bar->remove_node('customize');
	$wp_admin_bar->remove_node('appearance');

	if (is_admin()) {
		// Back-office : "Aller sur le site"
		$site_node = $wp_admin_bar->get_node('site-name');
		if ($site_node) {
			$wp_admin_bar->add_node([
				'id'    => 'site-name',
				'title' => 'Aller sur le site',
				'href'  => home_url('/'),
				'meta'  => ['target' => '_blank'],
			]);
			$wp_admin_bar->remove_node('view-site');
			$wp_admin_bar->remove_node('dashboard');
		}
	} else {
		// Front-end : remplacer "Modifier la page" par le bon lien d'edition
		$wp_admin_bar->remove_node('edit');

		$edit_url   = '';
		$edit_label = 'Modifier la page';

		if (is_front_page()) {
			$edit_url = admin_url('admin.php?page=zz-contenu-home');
		} elseif (is_page('a-propos')) {
			$edit_url = admin_url('admin.php?page=zz-contenu-about');
		} elseif (is_page('contact')) {
			$edit_url = admin_url('admin.php?page=zz-contenu-contact');
		} elseif (is_page('cours')) {
			$edit_url = admin_url('admin.php?page=zz-contenu-cours');
		} elseif (is_page('revendeurs')) {
			$edit_url = admin_url('admin.php?page=zz-contenu-revendeurs');
		} elseif (is_page('collaborations') || is_post_type_archive('projet')) {
			$edit_url   = admin_url('edit.php?post_type=projet');
			$edit_label = 'Gérer les projets';
		} elseif (is_singular('projet')) {
			$edit_url   = get_edit_post_link(get_the_ID(), 'raw');
			$edit_label = 'Modifier le projet';
		}

		if ($edit_url) {
			$wp_admin_bar->add_node([
				'id'    => 'zz-edit-page',
				'title' => $edit_label,
				'href'  => $edit_url,
			]);
		}

		// Collaborations : lien vers l'editeur custom du contenu
		if (is_page('collaborations')) {
			$wp_admin_bar->add_node([
				'id'    => 'zz-edit-page-wp',
				'title' => 'Modifier la page',
				'href'  => admin_url('admin.php?page=zz-contenu-collaborations'),
			]);
		}

		// Revendeurs : lien rapide pour ajouter un evenement
		if (is_page('revendeurs')) {
			$wp_admin_bar->add_node([
				'id'    => 'zz-new-event',
				'title' => '+ Nouvel événement',
				'href'  => admin_url('post-new.php?post_type=evenement'),
			]);
		}

		// Supprimer le sous-menu site-name sur le front
		$wp_admin_bar->remove_node('dashboard');
	}
});

/**
 * ============================================================
 * 3. PAGE DE LOGIN BRANDEE
 * ============================================================
 */
add_action('login_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	?>
	<style>
		@font-face {
			font-family: 'General Sans';
			src: url('<?php echo esc_url($uri); ?>/assets/fonts/GeneralSans-Regular.woff2') format('woff2');
			font-weight: 400;
			font-style: normal;
			font-display: swap;
		}
		@font-face {
			font-family: 'General Sans';
			src: url('<?php echo esc_url($uri); ?>/assets/fonts/GeneralSans-Medium.woff2') format('woff2');
			font-weight: 500;
			font-style: normal;
			font-display: swap;
		}

		body.login {
			background: #F8F6F3 !important;
			font-family: 'General Sans', -apple-system, sans-serif !important;
		}

		#login h1 a {
			background-image: none !important;
			width: 60px !important;
			height: 58px !important;
			margin: 0 auto 30px !important;
			pointer-events: none;
		}

		#login h1 a::after {
			content: '';
			display: block;
			width: 60px;
			height: 58px;
			background-image: url("data:image/svg+xml,%3Csvg width='60' height='58' viewBox='0 0 50 48' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.0033 23.6486C10.6751 23.0327 11.4176 22.4925 12.2082 22.0422L12.2102 22.0351C17.133 19.2261 23 14.2279 23 6.36224V0H1.54637V6.17241H16.7397V6.36325C16.7397 10.3173 14.1601 13.7938 9.0724 16.6957L9.07035 16.7028C7.87626 17.3843 6.75694 18.1992 5.74309 19.1281C1.77167 22.7661 0 26.8636 0 32.414V48H18.5176C18.5176 48 21.3492 45.3525 21.3492 41.042C21.3492 37.0284 14.795 33.6903 15.4248 29.9392C15.8918 27.1604 20.1489 27.2624 22.2575 27.4179V21.3687C16.0516 20.3569 10.9854 23.1498 10.5092 28.9809C9.97974 35.4623 16.0157 38.5975 17.3173 40.4675C17.4566 40.6826 17.5058 40.7936 17.5058 41.0754C17.5058 41.4247 17.1371 41.8276 16.666 41.8276H6.26025V32.414C6.26025 27.9854 7.75644 25.7074 10.0033 23.6496V23.6486Z' fill='%231A1A1A'/%3E%3Cpath d='M36.1645 37.0448C34.4271 37.0448 32.7813 37.4365 31.3125 38.1343V30.9412C31.3125 25.4939 34.5836 22.4264 37.2123 20.3151C44.8969 14.1418 50 11.4419 50 4.69106C50 1.86491 48.1514 0.0171662 45.3528 0.0171662C39.5076 0.0181759 39.1659 8.68037 35.3215 8.68037C31.4772 8.68037 32.84 0 32.84 0C32.84 0 27.2829 0 25.949 0C25.949 0 23.7896 12.6788 32.6681 13.2927C41.8801 13.9298 43.0339 4.17106 45.2036 4.17106C45.8098 4.17106 46.061 4.49416 46.061 5.15854C46.061 9.91624 38.6904 12.914 33.1354 17.1588C29.0368 20.2909 25.0226 23.7228 25.0226 31.7297V47.3326C25.0093 47.5537 25 47.7748 25 47.999H25.0226H31.2919H31.3146V47.5446C31.5493 45.1203 33.6346 43.2181 36.1666 43.2181C38.854 43.2181 41.0412 45.3627 41.0412 48H47.3332C47.3332 41.96 42.3247 37.0468 36.1676 37.0468L36.1645 37.0448Z' fill='%231A1A1A'/%3E%3C/svg%3E");
			background-size: contain;
			background-repeat: no-repeat;
		}

		.login form {
			background: #FFFFFF !important;
			border: 1px solid rgba(26, 26, 26, 0.06) !important;
			border-radius: 8px !important;
			box-shadow: 0 2px 12px rgba(26, 26, 26, 0.06) !important;
			padding: 26px 24px !important;
		}

		.login form .input,
		.login form input[type="text"],
		.login form input[type="password"] {
			font-family: 'General Sans', sans-serif !important;
			border: 1px solid rgba(26, 26, 26, 0.15) !important;
			border-radius: 6px !important;
			padding: 10px 14px !important;
			font-size: 15px !important;
			background: #FFFFFF !important;
			transition: border-color 0.2s ease !important;
		}

		.login form .input:focus,
		.login form input[type="text"]:focus,
		.login form input[type="password"]:focus {
			border-color: #B8956A !important;
			box-shadow: 0 0 0 1px #B8956A !important;
		}

		.login label {
			font-family: 'General Sans', sans-serif !important;
			font-size: 14px !important;
			color: #1A1A1A !important;
		}

		#wp-submit {
			font-family: 'General Sans', sans-serif !important;
			font-weight: 500 !important;
			font-size: 15px !important;
			background: #1A1A1A !important;
			border: none !important;
			border-radius: 30px !important;
			padding: 10px 32px !important;
			color: #FFFFFF !important;
			cursor: pointer !important;
			transition: background 0.3s ease !important;
			text-shadow: none !important;
			box-shadow: none !important;
		}

		#wp-submit:hover {
			background: #333333 !important;
		}

		.login #backtoblog,
		.login #nav {
			font-family: 'General Sans', sans-serif !important;
			text-align: center !important;
		}

		.login #backtoblog a,
		.login #nav a {
			color: rgba(26, 26, 26, 0.5) !important;
			transition: color 0.2s ease !important;
		}

		.login #backtoblog a:hover,
		.login #nav a:hover {
			color: #1A1A1A !important;
		}

		.login .message,
		.login .success {
			font-family: 'General Sans', sans-serif !important;
			border-left-color: #B8956A !important;
			border-radius: 6px !important;
		}
	</style>
	<?php
});

add_filter('login_headerurl', function () {
	return home_url('/');
});

add_filter('login_headertext', function () {
	return 'Atelier ZougZoug';
});

/**
 * ============================================================
 * 4. ADMIN GLOBALE — Font + Couleurs + Styles
 * ============================================================
 */
add_action('admin_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	wp_enqueue_style('zz-admin-global', $uri . '/inc/admin/admin-global.css', [], ZZ_VERSION);
});

// Charger les styles admin bar aussi cote front (utilisateurs connectes)
add_action('wp_enqueue_scripts', function () {
	if (!is_admin_bar_showing()) return;
	$uri = get_template_directory_uri();
	wp_enqueue_style('zz-admin-bar-front', $uri . '/inc/admin/admin-global.css', [], ZZ_VERSION);
});

/**
 * Masquer les notices ennuyeuses pour Charlotte
 */
add_action('admin_head', function () {
	if (!current_user_can('manage_options')) return;
	echo '<style>.update-nag, .notice:not(.zz-notice) { display: none !important; }</style>';
});

/**
 * Footer admin custom
 */
add_filter('admin_footer_text', function () {
	return '<span style="font-family:General Sans,sans-serif;color:rgba(26,26,26,0.4);font-size:13px;">Backoffice sur mesure par <a href="https://yvescharvis.fr" target="_blank" rel="noopener" style="color:rgba(26,26,26,0.4);text-decoration:underline;">Yves Charvis</a></span>';
});

add_filter('update_footer', function () {
	return '';
}, 99);
