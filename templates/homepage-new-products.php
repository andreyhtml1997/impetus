<?php

$products = get_products(array(
	'is_random' => true,
	'highlighted' => 'new',
	'audience_category' => get_audience_category_id_by_user_cookie_audience_category(),
	'per_page' => 10
));

$items = array();

if ($products) {
	foreach ($products as $i => $p) {

		$pid = 0;

		if (is_object($p)) {
			$pid = !empty($p->ID) ? (int) $p->ID : 0;
		}

		if (!$pid && is_array($p)) {
			$pid = !empty($p['ID']) ? (int) $p['ID'] : 0;
		}

		$in_stock = $pid ? (get_field('product_size', $pid) ? 1 : 0) : 0;

		$items[] = array(
			'idx' => (int) $i,
			'in_stock' => $in_stock,
			'p' => $p,
		);
	}
}

usort($items, function ($a, $b) {
	if ($a['in_stock'] === $b['in_stock']) {
		return $a['idx'] <=> $b['idx']; // сохранить исходный порядок внутри группы
	}

	return $b['in_stock'] <=> $a['in_stock']; // in-stock вперед
});

$sorted_products = array_map(function ($it) {
	return $it['p'];
}, $items);

get_template_part('templates/highlighted-products', null, array(
	'title' => 'Новинки',
	'product_objs' => $sorted_products,
	'catalog_url' => get_catalog_url(null, null, null, null, 'new'),
));
?>