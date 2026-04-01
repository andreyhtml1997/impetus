<?php
/**
 * Meta Pixel + Conversions API (без плагинов, без WooCommerce)
 * PHP 7.4-compatible
 *
 * ВАЖНО:
 * - _fbp/_fbc ставим в формате fb.1.<timestamp_ms>.<...> и фиксируем дубли по domain/host
 * - PageView: один event_id на страницу (browser+server) -> дедуп
 * - Остальные события: уникальный event_id на событие (browser+server) -> дедуп
 * - Сервер: универсальный AJAX endpoint
 * - Purchase: на /thanks/?order_id=&amount=&currency=
 */

/* =======================
 * 0) HELPERS
 * ======================= */

if (!function_exists('esf_meta_now_ms')) {
  function esf_meta_now_ms()
  {
    return (int) floor(microtime(true) * 1000);
  }
}

if (!function_exists('esf_meta_uuid')) {
  function esf_meta_uuid()
  {
    if (function_exists('wp_generate_uuid4')) {
      return wp_generate_uuid4();
    }

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }
}

if (!function_exists('esf_meta_page_eid')) {
  function esf_meta_page_eid()
  {
    if (!empty($GLOBALS['esf_meta_page_eid'])) {
      return (string) $GLOBALS['esf_meta_page_eid'];
    }
    $GLOBALS['esf_meta_page_eid'] = esf_meta_uuid();
    return (string) $GLOBALS['esf_meta_page_eid'];
  }
}

if (!function_exists('esf_meta_root_domain')) {
  function esf_meta_root_domain()
  {
    $host = parse_url(home_url('/'), PHP_URL_HOST);
    $host = (string) $host;

    if ($host) {
      if (strpos($host, 'www.') === 0) {
        $host = substr($host, 4);
      }
    }
    return $host;
  }
}

if (!function_exists('esf_meta_is_valid_fbp_fbc')) {
  function esf_meta_is_valid_fbp_fbc($cookie_value)
  {
    if (!$cookie_value) {
      return false;
    }

    $parts = explode('.', (string) $cookie_value);
    if (count($parts) < 4) {
      return false;
    }

    // ожидаем fb.1.<timestamp_ms>.<rest>
    if ((string) $parts[0] !== 'fb') {
      return false;
    }
    if ((string) $parts[1] !== '1') {
      return false;
    }

    $ms = preg_replace('/\D+/', '', (string) $parts[2]);
    if ($ms === '') {
      return false;
    }

    $ms_i = (int) $ms;
    if ($ms_i < 1000000000000) { // < 1e12 => это секунды/мусор
      return false;
    }

    // защита от будущего (кривые часы / мусор)
    $now = esf_meta_now_ms();
    $max_future = $now + (5 * 60 * 1000); // +5 минут
    if ($ms_i > $max_future) {
      return false;
    }

    return true;
  }
}

if (!function_exists('esf_meta_set_cookie_both')) {
  function esf_meta_set_cookie_both($name, $value, $expires, $secure, $samesite, $httponly)
  {
    $path = '/';
    $domain = esf_meta_root_domain();

    // 1) host-only cookie (без domain)
    if (PHP_VERSION_ID >= 70300) {
      setcookie($name, $value, [
        'expires' => $expires,
        'path' => $path,
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite,
      ]);
    } else {
      setcookie($name, $value, $expires, $path . '; samesite=' . $samesite, '', $secure, $httponly);
    }

    // 2) domain cookie (чтобы не было дублей между www/без www/поддоменами)
    if ($domain) {
      if (PHP_VERSION_ID >= 70300) {
        setcookie($name, $value, [
          'expires' => $expires,
          'path' => $path,
          'domain' => $domain,
          'secure' => $secure,
          'httponly' => $httponly,
          'samesite' => $samesite,
        ]);
      } else {
        setcookie($name, $value, $expires, $path . '; samesite=' . $samesite, $domain, $secure, $httponly);
      }
    }
  }
}

if (!function_exists('esf_meta_dedup_key')) {
  function esf_meta_dedup_key($event_name, $event_id)
  {
    return 'esf_meta_sent_' . md5((string) $event_name . '|' . (string) $event_id);
  }
}

