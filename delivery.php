<?php 
/*Template Name: Доставка*/
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
				<h3>Доставка</h3>
				<div class="cabinet-delivery">
					<div class="item">
						<div class="delivery-top d-flex align-items-center justify-content-between">
							<div class="delivery-name">
								<span class="data">Доставка:</span>
								<span class="value">Новою поштою</span>
							</div>
							<button type="button" class="delivery-del d-inline-flex align-items-center">
								<span class="ic icon-trash"></span>
								<span class="value">Видалити</span>
							</button>
						</div>
						<div class="delivery-detail d-flex align-items-center">
							<div class="delivery-icon">
								<img src="<?php echo get_template_directory_uri();?>/images/delivery.svg" alt="">
							</div>
							<div>
								<div class="data">м. Київ</div>
								<div class="value">Відділення №3 (до 30 кг на одне місце) вул. Шевченка 23</div>
							</div>
						</div>
					</div>
					<a href="#" class="btn-default cta">Додати</a>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer();?>






