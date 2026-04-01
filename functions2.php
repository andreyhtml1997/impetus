<?php

if (!defined('ABSPATH'))
	exit;




require get_template_directory() . '/functions/custom/promocode.php';



require get_template_directory() . '/functions/cleanup.php';
require get_template_directory() . '/functions/setup.php';
require get_template_directory() . '/functions/enqueues.php';


function include_class($class)
{
	require_once "functions/class-$class.php";
}

include_class('novaposhta');
include_class('liqpay');

add_action('after_setup_theme', 'theme_setup');
function theme_setup()
{
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-logo');
	add_theme_support('html5', array(
		'comment-list',
		'comment-form',
		'search-form',
		'gallery',
		'caption',
		'style',
		'script'
	));
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('automatic-feed-links');
}

add_action('init', 'custom_menu');
function custom_menu()
{

	register_nav_menu('header-menu', 'Header menu');
	register_nav_menu('page-menu', 'Sidebar menu');
	register_nav_menu('footer-mobile-menu', 'Footer mobile menu');
	register_nav_menu('footer-right-menu', 'Footer right menu');
	register_nav_menu('footer-first-left-menu', 'Footer first left menu');
	register_nav_menu('footer-second-left-menu', 'Footer second left menu');
	register_nav_menu('footer-third-left-menu', 'Footer third left menu');
	register_nav_menu('chosen-language-menu', 'Chosen language menu');
	register_nav_menu('languages-menu', 'Languages menu');

	register_nav_menu('cabinet_menu', __('Cabinet menu '));
}

add_action('before_delete_post', 'forbidden_delete_posts', 10, 2);
function forbidden_delete_posts($post_id, $post)
{

	if (
		in_array($post_id, array(
			HOME_PAGE_ID,
			BLOG_PAGE_ID,
			ABOUT_US_PAGE_ID,
			BRANDS_PAGE_ID,
			PAYMENT_AND_DELIVERY_PAGE_ID,
			FAQ_PAGE_ID,
			PRIVACY_POLICY_PAGE_ID,
			TERMS_AND_CONDITIONS_PAGE_ID,
			PARTNERSHIP_PAGE_ID,
			CONTACTS_PAGE_ID,
			CATALOG_PAGE_ID,
			WISHLIST_PAGE_ID,
			CHECKOUT_PAGE_ID,
			THANKS_PAGE_ID,
		))
	)
		wp_die('Видалити неможливо! Зверніться до розробника!');

	if (in_array($post->post_type, array('product')))
		wp_die('Видалити неможливо! Зверніться до розробника!');
}

/* add_action('pre_delete_term', 'forbidden_delete_terms', 1, 2);
function forbidden_delete_terms($term_id, $taxonomy)
{

	if (
		in_array($term_id, array(
			FOOTER_RIGHT_MENU_ID,
			FOOTER_FIRST_LEFT_MENU_ID,
			FOOTER_SECOND_LEFT_MENU_ID,
			FOOTER_THIRD_LEFT_MENU_ID,
			FOOTER_MOBILE_MENU_ID,
			HEADER_MENU_ID,
			CHOSEN_LANGUAGE_MENU_ID,
			LANGUAGES_MENU_ID,
			PAGE_MENU_ID,
		))
	)
		wp_die('Видалити неможливо! Зверніться до розробника!');

	if (
		in_array($taxonomy, array(
			'brand',
			'product_category',
			'audience_category',
			'material',
			'size',
			'color',
		))
	)
		wp_die('Видалити неможливо! Зверніться до розробника!');
} */

function _wp_redirect($url, $status = 302)
{
	wp_redirect($url, $status);
	exit;
}

add_action('template_redirect', 'maybe_redirect', 1);
function maybe_redirect()
{

	if (is_page(THANKS_PAGE_ID)) {

		global $order_id;

		if (!isset($_GET['order_id']))
			_wp_redirect(home_url());

		$order_id = absint($_GET['order_id']);
		if (empty($order_id))
			_wp_redirect(home_url());
	}
}
//Replacement of jpeg and png files with webp when downloading to Media
add_filter('wp_handle_sideload_prefilter', 'replacement_image_file_when_downloading');
add_filter('wp_handle_upload_prefilter', 'replacement_image_file_when_downloading');
function replacement_image_file_when_downloading($file)
{

	if (
		!in_array($file['type'], array(
			'image/jpeg',
			'image/png'
		))
	)
		return $file;


	$destination = webpImage($file['tmp_name'], $file['tmp_name']);
	$info = stat($destination);
	if (!isset($info['size']))
		return $file;


	$new_format = ".webp";
	$old_format = array("/\.(jpg|png|jpeg)$/i");

	$file['type'] = 'image/webp';
	$file['size'] = $info['size'];
	$file['name'] = preg_replace($old_format, $new_format, $file['name']);

	return $file;
}

function webpImage($source, $destination = false, $quality = 100, $removeOld = false)
{

	$info = getimagesize($source);
	$isAlpha = false;

	if (!$destination) {
		$dir = pathinfo($source, PATHINFO_DIRNAME);
		$name = pathinfo($source, PATHINFO_FILENAME);
		$destination = $dir . DIRECTORY_SEPARATOR . $name . '.webp';
	}

	if ($info['mime'] == 'image/jpeg')
		$image = imagecreatefromjpeg($source);
	elseif ($isAlpha = $info['mime'] == 'image/gif') {
		$image = imagecreatefromgif($source);
	} elseif ($isAlpha = $info['mime'] == 'image/png') {
		$image = imagecreatefrompng($source);
	} else {
		return $source;
	}

	if ($isAlpha) {
		imagepalettetotruecolor($image);
		imagealphablending($image, true);
		imagesavealpha($image, true);
	}

	imagewebp($image, $destination, $quality);

	if ($removeOld)
		unlink($source);

	return $destination;
}

add_filter('nav_menu_item_id', 'nav_menu_item_id_filter', 10, 4);
function nav_menu_item_id_filter($menu_item_id, $menu_item, $args, $depth)
{

	if (
		is_int($args->menu) && in_array($args->menu, array(
			PAGE_MENU_ID,
			FOOTER_RIGHT_MENU_ID,
			FOOTER_FIRST_LEFT_MENU_ID,
			FOOTER_SECOND_LEFT_MENU_ID,
			FOOTER_THIRD_LEFT_MENU_ID,
			FOOTER_MOBILE_MENU_ID,
			HEADER_MENU_ID,
			CHOSEN_LANGUAGE_MENU_ID,
			LANGUAGES_MENU_ID
		))
	)
		return '';

	return $menu_item_id;
}

add_filter('nav_menu_css_class', 'nav_menu_item_class_filter', 10, 4);
function nav_menu_item_class_filter($classes, $item, $args, $depth)
{

	if (
		is_int($args->menu) && in_array($args->menu, array(
			FOOTER_RIGHT_MENU_ID,
			FOOTER_FIRST_LEFT_MENU_ID,
			FOOTER_SECOND_LEFT_MENU_ID,
			FOOTER_THIRD_LEFT_MENU_ID,
			FOOTER_MOBILE_MENU_ID,
			CHOSEN_LANGUAGE_MENU_ID,
			LANGUAGES_MENU_ID
		))
	)
		return array();

	if (
		is_int($args->menu) && in_array($args->menu, array(
			PAGE_MENU_ID,
			HEADER_MENU_ID
		))
	) {

		$_classes = array();

		if (in_array('current-menu-item', $classes))
			$_classes[] = 'active';

		if (in_array('menu-item-has-children', $classes))
			$_classes[] = 'has-children';

		return $_classes;
	}

	return $classes;
}

add_filter('nav_menu_link_attributes', 'nav_menu_item_link_attributes_filter', 10, 4);
function nav_menu_item_link_attributes_filter($atts, $item, $args, $depth)
{

	if (is_int($args->menu) && in_array($args->menu, array(HEADER_MENU_ID)))
		$atts['class'] = 'nav-item';

	return $atts;
}

add_filter('wp_nav_menu_objects', 'add_icons_to_menu_items', 10, 2);
function add_icons_to_menu_items($items, $args)
{

	if (is_int($args->menu) && in_array($args->menu, array(PAGE_MENU_ID)) && !empty($items)) {
		foreach ($items as $item) {

			if (empty($item->classes[0]))
				continue;

			$item->title = sprintf(
				'<span class="ic %2$s"></span>
									<span class="value">%1$s</span>',
				$item->title,
				$item->classes[0],
			);
		}
	}

	if (
		is_int($args->menu) && in_array($args->menu, array(
			FOOTER_FIRST_LEFT_MENU_ID,
			FOOTER_SECOND_LEFT_MENU_ID,
			FOOTER_THIRD_LEFT_MENU_ID,
			FOOTER_MOBILE_MENU_ID,
			HEADER_MENU_ID,
			FOOTER_RIGHT_MENU_ID
		)) && !empty($items)
	) {
		foreach ($items as $key => $item) {

			switch ($item->object) {
				case 'audience_category':

					$item->url = get_catalog_url(get_term($item->object_id)->slug);

					break;
				case 'product_category':

					$product_category_audience_objs = get_field('product_category_audiences', 'product_category_' . $item->object_id);

					$audience_category_slug = get_audience_category_slug_by_product_category($product_category_audience_objs);
					if ($audience_category_slug === false) {
						unset($items[$key]);
						break;
					}

					$item->url = get_catalog_url($audience_category_slug, get_term($item->object_id)->slug);

					break;
				case 'brand':

					$brand_audience_objs = get_field('brand_audiences', 'brand_' . $item->object_id);

					$audience_category_slug = get_audience_category_slug_by_brand($brand_audience_objs);
					if ($audience_category_slug === false) {
						unset($items[$key]);
						break;
					}

					$item->url = get_catalog_url($audience_category_slug, null, get_term($item->object_id)->slug);

					break;
			}
		}
	}

	return $items;
}

function replace_phone($phone)
{
	return preg_replace('/\D+/', '', $phone);
}

function mask_phone($phone)
{
	return sprintf(
		'+%1$s (%2$s) %3$s-%4$s-%5$s',
		substr($phone, 0, 2),
		substr($phone, 2, 3),
		substr($phone, 5, 3),
		substr($phone, 8, 2),
		substr($phone, 10, 2)
	);
}

function ajax_security($is_front = false)
{

	$notice = 'Виникла помилка! Спробуйте оновити сторінку!';
	$notice = $is_front ? front_notice_html($notice) : $notice;

	$security = wp_strip_all_tags($_POST['security']);
	if (!wp_verify_nonce($security, NONCE))
		wp_send_json_error(_add_ajax_notice(array($notice)));
}

function front_notice_html($notice, $class = 'fail')
{
	return sprintf('<p class="%2$s">%1$s</p>', $notice, $class);
}

