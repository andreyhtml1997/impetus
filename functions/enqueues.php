<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Версия ассета по filemtime (чтобы браузер нормально кешировал статику)
 */
function mw_asset_ver($rel_path)
{
	$abs = get_stylesheet_directory() . $rel_path;

	if (!file_exists($abs)) {
		return null;
	}

	return (string) filemtime($abs);
}

/* ------------------Enqueues----------------------- */
function scripts_and_styles()
{

	$is_catalog = false;

	if (!is_admin()) {
		$uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
		$path = $uri ? (string) parse_url($uri, PHP_URL_PATH) : '';
		$path = $path ? '/' . ltrim($path, '/') : '';

		// если в URL есть "catalog" (любой язык, любые вложенности)
		if ($path && strpos($path, 'catalog') !== false) {
			$is_catalog = true;
		}
	}
	// ---------------- Styles ----------------

	// Главный style.css темы (обычно критичный для первого экрана)
	$theme_style_ver = mw_asset_ver('/style.css');
	wp_enqueue_style('style-css', get_stylesheet_uri(), [], $theme_style_ver);

	// Локальные стили с корректными версиями (для кеша)
	wp_enqueue_style(
		'font-style',
		get_template_directory_uri() . '/assets/fonts/style.css',
		[],
		mw_asset_ver('/assets/fonts/style.css')
	);

	wp_enqueue_style(
		'plugins-css',
		get_template_directory_uri() . '/assets/css/plugins.min.css',
		[],
		mw_asset_ver('/assets/css/plugins.min.css')
	);
	if (!$is_catalog) {
		wp_enqueue_style(
			'fancy-css',
			get_template_directory_uri() . '/assets/css/fancybox.css',
			[],
			mw_asset_ver('/assets/css/fancybox.css')
		);
	}
	// if (!$is_catalog) {
	wp_enqueue_style(
		'ui-css',
		get_template_directory_uri() . '/assets/css/jquery-ui.css',
		[],
		mw_asset_ver('/assets/css/jquery-ui.css')
	);
	// }

	// Основной CSS сайта (часто критичен)
	wp_enqueue_style(
		'main-css',
		get_template_directory_uri() . '/assets/css/style.css',
		[],
		mw_asset_ver('/assets/css/style.css')
	);

	wp_enqueue_style(
		'media-css',
		get_template_directory_uri() . '/assets/css/media.css',
		[],
		mw_asset_ver('/assets/css/media.css')
	);

	// ---------------- Scripts ----------------

	// Встроенный jQuery WP (быстрее и стабильнее, чем внешний CDN)
	wp_enqueue_script('jquery');

	wp_enqueue_script(
		'plugins-js',
		get_template_directory_uri() . '/assets/js/plugins.js',
		['jquery'],
		mw_asset_ver('/assets/js/plugins.js'),
		true
	);

	wp_enqueue_script(
		'smoothscroll',
		get_template_directory_uri() . '/assets/js/smoothscroll.js',
		['jquery'],
		mw_asset_ver('/assets/js/smoothscroll.js'),
		true
	);
	if (!$is_catalog) {
		wp_enqueue_script(
			'simpleParallax',
			get_template_directory_uri() . '/assets/js/simpleParallax.min.js',
			['jquery'],
			mw_asset_ver('/assets/js/simpleParallax.min.js'),
			true
		);
	}
	if (!$is_catalog) {
		wp_enqueue_script(
			'fancy-scripts',
			get_template_directory_uri() . '/assets/js/fancybox.js',
			['jquery'],
			mw_asset_ver('/assets/js/fancybox.js'),
			true
		);
	}
	// if (!$is_catalog) {
	wp_enqueue_script(
		'ui-scripts',
		get_template_directory_uri() . '/assets/js/jquery-ui.min.js',
		['jquery'],
		mw_asset_ver('/assets/js/jquery-ui.min.js'),
		true
	);
	// }
	if (!$is_catalog) {
		wp_enqueue_script(
			'touch-scripts',
			get_template_directory_uri() . '/assets/js/jquery.ui.touch-punch.min.js',
			['jquery'],
			mw_asset_ver('/assets/js/jquery.ui.touch-punch.min.js'),
			true
		);
	}
	if (!$is_catalog) {
		wp_enqueue_script(
			'gsap1-scripts',
			get_template_directory_uri() . '/assets/js/gsap3/TweenMax.min.js',
			[],
			mw_asset_ver('/assets/js/gsap3/TweenMax.min.js'),
			true
		);
	}
	if (!$is_catalog) {
		wp_enqueue_script(
			'gsap2-scripts',
			get_template_directory_uri() . '/assets/js/gsap3/ScrollTrigger.min.js',
			[],
			mw_asset_ver('/assets/js/gsap3/ScrollTrigger.min.js'),
			true
		);
	}
	if (!$is_catalog) {
		wp_enqueue_script(
			'gsap3-scripts',
			get_template_directory_uri() . '/assets/js/gsap3/gsap.min.js',
			[],
			mw_asset_ver('/assets/js/gsap3/gsap.min.js'),
			true
		);
	}
	wp_enqueue_script(
		'media-scripts',
		get_template_directory_uri() . '/assets/js/scripts.js',
		['jquery'],
		mw_asset_ver('/assets/js/scripts.js'),
		true
	);

	// Внешний (оставляем как есть)
	/* wp_enqueue_script(
		'lottie-player',
		'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
		[],
		null,
		true
	); */

	// Inline vars
	$vars = [
		'ajaxurl' => admin_url('admin-ajax.php'),
		'securitycode' => wp_create_nonce(NONCE),
		'currentpagelink' => get_permalink(),
		'postID' => get_queried_object_id(),
	];

	wp_add_inline_script('jquery', "var appVars = " . json_encode($vars, JSON_PRETTY_PRINT) . ";", 'before');
}
add_action('wp_enqueue_scripts', 'scripts_and_styles');

