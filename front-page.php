<?php

if (!defined('ABSPATH'))
	exit;

get_header();

?>

<?php get_template_part('templates/homepage-videobanner'); ?>

<?php get_template_part('templates/homepage-brands'); ?>

<?php get_template_part('templates/homepage-popular-products'); ?>

<?php get_template_part('templates/homepage-audience-categories'); ?>

<?php get_template_part('templates/homepage-new-products'); ?>

<?php get_template_part('templates/homepage-popular-categories'); ?>

<?php get_template_part('templates/homepage-promotional-products'); ?>

<?php get_template_part('templates/homepage-subscribe'); ?>

<?php get_template_part('templates/homepage-about'); ?>

<?php get_template_part('templates/last-posts'); ?>

<?php get_footer(); ?>