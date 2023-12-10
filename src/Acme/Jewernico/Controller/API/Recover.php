<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Recover
{
    public static function verify_email()
    {
        $data = Flight::request()->data->getData();
        $email = $data["correoElectronico"];
        $user = \Acme\Jewernico\Command\Database::getUser($email);
        if (empty($user)) {
            Flight::json(["error" => "El correo electrónico no está registrado"], 404);
            return;
        }

        Flight::json(["email" => $user["CorreoElectronico"]], 200);
    }

    public static function get_question() {
        $data = Flight::request()->data->getData();
        $email = $data["correoElectronico"];
        $question = \Acme\Jewernico\Command\Database::getSecurityQuestionOf($email);
        Flight::json(["question" => $question["Pregunta"]], 200);
    }

    public static function verify_question()
    {
        $data = Flight::request()->data->getData();
        $email = $data["correoElectronico"];
        $answer = $data["respuesta"];

        $dbAnswer = \Acme\Jewernico\Command\Database::getSecurityAnswerOf($email);
        if ($dbAnswer["Respuesta"] !== $answer) {
            Flight::json(["error" => "La respuesta no coincide"], 403);
            return;
        }

        Flight::json([], 200);
    }

    public static function update_password()
    {
        $data = Flight::request()->data->getData();
        $password = $data["password"];
        $email = $data["correoElectronico"];

        $result = \Acme\Jewernico\Command\Database::updatePasswordForUser($email, $password);
        if ($result != 1) {
            Flight::json(["error" => "Error al actualizar la contraseña"], 400);
            return;
        }

        Flight::json([], 200);
    }
}

?>