function _add_ajax_notice($errors, $status = 3, $answer = array())
{

	if (!isset($answer['error']['input']))
		$answer['error']['input'] = array();

	if (!isset($answer['error']['info']))
		$answer['error']['info'] = array();

	if (!isset($answer['error']['info_error']))
		$answer['error']['info_error'] = array();

	if (!isset($answer['error']['info_success']))
		$answer['error']['info_success'] = array();

	if (!isset($answer['error']['info_notice']))
		$answer['error']['info_notice'] = array();

	if (!isset($answer['error']['message']))
		$answer['error']['message'] = array();

	if (is_wp_error($errors)) {
		foreach ($errors->get_error_codes() as $error_code) {
			$answer['error']['input'][] = $error_code;
			$answer['error']['info'][] = $errors->get_error_message($error_code);
		}
	} elseif (!empty($errors)) {
		foreach ($errors as $input => $info) {

			if ($input === 'info_error') {
				$answer['error']['info_error'][] = $info;
			} elseif ($input === 'info_success') {
				$answer['error']['info_success'][] = $info;
			} elseif ($input === 'info_notice') {
				$answer['error']['info_notice'][] = $info;
			} elseif ($input === 'message') {
				foreach ($info as $key => $value)
					$answer['error']['message'][] = array(
						'name' => $key,
						'message' => $value,
					);
			} else {
				$answer['error']['input'][] = $input;
				$answer['error']['info'][] = $info;
			}
		}
	}


	$answer['error']['info'] = array_diff($answer['error']['info'], array(''));
	$answer['error']['info_error'] = array_diff($answer['error']['info_error'], array(''));
	$answer['error']['info_success'] = array_diff($answer['error']['info_success'], array(''));
	$answer['error']['info_notice'] = array_diff($answer['error']['info_notice'], array(''));

	$answer['error']['input'] = array_unique($answer['error']['input']);
	$answer['error']['info'] = array_unique($answer['error']['info']);
	$answer['error']['info_error'] = array_unique($answer['error']['info_error']);
	$answer['error']['info_success'] = array_unique($answer['error']['info_success']);
	$answer['error']['info_notice'] = array_unique($answer['error']['info_notice']);

	$answer['error']['input'] = array_diff($answer['error']['input'], array(
		'',
		0
	));

	$answer['error']['input'] = array_values($answer['error']['input']);
	$answer['error']['info'] = array_values($answer['error']['info']);
	$answer['error']['info_error'] = array_values($answer['error']['info_error']);
	$answer['error']['info_success'] = array_values($answer['error']['info_success']);
	$answer['error']['info_notice'] = array_values($answer['error']['info_notice']);

	$answer['status'] = $status;

	return $answer;
}

add_filter('body_class', 'my_body_class');
function my_body_class($classes)
{

	if (is_page(CHECKOUT_PAGE_ID))
		$classes[] = 'no-banner';

	return $classes;
}

add_action('wp_loaded', 'my_flush_rules');
function my_flush_rules()
{

	global $wp_rewrite;

	$rules = get_option('rewrite_rules');

	$catalog_page_slug = get_post_field('post_name', CATALOG_PAGE_ID);
	if (empty($catalog_page_slug))
		return;

	if (
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$']) ||
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$']) ||
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$']) ||
		!isset($rules['uk/(' . $catalog_page_slug . ')/([^/]+)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$']) ||
		!isset($rules['en/(' . $catalog_page_slug . ')/([^/]+)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$']) ||
		!isset($rules['(' . $catalog_page_slug . ')/([^/]+)/?$'])
	)
		$wp_rewrite->flush_rules();
}

add_filter('rewrite_rules_array', 'my_insert_rewrite_rules');
function my_insert_rewrite_rules($rules)
{

	$catalog_page_slug = get_post_field('post_name', CATALOG_PAGE_ID);
	if (empty($catalog_page_slug))
		return $rules;

	$newrules = array(
		'uk/(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&highlighted=$matches[3]',
		'uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&highlighted=$matches[4]',
		'uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]',
		'uk/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]',
		'uk/(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]',
		'uk/(' . $catalog_page_slug . ')/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&highlighted=$matches[3]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&highlighted=$matches[4]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]',
		'en/(' . $catalog_page_slug . ')/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]',
		'(' . $catalog_page_slug . ')/([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&highlighted=$matches[3]',
		'(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/(new|bestseller|sale)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&highlighted=$matches[4]',
		'(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]',
		'(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]',
		'(' . $catalog_page_slug . ')/([^/]+)/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]',
		'(' . $catalog_page_slug . ')/([^/]+)/?$' => 'index.php?pagename=$matches[1]&audience_category=$matches[2]',
	);
	return $newrules + $rules;
}

add_filter('query_vars', 'my_insert_query_vars');
function my_insert_query_vars($vars)
{

	array_push(
		$vars,
		'brand',
		'audience_category',
		'product_category',
		'highlighted',
	);
	return $vars;
}

function get_user_cookie_modal_subscribe()
{

	return isset($_COOKIE['modal_subscribe']) ? $_COOKIE['modal_subscribe'] : 0;
}

function get_user_cookie_size()
{

	return isset($_COOKIE['chosen_size']) ? $_COOKIE['chosen_size'] : 0;
}

function get_user_cookie_wishlist()
{

	return isset($_COOKIE['wishlist']) ? maybe_unserialize($_COOKIE['wishlist']) : array();
}

function get_user_cookie_cart()
{

	return isset($_COOKIE['cart']) ? maybe_unserialize($_COOKIE['cart']) : array();
}

function get_user_cookie_audience_category()
{

	return isset($_COOKIE['audience_category']) ? $_COOKIE['audience_category'] : get_term(WOMEN_AUDIENCE_CATEGORY_TERM_ID)->slug;
}

function get_user_cookie_sort_by()
{

	$sort_params = get_sort_params();

	$first_key = array_key_first($sort_params);

	return isset($_COOKIE['sort_by']) ? $_COOKIE['sort_by'] : $first_key;
}

function get_share_link_by($social, $url, $text = '')
{

	switch ($social) {
		case 'linkedin':
			return add_query_arg(array(
				'url' => $url,
				'title' => $text
			), 'https://www.linkedin.com/shareArticle');
		case 'twitter':
			return add_query_arg(array(
				'url' => $url,
				'text' => $text
			), 'https://twitter.com/intent/tweet');
		case 'facebook':
			return add_query_arg(array(
				'u' => $url,
				'quote' => $text
			), 'https://www.facebook.com/sharer/sharer.php');
		case 'telegram':
			return add_query_arg(array(
				'url' => $url,
				'text' => $text
			), 'https://t.me/share/url');
		case 'whatsapp':
			return add_query_arg(array('text' => trim($text . ' ' . $url)), 'https://api.whatsapp.com/send');
		case 'reddit':
			return add_query_arg(array(
				'url' => $url,
				'title' => $text
			), 'http://www.reddit.com/submit');
		case 'viber':
			return add_query_arg(array('text' => trim($text . ' ' . $url)), 'viber://forward');
	}

	return false;
}

add_filter('kses_allowed_protocols', 'custom_allowed_protocols');
function custom_allowed_protocols($protocols)
{

	$protocols[] = 'viber';

	return $protocols;
}

function get_catalog_url($audience_category = null, $product_category = null, $brand = null, $brand_product_category = null, $highlighted = null)
{

	if ($audience_category === null)
		$audience_category = get_user_cookie_audience_category();

	return sprintf(
		'%1$s%2$s%3$s%4$s%5$s%6$s',
		get_permalink(CATALOG_PAGE_ID),
		trailingslashit($audience_category),
		!empty($product_category) ? trailingslashit($product_category) : '',
		!empty($brand) ? trailingslashit('brand_' . $brand) : '',
		!empty($brand_product_category) ? trailingslashit($brand_product_category) : '',
		!empty($highlighted) ? trailingslashit($highlighted) : ''
	);
}

add_action('template_redirect', 'frontend_exist_catalog', 1);
function frontend_exist_catalog()
{

	if (!is_page(CATALOG_PAGE_ID))
		return;


	$audience_category_obj = get_audience_category_by_query_var_audience_category();
	if ($audience_category_obj === false)
		_wp_redirect(home_url());
}

add_action('template_redirect', 'frontend_exist_product', 2);
function frontend_exist_product()
{

	if (!is_singular('product'))
		return;


	global $product_obj;

	$post_status = isset($_GET['preview']) && $_GET['preview'] == true ? 'any' : 'publish';

	$product_objs = get_products(array(
		'product_ids' => get_the_ID(),
		'post_status' => $post_status
	));
	$product_obj = array_shift($product_objs);
	if (isset($product_obj->ID))
		return;


	global $wp_query;
	$wp_query->set_404();
	status_header(404);
	nocache_headers();
}

add_action('template_redirect', 'frontend_set_cookie', 3);
function frontend_set_cookie()
{

	$audience_category = get_query_var('audience_category', false);
	if ($audience_category !== false) {

		$audience_category_obj = get_term_by('slug', $audience_category, 'audience_category');
		if (isset($audience_category_obj->term_id))
			update_cookie('audience_category', $audience_category_obj->slug);
	}
}

function update_cookie($name, $value, $period = 30)
{

	$_COOKIE[$name] = $value;

	setcookie($name, $value, time() + $period * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
}

function get_audience_category_by_query_var_audience_category()
{

	$audience_category = get_query_var('audience_category');
	if ($audience_category === '')
		return false;

	return get_term_by('slug', $audience_category, 'audience_category');
}

function get_product_category_by_query_var_product_category()
{

	$product_category = get_query_var('product_category');
	if ($product_category === '')
		return false;

	return get_term_by('slug', $product_category, 'product_category');
}

function get_brand_by_query_var_brand()
{

	$brand = get_query_var('brand');
	if ($brand === '')
		return false;

	return get_term_by('slug', $brand, 'brand');
}

function get_blog_posts($offset = 0)
{

	$per_page = get_option('posts_per_page', 6);

	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'numberposts' => $per_page + 1, //1 - additional post to understand further pagination
		'offset' => $offset,
		'order' => 'DESC',
	);
	$post_objs = get_posts($args);

	$_post_objs = array_slice($post_objs, 0, $per_page);


	ob_start();

	global $post;

	if (!empty($_post_objs)) {
		foreach ($_post_objs as $post) {

			setup_postdata($post);

			get_template_part('templates/post-item');
		}
	}

	wp_reset_postdata();

	return array(
		'html' => ob_get_clean(),
		'is_hidden_button' => count($post_objs) > $per_page ? false : true,
	);
}

