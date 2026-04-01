<?php

if ( ! has_nav_menu( 'chosen-language-menu' ) )
	return;

?>

<div class="header-langs <?php echo $args['class']; ?>"><!--open-->

	<?php
	wp_nav_menu( array(	'theme_location'	=> 'chosen-language-menu',
						'menu'				=> CHOSEN_LANGUAGE_MENU_ID,
						'menu_class'      	=> 'nav langs-btn d-inline-flex align-items-center',
						'container'       	=> false,
						'items_wrap'		=> '<ul class="%2$s">%3$s</ul>',
					) );
	?>

	<?php
	wp_nav_menu( array(	'theme_location'	=> 'languages-menu',
						'menu'				=> LANGUAGES_MENU_ID,
						'menu_class'      	=> 'nav langs-dropdown',
						'container'       	=> false,
						'items_wrap'		=> '<ul class="%2$s">%3$s</ul>',
					) );
	?>

</div>