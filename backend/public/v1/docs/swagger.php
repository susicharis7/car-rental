<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);

require __DIR__ . '/../../../vendor/autoload.php';

if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1'){
   define('BASE_URL', 'http://localhost/car-rental/backend');
} else {
   define('BASE_URL', 'https://add-production-server-after-deployment/backend/');
}

// looks for swagger annotations
$openapi = \OpenApi\Generator::scan([
   __DIR__ . '/doc_setup.php',
   __DIR__ . '/../../../rest/routes'
]);

// sets `header` for browser to know that this is JSON
header('Content-Type: application/json');
echo $openapi->toJson();



?>