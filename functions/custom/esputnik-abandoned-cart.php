<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Abandoned cart → eSputnik
 * Логика:
 * - при любом обновлении корзины сохраняем snapshot в transient по visitor-id
 * - при активности/обновлении корзины пере-планируем отправку через 1 час
 * - когда наступает время → собираем до 6 items и шлём event abandoned_cart
 */

add_action('impetus_es_send_abandoned_cart', 'impetus_es_send_abandoned_cart', 10, 1);

add_action('wp_ajax_front_es_touch', 'front_es_touch');
add_action('wp_ajax_nopriv_front_es_touch', 'front_es_touch');

add_action('wp_ajax_front_es_set_email', 'front_es_set_email');
add_action('wp_ajax_nopriv_front_es_set_email', 'front_es_set_email');

function impetus_es_ac_get_vid()
{
  $vid = '';

  if (function_exists('impetus_es_get_vid_from_cookie')) {
    $vid = impetus_es_get_vid_from_cookie();
  }

  if (!$vid && !empty($_COOKIE['es_vid'])) {
    $vid = sanitize_text_field(wp_unslash($_COOKIE['es_vid']));
  }

  if (!$vid) {
    $vid = wp_generate_uuid4();
    update_cookie('es_vid', $vid, 365);
  }

  return $vid;
}

function impetus_es_ac_get_email()
{
  $email = '';

  if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (!empty($user->user_email) && is_email($user->user_email)) {
      $email = $user->user_email;
    }
  }

  if (!$email && !empty($_COOKIE['es_email'])) {
    $_email = sanitize_text_field(wp_unslash($_COOKIE['es_email']));
    if (is_email($_email)) {
      $email = $_email;
    }
  }

  return $email;
}

function impetus_es_ac_key($vid)
{
  return 'impetus_es_ac_' . md5((string) $vid);
}

function impetus_es_ac_get($vid)
{
  return get_transient(impetus_es_ac_key($vid));
}

function impetus_es_ac_set($vid, $data)
{
  // держим неделю, чтобы крон успел отработать даже при редком трафике
  set_transient(impetus_es_ac_key($vid), $data, 7 * DAY_IN_SECONDS);
}

function impetus_es_ac_delete($vid)
{
  delete_transient(impetus_es_ac_key($vid));
  wp_clear_scheduled_hook('impetus_es_send_abandoned_cart', array($vid));
}

function impetus_es_ac_reschedule($vid, $ts)
{
  wp_clear_scheduled_hook('impetus_es_send_abandoned_cart', array($vid));
  wp_schedule_single_event($ts, 'impetus_es_send_abandoned_cart', array($vid));
}

function impetus_es_ac_on_cart_update($cart)
{
  $vid = impetus_es_ac_get_vid();

  if (empty($cart)) {
    impetus_es_ac_delete($vid);
    return;
  }

  $record = impetus_es_ac_get($vid);
  if (!is_array($record)) {
    $record = array();
  }

  $record['vid'] = $vid;
  $record['cart'] = $cart;
  $record['updated'] = time();
  $record['last_activity'] = time();
  $record['sent'] = !empty($record['sent']) ? 1 : 0;
  $record['tries'] = isset($record['tries']) ? absint($record['tries']) : 0;

  $email = impetus_es_ac_get_email();
  if ($email) {
    $record['email'] = $email;
  }

  // Планируем только если email уже известен
  if (!empty($record['email']) && is_email($record['email']) && empty($record['sent'])) {
    $record['scheduled'] = time() + (defined('IMPETUS_ES_AC_DELAY') ? IMPETUS_ES_AC_DELAY : HOUR_IN_SECONDS);
    impetus_es_ac_reschedule($vid, $record['scheduled']);
  }

  impetus_es_ac_set($vid, $record);
}

function impetus_es_ac_touch()
{
  $vid = impetus_es_ac_get_vid();
  $record = impetus_es_ac_get($vid);

  if (!is_array($record) || empty($record['cart'])) {
    return;
  }

  if (!empty($record['sent'])) {
    return;
  }

  $record['last_activity'] = time();

  $email = impetus_es_ac_get_email();
  if ($email) {
    $record['email'] = $email;
  }

  if (!empty($record['email']) && is_email($record['email'])) {
    $record['scheduled'] = time() + (defined('IMPETUS_ES_AC_DELAY') ? IMPETUS_ES_AC_DELAY : HOUR_IN_SECONDS);
    impetus_es_ac_reschedule($vid, $record['scheduled']);
  }

  impetus_es_ac_set($vid, $record);
}

