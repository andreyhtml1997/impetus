<?php
/**
 * === PROMO CODES (custom, unlimited, until deleted) ===
 * - CPT: promo_code
 * - Fields: code, type (percent|fixed), amount, active
 * - Checkout field + validation + discount application via fees
 * - Persists to order meta
 */

/** 1) CPT Promo Codes */
add_action('init', function () {
	register_post_type('promo_code', [
		'labels' => [
			'name' => 'Promo Codes',
			'singular_name' => 'Promo Code',
			'add_new' => 'Add Promo Code',
			'add_new_item' => 'Add New Promo Code',
			'edit_item' => 'Edit Promo Code',
			'new_item' => 'New Promo Code',
			'view_item' => 'View Promo Code',
			'search_items' => 'Search Promo Codes',
			'not_found' => 'No Promo Codes found',
			'not_found_in_trash' => 'No Promo Codes found in Trash',
			'menu_name' => 'Promo Codes',
		],
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_icon' => 'dashicons-tickets-alt',
		'supports' => ['title'],
	]);
});

/** 2) Meta boxes: code, type, amount, active */
add_action('add_meta_boxes', function () {
	add_meta_box('pc_fields', 'Promo Code Settings', function ($post) {
		$code = get_post_meta($post->ID, '_pc_code', true);
		$type = get_post_meta($post->ID, '_pc_type', true);      // percent|fixed
		$amount = get_post_meta($post->ID, '_pc_amount', true);
		$active = get_post_meta($post->ID, '_pc_active', true);

		wp_nonce_field('pc_save', 'pc_nonce');

		echo '<p><label>Code<br><input type="text" name="pc_code" value="' . esc_attr($code) . '" style="width:320px" placeholder="e.g. SUMMER10"></label></p>';
		echo '<p><label>Type<br>
			<select name="pc_type" style="width:320px">
				<option value="percent"' . selected($type, 'percent', false) . '>Percent (%)</option>
				<option value="fixed"' . selected($type, 'fixed', false) . '>Fixed amount</option>
			</select></label></p>';
		echo '<p><label>Amount<br><input type="number" step="0.01" min="0" name="pc_amount" value="' . esc_attr($amount) . '" style="width:320px" placeholder="10 or 100.00"></label></p>';
		echo '<p><label><input type="checkbox" name="pc_active" value="1" ' . checked($active, '1', false) . '> Active</label></p>';
	}, 'promo_code', 'normal', 'high');
});

add_action('save_post_promo_code', function ($post_id) {
	if (!isset($_POST['pc_nonce']) || !wp_verify_nonce($_POST['pc_nonce'], 'pc_save'))
		return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	$code = isset($_POST['pc_code']) ? strtoupper(trim(wp_unslash($_POST['pc_code']))) : '';
	$type = isset($_POST['pc_type']) ? sanitize_text_field($_POST['pc_type']) : 'percent';
	$amount = isset($_POST['pc_amount']) ? (float) $_POST['pc_amount'] : 0;
	$active = isset($_POST['pc_active']) ? '1' : '0';

	update_post_meta($post_id, '_pc_code', $code);
	update_post_meta($post_id, '_pc_type', $type === 'fixed' ? 'fixed' : 'percent');
	update_post_meta($post_id, '_pc_amount', max(0, $amount));
	update_post_meta($post_id, '_pc_active', $active);
});







/* ==== PROMO: helpers (cookie) ==== */

if (!function_exists('pc_get_active_promo_by_code')) {
	// Если ты уже добавил мою функцию ранее — этот блок пропустится
	function pc_get_active_promo_by_code($raw_code)
	{
		$code = strtoupper(trim((string) $raw_code));
		if (!$code)
			return null;

		$q = new WP_Query([
			'post_type' => 'promo_code',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'orderby' => 'menu_order', // твое правило
			'meta_query' => [
				[
					'key' => '_pc_code',
					'value' => $code
				],
				[
					'key' => '_pc_active',
					'value' => '1'
				],
			],
		]);
		if (!$q->have_posts())
			return null;
		$p = $q->posts[0];
		return [
			'id' => $p->ID,
			'code' => get_post_meta($p->ID, '_pc_code', true),
			'type' => get_post_meta($p->ID, '_pc_type', true),   // percent|fixed
			'amount' => (float) get_post_meta($p->ID, '_pc_amount', true),
		];
	}
}



