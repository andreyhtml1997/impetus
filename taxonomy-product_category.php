<?php



if (!defined('ABSPATH'))
  exit;

//https://under.bestclients.com.ua/catalog/women/
//https://under.bestclients.com.ua/catalog/men/
//https://under.bestclients.com.ua/catalog/women/pizhamy/
//https://under.bestclients.com.ua/catalog/women/brand_aruelle/
//https://under.bestclients.com.ua/catalog/women/brand_aruelle/pizhamy/


$audience_category_obj = get_audience_category_by_query_var_audience_category();
$product_category_obj = get_product_category_by_query_var_product_category();
$brand_obj = get_brand_by_query_var_brand();
$sort_by = get_user_cookie_sort_by();
$highlighted = get_query_var('highlighted');
$search = get_query_var('search');

$is_brand_catalog = $brand_obj !== false;


$filter_audience_category_objs = get_audience_categories();
$product_category_slug = null;
$brand_slug = null;
$product_category_id = 0;
$brand_id = 0;
$filter_brand_objs = null;
$filter_product_category_objs = get_field('product_category_filter', 'audience_category_' . $audience_category_obj->term_id);

if ($is_brand_catalog) {

  $brand_slug = $brand_obj->slug;
  $brand_id = $brand_obj->term_id;

  $filter_audience_category_objs = get_field('brand_audiences', 'brand_' . $brand_obj->term_id);
  $filter_product_category_objs = get_field('product_category_filter', 'brand_' . $brand_obj->term_id);

  if ($product_category_obj) {
    $product_category_slug = $product_category_obj->slug;
    $product_category_id = $product_category_obj->term_id;
  }

} elseif ($product_category_obj) {

  $product_category_slug = $product_category_obj->slug;
  $product_category_id = $product_category_obj->term_id;

  $filter_audience_category_objs = get_field('product_category_audiences', 'product_category_' . $product_category_obj->term_id);
}

if (!$is_brand_catalog)
  $filter_brand_objs = get_brands_by_audience_category($audience_category_obj->term_id);

$filter_size_objs = get_filter_available_params($audience_category_obj->term_id, $product_category_id, $brand_id, 'product_size');
$filter_color_objs = get_filter_available_params($audience_category_obj->term_id, $product_category_id, $brand_id, 'product_color');
$filter_material_objs = get_filter_available_params($audience_category_obj->term_id, $product_category_id, $brand_id, 'product_material');
$filter_min_max_price = get_filter_available_params($audience_category_obj->term_id, $product_category_id, $brand_id, 'product_price');

get_header();

?>

<?php if (!$is_brand_catalog): ?>
  <section class="catalog-hero">
    <div class="container-fluid">

      <?php

      $parent_pages = array();
      $current_pagename = $audience_category_obj->name;

      if ($product_category_obj) {
        $parent_pages[] = array(
          'url' => get_catalog_url($audience_category_obj->slug),
          'name' => $audience_category_obj->name,
        );
        $current_pagename = $product_category_obj->name;
      }

      ?>

      <?php
      get_template_part('templates/breadcrumbs', null, array(
        'parent_pages' => $parent_pages,
        'current_pagename' => $current_pagename,
      ));
      ?>

    </div>
  </section>
<?php endif; ?>

