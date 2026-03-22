<?php
/**
 * Application Bootstrap
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Directory separator
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

define('APP_PATH', ROOT_PATH . DS . 'app');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('CORE_PATH', APP_PATH . DS . 'Core');
define('MODELS_PATH', APP_PATH . DS . 'Models');
define('CONTROLLERS_PATH', APP_PATH . DS . 'Controllers');
define('VIEWS_PATH', ROOT_PATH . DS . 'views');
define('ROUTES_PATH', ROOT_PATH . DS . 'routes');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');

// Load configurations
require_once CONFIG_PATH . DS . 'app.php';
require_once CONFIG_PATH . DS . 'database.php';

// Load core helpers
require_once CORE_PATH . DS . 'helpers.php';
// Others are handled by the autoloader


// Composer Autoloader
if (file_exists(ROOT_PATH . DS . 'vendor' . DS . 'autoload.php')) {
    require_once ROOT_PATH . DS . 'vendor' . DS . 'autoload.php';
}

// Simple Autoloader for Core and Models
spl_autoload_register(function ($class) {
    $paths = [CORE_PATH, MODELS_PATH];
    foreach ($paths as $path) {
        $file = $path . DS . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


// Start Session
Session::start();
