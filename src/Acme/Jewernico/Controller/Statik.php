<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Statik
{
    public static function index(): void
    {
        $context = [
            "introduction_title" => "Hello world!",
            "introduction_text" => "This is the skeleton for a Flight app."
        ];

        Flight::view()->display("index.twig", $context);
    }

    public static function about_us(): void
    {
        Flight::view()->display("about_us.twig", []);
    }
}
