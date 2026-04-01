<?php
// Регистрируем кастомный фид для Facebook Catalog
add_action('init', function () {
  // общий каталог (как был)
  add_feed('facebook_catalog', 'impetus_facebook_catalog_feed');

  // жіночий одяг
  add_feed('facebook_catalog_women', function () {
    impetus_facebook_catalog_feed('female');
  });

  // чоловічий одяг
  add_feed('facebook_catalog_men', function () {
    impetus_facebook_catalog_feed('male');
  });


  // ===== Google XML UA/RU (TranslatePress) =====
  add_feed('googleua', function () {
    impetus_facebook_catalog_feed('', 'ua');
  });

  add_feed('googleru', function () {
    impetus_facebook_catalog_feed('', 'ru');
  });
});




// ====== МАППИНГ GENDER / AGE GROUP ПО АУДИТОРІЇ ======

function impetus_get_gender_from_audience($term)
{
  if (!$term || !isset($term->name)) {
    return '';
  }

  $name = mb_strtolower(trim($term->name), 'UTF-8');

  // чоловіки / men / мужская
  if (strpos($name, 'чолов') !== false || strpos($name, 'чол.') !== false || strpos($name, 'men') !== false || strpos($name, 'man') !== false || strpos($name, 'чол ') !== false) {
    return 'male';
  }

  // жінки / women / женская / дівчата
  if (strpos($name, 'жін') !== false || strpos($name, 'жен') !== false || strpos($name, 'women') !== false || strpos($name, 'woman') !== false || strpos($name, 'дівч') !== false) {
    return 'female';
  }

  // унісекс
  if (strpos($name, 'унісекс') !== false || strpos($name, 'unisex') !== false) {
    return 'unisex';
  }

  return ''; // если не распознали — лучше не слать ничего
}

function impetus_get_age_group_from_audience($term)
{
  // По умолчанию — дорослі
  $age = 'adult';

  if (!$term || !isset($term->name)) {
    return $age;
  }

  $name = mb_strtolower(trim($term->name), 'UTF-8');

  // Дітям / дитячий / kids / teen / підлітки → kids
  if (
    strpos($name, 'діт') !== false ||      // Дітям, Дітям і підліткам і т.п.
    strpos($name, 'дитяч') !== false ||    // дитячий, дитяча білизна
    strpos($name, 'kids') !== false ||
    strpos($name, 'teen') !== false ||
    strpos($name, 'підліт') !== false
  ) {
    $age = 'kids';
  }

  return $age;
}







function impetus_trp_get_settings()
{
  if (!class_exists('TRP_Translate_Press')) {
    return array();
  }

  $trp = TRP_Translate_Press::get_trp_instance();
  if (!$trp) {
    return array();
  }

  $settings_component = $trp->get_component('settings');
  if (!$settings_component) {
    return array();
  }

  $settings = $settings_component->get_settings();
  if (!$settings || !is_array($settings)) {
    return array();
  }

  return $settings;
}

function impetus_trp_resolve_lang_code($short)
{
  $short = (string) $short;
  $short = mb_strtolower(trim($short), 'UTF-8');

  if (!$short) {
    return '';
  }

  // если передали уже полноценный code типа uk_UA / ru_RU
  if (strpos($short, '_') !== false) {
    return $short;
  }

  $settings = impetus_trp_get_settings();
  if (!$settings) {
    return '';
  }

  $langs = array();

  if (!empty($settings['default-language'])) {
    $langs[] = $settings['default-language'];
  }

  if (!empty($settings['translation-languages'])) {
    if (is_array($settings['translation-languages'])) {
      foreach ($settings['translation-languages'] as $lc) {
        if ($lc) {
          $langs[] = $lc;
        }
      }
    }
  }

  if (!$langs) {
    return '';
  }

  // ua -> ищем uk / ua
  if ($short === 'ua') {
    foreach ($langs as $code) {
      $slug = mb_strtolower(preg_replace('/_.*/', '', (string) $code), 'UTF-8');
      if ($slug === 'uk' || $slug === 'ua') {
        return $code;
      }
    }
  }

  // ru -> ищем ru
  if ($short === 'ru') {
    foreach ($langs as $code) {
      $slug = mb_strtolower(preg_replace('/_.*/', '', (string) $code), 'UTF-8');
      if ($slug === 'ru') {
        return $code;
      }
    }
  }

  // fallback
  if (!empty($settings['default-language'])) {
    return $settings['default-language'];
  }

  return '';
}

