<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Checkout {
    public static function load() {
        if (!isset($_SESSION["user"])) {
            Flight::redirect("/login");
        }
        if ($_SESSION["cartCount"] == 0) {
            Flight::redirect("/cart");
        }

        Flight::view()->display("checkout.twig", []);
    }

    public static function information($type) {
        if (!isset($_SESSION["user"])) {
            Flight::redirect("/login");
        }
        $cart = \Acme\Jewernico\Command\Database::getCart($_SESSION["user"]->getId());

        $countries = \Acme\Jewernico\Command\Database::getCountries();

        Flight::view()->display("checkout_details.twig", ["carrito" => $cart, "paises" => $countries, "tipo" => $type]);
    }
}

?>