<?php

/* Template Name: Бренди */

if (!defined('ABSPATH'))
	exit;


$brand_objs = get_brands();

get_header();

?>

<section class="catalog-hero">
	<div class="container-fluid">

		<?php get_template_part('templates/breadcrumbs'); ?>

		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="brands-section margin-bottom">

	<?php if (!empty($brand_objs)): ?>
		<?php foreach ($brand_objs as $key => $brand_obj): ?>

			<?php

			$brand_image_id = get_field('brand_image', 'brand_' . $brand_obj->term_id);
			$brand_logo = get_field('brand_logo', 'brand_' . $brand_obj->term_id);
			$brand_content = get_field('brand_content', 'brand_' . $brand_obj->term_id);
			$brand_audience_objs = get_field('brand_audiences', 'brand_' . $brand_obj->term_id);

			$audience_category_slug = get_audience_category_slug_by_brand($brand_audience_objs);
			if ($audience_category_slug === false)
				continue;

			$product_objs = mw_sort_products_in_stock_first(get_products(array(
				'brand_id' => $brand_obj->term_id,
				'is_random' => true,
				'per_page' => 10
			)));

			?>

			<div class="b-item">
				<div class="container-fluid d-lg-flex justify-content-between">
					<a href="<?php echo get_catalog_url($audience_category_slug, null, $brand_obj->slug); ?>" class="brands-left op">
						<div class="brands-image">

							<?php if (!empty($brand_image_id)): ?>
								<img src="<?php echo wp_get_attachment_image_url($brand_image_id, 'full'); ?>"
									srcset="<?php echo wp_get_attachment_image_srcset($brand_image_id, 'full'); ?>"
									sizes="<?php echo wp_get_attachment_image_sizes($brand_image_id, 'full'); ?>" alt="Image">
							<?php endif; ?>

						</div>
						<div class="left-info">

							<?php if (!empty($brand_logo)): ?>
								<div class="brands-logo">
									<img src="<?php echo wp_get_attachment_image_url($brand_logo, 'full'); ?>"
										srcset="<?php echo wp_get_attachment_image_srcset($brand_logo, 'full'); ?>"
										sizes="<?php echo wp_get_attachment_image_sizes($brand_logo, 'full'); ?>" alt="Image">
								</div>
							<?php endif; ?>

							<div class="btn-more white small d-flex align-items-center justify-content-center">
								<span class="value">весь асортимент</span>
								<span class="icon d-flex align-items-center justify-content-end">
									<span class="ic icon-right"></span>
									<span class="ic icon-right"></span>
								</span>
							</div>
						</div>
					</a>
					<div class="brands-info">
						<div class="title-container op d-flex align-items-center justify-content-between">
							<h2><?php echo esc_html($brand_obj->name); ?></h2>
							<div class="slider-navs"></div>
						</div>
						<div class="brands-anons op">
							<div class="anons collapse" id="brand-<?php echo $key; ?>"><?php echo wpautop($brand_content); ?></div>
							<button type="button" class="anons-more d-inline-flex align-items-center collapsed" data-toggle="collapse"
								href="#brand-<?php echo $key; ?>" role="button" aria-expanded="false">
								<span class="value">Показати <span>більше</span><span>менше</span></span>
								<span class="ic icon-down"></span>
							</button>
						</div>

						<?php if (!empty($product_objs)): ?>
							<div class="catalog-list catalog-slider2 op">

								<?php global $post; ?>

								<?php foreach ($product_objs as $post): ?>

									<?php setup_postdata($post); ?>

									<?php get_template_part('templates/product-item-by-filter'); ?>

								<?php endforeach; ?>

								<?php wp_reset_postdata(); ?>

							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>

		<?php endforeach; ?>
	<?php endif; ?>

</section>

<?php get_footer(); ?>