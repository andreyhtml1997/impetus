<?php 

if ( ! defined( 'ABSPATH' ) )
	exit;

get_header();

?>

<section class="hero-inner margin-bottom">
	<div class="container-fluid">

		<?php get_template_part( 'templates/breadcrumbs' ); ?>
		
		<h1><?php the_title(); ?></h1>
		
		<?php the_content(); ?>
		
	</div>
</section>

<?php get_footer(); ?>