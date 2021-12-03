<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Работа с пользователями
$router->post('register', ['uses' => 'UsersController@register']);
$router->post('login', ['uses' => 'UsersController@authenticate']);

// Ресты для учета ценных бумаг
$router->get('stocks/{id:[0-9]+}', ['uses' => 'StocksController@getStock']);
$router->get('stocks', ['uses' => 'StocksController@filterStocks']);
$router->post('stocks', ['uses' => 'StocksController@addStock']);
$router->put('stocks/{id:[0-9]+}', ['uses' => 'StocksController@updateStock']);
$router->put('stocks/{id:[0-9]+}/refresh-price', ['uses' => 'StocksController@refreshPrice']);

// Ресты для поиска бумаг/валют по тикеру
$router->get('currency/{name:[A-Za-z\@0-9]+}/{driver:[A-Z]+}', ['uses' => 'StocksController@searchCurrency']);
$router->get('ticker/{ticker:[A-Za-z\@0-9]+}/{driver:[A-Z]+}', ['uses' => 'StocksController@searchStock']);

// Ресты для получения котировки и разницы со вчерашним закрытием
$router->get('ticker/{ticker:[A-Za-z\@0-9]+}/price', ['uses' => 'StocksController@getPriceByTicker']);
$router->get('ticker/{ticker:[A-Za-z\@0-9]+}/diff', ['uses' => 'StocksController@getDiffByTicker']);

//Тестовые ресты
$router->get('test/echo/{text:[A-Za-z0-9]+}', ['uses' => 'TestController@echo']);