<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Cart {
    public static function load(){
        $cart = \Acme\Jewernico\Command\Database::getCart($_SESSION["user"]->getId());
        
        Flight::view()->display("cart.twig", ["productos" => $cart]);
    }
}

?>