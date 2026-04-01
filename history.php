<?php 
/*Template Name: Історія*/
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
				<h3>історія замовлень</h3>
				<div class="cabinet-orders">
					<div class="orders-header d-flex align-items-center justify-content-between">
						<div class="item number">Номер замовлення</div>
						<div class="item status">Статус</div>
						<div class="item date">Дата замовлення</div>
						<div class="item summa">Сумма замовлення</div>
					</div>
					<div class="orders-item">
						<button type="button" class="orders-button w-100 d-flex align-items-center justify-content-between collapsed"  data-toggle="collapse" href="#order-1" role="button" aria-expanded="false">
							<div class="item info d-flex align-items-center">
								<div class="number d-flex align-items-center">
									<span class="ic icon-order"></span>
									<span class="value">#89798379</span>
								</div>
								<div class="stat d-flex align-items-center">
									<span class="icon">
										<img src="<?php echo get_template_directory_uri();?>/images/ok.svg" alt="">
									</span>
									<span class="value">Замовлення отримано</span>
								</div>
							</div>
							<div class="item date">17 Квітня 2025</div>
							<div class="item summa d-inline-flex align-items-center justify-content-center">
								<span class="value">2 220 грн</span>
								<span class="ic icon-down"></span>
							</div>
						</button>
						<div class="orders-info collapse" id="order-1">
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o1.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o2.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-detail d-md-flex justify-content-between">
								<div class="detail-info">
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Отримувач:</span>
										<span class="value">
											<div>Чорнява Анастасія</div>
											<div>+38 (099) 234-55-22</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Оплата</span>
										<span class="value">
											<div>Оплата карткою</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Доставка:</span>
										<span class="value">
											<div>м. Львів, поштомат “Нова Пошта”</div>
											<div>№ 88333, вул. Шевченка 223</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Номер ТТН:</span>
										<span class="value">
											<div>3472492943480</div>
										</span>
									</div>
								</div>
								<div class="detail-steps">
									<div class="step">
										<div class="data">Отримали ваше замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Комплектація замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прямує до вашого міста</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прибула у відділення</div>
										<div class="value">18 Березня 2025</div>
									</div>
									<div class="step whait">
										<div class="data">Повернення товару</div>
										<div class="value">18 Березня 2025</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="orders-item">
						<button type="button" class="orders-button w-100 d-flex align-items-center justify-content-between collapsed"  data-toggle="collapse" href="#order-2" role="button" aria-expanded="false">
							<div class="item info d-flex align-items-center">
								<div class="number d-flex align-items-center">
									<span class="ic icon-order"></span>
									<span class="value">#89798379</span>
								</div>
								<div class="stat d-flex align-items-center">
									<span class="icon">
										<img src="<?php echo get_template_directory_uri();?>/images/ok.svg" alt="">
									</span>
									<span class="value">Замовлення отримано</span>
								</div>
							</div>
							<div class="item date">17 Квітня 2025</div>
							<div class="item summa d-inline-flex align-items-center justify-content-center">
								<span class="value">2 220 грн</span>
								<span class="ic icon-down"></span>
							</div>
						</button>
						<div class="orders-info collapse" id="order-2">
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o1.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o2.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-detail d-md-flex justify-content-between">
								<div class="detail-info">
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Отримувач:</span>
										<span class="value">
											<div>Чорнява Анастасія</div>
											<div>+38 (099) 234-55-22</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Оплата</span>
										<span class="value">
											<div>Оплата карткою</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Доставка:</span>
										<span class="value">
											<div>м. Львів, поштомат “Нова Пошта”</div>
											<div>№ 88333, вул. Шевченка 223</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Номер ТТН:</span>
										<span class="value">
											<div>3472492943480</div>
										</span>
									</div>
								</div>
								<div class="detail-steps">
									<div class="step">
										<div class="data">Отримали ваше замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Комплектація замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прямує до вашого міста</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прибула у відділення</div>
										<div class="value">18 Березня 2025</div>
									</div>
									<div class="step whait">
										<div class="data">Повернення товару</div>
										<div class="value">18 Березня 2025</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="orders-item">
						<button type="button" class="orders-button w-100 d-flex align-items-center justify-content-between collapsed"  data-toggle="collapse" href="#order-3" role="button" aria-expanded="false">
							<div class="item info d-flex align-items-center">
								<div class="number d-flex align-items-center">
									<span class="ic icon-order"></span>
									<span class="value">#89798379</span>
								</div>
								<div class="stat d-flex align-items-center">
									<span class="icon">
										<img src="<?php echo get_template_directory_uri();?>/images/ok.svg" alt="">
									</span>
									<span class="value">Замовлення отримано</span>
								</div>
							</div>
							<div class="item date">17 Квітня 2025</div>
							<div class="item summa d-inline-flex align-items-center justify-content-center">
								<span class="value">2 220 грн</span>
								<span class="ic icon-down"></span>
							</div>
						</button>
						<div class="orders-info collapse" id="order-3">
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o1.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-order d-flex">
								<a href="#" class="item info d-flex align-items-center">
									<div class="item-image">
										<img src="<?php echo get_template_directory_uri();?>/images/o2.jpg" alt="">
									</div>
									<div>
										<div class="item-name">Aara піжама з короткими шортами</div>
										<div class="item-size">
											<span class="data">Size:</span>
											<span class="value">XS</span>
										</div>
									</div>
								</a>
								<div class="item date align-self-center">1 шт.</div>
								<div class="item summa align-self-center">
									<div class="old-price">1 200 грн</div>
									<div class="price">800 грн</div>
								</div>
							</div>
							<div class="info-detail d-md-flex justify-content-between">
								<div class="detail-info">
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Отримувач:</span>
										<span class="value">
											<div>Чорнява Анастасія</div>
											<div>+38 (099) 234-55-22</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Оплата</span>
										<span class="value">
											<div>Оплата карткою</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Доставка:</span>
										<span class="value">
											<div>м. Львів, поштомат “Нова Пошта”</div>
											<div>№ 88333, вул. Шевченка 223</div>
										</span>
									</div>
									<div class="info-item d-md-flex align-items-start">
										<span class="data">Номер ТТН:</span>
										<span class="value">
											<div>3472492943480</div>
										</span>
									</div>
								</div>
								<div class="detail-steps">
									<div class="step">
										<div class="data">Отримали ваше замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Комплектація замовлення</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прямує до вашого міста</div>
										<div class="value">17 Березня 2025</div>
									</div>
									<div class="step">
										<div class="data">Прибула у відділення</div>
										<div class="value">18 Березня 2025</div>
									</div>
									<div class="step whait">
										<div class="data">Повернення товару</div>
										<div class="value">18 Березня 2025</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="cabinet-orders">
					<div class="no-orders text-center">
						<div class="orders-image">
							<img src="<?php echo get_template_directory_uri();?>/images/orders.svg" alt="">
						</div>
						<div class="no-text">нажаль замовлення відсутні</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php get_footer();?>