/* =======================
 * 1) SERVER: _fbp/_fbc cookies
 * ======================= */

add_action('init', function () {


  $secure = is_ssl() ? true : false;
  $samesite = 'Lax';
  $httponly = false;

  $expires_meta = time() + (90 * DAY_IN_SECONDS);

  $ts_ms = esf_meta_now_ms();

  // _fbp
  $need_fbp = false;
  if (empty($_COOKIE['_fbp'])) {
    $need_fbp = true;
  } else {
    $fbp_raw = (string) $_COOKIE['_fbp'];
    if (!esf_meta_is_valid_fbp_fbc($fbp_raw)) {
      $need_fbp = true;
    }
  }

  if ($need_fbp) {
    $rand = mt_rand(1000000000, 2147483647);
    $fbp = 'fb.1.' . $ts_ms . '.' . $rand;
    esf_meta_set_cookie_both('_fbp', $fbp, $expires_meta, $secure, $samesite, $httponly);
    $_COOKIE['_fbp'] = $fbp;
  } else {
    // если есть дубли в браузере — принудительно унифицируем значением из $_COOKIE
    $fbp_keep = (string) $_COOKIE['_fbp'];
    esf_meta_set_cookie_both('_fbp', $fbp_keep, $expires_meta, $secure, $samesite, $httponly);
  }

  // _fbc из fbclid
  if (isset($_GET['fbclid'])) {
    $fbclid = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $_GET['fbclid']);

    if ($fbclid !== '') {
      $need_fbc = false;

      if (empty($_COOKIE['_fbc'])) {
        $need_fbc = true;
      } else {
        $fbc_raw = (string) $_COOKIE['_fbc'];
        if (!esf_meta_is_valid_fbp_fbc($fbc_raw)) {
          $need_fbc = true;
        }
      }

      if ($need_fbc) {
        $fbc = 'fb.1.' . $ts_ms . '.' . $fbclid;
        esf_meta_set_cookie_both('_fbc', $fbc, $expires_meta, $secure, $samesite, $httponly);
        $_COOKIE['_fbc'] = $fbc;
      } else {
        // унифицируем (на случай дублей)
        $fbc_keep = (string) $_COOKIE['_fbc'];
        esf_meta_set_cookie_both('_fbc', $fbc_keep, $expires_meta, $secure, $samesite, $httponly);
      }
    }
  }

}, 0);

/* =======================
 * 2) SERVER: send event to CAPI
 * ======================= */

