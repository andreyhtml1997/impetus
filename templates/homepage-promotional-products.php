<?php

$products = get_products(array(
	'is_random' => true,
	'highlighted' => 'sale',
	'audience_category' => get_audience_category_id_by_user_cookie_audience_category(),
	'per_page' => 10
));

$decorated = array();

if ($products) {
	foreach ($products as $i => $p) {

		$pid = 0;

		if (is_object($p) && !empty($p->ID)) {
			$pid = (int) $p->ID;
		} elseif (is_array($p) && !empty($p['ID'])) {
			$pid = (int) $p['ID'];
		}

		$in_stock = $pid && get_field('product_size', $pid) ? 1 : 0;

		$decorated[] = array(
			'idx' => (int) $i,      // чтобы сохранить исходный порядок внутри групп
			'in_stock' => $in_stock,
			'p' => $p,
		);
	}
}

usort($decorated, function ($a, $b) {
	return ($b['in_stock'] <=> $a['in_stock']) ?: ($a['idx'] <=> $b['idx']);
});

$sorted_products = $decorated ? array_column($decorated, 'p') : array();

get_template_part('templates/highlighted-products', null, array(
	'title' => 'Знижки',
	'product_objs' => $sorted_products,
	'catalog_url' => get_catalog_url(null, null, null, null, 'sale'),
));

?>