function impetus_trp_translate_value($value, $lang_code)
{
  if (!$value) {
    return $value;
  }

  if (!$lang_code) {
    return $value;
  }

  if (!function_exists('trp_translate')) {
    return $value;
  }

  return trp_translate($value, $lang_code, false);
}




/**
 * Helper: get preferred image ID for social (Instagram gallery -> featured image)
 */
function impetus_get_product_instagram_image_id($product_id)
{
  $product_id = (int) $product_id;
  if (!$product_id)
    return 0;

  $id = 0;

  if (function_exists('get_field')) {
    $ig_gallery = get_field('product_gallery_instagram', $product_id);

    if ($ig_gallery && is_array($ig_gallery)) {
      foreach ($ig_gallery as $it) {
        if (is_array($it) && !empty($it['ID'])) {
          $id = (int) $it['ID'];
          break;
        }
        if (is_numeric($it)) {
          $id = (int) $it;
          break;
        }
      }
    }
  }

  if (!$id)
    $id = (int) get_post_thumbnail_id($product_id);

  return $id;
}

/**
 * Yoast: put our image FIRST in og:image list (with width/height)
 * Important: adds image to TOP, so Meta will pick it first.
 */
add_filter('wpseo_add_opengraph_images', function ($image_container) {

  if (!is_singular('product'))
    return;

  $product_id = (int) get_queried_object_id();
  $img_id = impetus_get_product_instagram_image_id($product_id);

  // ВАЖНО: если в IG-галерее пусто и нет thumbnail — ничего не трогаем
  if (!$img_id)
    return;

  if (is_object($image_container) && method_exists($image_container, 'add_image_by_id')) {
    $image_container->add_image_by_id($img_id);
  }

}, 10);

/**
 * Yoast: twitter image (на всякий случай)
 */
add_filter('wpseo_twitter_image', function ($img) {

  if (!is_singular('product'))
    return $img;

  $product_id = (int) get_queried_object_id();
  $img_id = impetus_get_product_instagram_image_id($product_id);
  if (!$img_id)
    return $img;

  $url = function_exists('wp_get_original_image_url') ? wp_get_original_image_url($img_id) : '';
  if (!$url)
    $url = wp_get_attachment_url($img_id);

  return $url ?: $img;

}, 10);

/**
 * (опционально) Yoast: og:type для товара (сейчас у вас "article")
 */
add_filter('wpseo_opengraph_type', function ($type) {
  return is_singular('product') ? 'product' : $type;
}, 10);






