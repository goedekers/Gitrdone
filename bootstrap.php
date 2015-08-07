<?php

chdir(__DIR__);

ini_set('soap.wsdl_cache_enabled', '0');

define('BASE_DIR', preg_replace('/\/$/', '', __DIR__));
define('APP_DIR', BASE_DIR . '/app');
define('DATA_DIR', BASE_DIR . '/data');
define('FRAMEWORK_DIR', APP_DIR . '/framework');
define('CONTROLLER_DIR', APP_DIR . '/controllers');

require_once('app/framework/app.php');
$app = new App('config.xml');