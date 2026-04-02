<?php

if (!defined('ABSPATH'))
	exit;

$company_facebook = get_field('company_facebook', 'option');
$company_instagram = get_field('company_instagram', 'option');
$company_telegram = get_field('company_telegram', 'option');
$company_youtube = get_field('company_youtube', 'option');
$header_running_advertisement = get_field('header_running_advertisement', 'option');
$header_running_advertisement_promocode = get_field('header_running_advertisement_promocode', 'option');

$audience_category_objs = get_audience_categories();

$cart = get_cart();
$wishlist = get_wishlist();

?>
<!DOCTYPE html>
<html lang="uk" style="margin-top:0!important;">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">





	<!-- Google Tag Manager -->
	<script>(function (w, d, s, l, i) {
			w[l] = w[l] || []; w[l].push({
				'gtm.start':
					new Date().getTime(), event: 'gtm.js'
			}); var f = d.getElementsByTagName(s)[0],
				j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
					'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
		})(window, document, 'script', 'dataLayer', 'GTM-NM3DGQWK');</script>
	<!-- GTM-KFLHLGBZ -->
	<!-- End Google Tag Manager -->

	<?php wp_head(); ?>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-T38024H9TP"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag() { dataLayer.push(arguments); }
	gtag('js', new Date());

	gtag('config', 'G-T38024H9TP');
</script>

