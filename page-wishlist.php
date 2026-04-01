<?php

/* Template Name: Список бажань */

if ( ! defined( 'ABSPATH' ) )
	exit;


$wishlist = get_wishlist();

get_header();

?>

<section class="catalog-hero">
	<div class="container-fluid">
		
		<?php get_template_part( 'templates/breadcrumbs' ); ?>

		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section id="JS-product-wishlist" class="catalog-section margin-bottom">
	<div class="container-fluid">
		<div class="d-xl-flex">
			<div class="catalog-list">
				<div class="list-container d-none d-xl-flex align-items-end justify-content-between">
					<div class="total">Товарів: <span><?php echo count( $wishlist ); ?></span></div>
				</div>
				<div class="row products-container"><!--ajax-content--></div><!--d-none-->

				<?php get_template_part( 'templates/ajax-preload', null, array( 'class' => 'items-load' ) ); ?>
				
				<input id="front-load-wishlist-products" name="product_wishlist" type="hidden">
				<button id="front-load-more-wishlist-products" class="load-more d-none" type="button"><!--active-->
					<span class="value">показати ще</span>
					<span class="ic icon-more"></span>
				</button>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>