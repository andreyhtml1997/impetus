<?php

/**
 * CATALOG SEO OVERRIDE (2 задачи в одном файле без дублей)
 *
 * 1) /catalog/ (и /ru/catalog/, /en/catalog/)
 *    ACF поля страницы catalog:
 *    - meta_title_ua|ru|en
 *    - meta_description_ua|ru|en
 *    fallback: Yoast SEO page fields -> Yoast templates (title-page / metadesc-page)
 *
 * 2) /catalog/{audience}/.../{last-slug}/ (и /ru/... /en/...)
 *    Audience: men|women|children (берём сегмент после catalog)
 *    ACF поля терма ТОЛЬКО product_category:
 *      {aud}_meta_title_{ua|ru|en}
 *      {aud}_meta_description_{ua|ru|en}
 *    fallback: Yoast term fields -> Yoast templates for taxonomy
 */

/* ------------------------- helpers (общие) ------------------------- */

function mw_catalog_get_lang_from_request()
{
  if (is_admin()) {
    return 'ua';
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  if (!$uri) {
    return 'ua';
  }

  $path = parse_url($uri, PHP_URL_PATH);
  if (!$path) {
    return 'ua';
  }

  $path = '/' . ltrim($path, '/');

  if (strpos($path, '/ru/') === 0 || $path === '/ru') {
    return 'ru';
  }

  if (strpos($path, '/en/') === 0 || $path === '/en') {
    return 'en';
  }

  return 'ua';
}

function mw_catalog_get_path_parts()
{
  if (is_admin()) {
    return array();
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  if (!$uri) {
    return array();
  }

  $path = parse_url($uri, PHP_URL_PATH);
  if (!$path) {
    return array();
  }

  $path = '/' . ltrim($path, '/');

  // убираем языковой префикс /ru/ или /en/
  if (strpos($path, '/ru/') === 0) {
    $path = substr($path, 3);
  } elseif (strpos($path, '/en/') === 0) {
    $path = substr($path, 3);
  }

  if (strpos($path, '/catalog/') !== 0) {
    return array();
  }

  $parts = array_values(array_filter(explode('/', trim($path, '/'))));
  return $parts ?: array();
}

function mw_catalog_is_catalog_request()
{
  $parts = mw_catalog_get_path_parts();
  return !empty($parts);
}

function mw_catalog_is_catalog_root_request()
{
  if (is_admin()) {
    return false;
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  if (!$uri) {
    return false;
  }

  $path = parse_url($uri, PHP_URL_PATH);
  if (!$path) {
    return false;
  }

  $path = '/' . ltrim($path, '/');
  $path = rtrim($path, '/') . '/';

  if ($path === '/catalog/' || $path === '/ru/catalog/' || $path === '/en/catalog/') {
    return true;
  }

  return false;
}

function mw_catalog_get_last_slug_from_request()
{
  $parts = mw_catalog_get_path_parts();
  if (!$parts) {
    return '';
  }

  $last = end($parts);
  if (!$last) {
    return '';
  }

  return sanitize_title($last);
}

function mw_catalog_get_audience_slug_from_request()
{
  $parts = mw_catalog_get_path_parts();
  if (!$parts) {
    return '';
  }

  $catalog_index = array_search('catalog', $parts, true);
  if ($catalog_index === false) {
    return '';
  }

  $aud = isset($parts[$catalog_index + 1]) ? $parts[$catalog_index + 1] : '';
  if (!$aud) {
    return '';
  }

  $aud = sanitize_title($aud);

  if ($aud === 'kids') {
    $aud = 'children';
  }

  return $aud; // men | women | children
}

function mw_catalog_wpseo_expand_vars($text, $context)
{
  if (!$text) {
    return '';
  }

  if (function_exists('wpseo_replace_vars')) {
    $text = wpseo_replace_vars($text, $context);
  }

  return trim((string) $text);
}

/* ------------------------- задача 2: term SEO по audience+lang ------------------------- */

function mw_catalog_get_term_from_last_slug()
{
  $slug = mw_catalog_get_last_slug_from_request();
  if (!$slug) {
    return false;
  }

  $taxonomies = array(
    'product_category',
    'audience_category',
  );

  foreach ($taxonomies as $taxonomy) {
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term && !is_wp_error($term)) {
      return $term;
    }
  }

  return false;
}

function mw_catalog_get_acf_term_seo_by_audience($term)
{
  $out = array(
    'title' => '',
    'desc' => ''
  );

  if (!$term || empty($term->taxonomy) || empty($term->term_id)) {
    return $out;
  }

  // эти поля у тебя "у каждой категории" -> product_category
  if ($term->taxonomy !== 'product_category') {
    return $out;
  }

  $aud = mw_catalog_get_audience_slug_from_request();
  if (!$aud) {
    return $out;
  }

  $lang = mw_catalog_get_lang_from_request(); // ua|ru|en

  $allowed_aud = array(
    'men',
    'women',
    'children'
  );
  $allowed_lang = array(
    'ua',
    'ru',
    'en'
  );

  if (!in_array($aud, $allowed_aud, true)) {
    return $out;
  }

  if (!in_array($lang, $allowed_lang, true)) {
    $lang = 'ua';
  }

  $term_id_prefixed = $term->taxonomy . '_' . (int) $term->term_id;

  $title_field = $aud . '_meta_title_' . $lang;
  $desc_field = $aud . '_meta_description_' . $lang;

  $t = get_field($title_field, $term_id_prefixed);
  $d = get_field($desc_field, $term_id_prefixed);

  // если в ACF будут переменные Yoast
  $t = mw_catalog_wpseo_expand_vars($t, $term);
  $d = mw_catalog_wpseo_expand_vars($d, $term);

  if ($t) {
    $out['title'] = wp_strip_all_tags($t);
  }

  if ($d) {
    $out['desc'] = wp_strip_all_tags($d);
  }

  return $out;
}

function mw_catalog_get_yoast_term_seo($term)
{
  $out = array(
    'title' => '',
    'desc' => ''
  );

  if (!$term || empty($term->taxonomy) || empty($term->term_id)) {
    return $out;
  }

  $taxonomy = $term->taxonomy;
  $term_id = (int) $term->term_id;

  // 1) Yoast поля терма
  if (class_exists('WPSEO_Taxonomy_Meta')) {
    $t = WPSEO_Taxonomy_Meta::get_term_meta($term_id, $taxonomy, 'title');
    $d = WPSEO_Taxonomy_Meta::get_term_meta($term_id, $taxonomy, 'desc');

    $t = mw_catalog_wpseo_expand_vars($t, $term);
    $d = mw_catalog_wpseo_expand_vars($d, $term);

    if ($t) {
      $out['title'] = wp_strip_all_tags($t);
    }

    if ($d) {
      $out['desc'] = wp_strip_all_tags($d);
    }
  }

  // 2) Шаблоны Search Appearance для таксы
  $wpseo_titles = get_option('wpseo_titles');

  if (!$out['title'] && is_array($wpseo_titles)) {
    $key = 'title-tax-' . $taxonomy;
    $tpl = !empty($wpseo_titles[$key]) ? $wpseo_titles[$key] : '';
    $tpl = mw_catalog_wpseo_expand_vars($tpl, $term);

    if ($tpl) {
      $out['title'] = wp_strip_all_tags($tpl);
    }
  }

  if (!$out['desc'] && is_array($wpseo_titles)) {
    $key = 'metadesc-tax-' . $taxonomy;
    $tpl = !empty($wpseo_titles[$key]) ? $wpseo_titles[$key] : '';
    $tpl = mw_catalog_wpseo_expand_vars($tpl, $term);

    if ($tpl) {
      $out['desc'] = wp_strip_all_tags($tpl);
    }
  }

  return $out;
}

function mw_catalog_get_final_term_seo($term)
{
  $out = array(
    'title' => '',
    'desc' => ''
  );

  if (!$term) {
    return $out;
  }

  // 1) ACF (audience+lang)
  $acf = mw_catalog_get_acf_term_seo_by_audience($term);
  if (!empty($acf['title'])) {
    $out['title'] = $acf['title'];
  }
  if (!empty($acf['desc'])) {
    $out['desc'] = $acf['desc'];
  }

  // 2) Yoast
  if (!$out['title'] || !$out['desc']) {
    $yoast = mw_catalog_get_yoast_term_seo($term);

    if (!$out['title'] && !empty($yoast['title'])) {
      $out['title'] = $yoast['title'];
    }

    if (!$out['desc'] && !empty($yoast['desc'])) {
      $out['desc'] = $yoast['desc'];
    }
  }

  return $out;
}

/* ------------------------- задача 1: SEO для /catalog/ root page ------------------------- */

function mw_catalog_get_yoast_post_seo($post_id)
{
  $out = array(
    'title' => '',
    'desc' => ''
  );

  $post_id = (int) $post_id;
  if (!$post_id) {
    return $out;
  }

  $post = get_post($post_id);
  if (!$post) {
    return $out;
  }

  // 1) Yoast поля страницы
  if (class_exists('WPSEO_Meta')) {
    $t = WPSEO_Meta::get_value('title', $post_id);
    $d = WPSEO_Meta::get_value('metadesc', $post_id);

    $t = mw_catalog_wpseo_expand_vars($t, $post);
    $d = mw_catalog_wpseo_expand_vars($d, $post);

    if ($t) {
      $out['title'] = wp_strip_all_tags($t);
    }

    if ($d) {
      $out['desc'] = wp_strip_all_tags($d);
    }
  }

  // 2) Шаблоны Yoast для страниц
  $wpseo_titles = get_option('wpseo_titles');

  if (!$out['title'] && is_array($wpseo_titles)) {
    $tpl = !empty($wpseo_titles['title-page']) ? $wpseo_titles['title-page'] : '';
    $tpl = mw_catalog_wpseo_expand_vars($tpl, $post);

    if ($tpl) {
      $out['title'] = wp_strip_all_tags($tpl);
    }
  }

  if (!$out['desc'] && is_array($wpseo_titles)) {
    $tpl = !empty($wpseo_titles['metadesc-page']) ? $wpseo_titles['metadesc-page'] : '';
    $tpl = mw_catalog_wpseo_expand_vars($tpl, $post);

    if ($tpl) {
      $out['desc'] = wp_strip_all_tags($tpl);
    }
  }

  return $out;
}

function mw_catalog_get_catalog_root_seo()
{
  $out = array(
    'title' => '',
    'desc' => ''
  );

  if (!defined('CATALOG_PAGE_ID')) {
    return $out;
  }

  $page_id = (int) CATALOG_PAGE_ID;
  if (!$page_id) {
    return $out;
  }

  $lang = mw_catalog_get_lang_from_request(); // ua|ru|en

  // ТОЛЬКО эти поля (как ты сказал)
  $acf_title = get_field('meta_title_' . $lang, $page_id);
  $acf_desc = get_field('meta_description_' . $lang, $page_id);

  if ($acf_title) {
    $out['title'] = wp_strip_all_tags($acf_title);
  }

  if ($acf_desc) {
    $out['desc'] = wp_strip_all_tags($acf_desc);
  }

  // fallback to Yoast
  if (!$out['title'] || !$out['desc']) {
    $yoast = mw_catalog_get_yoast_post_seo($page_id);

    if (!$out['title'] && !empty($yoast['title'])) {
      $out['title'] = $yoast['title'];
    }

    if (!$out['desc'] && !empty($yoast['desc'])) {
      $out['desc'] = $yoast['desc'];
    }
  }

  return $out;
}

/* ------------------------- filters (один набор, без дублей) ------------------------- */

add_filter('wpseo_title', function ($title) {

  // 1) /catalog/ root page
  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['title']) ? $seo['title'] : $title;
  }

  // 2) /catalog/... term pages
  if (!mw_catalog_is_catalog_request()) {
    return $title;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $title;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['title']) ? $seo['title'] : $title;

}, 20);

