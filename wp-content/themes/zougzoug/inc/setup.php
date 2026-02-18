<?php
/**
 * Theme setup — supports, menus, image sizes
 */

add_action('after_setup_theme', function () {
	// Title tag
	add_theme_support('title-tag');

	// Post thumbnails
	add_theme_support('post-thumbnails');

	// HTML5 support
	add_theme_support('html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	]);

	// Navigation menus
	register_nav_menus([
		'primary'   => 'Navigation principale (centre)',
		'secondary' => 'Navigation secondaire (droite)',
	]);

	// Custom image sizes
	add_image_size('projet-card', 600, 450, true);
	add_image_size('projet-lightbox', 1200, 0, false);
	add_image_size('hero-slide', 1440, 900, true);
	add_image_size('og-image', 1200, 630, true);
});

// Hide admin bar in editor preview iframe
add_filter('show_admin_bar', function ($show) {
	if (isset($_GET['zz_preview']) && absint($_GET['zz_preview']) === 1) {
		return false;
	}
	return $show;
});