if (!function_exists('esf_capi_send')) {
  function esf_capi_send($event_name, $params = [], $event_id = '')
  {

    if (!defined('META_PIXEL_ID')) {
      return false;
    }
    if (!defined('META_CAPI_TOKEN')) {
      return false;
    }

    $event_name = (string) $event_name;

    if (!$event_id) {
      $event_id = esf_meta_uuid();
    }
    $event_id = sanitize_text_field((string) $event_id);

    if (!$event_id) {
      return false;
    }

    // серверный дедуп от повторной отправки (на всякий)
    $dedup_key = esf_meta_dedup_key($event_name, $event_id);
    if (get_transient($dedup_key)) {
      return true;
    }

    $scheme = is_ssl() ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $url = $scheme . $host . $uri;

    $user_data = [];

    if (!empty($_SERVER['REMOTE_ADDR'])) {
      $user_data['client_ip_address'] = (string) $_SERVER['REMOTE_ADDR'];
    }
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
      $user_data['client_user_agent'] = (string) $_SERVER['HTTP_USER_AGENT'];
    }

    if (!empty($_COOKIE['_fbp'])) {
      $fbp_raw = sanitize_text_field((string) $_COOKIE['_fbp']);
      if (esf_meta_is_valid_fbp_fbc($fbp_raw)) {
        $user_data['fbp'] = $fbp_raw;
      }
    }

    if (!empty($_COOKIE['_fbc'])) {
      $fbc_raw = sanitize_text_field((string) $_COOKIE['_fbc']);
      if (esf_meta_is_valid_fbp_fbc($fbc_raw)) {
        $user_data['fbc'] = $fbc_raw;
      }
    }

    // extra user_data из params
    if (is_array($params)) {
      $extra_from_params = [];

      if (!empty($params['extra_user_data'])) {
        if (is_array($params['extra_user_data'])) {
          $extra_from_params = array_merge($extra_from_params, $params['extra_user_data']);
        }
        unset($params['extra_user_data']);
      }

      if (!empty($params['email'])) {
        $email_norm = mb_strtolower(trim((string) $params['email']), 'UTF-8');
        if ($email_norm !== '') {
          $extra_from_params['em'] = hash('sha256', $email_norm);
        }
      }

      if (!empty($params['phone'])) {
        $phone_norm = preg_replace('/\D+/', '', (string) $params['phone']);
        if ($phone_norm !== '') {
          $extra_from_params['ph'] = hash('sha256', $phone_norm);
        }
      }

      if (!empty($params['firstname'])) {
        $fn_norm = mb_strtolower(trim((string) $params['firstname']), 'UTF-8');
        if ($fn_norm !== '') {
          $extra_from_params['fn'] = hash('sha256', $fn_norm);
        }
      }

      if (!empty($params['lastname'])) {
        $ln_norm = mb_strtolower(trim((string) $params['lastname']), 'UTF-8');
        if ($ln_norm !== '') {
          $extra_from_params['ln'] = hash('sha256', $ln_norm);
        }
      }

      if (!empty($params['city'])) {
        $ct_norm = mb_strtolower(trim((string) $params['city']), 'UTF-8');
        if ($ct_norm !== '') {
          $extra_from_params['ct'] = hash('sha256', $ct_norm);
        }
      }

      if (!empty($extra_from_params)) {
        $user_data = array_merge($user_data, $extra_from_params);
      }
    }

    $allowed_root = [
      'event_name',
      'event_time',
      'action_source',
      'event_id',
      'event_source_url',
      'user_data',
      'data_processing_options',
      'data_processing_options_country',
      'data_processing_options_state',
      'external_id',
    ];

    $data = [
      'event_name' => $event_name,
      'event_time' => time(),
      'action_source' => 'website',
      'event_id' => $event_id,
      'event_source_url' => $url,
      'user_data' => array_filter($user_data),
    ];

    $custom = [];

    if (is_array($params)) {
      if (isset($params['custom_data'])) {
        if (is_array($params['custom_data'])) {
          $custom = $params['custom_data'];
        }
        unset($params['custom_data']);
      }

      foreach ($params as $k => $v) {
        if (in_array($k, $allowed_root, true)) {
          $data[$k] = $v;
        } else {
          $custom[$k] = $v;
        }
      }
    }

    if (!empty($custom)) {
      $data['custom_data'] = $custom;
    }

    $payload = ['data' => [$data]];
    if (defined('META_TEST_EVENT_CODE')) {
      if (META_TEST_EVENT_CODE) {
        $payload['test_event_code'] = META_TEST_EVENT_CODE;
      }
    }

    $endpoint = 'https://graph.facebook.com/v18.0/' . META_PIXEL_ID . '/events?access_token=' . rawurlencode(META_CAPI_TOKEN);

    $is_pageview = false;
    if ($event_name === 'PageView') {
      $is_pageview = true;
    }

    $timeout = 6;
    $blocking = true;

    // PageView делаем неблокирующим, чтобы не тормозить сайт
    if ($is_pageview) {
      $timeout = 1;
      $blocking = false;
    }

    $res = wp_remote_post($endpoint, [
      'headers' => ['Content-Type' => 'application/json'],
      'body' => wp_json_encode($payload),
      'timeout' => $timeout,
      'blocking' => $blocking,
      'sslverify' => true,
    ]);

    if (!is_wp_error($res)) {
      set_transient($dedup_key, 1, 10 * MINUTE_IN_SECONDS);
      return true;
    }

    return false;
  }
}

/* =======================
 * 3) BROWSER: Pixel + автотрекинг
 * ======================= */

