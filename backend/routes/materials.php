<?php

require_once 'vendor/autoload.php';

Flight::route("GET /materials", function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM material");
    $query->execute();
    $materials = $query->fetchAll();
    Flight::json($materials);
});