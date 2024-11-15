<?php
  require_once('../includes/app_header.inc.php');

  user::require_login();

  document::$template = settings::get('store_template_admin');

  breadcrumbs::reset();
  breadcrumbs::add(language::translate('title_dashboard', 'Dashboard'), WS_DIR_ADMIN);
  breadcrumbs::add(language::translate('title_about', 'About'));

// Build apps list menu
  $box_apps_menu = new ent_view();
  $box_apps_menu->snippets['apps'] = [];

  foreach (functions::admin_get_apps() as $app) {

    if (!empty(user::$data['apps']) && empty(user::$data['apps'][$app['code']]['status'])) continue;

    $box_apps_menu->snippets['apps'][$app['code']] = [
      'code' => $app['code'],
      'name' => $app['name'],
      'link' => document::link(WS_DIR_ADMIN, ['app' => $app['code'], 'doc' => $app['default']]),
      'theme' => [
        'icon' => !(empty($app['theme']['icon'])) ? $app['theme']['icon'] : 'fa-plus',
        'color' => !(empty($app['theme']['color'])) ? $app['theme']['color'] : '#97a3b5',
      ],
      'active' => (isset($_GET['app']) && $_GET['app'] == $app['code']) ? true : false,
      'menu' => [],
    ];

    if (!empty($app['menu'])) {
      foreach ($app['menu'] as $item) {

        if (!empty(user::$data['apps']) && (empty(user::$data['apps'][$app['code']]['status']) || !in_array($item['doc'], user::$data['apps'][$app['code']]['docs']))) continue;

        $params = !empty($item['params']) ? array_merge(['app' => $app['code'], 'doc' => $item['doc']], $item['params']) : ['app' => $app['code'], 'doc' => $item['doc']];

        if (isset($_GET['doc']) && $_GET['doc'] == $item['doc']) {
          $selected = true;
          if (!empty($item['params'])) {
            foreach ($item['params'] as $param => $value) {
              if (!isset($_GET[$param]) || $_GET[$param] != $value) {
                $selected = false;
                break;
              }
            }
          }
        } else {
          $selected = false;
        }

        $box_apps_menu->snippets['apps'][$app['code']]['menu'][] = [
          'title' => $item['title'],
          'doc' => $item['doc'],
          'link' => document::link(WS_DIR_ADMIN, ['app' => $app['code'], 'doc' => $item['doc']] + (!empty($item['params']) ? $item['params'] : [])),
          'active' => $selected ? true : false,
        ];
      }
    }
  }

  document::$snippets['box_apps_menu'] = $box_apps_menu->stitch('views/box_apps_menu');

// CPU Usage
  if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
    if (function_exists('sys_getloadavg')) {
      $cpu_usage = round(sys_getloadavg()[0], 2);
    }
  }

// Memory Usage
  if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {

    if (@is_readable('/proc/meminfo')) {
      $fh = fopen('/proc/meminfo','r');

      while ($line = fgets($fh)) {
        $pieces = array();
        if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
          $ram_usage = $pieces[1];
          continue;
        }
        if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
          $ram_free = $pieces[1];
          continue;
        }
      }

      fclose($fh);

      $ram_usage = round($ram_usage / ($ram_usage + $ram_free) * 100, 2);
    }
  }

// Server Uptime
  if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
    if (@is_readable('/proc/uptime')) {
      $raw_uptime = round((float)file_get_contents('/proc/uptime'));
      $seconds = fmod($raw_uptime, 60);  $raw_uptime = intdiv($raw_uptime, 60);
      $minutes = $raw_uptime % 60;  $raw_uptime = intdiv($raw_uptime, 60);
      $hours = $raw_uptime % 24;  $raw_uptime = intdiv($raw_uptime, 24);
      $days = $raw_uptime;

      if ($days) {
        $uptime = $days .' day(s)';
      } else if ($hours) {
        $uptime = $hours .' hour(s)';
      } else if ($minutes) {
        $iptime = $minutes .' minute(s)';
      } else if ($seconds) {
        $uptime = $seconds .' second(s)';
      }
    }
  }

  $_page = new ent_view();
  $_page->snippets = [
    'machine' => [
      'name' => php_uname('n'),
      'architecture' => php_uname('m'),
      'os' => [
        'name' => php_uname('s') .' '. php_uname('r'),
        'version' => php_uname('v'),
      ],
      'ip_address' => $_SERVER['SERVER_ADDR'],
      'hostname' => gethostbyaddr($_SERVER['SERVER_ADDR']),
      'cpu_usage' => !empty($cpu_usage) ? $cpu_usage : '',
      'memory_usage' => !empty($memory_usage) ? $memory_usage : '',
      'uptime' =>  !empty($uptime) ? $uptime : '',
    ],
    'web_server' => [
      'name' => !empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '',
      'sapi' => php_sapi_name(),
      'current_user' => get_current_user(),
      'loaded_modules' => function_exists('apache_get_modules') ? apache_get_modules() : [],
    ],
    'php' => [
      'version' => PHP_VERSION .' ('. ((PHP_INT_SIZE === 8) ? '64-bit' : '32-bit') .')',
      'whoami' => (function_exists('exec') && !in_array('exec', preg_split('#\s*,\s*#', ini_get('disabled_functions')))) ? exec('whoami') : '',
      'loaded_extensions' => (function_exists('get_loaded_extensions') && !in_array('get_loaded_extensions', preg_split('#\s*,\s*#', ini_get('disabled_functions')))) ? get_loaded_extensions() : [],
      'disabled_functions' => ini_get('disabled_functions') ?  preg_split('#\s*,\s*#', ini_get('disabled_functions')) : [],
      'memory_limit' => ini_get('memory_limit'),
    ],
    'database' => [
      'name' => database::server_info(),
      'library' => mysqli_get_client_info(),
      'hostname' => DB_SERVER,
      'user' => DB_USERNAME,
      'database' => DB_DATABASE,
    ],
  ];

  echo $_page->stitch(FS_DIR_TEMPLATE . 'pages/about.inc.php');

  require_once vmod::check(FS_DIR_APP . 'includes/app_footer.inc.php');
