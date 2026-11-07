<?php

require 'vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Container;

// Simulasi HTTP request
$_SERVER['REQUEST_URI'] = '/api/hello';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Pengujian Router
$router = new Router();

$router->get('/api/hello', function() {
    echo "Router bekerja! Endpoint /api/hello terpanggil." . PHP_EOL;
});

$router->dispatch(new Request());

// Pengujian Container
$container = new Container();

$container->bind('contoh', function($container) {
    return 'Container bekerja!';
});

echo $container->make('contoh') . PHP_EOL;