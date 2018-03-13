<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
ini_set('display_errors', 1);

$router = new EdmondsCommerce\MockServer\StaticRouter();
$router->addRoute('/first-text', 'First Text');
$router->addRoute('/second-visible-text', 'Second Visible Text');
$router->addRoute('/test', 'Success');
$router->addRoute('/not-present', 'There is not text that I am looking for.');
$router->addStaticRoute('/', __DIR__ . "/html/textSearch.html");
$router->addStaticRoute('/invisible', __DIR__ . "/html/invisible.html");
$router->addStaticRoute('/scroll-test', __DIR__ . "/html/scroll-test.html");
$router->addStaticRoute('/table', __DIR__ . "/html/table.html");
$router->addStaticRoute('/iframe-test', __DIR__ . "/html/iframeTest.html");
$router->addStaticRoute('/iframe-test2', __DIR__ . "/html/iframeTest2.html");




$router->run()->send();