<?php

require_once 'vendor/autoload.php';
require_once 'tokens.php';

Flight::route("GET /categories", function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM categoria");
    $query->execute();
    $categories = $query->fetchAll();
    Flight::json($categories);
});

Flight::route("POST /categories", function () {
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");

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
    } catch (Exception $e) {
        Flight::json(
            array(
                "status" => 500,
                "message" => $e->getMessage()
            ),
            500
        );
    }
});