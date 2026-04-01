<?php

function mw_product_is_in_stock($product)
{
  $pid = is_object($product) ? (int) ($product->ID ?? 0) : (int) $product;

  // если get_products() уже подтянул product_status в объект — используем его
  $status = (is_object($product) && isset($product->product_status))
    ? $product->product_status
    : ($pid ? get_post_meta($pid, 'product_status', true) : 0);

  return (int) $status === 1;
}

function mw_sort_products_in_stock_first($products)
{
  if (empty($products) || !is_array($products))
    return array();

  $decor = array();

  foreach ($products as $i => $p) {
    $decor[] = array(
      'i' => (int) $i,                       // сохраняем исходный порядок внутри групп
      's' => mw_product_is_in_stock($p) ? 1 : 0, // 1 = в наличии (product_status = 1)
      'p' => $p,
    );
  }

  usort($decor, function ($a, $b) {
    return ($b['s'] <=> $a['s']) ?: ($a['i'] <=> $b['i']);
  });

  return array_column($decor, 'p');
}

