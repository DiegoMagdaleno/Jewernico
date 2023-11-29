<?php

require_once 'vendor/autoload.php';

Flight::register(
    "db",
    "PDO",
    array("mysql:host=localhost;dbname=jewernico_gina", "root", ""),
    function($db){
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
);


require 'routes/auth/login.php';
require 'routes/auth/register.php';

Flight::start();