/**
 * Preconnect к внешним доменам (ускоряет DNS/TLS)
 */
add_filter('wp_resource_hints', function ($hints, $relation_type) {

	if ($relation_type !== 'preconnect') {
		return $hints;
	}

	$hints[] = 'https://unpkg.com';
	$hints[] = 'https://keepincrm.chat';
	$hints[] = 'https://static.keepincrm.com';
	$hints[] = 'https://connect.facebook.net';
	$hints[] = 'https://www.googletagmanager.com';

	return $hints;

}, 10, 2);

/**
 * Убираем render-blocking для части CSS:
 * preload -> onload rel=stylesheet
 * (ничего не отключаем, просто меняем способ загрузки)
 */
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {

	if (is_admin()) {
		return $html;
	}

	// Эти оставляем блокирующими (чтобы не было мигания без стилей)
	$blocking = [
		// 'style-css',
		'main-css',
	];

	if (in_array($handle, $blocking, true)) {
		return $html;
	}

	$href = esc_url($href);

	$out = "<link rel='preload' as='style' href='{$href}' onload=\"this.onload=null;this.rel='stylesheet'\">";
	$out .= "\n<noscript><link rel='stylesheet' href='{$href}'></noscript>\n";

	return $out;

}, 10, 4);



add_action('wp_enqueue_scripts', function () {

	if (is_admin()) {
		return;
	}

	// Возвращаем $ как jQuery для фронта (fix для noConflict + Autoptimize)
	wp_add_inline_script('jquery', 'window.$ = window.jQuery;', 'after');

}, 999);



add_action('wp_default_scripts', function ($scripts) {

	if (is_admin()) {
		return;
	}

	if (!isset($scripts)) {
		return;
	}

	$scripts->add_data('jquery', 'group', 1);
	$scripts->add_data('jquery-core', 'group', 1);
	$scripts->add_data('jquery-migrate', 'group', 1);

}, 1);





add_action('wp_head', function () {

	if (is_admin()) {
		return;
	}

	echo "<link rel='preconnect' href='https://fonts.googleapis.com'>\n";
	echo "<link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>\n";

}, 1);

add_filter('style_loader_src', function ($src, $handle) {

	if (!$src) {
		return $src;
	}

	if (strpos($src, 'fonts.googleapis.com') === false) {
		return $src;
	}

	if (strpos($src, 'display=') !== false) {
		return $src;
	}

	$join = '?';
	if (strpos($src, '?') !== false) {
		$join = '&';
	}

	return $src . $join . 'display=swap';

}, 10, 2);

add_filter('style_loader_tag', function ($html, $handle, $href, $media) {

	if (is_admin()) {
		return $html;
	}

	if (!$href) {
		return $html;
	}

	if (strpos($href, 'fonts.googleapis.com') === false) {
		return $html;
	}

	$href = esc_url($href);

	$out = "<link rel='preload' as='style' href='{$href}' onload=\"this.onload=null;this.rel='stylesheet'\">";
	$out .= "\n<noscript><link rel='stylesheet' href='{$href}'></noscript>\n";

	return $out;

}, 10, 4);
