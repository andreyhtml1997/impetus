<?php

if (!defined('ABSPATH')) {
	exit;
}

function impetus_sp_get_lang_from_request()
{
	if (is_admin()) {
		return 'ua';
	}

	$uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
	$path = $uri ? parse_url($uri, PHP_URL_PATH) : '';

	if (!$path) {
		return 'ua';
	}

	$path = '/' . ltrim($path, '/');

	if (strpos($path, '/ru/') === 0 || $path === '/ru') {
		return 'ru';
	}

	return 'ua';
}

function impetus_sp_clean_text($text)
{
	$text = (string) $text;
	$text = wp_strip_all_tags($text);
	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	$text = preg_replace('/\s+/u', ' ', $text);
	$text = trim($text);

	return $text;
}

function impetus_sp_mb_contains($haystack, $needle)
{
	$haystack = (string) $haystack;
	$needle = (string) $needle;

	if (!$haystack || !$needle) {
		return false;
	}

	if (function_exists('mb_stripos')) {
		return mb_stripos($haystack, $needle, 0, 'UTF-8') !== false;
	}

	return stripos($haystack, $needle) !== false;
}

function impetus_sp_first_sentence($text)
{
	$text = (string) $text;
	$text = wp_strip_all_tags($text);
	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	$text = preg_replace('/\s+/u', ' ', $text);
	$text = trim($text);

	if (!$text) {
		return '';
	}

	$parts = preg_split('/(?<=[\.\!\?])\s+/u', $text);

	if (!empty($parts[0])) {
		return trim($parts[0]);
	}

	return $text;
}

function impetus_sp_expand_wpseo_vars($text, $context)
{
	$text = (string) $text;

	if (!$text) {
		return '';
	}

	if (function_exists('wpseo_replace_vars')) {
		$text = wpseo_replace_vars($text, $context);
	}

	return impetus_sp_clean_text($text);
}

function impetus_sp_get_term_name($value, $taxonomy = '')
{
	if (!$value) {
		return '';
	}

	if (is_object($value) && !empty($value->name)) {
		return trim((string) $value->name);
	}

	if (is_array($value) && isset($value['name'])) {
		return trim((string) $value['name']);
	}

	$term = $taxonomy ? get_term((int) $value, $taxonomy) : get_term((int) $value);

	if ($term && !is_wp_error($term) && !empty($term->name)) {
		return trim((string) $term->name);
	}

	return '';
}

function impetus_sp_get_term_names($value, $taxonomy = '')
{
	$names = array();

	if (!$value) {
		return $names;
	}

	if (!is_array($value)) {
		$value = array($value);
	}

	foreach ($value as $item) {
		$name = impetus_sp_get_term_name($item, $taxonomy);

		if ($name) {
			$names[] = $name;
		}
	}

	$names = array_values(array_unique(array_filter($names)));

	return $names;
}

function impetus_sp_get_group_field_text($group_id, $field_name)
{
	$group_id = (int) $group_id;

	if (!$group_id || !$field_name) {
		return '';
	}

	$value = get_field($field_name, 'product_group_' . $group_id);

	return impetus_sp_clean_text($value);
}

function impetus_sp_get_product_data($post_id)
{
	$post_id = (int) $post_id;

	$title = impetus_sp_clean_text(get_the_title($post_id));
	$brand = impetus_sp_get_term_name(get_field('product_brand', $post_id), 'brand');
	$color = impetus_sp_get_term_name(get_field('product_color', $post_id), 'color');
	$country = impetus_sp_get_term_name(get_field('product_country', $post_id), 'country');

	$material_names = impetus_sp_get_term_names(get_field('product_material', $post_id), 'material');
	$materials = $material_names ? implode(', ', $material_names) : '';

	$product_group = (int) get_field('product_group', $post_id);
	$product_description = impetus_sp_get_group_field_text($product_group, 'product_description');
	$product_composition = impetus_sp_get_group_field_text($product_group, 'product_composition');

	return array(
		'title' => $title,
		'brand' => $brand,
		'color' => $color,
		'country' => $country,
		'materials' => $materials,
		'product_description' => $product_description,
		'product_composition' => $product_composition,
	);
}

function impetus_sp_get_yoast_meta($post_id)
{
	$out = array(
		'title' => '',
		'desc' => '',
	);

	$post_id = (int) $post_id;

	if (!$post_id) {
		return $out;
	}

	$post = get_post($post_id);

	if (!$post) {
		return $out;
	}

	if (class_exists('WPSEO_Meta')) {
		$title = WPSEO_Meta::get_value('title', $post_id);
		$desc = WPSEO_Meta::get_value('metadesc', $post_id);

		$title = impetus_sp_expand_wpseo_vars($title, $post);
		$desc = impetus_sp_expand_wpseo_vars($desc, $post);

		if ($title) {
			$out['title'] = $title;
		}

		if ($desc) {
			$out['desc'] = $desc;
		}
	}

	return $out;
}

