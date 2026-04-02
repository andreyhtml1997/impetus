<?php

$product_category_objs = get_field( 'main_popular_product_categories' );
if ( empty( $product_category_objs ) )
	return;

?>

<section class="main-sections margin-bottom">
	<div class="container-fluid big">
		<div class="row gutters-10">
			
			<?php foreach ( $product_category_objs as $product_category_obj ) : ?>
				
				<?php

				$product_category_image_id 		= get_field( 'product_category_image', 'product_category_' . $product_category_obj->term_id );
				$product_category_audience_objs = get_field( 'product_category_audiences', 'product_category_' . $product_category_obj->term_id );
				
				$audience_category_slug = get_audience_category_slug_by_product_category( $product_category_audience_objs );
				if ( $audience_category_slug === false )
					continue;
				
				?>
				
				<div class="col-12 col-sm-6 col-xl-3"><!--checked-->
					<a href="<?php echo get_catalog_url( $audience_category_slug, $product_category_obj->slug ); ?>" class="item op">
						<div class="item-image">
							
							<?php if ( ! empty( $product_category_image_id ) ) : ?>
							<?php
							echo wp_get_attachment_image( $product_category_image_id, 'full', false, array(
								'alt' => 'Image',
								'loading' => 'lazy',
								'decoding' => 'async',
							) );
							?>
							<?php endif; ?>

						</div>
						<div class="item-info">
							<div class="item-name"><?php echo esc_html( $product_category_obj->name ); ?></div>
							<div class="btn-more white small  d-inline-flex align-items-center justify-content-center cta">
								<span class="value">Переглянути</span>
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
