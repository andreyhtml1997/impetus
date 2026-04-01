<div class="modal fade" id="quick" tabindex="-1" aria-labelledby="quickLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close"><span class="ic icon-close"></span></button>
			<div class="quick-buy">
				<div class="quick-title">швидке замовлення</div>
				<div class="items">
					<div class="item d-flex ">
						<button type="button" class="item-delete align-self-center">
							<span class="ic icon-trash"></span>
						</button>
						<a href="#" class="item-image">
							<img src="<?php echo get_template_directory_uri();?>/images/c1.jpg" alt="">
						</a>
						<div class="item-info w-100 d-flex flex-column align-items-start justify-content-between">
							<div>
								<a href="#" class="item-name">Aara піжама з короткими шортами</a>
								<div class="item-size"><span>Size:</span>XS</div>
							</div>
							<div class="item-buy w-100 d-md-flex align-items-center justify-content-end">
								<div class="item-cta d-flex align-items-center">
									<div class="cart-quantity d-flex align-items-center justify-content-between">
										<button class="quant-button quant-minus" data-number="-1"><span class="ic icon-minus"></span></button>
										<input type="text" class="quant-input" name="quant" value="1" data-quantity="1">
										<button class="quant-button quant-plus" data-number="1"><span class="ic icon-plus"></span></button>
									</div>
								</div>
								<div class="item-price d-inline-flex align-items-end">
									<div class="price">800 грн</div>
									<div class="old-price">1 200 грн</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="quick-form">
					<div class="quick-title">ваші данні</div>
					<form action="">
						<div class="form-container d-lg-flex">
							<div class="input-container">
								<label class="label">Ваше ім’я</label>
								<input type="text" class="input">
							</div>
							<div class="input-container">
								<label class="label">Ваш телефон</label>
								<input type="text" class="input phone">
							</div>
							<div class="input-container">
								<input type="submit" class="btn-default w-100" value="Підтвердити">
							</div>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="pol1">
								<span>Новий клієнт? Створити аккаунт</span>
							</label>
						</div>
					</form>
				</div>
				<div class="quick-other">
					<div class="quick-title">з цими товарами купують</div>
					<div class="row">
						<div class="col-12 col-md-6">
							<a href="#" class="item d-flex align-items-center">
								<div class="item-image">
									<img src="<?php echo get_template_directory_uri();?>/images/c3.jpg" alt="">
								</div>
								<div>
									<div class="item-name">Домашні капці aruelle</div>
									<div class="item-price d-inline-flex align-items-end">
										<div class="price">800 грн</div>
										<div class="old-price">1 200 грн</div>
									</div>
								</div>
							</a>
						</div>
						<div class="col-12 col-md-6">
							<a href="#" class="item d-flex align-items-center">
								<div class="item-image">
									<img src="<?php echo get_template_directory_uri();?>/images/c4.jpg" alt="">
								</div>
								<div>
									<div class="item-name">Домашні капці aruelle</div>
									<div class="item-price d-inline-flex align-items-end">
										<div class="price">800 грн</div>
										<div class="old-price">1 200 грн</div>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>