function impetus_sp_build_auto_meta($post_id)
{
	$data = impetus_sp_get_product_data($post_id);
	$lang = impetus_sp_get_lang_from_request();

	$title_parts = array();
	$title_parts[] = $data['title'];

	if ($data['brand'] && !impetus_sp_mb_contains($data['title'], $data['brand'])) {
		$title_parts[] = $data['brand'];
	}

	if ($data['materials']) {
		$title_parts[] = $data['materials'];
	}

	if ($lang === 'ru') {
		$title = implode(', ', array_filter($title_parts)) . ' — купить в Украине';
	} else {
		$title = implode(', ', array_filter($title_parts)) . ' — купити в Україні';
	}

	$title = impetus_sp_clean_text($title);

	$desc_parts = array();
	$desc_parts[] = $data['title'] . '.';

	if ($lang === 'ru') {
		if ($data['color']) {
			$desc_parts[] = 'Цвет: ' . $data['color'] . '.';
		}

		if ($data['materials']) {
			$desc_parts[] = 'Материалы: ' . $data['materials'] . '.';
		}

		if ($data['product_description']) {
			$desc_parts[] = impetus_sp_first_sentence($data['product_description']);
		} elseif ($data['product_composition']) {
			$desc_parts[] = 'Состав: ' . impetus_sp_clean_text($data['product_composition']) . '.';
		}

		if ($data['country']) {
			$desc_parts[] = 'Произведено в ' . $data['country'] . '.';
		}

		$desc_parts[] = 'Заказывайте в Underline Store. Доставка по всей Украине.';
	} else {
		if ($data['color']) {
			$desc_parts[] = 'Колір: ' . $data['color'] . '.';
		}

		if ($data['materials']) {
			$desc_parts[] = 'Матеріали: ' . $data['materials'] . '.';
		}

		if ($data['product_description']) {
			$desc_parts[] = impetus_sp_first_sentence($data['product_description']);
		} elseif ($data['product_composition']) {
			$desc_parts[] = 'Склад: ' . impetus_sp_clean_text($data['product_composition']) . '.';
		}

		if ($data['country']) {
			$desc_parts[] = 'Вироблено в ' . $data['country'] . '.';
		}

		$desc_parts[] = 'Купуйте в Underline Store. Доставка по всій Україні.';
	}

	$desc = implode(' ', array_filter($desc_parts));
	$desc = impetus_sp_clean_text($desc);

	return array(
		'title' => $title,
		'desc' => $desc,
	);
}

function impetus_sp_get_final_meta($post_id)
{
	$yoast = impetus_sp_get_yoast_meta($post_id);
	$auto = impetus_sp_build_auto_meta($post_id);

	return array(
		'title' => !empty($yoast['title']) ? $yoast['title'] : $auto['title'],
		'desc' => !empty($yoast['desc']) ? $yoast['desc'] : $auto['desc'],
	);
}

function impetus_sp_is_product_request()
{
	return is_singular('product');
}

add_filter('wpseo_title', function ($title) {
	if (!impetus_sp_is_product_request()) {
		return $title;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $title;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['title']) ? $meta['title'] : $title;
}, 30);

add_filter('wpseo_metadesc', function ($desc) {
	if (!impetus_sp_is_product_request()) {
		return $desc;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $desc;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['desc']) ? $meta['desc'] : $desc;
}, 30);

add_filter('wpseo_opengraph_title', function ($title) {
	if (!impetus_sp_is_product_request()) {
		return $title;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $title;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['title']) ? $meta['title'] : $title;
}, 30);

add_filter('wpseo_opengraph_desc', function ($desc) {
	if (!impetus_sp_is_product_request()) {
		return $desc;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $desc;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['desc']) ? $meta['desc'] : $desc;
}, 30);

add_filter('wpseo_twitter_title', function ($title) {
	if (!impetus_sp_is_product_request()) {
		return $title;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $title;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['title']) ? $meta['title'] : $title;
}, 30);

add_filter('wpseo_twitter_description', function ($desc) {
	if (!impetus_sp_is_product_request()) {
		return $desc;
	}

	$post_id = (int) get_queried_object_id();

	if (!$post_id) {
		return $desc;
	}

	$meta = impetus_sp_get_final_meta($post_id);

	return !empty($meta['desc']) ? $meta['desc'] : $desc;
}, 30);