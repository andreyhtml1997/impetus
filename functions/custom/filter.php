<?php
// ================== OPTIONAL TOGGLE ==================
// Включить ручные SEO-оверрайды (CPT seo_catalog по seo_key) для ЛЮБЫХ /catalog/* URL.
// По умолчанию выключено.
// Раскомментируй строку ниже, если захочешь включить:
/// define('IMPUTUS_SEO_OVERRIDE_ANY_CATALOG_URL', true);
add_action('wp_loaded', 'my_flush_rules');
function my_flush_rules()
{
  global $wp_rewrite;

  $rules = (array) get_option('rewrite_rules');

  $catalog_page_slug = get_post_field('post_name', CATALOG_PAGE_ID);
  if (empty($catalog_page_slug)) {
    return;
  }

  $prefixes = array(
    'uk/',
    'en/',
    ''
  );

  $need = array();
  foreach ($prefixes as $p) {
    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/?$';

    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/color[-_]([^/]+)/?$';
    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/material_([^/]+)/?$';

    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/color[-_]([^/]+)/?$';
    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/material_([^/]+)/?$';

    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/color[-_]([^/]+)/?$';
    $need[] = $p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/material_([^/]+)/?$';
  }

  foreach ($need as $k) {
    if (!isset($rules[$k])) {
      $wp_rewrite->flush_rules();
      return;
    }
  }
}

