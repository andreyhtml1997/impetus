<?php

if (!defined('ABSPATH'))
	exit;


global $product_obj; ?>



<?php

$audience_category_obj = get_term($product_obj->product_audience_category);
$brand_obj = get_term($product_obj->product_brand);
$country_obj = get_term($product_obj->product_country);

$delivery_conditions = get_field('delivery_conditions', 'option');
$return_conditions = get_field('return_conditions', 'option');

$color_objs = get_product_colors($product_obj->product_color, true);
$all_ids = get_field('product_size_all', $product_obj->ID, false);
$avail_ids = get_field('product_size', $product_obj->ID, false);

// приводим к int[]
$all_ids = array_values(array_filter(array_map('intval', (array) $all_ids)));
$avail_ids = array_values(array_filter(array_map('intval', (array) $avail_ids)));

// fallback, если вдруг all пустой
if (empty($all_ids)) {
	$all_ids = $avail_ids;
}

$all_size_objs = get_product_sizes($all_ids, true);

// быстрый lookup доступных
$avail_map = [];
foreach ($avail_ids as $id) {
	$avail_map[(int) $id] = 1;
}

$group_product_ids = get_group_products($product_obj->product_group);

if (!empty($product_obj->product_group)) {
	$product_category_objs = get_field('product_product_category', 'product_group_' . $product_obj->product_group);
	$product_composition = get_field('product_composition', 'product_group_' . $product_obj->product_group);
	$product_description = get_field('product_description', 'product_group_' . $product_obj->product_group);
}

if (!empty($brand_obj))
	$care_rules = get_field('brand_care_rules', 'brand_' . $brand_obj->term_id);



$color_obj = !empty($product_obj->product_color) ? get_term((int) $product_obj->product_color) : null;

// product_material у тебя массив ID
$material_ids = !empty($product_obj->product_material) ? (array) $product_obj->product_material : array();
$material_ids = array_values(array_filter(array_map('intval', $material_ids)));

$material_objs = !empty($material_ids) ? get_terms(array(
	'taxonomy' => 'material',
	'hide_empty' => false,
	'include' => $material_ids,
)) : array();





$cross_sell_product_objs = get_products(array(
	'is_random' => true,
	'per_page' => 2,
	'exclude_ids' => array($product_obj->ID)
));
$highlighted_product_objs = get_products(array(
	'is_random' => true,
	'per_page' => 10,
	'exclude_ids' => array($product_obj->ID)
));

get_header();

?>

<section class="catalog-hero">
	<div class="container-fluid">

		<?php

		$parent_pages = array(
			array(
				'url' => get_catalog_url($audience_category_obj->slug),
				'name' => $audience_category_obj->name,
			)
		);

		if (!empty($product_category_objs))
			foreach ($product_category_objs as $product_category_obj)
				$parent_pages[] = array(
					'url' => get_catalog_url($audience_category_obj->slug, $product_category_obj->slug),
					'name' => $product_category_obj->name,
				);

		?>

		<?php get_template_part('templates/breadcrumbs', null, array('parent_pages' => $parent_pages)); ?>

	</div>