function front_es_touch()
{
  ajax_security(true);

  impetus_es_ac_touch();

  wp_send_json_success(_add_ajax_notice(array(), 1));
}

function impetus_es_subscribe_contact($email)
{
  if (!defined('IMPETUS_ES_API_KEY') || !IMPETUS_ES_API_KEY)
    return;
  if (!defined('IMPETUS_ES_API_USER') || !IMPETUS_ES_API_USER)
    return;

  $payload = array(
    'contact' => array(
      'channels' => array(
        array(
          'type' => 'email',
          'value' => $email,
        ),
      ),
      // опционально: firstName/lastName/fields/address
    ),
    // опционально, но полезно:
    'groups' => array('Subscribers'),
    'formType' => 'site', // будет событие subscribeFromApi-site / subscribeUpdateFromApi-site
  );

  $headers = array(
    'Content-Type' => 'application/json; charset=utf-8',
    'Accept' => 'application/json',
    'Authorization' => 'Basic ' . base64_encode(IMPETUS_ES_API_USER . ':' . IMPETUS_ES_API_KEY),
  );

  $response = wp_remote_post('https://esputnik.com/api/v1/contact/subscribe', array(
    'timeout' => 15,
    'headers' => $headers,
    'body' => wp_json_encode($payload),
  ));

  // по желанию: залогировать код/ошибку
  // ✅ ЛОГИРУЕМ РЕЗУЛЬТАТ (чтобы проверить без доступа в eSputnik)
  $code = 0;
  $body = '';

  if (is_wp_error($response)) {
    $body = $response->get_error_message();
  } else {
    $code = (int) wp_remote_retrieve_response_code($response);
    $body = (string) wp_remote_retrieve_body($response);
  }

  impetus_es_ac_debug_add(array(
    'type' => 'subscribe_contact',
    'email' => $email,
    'code' => $code,
    'body' => is_string($body) ? mb_substr($body, 0, 2000) : '',
  ));
}

function front_es_set_email()
{
  ajax_security(true);

  $email = isset($_POST['email']) ? sanitize_text_field(wp_unslash($_POST['email'])) : '';
  if (!$email || !is_email($email)) {
    wp_send_json_success(_add_ajax_notice(array(), 1));
  }

  update_cookie('es_email', $email, 180);

  // ✅ ДОБАВЛЯЕМ КОНТАКТ В eSputnik
  impetus_es_subscribe_contact($email);

  // если корзина есть — обновим snapshot и запланируем abandoned_cart
  $cart = get_cart();
  impetus_es_ac_on_cart_update($cart);

  wp_send_json_success(_add_ajax_notice(array(), 1));
}


function impetus_es_ac_build_items($cart)
{
  if (empty($cart) || !is_array($cart)) {
    return array();
  }

  $product_ids = array_keys($cart);
  if (empty($product_ids)) {
    return array();
  }

  $product_objs = get_products(array(
    'product_ids' => $product_ids,
    'post_status' => 'publish'
  ));
  if (empty($product_objs)) {
    return array();
  }

  $items = array();
  $limit = 6;

  foreach ($cart as $product_id => $sizes) {
    if (empty($sizes) || !is_array($sizes)) {
      continue;
    }

    $_product_objs = wp_list_filter($product_objs, array('ID' => (int) $product_id));
    $product_obj = array_shift($_product_objs);

    if (empty($product_obj) || empty($product_obj->product_status) || empty($product_obj->product_final_price)) {
      continue;
    }

    foreach ($sizes as $size_id => $quantity) {
      $quantity = absint($quantity);
      if ($quantity <= 0) {
        continue;
      }

      if (!empty($product_obj->product_size)) {
        if (!in_array((int) $size_id, (array) $product_obj->product_size)) {
          continue;
        }
      }

      $size_obj = get_term((int) $size_id);
      $size_name = '';
      if ($size_obj && !is_wp_error($size_obj)) {
        $size_name = $size_obj->name;
      }

      $image_url = get_the_post_thumbnail_url((int) $product_id, 'full');
      if (!$image_url && !empty($product_obj->product_gallery[0])) {
        $image_url = wp_get_attachment_image_url((int) $product_obj->product_gallery[0], 'full');
      }

      $old_price = is_promo_price($product_obj) ? (float) $product_obj->product_price : 0;

      $items[] = array(
        'name' => (string) $product_obj->post_title,
        'price' => (string) number_format((float) $product_obj->product_final_price, 2, '.', ''),
        'size' => (string) $size_name,
        'quantity' => (string) $quantity,
        'url' => (string) get_permalink((int) $product_id),
        'imageurl' => (string) ($image_url ? $image_url : ''),
        'oldprice' => (string) number_format((float) $old_price, 2, '.', ''),
      );

      if (count($items) >= $limit) {
        break 2;
      }
    }
  }

  return $items;
}

