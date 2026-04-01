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


$gallery = $post->product_gallery ?? [];

// если прилетело строкой — приводим к массиву
if (is_string($gallery)) {
	$gallery = trim($gallery);

	$maybe = $gallery ? maybe_unserialize($gallery) : [];

	if (is_array($maybe)) {
		$gallery = $maybe;
	} else {
		$json = $gallery ? json_decode($gallery, true) : null;

		if (is_array($json)) {
			$gallery = $json;
		} else {
			$gallery = $gallery ? preg_split('/\s*,\s*/', $gallery, -1, PREG_SPLIT_NO_EMPTY) : [];
		}
	}
}

// если массив картинок (ACF array) — достаём ID
if (is_array($gallery) && isset($gallery[0]) && is_array($gallery[0])) {
	$gallery = array_map(function ($img) {
		$id = 0;
		if (isset($img['ID']))
			$id = (int) $img['ID'];
		if (!$id && isset($img['id']))
			$id = (int) $img['id'];
		return $id;
	}, $gallery);
}

// чистим и приводим к int[]
$gallery = is_array($gallery) ? array_values(array_filter(array_map('intval', $gallery))) : [];

$images = array_slice($gallery, 1);

$mobile_images = [];
if (!empty($args['mobile_slider'])) {
	$mobile_images = array_slice($gallery, 0, 3);
}


$all_ids = get_field('product_size_all', $post->ID, false);
$avail_ids = get_field('product_size', $post->ID, false);

$all_ids = array_values(array_filter(array_map('intval', (array) $all_ids)));
$avail_ids = array_values(array_filter(array_map('intval', (array) $avail_ids)));

if (empty($all_ids)) {
	$all_ids = $avail_ids;
}

$size_objs = get_product_sizes($all_ids, true);

$avail_map = [];
foreach ($avail_ids as $id) {
	$avail_map[(int) $id] = 1;
}

// активный размер по умолчанию — первый доступный
$active_size_id = (int) get_chosen_size();
if (!$active_size_id || empty($avail_map[$active_size_id])) {
	$active_size_id = 0;
	if (!empty($size_objs)) {
		foreach ($size_objs as $so) {
			if (!empty($avail_map[(int) $so->term_id])) {
				$active_size_id = (int) $so->term_id;
				break;
			}
		}
	}
}



?>

<div class="<?php echo esc_attr($args['class']); ?> product-item" data-product-container
	data-product-id="<?php echo $post->ID; ?>" data-product-quantity="1"
	data-product-size="<?php echo $active_size_id; ?>" data-is-single="0"
	data-product-name="<?php echo esc_attr(get_the_title($post->ID)); ?>"
	data-product-price="<?php echo esc_attr($final_num); ?>" data-currency="UAH">
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

				echo get_the_post_thumbnail($post->ID, 'full', [
					'data-no-fetchpriority' => '1',
				]);
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
							<?php
							$is_available = !empty($avail_map[(int) $size_obj->term_id]);
							$is_active = $is_available && $active_size_id && ((int) $size_obj->term_id === (int) $active_size_id);
							?>
							<button class="front-set-chosen-size s-item
			<?php is_class($is_active, 'active'); ?>
			<?php is_class(!$is_available, 'no-available'); ?>" data-size-id="<?php echo $size_obj->term_id; ?>"
								data-size-name="<?php echo esc_attr($size_obj->name); ?>" type="button">
								<?php echo wp_kses_post($size_obj->name); ?>
							</button>
						<?php endforeach; ?>

					</div>
				<?php endif; ?>

				<div class="item-buttons d-xl-flex d-none">
					<?php
					$is_in_stock = get_field('product_status', $post->ID) ? 1 : 0;
					?>

					<?php get_template_part('templates/button-add-cart', null, array(
						'product_id' => $post->ID,
						'is_in_stock' => $is_in_stock,
					)); ?>
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

			<?php get_template_part('templates/button-mobile-add-cart', null, array(
				'product_id' => $post->ID,
				'is_in_stock' => $is_in_stock,
			)); ?>



		</div>
	</div>
</div>