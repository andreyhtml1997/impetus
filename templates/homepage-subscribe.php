<?php

$title = get_field('main-sub-zagolovok');
$subtitle = get_field('main-sub-anons');
$image_id = get_field('main-sub-zobrazhennya');

if (empty($image_id))
	return;

?>

<section class="main-subscribe margin-bottom op">
	<div class="sub-image">
		<?php
		echo wp_get_attachment_image($image_id, 'full', false, array(
			'class' => 'parallax',
			'alt' => 'Image',
			'loading' => 'lazy',
			'decoding' => 'async',
		));
		?>
	</div>
	<div class="sub-info">
		<div class="container-fluid d-lg-flex align-items-center">
			<!-- <div class="sub-form">
				
				<?php if (!empty($title)): ?>
				<div class="form-title op"><?php echo esc_html($title); ?></div>
				<?php endif; ?>
				
				<?php if (!empty($subtitle)): ?>
				<div class="form-anons op"><?php echo esc_html($subtitle); ?></div>
				<?php endif; ?>
				
				<?php echo do_shortcode('[contact-form-7 id="10352b3" title="Підписка"]'); ?>

			</div> -->
			<div class="sub-logo op">
				<img src="<?php echo get_template_directory_uri(); ?>/images/un.svg" alt="Image" width="769" height="142">
			</div>
		</div>
	</div>
</section>
