<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Register
{
    public static function register()
    {
        $data = Flight::request()->data->getData();

        if ($data["captcha"] != $_SESSION["phrase"]) {
            Flight::json(["error" => "Captcha incorrecto"], 400);
            return;
        }

        $user = \Acme\Jewernico\Command\Database::getUser($data["correoElectronico"]);
        if (!empty($user)) {
            Flight::json(["error" => "El correo electrónico ya está registrado"], 409);
            return;
        }

        $res = \Acme\Jewernico\Command\Database::insertUser($data["nombre"], $data["apellidoPaterno"], $data["apellidoMaterno"], $data["correoElectronico"], $data["password"]);
        if ($res != 1) {
            Flight::json(["error"=> "Error durante el registro de usuario"], 400);
            return;
        }

        $res = \Acme\Jewernico\Command\Database::linkSecurityQuestion($data["idPregunta"], $data["correoElectronico"], $data["respuesta"]);
        if ($res != 1) {
            Flight::json(["error"=>"Error durante el registro de la pregunta de seguridad"], 400);
            return;
        }

        $res = \Acme\Jewernico\Command\Database::linkCartToUser($data["correoElectronico"]);
        if ($res != 1) {
            Flight::json(["error"=>"Error durante el registro del carrito"], 400);
            return;
        }
    }
}

?>