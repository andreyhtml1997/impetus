<?php

/* Template Name: Чекаут */

$cart = get_cart();

$cart_data = get_cart_data($cart, CHECKOUT_PAGE_ID);




$ga4_currency = 'UAH';

$ga4_items = [];
$ga4_value = 0;

if (!empty($cart) && is_array($cart)) {
	foreach ($cart as $row) {

		// попробуем аккуратно достать данные из структуры корзины
		$product_id = 0;
		if (!empty($row['product_id']))
			$product_id = (int) $row['product_id'];
		if (!$product_id && !empty($row['id']))
			$product_id = (int) $row['id'];

		$qty = 1;
		if (!empty($row['quantity']))
			$qty = (int) $row['quantity'];
		if ($qty < 1)
			$qty = 1;

		$price = 0;
		if (isset($row['final_price']))
			$price = (float) $row['final_price'];
		elseif (isset($row['price']))
			$price = (float) $row['price'];

		$name = '';
		if (!empty($row['name']))
			$name = (string) $row['name'];
		elseif ($product_id)
			$name = (string) get_the_title($product_id);

		$variant = '';
		if (!empty($row['size_name']))
			$variant = (string) $row['size_name'];
		elseif (!empty($row['size']))
			$variant = (string) $row['size'];

		// минимум для GA4: id + name + price
		if (!$product_id || !$name || $price <= 0)
			continue;

		$ga4_value += $price * $qty;

		$item = [
			'item_id' => (string) $product_id,
			'item_name' => $name,
			'quantity' => $qty,
			'price' => $price,
		];

		if ($variant) {
			$item['item_variant'] = $variant;
		}

		$ga4_items[] = $item;
	}
}

$ga4_value = (float) $ga4_value;

// хеш корзины — чтобы не дублить begin_checkout при обновлении страницы,
// но чтобы событие снова отправлялось, если корзина реально поменялась
$ga4_cart_hash = md5(wp_json_encode($ga4_items) . '|' . $ga4_value);


$payment_note = get_field('page_checkout_payment_note');

get_header();

?>
<form id="promo-fallback" class="js-promo-form" method="post" data-postid="<?php echo CHECKOUT_PAGE_ID; ?>"
	style="display:none"></form>
<section class="order-section">
	<div class="container-fluid">
		<form action="">
			<div class="order-container d-xl-flex justify-content-between">
				<div class="order-info">
					<div class="order-block">
						<div class="title-container d-md-flex align-items-center justify-content-between">
							<h3>Ваші контактні дані</h3>
							<!--<div class="order-register d-flex align-items-center">
								<span class="value">Зареєструватись</span>
								<div class="radio">
									<label>
										<input type="radio" name="pol1">
										<span>Так</span>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="pol1" checked>
										<span>Ні</span>
									</label>
								</div>
							</div>-->
						</div>
						<div class="input-container">
							<label class="label">Ім’я</label>
							<div>
								<input class="input important" type="text" name="firstname">
							</div>
						</div>
						<div class="input-container">
							<label class="label">Прізвище</label>
							<div>
								<input class="input important" type="text" name="lastname">
							</div>
						</div>
						<div class="input-container">
							<label class="label">Телефон</label>
							<div>
								<input class="input important phone" type="text" name="phone">
							</div>
						</div>
						<!--<div class="input-anons">Перевірте, будь ласка, правильність введеного номеру телефону</div>-->
						<div class="input-container">
							<label class="label">Email</label>
							<div>
								<input class="input important" type="text" name="email">
							</div>
						</div>
					</div>



					<div class="order-block">
						<h3>Спосіб доставки</h3>

						<?php foreach (delivery_types() as $key => $value): ?>
							<div class="radio">
								<label>
									<input type="radio" name="delivery_type" value="<?php echo $key; ?>">
									<span><?php echo $value; ?></span>
								</label>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="order-block">
						<h3>Адреса доставки</h3>

						<div class="row">
							<div class="col-12">
								<div class="input-container">
									<select class="js-select-city" name="location" data-placeholder="Населений пункт">
										<option value="0">Оберіть населений пункт</option>
									</select>
									<input type="hidden" name="location_text" value="">

									<div class="sel-drop"></div>
								</div>
							</div>

							<div class="col-12 js-warehouse-wrap">
								<div class="input-container">
									<select class="js-select-warehouse" name="warehouse" data-placeholder="Оберіть відділення">
										<option value="0">Оберіть відділення</option>
									</select>
									<input type="hidden" name="warehouse_text" value="">

									<div class="sel-drop"></div>
								</div>
							</div>
							<div class="col-12 js-address-wrap" style="display:none;">
								<div class="input-container">
									<label class="label">Адреса (вулиця, будинок, квартира)</label>
									<div class="">
										<input class="input important" type="text" name="address_full">
									</div>
								</div>
							</div>


							<div class="col-12 js-address-wrap js-express-wrap" style="display:none;">
								<div class="radio">
									<label>
										<input type="checkbox" name="address_express" value="1">
										<span>Експрес доставка курʼєром день в день (Київ та околиці)</span>
									</label>
								</div>
							</div>
						</div>
					</div>




					<div class="order-block">
						<h3>Спосіб оплати</h3>

						<?php foreach (payment_types() as $key => $value): ?>
							<div class="radio">
								<label>
									<input type="radio" name="payment_type" value="<?php echo $key; ?>">
									<span><?php echo esc_html($value); ?></span>
								</label>
							</div>
						<?php endforeach; ?>

						<div class="order-anons"><?php echo wpautop($payment_note); ?></div>
						<textarea class="textarea" name="comment" placeholder="Коментар (необов’язково)"></textarea>
					</div>
				</div>
				<div class="order-cart">
					<div class="sticky-cart">
						<div class="modal-cart d-flex flex-column">
							<?php echo $cart_data['html']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="sticky-stop"></div>
		</form>
	</div>
</section>
<?php if (!empty($ga4_items) && $ga4_value > 0): ?>
	<script>
		(function () {
			var cartHash = <?php echo json_encode($ga4_cart_hash); ?>;
			var key = 'ga4_begin_checkout_sent_' + cartHash;

			try {
				if (sessionStorage.getItem(key) === '1') return;
			} catch (e) { }

			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({ ecommerce: null });
			window.dataLayer.push({
				event: 'begin_checkout',
				ecommerce: {
					currency: <?php echo json_encode($ga4_currency); ?>,
				value: <?php echo json_encode((float) $ga4_value); ?>,
					items: <?php echo wp_json_encode($ga4_items); ?>
					}
				});

		try { sessionStorage.setItem(key, '1'); } catch (e) { }
			}) ();
	</script>
<?php endif; ?>

<?php get_footer(); ?>