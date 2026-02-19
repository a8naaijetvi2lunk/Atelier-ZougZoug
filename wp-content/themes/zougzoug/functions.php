<?php
/**
 * Atelier ZougZoug — functions.php
 */

define('ZZ_VERSION', '1.0.0');

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

// CF7 — Honeypot : marquer comme spam si le champ piege est rempli
add_filter('wpcf7_spam', function ($spam) {
	if ($spam) return $spam;
	$hp = isset($_POST['zz_hp']) ? sanitize_text_field($_POST['zz_hp']) : '';
	if (!empty($hp)) {
		return true;
	}
	return $spam;
});

// CF7 — Cloudflare Turnstile : verifier le token cote serveur
add_filter('wpcf7_spam', function ($spam) {
	if ($spam) return $spam;
	$secret = defined('TURNSTILE_SECRET_KEY') ? TURNSTILE_SECRET_KEY : '';
	if (empty($secret)) return $spam; // Turnstile pas configure, on laisse passer
	$token = isset($_POST['cf-turnstile-response']) ? sanitize_text_field($_POST['cf-turnstile-response']) : '';
	if (empty($token)) return true;
	$response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
		'body' => [
			'secret'   => $secret,
			'response' => $token,
			'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
		],
	]);
	if (is_wp_error($response)) return $spam;
	$body = json_decode(wp_remote_retrieve_body($response), true);
	return empty($body['success']);
});

// REST API (doit etre charge hors is_admin car les requetes REST ne sont pas en contexte admin)
require_once get_template_directory() . '/inc/admin/admin-api.php';

// Admin bar + branding (doit etre charge partout car la barre admin s'affiche aussi cote front)
require_once get_template_directory() . '/inc/admin/admin-customize.php';

// Migration auto des URLs si le domaine change
require_once get_template_directory() . '/inc/url-migration.php';

// Admin only
if (is_admin()) {
	require_once get_template_directory() . '/inc/admin/admin-dashboard.php';
	require_once get_template_directory() . '/inc/admin/admin-pages.php';
}