add_filter('wpseo_metadesc', function ($desc) {

  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['desc']) ? $seo['desc'] : $desc;
  }

  if (!mw_catalog_is_catalog_request()) {
    return $desc;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $desc;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['desc']) ? $seo['desc'] : $desc;

}, 20);

add_filter('wpseo_opengraph_title', function ($og_title) {

  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['title']) ? $seo['title'] : $og_title;
  }

  if (!mw_catalog_is_catalog_request()) {
    return $og_title;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $og_title;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['title']) ? $seo['title'] : $og_title;

}, 20);

add_filter('wpseo_opengraph_desc', function ($og_desc) {

  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['desc']) ? $seo['desc'] : $og_desc;
  }

  if (!mw_catalog_is_catalog_request()) {
    return $og_desc;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $og_desc;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['desc']) ? $seo['desc'] : $og_desc;

}, 20);

add_filter('wpseo_twitter_title', function ($tw_title) {

  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['title']) ? $seo['title'] : $tw_title;
  }

  if (!mw_catalog_is_catalog_request()) {
    return $tw_title;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $tw_title;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['title']) ? $seo['title'] : $tw_title;

}, 20);

add_filter('wpseo_twitter_description', function ($tw_desc) {

  if (mw_catalog_is_catalog_root_request()) {
    $seo = mw_catalog_get_catalog_root_seo();
    return !empty($seo['desc']) ? $seo['desc'] : $tw_desc;
  }

  if (!mw_catalog_is_catalog_request()) {
    return $tw_desc;
  }

  $term = mw_catalog_get_term_from_last_slug();
  if (!$term) {
    return $tw_desc;
  }

  $seo = mw_catalog_get_final_term_seo($term);
  return !empty($seo['desc']) ? $seo['desc'] : $tw_desc;

}, 20);



