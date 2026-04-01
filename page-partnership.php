<?php

/* Template Name: Партнерство */

if ( ! defined( 'ABSPATH' ) )
	exit;

$partner_why_zagolovok 		= get_field( 'partner-why-zagolovok' );
$partner_why_elementies		= get_field( 'partner-why-elementy' );
$partner_zagolovok			= get_field( 'partner-zagolovok' );
$partner_brendy_images		= get_field( 'partner-brendy' );
$partner_info_image_id		= get_field( 'partner-info-foto' );
$partner_info_blocks 		= get_field( 'partner-info-bloky' );
$partner_geo_image_id 		= get_field( 'partner-geo-fon' );
$partner_geo_blocks 		= get_field( 'partner-geo-bloky' );
$partner_forma_zagolovok 	= get_field( 'partner-forma-zagolovok' );
$partner_forma_anons 		= get_field( 'partner-forma-anons' );

get_header();

?>

<section class="page-section">
	<div class="container-fluid">
		<div class="page-container d-md-flex justify-content-between">

			<?php get_template_part( 'templates/pagemenu-sidebar' ); ?>
			
			<div class="page-content margin-bottom">
				<div class="content-container">
					
					<?php get_template_part( 'templates/breadcrumbs' ); ?>

					<h1><?php the_title(); ?></h1>
					<div class="page-inner">
						<div class="partner-section">
							<div class="partner-anons"><?php the_content(); ?></div>
							
							<?php if ( has_post_thumbnail() ) : ?>
							<div class="partner-image margin-bottom">
								<?php the_post_thumbnail(); ?>
							</div>
							<?php endif; ?>

							<?php if ( ! empty( $partner_why_elementies ) ) : ?>
							<div class="partner-why margin-bottom">
								
								<?php if ( ! empty( $partner_why_zagolovok ) ) : ?>
								<h2><?php echo esc_html( $partner_why_zagolovok ); ?></h2>
								<?php endif; ?>
								
								<div class="row gutters-16">
									
									<?php foreach ( $partner_why_elementies as $partner_why_element ) : ?>
										
										<?php
										if ( empty( $partner_why_element['partner-why-znachennya'] ) || empty( $partner_why_element['partner-why-anons'] ) )
											continue;
										?>
										
										<div class="col-12 col-lg-4">
											<div class="item">
												<div class="data"><?php echo esc_html( $partner_why_element['partner-why-znachennya'] ); ?></div>
												<div class="value"><?php echo esc_html( $partner_why_element['partner-why-anons'] ); ?></div>
											</div>
										</div>
									
									<?php endforeach; ?>

								</div>
							</div>
							<?php endif; ?>

							<?php if ( ! empty( $partner_brendy_images ) ) : ?>
							<div class="partner-brands margin-bottom">
								
								<?php if ( ! empty( $partner_zagolovok ) ) : ?>
								<h2><?php echo esc_html( $partner_zagolovok ); ?></h2>
								<?php endif; ?>
								
								<div class="items d-flex align-items-center flex-wrap">
									
									<?php foreach ( $partner_brendy_images as $partner_brendy_image_id ) : ?>
									<div class="item d-flex align-items-center justify-content-center">
										<img src="<?php echo wp_get_attachment_image_url( $partner_brendy_image_id, 'full' ); ?>" alt="Image">
									</div>
									<?php endforeach; ?>

								</div>
							</div>
							<?php endif; ?>

							<?php if ( ! empty( $partner_info_blocks ) ) : ?>
							<div class="partner-info margin-bottom d-lg-flex justify-content-between">
								<div class="info-image order-12">
									<div class="image-container">
										
										<?php if ( ! empty( $partner_info_image_id ) ) : ?>
										<img src="<?php echo wp_get_attachment_image_url( $partner_info_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $partner_info_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $partner_info_image_id, 'full' ); ?>" alt="Image">
										<?php endif; ?>

									</div>
								</div>
								<div class="info-container">
									
									<?php foreach ( $partner_info_blocks as $partner_info_block ) : ?>
									<div class="info-block">
										<div class="data"><?php echo esc_html( $partner_info_block['partner-info-zagolovok'] ); ?></div>
										
										<?php if ( ! empty( $partner_info_block['partner-info-anons'] ) ) : ?>
										<div class="value"><?php echo esc_html( $partner_info_block['partner-info-anons'] ); ?></div>
										<?php endif; ?>
										
									</div>
									<?php endforeach; ?>

								</div>
							</div>
							<?php endif; ?>
							
						</div>
					</div>
				</div>
				
				<?php if ( ! empty( $partner_geo_blocks ) ) : ?>
				<div class="geografy-section margin-bottom">
						
					<?php if ( ! empty( $partner_geo_image_id ) ) : ?>
					<div class="bg">
						<img src="<?php echo wp_get_attachment_image_url( $partner_geo_image_id, 'full' ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $partner_geo_image_id, 'full' ); ?>" sizes="<?php echo wp_get_attachment_image_sizes( $partner_geo_image_id, 'full' ); ?>" alt="Image">
					</div>
					<?php endif; ?>

					<div class="geografy-container">
						
						<?php foreach ( $partner_geo_blocks as $partner_geo_block ) : ?>
						<div class="geo-block">
							
							<?php if ( ! empty( $partner_geo_block['partner-geo-zagolovok'] ) ) : ?>
							<h2><?php echo esc_html( $partner_geo_block['partner-geo-zagolovok'] ); ?></h2>
							<?php endif; ?>

							<?php if ( ! empty( $partner_geo_block['partner-geo-pidzagolovok'] ) ) : ?>
							<div class="title"><?php echo esc_html( $partner_geo_block['partner-geo-pidzagolovok'] ); ?></div>	
							<?php endif; ?>

							<?php if ( ! empty( $partner_geo_block['partner-geo-elementy'] ) ) : ?>
							<div class="row">
								
								<?php foreach ( $partner_geo_block['partner-geo-elementy'] as $element ) : ?>
									
									<?php
									if ( empty( $element['partner-geo-elementy-znachennya'] ) || empty( $element['partner-geo-elementy-anons'] ) )
										continue;
									?>
									
									<div class="col-6">
										<div class="item">
											<div class="data"><?php echo esc_html( $element['partner-geo-elementy-znachennya'] ); ?></div>
											<div class="value"><?php echo esc_html( $element['partner-geo-elementy-anons'] ); ?></div>
										</div>
									</div>
								
								<?php endforeach; ?>

							</div>
							<?php endif; ?>

						</div>
						<?php endforeach; ?>

					</div>
				</div>
				<?php endif; ?>

				<div class="contacts-form partner">
					
					<?php if ( ! empty( $partner_forma_zagolovok ) ) : ?>
					<h2><?php echo esc_html( $partner_forma_zagolovok ); ?></h2>
					<?php endif; ?>
					
					<?php if ( ! empty( $partner_forma_anons ) ) : ?>
					<div class="anons"><?php echo esc_html( $partner_forma_anons ); ?></div>
					<?php endif; ?>
					
					<?php echo do_shortcode('[contact-form-7 id="7b3fe4f" title="Зворотній звязок"]');?>
					
				</div>
			</div>
		</div>
		<div class="sticky-stop"></div>
	</div>
</section>

<?php get_footer(); ?>