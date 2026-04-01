<?php
// Поиск в админке (Список записей) по product_title ИЛИ по meta product_article
add_filter('posts_search', function ($search, $wp_query) {
    // Только админка
    if (!is_admin()) {
        return $search;
    }

    // Только главный запрос списка записей
    if (!$wp_query->is_main_query()) {
        return $search;
    }

    // Только для CPT 'product'
    $post_type = $wp_query->get('post_type');
    if ($post_type !== 'product') {
        return $search;
    }

    // Должна быть строка поиска
    $s = $wp_query->get('s');
    if ($s === '' || $s === null) {
        return $search;
    }

    global $wpdb;
    $like = '%' . $wpdb->esc_like($s) . '%';

    // Полностью переопределяем часть WHERE, чтобы было OR: title LIKE ... ИЛИ meta_value LIKE ...
    // EXISTS избавляет от JOIN и проблем с DISTINCT/дубликатами.
    $search = $wpdb->prepare(
        " AND ( {$wpdb->posts}.post_title LIKE %s
                OR EXISTS (
                    SELECT 1
                    FROM {$wpdb->postmeta} pm
                    WHERE pm.post_id = {$wpdb->posts}.ID
                      AND pm.meta_key = 'product_article'
                      AND pm.meta_value LIKE %s
                )
        )",
        $like,
        $like
    );

    return $search;
}, 10, 2);
