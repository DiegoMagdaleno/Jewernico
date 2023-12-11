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
            \Acme\Jewernico\Command\Database::resetLoginAttemps($data["correoElectronico"]);
            Flight::json($ret, 200);
        } else {
            $db = Flight::db();
            \Acme\Jewernico\Command\Database::incrementLoginAttemps($data["correoElectronico"]);
            $attemps = \Acme\Jewernico\Command\Database::getLoginAttemps($data["correoElectronico"]);
            if ($attemps >= 3) {
                $mail = new \Acme\Jewernico\Command\Email($data["correoElectronico"], "Tu cuenta ha sido bloqueada por intentos fallidos de inicio de sesión. Para desbloquearla, sigue las instrucciones en el siguiente enlace: " . $GLOBALS['url'] . "recover", "Tu cuenta ha sido bloqueada", );
                Flight::json(array("error" => "Usuario bloqueado. Se han enviado instrucciones a tu correo para recuperarlo."), 403);
                $mail->send();
                return;
            }
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
        $_SESSION['cart'] = 0;
    }
}