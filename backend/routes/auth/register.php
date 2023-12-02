<?php

require_once("vendor/autoload.php");
require_once("util.php");

use Firebase\JWT\JWT;

Flight::route("POST /register", function () {
    $data = Flight::request()->data->getData();
    $db = Flight::db();
    $permissionLevel = 0;

    $tokenCookie = Flight::request()->cookies->token;

    if (isset($data["nivelPermisos"])) {
        try {
            $token = validateToken($tokenCookie);
            if ($token != NULL && $token->data->nivelPermisos == 2) {
                $permissionLevel = $data["nivelPermisos"];
            }
        } catch (Exception $e) {
        }
    }


    $query = $db->prepare("SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico");
    $query->execute(
        array(
            ":correoElectronico" => $data["correoElectronico"]
        )
    );
    $existingUser = $query->fetch();

    if ($existingUser) {
        Flight::json(
            array(
                "status" => 409,
                "message" => "El usuario ya existe"
            ),
            409
        );
    } else {
        $query = $db->prepare("INSERT INTO usuario (Nombre, ApellidoMaterno, ApellidoPaterno, CorreoElectronico, Password, NivelPermisos) VALUES (:nombre, :apellidoMaterno, :apellidoPaterno, :correoElectronico, :password, :nivelPermisos)");
        $query->execute(
            array(
                ":nombre" => $data["nombre"],
                ":apellidoMaterno" => $data["apellidoMaterno"],
                ":apellidoPaterno" => $data["apellidoPaterno"],
                ":correoElectronico" => $data["correoElectronico"],
                ":password" => password_hash($data["password"], PASSWORD_DEFAULT),
                ":nivelPermisos" => $permissionLevel
            )
        );

        $userId = $db->lastInsertId();
        $key = "ALGUNA_CLAVE_SECRETA"; // ESTA LLAVE ES SOLO PORQUE ESTE PROYECTO ES DE TESTING
        $token = array(
            $data => [
                "id" => $userId,
                "nombre" => $data["nombre"],
                "apellidoMaterno" => $data["apellidoMaterno"],
                "apellidoPaterno" => $data["apellidoPaterno"],
                "correoElectronico" => $data["correoElectronico"],
                "nivelPermisos" => $permissionLevel,
            ],
            "exp" => strtotime("now") + 3600,
            "key" => $key
        );

        $refreshToken = array(
            $data => [
                "id" => $userId,
                "nivelPermisos" => $permissionLevel,
            ],
            "exp" => strtotime("now") + 604800,
            "key" => $key
        );

        $jwt = JWT::encode($token, $key, "HS256");
        $jwtRefresh = JWT::encode($refreshToken, $key, "HS256");

        Flight::json(
            array(
                "status" => 200,
                "message" => "Usuario creado",
                "token" => $jwt,
                "refreshToken" => $jwtRefresh,
            ),
            200
        );
        return;
    }
});


?>