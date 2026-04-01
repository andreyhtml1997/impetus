<?php

/* Template Name: Дякуємо */

$classes = array('no-banner');

global $order_id;

$order_id = 0;
if (isset($_GET['order_id'])) {
	$order_id = absint($_GET['order_id']);
}

get_header();

?>

<section class="order-thanks d-flex align-items-center justify-content-center">
	<div class="container-fluid">
		<div class="thanks-image">
			<img src="<?php echo get_template_directory_uri(); ?>/images/thanks.svg" alt="Image">
		</div>
		<h2>Дякуємо за ваше замовлення <br />в магазині <span>underline shop!</span></h2>
		<div class="thanks-anons">Номер вашого замовлення <b><?php echo $order_id; ?></b>. Наші менеджери вже починають
			готувати ваше замовлення для відправки.</div>
		<a href="<?php echo home_url(); ?>" class="btn-more back d-inline-flex align-items-center justify-content-center">
			<span class="icon d-flex align-items-center justify-content-end">
				<span class="ic icon-right"></span>
				<span class="ic icon-right"></span>
			</span>
			<span class="value">повернутись на головну</span>
		</a>
	</div>
</section>



<?php
$stored = array();
$email_order_data = array();

if (!empty($order_id)) {
	$stored = get_option('impetus_order_' . $order_id);
	if (is_array($stored)) {
		if (!empty($stored['email_order_data'])) {
			if (is_array($stored['email_order_data'])) {
				$email_order_data = $stored['email_order_data'];
			}
		}
	}
}

$value = 0;
$currency = 'UAH';
$items = array();

if (!empty($email_order_data)) {
	if (isset($email_order_data['grand_total'])) {
		$value = (float) $email_order_data['grand_total'];
	}
	if (empty($value)) {
		if (isset($email_order_data['cart_total'])) {
			$value = (float) $email_order_data['cart_total'];
		}
	}
	if (isset($email_order_data['items'])) {
		if (is_array($email_order_data['items'])) {
			$items = $email_order_data['items'];
		}
	}
}
?>

<?php if (!empty($order_id)) { ?>
	<script>
		(function () {
			var orderId = <?php echo json_encode((string) $order_id); ?>;
			var key = 'ga4_purchase_sent_' + orderId;

			try {
				if (localStorage.getItem(key) === '1') return;
			} catch (e) { }

			// 1) GTM-friendly событие (то, что ловит маркетолог)
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({
				event: 'purchase',
				ecommerce: {
					transaction_id: orderId,
					currency: <?php echo json_encode($currency); ?>,
					value: <?php echo json_encode((float) $value); ?>,
					items: <?php echo wp_json_encode($items); ?>
				}
			});

			// ✅ дедуп ставим сразу, чтобы не было повторов при перезагрузке
			try { localStorage.setItem(key, '1'); } catch (e) { }





			// 2) GA4 через gtag (если нужно)
			/*
			var payload = {
				transaction_id: orderId,
				currency: <?php echo json_encode($currency); ?>,
			value: <?php echo json_encode((float) $value); ?>,
				items: <?php echo wp_json_encode($items); ?>
		};

		var tries = 0;
		(function waitGtag() {
			if (typeof window.gtag === 'function') {
				window.gtag('event', 'purchase', payload);
				return;
			}
			if (++tries < 30) setTimeout(waitGtag, 100);
		})();*/



							}) ();
	</script>

<?php } ?>





<?php get_footer(); ?>