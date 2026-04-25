<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseDir = dirname(__DIR__);
$coreDir = is_dir($baseDir . '/core') ? 'core' : (is_dir($baseDir . '/Core') ? 'Core' : null);

if (!$coreDir) {
    $foundNames = array_map('basename', array_filter(glob($baseDir . '/*'), 'is_dir'));
    die("Error: 'core' directory not found. Found: " . implode(', ', $foundNames));
}

// List all files in core for debugging
$filesInCore = array_map('basename', glob($baseDir . "/$coreDir/*"));
$findFile = function($name) use ($filesInCore) {
    foreach ($filesInCore as $f) if (strtolower($f) == strtolower($name)) return $f;
    return null;
};

$autoloader = $findFile('Autoloader.php');
$helpers = $findFile('Helpers.php');

if (!$autoloader || !$helpers) {
    die("Error: Missing files in $coreDir/. Found: " . implode(', ', $filesInCore));
}

require_once $baseDir . "/$coreDir/$autoloader";
require_once $baseDir . "/$coreDir/$helpers";

// Load environment variables
loadEnv(__DIR__ . '/../.env');

use Core\Router;

$router = new Router();

// Routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/table/{number}', 'OrderController@table');
$router->add('POST', '/api/order', 'OrderController@placeOrder');
$router->add('GET', '/chef', 'ChefController@index');
$router->add('POST', '/chef/update', 'ChefController@updateStatus');
$router->add('GET', '/waiter', 'WaiterController@index');
$router->add('POST', '/waiter/approve', 'WaiterController@approvePayment');
$router->add('POST', '/waiter/notif-read', 'WaiterController@markNotifRead');
$router->add('GET', '/admin', 'AdminController@index');
$router->add('GET', '/admin/items', 'AdminController@items');
$router->add('GET', '/admin/tables', 'AdminController@tables');
$router->add('GET', '/admin/expenses', 'AdminController@expenses');
$router->add('GET', '/admin/income', 'AdminController@income');
$router->add('GET', '/api/chef/data', 'ChefController@getData');
$router->add('GET', '/api/waiter/data', 'WaiterController@getData');
$router->add('POST', '/admin/items/save', 'AdminController@saveItem');
$router->add('POST', '/admin/items/delete', 'AdminController@deleteItem');
$router->add('POST', '/admin/tables/save', 'AdminController@saveTable');
$router->add('POST', '/admin/tables/delete', 'AdminController@deleteTable');
$router->add('POST', '/admin/expenses/save', 'AdminController@saveExpense');
$router->add('POST', '/admin/expenses/delete', 'AdminController@deleteExpense');

// Dispatch
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($url, $method);
