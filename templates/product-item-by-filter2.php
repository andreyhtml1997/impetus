<?php
$post_id = get_the_ID();

$price = get_field('product_price', $post_id);
$promo = get_field('product_promo_price', $post_id);

// финальная цена: если есть промо (>0) — берём её, иначе обычную
$final = $price;

if ($promo) {
	if (intval($promo) > 0) {
		$final = $promo;
	}
}

// на всякий случай приведём к числу для форматирования
$final_num = $final ? floatval($final) : 0;
$price_num = $price ? floatval($price) : 0;
$has_promo = 0;

if ($promo) {
	if (intval($promo) > 0) {
		$has_promo = 1;
	}
}
?>

<?php

$defaults = array(
	'class' => 'slide',
	'mobile_slider' => false,
	'fetchpriority_high' => false,
);
$args = wp_parse_args($args, $defaults);


$images = array_slice($post->product_gallery, 1);

if ($args['mobile_slider'])
	$mobile_images = array_slice($post->product_gallery, 0, 3);

$size_objs = get_product_sizes($post->product_size, true);



?>

<div class="<?php echo esc_attr($args['class']); ?> product-item" data-product-container
	data-product-id="<?php echo $post->ID; ?>" data-product-quantity="1"
	data-product-size="<?php echo get_chosen_size(); ?>" data-is-single="0">
	<div class="item">

		<?php get_template_part('templates/product-badge', null, array('product_obj' => $post)); ?>

		<?php get_template_part('templates/button-wishlist', null, array('product_id' => $post->ID)); ?>

		<div class="item-media">
			<a href="<?php the_permalink(); ?>" class="item-image">
				<?php
				$thumb_attrs = array();

				if (!empty($args['fetchpriority_high'])) {
					$thumb_attrs['fetchpriority'] = 'high';
				}

				echo get_the_post_thumbnail($post->ID, 'full', $thumb_attrs);
				?>
			</a>

			<?php if (!empty($images)): ?>
				<div class="media-slider">

					<?php foreach ($images as $image_id): ?>
						<div class="slide-in">
							<a href="<?php the_permalink(); ?>" class="m-item">
								<img src="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>"
									srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'full'); ?>"
									sizes="<?php echo wp_get_attachment_image_sizes($image_id, 'full'); ?>" alt="Image">
							</a>
						</div>
					<?php endforeach; ?>

				</div>
			<?php endif; ?>

			<?php if (!empty($mobile_images)): ?>
				<div class="media-slider mob-slider">


					<?php foreach ($mobile_images as $image_id): ?>
						<div class="slide-in">
							<a href="<?php the_permalink(); ?>" class="m-item">
								<img src="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>"
									srcset="<?php echo wp_get_attachment_image_srcset($image_id, 'full'); ?>"
									sizes="<?php echo wp_get_attachment_image_sizes($image_id, 'full'); ?>" alt="Image">
							</a>
						</div>
					<?php endforeach; ?>

				</div>
			<?php endif; ?>

			<div class="item-info">

				<?php if (!empty($size_objs)): ?>
					<div
						class="item-sizes d-flex align-items-center justify-content-center sizes-container <?php is_class(is_product_in_cart($post->ID), 'd-none'); ?>">
						<!--d-none failer-->

						<?php foreach ($size_objs as $size_obj): ?>
							<button
								class="front-set-chosen-size s-item <?php is_class(get_chosen_size() == $size_obj->term_id, 'active'); ?>"
								data-size-id="<?php echo $size_obj->term_id; ?>"
								type="button"><?php echo esc_html($size_obj->name); ?></button>
						<?php endforeach; ?>

					</div>
				<?php endif; ?>

				<div class="item-buttons d-flex">

					<?php get_template_part('templates/button-add-cart', null, array('product_id' => $post->ID)); ?>

					<!--<button class="cta btn-border upper" type="button" data-toggle="modal" data-target="#quick">швидке замовлення</button>-->
				</div>
			</div>
		</div>
		<a href="<?php the_permalink(); ?>" class="item-name"><?php the_title(); ?></a>
		<div class="d-flex align-items-center justify-content-between">
			<div class="item-prices old d-md-flex align-items-end">
				<div class="price"><?php echo $final_num ? number_format($final_num, 0, '.', ' ') : ''; ?> грн</div>

				<?php if ($has_promo): ?>
					<div class="old"><?php echo $price_num ? number_format($price_num, 0, '.', ' ') : ''; ?> грн</div>
				<?php endif; ?>

			</div>

			<?php get_template_part('templates/button-mobile-add-cart', null, array('product_id' => $post->ID)); ?>

		</div>
	</div>
</div>