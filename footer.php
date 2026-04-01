<?php

$phone = get_field('administrator_phone', 'option');
$email = get_field('administrator_email', 'option');
$address = get_field('address', 'option');
$company_facebook = get_field('company_facebook', 'option');
$company_instagram = get_field('company_instagram', 'option');
$company_telegram = get_field('company_telegram', 'option');
$company_youtube = get_field('company_youtube', 'option');

$phone = replace_phone($phone);

?>

</main>
<script src="https://keepincrm.chat/chat-widget.js?widgetId=i8pJ5o7oNtmE" async></script>
<footer class="footer">
	<div class="container-fluid">
		<div class="footer-top d-lg-flex align-items-center">
			<div class="footer-left">
				<a href="<?php echo home_url(); ?>" class="logo">
					<img src="<?php echo get_template_directory_uri(); ?>/images/logo-white.svg" alt="Logo">
				</a>
			</div>

			<?php if (!empty($company_facebook) || !empty($company_instagram) || !empty($company_telegram) || !empty($company_youtube)): ?>
				<div class="footer-right w-100 d-lg-flex align-items-center justify-content-end">
					<div class="footer-title">Доєднуйтесь до нас в соціальних мережах</div>
					<div class="socials">

						<?php
						get_template_part('templates/socials', null, array(
							'instagram' => $company_instagram,
							'facebook' => $company_facebook,
							'telegram' => $company_telegram,
							'youtube' => $company_youtube,
						));
						?>

					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
	<div class="footer-middle">
		<div class="container-fluid d-lg-flex">

			<?php if (!empty($address) || !empty($email) || !empty($phone)): ?>
				<div class="footer-left">
					<div class="footer-name">Контакти</div>

					<?php if (!empty($phone)): ?>
						<a href="tel:<?php echo esc_attr($phone); ?>" class="footer-phone"><?php echo mask_phone($phone); ?></a>
					<?php endif; ?>

					<?php if (!empty($email)): ?>
						<a href="mailto:<?php echo antispambot($email); ?>" class="footer-email"><?php echo antispambot($email); ?></a>
					<?php endif; ?>

					<?php if (!empty($address)): ?>
						<div class="footer-adres"><?php echo esc_html($address); ?></div>
					<?php endif; ?>

					<div class="pays d-flex align-items-center ">
						<div class="item">
							<img alt="Image" src="<?php echo get_template_directory_uri(); ?>/images/visa.svg">
						</div>
						<div class="item">
							<img alt="Image" src="<?php echo get_template_directory_uri(); ?>/images/mastercard.svg">
						</div>
						<div class="item">
							<img alt="Image" src="<?php echo get_template_directory_uri(); ?>/images/liq.svg">
						</div>
					</div>

				</div>
			<?php endif; ?>

			<div class="footer-navs w-100 ">
				<div class="row justify-content-between flex-nowrap">

					<?php if (has_nav_menu('footer-mobile-menu') || has_nav_menu('footer-first-left-menu') || has_nav_menu('footer-second-left-menu') || has_nav_menu('footer-third-left-menu')): ?>
						<div class="col-12 col-lg-4 col-xl-7">
							<div class="footer-name">Категорії</div>

							<?php
							wp_nav_menu(array(
								'theme_location' => 'footer-mobile-menu',
								'menu' => FOOTER_MOBILE_MENU_ID,
								'menu_class' => 'nav flex-column d-flex d-xl-none',
								'container' => false,
								'items_wrap' => '<ul class="%2$s">%3$s</ul>',
							));
							?>

							<div class="row d-none d-xl-flex">

								<?php
								$params = array(
									FOOTER_FIRST_LEFT_MENU_ID => 'footer-first-left-menu',
									FOOTER_SECOND_LEFT_MENU_ID => 'footer-second-left-menu',
									FOOTER_THIRD_LEFT_MENU_ID => 'footer-third-left-menu',
								);
								?>

								<?php foreach ($params as $menu_id => $theme_location): ?>

									<?php
									if (!has_nav_menu($theme_location))
										continue;
									?>

									<div class="col">

										<?php
										wp_nav_menu(array(
											'theme_location' => $theme_location,
											'menu' => $menu_id,
											'menu_class' => 'nav flex-column',
											'container' => false,
											'items_wrap' => '<ul class="%2$s">%3$s</ul>',
										));
										?>

									</div>

								<?php endforeach; ?>

							</div>
						</div>
					<?php endif; ?>

					<?php if (has_nav_menu('footer-right-menu')): ?>
						<div class="col-12 col-lg-8 col-xl-5 d-none d-lg-block">
							<div class="footer-name">Клієнту</div>

							<?php
							wp_nav_menu(array(
								'theme_location' => 'footer-right-menu',
								'menu' => FOOTER_RIGHT_MENU_ID,
								'menu_class' => 'nav flex-wrap nv',
								'container' => false,
								'items_wrap' => '<ul class="%2$s">%3$s</ul>',
							));
							?>

						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="footer-bottom d-xl-flex align-items-center">
			<div class="footer-left">
				<div class="copy">©<?php echo date("Y"); ?> Underline Store Всі права захищені</div>
			</div>
			<div class="footer-right w-100 d-lg-flex align-items-center justify-content-between">
				<div class="links">
					<a
						href="<?php echo get_permalink(TERMS_AND_CONDITIONS_PAGE_ID); ?>"><?php echo get_the_title(TERMS_AND_CONDITIONS_PAGE_ID); ?></a>
					<a
						href="<?php echo get_permalink(PRIVACY_POLICY_PAGE_ID); ?>"><?php echo get_the_title(PRIVACY_POLICY_PAGE_ID); ?></a>
				</div>
				<div class="dev d-inline-flex align-items-center">
					<span class="value">Develop with love</span>
					<a href="https://esfirum.com" target="_blank">
						<img src="<?php echo get_template_directory_uri(); ?>/images/esfirum.svg" alt="Image">
					</a>
				</div>
			</div>
		</div>
	</div>
