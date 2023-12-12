<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Checkout {
    public static function load() {
        Flight::view()->display("checkout.twig", []);
    }

    public static function information() {
        $cart = \Acme\Jewernico\Command\Database::getCart($_SESSION["user"]->getId());

        $countries = \Acme\Jewernico\Command\Database::getCountries();

        Flight::view()->display("checkout_details.twig", ["carrito" => $cart, "paises" => $countries]);
    }
}

?>