<?php

session_start();
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$databaseUrl = "mysql:host=" . $_ENV["DATABASE_HOST"] . ";dbname=" . $_ENV["DATABASE_NAME"];

Flight::register(
    "db",
    "PDO",
    array($databaseUrl, $_ENV["DATABASE_USER"], $_ENV["DATABASE_PASSWORD"]),
    function($db){
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
);


require 'routes/auth/login.php';
require 'routes/auth/register.php';
require 'routes/users.php';
require 'routes/products.php';
require 'routes/categories.php';
require 'routes/materials.php';
require 'routes/auth/refresh.php';
require 'routes/captcha.php';
require 'routes/question.php';

Flight::start();