/* ------------------------- CANONICAL для /catalog/* ------------------------- */

function mw_catalog_current_canonical_url()
{
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (!$uri) {
    return '';
  }

  $path = parse_url($uri, PHP_URL_PATH);
  if (!$path) {
    return '';
  }

  $path = '/' . ltrim($path, '/');
  $base = home_url('/');

  $base_path = parse_url($base, PHP_URL_PATH);
  $base_path = $base_path ? ('/' . trim($base_path, '/')) : '';
  $base_path = $base_path === '/' ? '' : $base_path;

  // TranslatePress can inject language prefix into home_url() on non-default language.
  // If REQUEST_URI already has same language prefix, avoid duplicated /ru/ru/ (or /en/en/).
  if (preg_match('~^/(ru|en)(/|$)~', $path, $m)) {
    $lang_prefix = '/' . $m[1];
    if ($base_path === $lang_prefix || strpos($base_path . '/', $lang_prefix . '/') === 0) {
      $path = preg_replace('~^/' . preg_quote($m[1], '~') . '(/|$)~', '/', $path, 1);
      if (!$path) {
        $path = '/';
      }
    }
  }

  return untrailingslashit($base) . user_trailingslashit($path);
}

// canonical, который выводит Yoast
add_filter('wpseo_canonical', function ($canonical) {
  if (!function_exists('mw_catalog_is_catalog_request') || !mw_catalog_is_catalog_request()) {
    return $canonical;
  }

  $url = mw_catalog_current_canonical_url();
  return $url ? $url : $canonical;
}, 999);

