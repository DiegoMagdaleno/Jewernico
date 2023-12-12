<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Checkout {
    public static function load() {
        Flight::view()->display("checkout.twig", []);
    }

    public static function information() {
        Flight::view()->display("checkout_details.twig", []);
    }
}

?>