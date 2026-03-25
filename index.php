<?php
echo "Hello from Railway!";
$router = require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/vendor/autoload.php'; 

$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