</section>
<section class="catalog-detail margin-bottom<?php echo !$product_obj->product_status ? ' no-available' : ''; ?>">
	<div class="container-fluid">
		<div class="d-lg-flex d-c">

			<?php get_template_part('templates/product-badge', null, array('product_obj' => $product_obj)); ?>

			<?php if (!empty($product_obj->product_gallery)): ?>
				<!-- max 5 foto -->
				<div class="detail-gallery">
					<?php $priority_set = false; ?>
					<?php foreach ($product_obj->product_gallery as $key => $image_id): ?>

						<div class="slide">

							<?php
							$is_video = false;
							if (!empty($product_obj->product_videos)) {
								if (isset($product_obj->product_videos[$key])) {
									if ($product_obj->product_videos[$key])
										$is_video = true;
								}
							}
							?>

							<?php if ($is_video): ?>
								<?php
								$video_id = $product_obj->product_videos[$key];
								$video_url = wp_get_attachment_url($video_id);
								$video_type = get_post_mime_type($video_id);

								if (!$video_type) {
									$video_type = 'video/mp4';
									$ext = strtolower(pathinfo($video_url, PATHINFO_EXTENSION));
									if ($ext === 'webm')
										$video_type = 'video/webm';
									if ($ext === 'ogv')
										$video_type = 'video/ogg';
									if ($ext === 'ogg')
										$video_type = 'video/ogg';
									if ($ext === 'm4v')
										$video_type = 'video/mp4';
								}

								$poster = wp_get_attachment_image_url($image_id, 'full');
								?>
								<div class="item item-video">
									<video autoplay loop muted playsinline preload="auto" <?php if ($poster)
										echo 'poster="' . $poster . '"'; ?>
										class="w-100">
										<source src="<?php echo $video_url; ?>" type="<?php echo $video_type; ?>">
									</video>
								</div>

							<?php else: ?>

								<?php
								$img_src = wp_get_attachment_image_src($image_id, 'full');
								$w = !empty($img_src[1]) ? (int) $img_src[1] : 0;
								$h = !empty($img_src[2]) ? (int) $img_src[2] : 0;

								// class: добавляем то, что WP обычно ставит, чтобы было “как ожидается”
								$main_img_class = 'attachment-full size-full wp-post-image';

								// fetchpriority только первому реальному изображению (как мы делали)
								$fp = '';
								if (!$priority_set) {
									$fp = ' fetchpriority="high"';
									$priority_set = true;
								}

								$wh = '';
								if ($w && $h) {
									$wh = ' width="' . $w . '" height="' . $h . '"';
								}
								?>

								<div class="item">
									<img<?php echo $fp . $wh; ?> class="<?php echo esc_attr($main_img_class); ?>" itemprop="image"
										src="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>"
										srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'full'); ?>"
										sizes="<?php echo wp_get_attachment_image_sizes($image_id, 'full'); ?>" alt="Image">
								</div>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>

				</div>
			<?php endif; ?>


			<div class="detail-right d-lg-flex justify-content-between">

				<?php if (!empty($product_obj->product_gallery)): ?>
					<!-- max 5 foto -->
					<div class="thumb-slider">

						<?php foreach ($product_obj->product_gallery as $image_id): ?>
							<div class="slide">
								<a href="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>" class="item" data-fancybox="gall">
									<img src="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>"
										srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'full'); ?>"
										sizes="<?php echo wp_get_attachment_image_sizes($image_id, 'full'); ?>" alt="Image">
								</a>
							</div>
						<?php endforeach; ?>

					</div>
				<?php endif; ?>

				<div class="detail-info d-flex flex-column justify-content-between">
					<?php
					$active_size_id = (int) get_chosen_size();

					if (!$active_size_id || empty($avail_map[$active_size_id])) {
						$active_size_id = 0;

						if (!empty($all_size_objs)) {
							foreach ($all_size_objs as $so) {
								if (!empty($avail_map[(int) $so->term_id])) {
									$active_size_id = (int) $so->term_id;
									break;
								}
							}
						}
					}

					$active_size_name = '';

					if ($active_size_id && !empty($all_size_objs)) {
						foreach ($all_size_objs as $so) {
							if ((int) $so->term_id === $active_size_id) {
								$active_size_name = $so->name;
								break;
							}
						}
					}

					$final_price = !empty($product_obj->product_final_price) ? (float) $product_obj->product_final_price : 0;



					$audience_slug = '';
					if (!empty($product_obj->product_audience_category)) {
						$aud_term = get_term((int) $product_obj->product_audience_category);
						if ($aud_term && !is_wp_error($aud_term))
							$audience_slug = $aud_term->slug;
					}

					// категория товара: у тебя product_product_category это массив ID (из termmeta группы)
					$product_cat_slug = '';
					if (!empty($product_obj->product_product_category) && is_array($product_obj->product_product_category)) {
						$cat_id = (int) reset($product_obj->product_product_category);
						if ($cat_id) {
							$cat_term = get_term($cat_id);
							if ($cat_term && !is_wp_error($cat_term))
								$product_cat_slug = $cat_term->slug;
						}
					}
					?>

					<div class="info-container info-sticky" data-product-container
						data-product-id="<?php echo $product_obj->ID; ?>" data-product-quantity="1"
						data-product-size="<?php echo $active_size_id; ?>"
						data-product-size-name="<?php echo esc_attr($active_size_name); ?>" data-is-single="1"
						data-product-name="<?php echo esc_attr(get_the_title($product_obj->ID)); ?>"
						data-product-price="<?php echo esc_attr($final_price); ?>" data-currency="UAH">

						<?php if (!empty($brand_obj) || !empty($country_obj) || !empty($product_obj->product_article)): ?>
							<div class="info-top d-flex align-items-center justify-content-between">
								<div class="props d-flex flex-wrap align-items-center">

									<?php if (!empty($brand_obj)): ?>

										<?php

										$brand_audience_objs = get_field('brand_audiences', 'brand_' . $brand_obj->term_id);
										$audience_category_slug = get_audience_category_slug_by_brand($brand_audience_objs);

										?>

										<?php if (!empty($audience_category_slug)): ?>
											<div class="item">
												<span class="data">Бренд:</span>
												<span class="value"><a
														href="<?php echo get_catalog_url($audience_category_slug, null, $brand_obj->slug); ?>"><?php echo esc_html($brand_obj->name); ?></a></span>
											</div>
										<?php endif; ?>

									<?php endif; ?>



									<?php if (!empty($color_obj) && !empty($audience_slug)): ?>
										<div class="item">
											<span class="data">Колір:</span>
											<span class="value">
												<a
													href="<?php echo get_catalog_url($audience_slug, $product_cat_slug ?: null, null, null, null, $color_obj->slug, null); ?>">
													<?php echo $color_obj->name; ?>
												</a>
											</span>
										</div>
									<?php endif; ?>

									<?php if (!empty($material_objs) && !empty($audience_slug)): ?>
										<div class="item">
											<span class="data">Матеріал:</span>
											<span class="value">
												<?php foreach ($material_objs as $i => $mat): ?>
													<?php if ($i): ?>, <?php endif; ?>
													<a
														href="<?php echo get_catalog_url($audience_slug, $product_cat_slug ?: null, null, null, null, null, $mat->slug); ?>">
														<?php echo $mat->name; ?>
													</a>
												<?php endforeach; ?>
											</span>
										</div>
									<?php endif; ?>

									<?php if (!empty($country_obj)): ?>
										<div class="item">
											<span class="data">Країна:</span>
											<span class="value"><?php echo esc_html($country_obj->name); ?></span>
										</div>
									<?php endif; ?>

									<?php if (!empty($product_obj->product_article)): ?>
										<div class="item">
											<span class="data">Артикул:</span>
											<span class="value"><?php echo esc_html($product_obj->product_article); ?></span>
										</div>
									<?php endif; ?>

								</div>
							</div>
						<?php endif; ?>

						<h1><?php the_title(); ?></h1>
						<div class="prices-block d-flex align-items-center justify-content-between ">

							<div class="d-inline-flex align-items-end flex-wrap">
								<div class="info-prices d-inline-flex align-items-end my-2 mr-3">
									<div class="price"><?php echo number_format($product_obj->product_final_price, 0, '.', ' '); ?> грн
									</div>

									<?php if (is_promo_price($product_obj)): ?>
										<div class="old"><?php echo number_format($product_obj->product_price, 0, '.', ' '); ?> грн</div>
									<?php endif; ?>
								</div>

								<?php if (!$product_obj->product_status): ?>
									<div class="info-prices d-inline-flex align-items-end single-product__no-available my-2  mr-2">
										<div class="price">Немає в наявності</div>
									</div>
								<?php endif; ?>
							</div>

							<div class="buttons d-inline-flex align-items-center">
								<button type="button" class="share-btn" data-toggle="modal" data-target="#share">
									<span class="ic icon-share"></span>
								</button>

								<?php get_template_part('templates/button-product-wishlist', null, array('product_id' => $product_obj->ID)); ?>
							</div>

						</div>

						<?php if (!empty($group_product_ids)): ?>

							<?php
							// Оставляем только опубликованные продукты
							$group_product_ids = array_values(array_filter($group_product_ids, function ($id) {
								$post = get_post($id);
								if (!$post)
									return false;
								if ($post->post_type !== 'product')
									return false;
								if ($post->post_status !== 'publish')
									return false;
								return true;
							}));
							?>

							<div class="detail-colors d-flex align-items-center">
								<span class="value">Кольори:</span>
								<div class="colors d-flex flex-wrap">
									<?php foreach ($group_product_ids as $group_product_id): ?>
										<?php
										$color_obj = get_field('product_color', $group_product_id);
										if (empty($color_obj))
											continue;

										// Доп. защита: пропустим ссылку, если get_permalink вернул "гостевой" формат ?p=
										$plink = get_permalink($group_product_id);
										if (strpos($plink, '/?p=') !== false)
											continue;
										?>
										<div class="color">
											<a href="<?php echo $plink; ?>" <?php checked($product_obj->ID, $group_product_id); ?>>
												<span
													style="background:<?php echo get_field('color_value', 'color_' . $color_obj->term_id); ?>;"></span>
											</a>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if (!empty($all_size_objs)): ?>

							<div class="detail-sizes">
								<div class="d-flex align-items-center justify-content-between">
									<span class="value">Розмірний ряд:</span>
									<button type="button" class="rozmir-btn d-inline-flex align-items-center" data-toggle="modal"
										data-target="#rozmir">
										<span class="ic icon-rozmir"></span>
										<span>Розмірна таблиця</span>
									</button>
								</div>
								<div class="items d-inline-flex single-product-sizes-container sizes-container"><!--failer-->

									<?php foreach ($all_size_objs as $size_obj): ?>

										<?php
										$is_available = !empty($avail_map[(int) $size_obj->term_id]);
										$is_active = $is_available && $active_size_id && ((int) $size_obj->term_id === (int) $active_size_id);
										?>

										<div class="front-set-chosen-size item
	<?php is_class($is_active, 'active'); ?>
	<?php is_class(!$is_available, 'no-available'); ?>" data-size-id="<?php echo $size_obj->term_id; ?>"
											data-size-name="<?php echo esc_attr($size_obj->name); ?>"
											data-size-slug="<?php echo esc_attr($size_obj->slug); ?>">
											<?php echo wp_kses_post($size_obj->name); ?>
										</div>
									<?php endforeach; ?>

								</div>
							</div>
						<?php endif; ?>




						<?php if (!empty($product_obj->is_product_expected)): ?>

							<div class="detail-buy d-flex">
								<div class="item w-100">
									<button type="button" class="btn-default cta w-100" data-toggle="modal" data-target="#available-modal">
										<span class="ic icon-cart"></span>
										<span class="value">Повідомити про наявність</span>
									</button>
								</div>
							</div>

						<?php elseif ($product_obj->product_status): ?>

							<div class="detail-buy d-flex">
								<div class="item">
									<div class="cart-quantity big d-flex align-items-center justify-content-between">
										<button class="quant-button quant-minus" data-number="-1"><span class="ic icon-minus"></span></button>
										<input type="text" class="quant-input" name="quant" value="1" data-quantity="1">
										<button class="quant-button quant-plus" data-number="1"><span class="ic icon-plus"></span></button>
									</div>
								</div>
								<div class="item">
									<button class="front-add-product-to-cart btn-default cta w-100" type="button">
										<span class="ic icon-cart"></span>
										<span class="value">Додати в кошик</span>
									</button>
								</div>
							</div>

						<?php endif; ?>

						<?php if (get_field('product_status', $product_obj->ID) != 1): ?>

							<?php
							// =========================
