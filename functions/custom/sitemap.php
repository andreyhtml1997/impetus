<?php
// =====================================================
// YOAST SITEMAP: indexable статические фильтры (brand/color/material) с >=3 in-stock
// Требует Yoast SEO XML sitemaps (sitemap_index.xml).
// =====================================================


// =====================================================
// YOAST: регистрируем кастомный sitemap и добавляем его в sitemap_index.xml
// =====================================================

// 1) Регистрируем sitemap в Yoast (правильный хук)
add_action('wpseo_register_extra_sitemaps', function () {

  global $wpseo_sitemaps;

  if (!$wpseo_sitemaps) {
    return;
  }

  if (!method_exists($wpseo_sitemaps, 'register_sitemap')) {
    return;
  }

  // Имя sitemap => будет /catalog_filters-sitemap.xml

}, 20);




// --- кэш ---
function impetus_catalog_filters_sitemap_cache_key()
{
  return 'impetus_catalog_filters_sitemap_v1';
}
function impetus_catalog_filters_sitemap_bust_cache()
{
  delete_transient(impetus_catalog_filters_sitemap_cache_key());
}

// сбрасываем кэш при изменениях товаров/групп
add_action('save_post_product', function () {
  impetus_catalog_filters_sitemap_bust_cache();
}, 10);

add_action('updated_term_meta', function () {
  impetus_catalog_filters_sitemap_bust_cache();
}, 10);
add_action('added_term_meta', function () {
  impetus_catalog_filters_sitemap_bust_cache();
}, 10);
add_action('deleted_term_meta', function () {
  impetus_catalog_filters_sitemap_bust_cache();
}, 10);

/**
 * Сбор всех indexable URL для sitemap.
 * Логика:
 * - считаем только publish товары
 * - "в наличии" = product_status не пустой
 * - добавляем URL только если count(in_stock) >= 3
 * - генерим URL только для одиночных фильтров: brand OR color OR material (category допускается)
 */