add_action('wp_head', function () {
  if (!defined('META_PIXEL_ID')) {
    return;
  }

  $page_eid = esf_meta_page_eid();
  ?>
  <!-- Meta Pixel (custom) -->
  <script>
    (function () {

      var pageEventId = <?php echo json_encode($page_eid); ?>;

      function genEventId() {
        try {
          if (window.crypto) {
            if (window.crypto.randomUUID) {
              return window.crypto.randomUUID();
            }
          }
        } catch (e) { }
        return (Date.now().toString(16) + Math.random().toString(16).slice(2));
      }

      window.esfGenEventId = genEventId;

      !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
          n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = true;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = true;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
      }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

      fbq('init', '<?php echo esc_js(META_PIXEL_ID); ?>');

      try {
        fbq('track', 'PageView', {}, { eventID: pageEventId });
      } catch (e) { }

      // Универсальная отправка на сервер (CAPI)
      window.esfCapi = function (eventName, params, eid) {
        var useEid = eid;
        if (!useEid) {
          useEid = genEventId();
        }

        try {
          var body = new FormData();
          body.append('action', 'meta_capi_event');
          body.append('event_name', eventName);
          body.append('event_id', useEid);
          body.append('params', JSON.stringify(params || {}));

          fetch('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', {
            method: 'POST',
            body: body,
            credentials: 'same-origin'
          });
        } catch (e) { }

        return useEid;
      };

      // Авто-Lead на submit (уникальный event_id)
      document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!f) return;
        if (f.tagName !== 'FORM') return;
        if (f.closest('.no-track')) return;
        if (f.hasAttribute('data-no-track')) return;

        var params = {
          form: (f.getAttribute('id') || f.getAttribute('name') || 'form'),
          location: location.pathname
        };

        var emailInput = f.querySelector('input[type="email"], input[name*="email"], input[name*="mail"]');
        if (emailInput) {
          if (emailInput.value) {
            params.email = emailInput.value;
          }
        }

        var phoneInput = f.querySelector('input[type="tel"], input[name*="phone"], input[name*="tel"], input[name*="mob"]');
        if (phoneInput) {
          if (phoneInput.value) {
            params.phone = phoneInput.value;
          }
        }

        var firstInput = f.querySelector('input[name="firstname"], input[name*="first_name"], input[name*="first-name"]');
        if (firstInput) {
          if (firstInput.value) {
            params.firstname = firstInput.value;
          }
        }

        var lastInput = f.querySelector('input[name="lastname"], input[name*="last_name"], input[name*="last-name"]');
        if (lastInput) {
          if (lastInput.value) {
            params.lastname = lastInput.value;
          }
        }

        var cityInput = f.querySelector('input[name*="city"], input[name*="misto"], input[name*="gorod"], select[name*="city"], select[name*="misto"], select[name*="gorod"]');
        if (cityInput) {
          if (cityInput.value) {
            params.city = cityInput.value;
          }
        }

        var eid = genEventId();

        try { fbq('track', 'Lead', params, { eventID: eid }); } catch (e) { }
        if (window.esfCapi) {
          window.esfCapi('Lead', params, eid);
        }
      }, true);

      // Автотрекинг кликов (уникальный event_id)
      (function () {
        if (!window.fbq) return;

        var INCLUDE = 'a, button, [role="button"], input[type="submit"], .btn, .button';
        var EXCLUDE = '#wpadminbar, .no-track, [data-no-track]';
        var MSG = ['wa.me', 'web.whatsapp.com', 't.me', 'telegram.me', 'viber://', 'vk.me', 'fb.me/messages', 'facebook.com/messages'];

        function pick(el, href, text) {
          text = (text || '').toLowerCase();

          var forced = el.getAttribute ? el.getAttribute('data-fb-event') : '';
          if (forced) return forced;

          if (href) {
            var h = href.toLowerCase();

            if (h.indexOf('tel:') === 0) return 'Contact';
            if (h.indexOf('mailto:') === 0) return 'Contact';

            var i = 0;
            for (i = 0; i < MSG.length; i++) {
              if (h.indexOf(MSG[i]) !== -1) return 'Contact';
            }
          }

          if (/subscribe|підпис|подпис/i.test(text)) return 'Subscribe';
          if (/send|submit|заяв|відправ|отправ|register|реєстр|регистра/i.test(text)) return 'Lead';
          return 'ClickCTA';
        }

        function buildParams(el, href, text) {
          var p = {};
          try {
            var raw = el.getAttribute ? el.getAttribute('data-fb-params') : '';
            if (raw) p = JSON.parse(raw);
          } catch (e) { }

          p.text = p.text || (text || '').trim().slice(0, 120);
          p.location = p.location || location.pathname;
          if (href) p.href = p.href || href;

          if (href) {
            if (href.indexOf('tel:') === 0) {
              var rawPhone = href.replace(/^tel:/i, '');
              var digits = rawPhone.replace(/\D+/g, '');
              if (digits) p.phone = digits;
            }
          }

          return p;
        }

        document.addEventListener('click', function (e) {
          if (e.defaultPrevented) return;
          if (e.button !== 0) return;
          if (e.metaKey) return;
          if (e.ctrlKey) return;
          if (e.shiftKey) return;
          if (e.altKey) return;

          var el = e.target && e.target.closest ? e.target.closest(INCLUDE) : null;
          if (!el) return;

          if (el.closest) {
            if (el.closest(EXCLUDE)) return;
          }

          var href = el.getAttribute ? (el.getAttribute('href') || '') : '';
          var text = (el.innerText || el.value || (el.getAttribute ? (el.getAttribute('aria-label') || '') : '')).trim();

          var name = pick(el, href, text);
          var p = buildParams(el, href, text);
          var eid = genEventId();

          try {
            fbq(name === 'ClickCTA' ? 'trackCustom' : 'track', name, p, { eventID: eid });
          } catch (e) { }

          if (window.esfCapi) {
            window.esfCapi(name, p, eid);
          }
        }, true);
      })();

      // ECOM: AddToCart / InitiateCheckout (уникальный event_id)
      (function () {
        if (!window.fbq) return;

        var ADD = ['[data-add-to-cart]', '.add-to-cart', '.product__add', '.btn-add', '.to-cart', 'a[href*="add-to-cart"]', 'button[name*="add-to-cart"]'];
        var CHECK = ['[data-checkout]', '.btn-checkout', '.go-checkout', 'a[href*="checkout"]'];

        function fire(name, p) {
          var eid = genEventId();
          try { fbq('track', name, p || {}, { eventID: eid }); } catch (e) { }
          if (window.esfCapi) {
            window.esfCapi(name, p || {}, eid);
          }
        }

        document.addEventListener('click', function (e) {
          var el = e.target && e.target.closest ? e.target.closest(ADD.join(',')) : null;
          if (el) {
            fire('AddToCart', { location: location.pathname, text: (el.innerText || '').trim() });
            return;
          }

          var el2 = e.target && e.target.closest ? e.target.closest(CHECK.join(',')) : null;
          if (el2) {
            fire('InitiateCheckout', { location: location.pathname, text: (el2.innerText || '').trim() });
          }
        }, true);
      })();

    })();
  </script>

  <noscript><img height="1" width="1" style="display:none" alt=""
      src="https://www.facebook.com/tr?id=<?php echo esc_attr(META_PIXEL_ID); ?>&ev=PageView&noscript=1" /></noscript>
  <!-- /Meta Pixel -->
  <?php
}, 1);

