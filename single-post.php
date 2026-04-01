<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;

get_header();

?>

<section class="article margin-bottom">
	<div class="container-fluid">

		<?php get_template_part( 'templates/breadcrumbs', null, array( 'parent_pages' => array( BLOG_PAGE_ID ) ) ); ?>
		
		<h1><?php the_title(); ?></h1>
		<div class="article-content">
			<?php the_content(); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>