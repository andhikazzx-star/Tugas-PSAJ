<?php
/**
 * Public Entry Point
 */

require_once dirname(__DIR__) . '/bootstrap/app.php';


// Fetch route info
$page = get_param('page', 'landing');
$action = get_param('action', 'index');

// Initialize and dispatch
$routes = require ROUTES_PATH . '/web.php';
$router = new Router($routes);
$router->dispatch($page, $action);
