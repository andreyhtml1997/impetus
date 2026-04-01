<?php
	
$defaults = array( 'class' => '' );
$args = wp_parse_args( $args, $defaults );

?>

<a href="<?php the_permalink(); ?>" class="item d-md-flex <?php echo $args['class']; ?>">
	<div class="item-image">
		<?php echo get_the_post_thumbnail( $post->ID, 'full'); ?>
	</div>
	<div class="item-info d-flex flex-column justify-content-between">
		<div>
			<div class="item-date"><?php echo get_the_date( 'd F Y' ); ?></div>
			<div class="item-name"><?php the_title(); ?></div>
		</div>
		<div class="link-plus d-inline-flex align-items-center">
			<span class="value">читати детальніше</span>
			<span class="ic icon-plus"></span>
		</div>
	</div>
</a>