<?php

$defaults = array(	'current_pagename' 	=> get_the_title(),
					'parent_pages'		=> array(),
				);
$args = wp_parse_args( $args, $defaults );

?>

<div class="breadcrumps">
	<ul class="nav">
		<li>
			<a href="<?php echo home_url(); ?>">Головна</a>
		</li>
		
		<?php if ( ! empty( $args['parent_pages'] ) ) : ?>
			<?php foreach ( $args['parent_pages'] as $page ) : ?>
				
				<?php
				if ( isset( $page['url'], $page['name'] ) ) {
					$url 	= $page['url'];
					$name	= $page['name'];
				} else {
					$url 	= get_permalink( $page );
					$name	= get_the_title( $page );
				}
				?>
				
				<li>
					<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a>
				</li>
				
			<?php endforeach; ?>
		<?php endif; ?>
	
		<li>
			<span><?php echo esc_html( $args['current_pagename'] ); ?></span>
		</li>
	</ul>
</div>