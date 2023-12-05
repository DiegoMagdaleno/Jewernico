<?php

require_once('vendor/autoload.php');

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
        // Check if the answer is equal to the data answer
        if ($respuesta[0]["Respuesta"] == $data["Respuesta"]) {
            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Respuesta correcta"
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