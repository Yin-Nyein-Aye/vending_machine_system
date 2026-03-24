<?php
// Auth
$router->post('api/login', 'AuthController@apiLogin');
$router->post('api/register', 'AuthController@apiRegister');

// Products (all protected by JWT)
$router->get('api/products', 'ProductsController@index', [JwtMiddleware::class]);
$router->get('api/products/{id}', 'ProductsController@show', [JwtMiddleware::class]);
$router->post('api/products', 'ProductsController@store', [JwtMiddleware::class]);
$router->put('api/products/{slug}', 'ProductsController@update', [JwtMiddleware::class]);
$router->delete('api/products/{slug}', 'ProductsController@delete', [JwtMiddleware::class]);
$router->post('api/products/{slug}/purchase', 'ProductsController@purchase', [
    JwtMiddleware::class
]);