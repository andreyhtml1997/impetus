<?php

add_action('wp_ajax_impetus_es_bind_email', 'impetus_es_bind_email');
add_action('wp_ajax_nopriv_impetus_es_bind_email', 'impetus_es_bind_email');

function impetus_es_get_vid_from_cookie()
{
  if (empty($_COOKIE['impetus_vid'])) {
    return '';
  }

  return sanitize_text_field(wp_unslash($_COOKIE['impetus_vid']));
}

function impetus_es_set_email_for_vid($vid, $email)
{
  $key = 'impetus_es_email_' . md5($vid);

  set_transient($key, $email, 30 * DAY_IN_SECONDS);

  return true;
}

function impetus_es_bind_email()
{
  ajax_security(true);

  if (empty($_POST['email'])) {
    wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
  }

  $email = sanitize_text_field(wp_unslash($_POST['email']));
  if (!is_email($email)) {
    wp_send_json_error(_add_ajax_notice(array(front_notice_html('Електронна адреса невірна!'))));
  }

  $vid = '';
  if (!empty($_POST['vid'])) {
    $vid = sanitize_text_field(wp_unslash($_POST['vid']));
  }

  if (empty($vid)) {
    $vid = impetus_es_get_vid_from_cookie();
  }

  if (empty($vid)) {
    wp_send_json_error(_add_ajax_notice(array(front_notice_html('Виникла помилка!'))));
  }

  impetus_es_set_email_for_vid($vid, $email);

  wp_send_json_success(_add_ajax_notice(array(), 1));
}