function impetus_catalog_filters_sitemap_get_entries()
{

  $cache_key = impetus_catalog_filters_sitemap_cache_key();
  $cached = get_transient($cache_key);
  if (is_array($cached)) {
    return $cached;
  }

  global $wpdb;

  // 1) вытащим минимум меты для товаров
  $rows = $wpdb->get_results("
		SELECT p.ID as post_id, p.post_modified_gmt as modified_gmt, pm.meta_key, pm.meta_value
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		WHERE p.post_type = 'product'
		  AND p.post_status = 'publish'
		  AND pm.meta_key IN ('product_group','product_status','product_color')
	", ARRAY_A);

  if (empty($rows)) {
    set_transient($cache_key, array(), 6 * HOUR_IN_SECONDS);
    return array();
  }

  $posts = array(); // post_id => ['group'=>, 'status'=>, 'color'=>, 'ts'=>]
  foreach ($rows as $r) {
    $pid = (int) $r['post_id'];
    if (!isset($posts[$pid])) {
      $ts = 0;
      if (!empty($r['modified_gmt'])) {
        $ts = strtotime($r['modified_gmt'] . ' UTC');
      }
      $posts[$pid] = array(
        'group' => 0,
        'status' => '',
        'color' => 0,
        'ts' => $ts ?: 0,
      );
    }

    $key = (string) $r['meta_key'];
    $val = maybe_unserialize($r['meta_value']);

    if ($key === 'product_group') {
      $posts[$pid]['group'] = (int) $val;
    } elseif ($key === 'product_status') {
      $posts[$pid]['status'] = (string) $val;
    } elseif ($key === 'product_color') {
      if (is_array($val)) {
        $val = reset($val);
      }
      $posts[$pid]['color'] = (int) $val;
    }
  }

  // 2) берем только товары "в наличии" и собираем group_ids
  $group_ids = array();
  foreach ($posts as $p) {
    if (!empty($p['status']) && !empty($p['group'])) {
      $group_ids[(int) $p['group']] = true;
    }
  }
  if (empty($group_ids)) {
    set_transient($cache_key, array(), 6 * HOUR_IN_SECONDS);
    return array();
  }

  $group_in = implode(',', array_map('intval', array_keys($group_ids)));

  // 3) termmeta групп (где хранится audience/category/brand/material)
  $trows = $wpdb->get_results("
		SELECT term_id, meta_key, meta_value
		FROM {$wpdb->termmeta}
		WHERE term_id IN ($group_in)
		  AND meta_key IN ('product_audience_category','product_product_category','product_brand','product_material')
	", ARRAY_A);

  $groups = array(); // group_id => meta array
  foreach ($trows as $tr) {
    $gid = (int) $tr['term_id'];
    $k = (string) $tr['meta_key'];
    $v = maybe_unserialize($tr['meta_value']);
    if (!isset($groups[$gid])) {
      $groups[$gid] = array();
    }
    $groups[$gid][$k] = $v;
  }

  // 4) считаем count+lastmod по страницам
  $bucket = array(); // key => ['type','aud','cat','term','count','ts']

  $touch = static function (&$bucket, $type, $aud, $cat, $term, $ts) {
    $key = $type . '|' . (int) $aud . '|' . (int) $cat . '|' . (int) $term;

    if (!isset($bucket[$key])) {
      $bucket[$key] = array(
        'type' => $type,
        'aud' => (int) $aud,
        'cat' => (int) $cat,
        'term' => (int) $term,
        'count' => 0,
        'ts' => 0,
      );
    }

    $bucket[$key]['count']++;

    if ((int) $ts > (int) $bucket[$key]['ts']) {
      $bucket[$key]['ts'] = (int) $ts;
    }
  };

  foreach ($posts as $p) {
    // только "в наличии"
    if (empty($p['status'])) {
      continue;
    }

    $gid = (int) $p['group'];
    if (!$gid || empty($groups[$gid])) {
      continue;
    }

    $gm = $groups[$gid];

    $aud = isset($gm['product_audience_category']) ? (int) $gm['product_audience_category'] : 0;
    if (!$aud)
      continue;

    $cats = isset($gm['product_product_category']) ? $gm['product_product_category'] : array();
    if (!is_array($cats))
      $cats = array();

    $brand = isset($gm['product_brand']) ? (int) $gm['product_brand'] : 0;

    $mats = isset($gm['product_material']) ? $gm['product_material'] : array();
    if (!is_array($mats))
      $mats = array();

    $color = (int) $p['color'];
    $ts = (int) $p['ts'];

    // audience-only: color/material/brand
    if ($color) {
      $touch($bucket, 'color', $aud, 0, $color, $ts);
    }

    foreach ($mats as $mid) {
      $mid = (int) $mid;
      if ($mid) {
        $touch($bucket, 'material', $aud, 0, $mid, $ts);
      }
    }

    if ($brand) {
      $touch($bucket, 'brand', $aud, 0, $brand, $ts);
    }

    // audience + category: color/material
    foreach ($cats as $cat) {
      $cat = (int) $cat;
      if (!$cat)
        continue;

      if ($color) {
        $touch($bucket, 'color', $aud, $cat, $color, $ts);
      }

      foreach ($mats as $mid) {
        $mid = (int) $mid;
        if ($mid) {
          $touch($bucket, 'material', $aud, $cat, $mid, $ts);
        }
      }

      // brand + category: (category идёт как brand_product_category в URL)
      if ($brand) {
        $touch($bucket, 'brand', $aud, $cat, $brand, $ts);
      }
    }
  }

  if (empty($bucket)) {
    set_transient($cache_key, array(), 6 * HOUR_IN_SECONDS);
    return array();
  }

  // 5) собираем ID терминов, чтобы получить slug
  $need_aud = array();
  $need_cat = array();
  $need_brand = array();
  $need_color = array();
  $need_mat = array();

  foreach ($bucket as $item) {
    if ((int) $item['count'] < 3)
      continue;

    $need_aud[(int) $item['aud']] = true;
    if (!empty($item['cat']))
      $need_cat[(int) $item['cat']] = true;

    if ($item['type'] === 'brand')
      $need_brand[(int) $item['term']] = true;
    if ($item['type'] === 'color')
      $need_color[(int) $item['term']] = true;
    if ($item['type'] === 'material')
      $need_mat[(int) $item['term']] = true;
  }

  if (empty($need_aud)) {
    set_transient($cache_key, array(), 6 * HOUR_IN_SECONDS);
    return array();
  }

  $aud_map = array();
  $aud_terms = get_terms(array(
    'taxonomy' => 'audience_category',
    'hide_empty' => false,
    'include' => array_keys($need_aud),
  ));
  foreach ($aud_terms as $t) {
    // только нужные аудитории
    if (
      !in_array($t->slug, array(
        'women',
        'men',
        'children'
      ), true)
    )
      continue;
    $aud_map[(int) $t->term_id] = $t->slug;
  }

  $cat_map = array();
  if (!empty($need_cat)) {
    $cat_terms = get_terms(array(
      'taxonomy' => 'product_category',
      'hide_empty' => false,
      'include' => array_keys($need_cat),
    ));
    foreach ($cat_terms as $t) {
      $cat_map[(int) $t->term_id] = $t->slug;
    }
  }

  $brand_map = array();
  if (!empty($need_brand)) {
    $brand_terms = get_terms(array(
      'taxonomy' => 'brand',
      'hide_empty' => false,
      'include' => array_keys($need_brand),
    ));
    foreach ($brand_terms as $t) {
      $brand_map[(int) $t->term_id] = $t->slug;
    }
  }

  $color_map = array();
  if (!empty($need_color)) {
    $color_terms = get_terms(array(
      'taxonomy' => 'color',
      'hide_empty' => false,
      'include' => array_keys($need_color),
    ));
    foreach ($color_terms as $t) {
      $color_map[(int) $t->term_id] = $t->slug;
    }
  }

  $mat_map = array();
  if (!empty($need_mat)) {
    $mat_terms = get_terms(array(
      'taxonomy' => 'material',
      'hide_empty' => false,
      'include' => array_keys($need_mat),
    ));
    foreach ($mat_terms as $t) {
      $mat_map[(int) $t->term_id] = $t->slug;
    }
  }

  // 6) строим URLs
  $out = array();
  foreach ($bucket as $item) {
    if ((int) $item['count'] < 3)
      continue;

    $aud_id = (int) $item['aud'];
    if (empty($aud_map[$aud_id]))
      continue;
    $aud_slug = $aud_map[$aud_id];

    $cat_id = (int) $item['cat'];
    $cat_slug = $cat_id && !empty($cat_map[$cat_id]) ? $cat_map[$cat_id] : null;

    $type = (string) $item['type'];
    $term_id = (int) $item['term'];
    $loc = '';

    if ($type === 'color') {
      if (empty($color_map[$term_id]))
        continue;
      $loc = get_catalog_url($aud_slug, $cat_slug, null, null, null, $color_map[$term_id], null);
    } elseif ($type === 'material') {
      if (empty($mat_map[$term_id]))
        continue;
      $loc = get_catalog_url($aud_slug, $cat_slug, null, null, null, null, $mat_map[$term_id]);
    } elseif ($type === 'brand') {
      if (empty($brand_map[$term_id]))
        continue;
      // если cat_slug есть — это brand + brand_product_category
      if ($cat_slug) {
        $loc = get_catalog_url($aud_slug, null, $brand_map[$term_id], $cat_slug);
      } else {
        $loc = get_catalog_url($aud_slug, null, $brand_map[$term_id]);
      }
    }

    if (!$loc)
      continue;

    $out[] = array(
      'loc' => $loc,
      'lastmod' => (int) $item['ts'],
    );
  }

  set_transient($cache_key, $out, 6 * HOUR_IN_SECONDS);

  return $out;
}

function impetus_build_catalog_filters_sitemap()
{
  global $wpseo_sitemaps;

  if (!isset($wpseo_sitemaps) || empty($wpseo_sitemaps)) {
    return;
  }

  $entries = impetus_catalog_filters_sitemap_get_entries();

  $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

  if (!empty($entries)) {
    foreach ($entries as $e) {
      if (empty($e['loc']))
        continue;

      $xml .= "<url>\n";
      $xml .= '<loc>' . esc_url($e['loc']) . "</loc>\n";

      if (!empty($e['lastmod'])) {
        $xml .= '<lastmod>' . gmdate('c', (int) $e['lastmod']) . "</lastmod>\n";
      }

      $xml .= "</url>\n";
    }
  }

  $xml .= "</urlset>";

  $wpseo_sitemaps->set_sitemap($xml);
}




add_filter('wpseo_sitemap_page_content', function ($content) {

  if (!function_exists('impetus_catalog_filters_sitemap_get_entries')) {
    return $content;
  }

  $entries = impetus_catalog_filters_sitemap_get_entries();
  if (empty($entries)) {
    return $content;
  }

  $out = '';
  foreach ($entries as $e) {
    if (empty($e['loc'])) {
      continue;
    }

    $loc = esc_url($e['loc']);
    if (!$loc) {
      continue;
    }

    $out .= "<url>\n";
    $out .= "<loc>{$loc}</loc>\n";

    if (!empty($e['lastmod'])) {
      $out .= "<lastmod>" . gmdate('c', (int) $e['lastmod']) . "</lastmod>\n";
    }

    $out .= "</url>\n";
  }

  return $content . $out;

}, 10, 1);