/* =======================
 * 4) SERVER: PageView (CAPI) с тем же event_id что в browser PageView
 * ======================= */

add_action('wp', function () {
  if (!defined('META_PIXEL_ID')) {
    return;
  }
  if (!defined('META_CAPI_TOKEN')) {
    return;
  }

  if (is_admin()) {
    return;
  }
  if (function_exists('wp_doing_cron')) {
    if (wp_doing_cron()) {
      return;
    }
  }
  if (defined('REST_REQUEST')) {
    if (REST_REQUEST) {
      return;
    }
  }
  if (defined('DOING_AJAX')) {
    if (DOING_AJAX) {
      return;
    }
  }

  $method = strtoupper(isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : 'GET');
  if ($method !== 'GET') {
    if ($method !== 'HEAD') {
      return;
    }
  }

  $eid = esf_meta_page_eid();
  esf_capi_send('PageView', [], $eid);
}, 1);

/* =======================
 * 5) SERVER: AJAX endpoint for any events
 * ======================= */

add_action('wp_ajax_nopriv_meta_capi_event', 'esf_capi_event_ajax');
add_action('wp_ajax_meta_capi_event', 'esf_capi_event_ajax');

function esf_capi_event_ajax()
{
  if (!defined('META_PIXEL_ID')) {
    wp_die();
  }
  if (!defined('META_CAPI_TOKEN')) {
    wp_die();
  }

  $name = isset($_POST['event_name']) ? sanitize_text_field((string) $_POST['event_name']) : '';
  $eid = isset($_POST['event_id']) ? sanitize_text_field((string) $_POST['event_id']) : '';

  if (!$name) {
    wp_die();
  }
  if (!$eid) {
    wp_die();
  }

  $params = [];
  if (isset($_POST['params'])) {
    $decoded = json_decode(stripslashes((string) $_POST['params']), true);
    if (is_array($decoded)) {
      $params = $decoded;
    }
  }

  esf_capi_send($name, $params, $eid);
  wp_die();
}

