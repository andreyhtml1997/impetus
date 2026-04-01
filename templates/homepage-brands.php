<?php

$brand_objs = get_brands();
if (empty($brand_objs))
	return;

?>

<section class="main-brands">
	<div class="container-fluid">
		<div class="brands-container d-flex align-items-center justify-content-center flex-wrap">

			<?php foreach ($brand_objs as $brand_obj): ?>

				<?php
				$brand_logo = get_field('brand_logo', 'brand_' . $brand_obj->term_id);
				$brand_audience_objs = get_field('brand_audiences', 'brand_' . $brand_obj->term_id);

				$audience_category_slug = get_audience_category_slug_by_brand($brand_audience_objs);
				if ($audience_category_slug === false)
					continue;

				// размеры картинки
				$image_data = array();
				if ($brand_logo) {
					$image_data = wp_get_attachment_image_src($brand_logo, 'full');
				}
				?>

				<a href="<?php echo get_catalog_url($audience_category_slug, null, $brand_obj->slug); ?>"
					class="item op d-flex align-items-center justify-content-center">
					<img src="<?php echo wp_get_attachment_image_url($brand_logo, 'full'); ?>"
						srcset="<?php echo wp_get_attachment_image_srcset($brand_logo, 'full'); ?>"
						sizes="<?php echo wp_get_attachment_image_sizes($brand_logo, 'full'); ?>" <?php if ($image_data) { ?>
							width="<?php echo (int) $image_data[1]; ?>" height="<?php echo (int) $image_data[2]; ?>" <?php } ?>
						alt="Image">
				</a>

			<?php endforeach; ?>

		</div>
	</div>
</section>