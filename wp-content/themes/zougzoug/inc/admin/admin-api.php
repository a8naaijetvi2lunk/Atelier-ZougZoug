<?php
/**
 * REST API Custom — Lecture / Sauvegarde des JSON de contenu
 */

add_action('rest_api_init', function () {
	// GET /wp-json/zougzoug/v1/page/{slug}
	register_rest_route('zougzoug/v1', '/page/(?P<slug>[a-z0-9_-]+)', [
		'methods'             => 'GET',
		'callback'            => 'zz_api_get_page',
		'permission_callback' => function () {
			return current_user_can('manage_options');
		},
		'args'                => [
			'slug' => [
				'validate_callback' => 'zz_api_validate_slug',
			],
		],
	]);

	// POST /wp-json/zougzoug/v1/page/{slug}
	register_rest_route('zougzoug/v1', '/page/(?P<slug>[a-z0-9_-]+)', [
		'methods'             => 'POST',
		'callback'            => 'zz_api_save_page',
		'permission_callback' => function () {
			return current_user_can('manage_options');
		},
		'args'                => [
			'slug' => [
				'validate_callback' => 'zz_api_validate_slug',
			],
		],
	]);
});

/**
 * Pages autorisees (whitelist)
 */
function zz_api_allowed_slugs() {
	return ['home', 'about', 'contact', 'cours', 'revendeurs', 'collaborations', 'mentions', 'global'];
}

/**
 * Valider le slug
 */
function zz_api_validate_slug($slug) {
	return in_array($slug, zz_api_allowed_slugs(), true);
}

/**
 * Chemin du fichier JSON
 */
function zz_api_json_path($slug) {
	return get_template_directory() . '/data/' . $slug . '.json';
}

/**
 * GET — Lire le JSON
 */
function zz_api_get_page($request) {
	$slug = $request->get_param('slug');
	$file = zz_api_json_path($slug);

	if (!file_exists($file)) {
		return new WP_REST_Response(['data' => new stdClass()], 200);
	}

	$content = file_get_contents($file);
	$data = json_decode($content, true);

	if (json_last_error() !== JSON_ERROR_NONE) {
		return new WP_Error('json_error', 'Le fichier JSON est invalide.', ['status' => 500]);
	}

	// Convertir les attachment IDs en URLs pour l'editeur
	$data = zz_api_resolve_ids_to_urls($data);

	return new WP_REST_Response(['data' => $data], 200);
}

/**
 * POST — Sauvegarder le JSON
 */
function zz_api_save_page($request) {
	$slug = $request->get_param('slug');
	$file = zz_api_json_path($slug);
	$data = $request->get_json_params();

	if (!isset($data['data'])) {
		return new WP_Error('missing_data', 'Le champ "data" est requis.', ['status' => 400]);
	}

	$page_data = $data['data'];

	// Convertir les URLs en attachment IDs pour le stockage
	$page_data = zz_api_resolve_urls_to_ids($page_data);

	// Sanitization recursive
	$page_data = zz_api_sanitize_recursive($page_data);

	// Encoder en JSON
	$json = wp_json_encode($page_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	if ($json === false) {
		return new WP_Error('encode_error', 'Erreur d\'encodage JSON.', ['status' => 500]);
	}

	// Backup avant ecrasement
	if (file_exists($file)) {
		$backup_dir = get_template_directory() . '/data/backups';
		if (!is_dir($backup_dir)) {
			wp_mkdir_p($backup_dir);
		}
		$backup_file = $backup_dir . '/' . $slug . '-' . date('Y-m-d-His') . '.json';
		copy($file, $backup_file);

		// Garder max 10 backups par page
		$backups = glob($backup_dir . '/' . $slug . '-*.json');
		if ($backups && count($backups) > 10) {
			sort($backups);
			$to_delete = array_slice($backups, 0, count($backups) - 10);
			foreach ($to_delete as $old) {
				unlink($old);
			}
		}
	}

	// Ecriture atomique (tmp + rename)
	$tmp_file = $file . '.tmp';
	$written = file_put_contents($tmp_file, $json);

	if ($written === false) {
		return new WP_Error('write_error', 'Impossible d\'ecrire le fichier.', ['status' => 500]);
	}

	if (!rename($tmp_file, $file)) {
		unlink($tmp_file);
		return new WP_Error('rename_error', 'Impossible de finaliser la sauvegarde.', ['status' => 500]);
	}

	return new WP_REST_Response([
		'success' => true,
		'message' => 'Contenu sauvegarde.',
	], 200);
}

/**
 * Sanitization recursive des donnees
 */
function zz_api_sanitize_recursive($data) {
	if (is_string($data)) {
		// Autoriser quelques balises HTML basiques
		return wp_kses($data, [
			'br'     => [],
			'strong' => [],
			'em'     => [],
			'a'      => ['href' => [], 'target' => [], 'rel' => []],
			'span'   => ['class' => []],
			'p'      => [],
			'ul'     => [],
			'li'     => [],
		]);
	}

	if (is_array($data)) {
		$clean = [];
		foreach ($data as $key => $value) {
			$clean_key = sanitize_text_field($key);
			$clean[$clean_key] = zz_api_sanitize_recursive($value);
		}
		return $clean;
	}

	if (is_int($data) || is_float($data)) {
		return $data;
	}

	if (is_bool($data)) {
		return $data;
	}

	if (is_null($data)) {
		return null;
	}

	return sanitize_text_field((string) $data);
}

/**
 * Conversion IDs → URLs pour l'editeur (GET)
 * - Entier positif qui est un attachment → URL complete
 * - Objet avec attachment_id → remplace par src (URL)
 */
function zz_api_resolve_ids_to_urls($data) {
	if (is_int($data) && $data > 0) {
		$url = wp_get_attachment_url($data);
		if ($url) return $url;
	}

	if (is_array($data)) {
		// Objet {attachment_id: 123, alt: "..."} → {src: "url", alt: "..."}
		if (isset($data['attachment_id']) && is_int($data['attachment_id'])) {
			$url = wp_get_attachment_url($data['attachment_id']);
			if ($url) {
				$data['src'] = $url;
				unset($data['attachment_id']);
			}
		}
		foreach ($data as $key => &$value) {
			$value = zz_api_resolve_ids_to_urls($value);
		}
	}

	return $data;
}

/**
 * Conversion URLs → IDs pour le stockage (POST)
 * - URL d'upload → attachment ID
 * - Objet {src: "upload-url", ...} → {attachment_id: ID, ...}
 */
function zz_api_resolve_urls_to_ids($data) {
	if (is_string($data) && strpos($data, '/wp-content/uploads/') !== false) {
		$id = attachment_url_to_postid($data);
		if (!$id) {
			// Essayer avec le chemin relatif
			$full = home_url('/' . ltrim($data, '/'));
			$id = attachment_url_to_postid($full);
		}
		if ($id > 0) return $id;
	}

	if (is_array($data)) {
		// Objet {src: "upload-url", alt: "..."} → {attachment_id: ID, alt: "..."}
		if (isset($data['src']) && is_string($data['src']) && strpos($data['src'], '/wp-content/uploads/') !== false) {
			$id = attachment_url_to_postid($data['src']);
			if (!$id) {
				$full = home_url('/' . ltrim($data['src'], '/'));
				$id = attachment_url_to_postid($full);
			}
			if ($id > 0) {
				$data['attachment_id'] = $id;
				unset($data['src']);
			}
		}
		foreach ($data as $key => &$value) {
			$value = zz_api_resolve_urls_to_ids($value);
		}
	}

	return $data;
}
