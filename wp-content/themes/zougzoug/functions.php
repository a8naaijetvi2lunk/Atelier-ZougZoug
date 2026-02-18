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

// Admin
if (is_admin()) {
	require_once get_template_directory() . '/inc/admin/admin-customize.php';
	require_once get_template_directory() . '/inc/admin/admin-dashboard.php';
	require_once get_template_directory() . '/inc/admin/admin-pages.php';
	require_once get_template_directory() . '/inc/admin/admin-api.php';
}
