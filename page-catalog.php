<?php

/* Template Name: Каталог */

if (!defined('ABSPATH'))
	exit;

//https://under.bestclients.com.ua/catalog/women/
//https://under.bestclients.com.ua/catalog/men/
//https://under.bestclients.com.ua/catalog/women/pizhamy/
//https://under.bestclients.com.ua/catalog/women/brand_aruelle/
//https://under.bestclients.com.ua/catalog/women/brand_aruelle/pizhamy/
get_header();

$audience_category_obj = get_audience_category_by_query_var_audience_category();
$product_category_obj = get_product_category_by_query_var_product_category();
$brand_obj = get_brand_by_query_var_brand();
$sort_by = get_user_cookie_sort_by();
$highlighted = get_query_var('highlighted');
$search = get_query_var('search');

$color_obj = get_color_by_query_var_color();
$material_obj = get_material_by_query_var_material();


$seo_ctx = function_exists('impetus_catalog_get_static_filter_context') ? impetus_catalog_get_static_filter_context() : array();

$seo_h1 = '';
$seo_text = '';

// ================== OPTIONAL: OVERRIDE ANY /catalog/* (H1 + TEXT) ==================
// Если включено — берём H1/текст из CPT seo_catalog по seo_key на любой странице каталога, даже если это комбинация фильтров.
$enabled_any = defined('IMPUTUS_SEO_OVERRIDE_ANY_CATALOG_URL') && IMPETUS_SEO_OVERRIDE_ANY_CATALOG_URL;

if ($enabled_any && function_exists('impetus_catalog_seo_key') && function_exists('impetus_catalog_lang') && function_exists('impetus_catalog_get_seo_override')) {

	$any_key = impetus_catalog_seo_key();
	$any_lang = impetus_catalog_lang();

	$any_ov = impetus_catalog_get_seo_override($any_key, $any_lang);

	if (!empty($any_ov['h1'])) {
		$seo_h1 = $any_ov['h1'];
	}

	if (!empty($any_ov['text'])) {
		$seo_text = $any_ov['text'];
	}
}