add_action('wp_ajax_front_load_more_posts', 'front_load_more_posts');
add_action('wp_ajax_nopriv_front_load_more_posts', 'front_load_more_posts');
function front_load_more_posts()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (!isset($data['offset']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$answer = get_blog_posts($data['offset']);

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function get_last_posts()
{

	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 8,
	);

	return get_posts($args);
}

function get_audience_category_slug_by_product_category($product_category_audience_objs)
{

	if (empty($product_category_audience_objs))
		return false;

	$user_audience_category = get_user_cookie_audience_category();

	$_product_category_audience_objs = wp_list_filter($product_category_audience_objs, array('slug' => $user_audience_category));

	return !empty($_product_category_audience_objs) ? $user_audience_category : $product_category_audience_objs[0]->slug;
}

function get_audience_category_slug_by_brand($brand_audience_objs)
{

	if (empty($brand_audience_objs))
		return false;

	$user_audience_category = get_user_cookie_audience_category();

	$_brand_audience_objs = wp_list_filter($brand_audience_objs, array('slug' => $user_audience_category));

	return !empty($_brand_audience_objs) ? $user_audience_category : $brand_audience_objs[0]->slug;
}

function get_audience_category_id_by_user_cookie_audience_category()
{

	$audience_category = get_user_cookie_audience_category();

	$audience_category_obj = get_term_by('slug', $audience_category, 'audience_category');

	return isset($audience_category_obj->term_id) ? $audience_category_obj->term_id : WOMEN_AUDIENCE_CATEGORY_TERM_ID;
}

function get_product_colors($include_ids = array(), $exclude_empty = false)
{

	if ($exclude_empty && empty($include_ids))
		return array();

	return get_terms(array(
		'taxonomy' => 'color',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
	));
}

function get_product_materials($include_ids = array(), $exclude_empty = false)
{

	if ($exclude_empty && empty($include_ids))
		return array();

	return get_terms(array(
		'taxonomy' => 'material',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
	));
}

function get_product_sizes($include_ids = array(), $exclude_empty = false)
{

	if ($exclude_empty && empty($include_ids))
		return array();

	return get_terms(array(
		'taxonomy' => 'size',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
		'orderby' => 'term_id',
		'order' => 'ASC',
	));
}

function get_brands($include_ids = array())
{

	return get_terms(array(
		'taxonomy' => 'brand',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
	));
}

function get_audience_categories($include_ids = array())
{

	return get_terms(array(
		'taxonomy' => 'audience_category',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
		'orderby' => 'term_id',
		'order' => 'ASC',
	));
}

function get_product_categories($include_ids = array())
{

	return get_terms(array(
		'taxonomy' => 'product_category',
		'parent' => 0,
		'hide_empty' => false,
		'include' => $include_ids,
	));
}

add_filter('pre_get_document_title', 'change_page_title');
function change_page_title($title)
{

	if (!is_page(CATALOG_PAGE_ID))
		return $title;

	return get_catalog_pagetitle() . ' - ' . get_bloginfo('name');
}

function get_catalog_pagetitle()
{

	$audience_category_obj = get_audience_category_by_query_var_audience_category();
	$product_category_obj = get_product_category_by_query_var_product_category();
	$brand_obj = get_brand_by_query_var_brand();

	$audience_category_fullname = get_field('audience_category_fullname', 'audience_category_' . $audience_category_obj->term_id);

	$title = $audience_category_fullname ?: 'Каталог';

	if ($product_category_obj)
		$title .= ' - ' . $product_category_obj->name;

	if ($brand_obj)
		$title .= ' - ' . $brand_obj->name;

	return $title;
}

function get_brands_by_audience_category($audience_category)
{

	global $wpdb;
	$meta_objs = $wpdb->get_results(
		"SELECT * 
								FROM {$wpdb->termmeta} 
								WHERE meta_key = 'brand_audiences'"
	);

	if (empty($meta_objs))
		return array();


	$brand_ids = array();

	foreach ($meta_objs as $meta_obj) {

		$brand_audiences = maybe_unserialize($meta_obj->meta_value);

		if (in_array($audience_category, $brand_audiences))
			$brand_ids[] = $meta_obj->term_id;
	}

	if (empty($brand_ids))
		return array();


	return get_brands($brand_ids);
}

/* function get_filter_available_params($audience_category, $product_category, $brand, $field)
{

	$meta_keys = array('product_group');

	if (
		in_array($field, array(
			'product_price',
			'product_size',
			'product_color',
			'is_product_new',
			'is_product_sale',
			'is_product_bestseller'
		))
	)
		$meta_keys[] = $field;

	if ($field == 'product_price')
		$meta_keys[] = 'product_promo_price';

	$_meta_keys = "'" . implode("','", $meta_keys) . "'";


	global $wpdb;
	$meta_objs = $wpdb->get_results(
		"SELECT * 
								FROM {$wpdb->postmeta} 
								WHERE meta_key IN ($_meta_keys)"
	);

	if (empty($meta_objs))
		return array();



	$_meta_objs = wp_list_filter($meta_objs, array('meta_key' => 'product_group'));
	$_post_group_ids = wp_list_pluck($_meta_objs, 'meta_value');

	$_post_group_ids = array_diff($_post_group_ids, array(
		0,
		''
	));
	$_post_group_ids = array_unique($_post_group_ids);


	$term_meta_keys = array('product_audience_category');

	if (in_array($field, array('product_material')))
		$term_meta_keys[] = $field;

	if (!empty($product_category))
		$term_meta_keys[] = 'product_product_category';

	if (!empty($brand))
		$term_meta_keys[] = 'product_brand';


	$_post_group_ids = implode(',', $_post_group_ids);
	$_term_meta_keys = "'" . implode("','", $term_meta_keys) . "'";


	global $wpdb;
	$term_meta_objs = $wpdb->get_results(
		"SELECT * 
								FROM {$wpdb->termmeta} 
								WHERE term_id IN ($_post_group_ids) 
									AND meta_key IN ($_term_meta_keys)"
	);


	$products_metadata = array();

	foreach ($meta_objs as $item) {

		if (!isset($products_metadata[$item->post_id]))
			$products_metadata[$item->post_id] = array(
				'post_id' => $item->post_id,
				'product_price' => 0,
				'product_promo_price' => 0,
			);

		if ($item->meta_key == 'product_group') {

			$_term_meta_objs = wp_list_filter($term_meta_objs, array('term_id' => $item->meta_value));

			if (!empty($_term_meta_objs))
				foreach ($_term_meta_objs as $_item)
					$products_metadata[$item->post_id][$_item->meta_key] = maybe_unserialize($_item->meta_value);

			continue;
		}


		$products_metadata[$item->post_id][$item->meta_key] = maybe_unserialize($item->meta_value);
	}

	if (empty($products_metadata))
		return array();




	$products_metadata = wp_list_filter($products_metadata, array('product_audience_category' => $audience_category));

	if (!empty($brand))
		$products_metadata = wp_list_filter($products_metadata, array('product_brand' => $brand));

	if (!empty($product_category) && !empty($products_metadata)) {
		foreach ($products_metadata as $key => $product_metadata) {

			if (!in_array($product_category, $product_metadata['product_product_category']))
				unset($products_metadata[$key]);
		}
	}

	if (empty($products_metadata))
		return array();


	switch ($field) {
		case 'is_product_new':
		case 'is_product_sale':
		case 'is_product_bestseller':

			$_products_metadata = wp_list_filter($products_metadata, array($field => 1));

			return !empty($_products_metadata);
		case 'product_color':

			$params = wp_list_pluck($products_metadata, $field);

			break;
		case 'product_size':
		case 'product_material':

			$params = wp_list_pluck($products_metadata, $field);
			$params = array_merge(...$params);

			break;
		case 'product_price':

			$prices = array();

			foreach ($products_metadata as $product_metadata)
				$prices[] = $product_metadata['product_promo_price'] != 0 && $product_metadata['product_promo_price'] < $product_metadata['product_price'] ? $product_metadata['product_promo_price'] : $product_metadata['product_price'];

			return array(
				'min' => !empty($prices) ? min($prices) : 0,
				'max' => !empty($prices) ? max($prices) : 0,
			);
	}


	if (empty($params))
		return array();


	$params = array_unique($params);

	$function_name = 'get_' . $field . 's'; //get_product_colors(), get_product_sizes(), get_product_materials()

	return $function_name($params);
} */



function get_filter_available_params($audience_category, $product_category, $brand, $field)
{
	$meta_keys = array('product_group');

	if (
		in_array($field, array(
			'product_price',
			'product_size',
			'product_color',
			'is_product_new',
			'is_product_sale',
			'is_product_bestseller',
		))
	) {
		$meta_keys[] = $field;
	}

	if ($field == 'product_price') {
		$meta_keys[] = 'product_promo_price';
	}

	$_meta_keys = "'" . implode("','", $meta_keys) . "'";

	global $wpdb;

	// <<< ВАЖНО: берём мету ТОЛЬКО от опубликованных товаров
	$meta_objs = $wpdb->get_results("
        SELECT pm.*
        FROM {$wpdb->postmeta} AS pm
        INNER JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
        WHERE pm.meta_key IN ($_meta_keys)
          AND p.post_type = 'product'
          AND p.post_status = 'publish'
    ");

	if (empty($meta_objs)) {
		return array();
	}

	// Собираем ID групп товаров из меты product_group
	$_meta_objs = wp_list_filter($meta_objs, array('meta_key' => 'product_group'));
	$_post_group_ids = wp_list_pluck($_meta_objs, 'meta_value');
	$_post_group_ids = array_diff($_post_group_ids, array(
		0,
		''
	));
	$_post_group_ids = array_unique($_post_group_ids);

	if (empty($_post_group_ids)) {
		return array();
	}

	$term_meta_keys = array('product_audience_category');

	if (in_array($field, array('product_material'))) {
		$term_meta_keys[] = $field;
	}

	if (!empty($product_category)) {
		$term_meta_keys[] = 'product_product_category';
	}

	if (!empty($brand)) {
		$term_meta_keys[] = 'product_brand';
	}

	$_post_group_ids = implode(',', $_post_group_ids);
	$_term_meta_keys = "'" . implode("','", $term_meta_keys) . "'";

	$term_meta_objs = $wpdb->get_results("
        SELECT *
        FROM {$wpdb->termmeta}
        WHERE term_id IN ($_post_group_ids)
          AND meta_key IN ($_term_meta_keys)
    ");

	$products_metadata = array();

	// Склеиваем всё в один массив по post_id
	foreach ($meta_objs as $item) {

		if (!isset($products_metadata[$item->post_id])) {
			$products_metadata[$item->post_id] = array(
				'post_id' => $item->post_id,
				'product_price' => 0,
				'product_promo_price' => 0,
			);
		}

		if ($item->meta_key === 'product_group') {

			$_term_meta_objs = wp_list_filter($term_meta_objs, array('term_id' => $item->meta_value));

			if (!empty($_term_meta_objs)) {
				foreach ($_term_meta_objs as $_item) {
					$products_metadata[$item->post_id][$_item->meta_key] = maybe_unserialize($_item->meta_value);
				}
			}

			continue;
		}

		$products_metadata[$item->post_id][$item->meta_key] = maybe_unserialize($item->meta_value);
	}

	if (empty($products_metadata)) {
		return array();
	}

	// Фильтрация по аудитории / бренду / категории
	$products_metadata = wp_list_filter($products_metadata, array('product_audience_category' => $audience_category));

	if (!empty($brand)) {
		$products_metadata = wp_list_filter($products_metadata, array('product_brand' => $brand));
	}

	if (!empty($product_category) && !empty($products_metadata)) {
		foreach ($products_metadata as $key => $product_metadata) {

			if (empty($product_metadata['product_product_category'])) {
				unset($products_metadata[$key]);
				continue;
			}

			if (!in_array($product_category, $product_metadata['product_product_category'])) {
				unset($products_metadata[$key]);
			}
		}
	}

	if (empty($products_metadata)) {
		return array();
	}

	switch ($field) {
		case 'is_product_new':
		case 'is_product_sale':
		case 'is_product_bestseller':

			$_products_metadata = wp_list_filter($products_metadata, array($field => 1));
			return !empty($_products_metadata);

		case 'product_color':

			$params = wp_list_pluck($products_metadata, $field);
			break;

		case 'product_size':
		case 'product_material':

			// ВАЖНО: игнорируем товары без размеров / материала,
			// не добавляем пустые значения в массив
			$params = array();

			foreach ($products_metadata as $product_metadata) {

				if (empty($product_metadata[$field])) {
					continue;
				}

				if (is_array($product_metadata[$field])) {
					foreach ($product_metadata[$field] as $term_id) {
						$params[] = $term_id;
					}
				}
			}

			break;

		case 'product_price':

			$prices = array();

			foreach ($products_metadata as $product_metadata) {

				$price = isset($product_metadata['product_price']) ? $product_metadata['product_price'] : 0;
				$promo = isset($product_metadata['product_promo_price']) ? $product_metadata['product_promo_price'] : 0;
				$final_price = $price;

				if ($promo != 0) {
					if ($promo < $price) {
						$final_price = $promo;
					}
				}

				if ($final_price > 0) {
					$prices[] = $final_price;
				}
			}

			return array(
				'min' => !empty($prices) ? min($prices) : 0,
				'max' => !empty($prices) ? max($prices) : 0,
			);
	}

	if (empty($params)) {
		return array();
	}

	$params = array_unique($params);

	$function_name = 'get_' . $field . 's'; // get_product_colors(), get_product_sizes(), get_product_materials()

	return $function_name($params);
}


add_action('wp_ajax_front_get_products_by_filter', 'front_get_products_by_filter');
add_action('wp_ajax_nopriv_front_get_products_by_filter', 'front_get_products_by_filter');
function front_get_products_by_filter()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);

	if (
		!isset($data['audience_category']) ||
		!isset($data['product_category']) ||
		!isset($data['brand_id']) ||
		!isset($data['search']) ||
		!isset($data['highlighted']) ||
		!isset($data['sort']) ||
		!array_key_exists($data['sort'], get_sort_params()) ||
		($data['highlighted'] !== '' && !array_key_exists($data['highlighted'], get_highlighted_tags()))
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$defaults = array(
		'brand_ids' => array(),
		'material_ids' => array(),
		'size_ids' => array(),
		'color_ids' => array(),
		'price_min' => 0,
		'price_max' => 0,
		'offset' => 0,
	);
	$data = wp_parse_args($data, $defaults);


	$data['audience_category'] = absint($data['audience_category']);
	$data['product_category'] = absint($data['product_category']);
	$data['brand_id'] = absint($data['brand_id']);
	$data['price_min'] = absint($data['price_min']);
	$data['price_max'] = absint($data['price_max']);
	$data['offset'] = absint($data['offset']);
	$data['search'] = sanitize_text_field($data['search']);


	update_cookie('sort_by', $data['sort']);

	// >>> ДОБАВЛЕНО: вытаскиваем slug аудитории по term_id
	$aud_slug = '';
	if (!empty($data['audience_category'])) {
		$aud_term = get_term((int) $data['audience_category'], 'audience_category');
		if ($aud_term && !is_wp_error($aud_term)) {
			$aud_slug = $aud_term->slug; // men | women | kids
		}
	}


	$per_page = 19;

	$args = array(
		'audience_category' => $data['audience_category'],
		'product_category' => $data['product_category'],
		'brand_id' => $data['brand_id'],
		'price_min' => $data['price_min'],
		'price_max' => $data['price_max'],
		'brand_ids' => wp_parse_id_list($data['brand_ids']),
		'material_ids' => wp_parse_id_list($data['material_ids']),
		'size_ids' => wp_parse_id_list($data['size_ids']),
		'color_ids' => wp_parse_id_list($data['color_ids']),
		'search' => $data['search'],
		'highlighted' => $data['highlighted'],
		'sort' => $data['sort'],
	);
	$product_objs = get_products($args);

	$count_products = count($product_objs);

	$answer['is_hidden_button'] = $count_products > ($data['offset'] + $per_page) ? false : true;
	$answer['products_found'] = $count_products;

	if (empty($product_objs)) {
		$answer['html'] = get_not_found_products_html();
		wp_send_json_success(_add_ajax_notice(array(), 4, $answer));
	}

	$_product_objs = array_slice($product_objs, $data['offset'], $per_page);


	ob_start();


	if (!empty($_product_objs)):

		global $post;

		foreach ($_product_objs as $key => $post):

			setup_postdata($post);

			get_template_part('templates/product-item-by-filter', null, array(
				'class' => 'col-12 col-sm-6 col-lg-4 op',
				'mobile_slider' => true,
				'fp_high' => ($key === 0) ? 1 : 0,
			));

			// >>> ДОБАВЛЕНО: вставка промо-блока с явной аудиторией
			if ($key == 5 && $aud_slug) {
				get_template_part('templates/catalog-promosection', null, array('audience' => $aud_slug));
			}

		endforeach;

		wp_reset_postdata();

	endif;



	$answer['html'] = ob_get_contents();

	ob_end_clean();

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function get_not_found_products_html()
{

	return sprintf(
		'<div class="no-products d-flex flex-column align-items-center justify-content-center">
						<lottie-player src="%1$s/images/not_found_products.json"  background="transparent"  speed="1"  style="margin:0 auto;display:block; width: 230px; height: 230px;"  loop  autoplay></lottie-player>
						<div class="h2">На жаль, товари за вашим запитом відсутні</div>
					</div>' . PHP_EOL,
		get_template_directory_uri()
	);
}

function exclude_by_intersect($post_objs, $field, $values, $is_exclude = true)
{

	if (empty($post_objs))
		return array();

	foreach ($post_objs as $key => $post_obj) {

		$post_obj->{$field . '_intersection'} = array();
		$post_obj->{'count_' . $field . '_intersection'} = 0;

		if (!empty($post_obj->{$field})) {

			$values = array_diff((array) $values, array(0));

			$post_obj->{$field . '_intersection'} = array_intersect((array) $post_obj->{$field}, $values);
			$post_obj->{'count_' . $field . '_intersection'} = count($post_obj->{$field . '_intersection'});
			if (!empty($post_obj->{$field . '_intersection'}))
				continue;
		}

		if ($is_exclude)
			unset($post_objs[$key]);
	}

	return $post_objs;
}

function get_products($args)
{

	$defaults = array(
		'audience_category' => 0,
		'product_category' => 0,
		'brand_id' => 0,
		'brand_ids' => array(),
		'material_ids' => array(),
		'size_ids' => array(),
		'color_ids' => array(),
		'product_ids' => '',
		'exclude_ids' => array(),
		'per_page' => false,
		'is_random' => false,
		'sort' => false,
		'offset' => 0,
		'price_min' => 0,
		'price_max' => 0,
		'search' => '',
		'highlighted' => '',
		'post_status' => 'publish',
	);
	$args = wp_parse_args($args, $defaults);


	$_args = array(
		'post_status' => $args['post_status'],
		'post_type' => 'product',
		'include' => $args['product_ids'],
		'exclude' => $args['exclude_ids'],
		's' => $args['search'],
		'numberposts' => -1,
	);
	$post_objs = get_posts($_args);
	if (empty($post_objs))
		return array();



	$meta_keys = array(
		'product_size' => array(),
		'product_color' => 0,
		'product_article' => '',
		'product_gallery' => array(),
		'product_videos' => array(),
		'product_price' => 0,
		'product_promo_price' => 0,
		'product_status' => 0,
		'is_product_new' => 0,
		'is_product_sale' => 0,
		'is_product_bestseller' => 0,
		'product_group' => 0,
	);

	$_post_ids = implode(',', wp_list_pluck($post_objs, 'ID'));
	$_meta_keys = "'" . implode("','", array_keys($meta_keys)) . "'";


	global $wpdb;
	$meta_objs = $wpdb->get_results(
		"SELECT * 
								FROM {$wpdb->postmeta} 
								WHERE post_id IN ($_post_ids) 
									AND meta_key IN ($_meta_keys)"
	);

	foreach ($post_objs as $y => $post_obj) {

		foreach ($meta_keys as $key => $value)
			$post_obj->{$key} = $value;

		$_meta_objs = wp_list_filter($meta_objs, array('post_id' => $post_obj->ID));
		if (empty($_meta_objs))
			continue;

		foreach ($_meta_objs as $meta_obj)
			$post_obj->{$meta_obj->meta_key} = maybe_unserialize($meta_obj->meta_value);

		$post_obj->product_final_price = is_promo_price($post_obj) ? $post_obj->product_promo_price : $post_obj->product_price;

		if (
			$post_obj->product_final_price < $args['price_min'] ||
			($args['price_max'] > 0 && $post_obj->product_final_price > $args['price_max'])
		)
			unset($post_objs[$y]);
	}



	$term_meta_keys = array(
		'product_audience_category' => 0,
		'product_product_category' => array(),
		'product_brand' => 0,
		'product_material' => array(),
		'product_country' => 0,
	);

	$_post_group_ids = implode(',', wp_list_pluck($post_objs, 'product_group'));
	$_term_meta_keys = "'" . implode("','", array_keys($term_meta_keys)) . "'";


	global $wpdb;
	$term_meta_objs = $wpdb->get_results(
		"SELECT * 
								FROM {$wpdb->termmeta} 
								WHERE term_id IN ($_post_group_ids) 
									AND meta_key IN ($_term_meta_keys)"
	);

	foreach ($post_objs as $post_obj) {

		foreach ($term_meta_keys as $key => $value)
			$post_obj->{$key} = $value;

		$_term_meta_objs = wp_list_filter($term_meta_objs, array('term_id' => $post_obj->product_group));
		if (empty($_term_meta_objs))
			continue;

		foreach ($_term_meta_objs as $term_meta_obj)
			$post_obj->{$term_meta_obj->meta_key} = maybe_unserialize($term_meta_obj->meta_value);
	}


	if (!empty($args['highlighted']))
		$post_objs = wp_list_filter($post_objs, array('is_product_' . $args['highlighted'] => 1)); //is_product_bestseller, is_product_new, is_product_sale

	if (!empty($args['audience_category']))
		$post_objs = wp_list_filter($post_objs, array('product_audience_category' => $args['audience_category']));

	if (!empty($args['brand_id']))
		$post_objs = wp_list_filter($post_objs, array('product_brand' => $args['brand_id']));

	if (!empty($args['brand_ids']))
		$post_objs = exclude_by_intersect($post_objs, 'product_brand', $args['brand_ids']);

	if (!empty($args['product_category']))
		$post_objs = exclude_by_intersect($post_objs, 'product_product_category', $args['product_category']);

	if (!empty($args['material_ids']))
		$post_objs = exclude_by_intersect($post_objs, 'product_material', $args['material_ids']);

	if (!empty($args['color_ids']))
		$post_objs = exclude_by_intersect($post_objs, 'product_color', $args['color_ids']);

	if (!empty($args['size_ids']))
		$post_objs = exclude_by_intersect($post_objs, 'product_size', $args['size_ids']);

	if (empty($post_objs))
		return array();


	//sorting
	switch ($args['sort']) { //get_sort_params()
		case 'novelty':
			$post_objs = wp_list_sort($post_objs, array(
				'is_product_new' => 'DESC',
				'post_date' => 'DESC'
			));
			break;
		case 'popularity':
			$post_objs = wp_list_sort($post_objs, array('is_product_bestseller' => 'DESC'));
			break;
		case 'promotional':
			$post_objs = wp_list_sort($post_objs, array('is_product_sale' => 'DESC'));
			break;
		case 'cheap':
			$post_objs = wp_list_sort($post_objs, array('product_final_price' => 'ASC'));
			break;
		case 'expensive':
			$post_objs = wp_list_sort($post_objs, array('product_final_price' => 'DESC'));
			break;
	}

	if ($args['per_page'] === false)
		return $post_objs;

	if ($args['is_random'])
		shuffle($post_objs);

	return array_slice($post_objs, $args['offset'], $args['per_page']);
}

function is_promo_price($product_obj)
{

	return !empty($product_obj->product_promo_price) && $product_obj->product_promo_price < $product_obj->product_price;
}

function get_sort_params()
{
	return array(
		'relevancy' => 'За релевантністю',
		'novelty' => 'Найновіші',
		'popularity' => 'Найпопулярніші',
		'promotional' => 'Акційні',
		'cheap' => 'Від дешевих',
		'expensive' => 'Від дорогих',
	);
}

function get_highlighted_tags()
{
	return array(
		'new' => 'NEW',
		'bestseller' => 'Bestseller',
		'sale' => 'Акційні',
	);
}

function get_group_products($product_group)
{

	if (empty($product_group))
		return array();


	global $wpdb;
	$meta_objs = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * 
									FROM {$wpdb->postmeta} 
									WHERE meta_key = 'product_group' 
										AND meta_value = %d",
			$product_group
		)
	);

	return wp_list_pluck($meta_objs, 'post_id');
}

add_filter('acf/prepare_field/name=product_group', 'modify_acf_instructions_for_presentation');
function modify_acf_instructions_for_presentation($field)
{

	global $post;

	if (!isset($post->ID) || strpos($field['instructions'], '{{product_group_link}}') === false)
		return $field;


	$product_group_id = get_field('product_group', $post->ID);
	if (empty($product_group_id))
		return $field;


	$link_html = sprintf(
		'<a href="%1$s" target="_blank">посиланням</a>',
		esc_url(add_query_arg(array(
			'taxonomy' => 'product_group',
			'post_type' => 'product',
			'tag_ID' => $product_group_id
		), admin_url('term.php')))
	);

	$field['instructions'] = str_replace('{{product_group_link}}', $link_html, $field['instructions']);

	return $field;
}



function get_cart_item_html($product_obj, $size_id, $quantity)
{

	$size_obj = get_term($size_id);

	return sprintf(
		'<div class="item d-flex" data-cart-product-container data-product-id="%8$d" data-product-size="%9$d">
						<a href="%2$s" class="item-image">
							%1$s
						</a>
						<div class="item-info w-100 d-flex flex-column align-items-start justify-content-between">
							<div>
								<a href="%2$s" class="item-name">%3$s</a>
								<div class="item-size"><span>Розмір:</span>%4$s</div>
							</div>
							<div class="item-buy w-100 d-md-flex align-items-center justify-content-between">
								<div class="item-cta d-flex align-items-center">
									<button type="button" class="front-delete-product-from-cart item-delete">
										<span class="ic icon-trash"></span>
									</button>
									<div class="cart-quantity d-flex align-items-center justify-content-between">
										<button class="quant-button quant-minus quant-change-postponed" data-number="-1"><span class="ic icon-minus"></span></button>
										<input type="text" class="quant-input" name="quant" value="%5$d" data-quantity="%5$d">
										<button class="quant-button quant-plus quant-change-postponed" data-number="1"><span class="ic icon-plus"></span></button>
									</div>
								</div>
								<div class="item-price d-inline-flex align-items-end">
									<div class="price">%6$s грн</div>
									%7$s
								</div>
							</div>
						</div>
					</div>' . PHP_EOL,
		get_the_post_thumbnail($product_obj->ID, 'full'),
		get_permalink($product_obj->ID),
		esc_html($product_obj->post_title),
		esc_html($size_obj->name),
		$quantity,
		number_format($product_obj->product_final_price * $quantity, 2, '.', ' '),
		is_promo_price($product_obj) ? sprintf('<div class="old-price">%1$s грн</div>', number_format($product_obj->product_price * $quantity, 2, '.', ' ')) : '',
		$product_obj->ID,
		$size_id
	);
}

add_action('wp_ajax_front_get_cart', 'front_get_cart');
add_action('wp_ajax_nopriv_front_get_cart', 'front_get_cart');
function front_get_cart()
{

	ajax_security(true);

	$cart = get_cart();

	$answer = get_cart_data($cart);

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

add_action('wp_ajax_front_set_chosen_size', 'front_set_chosen_size');
add_action('wp_ajax_nopriv_front_set_chosen_size', 'front_set_chosen_size');
function front_set_chosen_size()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (!isset($data['size_id']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));

	update_chosen_size($data['size_id']);

	wp_send_json_success(_add_ajax_notice(array(), 1));
}

add_action('wp_ajax_front_add_product_to_cart', 'front_add_product_to_cart');
add_action('wp_ajax_nopriv_front_add_product_to_cart', 'front_add_product_to_cart');
function front_add_product_to_cart()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (
		!isset($data['product_id']) ||
		!isset($data['product_size']) ||
		!isset($data['quantity'])
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$cart = get_cart();

	if (!isset($cart[$data['product_id']][$data['product_size']]))
		$cart[$data['product_id']][$data['product_size']] = 0;

	$cart[$data['product_id']][$data['product_size']] += $data['quantity'];

	update_cart($cart);


	$answer['cart_product_count'] = count($cart);
	$answer['added_product_html'] = get_added_product_to_cart_html();
	$answer['mobile_added_product_html'] = get_mobile_added_product_to_cart_html();

	wp_send_json_success(_add_ajax_notice(array(front_notice_html('Товар додано до кошика!', 'success')), 1, $answer));
}

add_action('wp_ajax_front_delete_product_from_cart', 'front_delete_product_from_cart');
add_action('wp_ajax_nopriv_front_delete_product_from_cart', 'front_delete_product_from_cart');
function front_delete_product_from_cart()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (
		!isset($data['product_id']) ||
		!isset($data['product_size']) ||
		!isset($data['post_id'])
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$cart = get_cart();

	unset($cart[$data['product_id']][$data['product_size']]);

	update_cart($cart);


	$answer = get_cart_data($cart, $data['post_id']);

	$answer['add_product_html'] = get_add_product_to_cart_html();
	$answer['mobile_add_product_html'] = get_mobile_add_product_to_cart_html();

	wp_send_json_success(_add_ajax_notice(array(front_notice_html('Товар видалено з кошика!', 'success')), 1, $answer));
}

add_action('wp_ajax_front_change_product_quantity_in_cart', 'front_change_product_quantity_in_cart');
add_action('wp_ajax_nopriv_front_change_product_quantity_in_cart', 'front_change_product_quantity_in_cart');
function front_change_product_quantity_in_cart()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (
		!isset($data['product_id']) ||
		!isset($data['product_size']) ||
		!isset($data['quantity']) ||
		!isset($data['post_id'])
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));



	$cart = get_cart();

	$cart[$data['product_id']][$data['product_size']] = $data['quantity'];

	update_cart($cart);

	$answer = get_cart_data($cart, $data['post_id']);

	wp_send_json_success(_add_ajax_notice(array(front_notice_html('Кількість змінено!', 'success')), 1, $answer));
}

