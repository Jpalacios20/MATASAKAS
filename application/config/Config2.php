<?php
// -- Zona Horaria
date_default_timezone_set('America/Lima');
// --
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = "https://";
} else {
    $protocol = "http://";
}
// --
$base_url =  $protocol . $_SERVER['HTTP_HOST'] .  '/sistema/SMART.HOTEL/'; //<--Direccion HTTP
// --
define('BASE_URL', $base_url);
define('DEFAULT_CONTROLLER', 'Login');
define('DEFAULT_LAYOUT', 'layout');
// --
$dbSettings = [];
foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $setting) {
    $value = getenv($setting);
    if ($value === false || ($setting !== 'DB_PASS' && $value === '')) {
        throw new RuntimeException('Required environment variable is not set: ' . $setting);
    }
    $dbSettings[$setting] = $value;
}

define('DB_HOST', $dbSettings['DB_HOST']);
define('DB_NAME', $dbSettings['DB_NAME']);
define('DB_USER', $dbSettings['DB_USER']);
define('DB_PASS', $dbSettings['DB_PASS']);
define('DB_PORT', (int) (getenv('DB_PORT') ?: 3306));