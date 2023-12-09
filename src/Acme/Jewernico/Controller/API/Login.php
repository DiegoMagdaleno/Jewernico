<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Login
{
    public static function login(): void
    {
        $data = Flight::request()->data->getData();

        if ($data["captcha"] !== $_SESSION["phrase"]) {
            Flight::json(array("error" => "Captcha incorrecto"), 403);
            return;
        }

        $user = \Acme\Jewernico\Command\Database::getUser($data['correoElectronico']);

        if ($user === null) {
            Flight::json(array("error" => "Usuario no encontrado"), 404);
            return;
        }

        if ($user["IntentosDeLogin"] >= 3) {
            Flight::json(array("error" => "Usuario bloqueado. Se han enviado instrucciones a tu correo para recuperarlo."), 403);
            return;
        }

        if (password_verify($data["password"], $user["Password"])) {
            $db = Flight::db();
            $query = $db->prepare("SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico");
            $query->execute(array(":correoElectronico" => $data["correoElectronico"]));
            $res = $query->fetch();
            $ret = array(
                "id" => $res["Id"],
                "nombre" => $res["Nombre"],
                "apellidoMaterno" => $res["ApellidoMaterno"],
                "apellidoPaterno" => $res["ApellidoPaterno"],
                "correoElectronico" => $res["CorreoElectronico"],
                "nivelPermisos" => $res["NivelPermisos"],
                "password" => $res["Password"],
            );
            Flight::json($ret, 200);
        } else {
            $db = Flight::db();
            $query = $db->prepare("UPDATE usuario SET IntentosDeLogin = IntentosDeLogin + 1 WHERE CorreoElectronico = :correoElectronico");
            $query->execute(array(":correoElectronico" => $data["correoElectronico"]));
            Flight::json(array("error" => "Contraseña incorrecta"), 403);
            return;
        }
    }

    public static function propagate(): void
    {
        $data = Flight::request()->data->getData();
        if (isset($_SESSION['user'])) {
            Flight::redirect("/");
        }

        $_SESSION['user'] = new \Acme\Jewernico\Model\User($data['id'], $data['nombre'], $data['correoElectronico'], $data['password'], $data['nivelPermisos']);
    }
}