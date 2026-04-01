<?php

$defaults = array('class' => 'preloader');
$args = wp_parse_args($args, $defaults);

?>
<div class="<?php echo $args['class']; ?> d-none">
	<lottie-player src="<?php echo get_template_directory_uri(); ?>/images/pre.json" background="transparent" speed="1"
		style="margin:0 auto;display:block; width: 270px; height: 200px;" loop autoplay></lottie-player>
</div>