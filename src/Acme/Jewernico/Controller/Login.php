<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Login {
    public static function load(): void {
    
        Flight::view()->display("login.twig", []);
    }

    public static function logout(): void {
        session_destroy();
        session_abort();
        Flight::redirect("/");
    }
}

?>