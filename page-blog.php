<?php 

/* Template Name: Блог */

if ( ! defined( 'ABSPATH' ) )
	exit;


$posts = get_blog_posts();

get_header();

?>

<section class="hero-inner">
	<div class="container-fluid">
		
		<?php get_template_part( 'templates/breadcrumbs' ); ?>

		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="blog-section margin-bottom">
	<div class="container-fluid">
		<div class="blog-list">
			<div class="row blog-items gutters-52">
				
				<?php if ( ! empty( $posts['html'] ) ) : ?>
					
					<?php echo $posts['html']; ?>
	
					<?php if ( ! $posts['is_hidden_button'] ) : ?>
					<div class="col-12">
						<button id="front-load-more-posts" class="load-more" type="button"><!--active-->
							<span class="value">Показати ще</span>
							<span class="ic icon-more"></span>
						</button>
					</div>
					<?php endif; ?>

				<?php else : ?>
					<div class="col-12">
						<div class="d-flex justify-content-center"><?php echo 'Новини не знайдено'; ?></div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>