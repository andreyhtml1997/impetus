<?php

$last_post_objs = get_last_posts();
if ( empty( $last_post_objs ) )
	return;

?>

<section class="main-blog margin-bottom">
	<div class="container-fluid">
		<div class="title-container op d-flex align-items-center justify-content-between">
			<h2>Останні новини компанії</h2>
			<div class="slider-navs"></div>
		</div>
		<div class="blog-list blog-slider op">
			
			<?php global $post; ?>
			
			<?php foreach ( $last_post_objs as $post ) : ?>
				
				<?php setup_postdata( $post ); ?>
				
				<div class="slide">
					
					<?php get_template_part( 'templates/post-item-content' ); ?>
	
				</div>

			<?php endforeach; ?>

			<?php wp_reset_postdata(); ?>
			
		</div>
	</div>
</section>