// Helpers (no redeclare)
// =========================
							if (!function_exists('site_term_id')) {
								function site_term_id($v)
								{
									if (is_object($v) && !empty($v->term_id))
										return (int) $v->term_id;
									if (is_array($v) && isset($v['term_id']))
										return (int) $v['term_id'];
									return (int) $v;
								}
							}

							if (!function_exists('site_like_clauses')) {
								function site_like_clauses($key, $id)
								{
									$id = (int) $id;
									return [
										[
											'key' => $key,
											'value' => '"' . $id . '"',
											'compare' => 'LIKE'
										],
										[
											'key' => $key,
											'value' => 'i:' . $id . ';',
											'compare' => 'LIKE'
										],
									];
								}
							}

							if (!function_exists('site_group_ids_by_category_ids')) {
								function site_group_ids_by_category_ids($cat_ids)
								{
									$cat_ids = array_values(array_filter(array_map('intval', (array) $cat_ids)));
									if (empty($cat_ids))
										return [];

									$mq = ['relation' => 'OR'];
									foreach ($cat_ids as $cid) {
										foreach (site_like_clauses('product_product_category', $cid) as $cl) {
											$mq[] = $cl;
										}
									}

									$terms = get_terms([
										'taxonomy' => 'product_group',
										'hide_empty' => false,
										'fields' => 'ids',
										'meta_query' => $mq,
									]);

									return (!empty($terms) && !is_wp_error($terms)) ? array_values(array_map('intval', $terms)) : [];
								}
							}

							if (!function_exists('site_category_ids_by_audience_id')) {
								function site_category_ids_by_audience_id($cat_tax, $aud_id)
								{
									$cat_tax = (string) $cat_tax;
									$aud_id = (int) $aud_id;
									if (!$cat_tax || !$aud_id)
										return [];

									$mq = ['relation' => 'OR'];
									foreach (site_like_clauses('product_category_audiences', $aud_id) as $cl) {
										$mq[] = $cl;
									}

									$terms = get_terms([
										'taxonomy' => $cat_tax,
										'hide_empty' => false,
										'fields' => 'ids',
										'meta_query' => $mq,
									]);

									return (!empty($terms) && !is_wp_error($terms)) ? array_values(array_map('intval', $terms)) : [];
								}
							}

							if (!function_exists('site_filter_group_ids_by_audience')) {
								function site_filter_group_ids_by_audience($group_ids, $aud_id)
								{
									$aud_id = (int) $aud_id;
									$group_ids = array_values(array_filter(array_map('intval', (array) $group_ids)));
									if (!$aud_id || empty($group_ids))
										return $group_ids;

									$out = [];
									foreach ($group_ids as $gid) {
										$key = 'product_group_' . (int) $gid;
										$v = get_field('product_audience_category', $key); // ACF on GROUP
										$gid_aud = site_term_id($v);

										if ($gid_aud === $aud_id) {
											$out[] = (int) $gid;
										}
									}
									return $out;
								}
							}

							if (!function_exists('site_pick_products_by_group_ids')) {
								function site_pick_products_by_group_ids(&$picked_ids, $group_ids, $exclude_ids, $need)
								{
									$need = (int) $need;
									if ($need <= 0)
										return;

									$group_ids = array_values(array_filter(array_map('intval', (array) $group_ids)));
									if (empty($group_ids))
										return;

									$exclude_ids = array_values(array_unique(array_filter(array_map('intval', (array) $exclude_ids))));
									$picked_ids = array_values(array_unique(array_filter(array_map('intval', (array) $picked_ids))));

									$q = new WP_Query([
										'post_type' => 'product',
										'post_status' => 'publish',
										'posts_per_page' => $need,
										'orderby' => 'rand',
										'fields' => 'ids',
										'no_found_rows' => true,
										'ignore_sticky_posts' => true,
										'post__not_in' => array_values(array_unique(array_merge($exclude_ids, $picked_ids))),
										'meta_query' => [
											'relation' => 'AND',
											[
												'key' => 'product_status',
												'value' => 1,
												'compare' => '=',
												'type' => 'NUMERIC',
											],
											[
												'key' => 'product_group',
												'value' => $group_ids,
												'compare' => 'IN',
												'type' => 'NUMERIC',
											],
										],
									]);

									if (!empty($q->posts)) {
										foreach ($q->posts as $id) {
											$picked_ids[] = (int) $id;
										}
									}
								}
							}

							// =========================
							// 1) similar_products -> 2 random
							// =========================
							$ids = [];
							$similar_products = get_field('similar_products', $product_obj->ID);
							$similar_products = (!empty($similar_products) && is_array($similar_products)) ? $similar_products : [];

							if (!empty($similar_products)) {
								$tmp = [];
								foreach ($similar_products as $p) {
									if (is_numeric($p))
										$tmp[] = (int) $p;
									elseif (is_object($p) && !empty($p->ID))
										$tmp[] = (int) $p->ID;
								}
								$tmp = array_values(array_unique(array_filter($tmp)));

								if (!empty($tmp)) {
									shuffle($tmp);
									$ids = array_slice($tmp, 0, 2);
								}
							}

							// =========================
							// 2) fallback -> by same category + same audience (FROM GROUP)
							// =========================
							if (empty($ids)) {

								$current_group_id = (int) get_field('product_group', $product_obj->ID);
								$group_key = 'product_group_' . $current_group_id;

								// Group audience (this is your snippet, but via get_field)
								$group_aud = get_field('product_audience_category', $group_key);
								$aud_id = site_term_id($group_aud);

								// Group categories: [0]=parent, [1]=child
								$group_cats = get_field('product_product_category', $group_key);
								$parent_cat = (is_array($group_cats) && !empty($group_cats[0]) && !is_wp_error($group_cats[0])) ? $group_cats[0] : null;
								$child_cat = (is_array($group_cats) && !empty($group_cats[1]) && !is_wp_error($group_cats[1])) ? $group_cats[1] : null;

								$exclude_ids = [(int) $product_obj->ID];
								$picked_ids = [];

								// Step A: child category groups (STRICT audience filter)
								if ($child_cat && $aud_id) {
									$group_ids = site_group_ids_by_category_ids([(int) $child_cat->term_id]);
									$group_ids = site_filter_group_ids_by_audience($group_ids, $aud_id);

									$need = 2 - count($picked_ids);
									site_pick_products_by_group_ids($picked_ids, $group_ids, $exclude_ids, $need);
								}

								// Step B: parent category groups (STRICT audience filter)
								if (count($picked_ids) < 2 && $parent_cat && $aud_id) {
									$group_ids = site_group_ids_by_category_ids([(int) $parent_cat->term_id]);
									$group_ids = site_filter_group_ids_by_audience($group_ids, $aud_id);

									$need = 2 - count($picked_ids);
									site_pick_products_by_group_ids($picked_ids, $group_ids, $exclude_ids, $need);
								}

								// Step C: just same audience -> categories that contain this audience -> groups -> products
								if (count($picked_ids) < 2 && $aud_id) {
									$cat_tax = '';
									if ($child_cat && !empty($child_cat->taxonomy))
										$cat_tax = $child_cat->taxonomy;
									elseif ($parent_cat && !empty($parent_cat->taxonomy))
										$cat_tax = $parent_cat->taxonomy;

									if ($cat_tax) {
										$aud_cat_ids = site_category_ids_by_audience_id($cat_tax, $aud_id);

										$aud_group_ids = !empty($aud_cat_ids) ? site_group_ids_by_category_ids($aud_cat_ids) : [];
										$aud_group_ids = site_filter_group_ids_by_audience($aud_group_ids, $aud_id);

										$need = 2 - count($picked_ids);
										site_pick_products_by_group_ids($picked_ids, $aud_group_ids, $exclude_ids, $need);
									}
								}

								$ids = $picked_ids;
							}

							?>

							<?php if (!empty($ids)): ?>
								<div class="info-bottom">
									<div class="detail-other">
										<h2>Схожі товари</h2>
										<div class="catalog-list small">
											<div class="row">
												<?php global $post; ?>

												<?php foreach ($ids as $pid): ?>
													<?php
													$post = get_post((int) $pid);
													if (!$post)
														continue;
													?>
													<?php setup_postdata($post); ?>
													<?php get_template_part('templates/product-item-by-filter', null, array('class' => 'col col-sm-6 col-lg-6')); ?>
												<?php endforeach; ?>

												<?php wp_reset_postdata(); ?>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>

						<?php endif; ?>

						<div class="detail-accords accordions mt-0">

							<?php if (!empty($product_composition)): ?>
								<div class="accord-item">
									<button class="accrodion-button text-left w-100 d-flex align-items-center collapsed" type="button"
										data-toggle="collapse" data-target="#product-composition" aria-expanded="true">
										<div class="link d-flex align-items-center justify-content-center"><span class="ic icon-plus"></span>
										</div>
										<div class="name d-flex align-items-start">Склад</div>
									</button>
									<div id="product-composition" class="collapse">
										<div class="answer">
											<?php echo $product_composition; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<?php if (!empty($product_description)): ?>
							<div class="detail-anons mb-0">
								<?php echo $product_description; ?>
							</div>
						<?php endif; ?>



						<div class="detail-accords accordions mt-0">

							<?php if (!empty($delivery_conditions)): ?>
								<div class="accord-item">
									<button class="accrodion-button text-left w-100 d-flex align-items-center collapsed" type="button"
										data-toggle="collapse" data-target="#delivery-conditions">
										<div class="link d-flex align-items-center justify-content-center"><span class="ic icon-plus"></span>
										</div>
										<div class="name d-flex align-items-start">Доставка</div>
									</button>
									<div id="delivery-conditions" class="collapse">
										<div class="answer">
											<?php echo $delivery_conditions; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<?php if (!empty($care_rules)): ?>
								<div class="accord-item">
									<button class="accrodion-button text-left w-100 d-flex align-items-center collapsed" type="button"
										data-toggle="collapse" data-target="#care-rules">
										<div class="link d-flex align-items-center justify-content-center"><span class="ic icon-plus"></span>
										</div>
										<div class="name d-flex align-items-start">Правила догляду</div>
									</button>
									<div id="care-rules" class="collapse">
										<div class="answer">
											<?php echo $care_rules; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<?php if (!empty($return_conditions)): ?>
								<div class="accord-item">
									<button class="accrodion-button text-left w-100 d-flex align-items-center collapsed" type="button"
										data-toggle="collapse" data-target="#return-conditions">
										<div class="link d-flex align-items-center justify-content-center"><span class="ic icon-plus"></span>
										</div>
										<div class="name d-flex align-items-start">Обмін / Повернення</div>
									</button>
									<div id="return-conditions" class="collapse">
										<div class="answer">
											<?php echo $return_conditions; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>

						</div>
					</div>
					<div>
						<div class="sticky-stop"></div>

						<?php
						// Временный вывод:
						$gkey = 'product_group_' . intval($product_obj->product_group);
						// var_dump( get_field('product_audience_category', $gkey) );
						// var_dump( get_field('product_product_category', $gkey) );
						// Посмотри SQL, чтобы убедиться, что meta_query с IN реально добавился
						// echo '<pre>' . esc_html( $GLOBALS['wpdb']->last_query ) . '</pre>';
						
						$related_products = get_related_products_mixed($product_obj, 2);
						if (!empty($related_products)): ?>

							<div class="info-bottom" data-nosnippet>
								<div class="detail-other">
									<h2>З цим товаром купують</h2>
									<div class="catalog-list small">
										<div class="row">
											<?php global $post; ?>
											<?php foreach ($related_products as $post): ?>
												<?php setup_postdata($post); ?>
												<?php get_template_part('templates/product-item-by-filter', null, array('class' => 'col col-sm-6 col-lg-6')); ?>
											<?php endforeach;
											wp_reset_postdata(); ?>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>









					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_template_part('templates/highlighted-products', null, array(
	'title' => 'Вам може сподобатись',
	'product_objs' => $highlighted_product_objs,
	'catalog_url' => false,
));
?>

<?php get_footer(); ?>
<!--<div class="item">
<button type="button" class="btn-border cta w-100" data-toggle="modal" data-target="#quick">Швидке замовлення</button>
</div>-->