<?php

$badges = array();

// Sale
if (!empty($args['product_obj']->is_product_sale)) {
	$badges[] = array(
		'class' => 'sale',
		'text' => 'Sale',
	);
}

// Bestseller
if (!empty($args['product_obj']->is_product_bestseller)) {
	$badges[] = array(
		'class' => 'best',
		'text' => 'Bestseller',
	);
}

// New
if (!empty($args['product_obj']->is_product_new)) {
	$badges[] = array(
		'class' => 'new',
		'text' => 'New',
	);
}

// Expected
if (!empty($args['product_obj']->is_product_expected)) {
	$badges[] = array(
		'class' => 'expected',
		'text' => 'Очікується', // или 'Очікується'
	);
}

// Если нет ни одного статуса — ничего не выводим
if (empty($badges)) {
	return;
}
?>

<div class="shilds">
	<?php foreach ($badges as $badge): ?>
		<div class="shild <?php echo $badge['class']; ?>"><?php echo $badge['text']; ?></div>
	<?php endforeach; ?>
</div>