// canonical ядра WP (на случай, если Yoast выключен/не сработал)
add_filter('get_canonical_url', function ($canonical, $post) {
  if (function_exists('mw_catalog_is_catalog_request') && mw_catalog_is_catalog_request()) {
    $url = mw_catalog_current_canonical_url();
    return $url ? $url : $canonical;
  }
  return $canonical;
}, 999, 2);


add_filter('wpseo_title', function ($title) {
  $ctx = function_exists('impetus_catalog_get_static_filter_context') ? impetus_catalog_get_static_filter_context() : array();
  if (empty($ctx))
    return $title;

  $seo_key = $ctx['seo_key'];
  $lang = $ctx['lang'];

  $override = function_exists('impetus_catalog_get_seo_override') ? impetus_catalog_get_seo_override($seo_key, $lang) : array();
  if (!empty($override['title']))
    return $override['title'];

  // По ТЗ гарантированно нужен UA шаблон
  if ($lang !== 'ua')
    return $title;

  $meta = impetus_catalog_build_meta_ua($ctx);
  return !empty($meta['title']) ? $meta['title'] : $title;
}, 50);

add_filter('wpseo_metadesc', function ($desc) {
  $ctx = function_exists('impetus_catalog_get_static_filter_context') ? impetus_catalog_get_static_filter_context() : array();
  if (empty($ctx))
    return $desc;

  $seo_key = $ctx['seo_key'];
  $lang = $ctx['lang'];

  $override = function_exists('impetus_catalog_get_seo_override') ? impetus_catalog_get_seo_override($seo_key, $lang) : array();
  if (!empty($override['description']))
    return $override['description'];

  if ($lang !== 'ua')
    return $desc;

  $meta = impetus_catalog_build_meta_ua($ctx);
  return !empty($meta['description']) ? $meta['description'] : $desc;
}, 50);

