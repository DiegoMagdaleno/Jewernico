<?php

require_once('vendor/autoload.php');
require_once('tokens.php');

Flight::route('GET /questions', function () {
    $db = Flight::db();
    $query = $db->query('SELECT * FROM pregunta');
    $questions = $query->fetchAll();
    Flight::json($questions);
});

Flight::route("POST /questions/answers", function () {
    $db = Flight::db();
    $data = $db->request()->data->getData();

    $usuario = $data["IdUsuario"];
    $pregunta = $data["IdPregunta"];
    $respuesta = $data["Respuesta"];

    $query = $db->prepare("INSERT INTO responder (IdUsuario, IdPregunta, Respuesta) VALUES (:usuario, :pregunta, :respuesta)");
    $query->execute(
        array(
            ":usuario" => $usuario,
            ":pregunta" => $pregunta,
            ":respuesta" => $respuesta
        )
    );

    Flight::json(
        array(
            "status" => 200,
            "message" => "Respuesta enviada"
        )
    );
});

Flight::route("POST /questions/email", function () {
    $db = Flight::db();
    $data = $db->request()->data->getData();

    $email = $data["correoElectronico"];

    $query = $db->prepare(
        "SELECT * FROM Pregunta WHERE Id = (
            SELECT IdPregunta FROM responder WHERE IdUsuario = (
                SELECT Id FROM usuario WHERE CorreoElectronico = :email
            )
        )"
    );

    $query->execute(
        array(
            ":email" => $email
        )
    );

    $result = $query->fetch();

    Flight::json($result);
});

Flight::route("GET /question/validate", function () {
    $db = Flight::db();
    $data = Flight::request()->data->getData();

    $usuario = $data["IdUsuario"];
    $pregunta = $data["IdPregunta"];

    $query = $db->prepare("SELECT * FROM responder WHERE IdUsuario = :usuario AND IdPregunta = :pregunta");
    $db->execute(
        array(
            ":usuario" => $usuario,
            ":pregunta" => $pregunta
        )
    );

    $respuesta = $query->fetchAll();
    if (count($respuesta) > 0) {

        if ($respuesta[0]["Respuesta"] == $data["Respuesta"]) {
            $userQuery = $db->prepare("SELECT * FROM usuario WHERE Id = :usuario");
            $userQuery->execute(
                array(
                    ":usuario" => $usuario
                )
            );
            $usuario = $userQuery->fetch();
            $dataToken = array(
                "id" => $usuario["Id"],
                "nombre" => $usuario["Nombre"],
                "apellidoMaterno" => $usuario["ApellidoMaterno"],
                "apellidoPaterno" => $usuario["ApellidoPaterno"],
                "nivelPermisos" => $usuario["NivelPermisos"],
            );

            $refreshToken = array(
                "id" => $usuario["Id"],
                "nivelPermisos" => $usuario["NivelPermisos"],
            );

            $now = time();

            $jwt = generateToken("ALGUNA_CLAVE_SECRETA", $dataToken, $now + 60 * 60 * 24);
            $jwtRefresh = generateToken("ALGUNA_CLAVE_SECRETA", $refreshToken, $now + 604800);

            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Respuesta correcta",
                    "token" => $jwt,
                    "RefreshToken" => $jwtRefresh
                )
            );
        } else {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "Respuesta incorrecta"
                )
            );
        }
    }
});