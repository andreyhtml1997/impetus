<?php /*Template Name: Нова доставка*/
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
				<div class="cabinet-delivery">
					<div class="order-block">
						<h3>Спосіб доставки</h3>
						<div class="radio">
							<label>
								<input type="radio" name="dost">
								<span>Доставка у відділення Нової пошти</span>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="dost">
								<span>Доставка кур`єром Нової пошти на адресу</span>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="dost">
								<span>Доставка в поштомат Нової пошти</span>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="dost">
								<span>УкрПошта</span>
							</label>
						</div>
					</div>
					<div class="order-block">
						<h3>адреса доставки</h3>
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="input-container">
									<select class="js-select" data-placeholder="Область">
										<option value=""></option>
										<option value="1">Київська</option>
										<option value="2">Київська</option>
										<option value="3">Київська</option>
									</select>
									<div class="sel-drop"></div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="input-container">
									<select class="js-select" data-placeholder="Місто">
										<option value=""></option>
										<option value="1">Київ</option>
										<option value="2">Київ</option>
										<option value="3">Київ</option>
									</select>
									<div class="sel-drop"></div>
								</div>
							</div>
							<div class="col-12">
								<div class="input-container">
									<select class="js-select" data-placeholder="Оберіть номер відділення">
										<option value=""></option>
										<option value="1">2345</option>
										<option value="2">3245</option>
										<option value="3">2354</option>
									</select>
									<div class="sel-drop"></div>
								</div>
							</div>
							<div class="col-12">
								<div class="delivery-buttons d-flex">
									<button type="button" class="btn-border but">Скасувати</button>
									<button type="button" class="btn-default but">Зберегти зміни</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer();?>






