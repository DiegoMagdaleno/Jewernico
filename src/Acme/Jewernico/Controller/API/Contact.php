<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Contact
{
    public static function send()
    {
        $data = Flight::request()->data->getData();

        $name = $data["nombre"];
        $email = $data["correoElectronico"];

        $mail = new \Acme\Jewernico\Command\Email($email, "Gracias por contactarnos, $name, en breve atenderemos tu solicitud.s", "Gracias por contactarnos");
        try {
            $mail->send();
        } catch (\Exception $e) {
            Flight::json(["error" => "Error al enviar el correo"], 500);
            return;
        }
    }
}

?>