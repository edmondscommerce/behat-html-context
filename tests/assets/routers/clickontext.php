<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
ini_set('display_errors', 1);

$router = new EdmondsCommerce\MockServer\StaticRouter();
$router->addStaticRoute('/', __DIR__ . "/html/textSearch.html");
$router->addRoute('/test', 'Success');

$router->run()->send();