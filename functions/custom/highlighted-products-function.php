<?php
function get_related_products_mixed( $product_obj, $limit = 2 ) {
	$result_posts = array();
	$current_id = 0;
	if (!empty($product_obj->ID)) {
		$current_id = intval($product_obj->ID);
	}

	// ===== 1) РУЧНЫЕ (similar_products), РАНДОМ ВНУТРИ =====
	$manual_ids = array();
	$manual_raw = get_field('similar_products', $current_id);
	if (!empty($manual_raw)) {
		foreach ((array)$manual_raw as $item) {
			if (is_object($item)) {
				$manual_ids[] = intval($item->ID);
			} else {
				$manual_ids[] = intval($item);
			}
		}
	}
	if (!empty($manual_ids)) {
		$manual_ids = array_diff(array_values(array_unique($manual_ids)), array($current_id));
		if (!empty($manual_ids)) {
			$q_manual = new WP_Query(array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'post__in'            => $manual_ids,
				'posts_per_page'      => $limit,
				'orderby'             => 'rand', // рандом по ручным
				'meta_query'          => array(
					array(
						'key'   => 'product_status',
						'value' => '1',
					),
				),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			));
			if (!empty($q_manual->posts)) {
				foreach ($q_manual->posts as $p) {
					$result_posts[] = $p;
					if (count($result_posts) >= $limit) {
						return $result_posts;
					}
				}
			}
		}
	}

	// ===== ДАННЫЕ ТЕКУЩЕЙ ГРУППЫ =====
	$group_id = 0;
	if (!empty($product_obj->product_group)) {
		$group_id = intval($product_obj->product_group);
	}
	$audience_id  = 0;
	$category_ids = array();

	if ($group_id) {
		$gkey = 'product_group_' . $group_id;

		$aud = get_field('product_audience_category', $gkey); // Term Object или ID
		if (!empty($aud)) {
			if (is_object($aud)) {
				if (!empty($aud->term_id)) {
					$audience_id = intval($aud->term_id);
				}
			} else {
				$audience_id = intval($aud);
			}
		}

		$cats = get_field('product_product_category', $gkey); // массив Term Object / ID
		if (!empty($cats)) {
			foreach ((array)$cats as $c) {
				if (is_object($c)) {
					if (!empty($c->term_id)) {
						$category_ids[] = intval($c->term_id);
					}
				} else {
					$category_ids[] = intval($c);
				}
			}
			if (!empty($category_ids)) {
				$category_ids = array_values(array_unique($category_ids));
			}
		}
	}

	// ===== РАЗБИВКА ГРУПП НА КОРЗИНЫ =====
	$bucket_same_gender_same_cat  = array(); // 2
	$bucket_same_gender_other_cat = array(); // 3

	$all_groups = get_terms(array(
		'taxonomy'   => 'product_group',
		'hide_empty' => false,
		'fields'     => 'ids',
	));

	if (!is_wp_error($all_groups)) {
		if (!empty($all_groups)) {
			foreach ($all_groups as $gid) {
				// по желанию можно исключить текущую группу, чтобы не показывать её вариации:
				// if ($group_id) { if (intval($gid) === $group_id) { continue; } }

				$gkey2 = 'product_group_' . intval($gid);

				$g_aud_id = 0;
				$aud2 = get_field('product_audience_category', $gkey2);
				if (!empty($aud2)) {
					if (is_object($aud2)) {
						if (!empty($aud2->term_id)) {
							$g_aud_id = intval($aud2->term_id);
						}
					} else {
						$g_aud_id = intval($aud2);
					}
				}

				$is_same_gender = false;
				if ($audience_id) {
					if ($g_aud_id === $audience_id) {
						$is_same_gender = true;
					}
				}

				if ($is_same_gender) {
					$g_cat_ids = array();
					$cats2 = get_field('product_product_category', $gkey2);
					if (!empty($cats2)) {
						foreach ((array)$cats2 as $gc) {
							if (is_object($gc)) {
								if (!empty($gc->term_id)) {
									$g_cat_ids[] = intval($gc->term_id);
								}
							} else {
								$g_cat_ids[] = intval($gc);
							}
						}
						if (!empty($g_cat_ids)) {
							$g_cat_ids = array_values(array_unique($g_cat_ids));
						}
					}

					$has_intersection = false;
					if (!empty($category_ids)) {
						foreach ($category_ids as $cid) {
							if (in_array($cid, $g_cat_ids, true)) {
								$has_intersection = true;
								break;
							}
						}
					}

					if ($has_intersection) {
						$bucket_same_gender_same_cat[] = intval($gid);
					} else {
						$bucket_same_gender_other_cat[] = intval($gid);
					}
				}
			}
		}
	}

	// ===== ХЕЛПЕР ВЫБОРКИ: РАНДОМ ИЗ ГРУПП =====
	$fetch_random_from_groups = function($group_ids, $exclude_ids, $need) {
		$found = array();
		if (!empty($group_ids)) {
			$meta_query = array(
				array(
					'key'   => 'product_status',
					'value' => '1',
				),
				array(
					'key'     => 'product_group',
					'value'   => $group_ids,
					'compare' => 'IN',
				),
			);
			$q = new WP_Query(array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'post__not_in'        => $exclude_ids,
				'posts_per_page'      => $need,
				'orderby'             => 'rand', // рандом внутри корзины
				'meta_query'          => $meta_query,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			));
			if (!empty($q->posts)) {
				foreach ($q->posts as $p) {
					$found[] = $p;
				}
			}
		}
		return $found;
	};

	// общий список исключений
	$exclude_ids = array($current_id);
	if (!empty($manual_ids)) {
		$exclude_ids = array_merge($exclude_ids, $manual_ids);
	}
	if (!empty($result_posts)) {
		foreach ($result_posts as $rp) {
			$exclude_ids[] = intval($rp->ID);
		}
	}
	if (!empty($exclude_ids)) {
		$exclude_ids = array_values(array_unique($exclude_ids));
	}

	// ===== 2) ТОТ ЖЕ ГЕНДЕР + ТЕ ЖЕ КАТЕГОРИИ (рандом) =====
	$need = $limit - count($result_posts);
	if ($need > 0) {
		$got = $fetch_random_from_groups($bucket_same_gender_same_cat, $exclude_ids, $need);
		if (!empty($got)) {
			foreach ($got as $p) {
				$result_posts[] = $p;
				$exclude_ids[]  = intval($p->ID);
			}
			$exclude_ids = array_values(array_unique($exclude_ids));
			if (count($result_posts) >= $limit) {
				return $result_posts;
			}
		}
	}

	// ===== 3) ТОТ ЖЕ ГЕНДЕР + ДРУГИЕ КАТЕГОРИИ (рандом) =====
	$need = $limit - count($result_posts);
	if ($need > 0) {
		$got = $fetch_random_from_groups($bucket_same_gender_other_cat, $exclude_ids, $need);
		if (!empty($got)) {
			foreach ($got as $p) {
				$result_posts[] = $p;
				$exclude_ids[]  = intval($p->ID);
			}
			$exclude_ids = array_values(array_unique($exclude_ids));
			if (count($result_posts) >= $limit) {
				return $result_posts;
			}
		}
	}

	// ===== редкий резерв: любые "в наличии" (рандом), чтобы не показывать пусто =====
	$need = $limit - count($result_posts);
	if ($need > 0) {
		$q_any = new WP_Query(array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'post__not_in'        => $exclude_ids,
			'posts_per_page'      => $need,
			'orderby'             => 'rand',
			'meta_query'          => array(
				array(
					'key'   => 'product_status',
					'value' => '1',
				),
			),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		));
		if (!empty($q_any->posts)) {
			foreach ($q_any->posts as $p) {
				$result_posts[] = $p;
			}
		}
	}

	return $result_posts;
}