</footer>
</div>
<div id="notice"><!-- notices --></div>
<div class="d-none" data-toggle="modal" data-target="#cart"></div>

<?php if (is_page(CHECKOUT_PAGE_ID)): ?>
	<div id="payment-form" class="d-none"><!--payment form--></div>
<?php endif; ?>

<?php get_template_part('templates/ajax-preload'); ?>

<?php //get_template_part( 'templates/modal-login' ); ?>
<?php //get_template_part( 'templates/modal-forgot' ); ?>
<?php //get_template_part( 'templates/modal-register' ); ?>
<?php //get_template_part( 'templates/modal-quick' ); ?>

<?php get_template_part('templates/modal-cart'); ?>
<?php get_template_part('templates/modal-mobile-buy'); ?>

<?php if (is_singular('product')): ?>
	<?php get_template_part('templates/modal-share'); ?>
	<?php get_template_part('templates/modal-size-chart'); ?>
	<?php get_template_part('templates/modal-available'); ?>
<?php endif; ?>

<?php if (!is_page(CHECKOUT_PAGE_ID) && !is_page(THANKS_PAGE_ID)): ?>
	<?php get_template_part('templates/modal-subscribe'); ?>
<?php endif; ?>




<?php wp_footer(); ?>
<script>
	!function (t, e, c, n) {
		var s = e.createElement(c);
		s.async = 1, s.src = 'https://statics.esputnik.com/scripts/' + n + '.js';
		var r = e.scripts[0];
		r.parentNode.insertBefore(s, r);
		var f = function () {
			f.c(arguments);
		};
		f.q = [];
		f.c = function () {
			f.q.push(arguments);
		};
		t['eS'] = t['eS'] || f;
	}(window, document, 'script', 'A3767702FC534C0981CF70DB8D982C07');
</script>
<script>eS('init');</script>
</body>

</html>