<?php

if ( empty( $args['product_objs'] ) )
	return;

?>

<section class="catalog-block margin-bottom">
	<div class="container-fluid">
		<div class="title-container op d-flex align-items-center justify-content-between">
			<h2><?php echo esc_html( $args['title'] ); ?></h2>
			<div class="slider-navs"></div>
		</div>
		<div class="catalog-list catalog-slider op">
			
			<?php global $post; ?>
			
			<?php foreach ( $args['product_objs'] as $post ) : ?>
				
				<?php setup_postdata( $post ); ?>
			
				<?php get_template_part( 'templates/product-item-by-filter' ); ?>

			<?php endforeach; ?>

			<?php wp_reset_postdata(); ?>
			
		</div>
		
		<?php if ( ! empty( $args['catalog_url'] ) ) : ?>
		<a href="<?php echo esc_url( $args['catalog_url'] ); ?>" class="btn-more op all d-flex align-items-center justify-content-center">
			<span class="value">переглянути всі</span>
			<span class="icon d-flex align-items-center justify-content-end">
				<span class="ic icon-right"></span>
				<span class="ic icon-right"></span>
			</span>
		</a>
		<?php endif; ?>
		
	</div>
</section>