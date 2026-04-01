<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * ACF Local JSON sync.
 * - Admin changes -> saved to /acf-json
 * - File changes -> available in ACF "Sync available"
 */
add_filter('acf/settings/save_json', function ($path) {
	$target = get_stylesheet_directory() . '/acf-json';

	if (!is_dir($target)) {
		wp_mkdir_p($target);
	}

	return $target;
});

add_filter('acf/settings/load_json', function ($paths) {
	$target = get_stylesheet_directory() . '/acf-json';

	if (!in_array($target, $paths, true)) {
		$paths[] = $target;
	}

	return $paths;
});
