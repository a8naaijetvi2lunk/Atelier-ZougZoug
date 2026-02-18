<?php
/**
 * Helper — charger les fichiers JSON de data/
 */

function zz_get_data(string $page): array {
	$file = get_template_directory() . '/data/' . $page . '.json';
	if (!file_exists($file)) return [];
	$json = file_get_contents($file);
	return json_decode($json, true) ?: [];
}
