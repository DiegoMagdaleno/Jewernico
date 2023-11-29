<?php

require_once("vendor/autoload.php");

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

    $key = "ALGUNA_CLAVE_SECRETA"; // ESTA LLAVE ES SOLO PORQUE ESTE PROYECTO ES DE TESTING
    $now = strtotime("now");

    if ($user) {
        if (password_verify($data["password"], $user["Password"])) {
            $token = array(
                "data" => [
                    "id" => $user["Id"],
                    "nombre" => $user["Nombre"],
                    "apellidoMaterno" => $user["ApellidoMaterno"],
                    "apellidoPaterno" => $user["ApellidoPaterno"],
                    "correoElectronico" => $user["CorreoElectronico"],
                    "nivelPermisos" => $user["NivelPermisos"],
                ],
                "exp" => $now + 3600,
                "key" => $key
            );

            $jwt = JWT::encode($token, $key, "HS256");
            Flight::json(
                array(
                    "token" => $jwt
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