<body <?php body_class(); ?>><!--no-banner-->
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NM3DGQWK" height="0" width="0"
			style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<div class="wrapper">
		<div class="menu-bg"></div>
		<header class="header d-flex align-items-center">
			<div class="header-container w-100">
				<div class="container-fluid d-flex align-items-center">
					<a href="<?php echo home_url(); ?>" class="logo">
						<img class="d-none d-md-block" src="<?php echo get_template_directory_uri(); ?>/images/logo.svg" alt="Logo"
							width="186" height="45">
						<img class="d-block d-md-none" src="<?php echo get_template_directory_uri(); ?>/images/logo-mobile.svg"
							alt="Logo" width="35" height="32">
					</a>
					<button class="menu-button d-flex d-xl-none align-items-center">
						<div class="button-icon d-flex align-items-center justify-content-center">
							<span class="but-icon"></span>
						</div>
						<span class="value">Каталог</span>
					</button>
					<div class="header-navs">

						<?php get_template_part('templates/languages-menu', null, array('class' => 'd-block d-md-none')); ?>

						<div class="navs-c d-xl-flex align-items-center justify-content-center">

							<?php if (!empty($audience_category_objs)): ?>
								<nav class="item item-cat">
									<ul class="nav menu-catalog">

										<?php foreach ($audience_category_objs as $key => $audience_category_obj): ?>

											<?php

											//тимчасово приховуємо з сайту
											if ($audience_category_obj->term_id == 17)
												continue;

											$audience_category_image_id = get_field('audience_category_image', 'audience_category_' . $audience_category_obj->term_id);
											$product_category_filter = get_field('product_category_filter', 'audience_category_' . $audience_category_obj->term_id);

											?>

											<li>
												<span class="nav-item drop-btn d-flex align-items-center">
													<?php echo esc_html($audience_category_obj->name); ?><span
														class="ic icon-down d-block d-xl-none"></span>
												</span>
												<div class="catalog-dropdown">
													<div class="container-fluid h-100">
														<div class="dropdown-container d-flex h-100">
															<div class="drop-right order-12">
																<ul class="nav catalog-menu w-100 flex-column">

																	<?php if (!empty($product_category_filter)): ?>
																		<li class="cat-li">
																			<span class="d-flex cat-item align-items-center justify-content-between"
																				data-toggle="collapse" href="#sub<?php echo $key; ?>" role="button"
																				aria-expanded="true"><!--collapsed-->
																				<span class="value">Категорії</span>
																				<span class="icon"></span>
																			</span>
																			<div class="catalog-sub collapse show" id="sub<?php echo $key; ?>">
																				<ul class="nav flex-column">

																					<?php foreach ($product_category_filter as $j => $item): ?>

																						<li>
																							<div class="sub-item d-flex align-items-center justify-content-between">
																								<a href="<?php echo get_catalog_url($audience_category_obj->slug, $item['option']->slug); ?>"
																									class="value">
																									<?php echo esc_html($item['option']->name); ?>
																								</a>

																								<?php if (!empty($item['child'])): ?>
																									<span class="icn collapsed" data-toggle="collapse"
																										href="#sub<?php echo $key; ?>-<?php echo $j; ?>" role="button"
																										aria-expanded="false"></span>
																								<?php endif; ?>

																							</div>

																							<?php if (!empty($item['child'])): ?>
																								<div class="sub-menu collapse" id="sub<?php echo $key; ?>-<?php echo $j; ?>">
																									<ul class="nav flex-column">

																										<?php foreach ($item['child'] as $j => $child_item): ?>
																											<li>
																												<a
																													href="<?php echo get_catalog_url($audience_category_obj->slug, $child_item['option']->slug); ?>">
																													<?php echo esc_html($child_item['option']->name); ?>
																												</a>
																											</li>
																										<?php endforeach; ?>

																									</ul>
																								</div>
																							<?php endif; ?>

																						</li>

																					<?php endforeach; ?>

																				</ul>
																			</div>
																		</li>
																	<?php endif; ?>

																	<!--<li class="cat-li">
																	<a href="<?php //echo get_catalog_url( $audience_category_obj->slug, null, null, null, 'bestseller' ); ?>" class="d-flex cat-item align-items-center justify-content-between">Бестселлери</a>
																</li>-->
																	<li class="cat-li">
																		<a href="<?php echo get_catalog_url($audience_category_obj->slug, null, null, null, 'new'); ?>"
																			class="d-flex cat-item align-items-center justify-content-between">Новинки</a>
																	</li>
																	<!--<li class="cat-li">
																	<a href="<?php //echo get_catalog_url( $audience_category_obj->slug, null, null, null, 'sale' ); ?>" class="d-flex cat-item align-items-center justify-content-between">Знижки</a>
																</li>-->
																</ul>
															</div>

															<?php if (!empty($audience_category_image_id)): ?>
																<div class="drop-left">
																	<div class="drop-image">
																		<?php
																		echo wp_get_attachment_image($audience_category_image_id, 'full', false, array(
																			'alt' => 'Image',
																			'loading' => 'lazy',
																			'decoding' => 'async',
																		));
																		?>
																	</div>

																	<?php if (!empty($company_facebook) || !empty($company_instagram) || !empty($company_telegram) || !empty($company_youtube)): ?>
																		<div class="drop-social">
																			<div class="value">Доєднуйтесь до нас в соціальних мережах</div>

																			<?php
																			get_template_part('templates/socials', null, array(
																				'instagram' => $company_instagram,
																				'facebook' => $company_facebook,
																				'telegram' => $company_telegram,
																				'youtube' => $company_youtube,
																			));
																			?>

																		</div>
																	<?php endif; ?>

																</div>
															<?php endif; ?>

														</div>
													</div>
												</div>
											</li>

										<?php endforeach; ?>

									</ul>
								</nav>
							<?php endif; ?>

							<?php if (has_nav_menu('header-menu')): ?>
								<nav class="item">

									<?php
									wp_nav_menu(array(
										'theme_location' => 'header-menu',
										'menu' => HEADER_MENU_ID,
										'menu_class' => 'nav menu-simple',
										'container' => false,
										'items_wrap' => '<ul class="%2$s">%3$s</ul>',
									));
									?>

								</nav>
							<?php endif; ?>

						</div>

						<?php if (!empty($company_facebook) || !empty($company_instagram) || !empty($company_telegram) || !empty($company_youtube)): ?>
							<div class="drop-social d-block d-xl-none">
								<div class="value">Доєднуйтесь до нас в соціальних мережах</div>

								<?php
								get_template_part('templates/socials', null, array(
									'instagram' => $company_instagram,
									'facebook' => $company_facebook,
									'telegram' => $company_telegram,
									'youtube' => $company_youtube,
								));
								?>

							</div>
						<?php endif; ?>

					</div>
					<div class="header-right d-flex align-items-center justify-content-end">

						<div class="header-search"><!--open-->
							<button type="button" class="search-btn">
								<span class="ic icon-search"></span>
							</button>
							<div class="search-dropdown">
								<form action="<?php echo get_catalog_url(); ?>" method="get">
									<div class="search-container">
										<input type="text" name="search">
										<button class="search-button" type="submit">
											<span class="ic icon-search"></span>
										</button>
									</div>
								</form>
								<button type="button" class="search-close">
									<span class="ic icon-close"></span>
								</button>
							</div>
						</div>

						<?php
						/* 
						<button type="button" class="login-btn header-btn" data-toggle="modal" data-target="#login">
							<span class="ic icon-user"></span>
						</button>
						 */
						?>

						<a href="<?php echo get_permalink(WISHLIST_PAGE_ID); ?>" class="fav-btn header-btn">
							<span class="ic icon-fav"></span>
							<div class="count" data-count-wishlist>
								<?php echo count($wishlist); ?>
							</div>
						</a>

						<?php if (!is_page(CHECKOUT_PAGE_ID)): ?>
							<button class="front-get-cart cart-btn header-btn" type="button">
								<span class="ic icon-cart"></span>
								<div class="count" data-cart-product-count>
									<?php echo count($cart); ?>
								</div>
							</button>
						<?php endif; ?>

						<?php get_template_part('templates/languages-menu', null, array('class' => 'd-none d-md-block')); ?>

					</div>
				</div>
			</div>

			<?php $is_copy_enabled = get_field('header_running_advertisement_copy', 'option') == 1; ?>

			<?php if (!empty($header_running_advertisement)): ?>
				<div class="header-banner-container<?php echo $is_copy_enabled ? ' copy-container-js' : ''; ?>" <?php if ($is_copy_enabled): ?> data-copy-content="
				<?php echo $header_running_advertisement_promocode; ?>" <?php endif; ?>>

					<div class="header-banner">
						<?php for ($i = 0; $i <= 15; $i++): ?>
							<div class="slide">
								<?php echo $header_running_advertisement; ?>
							</div>
						<?php endfor; ?>
					</div>

					<?php if ($is_copy_enabled): ?>
						<div class="header-banner-container__hover">
							Настисніть, щоб скопіювати промокод <span>
								<?php echo $header_running_advertisement_promocode; ?>
							</span>
						</div>

						<div class="header-banner-container__success">
							Промокод <span>
								<?php echo $header_running_advertisement_promocode; ?>
							</span> скопійовано!
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</header>
		<main class="content">
