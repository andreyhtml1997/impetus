<?php
get_template_part('templates/highlighted-products', null, array(
	'title' => 'Наші бестселлери',
	'product_objs' => get_products(array('is_random' => true, 'highlighted' => 'bestseller', 'audience_category' => get_audience_category_id_by_user_cookie_audience_category(), 'per_page' => 10)),
	'catalog_url' => get_catalog_url(null, null, null, null, 'bestseller'),
));
?>