// Основная функция фида
function impetus_facebook_catalog_feed($gender_filter = '', $lang = '')
{
  if (!function_exists('get_field')) {
    return;
  }

  header('Content-Type: application/xml; charset=' . get_option('blog_charset'), true);




  $lang_code = impetus_trp_resolve_lang_code($lang);

  $can_switch = false;
  if (function_exists('trp_switch_language')) {
    if (function_exists('trp_restore_language')) {
      $can_switch = true;
    }
  }

  if ($can_switch) {
    if ($lang_code) {
      trp_switch_language($lang_code);
    }
  }




  $args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'perm' => 'readable',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
  );

  $q = new WP_Query($args);

  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  ?>
  <rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
      <title>
        <?php echo wp_strip_all_tags(get_bloginfo('name')); ?> – Facebook Catalog
      </title>
      <link>
      <?php echo esc_url(home_url('/')); ?>
      </link>
      <description>Product feed for Facebook / Instagram Catalog</description>
      <?php
      if ($q->have_posts()) {
        while ($q->have_posts()) {
          $q->the_post();

          $product_id = get_the_ID();
          $is_new_collection = get_field('is_new_collection', $product_id, false);
          // ====== ДАННЫЕ ИЗ PRODUCT GROUP (АУДИТОРІЯ, МАТЕРІАЛИ) ======
          $gender = '';
          $age_group = 'adult';
          $material_str = '';

          $product_group_id = get_field('product_group', $product_id);

          if ($product_group_id) {
            $pg_term = get_term($product_group_id, 'product_group');
            $term_id_prefixed = 'product_group_' . $product_group_id;

            // 1) Аудиторія → gender / age_group
            $audience_term = get_field('product_audience_category', $term_id_prefixed);
            if ($audience_term instanceof WP_Term) {
              $gender = impetus_get_gender_from_audience($audience_term);
              $age_group = impetus_get_age_group_from_audience($audience_term);
            }

            // 2) Матеріали → g:material
            $material_terms = get_field('product_material', $term_id_prefixed);
            if ($material_terms && is_array($material_terms)) {
              $material_names = array();

              foreach ($material_terms as $mt) {
                if ($mt instanceof WP_Term && !empty($mt->name)) {
                  $material_names[] = trim($mt->name);
                }
              }

              if (!empty($material_names)) {
                // Типичный формат для фида: "Cotton/Elastane"
                $material_str = implode('/', $material_names);
              }
            }
          }





          // если этот фид фильтруется по полу — пропускаем товары с другим gender
          if ($gender_filter) {
            if ($gender !== $gender_filter) {
              continue;
            }
          }






          // ---------- Базовый ID товара ----------
          $article = get_field('product_article', $product_id);
          $base_item_id = $article ? (string) $article : (string) $product_id;

          // ---------- Група товару (item_group_id) ----------
          $group_term_id = get_field('product_group', $product_id);
          $group_term = null;

          if ($group_term_id) {
            $group_term = get_term($group_term_id, 'product_group');
          }

          // item_group_id теперь = один конкретный товар/цвет
// то есть разные цвета будут в разных группах
          $item_group_id = $base_item_id;

          // ---------- Описание ----------
          $description = '';

          if ($group_term && !is_wp_error($group_term)) {
            $term_id_prefixed = 'product_group_' . $group_term->term_id;
            $desc_group = get_field('product_description', $term_id_prefixed);
            if ($desc_group) {
              $description = $desc_group;
            }
          }

          if (!$description) {
            $excerpt = get_the_excerpt();
            if ($excerpt) {
              $description = $excerpt;
            }
          }

          if (!$description) {
            $content = get_the_content(null, false, $product_id);
            if ($content) {
              $description = $content;
            }
          }

          $description = wp_strip_all_tags($description);

          // ---------- Цена / промо-цена ----------
          $price_raw = get_field('product_price', $product_id);        // старая/обычная
          $promo_raw = get_field('product_promo_price', $product_id);  // акционная
    
          $price_num = 0.0;
          $promo_num = 0.0;

          if ($price_raw) {
            $price_num = floatval(str_replace(',', '.', (string) $price_raw));
          }

          if ($promo_raw) {
            $promo_num = floatval(str_replace(',', '.', (string) $promo_raw));
          }

          $currency = 'UAH';

          // g:price — ВСЕГДА обычная (старая) цена, если она есть
// если обычной цены нет — тогда отдаём то, что есть (promo)
          $base_num = $price_num > 0 ? $price_num : ($promo_num > 0 ? $promo_num : 0);
          $price_str = number_format($base_num, 2, '.', '') . ' ' . $currency;

          // g:sale_price — только если promo реально меньше base
          $sale_price = '';
          if ($promo_num > 0 && $price_num > 0 && $promo_num < ($price_num - 0.0001)) {
            $sale_price = number_format($promo_num, 2, '.', '') . ' ' . $currency;
          }


          // ---------- Наявність ----------
          $status_field = get_field('product_status', $product_id);
          $expected_field = get_field('is_product_expected', $product_id);

          // У фід віддаємо тільки товари, які є в наявності
          if (empty($status_field)) {
            continue;
          }

          if ($expected_field) {
            continue;
          }

          $availability = 'in stock';

          // ---------- Бренд (из product_group) ----------
          $brand_name = '';

          if ($group_term && !is_wp_error($group_term)) {
            $term_id_prefixed = 'product_group_' . $group_term->term_id;
            $brand_term = get_field('product_brand', $term_id_prefixed);

            if ($brand_term) {
              if (!is_wp_error($brand_term)) {
                $brand_name = $brand_term->name;
              }
            }
          }

          // ---------- product_type (категории из product_group) ----------
          $product_type = '';

          if ($group_term && !is_wp_error($group_term)) {
            $term_id_prefixed = 'product_group_' . $group_term->term_id;
            $cats = get_field('product_product_category', $term_id_prefixed);

            if ($cats) {
              if (is_array($cats)) {
                $names = array();
                foreach ($cats as $cat_term) {
                  if ($cat_term) {
                    $names[] = $cat_term->name;
                  }
                }
                if (!empty($names)) {
                  $product_type = implode(' > ', $names);
                }
              }
            }
          }

          // ---------- Цвет ----------
          $color_value = '';
          $color_term = get_field('product_color', $product_id);

          if ($color_term) {
            if (!is_wp_error($color_term)) {
              $color_value = $color_term->name;
            }
          }

          // ---------- Размеры ----------
          $size_terms = get_field('product_size_all', $product_id);

          if ($size_terms && !is_array($size_terms)) {
            $size_terms = array($size_terms);
          }

          $variant_sizes = array();

          if ($size_terms && is_array($size_terms)) {
            foreach ($size_terms as $st) {
              if ($st instanceof WP_Term) {
                $variant_sizes[] = $st;
                continue;
              }

              if (is_numeric($st)) {
                $size_term = get_term((int) $st, 'size');

                if ($size_term && !is_wp_error($size_term)) {
                  $variant_sizes[] = $size_term;
                }
              }
            }
          }

          if (!$variant_sizes) {
            $variant_sizes[] = null;
          }

          // ---------- Картинки ----------
          $image_link = '';
          $thumb_id = get_post_thumbnail_id($product_id);
          $ig_additional_ids = array();

          // 1) Пытаемся взять рекламную галерею (ACF Gallery)
          $ig_gallery = get_field('product_gallery_instagram', $product_id);

          // Нормализуем: ACF Gallery может вернуть массив ID или массив массивов (attachment arrays)
          $ig_ids = array();

          if ($ig_gallery && is_array($ig_gallery)) {
            foreach ($ig_gallery as $it) {
              if (is_array($it) && !empty($it['ID'])) {
                $ig_ids[] = (int) $it['ID'];
              } elseif (is_numeric($it)) {
                $ig_ids[] = (int) $it;
              }
            }
            $ig_ids = array_values(array_unique(array_filter($ig_ids)));
          }

          // 2) Если есть рекламные фото — используем их
          if (!empty($ig_ids)) {

            $first_id = $ig_ids[0];
            $first_url = wp_get_attachment_image_url($first_id, 'full');
            if ($first_url) {
              $image_link = $first_url;
            }

            // additional — остальные из рекламной галереи
            // (первый не дублируем)
            $ig_additional_ids = array_slice($ig_ids, 1);

          } else {

            // 3) Fallback: как было сейчас — thumbnail в image_link
            if ($thumb_id) {
              $image_url = wp_get_attachment_image_url($thumb_id, 'full');
              if ($image_url) {
                $image_link = $image_url;
              }
            }

            // additional — из обычной галереи товара
            $gallery_ids = get_field('product_gallery', $product_id);
            $ig_additional_ids = array();

            if ($gallery_ids && is_array($gallery_ids)) {
              foreach ($gallery_ids as $gid) {
                if ($gid && $gid !== $thumb_id) {
                  $ig_additional_ids[] = (int) $gid;
                }
              }
            }

            $ig_additional_ids = array_values(array_unique(array_filter($ig_additional_ids)));
          }

          // ===== TranslatePress: перевод значений под язык фида =====
          $title_value = get_the_title($product_id);

          if ($lang_code) {
            $title_value = impetus_trp_translate_value($title_value, $lang_code);
            $description = impetus_trp_translate_value($description, $lang_code);

            if ($brand_name) {
              $brand_name = impetus_trp_translate_value($brand_name, $lang_code);
            }

            if ($product_type) {
              $product_type = impetus_trp_translate_value($product_type, $lang_code);
            }

            if ($color_value) {
              $color_value = impetus_trp_translate_value($color_value, $lang_code);
            }



            if ($material_str) {
              $material_str = impetus_trp_translate_value($material_str, $lang_code);
            }
          }

          foreach ($variant_sizes as $size_term) {
            $variant_item_id = $base_item_id;
            $variant_title = $title_value;
            $variant_size_value = '';
            $variant_link = get_permalink($product_id);
            $variant_query_args = array();

            if ($color_term instanceof WP_Term && !empty($color_term->slug)) {
              $variant_query_args['color'] = $color_term->slug;
            }

            if ($size_term instanceof WP_Term) {
              $variant_item_id = $base_item_id . '-' . $size_term->slug;
              $variant_size_value = $size_term->name;

              if ($lang_code) {
                $variant_size_value = impetus_trp_translate_value($variant_size_value, $lang_code);
              }

              if ($variant_size_value) {
                $variant_title .= ' - ' . $variant_size_value;
              }

              $variant_query_args['size'] = $size_term->slug;
            }

            if ($variant_query_args) {
              $variant_link = add_query_arg($variant_query_args, get_permalink($product_id));
            }
            ?>
            <item>
              <g:id>
                <?php echo htmlspecialchars($variant_item_id, ENT_XML1); ?>
              </g:id>

              <g:item_group_id>
                <?php echo htmlspecialchars($item_group_id, ENT_XML1); ?>
              </g:item_group_id>

              <?php if ((string) $is_new_collection === '1'): ?>
                <g:custom_label_0>new_collection</g:custom_label_0>
              <?php endif; ?>

              <g:title>
                <?php echo htmlspecialchars($variant_title, ENT_XML1); ?>
              </g:title>

              <g:description>
                <?php echo htmlspecialchars($description, ENT_XML1); ?>
              </g:description>

              <g:link>
                <?php echo esc_url($variant_link); ?>
              </g:link>

              <?php if ($image_link): ?>
                <g:image_link>
                  <?php echo esc_url($image_link); ?>
                </g:image_link>
              <?php endif; ?>

              <?php
              if (!empty($ig_additional_ids)) {
                foreach ($ig_additional_ids as $aid) {
                  $u = wp_get_attachment_image_url($aid, 'full');

                  if ($u) {
                    ?>
                    <g:additional_image_link>
                      <?php echo esc_url($u); ?>
                    </g:additional_image_link>
                    <?php
                  }
                }
              }
              ?>

              <g:condition>new</g:condition>

              <g:availability>
                <?php echo $availability; ?>
              </g:availability>

              <g:price>
                <?php echo $price_str; ?>
              </g:price>

              <?php if ($sale_price): ?>
                <g:sale_price>
                  <?php echo $sale_price; ?>
                </g:sale_price>
              <?php endif; ?>

              <?php if ($brand_name): ?>
                <g:brand>
                  <?php echo htmlspecialchars($brand_name, ENT_XML1); ?>
                </g:brand>
              <?php endif; ?>

              <?php if ($product_type): ?>
                <g:product_type>
                  <?php echo htmlspecialchars($product_type, ENT_XML1); ?>
                </g:product_type>
              <?php endif; ?>

              <?php if ($color_value): ?>
                <g:color>
                  <?php echo htmlspecialchars($color_value, ENT_XML1); ?>
                </g:color>
              <?php endif; ?>

              <?php if ($gender): ?>
                <g:gender>
                  <?php echo $gender; ?>
                </g:gender>
              <?php endif; ?>

              <?php if ($age_group): ?>
                <g:age_group>
                  <?php echo $age_group; ?>
                </g:age_group>
              <?php endif; ?>

              <?php if ($material_str): ?>
                <g:material>
                  <?php echo htmlspecialchars($material_str, ENT_XML1); ?>
                </g:material>
              <?php endif; ?>

              <?php if ($variant_size_value): ?>
                <g:size>
                  <?php echo htmlspecialchars($variant_size_value, ENT_XML1); ?>
                </g:size>
              <?php endif; ?>
            </item>
            <?php
          }
        } // while
        wp_reset_postdata();
      } // if have_posts
      if ($can_switch) {
        if ($lang_code) {
          trp_restore_language();
        }
      }
      ?>
    </channel>
  </rss>
  <?php
}
