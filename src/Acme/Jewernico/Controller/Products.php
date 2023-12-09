<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Products
{
    public static function load(): void
    {
        $products = \Acme\Jewernico\Command\Database::getProducts();
        
        Flight::view()->display("products.twig", ["productos" => $products]);
    }

    public static function loadProduct($id): void
    {
        $product = \Acme\Jewernico\Command\Database::getProduct($id);

        Flight::view()->display("product.twig", ["producto" => $product]);
    }
}