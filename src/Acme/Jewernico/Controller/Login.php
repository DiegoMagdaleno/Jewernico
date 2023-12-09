<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Login {
    public static function load(): void {
    
        Flight::view()->display("login.twig", []);
    }
}

?>