/** 4) Checkout field */
add_filter('woocommerce_after_order_notes', function ($checkout) {
	echo '<div id="pc_checkout_field"><h3>' . esc_html__('��������', 'woocommerce') . '</h3>';
	woocommerce_form_field('pc_code', [
		'type' => 'text',
		'class' => ['form-row-wide'],
		'required' => false,
		'placeholder' => '������� �������� (���� ����)',
	], $checkout->get_value('pc_code'));
	echo '</div>';
});

/** 5) Validate + store code in session */
add_action('woocommerce_checkout_process', function () {
	if (!isset($_POST['pc_code']))
		return;

	$code = trim(wp_unslash($_POST['pc_code']));
	if (!$code) {
		// �������, ���� �����
		if (WC()->session)
			WC()->session->__unset('pc_applied');
		return;
	}

	$promo = pc_get_active_promo_by_code($code);
	if (!$promo) {
		wc_add_notice('�������� �� ������ ��� ��������.', 'error');
		// �� ��������� � ������
		if (WC()->session)
			WC()->session->__unset('pc_applied');
		return;
	}

	// �� � ��������� � ������
	if (WC()->session) {
		WC()->session->set('pc_applied', [
			'code' => $promo['code'],
			'type' => $promo['type'],
			'amount' => $promo['amount'],
		]);
	}
});

/** 6) Apply discount via negative fee (recalc-safe) */
add_action('woocommerce_cart_calculate_fees', function ($cart) {
	if (is_admin() && !defined('DOING_AJAX'))
		return;
	if (!$cart)
		return;

	$applied = WC()->session ? WC()->session->get('pc_applied') : null;
	if (!$applied)
		return;

	$subtotal = (float) $cart->get_subtotal();
	$discount = 0.0;

	if ($applied['type'] === 'fixed') {
		$discount = (float) $applied['amount'];
	} else {
		$percent = (float) $applied['amount'];
		$discount = $subtotal * ($percent / 100);
	}

	// �����������: �� ������ �����
	if ($discount > $subtotal)
		$discount = $subtotal;

	if ($discount > 0) {
		$label = '�������� ' . $applied['code'];
		$cart->add_fee($label, -abs($discount)); // ������������� fee = ������
	}
}, 20);

/** 7) Persist promo to order meta */
add_action('woocommerce_checkout_create_order', function ($order, $data) {
	$applied = WC()->session ? WC()->session->get('pc_applied') : null;
	if (!$applied)
		return;

	$order->update_meta_data('_pc_code', $applied['code']);
	$order->update_meta_data('_pc_type', $applied['type']);
	$order->update_meta_data('_pc_amount', $applied['amount']);
}, 10, 2);

/** 8) Show promo in admin order & emails */
add_action('woocommerce_admin_order_data_after_order_details', function ($order) {
	$code = $order->get_meta('_pc_code');
	if (!$code)
		return;
	$type = $order->get_meta('_pc_type');
	$amount = $order->get_meta('_pc_amount');
	echo '<p><strong>Promo Code:</strong> ' . esc_html($code) . ' (' . esc_html($type) . ' ' . esc_html($amount) . ')</p>';
});
add_filter('woocommerce_email_order_meta_fields', function ($fields, $sent_to_admin, $order) {
	$code = $order->get_meta('_pc_code');
	if ($code) {
		$fields['pc_code'] = [
			'label' => 'Promo Code',
			'value' => $code
		];
	}
	return $fields;
}, 10, 3);

