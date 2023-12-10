<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class EmailCoupon {
    public static function send() {
        $data = Flight::request()->data->getData();

        // Crear una nueva clase de correo
        // argumentos: $to, $content, $subject
        // para pasar argumentos a una funcion
        // mi_funcion($arg1, $arg2, $arg3)
        //$email = new \Acme\Jewernico\Command\Email(argumentos aqui);

        
        // enviame
        // $email->send();
    }
}


?>