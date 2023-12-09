<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Admin {
    public static function load() {
        Flight::view()->display("admin.twig", []);
    }

    public static function loadProducts() {
        $products = \Acme\Jewernico\Command\Database::getProducts();

        Flight::view()->display("admin_products.twig", ["productos"=> $products]);
    }
}


?>