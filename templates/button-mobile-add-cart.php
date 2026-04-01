<?php
$product_id = absint($args['product_id'] ?? 0);
if (!$product_id) {
	return;
}

$is_in_stock = isset($args['is_in_stock'])
	? absint($args['is_in_stock'])
	: (get_field('product_status', $product_id) ? 1 : 0);

if (!$is_in_stock) {
	echo get_mobile_not_in_stock_product_html();
	return;
}

echo !is_product_in_cart($product_id)
	? get_mobile_add_product_to_cart_html()
	: get_mobile_added_product_to_cart_html();
