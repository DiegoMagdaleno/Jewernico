<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Products
{
    public static function load(): void
    {
        $products = \Acme\Jewernico\Command\Database::getProducts();

        for ($i = 0; $i < count($products); $i++)
        {
            if (!is_array($products[$i]["Imagenes"])) {
                $images = $products[$i]["Imagenes"];
                $products[$i]["Imagenes"] = [];
                if (!empty($images)) {
                    $products[$i]["Imagenes"][] = $images;
                }
            }
        }
        
        Flight::view()->display("products.twig", ["productos" => $products]);
    }

    public static function loadProduct($id): void
    {
        $product = \Acme\Jewernico\Command\Database::getProduct($id);

        Flight::view()->display("product.twig", ["producto" => $product]);
    }
}