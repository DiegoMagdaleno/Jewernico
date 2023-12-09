<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Admin {
    public static function load() {
        Flight::view()->display("admin.twig", []);
    }
}


?>