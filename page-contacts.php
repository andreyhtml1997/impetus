<?php

/* Template Name: Контакти */

if ( ! defined( 'ABSPATH' ) )
	exit;

$email 		= get_field( 'administrator_email', 'option' );
$address 	= get_field( 'address', 'option' );
$phones 	= get_field( 'contact_phones' );

get_header();

?>

<section class="contacts-section margin-bottom">
	<div class="container-fluid">
		<div class="contacts-container d-flex justify-content-between">
			<div class="contacts-left">
				<div class="hero-inner">
					
					<?php get_template_part( 'templates/breadcrumbs' ); ?>

					<h1><?php the_title(); ?></h1>
				</div>
				<div class="contacts-anons"><?php the_content(); ?></div>
				<div class="d-md-flex contacts-info">
					
					<?php if ( ! empty( $phones ) ) : ?>
					<div class="contacts-phones">
						<div class="contacts-data">Телефони</div>

						<?php foreach ( $phones as $phone ) : ?>
							
							<?php
							
							$phone = replace_phone( $phone['contacts-telefon'] );
							
							?>
							
							<a href="tel:<?php echo esc_attr( $phone ); ?>" class="value phone"><?php echo mask_phone( $phone ); ?></a>
						
						<?php endforeach; ?>
							
					</div>
					<?php endif; ?>
					
					<?php if ( ! empty( $address ) || ! empty( $email ) ) : ?>
					<div class="contacts-values">
						
						<?php if ( ! empty( $email ) ) : ?>
						<div class="info-container">
							<div class="contacts-data">Написати нам</div>
							<a href="mailto:<?php echo antispambot( $email ); ?>" class="value"><?php echo antispambot( $email ); ?></a>
						</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $address ) ) : ?>
						<div class="info-container">
							<div class="contacts-data">Наш офіс</div>
							<div class="value"><?php echo esc_html( $address ); ?></div>
						</div>
						<?php endif; ?>
						
					</div>
					<?php endif; ?>
					
				</div>
				<div class="contacts-form">
					<h2>Зворотній зв’язок</h2>
					
					<?php echo do_shortcode('[contact-form-7 id="7b3fe4f" title="Зворотній звязок"]');?>

				</div>
			</div>
			
			<?php if ( has_post_thumbnail() ) : ?>
			<div class="contacts-image">
				<div class="image-container">
					<?php the_post_thumbnail( 'full', array( 'class' => 'parallax' ) ); ?>
				</div>
			</div>
			<?php endif; ?>
			
		</div>
	</div>
</section>

<?php get_footer(); ?>