<?php

$big_image_id 	= get_field( 'main-about-foto_1' );
$small_image_id = get_field( 'main-about-foto_2' );
$description 	= get_field( 'main-about-opys' );

if ( empty( $description ) )
	return;

?>

<section class="main-about margin-bottom">
	<div class="container-fluid">
		<div class="about-container d-lg-flex justify-content-between">

			<?php if ( ! empty( $big_image_id ) ) : ?>
			<div class="image-left op">
				<div class="image-container">
					<?php
					echo wp_get_attachment_image( $big_image_id, 'full', false, array(
						'class' => 'parallax',
						'alt' => 'Image',
						'loading' => 'lazy',
						'decoding' => 'async',
					) );
					?>
				</div>
			</div>
			<?php endif; ?>

			<div class="about-info op">
				<h2>Магазин чоловічого та жіночого одягу <span>underline store</span></h2>
				<div class="anons op"><?php echo $description; ?></div>
				<a href="<?php echo get_catalog_url(); ?>" class="link-default op d-inline-flex align-items-center">
					<span class="value">Дивитись всі</span>
					<span class="icon d-flex align-items-center justify-content-center">
						<span class="ic icon-right2"></span>
					</span>
				</a>

				<?php if ( ! empty( $small_image_id ) ) : ?>
				<div class="image-right op">
					<?php
					echo wp_get_attachment_image( $small_image_id, 'full', false, array(
						'class' => 'parallax',
						'alt' => 'Image',
						'loading' => 'lazy',
						'decoding' => 'async',
					) );
					?>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
