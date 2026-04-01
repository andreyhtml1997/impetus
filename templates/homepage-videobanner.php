<?php

$main_hero_video_url = get_field('main-hero-video');
$main_hero_zagolovok = get_field('main-hero-zagolovok');
$main_hero_pidzagolovok = get_field('main-hero-pidzagolovok');

if (empty($main_hero_video_url))
	return;

$audience_category_objs = get_audience_categories(array(
	MEN_AUDIENCE_CATEGORY_TERM_ID,
	WOMEN_AUDIENCE_CATEGORY_TERM_ID
));

?>

<section class="main-hero">
	<div class="hero-video">
		<video autoplay loop muted playsinline poster="#">
			<source src="<?php echo esc_url($main_hero_video_url); ?>">
		</video>
	</div>
	<div class="hero-info">

		<?php if (!empty($main_hero_zagolovok)): ?>
			<h1><?php echo esc_html($main_hero_zagolovok); ?></h1>
		<?php endif; ?>

		<?php if (!empty($main_hero_pidzagolovok)): ?>
			<div class="hero-anons"><?php echo esc_html($main_hero_pidzagolovok); ?></div>
		<?php endif; ?>

		<div class="hero-buttons d-flex align-items-center justify-content-center">

			<?php foreach ($audience_category_objs as $audience_category_obj): ?>
				<a href="<?php echo get_catalog_url($audience_category_obj->slug); ?>"
					class="cta d-flex align-items-center justify-content-center"><?php echo esc_html($audience_category_obj->name); ?></a>
			<?php endforeach; ?>

		</div>
	</div>
</section>