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
					<img class="parallax" src="<?php echo wp_get_attachment_image_url( $big_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $big_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $big_image_id, 'full' ); ?>" alt="Image">
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
					<img class="parallax" src="<?php echo wp_get_attachment_image_url( $small_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $small_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $small_image_id, 'full' ); ?>" alt="Image">
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>