/* =======================
 * 6) Purchase on /thanks/
 * ======================= */

add_action('wp', function () {
  if (!defined('META_PIXEL_ID')) {
    return;
  }
  if (!defined('META_CAPI_TOKEN')) {
    return;
  }

  $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
  if (strpos($uri, '/thanks/') === false) {
    return;
  }

  $value = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
  $currency = isset($_GET['currency']) ? strtoupper(sanitize_text_field((string) $_GET['currency'])) : 'UAH';
  $order_id = isset($_GET['order_id']) ? sanitize_text_field((string) $_GET['order_id']) : '';

  // extra user_data из options impetus_order_{order_id}
  $extra_user_data = [];
  $external_id_hashed = '';

  if ($order_id) {
    $stored = get_option('impetus_order_' . $order_id);

    if (is_array($stored)) {
      if (!empty($stored['data'])) {
        if (is_array($stored['data'])) {
          $order_data = $stored['data'];

          if (!empty($order_data['email'])) {
            $email_normalized = mb_strtolower(trim((string) $order_data['email']), 'UTF-8');
            if ($email_normalized !== '') {
              $extra_user_data['em'] = hash('sha256', $email_normalized);
            }
          }

          $phone_normalized = '';
          if (!empty($order_data['phone'])) {
            $phone_normalized = preg_replace('/\D+/', '', (string) $order_data['phone']);
            if ($phone_normalized !== '') {
              $extra_user_data['ph'] = hash('sha256', $phone_normalized);
            }
          }

          if (!empty($order_data['firstname'])) {
            $fn_normalized = mb_strtolower(trim((string) $order_data['firstname']), 'UTF-8');
            if ($fn_normalized !== '') {
              $extra_user_data['fn'] = hash('sha256', $fn_normalized);
            }
          }

          if (!empty($order_data['lastname'])) {
            $ln_normalized = mb_strtolower(trim((string) $order_data['lastname']), 'UTF-8');
            if ($ln_normalized !== '') {
              $extra_user_data['ln'] = hash('sha256', $ln_normalized);
            }
          }

          if (!empty($order_data['location'])) {
            $city_normalized = mb_strtolower(trim((string) $order_data['location']), 'UTF-8');
            if ($city_normalized !== '') {
              $extra_user_data['ct'] = hash('sha256', $city_normalized);
            }
          }

          $extra_user_data['country'] = hash('sha256', 'ua');

          $zip_raw = '';
          if (!empty($order_data['warehouse'])) {
            if (preg_match('/\b(\d{5})\b/u', (string) $order_data['warehouse'], $m)) {
              $zip_raw = (string) $m[1];
            }
          }
          if ($zip_raw !== '') {
            $extra_user_data['zp'] = hash('sha256', trim($zip_raw));
          }

          $external_parts = [];
          if (!empty($order_data['email'])) {
            $external_parts[] = mb_strtolower(trim((string) $order_data['email']), 'UTF-8');
          }
          if ($phone_normalized) {
            $external_parts[] = $phone_normalized;
          }
          if ($order_id) {
            $external_parts[] = (string) $order_id;
          }

          if (!empty($external_parts)) {
            $external_id_hashed = hash('sha256', implode('|', $external_parts));
          }
        }
      }
    }
  }

  $params = [
    'custom_data' => array_filter([
      'value' => $value,
      'currency' => $currency,
      'order_id' => $order_id,
    ]),
  ];

  if (!empty($extra_user_data)) {
    $params['extra_user_data'] = $extra_user_data;
  }

  if ($external_id_hashed !== '') {
    $params['external_id'] = $external_id_hashed;
  }

  $purchase_eid = esf_meta_uuid();

  // SERVER
  esf_capi_send('Purchase', $params, $purchase_eid);

  // BROWSER (dedup по eventID)
  add_action('wp_footer', function () use ($value, $currency, $order_id, $purchase_eid) { ?>
    <script>
      try {
        fbq('track', 'Purchase', {
          value: <?php echo json_encode($value); ?>,
          currency: <?php echo json_encode($currency); ?>,
            order_id: <?php echo json_encode($order_id); ?>
            }, { eventID: <?php echo json_encode($purchase_eid); ?> });
          } catch (e) { }
    </script>
  <?php });
});

