<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Cart {
    public static function load(){
        Flight::view()->display("cart.twig", []);
    }
}

?>