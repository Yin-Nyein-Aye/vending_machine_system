<?php

// HOME
$router->get('', 'HomeController@index');

// AUTH
$router->get('login', 'AuthController@showLogin');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@showRegister');
$router->post('register', 'AuthController@register');
$router->get('logout', 'AuthController@logout', [AuthMiddleware::class]);

// PRODUCTS
$router->get('products', 'ProductsController@index', [AuthMiddleware::class]);
$router->get('products/{slug}', 'ProductsController@show', [AuthMiddleware::class]);

// Purchase
$router->get('products/{slug}/purchase', 'ProductsController@purchaseForm', [AuthMiddleware::class]);
$router->post('products/{slug}/purchase', 'ProductsController@purchase', [AuthMiddleware::class]);

// ADMIN
$router->get('admin/panel', 'ProductsController@adminPanel', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);

// CRUD Admin
$router->get('admin/products/create', 'ProductsController@create', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);
$router->post('admin/products', 'ProductsController@store', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);
$router->get('admin/products/{slug}/edit', 'ProductsController@edit', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);
$router->post('admin/products/{slug}', 'ProductsController@update', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);
$router->post('admin/products/{slug}/delete', 'ProductsController@delete', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);