<?php
require_once __DIR__ . "/../app/config/config.php";
require_once __DIR__ . "/../app/config/Database.php";
require_once __DIR__ . "/../app/controllers/HomeController.php";
require_once __DIR__ . "/../app/controllers/AuthController.php";
require_once __DIR__ . "/../app/controllers/ProductsController.php";
require_once __DIR__ . "/../app/middleware/AuthMiddleware.php";
require_once __DIR__ . "/../app/middleware/AdminMiddleware.php";
require_once __DIR__ . "/../app/core/Router.php";

session_start();

$db = (new Database())->connect();

$home = new HomeController();
$auth = new AuthController($db);
$products = new ProductsController($db);

// Router
$router = new Router();
$router->registerController('HomeController', $home);
$router->registerController('AuthController', $auth);
$router->registerController('ProductsController', $products);

// Load routes
require_once __DIR__ . "/../routes/web.php";
require_once __DIR__ . "/../routes/api.php";

return $router;