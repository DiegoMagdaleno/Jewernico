<?php

require_once('vendor/autoload.php');
require_once('tokens.php');

Flight::route("POST /password/reset", function () {
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");
        $data = Flight::request()->data->getData();
        $password = $data["password"];
        $userId = $token->data->id;

        $db = Flight::db();
        $query = $db->prepare("UPDATE usuario SET Password = :password WHERE Id = :id");
        $query->execute(array(
            ":password" => password_hash($password, PASSWORD_DEFAULT),
            ":id" => $userId
        ));

        Flight::json(
            array(
                "status" => 200,
                "message" => "Contraseña actualizada"
            )
        );
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