/** 9) Auto-clear session promo after order placed */
add_action('woocommerce_thankyou', function () {
	if (WC()->session)
		WC()->session->__unset('pc_applied');
});



























function pc_cookie_name()
{
	return 'promo_code';
}

function pc_get_applied_code()
{
	return isset($_COOKIE[pc_cookie_name()]) ? strtoupper(trim($_COOKIE[pc_cookie_name()])) : '';
}

function pc_set_applied_code($code)
{
	$code = strtoupper(trim((string) $code));
	if (!$code)
		return;
	// 30 дней, путь - на весь сайт
	@setcookie(pc_cookie_name(), $code, time() + 60 * 60 * 24 * 30, '/', '', is_ssl(), true);
	$_COOKIE[pc_cookie_name()] = $code;
}

function pc_clear_applied_code()
{
	@setcookie(pc_cookie_name(), '', time() - 3600, '/', '', is_ssl(), true);
	unset($_COOKIE[pc_cookie_name()]);
}

function pc_calc_discount($cart_total, $promo)
{
	if (!$promo)
		return 0.0;
	$disc = 0.0;
	if ($promo['type'] === 'fixed') {
		$disc = (float) $promo['amount'];
	} else {
		$disc = ((float) $promo['amount']) * ((float) $cart_total) / 100;
	}
	if ($disc < 0)
		$disc = 0;
	if ($disc > $cart_total)
		$disc = $cart_total;
	return (float) $disc;
}

/* ==== PROMO: AJAX handlers ==== */

add_action('wp_ajax_apply_promo_code', 'front_apply_promo_code');
add_action('wp_ajax_nopriv_apply_promo_code', 'front_apply_promo_code');
function front_apply_promo_code()
{
	ajax_security(true);
	$code = isset($_POST['code']) ? strtoupper(trim(wp_unslash($_POST['code']))) : '';
	$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

	if (!$code) {
		pc_clear_applied_code();
		wp_send_json_error(['error' => ['info' => 'Вкажіть промокод.']]);
	}

	$promo = pc_get_active_promo_by_code($code);
	if (!$promo) {
		pc_clear_applied_code();
		wp_send_json_error(['error' => ['info' => 'Промокод не знайдено або вимкнено.']]);
	}

	pc_set_applied_code($promo['code']);

	$cart = get_cart();
	$cart_data = get_cart_data($cart, $post_id);

	wp_send_json_success([
		'status' => 1,
		'html' => $cart_data['html'],
		'cart_product_count' => $cart_data['cart_product_count'],
	]);
}

add_action('wp_ajax_remove_promo_code', 'front_remove_promo_code');
add_action('wp_ajax_nopriv_remove_promo_code', 'front_remove_promo_code');
function front_remove_promo_code()
{
	ajax_security(true);
	$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

	pc_clear_applied_code();

	$cart = get_cart();
	$cart_data = get_cart_data($cart, $post_id);

	wp_send_json_success([
		'status' => 1,
		'html' => $cart_data['html'],
		'cart_product_count' => $cart_data['cart_product_count'],
	]);
}

function pc_set_promo_flash($msg, $type = 'error')
{
	@setcookie('promo_flash_msg', $msg, time() + 60, '/', '', is_ssl(), true);
	@setcookie('promo_flash_type', $type, time() + 60, '/', '', is_ssl(), true);
}
function pc_get_promo_flash()
{
	$msg = isset($_COOKIE['promo_flash_msg']) ? $_COOKIE['promo_flash_msg'] : '';
	$type = isset($_COOKIE['promo_flash_type']) ? $_COOKIE['promo_flash_type'] : '';
	if ($msg) {
		@setcookie('promo_flash_msg', '', time() - 3600, '/', '', is_ssl(), true);
		@setcookie('promo_flash_type', '', time() - 3600, '/', '', is_ssl(), true);
		return [
			'msg' => $msg,
			'type' => ($type ?: 'error')
		];
	}
	return null;
}