<section id="JS-product-filter" class="catalog-section margin-bottom <?php if ($is_brand_catalog)
  echo 'brand'; ?>">
  <div class="container-fluid">

    <?php if ($is_brand_catalog): ?>

      <?php
      get_template_part('templates/breadcrumbs', null, array(
        'parent_pages' => array(BRANDS_PAGE_ID),
        'current_pagename' => 'Бренд ' . $brand_obj->name,
      ));
      ?>

      <?php

      $brand_logo = get_field('brand_logo', 'brand_' . $brand_obj->term_id);
      $brand_gallery = get_field('brand_gallery', 'brand_' . $brand_obj->term_id);

      ?>

      <div class="d-xl-flex brand-detail">
        <div class="detail-left d-flex flex-column justify-content-between">
          <h1>Бренд <nobr><?php echo esc_html($brand_obj->name); ?></nobr>
          </h1>

          <?php if (!empty($brand_logo)): ?>
            <div class="detail-logo">
              <?php
              echo wp_get_attachment_image($brand_logo, 'full', false, array(
                'alt' => 'Image',
                'loading' => 'lazy',
                'decoding' => 'async',
              ));
              ?>
            </div>
          <?php endif; ?>

        </div>

        <?php if (!empty($brand_gallery)): ?>
          <div class="brand-gallery">
            <div class="row gutters-4">

              <?php foreach ($brand_gallery as $brand_gallery_image_id): ?>
                <div class="col-4">
                  <div class="item">
                    <?php
                    echo wp_get_attachment_image($brand_gallery_image_id, 'full', false, array(
                      'alt' => 'Image',
                      'loading' => 'lazy',
                      'decoding' => 'async',
                    ));
                    ?>
                  </div>
                </div>
              <?php endforeach; ?>

            </div>
          </div>
        <?php endif; ?>

      </div>

    <?php endif; ?>

    <?php if (!$is_brand_catalog): ?>
      <div class="h1-container d-flex flex-wrap align-items-end catalog-section">
        <h1 class="h1-anons"><?php echo get_catalog_pagetitle(); ?></h1>
      </div>
    <?php endif; ?>

    <div class="d-xl-flex">
      <div class="catalog-left">
        <div class="catalog-filter">
          <div class="filter-title d-flex d-xl-none align-items-center justify-content-between">
            <span class="value">Фільтр</span>
            <button type="button" class="filter-close d-flex align-items-center justify-content-center">
              <span class="ic icon-close"></span>
            </button>
          </div>
          <div class="filter-container">
            <form action="">

              <?php if (!empty($filter_audience_category_objs)): ?>
                <div class="filter-block">
                  <button type="button" class="filter-name w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" href="#filter-audience_category" role="button"
                    aria-expanded="false"><!--collapsed-->
                    <span class="value">Аудиторія</span>
                    <span class="ic icon-plus"></span>
                  </button>
                  <div class="collapse show" id="filter-audience_category">
                    <div class="block-container">

                      <?php foreach ($filter_audience_category_objs as $filter_audience_category_obj): ?>

                        <?php
                        //тимчасово приховуємо з сайту
                        if ($filter_audience_category_obj->term_id == 17)
                          continue;
                        ?>

                        <?php
                        get_template_part('templates/filter-checkbox-item', null, array(
                          'url' => get_catalog_url($filter_audience_category_obj->slug, null, $brand_slug),
                          'name' => $filter_audience_category_obj->name,
                          'checked' => checked($filter_audience_category_obj->term_id, $audience_category_obj->term_id, false),
                        ));
                        ?>

                      <?php endforeach; ?>

                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="filter-block">
                <button type="button" class="filter-name w-100 d-flex align-items-center justify-content-between"
                  data-toggle="collapse" href="#filter-product_category" role="button"
                  aria-expanded="false"><!--collapsed-->
                  <span class="value">Категорія</span>
                  <span class="ic icon-plus"></span>
                </button>
                <div class="collapse show" id="filter-product_category">
                  <div class="block-container">

                    <?php if (!empty($filter_product_category_objs)): ?>
                      <?php foreach ($filter_product_category_objs as $item): ?>

                        <?php

                        $_product_category_slug = $product_category_slug != $item['option']->slug ? $item['option']->slug : null;

                        $catalog_url = $is_brand_catalog ? get_catalog_url($audience_category_obj->slug, null, $brand_slug, $_product_category_slug) : get_catalog_url($audience_category_obj->slug, $_product_category_slug, $brand_slug);

                        get_template_part('templates/filter-checkbox-item', null, array(
                          'url' => $catalog_url,
                          'name' => $item['option']->name,
                          'checked' => checked($item['option']->term_id, $product_category_obj->term_id, false),
                        ));
                        ?>

                        <?php if (!empty($item['child'])): ?>
                          <div class="filter-children">

                            <?php foreach ($item['child'] as $child_item): ?>

                              <?php

                              $_product_category_slug = $product_category_slug != $child_item['option']->slug ? $child_item['option']->slug : null;

                              $catalog_url = $is_brand_catalog ? get_catalog_url($audience_category_obj->slug, null, $brand_slug, $_product_category_slug) : get_catalog_url($audience_category_obj->slug, $_product_category_slug, $brand_slug);

                              get_template_part('templates/filter-checkbox-item', null, array(
                                'url' => $catalog_url,
                                'name' => $child_item['option']->name,
                                'checked' => checked($child_item['option']->term_id, $product_category_obj->term_id, false),
                              ));
                              ?>

                            <?php endforeach; ?>

                          </div>
                        <?php endif; ?>

                      <?php endforeach; ?>
                    <?php endif; ?>


                    <?php foreach (get_highlighted_tags() as $key => $name): ?>

                      <?php

                      $highlighted_slug = $key != $highlighted ? $key : null;

                      $catalog_url = $is_brand_catalog ? get_catalog_url($audience_category_obj->slug, null, $brand_slug, null, $highlighted_slug) : get_catalog_url($audience_category_obj->slug, null, null, null, $highlighted_slug);

                      $is_highlighted_products = get_filter_available_params($audience_category_obj->term_id, 0, $brand_id, 'is_product_' . $key); //is_product_new, is_product_sale, is_product_bestseller
                      if (empty($is_highlighted_products))
                        continue;

                      get_template_part('templates/filter-checkbox-item', null, array(
                        'url' => $catalog_url,
                        'name' => $name,
                        'checked' => checked($key, $highlighted, false),
                      ));
                      ?>

                    <?php endforeach; ?>

                  </div>
                </div>
              </div>

              <?php if (!empty($filter_min_max_price['min']) && !empty($filter_min_max_price['max']) && $filter_min_max_price['min'] != $filter_min_max_price['max']): ?>
                <div class="filter-block">
                  <button type="button" class="filter-name w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" href="#filter-min_max_price" role="button"
                    aria-expanded="false"><!--collapsed-->
                    <span class="value">Ціна</span>
                    <span class="ic icon-plus"></span>
                  </button>
                  <div class="collapse show" id="filter-min_max_price">
                    <div class="block-container">
                      <div class="filter-slider" data-min="<?php echo $filter_min_max_price['min']; ?>"
                        data-max="<?php echo $filter_min_max_price['max']; ?>"
                        data-value-min="<?php echo $filter_min_max_price['min']; ?>"
                        data-value-max="<?php echo $filter_min_max_price['max']; ?>">
                        <div class="price-slider">
                          <div class="jq-ui-slider"><!--ui-slider--></div>
                        </div>
                        <div class="price-inputs d-flex justify-content-between">
                          <div>
                            <span class="slider-input price-min"><?php echo $filter_min_max_price['min']; ?></span>&nbsp;₴
                          </div>
                          <div>
                            <span class="slider-input price-max"><?php echo $filter_min_max_price['max']; ?></span>&nbsp;₴
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if (!empty($filter_color_objs)): ?>
                <div class="filter-block">
                  <button type="button" class="filter-name w-100 d-flex align-items-center justify-content-between"
                    data-toggle="collapse" href="#filter-color" role="button" aria-expanded="false"><!--collapsed-->
                    <span class="value">Колір</span>
                    <span class="ic icon-plus"></span>
                  </button>
                  <div class="collapse show" id="filter-color">
                    <div class="block-container">
                      <div class="colors d-flex flex-wrap">

                        <?php foreach ($filter_color_objs as $filter_color_obj): ?>
                          <div class="color">
                            <label>
															<input type="checkbox" name="color"
																data-term-id="<?php echo $filter_color_obj->term_id; ?>"
																data-term-slug="<?php echo esc_attr($filter_color_obj->slug); ?>">
                              <span
                                style="background:<?php echo get_field('color_value', 'color_' . $filter_color_obj->term_id); ?>;"></span>
                            </label>
                          </div>
                        <?php endforeach; ?>

                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php
              get_template_part('templates/catalog-filter', null, array(
                'name' => 'size',
                'title' => 'Розмір',
                'objs' => $filter_size_objs,
              ));
              ?>

              <?php
              get_template_part('templates/catalog-filter', null, array(
                'name' => 'brand',
                'title' => 'Бренд',
                'objs' => $filter_brand_objs,
              ));
              ?>

              <?php
              get_template_part('templates/catalog-filter', null, array(
                'name' => 'material',
                'title' => 'Матеріал',
                'objs' => $filter_material_objs,
              ));
              ?>

              <div class="filter-bottom">
                <button id="front-load-products" class="btn-black w-100 upper" type="button">застосувати</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="catalog-list">
        <div class="list-container d-flex align-items-end justify-content-between">
          <div class="total d-none d-xl-block">Товарів: <span id="products-found">0</span></div>
          <button type="button" class="filter-btn d-flex align-items-center d-xl-none">
            <span class="ic icon-filter"></span>
            Фільтр
          </button>
          <div class="list-sort">
            <div class="d-flex align-items-center">
              <span class="value">Сортувати:</span>
              <select name="sort">

                <?php foreach (get_sort_params() as $key => $value): ?>
                  <option <?php selected($key, $sort_by); ?> value="<?php echo esc_attr($key); ?>">
                    <?php echo esc_html($value); ?>
                  </option>
                <?php endforeach; ?>

              </select>
            </div>
            <div class="sel-drop"></div>
          </div>
        </div>
        <div class="row products-container"><!--ajax-content--></div><!--d-none-->

        <?php get_template_part('templates/ajax-preload', null, array('class' => 'items-load')); ?>

        <input type="hidden" name="search" value="<?php echo esc_attr($search); ?>">
        <input type="hidden" name="highlighted" value="<?php echo esc_attr($highlighted); ?>">
        <input type="hidden" name="audience_category" value="<?php echo esc_attr($audience_category_obj->term_id); ?>">
        <input type="hidden" name="product_category" value="<?php echo esc_attr($product_category_id); ?>">
        <input type="hidden" name="brand_id" value="<?php echo esc_attr($brand_id); ?>">
        <button id="front-load-more-products" class="load-more d-none" type="button"><!--active-->
          <span class="value">показати ще</span>
          <span class="ic icon-more"></span>
        </button>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
