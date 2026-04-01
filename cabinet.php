<?php 
/*Template Name: Каібінет*/
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
				<h3>особистий кабінет</h3>
				<div class="cabinet-form">
					<form action="">
						<div class="input-container ok">
							<label class="label">Ім’я</label>
							<input type="text" class="input" value="Анастасія">
						</div>
						<div class="input-container ok">
							<label class="label">Прізвище</label>
							<input type="text" class="input" value="Хижа">
						</div>
						<div class="input-container ok">
							<label class="label">Телефон</label>
							<input type="text" class="input phone" value="+38 (097) 060 15 17">
						</div>
						<div class="input-container ok">
							<label class="label">Email</label>
							<input type="text" class="input" value="example@gmail.com">
						</div>
						<div class="input-container">
							<input type="submit" class="btn-default submit" value="Зберегти зміни">
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="sub" checked>
								<span>Підписатись на оновлення від UNDERLINE Store</span>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="sub">
								<span>Відписатись від россилки</span>
							</label>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer();?>






