<?php

$audience_categories = get_field( 'main_audience_categories' );
if ( empty( $audience_categories ) )
	return;

?>

<section class="main-catalog margin-bottom">
	<div class="container-fluid big">
		<div class="row gutters-10">
			
			<?php foreach ( $audience_categories as $item ) : ?>
			
				<?php
				if ( empty( $item['audience_category'] ) )
					continue;
				?>
			
				<div class="col-12 col-md-6">
					<a href="<?php echo get_catalog_url( $item['audience_category']->slug ); ?>" class="item op">
						<div class="item-video">
	
							<?php if ( ! empty( $item['video'] ) ) : ?>
							<video autoplay loop muted playsinline >
								<source src="<?php echo esc_url( $item['video'] ); ?>">
							</video>
							<?php endif; ?>
	
						</div>
						<div class="item-info">
							<div class="item-name"><?php echo esc_html( $item['audience_category']->name ); ?></div>
							<div class="btn-more white small  d-flex align-items-center justify-content-center">
								<span class="value">Весь асортимент</span>
								<span class="icon d-flex align-items-center justify-content-end">
									<span class="ic icon-right"></span>
									<span class="ic icon-right"></span>
								</span>
							</div>
						</div>
					</a>
				</div>

			<?php endforeach; ?>

		</div>
	</div>
</section>