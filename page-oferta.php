<?php

/* Template Name: Оферта */

if ( ! defined( 'ABSPATH' ) )
	exit;

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
						<div class="politica-section">
						
							<?php the_content(); ?>
						
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="sticky-stop"></div>
	</div>
</section>

<?php get_footer(); ?>