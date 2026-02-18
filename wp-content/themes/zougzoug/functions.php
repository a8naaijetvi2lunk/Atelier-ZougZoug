<?php
/**
 * Atelier ZougZoug — functions.php
 */

define('ZZ_VERSION', '1.0.0');
define('ZZ_TEXT_DOMAIN', 'zougzoug');

// Includes
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/json-loader.php';
require_once get_template_directory() . '/inc/cpt-projet.php';
require_once get_template_directory() . '/inc/cpt-evenement.php';
require_once get_template_directory() . '/inc/image-optimizer.php';
require_once get_template_directory() . '/inc/seo.php';

// CF7 — desactiver autop (les <p> cassent le layout flex des form-group)
add_filter('wpcf7_autop_or_not', '__return_false');

// CF7 — desactiver la feuille de style par defaut (on a nos propres styles)
add_filter('wpcf7_load_css', '__return_false');

// REST API (doit etre charge hors is_admin car les requetes REST ne sont pas en contexte admin)
require_once get_template_directory() . '/inc/admin/admin-api.php';

// Admin bar + branding (doit etre charge partout car la barre admin s'affiche aussi cote front)
require_once get_template_directory() . '/inc/admin/admin-customize.php';

// Admin only
if (is_admin()) {
	require_once get_template_directory() . '/inc/admin/admin-dashboard.php';
	require_once get_template_directory() . '/inc/admin/admin-pages.php';
}
