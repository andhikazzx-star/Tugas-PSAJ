<?php
/**
 * e-Rapor Sisipan – Entry Point
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// Paths
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . DS . 'app');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('CORE_PATH', APP_PATH . DS . 'Core');
define('MODELS_PATH', APP_PATH . DS . 'Models');
define('CONTROLLERS_PATH', APP_PATH . DS . 'Controllers');

// Load configurations
require_once CONFIG_PATH . DS . 'app.php';
require_once CONFIG_PATH . DS . 'database.php';

// Load core helpers
require_once CORE_PATH . DS . 'helpers.php';
require_once CORE_PATH . DS . 'Session.php';
require_once CORE_PATH . DS . 'Middleware.php';

// Composer Autoloader
if (file_exists(ROOT_PATH . DS . 'vendor' . DS . 'autoload.php')) {
    require_once ROOT_PATH . DS . 'vendor' . DS . 'autoload.php';
}

// Simple Autoloader for Models
spl_autoload_register(function ($class) {
    $file = MODELS_PATH . DS . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Start Session
Session::start();

// Routing logic
require_once ROOT_PATH . DS . 'routes.php';
