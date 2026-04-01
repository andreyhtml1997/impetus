<?php

if (!defined('ABSPATH'))
	exit;

define('PRODUCT_PER_PAGE', 12);


require get_template_directory() . '/functions/custom/promocode.php';
require get_template_directory() . '/functions/custom/esputnik-abandoned-cart.php';



require get_template_directory() . '/functions/cleanup.php';
require get_template_directory() . '/functions/setup.php';
require get_template_directory() . '/functions/enqueues.php';
require get_template_directory() . '/functions/custom/acf-json.php';
require get_template_directory() . '/functions/custom/filter.php';
require get_template_directory() . '/functions/custom/sitemap.php';


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

function webpImage($source, $destination = false, $quality = 40, $removeOld = false)
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






function get_user_cookie_modal_subscribe()
{

	return isset($_COOKIE['modal_subscribe']) ? $_COOKIE['modal_subscribe'] : 0;
}

function get_user_cookie_size()
{
	return isset($_COOKIE['chosen_size']) ? (int) $_COOKIE['chosen_size'] : 0;
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


function get_audience_category_id_by_user_cookie_audience_category()
{

	$audience_category = get_user_cookie_audience_category();

	$audience_category_obj = get_term_by('slug', $audience_category, 'audience_category');

	return isset($audience_category_obj->term_id) ? $audience_category_obj->term_id : WOMEN_AUDIENCE_CATEGORY_TERM_ID;
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

	$show_audience_before_category = true;

	if ($product_category_obj && !empty($product_category_obj->term_id)) {
		$show_flag = get_field('show_audience_before_category_on_filter_pages', 'product_category_' . (int) $product_category_obj->term_id);
		if ($show_flag !== null && $show_flag !== '') {
			$show_audience_before_category = (bool) $show_flag;
		}
	}

	if (!$show_audience_before_category && $product_category_obj) {
		$title = '';
	}

	if ($product_category_obj)
		$title .= ($title !== '' ? ' - ' : '') . $product_category_obj->name;

	if ($brand_obj)
		$title .= ($title !== '' ? ' - ' : '') . $brand_obj->name;

	return $title;
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

	$data['is_single'] = isset($data['is_single']) ? absint($data['is_single']) : 0;

	if (
		!isset($data['product_id']) ||
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

	$data['is_single'] = isset($data['is_single']) ? absint($data['is_single']) : 0;

	if (
		!isset($data['product_id']) ||
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


	$count_products = count($product_objs);

	$answer['is_hidden_button'] = $count_products > ($data['offset'] + PRODUCT_PER_PAGE) ? false : true;
	$answer['products_found'] = $count_products;

	if (empty($product_objs)) {
		$answer['html'] = get_not_found_products_html();
		wp_send_json_success(_add_ajax_notice(array(), 4, $answer));
	}

	$_product_objs = array_slice($product_objs, $data['offset'], PRODUCT_PER_PAGE);


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

function get_not_in_stock_product_html()
{
	return '<button class="front-add-product-to-cart cta btn-border upper disabled" data-laptop-button type="button" disabled>Немає в наявності</button>';
}


function get_added_product_to_cart_html()
{
	return '<button class="front-get-cart cta btn-border upper" data-laptop-button type="button">В кошику</button>';
}

function get_mobile_add_product_to_cart_html()
{
	return '<button class="mobile-buy open-mobile-choose-size d-block d-xl-none" data-mobile-button type="button"><span class="ic icon-cart"></span></button>';
}
function get_mobile_not_in_stock_product_html()
{
	return '<button class="mobile-buy d-block d-xl-none disabled" data-mobile-button type="button" disabled>немає в наявності</button>';
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

	if (function_exists('impetus_es_ac_on_cart_update')) {
		impetus_es_ac_on_cart_update($cart);
	}
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


add_action('wp_ajax_front_get_wishlist_count', 'front_get_wishlist_count');
add_action('wp_ajax_nopriv_front_get_wishlist_count', 'front_get_wishlist_count');

function front_get_wishlist_count()
{
	ajax_security(true);

	$wishlist = get_wishlist();

	$answer = array(
		'count_wishlist' => is_array($wishlist) ? count($wishlist) : 0,
	);

	wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function get_chosen_size()
{
	$size_param = '';

	if (!empty($_GET['size'])) {
		$size_param = wp_unslash($_GET['size']);
	} elseif (!empty($_GET['product_size'])) {
		$size_param = wp_unslash($_GET['product_size']);
	}

	if ($size_param) {
		$size_slug = sanitize_title($size_param);
		$size_term = get_term_by('slug', $size_slug, 'size');

		if (!$size_term && is_numeric($size_slug)) {
			$size_term = get_term((int) $size_slug, 'size');
		}

		if ($size_term && !is_wp_error($size_term)) {
			return (int) $size_term->term_id;
		}
	}

	return (int) get_user_cookie_size();
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
	return array(
		1 => 'Доставка у відділення Нової пошти',
		2 => 'Доставка у поштомат Нової пошти',
		3 => 'Адресна доставка курʼєром',
	);
}
function np_clean_search_term($term)
{
	$term = wp_unslash($term);
	$term = sanitize_text_field($term);

	// убираем ВСЕ пробельные символы
	$term = preg_replace('/\s+/u', '', $term);

	if (empty($term))
		return '';

	// твоя нормализация под НП
	$term = np_prepare_search_string($term);

	return $term;
}


function np_get_postomat_type_ref()
{
	$cached = get_transient('np_type_ref_postomat');
	if (!empty($cached))
		return $cached;

	$class_novaposhta = new NOVAPOSHTA();
	$types = $class_novaposhta->get_novaposhta_warehouse_types();

	if (empty($types))
		return '';

	foreach ($types as $type) {

		$ref = $type->Ref ?? '';
		$desc = $type->Description ?? '';

		if (empty($ref))
			continue;

		if (empty($desc))
			continue;

		$pos = mb_stripos($desc, 'поштомат');
		if ($pos === false)
			continue;

		set_transient('np_type_ref_postomat', $ref, 7 * DAY_IN_SECONDS);
		return $ref;
	}

	return '';
}



add_action('wp_ajax_front_search_location', 'front_search_location');
add_action('wp_ajax_nopriv_front_search_location', 'front_search_location');
function front_search_location()
{
	ajax_security(true);

	$delivery_type = 0;
	if (isset($_POST['delivery_type']))
		$delivery_type = absint(wp_unslash($_POST['delivery_type']));

	$search = '';
	if (isset($_POST['search']))
		$search = np_clean_search_term($_POST['search']);

	$answer = array();
	$answer['items'] = array();

	/* 	if (empty($search))
			wp_send_json_success(_add_ajax_notice(array(), 1, $answer)); */

	// 1/2 = відділення/поштомат => только settlements с отделениями
	// 3 = адресная доставка => settlements без ограничения по наличию отделений
	$is_warehouse = true;
	if ($delivery_type === 3)
		$is_warehouse = false;

	$cities_data = search_city_by_text($search, $is_warehouse);

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

function search_city_by_text($search, $is_warehouse = true)
{
	$class_novaposhta = new NOVAPOSHTA();

	$args = array(
		'page' => 1,
		'limit' => 100,
	);

	if ($is_warehouse)
		$args['is_warehouse'] = true;

	if (!empty($search))
		$args['find_by'] = $search;

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

	$search = '';
	if (isset($_POST['search']))
		$search = np_clean_search_term($_POST['search']);

	$location_ref = '';
	if (isset($_POST['location_ref']))
		$location_ref = sanitize_text_field(wp_unslash($_POST['location_ref']));

	$delivery_type = 1;
	if (isset($_POST['delivery_type']))
		$delivery_type = absint(wp_unslash($_POST['delivery_type']));

	if (empty($location_ref))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Оберіть населений пункт!'))));

	$answer = array();
	$answer['items'] = array();

	$warehouses_data = search_warehouse_by($search, $location_ref, $delivery_type);

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


function np_is_postomat_name($name)
{
	if (empty($name))
		return false;

	// чтобы ловило и "Поштомат", и "ПОШТОМАТ"
	$pos = mb_stripos($name, 'поштомат');

	if ($pos === false)
		return false;

	return true;
}

function search_warehouse_by($search, $location_ref, $delivery_type = 1)
{
	$class_novaposhta = new NOVAPOSHTA();

	$args = array(
		'page' => 1,
		'limit' => 500,
		'settlement_ref' => $location_ref,
	);

	// 2 = поштомат
	if ($delivery_type === 2) {

		$type_ref = np_get_postomat_type_ref();

		if (!empty($type_ref)) {
			$args['type_ref'] = $type_ref;
		}

		// fallback, если вдруг type_ref не нашли
		if (empty($type_ref)) {
			if (empty($search))
				$search = 'поштомат';
		}
	}

	if (!empty($search))
		$args['find_by'] = $search;

	$warehouses = $class_novaposhta->get_novaposhta_warehouses($args);
	if (empty($warehouses))
		return array();

	$items = array();

	foreach ($warehouses as $warehouse) {

		$ref = $warehouse->Ref ?? '';
		$name = $warehouse->Description ?? '';

		if (empty($ref))
			continue;

		// на всякий пожарный, если сработал fallback (без type_ref)
		if ($delivery_type === 2) {
			$pos = mb_stripos($name, 'поштомат');
			if ($pos === false)
				continue;
		}

		if ($delivery_type === 1) {
			$pos = mb_stripos($name, 'поштомат');
			if ($pos !== false)
				continue;
		}

		$items[] = array(
			'ref' => $ref,
			'name' => $name,
		);
	}

	return $items;
}






add_action('wp_ajax_front_place_order', 'front_place_order');
add_action('wp_ajax_nopriv_front_place_order', 'front_place_order');
function front_place_order()
{

	ajax_security(true);

	$answer = array();

	$data = wp_unslash($_POST['data']);

	if (!isset($data['firstname']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['lastname']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['phone']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['email']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['comment']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['payment_type']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['delivery_type']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	if (!isset($data['location']))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));

	$delivery_type_tmp = absint($data['delivery_type']);

	if ($delivery_type_tmp === 3) {
		if (!isset($data['address_full']))
			$data['address_full'] = '';
	} else {
		if (!isset($data['warehouse']))
			wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
	}

	if (!isset($data['location_text']))
		$data['location_text'] = '';
	if (!isset($data['warehouse_text']))
		$data['warehouse_text'] = '';
	if (!isset($data['address_express']))
		$data['address_express'] = 0;


	$data['payment_type'] = absint($data['payment_type']);
	$data['delivery_type'] = absint($data['delivery_type']);
	$data['firstname'] = sanitize_text_field($data['firstname']);
	$data['lastname'] = sanitize_text_field($data['lastname']);
	$data['phone'] = sanitize_text_field($data['phone']);
	$data['email'] = sanitize_text_field($data['email']);
	$data['comment'] = sanitize_text_field($data['comment']);
	$data['location'] = sanitize_text_field($data['location']);

	if (!isset($data['warehouse']))
		$data['warehouse'] = '';

	$data['warehouse'] = sanitize_text_field($data['warehouse']);

	$data['location_text'] = sanitize_text_field($data['location_text']);
	$data['warehouse_text'] = sanitize_text_field($data['warehouse_text']);

	if (!isset($data['address_full']))
		$data['address_full'] = '';

	$data['address_full'] = sanitize_text_field($data['address_full']);
	$data['address_express'] = absint($data['address_express']);



	$delivery_types = delivery_types();
	$payment_types = payment_types();


	if (!strlen($data['firstname']))
		$answer = _add_ajax_notice(array('firstname' => front_notice_html('Ім’я є обов’язковим для заповнення!')), 3, $answer);

	if (!strlen($data['lastname']))
		$answer = _add_ajax_notice(array('lastname' => front_notice_html('Прізвище є обов’язковим для заповнення!')), 3, $answer);

	/* if (strlen($data['email']) && !is_email($data['email']))
		$answer = _add_ajax_notice(array('email' => front_notice_html('Електронна адреса невірна!')), 3, $answer); */
	if (!strlen($data['email'])) {
		$answer = _add_ajax_notice(array('email' => front_notice_html('Email є обов’язковим для заповнення!')), 3, $answer);
	} else {
		if (!is_email($data['email'])) {
			$answer = _add_ajax_notice(array('email' => front_notice_html('Електронна адреса невірна!')), 3, $answer);
		}
	}

	$data['phone'] = replace_phone($data['phone']);
	if (!strlen($data['phone']))
		$answer = _add_ajax_notice(array('phone' => front_notice_html('Будь ласка, введіть правильний номер телефону!')), 3, $answer);

	if (!array_key_exists($data['delivery_type'], $delivery_types))
		$answer = _add_ajax_notice(array('delivery_type' => front_notice_html('Оберіть спосіб доставки!')), 3, $answer);

	if (!array_key_exists($data['payment_type'], $payment_types))
		$answer = _add_ajax_notice(array('payment_type' => front_notice_html('Оберіть спосіб оплати!')), 3, $answer);

	if (empty($data['location']))
		$answer = _add_ajax_notice(array('location' => front_notice_html('Оберіть населений пункт!')), 3, $answer);

	if ($data['delivery_type'] === 1 || $data['delivery_type'] === 2) {
		if (empty($data['warehouse'])) {
			$txt = 'Оберіть відділення!';
			if ($data['delivery_type'] === 2) {
				$txt = 'Оберіть поштомат!';
			}
			$answer = _add_ajax_notice(array('warehouse' => front_notice_html($txt)), 3, $answer);
		}
	}

	if ($data['delivery_type'] === 3) {
		if (empty($data['address_full'])) {
			$answer = _add_ajax_notice(array('address_full' => front_notice_html('Вкажіть адресу доставки!')), 3, $answer);
		}
	}


	if (!empty($answer))
		wp_send_json_error($answer);


	$admin_email = get_field('administrator_email', 'option');

	if (!is_email($admin_email))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));


	$cart = get_cart();
	if (empty($cart))
		wp_send_json_error(_add_ajax_notice(array(front_notice_html('Корзина пуста! Неможливо оформити замовлення!'))));


	$order_id = ceil(microtime(true));

	$location_for_email = !empty($data['location_text']) ? $data['location_text'] : $data['location'];
	$warehouse_for_email = !empty($data['warehouse_text']) ? $data['warehouse_text'] : $data['warehouse'];

	$delivery_row_title = 'Відділення';
	$delivery_row_value = $warehouse_for_email;
	$express_row_html = '';

	if ($data['delivery_type'] === 2) {
		$delivery_row_title = 'Поштомат';
	}

	if ($data['delivery_type'] === 3) {
		$delivery_row_title = 'Адреса';
		$delivery_row_value = $data['address_full'];

		if (!empty($data['address_express'])) {
			$express_row_html = '<div><b>Експрес доставка</b>: Так</div>';
		}
	}

	$order_data_html = sprintf(
		'<div>
		<div><b>Ім’я</b>: %1$s</div>
		<div><b>Прізвище</b>: %2$s</div>
		<div><b>E-mail</b>: %3$s</div>
		<div><b>Телефон</b>: %4$s</div>
		<div><b>Спосіб оплати</b>: %5$s</div>
		<div><b>Спосіб доставки</b>: %6$s</div>
		<div><b>Населений пункт</b>: %7$s</div>
		<div><b>%8$s</b>: %9$s</div>
		%10$s
		<div><b>Коментар</b>: %11$s</div>
	</div>',
		$data['firstname'],
		$data['lastname'],
		$data['email'],
		mask_phone($data['phone']),
		$payment_types[$data['payment_type']],
		$delivery_types[$data['delivery_type']],
		$location_for_email,
		$delivery_row_title,
		$delivery_row_value,
		$express_row_html,
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
	$items = array();

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






			$article = '';
			if (!empty($product_obj->product_article)) {
				$article = (string) $product_obj->product_article;
			} else {
				$tmp = get_post_meta($product_obj->ID, 'product_article', true);
				if (!empty($tmp)) {
					$article = (string) $tmp;
				}
			}

			$size_name = '';
			$size_obj = get_term($size_id);
			if (!empty($size_obj)) {
				if (!is_wp_error($size_obj)) {
					$size_name = (string) $size_obj->name;
				}
			}

			$item_id = $article;
			if (empty($item_id)) {
				$item_id = (string) $product_obj->ID;
			}

			$items[] = array_filter(array(
				'item_id' => $item_id,
				'item_name' => (string) get_the_title($product_obj->ID),
				'price' => (float) $product_obj->product_final_price,
				'quantity' => (int) $quantity,
				'item_variant' => $size_name,
			));







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
		'items' => $items,
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

	$location_for_email = '';
	$warehouse_for_email = '';

	if (!empty($data['location_text'])) {
		$location_for_email = $data['location_text'];
	} else {
		if (!empty($data['location'])) {
			$location_for_email = $data['location'];
		}
	}

	if (!empty($data['warehouse_text'])) {
		$warehouse_for_email = $data['warehouse_text'];
	} else {
		if (!empty($data['warehouse'])) {
			$warehouse_for_email = $data['warehouse'];
		}
	}

	$address_parts = array();

	if (!empty($location_for_email)) {
		$address_parts[] = $location_for_email;
	}

	if (absint($data['delivery_type']) === 3) {
		if (!empty($data['address_full'])) {
			$address_parts[] = $data['address_full'];
		}
	} else {
		if (!empty($warehouse_for_email)) {
			$address_parts[] = $warehouse_for_email;
		}
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
								<?php if (absint($data['delivery_type']) === 3) { ?>
									<?php if (!empty($data['address_express'])) { ?>
										<tr>
											<td style="padding:6px 0;color:#777777;">Експрес доставка:</td>
											<td style="padding:6px 0;color:#111111;">Так</td>
										</tr>
									<?php } ?>
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

	$atts['to'][] = 'andreyhtml1997@gmail.com';
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
require get_template_directory() . '/functions/custom/meta-single-product.php';


require get_template_directory() . '/functions/custom/out-stock-in-the-end.php';



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