add_filter('rewrite_rules_array', 'my_insert_rewrite_rules');
function my_insert_rewrite_rules($rules)
{
  $catalog_page_slug = get_post_field('post_name', CATALOG_PAGE_ID);
  if (empty($catalog_page_slug))
    return $rules;

  $filter_rules = array();

  $prefixes = array(
    'uk/',
    'en/',
    ''
  );

  foreach ($prefixes as $p) {

    // ===== audience only =====
    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&color=$matches[3]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&material=$matches[3]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/color[-_]([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&color=$matches[3]&material=$matches[4]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/material_([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&material=$matches[3]&color=$matches[4]';


    // ===== audience + product_category =====
    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]&color=$matches[4]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]&material=$matches[4]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/color[-_]([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]&color=$matches[4]&material=$matches[5]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/((?!brand_)[^/]+)/material_([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&product_category=$matches[3]&material=$matches[4]&color=$matches[5]';


    // ===== audience + brand =====
    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&color=$matches[4]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&material=$matches[4]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/color[-_]([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&color=$matches[4]&material=$matches[5]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/material_([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&material=$matches[4]&color=$matches[5]';


    // ===== audience + brand + product_category (категория после brand_) =====
    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]&color=$matches[5]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]&material=$matches[5]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/color[-_]([^/]+)/material_([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]&color=$matches[5]&material=$matches[6]';

    $filter_rules[$p . '(' . $catalog_page_slug . ')/([^/]+)/brand_([^/]+)/([^/]+)/material_([^/]+)/color[-_]([^/]+)/?$']
      = 'index.php?pagename=$matches[1]&audience_category=$matches[2]&brand=$matches[3]&product_category=$matches[4]&material=$matches[5]&color=$matches[6]';
  }

  // ===== старые правила (как было) =====
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

  $newrules = $filter_rules + $newrules;

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
    'color',
    'material'
  );
  return $vars;
}

function get_catalog_url($audience_category = null, $product_category = null, $brand = null, $brand_product_category = null, $highlighted = null, $color = null, $material = null)
{
  if ($audience_category === null)
    $audience_category = get_user_cookie_audience_category();

  return sprintf(
    '%1$s%2$s%3$s%4$s%5$s%6$s%7$s%8$s',
    get_permalink(CATALOG_PAGE_ID),
    trailingslashit($audience_category),
    !empty($product_category) ? trailingslashit($product_category) : '',
    !empty($brand) ? trailingslashit('brand_' . $brand) : '',
    !empty($brand_product_category) ? trailingslashit($brand_product_category) : '',
    !empty($color) ? trailingslashit('color-' . $color) : '',
    !empty($material) ? trailingslashit('material_' . $material) : '',
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

add_action('template_redirect', 'impetus_redirect_en_prefix_to_default', 0);
function impetus_redirect_en_prefix_to_default()
{
  if (is_admin() || wp_doing_ajax()) {
    return;
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (!$uri) {
    return;
  }

  $path = parse_url($uri, PHP_URL_PATH);
  if (!$path) {
    return;
  }

  $path = '/' . ltrim($path, '/');
  if (!preg_match('~^/en(?=/|$)~i', $path)) {
    return;
  }

  $new_path = preg_replace('~^/en(?=/|$)~i', '', $path, 1);
  $new_path = '/' . ltrim((string) $new_path, '/');
  if ($new_path === '//') {
    $new_path = '/';
  }

  $host = isset($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
  if ($host === '') {
    $host = (string) parse_url(home_url('/'), PHP_URL_HOST);
  }

  $scheme = is_ssl() ? 'https' : 'http';
  $target = $scheme . '://' . $host . $new_path;
  $query = parse_url($uri, PHP_URL_QUERY);
  if (!empty($query)) {
    $target .= '?' . $query;
  }

  wp_redirect($target, 301);
  exit;
}

add_action('template_redirect', 'impetus_block_seo_catalog_urls', 0);
function impetus_block_seo_catalog_urls()
{
  if (is_admin() || wp_doing_ajax()) {
    return;
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  $path = $uri ? parse_url($uri, PHP_URL_PATH) : '';
  $path = '/' . ltrim((string) $path, '/');

  $is_seo_catalog_path = (bool) preg_match('~^/(seo_catalog|ru/seo_catalog|en/seo_catalog|uk/seo_catalog)(/|$)~i', $path);
  $is_seo_catalog_post = is_singular('seo_catalog') || is_post_type_archive('seo_catalog');

  if ($is_seo_catalog_path || $is_seo_catalog_post) {
    _wp_redirect(home_url('/'), 301);
  }
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


add_action('template_redirect', 'impetus_catalog_normalize_filter_url', 4);
function impetus_catalog_normalize_filter_url()
{
  if (!is_page(CATALOG_PAGE_ID))
    return;

  $aud = get_query_var('audience_category');
  if ($aud === '')
    return;

  $brand = get_query_var('brand');
  $pc = get_query_var('product_category');
  $highlighted = get_query_var('highlighted');
  $color = get_query_var('color');
  $material = get_query_var('material');

  $expected = '';

  // ВАЖНО: если есть brand — product_category в URL стоит после brand_
  if ($brand !== '') {
    $expected = get_catalog_url($aud, null, $brand, ($pc !== '' ? $pc : null), ($highlighted !== '' ? $highlighted : null), ($color !== '' ? $color : null), ($material !== '' ? $material : null));
  } else {
    $expected = get_catalog_url($aud, ($pc !== '' ? $pc : null), null, null, ($highlighted !== '' ? $highlighted : null), ($color !== '' ? $color : null), ($material !== '' ? $material : null));
  }

  if (!$expected)
    return;

  $cur_path = isset($_SERVER['REQUEST_URI']) ? parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
  if (!$cur_path)
    return;

  $cur_path = user_trailingslashit('/' . ltrim($cur_path, '/'));

  $exp_path = parse_url($expected, PHP_URL_PATH);
  if (!$exp_path)
    return;

  $exp_path = user_trailingslashit('/' . ltrim($exp_path, '/'));

  if ($cur_path !== $exp_path) {
    _wp_redirect($expected, 301);
  }
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

function get_color_by_query_var_color()
{
  $color = get_query_var('color');
  if ($color === '') {
    return false;
  }
  return get_term_by('slug', $color, 'color');
}

function get_material_by_query_var_material()
{
  $material = get_query_var('material');
  if ($material === '') {
    return false;
  }
  return get_term_by('slug', $material, 'material');
}

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
  $product_objs = mw_sort_products_in_stock_first(get_products($args));

  $count_products = count($product_objs);
  $answer['facets'] = !empty($product_objs) ? impetus_build_facets_for_ui($args, $product_objs) : array();
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
        'class' => 'col-12 col-sm-6 col-lg-4 op',
        'mobile_slider' => true,
      ));

      // >>> ДОБАВЛЕНО: вставка промо-блока с явной аудиторией
      if (($data['offset'] + $key) == 5 && $aud_slug) {
        get_template_part('templates/catalog-promosection', null, array('audience' => $aud_slug));
      }

    endforeach;

    wp_reset_postdata();

  endif;



  $answer['html'] = ob_get_contents();

  ob_end_clean();

  wp_send_json_success(_add_ajax_notice(array(), 1, $answer));
}

function impetus_collect_facet_ids($product_objs)
{
  $brand = array();
  $color = array();
  $material = array();
  $size = array();
  $prices = array();

  foreach ((array) $product_objs as $p) {

    if (!empty($p->product_brand)) {
      $brand[(int) $p->product_brand] = 1;
    }

    if (!empty($p->product_color)) {
      $color[(int) $p->product_color] = 1;
    }

    foreach ((array) $p->product_material as $id) {
      $id = (int) $id;
      if ($id)
        $material[$id] = 1;
    }

    foreach ((array) $p->product_size as $id) {
      $id = (int) $id;
      if ($id)
        $size[$id] = 1;
    }

    $price = isset($p->product_final_price) ? (int) $p->product_final_price : 0;
    if ($price > 0)
      $prices[] = $price;
  }

  return array(
    'brand_ids' => array_values(array_keys($brand)),
    'color_ids' => array_values(array_keys($color)),
    'material_ids' => array_values(array_keys($material)),
    'size_ids' => array_values(array_keys($size)),
    'min_price' => !empty($prices) ? min($prices) : 0,
    'max_price' => !empty($prices) ? max($prices) : 0,
  );
}

function impetus_build_facets_for_ui($args, $current_products)
{
  // min/max цены можно брать из текущей выдачи (после всех фильтров)
  $facets_current = impetus_collect_facet_ids($current_products);

  // base args для пересчёта фасетов
  $base = $args;
  $base['offset'] = 0;

  // BRAND facet: считаем без brand_ids (но brand_id фиксированный оставляем)
  if (!empty($base['brand_id'])) {
    $facets_current['brand_ids'] = array((int) $base['brand_id']);
  } else {
    $a = $base;
    $a['brand_ids'] = array();
    $p = get_products($a);
    $facets_current['brand_ids'] = impetus_collect_facet_ids($p)['brand_ids'];
  }

  // COLOR facet: считаем без color_ids
  $a = $base;
  $a['color_ids'] = array();
  $p = get_products($a);
  $facets_current['color_ids'] = impetus_collect_facet_ids($p)['color_ids'];

  // MATERIAL facet: считаем без material_ids
  $a = $base;
  $a['material_ids'] = array();
  $p = get_products($a);
  $facets_current['material_ids'] = impetus_collect_facet_ids($p)['material_ids'];

  // SIZE facet: считаем без size_ids
  $a = $base;
  $a['size_ids'] = array();
  $p = get_products($a);
  $facets_current['size_ids'] = impetus_collect_facet_ids($p)['size_ids'];

  return $facets_current;
}





function impetus_catalog_lang()
{
  $path = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  $path = $path ? parse_url($path, PHP_URL_PATH) : '';
  $path = trim((string) $path, '/');

  $first = $path ? strtok($path, '/') : '';

  // если вдруг ты реально используешь /uk/ как префикс — пусть тоже будет UA
  if ($first === 'uk')
    return 'ua';

  if ($first === 'en')
    return 'en';

  // если когда-то появится /ru/ — поддержим
  if ($first === 'ru')
    return 'ru';

  // ✅ default = ua
  return 'ua';
}

function impetus_catalog_normalize_seo_key($key)
{
  $key = trim((string) $key);
  if (!$key)
    return '';

  // если вставили полный URL — отрежем домен
  $key = preg_replace('~^https?://[^/]+~i', '', $key);

  // отрежем query-string
  $key = strtok($key, '?');

  // приведём к /path/ (со слешем в конце)
  $key = '/' . ltrim($key, '/');
  return user_trailingslashit($key);
}

function impetus_catalog_strip_lang_prefix($key)
{
  $key = impetus_catalog_normalize_seo_key($key);
  if (!$key) {
    return '';
  }

  $stripped = preg_replace('~^/(ru|en|uk)(?=/|$)~i', '', $key, 1);
  if (!$stripped) {
    $stripped = '/';
  }

  return user_trailingslashit('/' . ltrim($stripped, '/'));
}

function impetus_catalog_seo_key_candidates($seo_key)
{
  $normalized = impetus_catalog_normalize_seo_key($seo_key);
  if (!$normalized) {
    return array();
  }

  $base = impetus_catalog_strip_lang_prefix($normalized);

  $paths = array($normalized);
  if ($base) {
    $paths[] = $base;
    foreach (array('ru', 'en', 'uk') as $lang_prefix) {
      $paths[] = '/' . $lang_prefix . '/' . ltrim($base, '/');
    }
  }

  $out = array();
  foreach ($paths as $p) {
    $p = impetus_catalog_normalize_seo_key($p);
    if (!$p) {
      continue;
    }

    $variants = array(
      $p,
      untrailingslashit($p),
      ltrim($p, '/'),
      ltrim(untrailingslashit($p), '/'),
    );

    foreach ($variants as $v) {
      if ($v !== '' && !in_array($v, $out, true)) {
        $out[] = $v;
      }
    }
  }

  return $out;
}

function impetus_catalog_seo_key()
{
  $path = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  $path = $path ? parse_url($path, PHP_URL_PATH) : '';
  return impetus_catalog_normalize_seo_key($path);
}

function impetus_catalog_get_seo_override_post_id($seo_key)
{
  static $cache = array();

  $k = impetus_catalog_normalize_seo_key($seo_key);
  if (!$k)
    return 0;

  if (isset($cache[$k]))
    return (int) $cache[$k];

  $key_candidates = impetus_catalog_seo_key_candidates($k);
  if (empty($key_candidates)) {
    $cache[$k] = 0;
    return 0;
  }

  // ⚠️ если у тебя другой post_type — поменяй тут.
  $post_type = 'seo_catalog';

  // 1) быстрый точный поиск
  $meta_query = array('relation' => 'OR');
  foreach ($key_candidates as $candidate) {
    $meta_query[] = array(
      'key' => 'seo_key',
      'value' => $candidate,
      'compare' => '='
    );
  }

  $q = new WP_Query(array(
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'fields' => 'ids',
    'meta_query' => $meta_query,
  ));

  $id = 0;
  if (!empty($q->posts[0])) {
    $id = (int) $q->posts[0];
  }

  // 2) фолбэк: ищем по нормализованному значению (на случай домена/пробелов/прочего)
  if (!$id) {
    $ids = get_posts(array(
      'post_type' => $post_type,
      'post_status' => 'publish',
      'numberposts' => -1,
      'fields' => 'ids',
    ));

    foreach ($ids as $pid) {
      $val = get_post_meta((int) $pid, 'seo_key', true);
      $val_candidates = impetus_catalog_seo_key_candidates($val);
      if (!empty(array_intersect($key_candidates, $val_candidates))) {
        $id = (int) $pid;
        break;
      }
    }
  }

  $cache[$k] = $id;
  return $id;
}

function impetus_catalog_get_seo_override($seo_key, $lang)
{
  $id = impetus_catalog_get_seo_override_post_id($seo_key);
  if (!$id)
    return array();

  $title = get_field('seo_catalog_meta_title_' . $lang, $id);

  // у тебя UA поле называется meta_description, а RU/EN — description (как ты написал)
  $desc = '';
  if ($lang === 'ua') {
    $desc = get_field('seo_catalog_meta_description_ua', $id);
  } else {
    $desc = get_field('seo_catalog_meta_description_' . $lang, $id);
    if (!$desc)
      $desc = get_field('seo_catalog_description_' . $lang, $id);
  }

  $h1 = get_field('seo_catalog_h1_' . $lang, $id);
  $text = get_field('seo_catalog_text_description_' . $lang, $id);

  return array(
    'title' => is_string($title) ? trim($title) : '',
    'description' => is_string($desc) ? trim($desc) : '',
    'h1' => is_string($h1) ? trim($h1) : '',
    'text' => is_string($text) ? trim($text) : '',
  );
}

function impetus_catalog_get_static_filter_context()
{
  if (!is_page(CATALOG_PAGE_ID))
    return array();

  $aud_obj = function_exists('get_audience_category_by_query_var_audience_category') ? get_audience_category_by_query_var_audience_category() : false;
  if (!$aud_obj || empty($aud_obj->term_id))
    return array();

  $pc_obj = function_exists('get_product_category_by_query_var_product_category') ? get_product_category_by_query_var_product_category() : false;
  $brand_obj = function_exists('get_brand_by_query_var_brand') ? get_brand_by_query_var_brand() : false;
  $color_obj = function_exists('get_color_by_query_var_color') ? get_color_by_query_var_color() : false;
  $material_obj = function_exists('get_material_by_query_var_material') ? get_material_by_query_var_material() : false;

  $highlighted = get_query_var('highlighted');

  // “статическая” по ТЗ = ровно один из brand/color/material
  $cnt = 0;
  if ($brand_obj)
    $cnt++;
  if ($color_obj)
    $cnt++;
  if ($material_obj)
    $cnt++;

  if ($cnt !== 1)
    return array();
  if ($highlighted)
    return array(); // highlighted к статике не относим

  // --- [назва розділу] для шаблона ---
  $section = '';
  $section_mode = 'raw';
  $section_audience_part = '';
  $section_category_part = '';
  $show_audience_before_category = true;
  $aud_slug = $aud_obj && !empty($aud_obj->slug) ? (string) $aud_obj->slug : '';

  if ($pc_obj && !empty($pc_obj->term_id)) {
    $show_flag = get_field('show_audience_before_category_on_filter_pages', 'product_category_' . (int) $pc_obj->term_id);
    if ($show_flag !== null && $show_flag !== '') {
      $show_audience_before_category = (bool) $show_flag;
    }
  }

  // если есть категория — пробуем взять кастомный h1 из ACF у product_category
  if ($pc_obj && !empty($pc_obj->term_id) && !empty($pc_obj->name)) {

    if (!$show_audience_before_category) {
      $section = (string) $pc_obj->name;
      $section_mode = 'category_only';
      $section_category_part = (string) $pc_obj->name;
    } else {

      // audience slug: women|men|children
      $field = '';
      if ($aud_slug === 'women') {
        $field = 'women_seo_h1_ua';
      } elseif ($aud_slug === 'men') {
        $field = 'men_seo_h1_ua';
      } elseif ($aud_slug === 'children') {
        $field = 'children_seo_h1_ua';
      } else {
        $field = 'women_seo_h1_ua';
      }

      $term_id_prefixed = 'product_category_' . (int) $pc_obj->term_id;

      $custom_section = get_field($field, $term_id_prefixed);

      if ($custom_section) {
        $section = trim((string) $custom_section);
        $section_mode = 'custom';
      } else {
        // fallback как раньше (твой текущий подход)
        $aud_full = get_field('audience_category_fullname', 'audience_category_' . (int) $aud_obj->term_id);
        $aud_part = $aud_full ? (string) $aud_full : (string) $aud_obj->name;

        // если у тебя сейчас было просто $pc_obj->name — можешь вернуть так же.
        // Я оставляю более "человеческий" вариант: аудитория + категория.
        $section_audience_part = (string) $aud_part;
        $section_category_part = (string) $pc_obj->name;
        $section_mode = 'audience_plus_category';
        $section = trim($section_audience_part . ' ' . $section_category_part);
        if (!$section) {
          $section = (string) $pc_obj->name;
          $section_mode = 'category_only';
          $section_category_part = (string) $pc_obj->name;
        }
      }
    }

  } else {
    // нет категории — fallback на fullname аудитории
    $aud_full = get_field('audience_category_fullname', 'audience_category_' . (int) $aud_obj->term_id);
    $section = $aud_full ? (string) $aud_full : (string) $aud_obj->name;
    $section_mode = 'audience_only';
    $section_audience_part = (string) $section;
  }

  $filter_name = '';
  $filter_value = '';
  $filter_code = '';
  $brand_id = 0;
  $color_ids = array();
  $material_ids = array();

  $lang = impetus_catalog_lang();

  if ($brand_obj) {
    $filter_name = 'бренд';
    $filter_value = $brand_obj->name;
    $filter_code = 'brand';
    $brand_id = (int) $brand_obj->term_id;
  }

  if ($color_obj) {
    $filter_name = 'колір';
    $filter_value = $color_obj->name;
    $filter_value = mb_strtolower((string) $filter_value, 'UTF-8');
    $filter_code = 'color';
    $color_ids = array((int) $color_obj->term_id);
  }

  if ($material_obj) {
    $filter_name = 'матеріал';
    $filter_value = $material_obj->name;
    $filter_value = mb_strtolower((string) $filter_value, 'UTF-8');
    $filter_code = 'material';
    $material_ids = array((int) $material_obj->term_id);
  }

  // минимальная актуальная цена (со скидкой)
  $args = array(
    'audience_category' => (int) $aud_obj->term_id,
    'product_category' => $pc_obj ? (int) $pc_obj->term_id : 0,
    'brand_id' => $brand_id,
    'color_ids' => $color_ids,
    'material_ids' => $material_ids,
    'post_status' => 'publish',
    'per_page' => false
  );

  $products = get_products($args);

  $min_price = 0;
  $in_stock_count = 0;

  if (!empty($products)) {
    foreach ($products as $p) {

      // "в наявності" = product_status не пустой (у тебя по проекту так везде проверяется)
      if (!empty($p->product_status)) {
        $in_stock_count++;

        $price = isset($p->product_final_price) ? (int) $p->product_final_price : 0;
        if ($price <= 0)
          continue;

        if (!$min_price || $price < $min_price) {
          $min_price = $price;
        }
      }
    }
  }

  return array(
    'lang' => $lang,
    'seo_key' => impetus_catalog_seo_key(),
    'section' => (string) $section,
    'section_mode' => (string) $section_mode,
    'section_audience_part' => (string) $section_audience_part,
    'section_category_part' => (string) $section_category_part,
    'audience_slug' => (string) $aud_slug,
    'show_audience_before_category' => $show_audience_before_category ? 1 : 0,
    'filter_code' => (string) $filter_code,
    'filter_name' => (string) $filter_name,
    'filter_value' => (string) $filter_value,
    'min_price' => (int) $min_price,
    'in_stock_count' => (int) $in_stock_count,
  );
}

function impetus_catalog_resolve_lang_code($lang)
{
  $lang = mb_strtolower(trim((string) $lang), 'UTF-8');

  if (function_exists('impetus_trp_resolve_lang_code')) {
    $resolved = impetus_trp_resolve_lang_code($lang);
    if (!empty($resolved) && is_string($resolved)) {
      return $resolved;
    }
  }

  if ($lang === 'ru') {
    return 'ru_RU';
  }

  if ($lang === 'en') {
    return 'en_GB';
  }

  return 'uk';
}

function impetus_catalog_translate_for_lang($value, $lang)
{
  $value = (string) $value;
  if ($value === '') {
    return '';
  }

  $lang_code = impetus_catalog_resolve_lang_code($lang);

  if (function_exists('impetus_trp_translate_value')) {
    $translated = impetus_trp_translate_value($value, $lang_code);
    if (is_string($translated) && $translated !== '') {
      return $translated;
    }
  }

  if (function_exists('trp_translate')) {
    $translated = trp_translate($value, $lang_code, false);
    if (is_string($translated) && $translated !== '') {
      return $translated;
    }
  }

  return $value;
}

function impetus_catalog_get_audience_label_by_lang($aud_slug, $lang)
{
  $aud_slug = mb_strtolower(trim((string) $aud_slug), 'UTF-8');
  $lang = mb_strtolower(trim((string) $lang), 'UTF-8');

  if ($lang === 'ru') {
    if ($aud_slug === 'women')
      return 'Женские товары';
    if ($aud_slug === 'men')
      return 'Мужские товары';
    if ($aud_slug === 'children')
      return 'Детские товары';
  }

  if ($lang === 'en') {
    if ($aud_slug === 'women')
      return 'Women products';
    if ($aud_slug === 'men')
      return 'Men products';
    if ($aud_slug === 'children')
      return 'Children products';
  }

  return '';
}

function impetus_catalog_build_section_by_lang($ctx, $lang)
{
  $lang = mb_strtolower(trim((string) $lang), 'UTF-8');

  $section = isset($ctx['section']) ? (string) $ctx['section'] : '';
  $mode = isset($ctx['section_mode']) ? (string) $ctx['section_mode'] : 'raw';
  $aud_slug = isset($ctx['audience_slug']) ? (string) $ctx['audience_slug'] : '';
  $aud_part = isset($ctx['section_audience_part']) ? (string) $ctx['section_audience_part'] : '';
  $cat_part = isset($ctx['section_category_part']) ? (string) $ctx['section_category_part'] : '';

  if ($mode === 'category_only' && $cat_part !== '') {
    $translated_cat = impetus_catalog_translate_for_lang($cat_part, $lang);
    return $translated_cat !== '' ? $translated_cat : $cat_part;
  }

  if ($mode === 'audience_only' && $aud_part !== '') {
    $aud_label = impetus_catalog_get_audience_label_by_lang($aud_slug, $lang);
    if ($aud_label !== '') {
      return $aud_label;
    }
    $translated_aud = impetus_catalog_translate_for_lang($aud_part, $lang);
    return $translated_aud !== '' ? $translated_aud : $aud_part;
  }

  if ($mode === 'audience_plus_category') {
    $aud_label = impetus_catalog_get_audience_label_by_lang($aud_slug, $lang);
    if ($aud_label === '' && $aud_part !== '') {
      $aud_label = impetus_catalog_translate_for_lang($aud_part, $lang);
      if ($aud_label === '') {
        $aud_label = $aud_part;
      }
    }

    $cat_label = '';
    if ($cat_part !== '') {
      $cat_label = impetus_catalog_translate_for_lang($cat_part, $lang);
      if ($cat_label === '') {
        $cat_label = $cat_part;
      }
    }

    $composed = trim($aud_label . ' ' . $cat_label);
    if ($composed !== '') {
      return $composed;
    }
  }

  $translated_section = impetus_catalog_translate_for_lang($section, $lang);
  if ($translated_section !== '') {
    return $translated_section;
  }

  return $section;
}

function impetus_catalog_get_filter_label_by_lang($ctx, $lang)
{
  $lang = mb_strtolower(trim((string) $lang), 'UTF-8');
  $code = isset($ctx['filter_code']) ? (string) $ctx['filter_code'] : '';

  if (!$code) {
    return isset($ctx['filter_name']) ? (string) $ctx['filter_name'] : '';
  }

  if ($lang === 'ru') {
    if ($code === 'brand')
      return 'бренд';
    if ($code === 'color')
      return 'цвет';
    if ($code === 'material')
      return 'материал';
  }

  if ($lang === 'en') {
    if ($code === 'brand')
      return 'brand';
    if ($code === 'color')
      return 'color';
    if ($code === 'material')
      return 'material';
  }

  if ($code === 'brand')
    return 'бренд';
  if ($code === 'color')
    return 'колір';
  if ($code === 'material')
    return 'матеріал';

  return isset($ctx['filter_name']) ? (string) $ctx['filter_name'] : '';
}

function impetus_catalog_build_meta_ua($ctx)
{
  if (empty($ctx['section']) || empty($ctx['filter_name']) || $ctx['filter_value'] === '') {
    return array();
  }

  $base = $ctx['section'] . ': ' . $ctx['filter_name'] . ' - ' . $ctx['filter_value'];

  $title = $base . ' купити по вигідній ціні в Україні';

  $desc = $base . ' замовити в ⏩ Underline Store ✔️ ' . $base;
  if (!empty($ctx['min_price'])) {
    $desc .= ' за ціною від ' . (int) $ctx['min_price'] . ' грн ⭐ Європейська якість ▶️ Доставка по всій Україні.';
  } else {
    $desc .= ' ⭐ Європейська якість ▶️ Доставка по всій Україні.';
  }

  return array(
    'title' => $title,
    'description' => $desc,
    'h1' => $base,
  );
}

function impetus_catalog_build_meta_ru($ctx)
{
  if (empty($ctx['section']) || $ctx['filter_value'] === '') {
    return array();
  }

  $section = impetus_catalog_build_section_by_lang($ctx, 'ru');

  $filter_name = impetus_catalog_get_filter_label_by_lang($ctx, 'ru');

  $filter_value = impetus_catalog_translate_for_lang((string) $ctx['filter_value'], 'ru');
  if ($filter_value === '') {
    $filter_value = (string) $ctx['filter_value'];
  }
  $filter_value = mb_strtolower((string) $filter_value, 'UTF-8');

  $base = $section . ': ' . $filter_name . ' - ' . $filter_value;

  $title = $base . ' купить по выгодной цене в Украине';

  $desc = $base . ' заказать в ⏩ Underline Store ✔️ ' . $base;
  if (!empty($ctx['min_price'])) {
    $desc .= ' по цене от ' . (int) $ctx['min_price'] . ' грн ⭐ Европейское качество ▶️ Доставка по всей Украине.';
  } else {
    $desc .= ' ⭐ Европейское качество ▶️ Доставка по всей Украине.';
  }

  return array(
    'title' => $title,
    'description' => $desc,
    'h1' => $base,
  );
}

function impetus_catalog_build_meta_en($ctx)
{
  if (empty($ctx['section']) || $ctx['filter_value'] === '') {
    return array();
  }

  $section = impetus_catalog_build_section_by_lang($ctx, 'en');

  $filter_name = impetus_catalog_get_filter_label_by_lang($ctx, 'en');

  $filter_value = impetus_catalog_translate_for_lang((string) $ctx['filter_value'], 'en');
  if ($filter_value === '') {
    $filter_value = (string) $ctx['filter_value'];
  }
  $filter_value = mb_strtolower((string) $filter_value, 'UTF-8');

  $base = $section . ': ' . $filter_name . ' - ' . $filter_value;

  $title = $base . ' buy at a great price in Ukraine';

  $desc = $base . ' order at ⏩ Underline Store ✔️ ' . $base;
  if (!empty($ctx['min_price'])) {
    $desc .= ' from ' . (int) $ctx['min_price'] . ' UAH ⭐ European quality ▶️ Delivery across Ukraine.';
  } else {
    $desc .= ' ⭐ European quality ▶️ Delivery across Ukraine.';
  }

  return array(
    'title' => $title,
    'description' => $desc,
    'h1' => $base,
  );
}

function impetus_catalog_build_meta_by_lang($ctx, $lang = '')
{
  $lang = $lang ? $lang : impetus_catalog_lang();
  $lang = mb_strtolower(trim((string) $lang), 'UTF-8');

  if ($lang === 'ru') {
    return impetus_catalog_build_meta_ru($ctx);
  }

  if ($lang === 'en') {
    return impetus_catalog_build_meta_en($ctx);
  }

  return impetus_catalog_build_meta_ua($ctx);
}

function impetus_catalog_desired_robots_array()
{
  if (!is_page(CATALOG_PAGE_ID)) {
    return array();
  }

  $brand = get_query_var('brand');
  $color = get_query_var('color');
  $material = get_query_var('material');
  $highlighted = get_query_var('highlighted');
  $search = get_query_var('search');

  // есть ли вообще "фильтр" в URL/запросе (product_category НЕ считаем фильтром)
  $has_filter = ($brand !== '' || $color !== '' || $material !== '' || $highlighted !== '' || $search !== '');
  if (!$has_filter) {
    return array(); // обычные страницы каталога/категории не трогаем
  }

  // "статическая" по ТЗ = ровно один из brand/color/material + без highlighted + без search
  $cnt = 0;
  if ($brand !== '')
    $cnt++;
  if ($color !== '')
    $cnt++;
  if ($material !== '')
    $cnt++;

  $is_single_static = ($cnt === 1 && $highlighted === '' && $search === '');

  if ($is_single_static) {
    $ctx = function_exists('impetus_catalog_get_static_filter_context') ? impetus_catalog_get_static_filter_context() : array();
    $in_stock = !empty($ctx['in_stock_count']) ? (int) $ctx['in_stock_count'] : 0;

    // ✅ индексируем только если >= 3 в наличии
    if ($in_stock >= 3) {
      return array(
        'index',
        'follow'
      );
    }

    // < 3 в наличии — страница существует, но закрыта
    return array(
      'noindex',
      'nofollow'
    );
  }

  // все остальные фильтры/комбинации (brand+color, highlighted, и т.п.) — исключаем из индекса
  return array(
    'noindex',
    'nofollow'
  );
}