/* =======================
 * 7) ViewContent (product + product_category)
 * ======================= */

add_action('wp_head', 'impetus_viewcontent_event', 2);

function impetus_viewcontent_event()
{
  if (!defined('META_PIXEL_ID')) {
    return;
  }

  // Product page
  if (is_singular('product')) {
    global $post;
    $product_id = $post ? (int) $post->ID : 0;
    if (!$product_id) {
      return;
    }

    $sku = get_field('product_article', $product_id);
    $content_id = $sku ? (string) $sku : (string) $product_id;

    $promo_price_raw = get_field('product_promo_price', $product_id);
    $price_raw = get_field('product_price', $product_id);

    $value_raw = $promo_price_raw ? $promo_price_raw : $price_raw;
    $value = 0;
    if ($value_raw) {
      $value = floatval(str_replace(',', '.', (string) $value_raw));
    }

    $currency = 'UAH';
    ?>
    <script>
      (function () {
        if (typeof fbq === 'undefined') return;

        var eid = (window.esfGenEventId ? window.esfGenEventId() : (Date.now().toString(16) + Math.random().toString(16).slice(2)));
        var params = {
          content_ids: [<?php echo json_encode($content_id); ?>],
          content_type: 'product',
          value: <?php echo json_encode($value); ?>,
            currency: <?php echo json_encode($currency); ?>
            };

      try { fbq('track', 'ViewContent', params, { eventID: eid }); } catch (e) { }

      if (window.esfCapi) {
        window.esfCapi('ViewContent', params, eid);
      }
          }) ();
    </script>
    <?php
    return;
  }

  // Category page
  if (!is_tax('product_category')) {
    return;
  }

  global $wp_query;
  $ids = [];

  if ($wp_query) {
    if (!empty($wp_query->posts)) {
      foreach ($wp_query->posts as $p) {
        $sku = get_field('product_article', $p->ID);
        $ids[] = $sku ? (string) $sku : (string) $p->ID;
      }
    }
  }

  if (empty($ids)) {
    return;
  }
  ?>
  <script>
    (function () {
      if (typeof fbq === 'undefined') return;

      var eid = (window.esfGenEventId ? window.esfGenEventId() : (Date.now().toString(16) + Math.random().toString(16).slice(2)));
      var params = {
        content_ids: <?php echo wp_json_encode($ids); ?>,
        content_type: 'product',
        value: 0.00,
        currency: 'UAH'
    };

    try { fbq('track', 'ViewContent', params, { eventID: eid }); } catch (e) { }

    if (window.esfCapi) {
      window.esfCapi('ViewContent', params, eid);
    }
      }) ();
  </script>
  <?php
}
