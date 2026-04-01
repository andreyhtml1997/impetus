<?php 
/*Template Name: Зміна пароля*/
$classes = array( 'no-banner' );
get_header();?>
<section class="cabinet-section">
	<div class="container-fluid">
		<div class="cabinet-container d-md-flex justify-content-between">
			<div class="cabinet-left">
				<div class="cabinet-nav">
					<ul class="nav flex-column">
						<?php 
							$menu_name= 'cabinet_menu';
							$locations = get_nav_menu_locations();
							$menu = wp_get_nav_menu_object($locations[$menu_name]);
							$menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
							$repeater_field = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
							foreach($menuitems as $menu_item) { ?>
							<li>
								<a href="<?php echo $menu_item->url; ?>" class="nav-item d-flex align-items-center <?php echo vince_check_active_menu($menu_item)?>">
									<span class="ic icon-<?php echo $menu_item->classes[0]; ?>"></span>
									<span class="value"><?php echo $menu_item->title; ?></span>
								</a>
							</li>
						<?php } ?>
						<li>
							<a href="#" class="nav-item d-flex align-items-center">
								<span class="ic icon-cabinet6"></span>
								<span class="value">Вихід</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="cabinet-info">
				<h3>зміна паролю</h3>
				<div class="cabinet-form">
					<form action="">
						<div class="input-container ok">
							<label class="label">Поточний пароль</label>
							<input type="password" class="input" value="dg">
						</div>
						<div class="input-container ok">
							<label class="label">Новий пароль</label>
							<input type="password" class="input" value="dg">
						</div>
						<div class="input-container">
							<label class="label">Підтвердіть новий пароль</label>
							<input type="password" class="input" value="">
						</div>
						<div class="input-container d-md-flex align-items-center">
							<input type="submit" class="btn-default submit" value="Зберегти зміни">
							<div class="pass-forgot">
								Упс, забув (-ла) пароль.
								<button type="button" class="forgot">Натисніть для відновлення.</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer();?>






