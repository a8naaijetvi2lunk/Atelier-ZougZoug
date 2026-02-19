<?php
/**
 * Migration automatique des URLs en base de donnees
 *
 * Compare le domaine dans WP_HOME (.env) avec celui stocke en BDD.
 * Si different, remplace toutes les URLs automatiquement (y compris
 * dans les donnees serialisees). Ne s'execute qu'une seule fois par
 * changement de domaine, uniquement en admin.
 */

add_action('admin_init', 'zz_auto_url_migration');

function zz_auto_url_migration() {
	if (wp_doing_ajax() || wp_doing_cron()) return;
	if (!current_user_can('manage_options')) return;

	global $wpdb;

	$new_url = defined('WP_HOME') ? rtrim(WP_HOME, '/') : '';
	if (empty($new_url)) return;

	// Lire l'URL brute en BDD (sans le override de la constante WP_SITEURL)
	$old_url = rtrim($wpdb->get_var(
		"SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'"
	), '/');

	if (empty($old_url) || $old_url === $new_url) return;

	// --- 1. Posts : post_content et guid (jamais serialises) ---
	$wpdb->query($wpdb->prepare(
		"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
		$old_url, $new_url, '%' . $wpdb->esc_like($old_url) . '%'
	));
	$wpdb->query($wpdb->prepare(
		"UPDATE {$wpdb->posts} SET guid = REPLACE(guid, %s, %s) WHERE guid LIKE %s",
		$old_url, $new_url, '%' . $wpdb->esc_like($old_url) . '%'
	));

	// --- 2. Commentaires (jamais serialises) ---
	$wpdb->query($wpdb->prepare(
		"UPDATE {$wpdb->comments} SET comment_content = REPLACE(comment_content, %s, %s) WHERE comment_content LIKE %s",
		$old_url, $new_url, '%' . $wpdb->esc_like($old_url) . '%'
	));

	// --- 3. Options (certaines peuvent etre serialisees) ---
	$options = $wpdb->get_results($wpdb->prepare(
		"SELECT option_id, option_value FROM {$wpdb->options} WHERE option_value LIKE %s",
		'%' . $wpdb->esc_like($old_url) . '%'
	));
	foreach ($options as $option) {
		$replaced = zz_url_replace_value($option->option_value, $old_url, $new_url);
		if ($replaced !== $option->option_value) {
			$wpdb->update(
				$wpdb->options,
				['option_value' => $replaced],
				['option_id' => $option->option_id]
			);
		}
	}

	// --- 4. Post meta (certaines peuvent etre serialisees) ---
	$metas = $wpdb->get_results($wpdb->prepare(
		"SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE %s",
		'%' . $wpdb->esc_like($old_url) . '%'
	));
	foreach ($metas as $meta) {
		$replaced = zz_url_replace_value($meta->meta_value, $old_url, $new_url);
		if ($replaced !== $meta->meta_value) {
			$wpdb->update(
				$wpdb->postmeta,
				['meta_value' => $replaced],
				['meta_id' => $meta->meta_id]
			);
		}
	}

	// Vider le cache objet WP pour que les nouvelles valeurs soient prises en compte
	wp_cache_flush();

	// Notification admin (visible une seule fois)
	set_transient('zz_url_migrated', ['old' => $old_url, 'new' => $new_url], 120);
}

/**
 * Remplace une URL dans une valeur, en gerant les donnees serialisees
 */
function zz_url_replace_value($value, $old_url, $new_url) {
	if (is_serialized($value)) {
		$data = @unserialize($value);
		if ($data !== false || $value === 'b:0;') {
			$data = zz_url_replace_recursive($data, $old_url, $new_url);
			return serialize($data);
		}
	}
	return str_replace($old_url, $new_url, $value);
}

/**
 * Remplacement recursif dans les tableaux et objets
 */
function zz_url_replace_recursive($data, $old_url, $new_url) {
	if (is_string($data)) {
		return str_replace($old_url, $new_url, $data);
	}
	if (is_array($data)) {
		foreach ($data as $key => $value) {
			$data[$key] = zz_url_replace_recursive($value, $old_url, $new_url);
		}
	}
	if (is_object($data)) {
		foreach (get_object_vars($data) as $key => $value) {
			$data->$key = zz_url_replace_recursive($value, $old_url, $new_url);
		}
	}
	return $data;
}

/**
 * Afficher la notification de migration reussie
 */
add_action('admin_notices', function () {
	$migrated = get_transient('zz_url_migrated');
	if (!$migrated) return;
	delete_transient('zz_url_migrated');
	$old = esc_html($migrated['old']);
	$new = esc_html($migrated['new']);
	echo '<div class="notice notice-success is-dismissible">';
	echo "<p><strong>Migration des URLs effectuee automatiquement</strong></p>";
	echo "<p><code>{$old}</code> &rarr; <code>{$new}</code></p>";
	echo '</div>';
});
