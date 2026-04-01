<?php

/* Template Name: Про нас */

if ( ! defined( 'ABSPATH' ) )
	exit;


$about_imp_zagolovok_2 			= get_field( 'about-imp-zagolovok_2' );
$about_imp_foto_image_id 		= get_field( 'about-imp-foto' );
$about_imp_video_url 			= get_field( 'about-imp-video' );
$about_brand_vishenka_tekst		= get_field( 'about-brand-vishenka_tekst' );
$about_brand_vishenka_image_id 	= get_field( 'about-brand-vishenka' );
$about_brand_3_foto_images 		= get_field( 'about-brand-3_foto' );

get_header();

?>

<section class="about-hero margin-bottom">
	<div class="container-fluid">
		<div class="hero-container d-lg-flex justify-content-between">
			<div class="hero-info">
				
				<?php get_template_part( 'templates/breadcrumbs' ); ?>

				<h1><?php the_field( 'about-hero-zagolovok' ); ?></h1>
				<div class="hero-subtitle"><?php the_field( 'about-hero-pidzagolovok' ); ?></div>
				<div class="hero-anons"><?php the_field( 'about-hero-anons' ); ?></div>
			</div>
			<div class="hero-image">

				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'full', array( 'class' => 'parallax' ) ); ?>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
<section class="about-search margin-bottom">
	<div class="container-fluid">
		<div class="search-container d-lg-flex">
			<div class="search-left op">
				<h2><?php the_field( 'about-brand-zagolovok' ); ?></h2>
				<?php if(get_field( 'about-brand-pidzagolovok' )): ?>
				<div class="sub-title op"><?php the_field( 'about-brand-pidzagolovok' ); ?></div>
				<?php endif; ?>
				<?php if(get_field( 'about-brand-anons' )): ?>
				<div class="anons op"><?php the_field( 'about-brand-anons' ); ?></div>
				<?php endif; ?>
			</div>
			<div class="search-right">
				<?php if(get_field( 'about-brand-pidzagolovok_2' )): ?>
				<div class="sub-title op"><?php the_field( 'about-brand-pidzagolovok_2' ); ?></div>
				<?php endif; ?>
				<?php if(get_field( 'about-brand-anons_2' )): ?>
				<div class="anons op">
					<?php the_field( 'about-brand-anons_2' ); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! empty( $about_brand_3_foto_images ) ) : ?>
		<div class="search-image">
			<div class="row">
				
				<?php foreach ( $about_brand_3_foto_images as $about_brand_3_foto_image_id ) : ?>
					<div class="col-4">
						<div class="item op">
							<img class="parallax" src="<?php echo wp_get_attachment_image_url( $about_brand_3_foto_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $about_brand_3_foto_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $about_brand_3_foto_image_id, 'full' ); ?>" alt="Image">
						</div>
					</div>
				<?php endforeach; ?>
			
			</div>
		</div>
		<?php endif; ?>
		
		<?php if ( ! empty( $about_brand_vishenka_tekst ) ) : ?>
		<div class="search-award op">
			<div class="img">

				<?php if ( ! empty( $about_brand_vishenka_image_id ) ) : ?>
				<img src="<?php echo wp_get_attachment_image_url( $about_brand_vishenka_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $about_brand_vishenka_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $about_brand_vishenka_image_id, 'full' ); ?>" alt="Image">
				<?php endif; ?>

			</div>
			<div class="sub-title"><?php echo $about_brand_vishenka_tekst; ?></div>
		</div>
		<?php endif; ?>

	</div>
</section>
<section class="about-brand margin-bottom">
	<div class="container-fluid d-lg-flex justify-content-between">
		<div class="brand-video op">

			<?php if ( ! empty( $about_imp_video_url ) ) : ?>
			<video autoplay loop muted playsinline >
				<source src="<?php echo esc_url( $about_imp_video_url ); ?>">
			</video>
			<?php endif; ?>

		</div>
		<div class="brand-right op">
			<h2><?php the_field( 'about-imp-zagolovok' ); ?></h2>
			<div class="sub-title op"><?php the_field( 'about-imp-pidzagolovok' ); ?></div>
			<div class="anons op">
				<?php the_field( 'about-imp-anons' ); ?>
			</div>

			<?php if ( ! empty( $about_imp_foto_image_id ) ) : ?>
			<div class="image op">
				<img src="<?php echo wp_get_attachment_image_url( $about_imp_foto_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $about_imp_foto_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $about_imp_foto_image_id, 'full' ); ?>" alt="Image">
			</div>
			<?php endif; ?>
			
			<?php if ( ! empty( $about_imp_zagolovok_2 ) ) : ?>
			<div class="op">
				<h2><?php echo $about_imp_zagolovok_2; ?></h2>
			</div>
			<?php endif; ?>
			
		</div>
	</div>
</section>

<?php get_template_part( 'templates/last-posts' ); ?>

<?php get_footer(); ?>