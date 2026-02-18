<?php
/**
 * Import events from revendeurs.json into CPT evenement
 */

if (!defined('ABSPATH')) {
	$wp_load = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
	if (file_exists($wp_load)) {
		require_once $wp_load;
	}
}

$data = json_decode(file_get_contents(get_template_directory() . '/data/revendeurs.json'), true);

$events = isset($data['evenements']) ? $data['evenements'] : [];
$expos = isset($data['expos']) ? $data['expos'] : [];

$months_fr = [
	'janvier' => '01', 'fevrier' => '02', 'mars' => '03', 'avril' => '04',
	'mai' => '05', 'juin' => '06', 'juillet' => '07', 'aout' => '08',
	'sept.' => '09', 'septembre' => '09', 'octobre' => '10',
	'nov.' => '11', 'novembre' => '11', 'decembre' => '12',
];

$imported = 0;

foreach ($events as $e) {
	$existing = get_posts(['post_type' => 'evenement', 'title' => $e['nom'], 'numberposts' => 1]);
	if (!empty($existing)) continue;

	$post_id = wp_insert_post([
		'post_type'   => 'evenement',
		'post_title'  => $e['nom'],
		'post_status' => 'publish',
	]);

	if (is_wp_error($post_id)) continue;

	// Parse dates from "details" like "18 — 31 mai" or "13 — 14 juin"
	if (!empty($e['details'])) {
		$month_key = strtolower(trim($e['month']));
		$month_num = isset($months_fr[$month_key]) ? $months_fr[$month_key] : '01';

		preg_match('/(\d+)\s*(?:—|-)\s*(\d+)/', $e['details'], $m);
		if (!empty($m)) {
			$year = $e['year'];
			update_post_meta($post_id, '_event_date_start', $year . '-' . $month_num . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT));
			update_post_meta($post_id, '_event_date_end', $year . '-' . $month_num . '-' . str_pad($m[2], 2, '0', STR_PAD_LEFT));
		}
	}

	update_post_meta($post_id, '_event_lieu', $e['lieu']);
	wp_set_object_terms($post_id, 'marche', 'type_evenement');

	$imported++;
	echo "Created: {$e['nom']} (ID $post_id)\n";
}

foreach ($expos as $e) {
	$existing = get_posts(['post_type' => 'evenement', 'title' => $e['nom'], 'numberposts' => 1]);
	if (!empty($existing)) continue;

	$post_id = wp_insert_post([
		'post_type'   => 'evenement',
		'post_title'  => $e['nom'],
		'post_status' => 'publish',
	]);

	if (is_wp_error($post_id)) continue;

	// Expo passee
	$month_key = strtolower(trim($e['month']));
	$month_num = isset($months_fr[$month_key]) ? $months_fr[$month_key] : '01';
	update_post_meta($post_id, '_event_date_start', $e['year'] . '-' . $month_num . '-01');

	update_post_meta($post_id, '_event_lieu', $e['lieu']);
	update_post_meta($post_id, '_event_passed', '1');
	wp_set_object_terms($post_id, 'exposition', 'type_evenement');

	$imported++;
	echo "Created expo: {$e['nom']} (ID $post_id)\n";
}

if (defined('WP_CLI')) {
	WP_CLI::success("Import termine : $imported evenements importes.");
} else {
	echo "Import termine : $imported evenements importes.\n";
}
