<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Statik
{
    public static function index(): void
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

        shuffle($products);
        $random_products = array_slice($products, 0, 4);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Flight::view()->display("index.twig", ["productos" => $random_products]);
    }

    public static function about_us(): void
    {
        Flight::view()->display("about_us.twig", []);
    }

    public static function contact(): void
    {
        Flight::view()->display("contact.twig", []);
    }

    public static function faq(): void
    {
        Flight::view()->display("faq.twig", []);
    }
}
