<?php

require_once("vendor/autoload.php");
require_once("tokens.php");

use Firebase\JWT\JWT;

Flight::route("POST /login", function () {
    $request = Flight::request();
    $data = $request->data->getData();
    $db = Flight::db();

    print_r($data);

    $query = $db->prepare("SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico");
    $query->execute(
        array(
            ":correoElectronico" => $data["correoElectronico"]
        )
    );


    $user = $query->fetch();

    $key = "ALGUNA_CLAVE_SECRETA";
    $now = strtotime("now");

    if ($user) {
        if (password_verify($data["password"], $user["Password"])) {
            $dataToken = array(
                "id" => $user["Id"],
                "nombre" => $user["Nombre"],
                "apellidoMaterno" => $user["ApellidoMaterno"],
                "apellidoPaterno" => $user["ApellidoPaterno"],
                "correoElectronico" => $user["CorreoElectronico"],
                "nivelPermisos" => $user["NivelPermisos"],
            );

            $dataRefreshToken = array(
                "id" => $user["Id"],
                "nivelPermisos" => $user["NivelPermisos"],
            );

            $jwt = generateToken($key, $dataToken, $now + 60 * 60 * 24);
            $jwtRefresh = generateToken($key, $dataRefreshToken, $now + 604800);

            Flight::json(
                array(
                    "token" => $jwt,
                    "refreshToken" => $jwtRefresh,
                )
            );
        } else {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "Contraseña incorrecta"
                ),
                403
            );
        }
    } else {
        Flight::json(
            array(
                "status" => 404,
                "message" => "Usuario no encontrado"
            ),
            404
        );
    }
});

?>