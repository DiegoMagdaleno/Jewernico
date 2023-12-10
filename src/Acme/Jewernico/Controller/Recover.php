<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Recover {
    public static function load(): void {
        Flight::view()->display("recover.twig", []);
    }
}