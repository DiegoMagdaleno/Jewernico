<?php

require_once 'vendor/autoload.php';
require_once 'util.php';

Flight::route("GET /categories", function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM categoria");
    $query->execute();
    $categories = $query->fetchAll();
    Flight::json($categories);
});

Flight::route("POST /categories", function () {
    $token = validateToken();

    if (!$token) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "No autorizado"
            ),
            403
        );
    } else {
        if ($token->data->nivelPermisos < 1) {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "No autorizado"
                ),
                403
            );
        } else {
            $data = Flight::request()->data->getData();
            $db = Flight::db();
            $query = $db->prepare("INSERT INTO categoria (Nombre) VALUES (:nombre)");
            $query->execute(
                array(
                    ":nombre" => $data["nombre"]
                )
            );
            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Categoría agregada"
                ),
                200
            );
        }
    }
});