// чтобы OG/Twitter совпадали
add_filter('wpseo_opengraph_title', function ($v) {
  return apply_filters('wpseo_title', $v);
}, 50);
add_filter('wpseo_opengraph_desc', function ($v) {
  return apply_filters('wpseo_metadesc', $v);
}, 50);
add_filter('wpseo_twitter_title', function ($v) {
  return apply_filters('wpseo_title', $v);
}, 50);
add_filter('wpseo_twitter_description', function ($v) {
  return apply_filters('wpseo_metadesc', $v);
}, 50);







add_action('wp_head', function () {
  if (!current_user_can('manage_options'))
    return;

  if (!function_exists('impetus_catalog_get_static_filter_context'))
    return;
  $ctx = impetus_catalog_get_static_filter_context();
  if (!$ctx)
    return;

  $id = impetus_catalog_get_seo_override_post_id($ctx['seo_key']);

  echo "\n<!-- SEO KEY: {$ctx['seo_key']} -->\n";
  echo "<!-- SEO OVERRIDE ID: {$id} -->\n";
}, 1);

// ================== OPTIONAL: OVERRIDE ANY /catalog/* ==================
// Если включено, то для ЛЮБЫХ URL каталога сначала пытаемся взять Title/Description из CPT seo_catalog по seo_key.
// Если не найдено — дальше работает твоя текущая логика (шаблон/статические правила).
add_filter('wpseo_title', function ($title) {

  // >>> Раскомментируй следующую строку, чтобы включить без константы:
  // $enabled = true;

  $enabled = defined('IMPUTUS_SEO_OVERRIDE_ANY_CATALOG_URL') && IMPETUS_SEO_OVERRIDE_ANY_CATALOG_URL;
  if (!$enabled)
    return $title;

  if (!is_page(CATALOG_PAGE_ID))
    return $title;
  if (!function_exists('impetus_catalog_seo_key') || !function_exists('impetus_catalog_lang'))
    return $title;
  if (!function_exists('impetus_catalog_get_seo_override'))
    return $title;

  $seo_key = impetus_catalog_seo_key();
  $lang = impetus_catalog_lang();

  $ov = impetus_catalog_get_seo_override($seo_key, $lang);
  if (!empty($ov['title']))
    return $ov['title'];

  return $title;
}, 5);

