<?php

require_once 'vendor/autoload.php';
require_once 'tokens.php';

Flight::route("GET /users", function () {
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");
        $permissionLevel = $token->data->nivelPermisos;
        if ($permissionLevel != 2) {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "No autorizado"
                ),
                403,
            );
        } else {
            $db = Flight::db();
            $query = $db->prepare("SELECT * FROM usuario");
            $query->execute();
            $users = $query->fetchAll();
            Flight::json($users);
        }
    } catch (Exception $e) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "No autorizado"
            ),
            403
        );
    }
});

Flight::route("GET /users/@id", function ($id) {
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");
        if ($token->data->id != $id && $token->data->nivelPermisos != 2) {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "No autorizado"
                ),
                403,
            );
        } else {
            $db = Flight::db();
            $query = $db->prepare("SELECT * FROM usuario WHERE Id = :id");
            $query->execute(
                array(
                    ":id" => $id,
                )
            );
            Flight::json($query->fetchAll());
        }
    } catch (Exception $e) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "No autorizado"
            ),
            403
        );
    }
});
?>