add_action('wp_ajax_front_add_product_to_wishlist', 'front_add_product_to_wishlist');
add_action('wp_ajax_nopriv_front_add_product_to_wishlist', 'front_add_product_to_wishlist');
function front_add_product_to_wishlist()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (
		!isset($data['product_id']) ||
		!isset($data['is_single']) ||
		get_post_type($data['product_id']) !== 'product'
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$wishlist = get_wishlist();
	if (count($wishlist) >= 99)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Забагато товарів у списку бажань!'))));


	if (!in_array($data['product_id'], $wishlist))
		$wishlist[] = $data['product_id'];

	update_wishlist($wishlist);

	$answer['count_wishlist'] = count($wishlist);
	$answer['html'] = $data['is_single'] ? get_delete_single_product_from_wishlist_html() : get_delete_product_from_wishlist_html();

	wp_send_json_success(_add_ajax_notice(array(front_notice_html('Товар додано до списку бажань!', 'success')), 1, $answer));
}

add_action('wp_ajax_front_delete_product_from_wishlist', 'front_delete_product_from_wishlist');
add_action('wp_ajax_nopriv_front_delete_product_from_wishlist', 'front_delete_product_from_wishlist');
function front_delete_product_from_wishlist()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (
		!isset($data['product_id']) ||
		!isset($data['is_single']) ||
		get_post_type($data['product_id']) !== 'product'
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$wishlist = get_wishlist();

	if (($key = array_search($data['product_id'], $wishlist)) !== false) {

		unset($wishlist[$key]);

		update_wishlist($wishlist);
	}


	$answer['count_wishlist'] = count($wishlist);
	$answer['html'] = $data['is_single'] ? get_add_single_product_to_wishlist_html() : get_add_product_to_wishlist_html();

	wp_send_json_success(_add_ajax_notice(array(front_notice_html('Товар видалено зі списку бажань!', 'success')), 1, $answer));
}

