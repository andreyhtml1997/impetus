<?php

if ( ! has_nav_menu( 'page-menu' ) )
	return;

?>

<div class="page-left">
	<div class="inner-menu">
		
		<?php
		wp_nav_menu( array(	'theme_location'	=> 'page-menu',
							'menu'            	=> PAGE_MENU_ID,
							'menu_class'		=> 'nav flex-column',
							'container'       	=> false,
							'items_wrap'		=> '<ul class="%2$s">%3$s</ul>',
						) );
		?>

	</div>
</div>