add_filter('wpseo_metadesc', function ($desc) {

  // >>> Раскомментируй следующую строку, чтобы включить без константы:
  // $enabled = true;

  $enabled = defined('IMPUTUS_SEO_OVERRIDE_ANY_CATALOG_URL') && IMPETUS_SEO_OVERRIDE_ANY_CATALOG_URL;
  if (!$enabled)
    return $desc;

  if (!is_page(CATALOG_PAGE_ID))
    return $desc;
  if (!function_exists('impetus_catalog_seo_key') || !function_exists('impetus_catalog_lang'))
    return $desc;
  if (!function_exists('impetus_catalog_get_seo_override'))
    return $desc;

  $seo_key = impetus_catalog_seo_key();
  $lang = impetus_catalog_lang();

  $ov = impetus_catalog_get_seo_override($seo_key, $lang);
  if (!empty($ov['description']))
    return $ov['description'];

  return $desc;
}, 5);




add_filter('wpseo_robots_array', function ($robots) {

  if (!function_exists('impetus_catalog_desired_robots_array')) {
    return $robots;
  }

  $desired = impetus_catalog_desired_robots_array();
  if (empty($desired)) {
    return $robots;
  }

  if (!is_array($robots)) {
    $robots = array();
  }

  // убираем старые директивы, чтобы не было конфликтов
  unset(
    $robots['index'],
    $robots['follow'],
    $robots['noindex'],
    $robots['nofollow'],
    $robots[0],
    $robots[1]
  );

  $want_index = in_array('index', $desired, true);
  $want_follow = in_array('follow', $desired, true);
  $want_noindex = in_array('noindex', $desired, true);
  $want_nofollow = in_array('nofollow', $desired, true);

  if ($want_index)
    $robots['index'] = 'index';
  if ($want_follow)
    $robots['follow'] = 'follow';

  if ($want_noindex)
    $robots['noindex'] = 'noindex';
  if ($want_nofollow)
    $robots['nofollow'] = 'nofollow';

  return $robots;
}, 50);

// Exclude technical seo_catalog URLs from Yoast XML sitemaps.
add_filter('wpseo_sitemap_exclude_post_type', function ($excluded, $post_type) {
  if ($post_type === 'seo_catalog') {
    return true;
  }
  return $excluded;
}, 10, 2);

// Safety net: if any URL with /seo_catalog/ is produced, drop it from sitemap output.
add_filter('wpseo_sitemap_entry', function ($url, $type, $post) {
  if (!is_array($url) || empty($url['loc'])) {
    return $url;
  }

  $path = parse_url((string) $url['loc'], PHP_URL_PATH);
  if (!$path) {
    return $url;
  }

  $path = '/' . ltrim($path, '/');
  if (preg_match('~^/(seo_catalog|ru/seo_catalog|en/seo_catalog|uk/seo_catalog)(/|$)~i', $path)) {
    return false;
  }

  return $url;
}, 10, 3);



// =====================================================
// FORCE ENDPOINT: /catalog_filters-sitemap.xml
// (чтобы не ловился твоим редиректом 404 -> главная)
// =====================================================

// query_var
add_filter('query_vars', function ($vars) {
  $vars[] = 'catalog_filters_sitemap';
  return $vars;
});

// rewrite rule
add_action('init', function () {
  add_rewrite_rule('catalog_filters-sitemap\.xml$', 'index.php?catalog_filters_sitemap=1', 'top');
}, 20);

// отдаём XML
add_action('template_redirect', function () {
  if ((int) get_query_var('catalog_filters_sitemap') !== 1) {
    return;
  }

  // важно: никаких редиректов, просто XML и exit
  header('Content-Type: application/xml; charset=UTF-8');

  $entries = function_exists('impetus_catalog_filters_sitemap_get_entries')
    ? impetus_catalog_filters_sitemap_get_entries()
    : array();

  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  if (!empty($entries)) {
    foreach ($entries as $e) {
      if (empty($e['loc']))
        continue;

      $loc = (string) $e['loc'];
      $lastmod = !empty($e['lastmod']) ? (int) $e['lastmod'] : 0;

      echo "<url>\n";
      echo '<loc>' . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . "</loc>\n";
      if ($lastmod) {
        echo '<lastmod>' . gmdate('c', $lastmod) . "</lastmod>\n";
      }
      echo "</url>\n";
    }
  }

  echo "</urlset>";
  exit;
}, 0);
