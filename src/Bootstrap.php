<?php

if (!defined("MY_CMS_PATH")) {
    die("NO SCRIPT");
}

use MyCMS\Application;

define('B_PATH_S', dirname(__FILE__) . '/..');
define('P_PATH', dirname(__FILE__) . '/');
define('P_PATH_S', dirname(__FILE__));
define('A_PATH', P_PATH_S . '/App');
define('CONFIG_PATH', A_PATH . '/Configuration');
define('I_PATH', A_PATH . '/Utils');
define('C_PATH', A_PATH . '/Content');
define('PLUGIN_PATH', C_PATH . '/Plugins');
define('MY_ADMIN_PATH', A_PATH . '/MyAdmin');
define('MY_ADMIN_TEMPLATE_PATH', '/src/App/MyAdmin');
define('MY_PLUGINS_PATH', '/src/App/Content/Plugins');

define('MY_CMS_WEBSITE', 'https://tuttarealstep.github.io/MyCMS');

session_start();

//Load configuration file
if (file_exists(CONFIG_PATH . '/my_config.php')) {
    require_once CONFIG_PATH . '/my_config.php';
}

require realpath(__DIR__) . '/../vendor/autoload.php';

$app = new Application();

require_once I_PATH . '/MyCMSForm.php';

$app->run();
