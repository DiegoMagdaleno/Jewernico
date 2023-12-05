<?php

require_once('vendor/autoload.php');

Flight::route('GET /questions', function(){
    $db = Flight::db();
    $query = $db->query('SELECT * FROM pregunta');
    $questions = $query->fetchAll();
    Flight::json($questions);
});