if (!empty($seo_ctx)) {
	$seo_key = $seo_ctx['seo_key'];
	$seo_lang = $seo_ctx['lang'];

	$seo_override = function_exists('impetus_catalog_get_seo_override') ? impetus_catalog_get_seo_override($seo_key, $seo_lang) : array();

	if (!empty($seo_override['h1'])) {
		$seo_h1 = $seo_override['h1'];
	} else {
		$seo_meta = function_exists('impetus_catalog_build_meta_by_lang') ? impetus_catalog_build_meta_by_lang($seo_ctx, $seo_lang) : impetus_catalog_build_meta_ua($seo_ctx);
		if (!empty($seo_meta['h1']))
			$seo_h1 = $seo_meta['h1'];
	}

	if (!empty($seo_override['text'])) {
		$seo_text = $seo_override['text'];
	}
}

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





	$allowed_cat_ids = mw_get_available_product_category_ids_for_brand_audience($audience_category_obj->term_id, $brand_id);

	if (!empty($allowed_cat_ids)) {
		$filter_product_category_objs = mw_filter_product_category_tree_by_ids($filter_product_category_objs, $allowed_cat_ids);
	} else {
		$filter_product_category_objs = array();
	}





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
					<h1><?php echo $seo_h1 ? $seo_h1 : ('Бренд ' . $brand_obj->name); ?></h1>

					<?php if (!empty($brand_logo)): ?>
						<div class="detail-logo">
							<img src="<?php echo wp_get_attachment_image_url($brand_logo, 'full'); ?>"
								srcset="<?php echo wp_get_attachment_image_srcset($brand_logo, 'full'); ?>"
								sizes="<?php echo wp_get_attachment_image_sizes($brand_logo, 'full'); ?>" alt="Image">
						</div>
					<?php endif; ?>

				</div>

				<?php if (!empty($brand_gallery)): ?>
					<div class="brand-gallery">
						<div class="row gutters-4">

							<?php foreach ($brand_gallery as $brand_gallery_image_id): ?>
								<div class="col-4">
									<div class="item">
										<img src="<?php echo wp_get_attachment_image_url($brand_gallery_image_id, 'full'); ?>"
											srcset="<?php echo wp_get_attachment_image_srcset($brand_gallery_image_id, 'full'); ?>"
											sizes="<?php echo wp_get_attachment_image_sizes($brand_gallery_image_id, 'full'); ?>" alt="Image">
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
				<h1 class="h1-anons"><?php echo $seo_h1 ? $seo_h1 : get_catalog_pagetitle(); ?></h1>
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
																data-term-slug="<?php echo esc_attr($filter_color_obj->slug); ?>"
																<?php echo ($color_obj && (int) $color_obj->term_id === (int) $filter_color_obj->term_id) ? 'checked' : ''; ?>>
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
								'checked_ids' => $material_obj ? array((int) $material_obj->term_id) : array(),
							));
							?>

							<div class="filter-bottom">
								<button id="front-load-products" class="btn-black w-100 upper" type="button">застосувати</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php
			$static_color_ids = $color_obj ? array((int) $color_obj->term_id) : array();
			$static_material_ids = $material_obj ? array((int) $material_obj->term_id) : array();
			$args = array(
				'audience_category' => $audience_category_obj->term_id,
				'product_category' => $product_category_id,
				'brand_id' => $brand_id,
				'sort' => $sort_by,
				'search' => $search,
				'highlighted' => $highlighted,
				'post_status' => 'publish',
				'color_ids' => $static_color_ids,
				'material_ids' => $static_material_ids,
				'color_ids' => $color_obj ? array((int) $color_obj->term_id) : array(),
				'material_ids' => $material_obj ? array((int) $material_obj->term_id) : array(),
			);
			$product_objs = mw_sort_products_in_stock_first(get_products($args));
			$count_products = count($product_objs);

			$is_hidden_button = $count_products > PRODUCT_PER_PAGE ? false : true;

			?>
			<div class="catalog-list">
				<div class="list-container d-flex align-items-end justify-content-between">
					<div class="total d-none d-xl-block">Товарів: <span
							id="products-found"><?php echo esc_html($count_products); ?></span></div>
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
				<div class="row products-container">

					<?php if (!empty($product_objs)): ?>

						<?php

						$_product_objs = array_slice($product_objs, 0, PRODUCT_PER_PAGE);

						?>

						<?php global $post; ?>

						<?php foreach ($_product_objs as $key => $post): ?>

							<?php setup_postdata($post); ?>

							<?php
							get_template_part('templates/product-item-by-filter', null, array(
								'class' => 'col-12 col-sm-6 col-lg-4 op',
								'mobile_slider' => true,
								'fetchpriority_high' => $key === 0,
							));
							?>

							<?php
							if ($key == 5)
								get_template_part('templates/catalog-promosection');
							?>

						<?php endforeach; ?>

						<?php wp_reset_postdata(); ?>

					<?php else: ?>

						<?php echo get_not_found_products_html(); ?>

					<?php endif; ?>

				</div>

				<?php get_template_part('templates/ajax-preload', null, array('class' => 'items-load')); ?>

				<input type="hidden" name="search" value="<?php echo esc_attr($search); ?>">
				<input type="hidden" name="highlighted" value="<?php echo esc_attr($highlighted); ?>">
				<input type="hidden" name="audience_category" value="<?php echo esc_attr($audience_category_obj->term_id); ?>">
				<input type="hidden" name="product_category" value="<?php echo esc_attr($product_category_id); ?>">
				<input type="hidden" name="brand_id" value="<?php echo esc_attr($brand_id); ?>">
				<button id="front-load-more-products" class="load-more <?php is_class($is_hidden_button, 'd-none'); ?>"
					type="button"><!--active-->
					<span class="value">показати ще</span>
					<span class="ic icon-more"></span>
				</button>

				<?php
				$show = true;
				if ($seo_text) {
					$show = true;
					$val = $seo_text;
				} else {


					// используем твои же хелперы из SEO-файла
					if (!function_exists('mw_catalog_is_catalog_request') || !function_exists('mw_catalog_get_term_from_last_slug')) {
						$show = false;
					}

					if ($show && !mw_catalog_is_catalog_request()) {
						$show = false;
					}

					$term = null;
					if ($show) {
						$term = mw_catalog_get_term_from_last_slug();
						if (!$term || empty($term->taxonomy) || empty($term->term_id)) {
							$show = false;
						}
					}

					$aud = '';
					$lang = '';
					if ($show) {
						$aud = mw_catalog_get_audience_slug_from_request(); // men|women|children
						$lang = mw_catalog_get_lang_from_request();          // ua|ru|en
				
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

						if (!in_array($aud, $allowed_aud, true))
							$show = false;
						if (!in_array($lang, $allowed_lang, true))
							$show = false;
					}

					$val = '';
					if ($show) {
						$field = $aud . '_description_' . $lang;
						$term_id_prefixed = $term->taxonomy . '_' . (int) $term->term_id;
						$val = get_field($field, $term_id_prefixed);

						if (!$val)
							$show = false;
					}
				}


				?>

				<?php if ($show): ?>
					<div class="category-description">
						<article class="text-content mt-5 pt-xl-4 pt-4">
							<?php echo $val; ?>
						</article>
						<div class="link-plus d-inline-flex align-items-center cursor-pointer">
							<span class="value">читати детальніше</span>
							<span class="ic icon-plus"></span>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
