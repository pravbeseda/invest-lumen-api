<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('ticker/{ticker:[A-Z]+}', ['uses' => 'TinkoffController@getInfoByTicker']);
$router->get('figi/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getInfoByFigi']);
$router->get('orderbook/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getOrderBook']);
$router->get('price/{figi:[A-Z0-9]+}', ['uses' => 'TinkoffController@getLastPrice']);