// Фолбэк применения/сброса промокода при обычном submit
add_action('template_redirect', function () {
	if (!empty($_POST['pc_fallback'])) {
		$raw = isset($_POST['promo_code']) ? wp_unslash($_POST['promo_code']) : '';
		$code = strtoupper(trim((string) $raw));

		if ($code) {
			$promo = pc_get_active_promo_by_code($code);
			if ($promo) {
				pc_set_applied_code($promo['code']);
				pc_set_promo_flash('Промокод застосовано', 'success');
			} else {
				pc_clear_applied_code();
				pc_set_promo_flash('Промокод не знайдено або вимкнено', 'error');
			}
		} else {
			pc_clear_applied_code();
			pc_set_promo_flash('Вкажіть промокод', 'error');
		}

		$redirect = wp_get_referer();
		if (!$redirect)
			$redirect = get_permalink(defined('CHECKOUT_PAGE_ID') ? CHECKOUT_PAGE_ID : get_the_ID());
		wp_safe_redirect($redirect);
		exit;
	}
});


function get_cart_data($cart, $post_id = 0)
{

	$cart_html = '';
	$cart_total = 0;

	if (!empty($cart)) {

		$product_ids = array_keys($cart);

		$product_objs = get_products(array('product_ids' => $product_ids));
		if (!empty($product_objs)) {
			foreach ($cart as $product_id => &$sizes) {
				foreach ($sizes as $size_id => $quantity) {

					$_product_objs = wp_list_filter($product_objs, array('ID' => $product_id));
					$product_obj = array_shift($_product_objs);

					if (
						empty($product_obj) ||
						!in_array($size_id, $product_obj->product_size) ||
						!$product_obj->product_status ||
						!$product_obj->product_final_price
					) {
						unset($sizes[$size_id]);
						continue;
					}

					$cart_total += $product_obj->product_final_price * $quantity;

					$cart_html .= get_cart_item_html($product_obj, $size_id, $quantity);
				}

				if (empty($sizes))
					unset($cart[$product_id]);
			}

			unset($sizes);

		} else {
			$cart = array();
		}

		update_cart($cart);
	}

	if (empty($cart))
		$cart_html = '<div class="cart-empty d-flex justify-content-center">Кошик порожній</div>';



	$header_running_advertisement = get_field('header_running_advertisement', 'option');

	$header_running_advertisement_html = '';
	if (!empty($header_running_advertisement) && $post_id != CHECKOUT_PAGE_ID)
		$header_running_advertisement_html = sprintf(
			'<div class="cart-free">
			<div class="free-title">%1$s</div>
			<!--<div class="free-anons">Додайте ще товарів на сумму <b>400 грн</b> і отримай безкоштовну доставку</div>
			<div class="free-progress">
				<div class="total" style="width:60%%;"></div>
			</div>-->
		</div>' . PHP_EOL,
			$header_running_advertisement
		);


	$html = sprintf(
		'<div class="cart-top">
							<div class="cart-title d-inline-flex align-items-center">
								<span class="ic icon-cart"></span>
								<span class="value">Кошик</span>
							</div>
							%1$s
						</div>
						<div class="items h-100">
							%2$s
						</div>' . PHP_EOL,
		$header_running_advertisement_html,
		$cart_html
	);

	if (!empty($cart)) {



		// === PROMO: applied code & discount ===
		$applied_code = pc_get_applied_code();
		$applied_promo = null;
		$promo_discount = 0.0;

		if ($applied_code) {
			$applied_promo = pc_get_active_promo_by_code($applied_code);
			if (!$applied_promo) {
				pc_clear_applied_code();
			}
		}

		if ($applied_promo) {
			$promo_discount = pc_calc_discount($cart_total, $applied_promo);
		}

		$total_to_pay = max(0, $cart_total - $promo_discount);
		// === /PROMO ===


		if ($post_id == CHECKOUT_PAGE_ID) {

			// перед сборкой $promo_form_html:
			$promo_msg_html = '';
			$flash = pc_get_promo_flash(); // см. п.2 — вернёт ['msg' => '...', 'type' => 'error|success'] и очистит куки
			if ($flash) {
				$class = $flash['type'] === 'success' ? 'promo-msg--success' : 'promo-msg--error';
				$promo_msg_html = '<div class="promo-msg ' . $class . '">' . $flash['msg'] . '</div>';
			}


			// UI: форма промокода
			$promo_form_html = sprintf(
				'<div class="cart-promo">
						<div class="d-md-flex">
							<input type="text"
										 class="input"
										 name="promo_code"
										 placeholder="Вводь тут, якщо маєш промокод"
										 value="%2$s"
										 form="promo-fallback">
										 
							<button type="submit"
											class="submit-promo d-flex align-items-center justify-content-center"
											name="pc_fallback"
											value="1"
											form="promo-fallback">Застосувати</button>
						</div>
						<div data-promo-msg></div>
						%3$s
				 </div>',
				$post_id,
				($applied_promo ? $applied_promo['code'] : ''),
				($applied_promo ? '<div class="promo-applied small">Застосовано промокод <b>' . $applied_promo['code'] . '</b>. <a href="#" class="js-remove-promo">Скасувати</a></div>' : ''),
				$promo_msg_html
			);

			$cart_buy_html = sprintf(
				'<div class="buy-items">
						<div class="buy-item d-flex align-items-center justify-content-between">
							<span class="value">Товарів на суму:</span>
							<span class="data">%1$s грн</span>
						</div>

						%2$s

						%3$s

						<div class="buy-item d-flex align-items-center justify-content-between">
							<span class="value">Пакування та доставка:</span>
							<span class="data">Безкоштовно</span>
						</div>

						<div class="buy-item summ d-flex align-items-center justify-content-between">
							<span class="value">Всього:</span>
							<span class="data">%4$s грн</span>
						</div>
					</div>
					<div class="buy-buttons d-md-flex">
						<a href="#" id="front-place-order" class="btn-default cta">Оформити замовлення</a>
					</div>',
				number_format($cart_total, 2, '.', ' '),
				$promo_form_html,
				($promo_discount > 0 ? '<div class="buy-item d-flex align-items-center justify-content-between"><span class="value">Знижка за промокодом:</span><span class="data">- ' . number_format($promo_discount, 2, '.', ' ') . ' грн</span></div>' : ''),
				number_format($total_to_pay, 2, '.', ' ')
			);
		} else {
			// твой прежний блок для мини-корзины оставляй без изменений (или по желанию сюда тоже добавим поле промокода)
			$cart_buy_html = sprintf(
				'<div class="buy-total d-flex align-items-center justify-content-between">
					<span class="value">Товарів на суму:</span>
					<span class="data"><span data-cart-total>%1$s</span> грн</span>
				</div>
				<div class="buy-buttons d-md-flex">
					<a href="%2$s" class="btn-default cta">Оформити замовлення</a>
				</div>',
				number_format($cart_total, 2, '.', ' '),
				get_permalink(CHECKOUT_PAGE_ID)
			);
		}


		$html .= sprintf(
			'<div class="cart-bottom">
				<!--<div class="cart-promo">
					<form action="">
						<div class="d-md-flex">
							<input type="text" class="input" placeholder="Вводь тут, якщо маєш промокод">
							<input type="submit" value="Застосувати" class="submit-promo d-flex align-items-center justify-content-center">
						</div>
					</form>
				</div>-->
				<div class="cart-buy">
					%1$s
				</div>
			</div>' . PHP_EOL,
			$cart_buy_html
		);
	}


	return array(
		'html' => $html,
		'cart_product_count' => count($cart),
	);
}