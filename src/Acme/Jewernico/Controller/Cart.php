<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Cart {
    public static function load(){
        if (!isset($_SESSION["user"])) {
            Flight::redirect("/login");
        }

        $cart = \Acme\Jewernico\Command\Database::getCart($_SESSION["user"]->getId());
        
        Flight::view()->display("cart.twig", ["productos" => $cart]);
    }
}

?>