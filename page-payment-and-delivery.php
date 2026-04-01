<?php 

/* Template Name: Доставка та оплата */

if ( ! defined( 'ABSPATH' ) )
	exit;

$items = get_field( 'delivery-bloky' );

get_header();

?>

<section class="page-section">
	<div class="container-fluid">
		<div class="page-container d-md-flex justify-content-between">

			<?php get_template_part( 'templates/pagemenu-sidebar' ); ?>

			<div class="page-content margin-bottom">
				<div class="content-container">
					
					<?php get_template_part( 'templates/breadcrumbs' ); ?>
					
					<div class="page-inner">
					
						<?php foreach ( $items as $item ) : ?>
						<div class="delivery-block">

							<?php if ( ! empty( $item['delivery-bloky-zagolovok'] ) ) : ?>
							<h2><?php echo esc_html( $item['delivery-bloky-zagolovok'] ); ?></h2>
							<?php endif; ?>
							
							<?php echo $item['delivery-bloky-opys_1']; ?>

							<?php if ( ! empty( $item['delivery-bloky-vazhlyva_informacziya'] ) ) : ?>
							<div class="warning"><span>Важливо️</span> <?php echo $item['delivery-bloky-vazhlyva_informacziya']; ?></div>
							<?php endif; ?>
							
							<?php echo $item['delivery-bloky-opys_2']; ?>

							<?php if ( ! empty( $item['delivery-bloky-pomitky'] ) ) : ?>
							<div class="bolds">
								<?php echo $item['delivery-bloky-pomitky']; ?>
							</div>
							<?php endif; ?>

						</div>
						<?php endforeach; ?>

					</div>
				</div>
			</div>
		</div>
		<div class="sticky-stop"></div>
	</div>
</section>

<?php get_footer(); ?>