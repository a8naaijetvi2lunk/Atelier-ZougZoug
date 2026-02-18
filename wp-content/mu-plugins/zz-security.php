<?php
/**
 * Plugin Name: ZZ Security Hardening
 * Description: Securite de base pour Atelier ZougZoug
 */

// Desactiver XML-RPC completement
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function($headers) {
	unset($headers['X-Pingback']);
	return $headers;
});

// Masquer la version de WordPress
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// Desactiver les emoji WP (performance)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// Desactiver les embeds oEmbed inutiles
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

// Desactiver les liens RSD et WLW
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Desactiver les shortlinks
remove_action('wp_head', 'wp_shortlink_wp_head');

// Desactiver REST API pour les utilisateurs non connectes (sauf routes publiques)
add_filter('rest_authentication_errors', function($result) {
	if (true === $result || is_wp_error($result)) {
		return $result;
	}
	$public_routes = [
		'/wp/v2/pages',
		'/wp/v2/posts',
		'/zougzoug/v1/',
		'/contact-form-7/',
	];
	$current_route = $_SERVER['REQUEST_URI'] ?? '';
	foreach ($public_routes as $route) {
		if (strpos($current_route, $route) !== false) {
			return $result;
		}
	}
	if (!is_user_logged_in()) {
		return new WP_Error(
			'rest_not_logged_in',
			'Acces non autorise.',
			['status' => 401]
		);
	}
	return $result;
});

// Desactiver l'enumeration des utilisateurs
add_action('init', function() {
	if (!is_admin() && isset($_GET['author'])) {
		wp_redirect(home_url(), 301);
		exit;
	}
});

// Limiter les tentatives de login (basique)
add_filter('authenticate', function($user, $username, $password) {
	if (empty($username) || empty($password)) return $user;
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$transient_key = 'login_attempts_' . md5($ip);
	$attempts = get_transient($transient_key) ?: 0;
	if ($attempts >= 5) {
		return new WP_Error('too_many_attempts',
			'Trop de tentatives de connexion. Reessayez dans 15 minutes.');
	}
	return $user;
}, 30, 3);

add_action('wp_login_failed', function() {
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$transient_key = 'login_attempts_' . md5($ip);
	$attempts = get_transient($transient_key) ?: 0;
	set_transient($transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);
});

add_action('wp_login', function() {
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	delete_transient('login_attempts_' . md5($ip));
});
