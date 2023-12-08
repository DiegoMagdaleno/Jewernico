<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Products
{
    public static function load(): void
    {
        Flight::view()->display("products.twig", ["products"]);
    }
}