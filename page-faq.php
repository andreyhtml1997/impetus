<?php 

/* Template Name: FAQ */

if ( ! defined( 'ABSPATH' ) )
	exit;

$faqs = get_field( 'faq' );

$phone = get_field( 'administrator_phone', 'option' );
$email = get_field( 'administrator_email', 'option' );

$phone = replace_phone( $phone );

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
						
						<?php if ( ! empty( $faqs ) ) : ?>
						<div id="faqs" class="accordion faqs">
							
							<?php foreach ( $faqs as $key => $faq ) : ?>
							<div class="faq-item">
								<div class="faq-header" id="heading-<?php echo $key; ?>">
									<button class="faq-button collapsed w-100 d-flex align-items-center" data-toggle="collapse" data-target="#faq-<?php echo $key; ?>" aria-expanded="true" aria-controls="faq-<?php echo $key; ?>">
										<div class="icon d-flex align-items-center justify-content-center">
											<span class="ic icon-plus"></span>
										</div>
										<div class="value"><?php echo esc_html( $faq['faq-pytannya'] ); ?></div>
									</button>
								</div>
								<div id="faq-<?php echo $key; ?>" class="collapse" aria-labelledby="heading-<?php echo $key; ?>" data-parent="#faqs">
									<div class="faq-body"><?php echo $faq['faq-vidpovid']; ?></div>
								</div>
							</div>
							<?php endforeach; ?>
							
						</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $phone ) || ! empty( $email ) ) : ?>
						<div class="faq-contacts d-lg-flex">
							
							<?php if ( ! empty( $phone ) ) : ?>
							<div class="item">
								<div class="data">Подзвонити нам</div>
								<a href="tel:<?php echo esc_attr( $phone ); ?>" class="value"><?php echo mask_phone( $phone ); ?></a>
							</div>
							<?php endif; ?>
							
							<?php if ( ! empty( $email ) ) : ?>
							<div class="item">
								<div class="data">Написати нам</div>
								<a href="mailto:<?php echo antispambot( $email ); ?>" class="value"><?php echo antispambot( $email ); ?></a>
							</div>
							<?php endif; ?>

						</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
		<div class="sticky-stop"></div>
	</div>
</section>

<?php get_footer(); ?>