function impetus_es_send_abandoned_cart($vid)
{
  $vid = (string) $vid;
  if (!$vid) {
    return;
  }

  $record = impetus_es_ac_get($vid);
  if (!is_array($record) || empty($record['cart'])) {
    return;
  }

  if (!empty($record['sent'])) {
    return;
  }

  // если в процессе пользователь стал активным — событие могло быть перенесено
  if (!empty($record['scheduled']) && time() + 30 < (int) $record['scheduled']) {
    return;
  }

  $email = !empty($record['email']) ? $record['email'] : '';
  if (!$email || !is_email($email)) {
    return;
  }

  if (!defined('IMPETUS_ES_API_KEY') || !IMPETUS_ES_API_KEY) {
    return;
  }

  $event_key = defined('IMPETUS_ES_EVENT_ABANDONED_CART') && IMPETUS_ES_EVENT_ABANDONED_CART ? IMPETUS_ES_EVENT_ABANDONED_CART : 'abandoned_cart';

  $items = impetus_es_ac_build_items($record['cart']);
  if (empty($items)) {
    return;
  }

  $payload = array(
    'eventTypeKey' => $event_key,
    'keyValue' => $email,
    'params' => array(
      array(
        'name' => 'email',
        'value' => $email
      ),
      array(
        'name' => 'json',
        'value' => $items
      ),
    ),
  );

  // eSputnik чаще всего принимает Basic: base64(api_key:)
  $headers = array(
    'Content-Type' => 'application/json; charset=utf-8',
    'Accept' => 'application/json',
    'Authorization' => 'Basic ' . base64_encode(IMPETUS_ES_API_USER . ':' . IMPETUS_ES_API_KEY),
  );

  $code = 0;
  $body = '';

  $response = wp_remote_post(
    'https://esputnik.com/api/v2/event',
    array(
      'timeout' => 15,
      'headers' => $headers,
      'body' => wp_json_encode($payload),
    )
  );

  if (is_wp_error($response)) {
    $body = $response->get_error_message();
    $code = 0;
  } else {
    $code = (int) wp_remote_retrieve_response_code($response);
    $body = (string) wp_remote_retrieve_body($response);
  }

  impetus_es_ac_debug_add(array(
    'vid' => $vid,
    'email' => $email,
    'code' => $code,
    'body' => is_string($body) ? mb_substr($body, 0, 2000) : '',
    'items_count' => count($items),
    // если хочешь видеть товары — раскомментируй:
    // 'items' => $items,
  ));

  if ($code >= 200 && $code < 300) {
    $record['sent'] = 1;
    $record['sent_at'] = time();
    impetus_es_ac_set($vid, $record);
    return;
  }


  // Retry (1 раз) через 15 минут
  $tries = isset($record['tries']) ? absint($record['tries']) : 0;
  $tries++;

  $record['tries'] = $tries;
  impetus_es_ac_set($vid, $record);

  if ($tries < 2) {
    wp_schedule_single_event(time() + 15 * MINUTE_IN_SECONDS, 'impetus_es_send_abandoned_cart', array($vid));
  }
}




function impetus_es_ac_debug_add($row)
{
  $log = get_option('impetus_es_ac_debug', array());
  if (!is_array($log)) {
    $log = array();
  }

  $row['ts'] = time();

  $log[] = $row;

  // держим последние 30 записей
  if (count($log) > 30) {
    $log = array_slice($log, -30);
  }

  update_option('impetus_es_ac_debug', $log, false);

  // дублируем в debug.log (если включен)
  if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    error_log('[ES abandoned_cart] ' . wp_json_encode($row));
  }
}

function impetus_es_ac_debug_last()
{
  $log = get_option('impetus_es_ac_debug', array());
  if (!is_array($log) || empty($log)) {
    return null;
  }
  return end($log);
}