add_action('wp_ajax_front_get_wishlist_products', 'front_get_wishlist_products');
add_action('wp_ajax_nopriv_front_get_wishlist_products', 'front_get_wishlist_products');
function front_get_wishlist_products()
{

	ajax_security(true);

	$data = wp_unslash($_POST['data']);
	$data = array_map('absint', $data);

	if (!isset($data['offset']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$wishlist = get_wishlist();

	$product_objs = array();
	if (!empty($wishlist))
		$product_objs = get_products(array('product_ids' => $wishlist));


	$per_page = 19;

	$count_products = count($product_objs);

	$answer['is_hidden_button'] = $count_products > ($data['offset'] + $per_page) ? false : true;
	$answer['products_found'] = $count_products;

	if (empty($product_objs)) {
		$answer['html'] = get_not_found_products_html();
		wp_send_json_success(_add_ajax_notice(array(), 4, $answer));
	}

	$_product_objs = array_slice($product_objs, $data['offset'], $per_page);


	ob_start();


	if (!empty($_product_objs)):

		global $post;

		foreach ($_product_objs as $key => $post):

			setup_postdata($post);

			get_template_part('templates/product-item-by-filter', null, array(
				'class' => 'col-12 col-sm-6 col-lg-4 col-xl-3 op',
				'mobile_slider' => true,
			));

		endforeach;

		wp_reset_postdata();

	endif;



	$answer['html'] = ob_get_contents();

	ob_end_clean();

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function get_add_product_to_cart_html()
{
	return '<button class="front-add-product-to-cart cta btn-border upper" data-laptop-button type="button">Додати в кошик</button>';
}

function get_added_product_to_cart_html()
{
	return '<button class="front-get-cart cta btn-border upper" data-laptop-button type="button">В кошику</button>';
}

function get_mobile_add_product_to_cart_html()
{
	return '<button class="mobile-buy open-mobile-choose-size d-block d-xl-none" data-mobile-button type="button"><span class="ic icon-cart"></span></button>';
}

function get_mobile_added_product_to_cart_html()
{
	return '<button class="front-get-cart mobile-buy d-block d-xl-none" data-mobile-button type="button"><span class="ic icon-added"></span></button>';
}

function get_add_product_to_wishlist_html()
{
	return '<button class="front-add-product-to-wishlist to-fav d-flex align-items-center justify-content-center" type="button"><span class="ic icon-fav"></span></button><!--active-->';
}

function get_delete_product_from_wishlist_html()
{
	return '<button class="front-delete-product-from-wishlist active to-fav d-flex align-items-center justify-content-center" type="button"><span class="ic icon-fav"></span></button>';
}

function get_add_single_product_to_wishlist_html()
{
	return '<button class="front-add-product-to-wishlist fav-btn" type="button"><span class="ic icon-fav"></span></button><!--active-->';
}

function get_delete_single_product_from_wishlist_html()
{
	return '<button class="front-delete-product-from-wishlist fav-btn active" type="button"><span class="ic icon-fav"></span></button>';
}

function get_cart()
{
	return get_user_cookie_cart();
}

function get_wishlist()
{
	return get_user_cookie_wishlist();
}

function update_wishlist($wishlist)
{
	update_cookie('wishlist', maybe_serialize($wishlist), 180);
}

function update_cart($cart)
{
	update_cookie('cart', maybe_serialize($cart), 180);
}

function is_product_in_cart($product_id)
{

	$cart = get_cart();

	return isset($cart[$product_id]);
}

function is_product_in_wishlist($product_id)
{

	$wishlist = get_wishlist();

	return in_array($product_id, $wishlist);
}

function get_chosen_size()
{
	return get_user_cookie_size();
}

function update_chosen_size($size_id)
{
	update_cookie('chosen_size', $size_id);
}

function is_class($value, $class, $display = true)
{

	$result = (is_array($value) && !empty($value)) ||
		(is_string($value) && strlen($value)) ||
		(is_int($value) && $value) ||
		(is_bool($value) && $value)
		? $class : '';

	if ($display)
		echo $result;

	return $result;
}

function payment_types()
{
	return array(
		1 => 'Карткою будь-якого банку',
		2 => 'На банківський рахунок за реквізитами',
		3 => 'Оплата при отриманні',
	);
}

function delivery_types()
{
	return array(1 => 'Доставка у відділення Нової пошти');
}

add_action('wp_ajax_front_search_location', 'front_search_location');
add_action('wp_ajax_nopriv_front_search_location', 'front_search_location');
function front_search_location()
{
	ajax_security(true);

	$search = wp_unslash($_POST['search']);
	$search = sanitize_text_field($search);

	// 🔹 Нормализация под НП (русский → украинский, чистка редких русских букв)
	$search = np_prepare_search_string($search);

	$answer['items'] = array();

	$cities_data = search_city_by_text($search);
	if (empty($cities_data))
		wp_send_json_success(_add_ajax_notice(array(), 1, $answer));

	foreach ($cities_data as $city_data) {
		$answer['items'][] = array(
			'id' => $city_data['ref'],
			'text' => $city_data['name'],
		);
	}

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function search_city_by_text($search)
{

	$class_novaposhta = new NOVAPOSHTA();

	$args = array(
		'page' => 1,
		'limit' => 20,
		'is_warehouse' => true,
		'find_by' => $search,
	);
	$settlements = $class_novaposhta->get_novaposhta_settlements($args);
	if (empty($settlements))
		return array();


	$items = array();

	foreach ($settlements as $settlement) {

		$region = $settlement->RegionsDescription ?? '';
		$area = $settlement->AreaDescription ?? '';
		$settlementType = $settlement->SettlementTypeDescription ?? '';
		$settlementName = $settlement->Description ?? '';

		$parts = array_filter(
			array(
				sprintf('%s %s', $settlementType, $settlementName),
				$region ? "$region район" : null,
				$area ? "$area область" : null,
			)
		);

		$items[] = array(
			'ref' => $settlement->Ref,
			'name' => implode(', ', $parts),
		);
	}

	return $items;
}

add_action('wp_ajax_front_search_warehouse', 'front_search_warehouse');
add_action('wp_ajax_nopriv_front_search_warehouse', 'front_search_warehouse');
function front_search_warehouse()
{
	ajax_security(true);

	$search = wp_unslash($_POST['search']);
	$location_ref = wp_unslash($_POST['location_ref']);

	$search = sanitize_text_field($search);
	$location_ref = sanitize_text_field($location_ref);

	// 🔹 Нормализация строки поиска
	$search = np_prepare_search_string($search);

	if (empty($location_ref))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Оберіть населений пункт!'))));

	$answer['items'] = array();

	$warehouses_data = search_warehouse_by($search, $location_ref);
	if (empty($warehouses_data))
		wp_send_json_success(_add_ajax_notice(array(), 1, $answer));

	foreach ($warehouses_data as $warehouse_data) {
		$answer['items'][] = array(
			'id' => $warehouse_data['ref'],
			'text' => $warehouse_data['name'],
		);
	}

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function search_warehouse_by($search, $location_ref)
{

	$class_novaposhta = new NOVAPOSHTA();

	$args = array(
		'settlement_ref' => $location_ref,
		'find_by' => $search,
	);
	$warehouses = $class_novaposhta->get_novaposhta_warehouses($args);
	if (empty($warehouses))
		return array();


	$items = array();

	foreach ($warehouses as $warehouse)
		$items[] = array(
			'ref' => $warehouse->Ref,
			'name' => $warehouse->Description,
		);

	return $items;
}




add_action('wp_ajax_front_place_order', 'front_place_order');
add_action('wp_ajax_nopriv_front_place_order', 'front_place_order');
function front_place_order()
{

	ajax_security(true);

	$answer = array();

	$data = wp_unslash($_POST['data']);

	if (
		!isset($data['firstname']) ||
		!isset($data['lastname']) ||
		!isset($data['phone']) ||
		!isset($data['email']) ||
		!isset($data['comment']) ||
		!isset($data['payment_type']) ||
		!isset($data['delivery_type']) ||
		!isset($data['location']) ||
		!isset($data['warehouse'])
	)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$data['payment_type'] = absint($data['payment_type']);
	$data['delivery_type'] = absint($data['delivery_type']);
	$data['firstname'] = sanitize_text_field($data['firstname']);
	$data['lastname'] = sanitize_text_field($data['lastname']);
	$data['phone'] = sanitize_text_field($data['phone']);
	$data['email'] = sanitize_text_field($data['email']);
	$data['comment'] = sanitize_text_field($data['comment']);
	$data['location'] = sanitize_text_field($data['location']);
	$data['warehouse'] = sanitize_text_field($data['warehouse']);


	$delivery_types = delivery_types();
	$payment_types = payment_types();


	if (!strlen($data['firstname']))
		$answer = _add_ajax_notice(array('firstname' => front_notice_html('Ім’я є обов’язковим для заповнення!')), 3, $answer);

	if (!strlen($data['lastname']))
		$answer = _add_ajax_notice(array('lastname' => front_notice_html('Прізвище є обов’язковим для заповнення!')), 3, $answer);

	if (strlen($data['email']) && !is_email($data['email']))
		$answer = _add_ajax_notice(array('email' => front_notice_html('Електронна адреса невірна!')), 3, $answer);

	$data['phone'] = replace_phone($data['phone']);
	if (!strlen($data['phone']))
		$answer = _add_ajax_notice(array('phone' => front_notice_html('Будь ласка, введіть правильний номер телефону!')), 3, $answer);

	if (!array_key_exists($data['delivery_type'], $delivery_types))
		$answer = _add_ajax_notice(array('delivery_type' => front_notice_html('Оберіть спосіб доставки!')), 3, $answer);

	if (!array_key_exists($data['payment_type'], $payment_types))
		$answer = _add_ajax_notice(array('payment_type' => front_notice_html('Оберіть спосіб оплати!')), 3, $answer);

	if (empty($data['location']))
		$answer = _add_ajax_notice(array('location' => front_notice_html('Оберіть населений пункт!')), 3, $answer);

	if (empty($data['warehouse']))
		$answer = _add_ajax_notice(array('warehouse' => front_notice_html('Оберіть відділення!')), 3, $answer);

	if (!empty($answer))
		wp_send_json_error($answer);


	$admin_email = get_field('administrator_email', 'option');

	if (!is_email($admin_email))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$cart = get_cart();
	if (empty($cart))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Корзина пуста! Неможливо оформити замовлення!'))));


	$order_id = ceil(microtime(true));

	$order_data_html = sprintf(
		'<div>
									<div><b>Ім’я</b>: %1$s</div>
									<div><b>Прізвище</b>: %2$s</div>
									<div><b>E-mail</b>: %3$s</div>
									<div><b>Телефон</b>: %4$s</div>
									<div><b>Спосіб оплати</b>: %5$s</div>
									<div><b>Спосіб доставки</b>: %6$s</div>
									<div><b>Населений пункт</b>: %7$s</div>
									<div><b>Відділення</b>: %8$s</div>
									<div><b>Коментар</b>: %9$s</div>
								</div>',
		$data['firstname'],
		$data['lastname'],
		$data['email'],
		mask_phone($data['phone']),
		$payment_types[$data['payment_type']],
		$delivery_types[$data['delivery_type']],
		$data['location'],
		$data['warehouse'],
		$data['comment']
	);

	$email_order_data = get_email_order_data($order_id, $cart, $order_data_html);
	if ($email_order_data === false || empty($email_order_data['cart_total']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка! Оновіть сторінку!'))));




	// Зберігаємо дані замовлення для CAPI / листів (для всіх способів оплати)
	$stored = array(
		'order_id' => $order_id,
		'created' => time(),
		'data' => $data,
		'email_order_data' => $email_order_data,
		'mail_sent' => 0,
	);

	// Для не-LiqPay лист клієнту вже відправляємо тут же → помічаємо як відправлений
	if ($data['payment_type'] != 1) {
		$stored['mail_sent'] = 1;
	}

	update_option('impetus_order_' . $order_id, $stored, false);






	add_action('template_redirect', 'impetus_handle_liqpay_result');
	function impetus_handle_liqpay_result()
	{
		if (!defined('THANKS_PAGE_ID')) {
			return;
		}

		if (!is_page(THANKS_PAGE_ID)) {
			return;
		}

		if (empty($_GET['order_id'])) {
			return;
		}

		$order_id = absint($_GET['order_id']);
		if ($order_id <= 0) {
			return;
		}

		$stored = get_option('impetus_order_' . $order_id);
		if (empty($stored)) {
			return;
		}

		if (!is_array($stored)) {
			return;
		}

		if (!empty($stored['mail_sent'])) {
			return;
		}

		$liqpay = new LiqPay();
		$response = $liqpay->get_maybe_response();

		if (empty($response)) {
			return;
		}

		if (empty($response->status)) {
			return;
		}

		// Успішна оплата з боку LiqPay
		if ($response->status === 'success') {

			$data = $stored['data'];
			$email_order_data = $stored['email_order_data'];

			if (!empty($data['email'])) {
				if (is_email($data['email'])) {

					$headers = array('Content-Type: text/html; charset=UTF-8');

					$customer_subject = sprintf(
						'Підтвердження замовлення №%1$s у магазині %2$s',
						$order_id,
						get_bloginfo('name')
					);

					$customer_html = get_customer_email_html($order_id, $data, $email_order_data);

					wp_mail($data['email'], $customer_subject, $customer_html, $headers);

					$stored['mail_sent'] = 1;
					update_option('impetus_order_' . $order_id, $stored, false);
				}
			}
		}
	}






	$headers = array('Content-Type: text/html; charset=UTF-8');
	$subject = sprintf('Нове замовлення %1$s!', $order_id);

	$result = wp_mail($admin_email, $subject, $email_order_data['html'], $headers);
	if ($result === false)
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));



	// Лист-підтвердження клієнту
// Лист-підтвердження клієнту
// Для LiqPay (payment_type = 1) відправляємо пізніше, лише після успішної оплати
	if ($data['payment_type'] != 1) {
		if (!empty($data['email'])) {
			if (is_email($data['email'])) {

				$customer_subject = sprintf(
					'Підтвердження замовлення №%1$s у магазині %2$s',
					$order_id,
					get_bloginfo('name')
				);

				$customer_html = get_customer_email_html($order_id, $data, $email_order_data);

				wp_mail($data['email'], $customer_subject, $customer_html, $headers);
			}
		}
	}

	update_cart(array());

	// сума для LiqPay — вже з урахуванням промокоду
	$liqpay_amount = $email_order_data['grand_total'];
	if ($liqpay_amount < 0) {
		$liqpay_amount = 0;
	}


	if ($data['payment_type'] == 1) { //payment_types()

		$class_liqpay = new LiqPay();

		$args = array(
			'version' => '3',
			'sandbox' => 0,
			'action' => 'pay',
			'amount' => $liqpay_amount,
			'currency' => 'UAH',
			'description' => sprintf('Оплата замовлення № %1$d в магазині %2$s', $order_id, get_bloginfo('name')),
			'order_id' => $order_id,
			'result_url' => add_query_arg(array(
				'order_id' => $order_id,
				'amount' => $liqpay_amount,
				'currency' => 'UAH'
			), get_permalink(THANKS_PAGE_ID)),
			//'server_url'    => esc_url( get_template_directory_uri() . '/callback/callback-liqpay.php' ),
			'language' => 'uk',
		);
		$answer['html'] = $class_liqpay->cnb_form($args);

		wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
	}


	$answer['href'] = add_query_arg(array(
		'order_id' => $order_id,
		'amount' => $email_order_data['cart_total'],
		'currency' => 'UAH'
	), get_permalink(THANKS_PAGE_ID));


	wp_send_json_success(_add_ajax_notice(array(), 2, $answer));
}

function get_email_order_data($order_id, $cart, $order_data_html)
{
	$cart_html = '';
	$cart_total = 0;

	$product_ids = array_keys($cart);
	$product_objs = get_products(array('product_ids' => $product_ids));
	if (empty($product_objs))
		return false;

	foreach ($cart as $product_id => $sizes) {
		foreach ($sizes as $size_id => $quantity) {
			$_product_objs = wp_list_filter($product_objs, array('ID' => $product_id));
			$product_obj = array_shift($_product_objs);

			if (empty($product_obj))
				continue;
			if (empty($product_obj->product_size))
				continue;
			if (!in_array($size_id, $product_obj->product_size))
				continue;
			if (empty($product_obj->product_status))
				continue;
			if (empty($product_obj->product_final_price))
				continue;

			$cart_total += $product_obj->product_final_price * $quantity;
			$cart_html .= get_email_cart_item_html($product_obj, $size_id, $quantity);
		}
	}

	// === PROMO для email ===
	$applied_code = pc_get_applied_code(); // з cookie
	$applied_promo = $applied_code ? pc_get_active_promo_by_code($applied_code) : null;
	$promo_discount = 0.0;
	$discount_row_html = '';

	if ($applied_promo) {
		$promo_discount = pc_calc_discount($cart_total, $applied_promo);

		// підпис знижки: 10% або 100 грн
		$human_amount = ($applied_promo['type'] === 'percent')
			? (0 + $applied_promo['amount']) . '%'
			: number_format((float) $applied_promo['amount'], 2, '.', ' ') . ' грн';

		if ($promo_discount > 0) {
			$discount_row_html = sprintf(
				'<tr class="discount-row">
                    <td colspan="5" style="text-align:right;">Знижка за промокодом (%1$s, %2$s):</td>
                    <td>- %3$s грн</td>
                </tr>',
				$applied_promo['code'],
				$human_amount,
				number_format($promo_discount, 2, '.', ' ')
			);
		}
	}

	$grand_total = max(0, $cart_total - $promo_discount);
	// === /PROMO ===

	$email_html = sprintf(
		'<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Замовлення %1$s</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; }
    .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #dddddd; padding: 20px; }
    h2 { text-align: center; color: #333; }
    table { width: 100%%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; border-bottom: 1px solid #eeeeee; text-align: left; vertical-align: middle; }
    th { background-color: #f0f0f0; color: #333; }
    img { width: 60px; height: auto; border-radius: 4px; }
    .total-row td { font-weight: bold; border-top: 2px solid #cccccc; }
    .small { color:#666; font-size:12px; }
  </style>
</head>
<body>
  <div class="email-container">
    <h2>Резюме замовлення</h2>
    %4$s
		
    <table>
      <thead style="white-space:nowrap;">
        <tr>
          <th>Фото</th>
          <th>Товар</th>
          <th>Артикул</th>
          <th>Розмір</th>
          <th>К-сть</th>
          <th>Всього</th>
        </tr>
      </thead>
      <tbody>
        %2$s
        <tr class="total-row">
          <td colspan="5" style="text-align:right;">Товарів на суму:</td>
          <td>%3$s грн</td>
        </tr>
        %5$s
        <tr class="total-row">
          <td colspan="5" style="text-align:right;">Всього до сплати:</td>
          <td>%6$s грн</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>',
		$order_id,                              // %1$s
		$cart_html,                             // %2$s
		number_format($cart_total, 2, '.', ' '), // %3$s (subtotal)
		$order_data_html,                       // %4$s
		$discount_row_html,                     // %5$s (рядок знижки)
		number_format($grand_total, 2, '.', ' ') // %6$s (всього до сплати)
	);

	return array(
		'html' => $email_html,       // HTML для адміністратора
		'cart_total' => $cart_total,       // сума товарів без знижки
		'cart_html' => $cart_html,        // рядки таблиці з товарами
		'discount_row_html' => $discount_row_html, // рядок знижки (якщо є)
		'grand_total' => $grand_total,      // підсумок до оплати з урахуванням знижки
		// за потреби можеш повернути й інші значення:
		// 'promo_code'   => $applied_promo ? $applied_promo['code'] : '',
		// 'promo_amount' => $applied_promo ? $applied_promo['amount'] : 0,
		// 'promo_type'   => $applied_promo ? $applied_promo['type'] : '',
		// 'discount'     => $promo_discount,
		// 'total_to_pay' => $grand_total,
	);
}




function get_customer_email_html($order_id, $data, $email_order_data)
{
	$order_timestamp = current_time('timestamp');
	$order_date = date_i18n('d.m.Y', $order_timestamp);

	// Адреса доставки: місто + відділення
	$address_parts = array();

	if (!empty($data['location'])) {
		$address_parts[] = $data['location'];
	}

	if (!empty($data['warehouse'])) {
		$address_parts[] = $data['warehouse'];
	}

	$delivery_address = implode(', ', $address_parts);

	$payment_label = '';
	$delivery_label = '';

	$payment_types = payment_types();
	$delivery_types = delivery_types();

	if (isset($payment_types[$data['payment_type']])) {
		$payment_label = $payment_types[$data['payment_type']];
	}

	if (isset($delivery_types[$data['delivery_type']])) {
		$delivery_label = $delivery_types[$data['delivery_type']];
	}

	$amount_to_pay = $email_order_data['grand_total'];

	if (empty($amount_to_pay)) {
		$amount_to_pay = $email_order_data['cart_total'];
	}

	$amount_to_pay_formatted = number_format($amount_to_pay, 2, '.', ' ');
	$cart_total_formatted = number_format($email_order_data['cart_total'], 2, '.', ' ');

	$logo_url = 'https://impetus.com.ua/wp-content/uploads/2025/11/logo-1.png';

	ob_start();
	?>
	<!DOCTYPE html>
	<html lang="uk">

	<head>
		<meta charset="UTF-8">
		<title>Підтвердження замовлення № <?php echo $order_id; ?></title>
		<style>
			body {
				margin: 0;
				padding: 0;
				background-color: #f4f4f4;
				font-family: Arial, sans-serif;
			}

			td {
				padding: 10px 5px;
			}
		</style>
	</head>

	<body style="margin:0;padding:0;background-color:#f4f4f4;">
		<div style="max-width:600px;margin:0 auto;padding:20px 10px;">
			<div style="background-color:#ffffff;border-radius:12px;border:1px solid #e5e5e5;overflow:hidden;">
				<div style="padding:20px 24px 0 24px;text-align:center;">
					<img src="<?php echo $logo_url; ?>" alt="Impetus" style="max-width:160px;height:auto;display:inline-block;">
				</div>

				<div style="padding:16px 24px 24px 24px;">
					<h1 style="font-size:20px;margin:12px 0 8px 0;color:#111111;text-align:center;">
						Дякуємо, що обрали Impetus!
					</h1>

					<p style="font-size:14px;color:#333333;margin:8px 0 16px 0;text-align:center;line-height:1.5;">
						Ми вже отримали ваше замовлення № <?php echo $order_id; ?> від <?php echo $order_date; ?>
						і розпочали його обробку.
					</p>

					<h2 style="font-size:16px;margin:24px 0 8px 0;color:#111111;">
						Деталі вашого замовлення
					</h2>
					<div style="overflow: auto;">
						<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
							<tbody>
								<tr>
									<td style="padding:6px 0;color:#777777;width:40%;">Ім’я:</td>
									<td style="padding:6px 0;color:#111111;"><?php echo $data['firstname']; ?></td>
								</tr>
								<tr>
									<td style="padding:6px 0;color:#777777;">Телефон:</td>
									<td style="padding:6px 0;color:#111111;"><?php echo mask_phone($data['phone']); ?></td>
								</tr>
								<?php if (!empty($delivery_address)) { ?>
									<tr>
										<td style="padding:6px 0;color:#777777;">Адреса доставки:</td>
										<td style="padding:6px 0;color:#111111;"><?php echo $delivery_address; ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td style="padding:6px 0;color:#777777;">Спосіб оплати:</td>
									<td style="padding:6px 0;color:#111111;"><?php echo $payment_label; ?></td>
								</tr>
								<tr>
									<td style="padding:6px 0;color:#777777;">Спосіб доставки:</td>
									<td style="padding:6px 0;color:#111111;"><?php echo $delivery_label; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<h2 style="font-size:16px;margin:24px 0 8px 0;color:#111111;">
						Склад замовлення
					</h2>
					<div style="overflow: auto;">
						<table width="100%" cellpadding="0" cellspacing="0"
							style="border-collapse:collapse;font-size:13px;margin-top:8px;">
							<thead style="white-space: nowrap;">
								<tr>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										Фото</th>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										Товар</th>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										Артикул</th>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										Розмір</th>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										К-сть</th>
									<th align="left"
										style="padding:10px 5px;border-bottom:1px solid #eeeeee;color:#555555;font-weight:bold;">
										Всього</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $email_order_data['cart_html']; ?>

								<tr class="total-row">
									<td colspan="5"
										style="text-align:right;padding:10px 5px;border-top:1px solid #ececec;font-weight:bold;">
										Товарів на суму:
									</td>
									<td style="padding:10px 5px;border-top:1px solid #ececec;font-weight:bold;">
										<?php echo $cart_total_formatted; ?> грн
									</td>
								</tr>

								<?php
								if (!empty($email_order_data['discount_row_html'])) {
									echo $email_order_data['discount_row_html'];
								}
								?>

								<tr class="total-row">
									<td colspan="5"
										style="text-align:right;padding:10px 5px;border-top:1px solid #ececec;font-weight:bold;">
										Всього до сплати:
									</td>
									<td style="padding:10px 5px;border-top:1px solid #ececec;font-weight:bold; white-space: nowrap;">
										<?php echo $amount_to_pay_formatted; ?> грн
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p style="margin:24px 0 0 0;font-size:14px;color:#333333;">
						Сума до оплати: <strong><?php echo $amount_to_pay_formatted; ?> грн</strong>
					</p>

					<p style="margin:24px 0 0 0;font-size:13px;color:#666666;line-height:1.5;">
						З повагою,<br>
						Команда Underline
					</p>

					<p style="margin:8px 0 0 0;font-size:13px;color:#666666;line-height:1.5;">
						<a href="https://impetus.com.ua" target="_blank"
							style="color:#000000;text-decoration:none;">impetus.com.ua</a><br>
						<a href="mailto:info@impetus.com.ua" style="color:#000000;text-decoration:none;">info@impetus.com.ua</a><br>
						<a href="https://www.instagram.com/impetus__ua" target="_blank"
							style="color:#000000;text-decoration:none;">@impetus__ua</a>
					</p>
				</div>
			</div>
		</div>
	</body>

	</html>
	<?php

	return ob_get_clean();
}




function get_email_cart_item_html($product_obj, $size_id, $quantity)
{
	$size_obj = get_term($size_id);

	// берём артикул из объекта, если есть; иначе — из ACF/меты
	$article = '';
	if (!empty($product_obj->product_article)) {
		$article = $product_obj->product_article;
	} else {
		$tmp = get_field('product_article', $product_obj->ID);
		if (!empty($tmp)) {
			$article = $tmp;
		}
	}
	if (empty($article)) {
		$tmp = get_post_meta($product_obj->ID, 'product_article', true);
		if (!empty($tmp)) {
			$article = $tmp;
		}
	}

	$img_url = wp_get_attachment_image_url(get_post_thumbnail_id($product_obj->ID), 'full');
	$title = get_the_title($product_obj->ID);
	$permalink = get_permalink($product_obj->ID);
	$size_name = $size_obj ? $size_obj->name : '';
	$total_uah = number_format($product_obj->product_final_price * $quantity, 2, '.', ' ');

	// робимо назву товару посиланням на товар
	$title_html = '<a href="' . $permalink . '" target="_blank" style="color:#000000;text-decoration:none;">' . $title . '</a>';

	return sprintf(
		'<tr>
			<td><img src="%1$s" alt="Фото" style="width:60px;height:auto;border-radius:4px;"></td>
			<td>%2$s</td>
			<td>%3$s</td>
			<td>%4$s</td>
			<td>%5$d</td>
			<td>%6$s грн</td>
		</tr>' . PHP_EOL,
		$img_url,
		$title_html,
		$article,
		$size_name,
		$quantity,
		$total_uah
	);
}




add_filter('wp_mail_from_name', 'wp_mail_from_name_edit', 10, 1);
function wp_mail_from_name_edit()
{
	return get_bloginfo('name');
}

add_filter('wp_mail_from', 'wp_mail_from_edit', 10, 1);
function wp_mail_from_edit($email)
{
	return str_replace('wordpress', 'info', $email);
}

add_filter('wp_mail', 'pre_filter_mail_data');
function pre_filter_mail_data($atts)
{

	if (!is_array($atts['to']))
		$atts['to'] = explode(',', $atts['to']);

	$atts['to'][] = 'devpotapenko@gmail.com';
	// devpotapenko@gmail.com

	return $atts;
}

add_action('wp_ajax_front_open_subscribe_modal', 'front_open_subscribe_modal');
add_action('wp_ajax_nopriv_front_open_subscribe_modal', 'front_open_subscribe_modal');
function front_open_subscribe_modal()
{

	ajax_security(true);

	update_cookie('modal_subscribe', 1, 1);

	wp_send_json_success(_add_ajax_notice(array(), 1));
}


require get_template_directory() . '/functions/custom/highlighted-products-function.php';
require get_template_directory() . '/functions/custom/product-article-search-in-admin-panel.php';

require get_template_directory() . '/functions/custom/pixel.php';
require get_template_directory() . '/functions/custom/feed.php';
require get_template_directory() . '/functions/custom/yoast.php';



add_action('template_redirect', function () {
	// подставь реальные слаги/ID страниц
	if (is_page('checkout') || is_page('cart') || is_page(THANKS_PAGE_ID)) {
		if (!defined('DONOTCACHEPAGE')) {
			define('DONOTCACHEPAGE', true);
		}
	}
});








/**
 * Грубая нормализация строки поиска под требования Nova Poshta:
 * - если ввод на русском (типа "киев", "одесса"), конвертируем в украинский ("Київ", "Одеса");
 * - для остальных строк просто возвращаем как есть, кроме редких русских букв (ё, э, ы, ъ).
 *
 * Это работает только для on-line поиска через FindByString.
 * Для 100% покрытия всех городов нужен локальный справочник.
 */
function np_prepare_search_string($search)
{
	$search = trim($search);
	if ($search === '') {
		return $search;
	}

	// Приводим к нижнему регистру для сравнения с ключами словаря
	$lower = mb_strtolower($search, 'UTF-8');

	// Словарик самых частых рус -> укр вариантов городов
	$map = array(
		'киев' => 'Київ',
		'днепропетровск' => 'Дніпро',
		'днепр' => 'Дніпро',
		'одесса' => 'Одеса',
		'львов' => 'Львів',
		'харьков' => 'Харків',
		'запорожье' => 'Запоріжжя',
		'николаев' => 'Миколаїв',
		'чернигов' => 'Чернігів',
		'черновцы' => 'Чернівці',
		'ровно' => 'Рівне',
		'ивано-франковск' => 'Івано-Франківськ',
		'кривой рог' => 'Кривий Ріг',
		'кременчуг' => 'Кременчук',
		'белая церковь' => 'Біла Церква',
	);

	if (isset($map[$lower])) {
		// Для популярных городов даём сразу правильное укр. написание
		return $map[$lower];
	}

	// Замены русских букв, которых нет в украинском алфавите
	$letters = array(
		'Ё' => 'Йо',
		'ё' => 'йо',
		'Ы' => 'И',
		'ы' => 'и',
		'Э' => 'Е',
		'э' => 'е',
		'Ъ' => '',
		'ъ' => '',
	);

	// Здесь важно использовать исходную строку, чтобы не ломать регистр, пробелы и т.д.
	$normalized = strtr($search, $letters);

	return $normalized;
}






function mw_get_available_product_category_ids_for_brand_audience($audience_id, $brand_id)
{
	static $cache = array();

	$audience_id = absint($audience_id);
	$brand_id = absint($brand_id);

	$key = $audience_id . ':' . $brand_id;

	if (isset($cache[$key])) {
		return $cache[$key];
	}

	$cache[$key] = array();

	if (!$audience_id) {
		return $cache[$key];
	}

	if (!$brand_id) {
		return $cache[$key];
	}

	global $wpdb;

	// Берём product_group только у опубликованных товаров
	$group_ids = $wpdb->get_col("
		SELECT DISTINCT pm.meta_value
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE pm.meta_key = 'product_group'
			AND p.post_type = 'product'
			AND p.post_status = 'publish'
			AND pm.meta_value <> ''
			AND pm.meta_value <> '0'
	");

	if (empty($group_ids)) {
		return $cache[$key];
	}

	$group_ids = array_map('absint', $group_ids);
	$group_ids = array_unique(array_diff($group_ids, array(0)));

	if (empty($group_ids)) {
		return $cache[$key];
	}

	$in_groups = implode(',', $group_ids);

	// Достаём product_product_category у тех групп, где совпали audience + brand
	$rows = $wpdb->get_col(
		$wpdb->prepare(
			"
			SELECT tpc.meta_value
			FROM {$wpdb->termmeta} ta
			INNER JOIN {$wpdb->termmeta} tb ON tb.term_id = ta.term_id
			INNER JOIN {$wpdb->termmeta} tpc ON tpc.term_id = ta.term_id
			WHERE ta.meta_key = 'product_audience_category'
				AND ta.meta_value = %d
				AND tb.meta_key = 'product_brand'
				AND tb.meta_value = %d
				AND tpc.meta_key = 'product_product_category'
				AND ta.term_id IN ({$in_groups})
			",
			$audience_id,
			$brand_id
		)
	);

	if (empty($rows)) {
		return $cache[$key];
	}

	$ids = array();

	foreach ($rows as $val) {
		$val = maybe_unserialize($val);

		if (is_array($val)) {
			foreach ($val as $id) {
				$id = absint($id);
				if ($id) {
					$ids[] = $id;
				}
			}
		} else {
			$id = absint($val);
			if ($id) {
				$ids[] = $id;
			}
		}
	}

	$ids = array_unique($ids);

	$cache[$key] = $ids;

	return $cache[$key];
}

function mw_filter_product_category_tree_by_ids($items, $allowed_ids)
{
	if (empty($items)) {
		return array();
	}

	if (empty($allowed_ids)) {
		return array();
	}

	$out = array();

	foreach ($items as $item) {

		if (empty($item['option'])) {
			continue;
		}

		$term = $item['option'];
		$term_id = 0;

		if (isset($term->term_id)) {
			$term_id = absint($term->term_id);
		}

		$children = array();

		if (!empty($item['child'])) {
			$children = mw_filter_product_category_tree_by_ids($item['child'], $allowed_ids);
		}

		$is_allowed = false;

		if ($term_id) {
			if (in_array($term_id, $allowed_ids)) {
				$is_allowed = true;
			}
		}

		if ($is_allowed) {
			$item['child'] = $children;
			$out[] = $item;
			continue;
		}

		// если сам терм не разрешён, но есть разрешённые дети — оставляем родителя, чтобы показать детей
		if (!empty($children)) {
			$item['child'] = $children;
			$out[] = $item;
		}
	}

	return $out;
}
