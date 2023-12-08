<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Jewernico
{
    public static function index(): void
    {
        $context = [
            "introduction_title" => "Hello world!",
            "introduction_text" => "This is the skeleton for a Flight app."
        ];

        Flight::view()->display("index.twig", $context);
    }

    public static function hello(): void
    {
        $context = [
            "introduction_title" => "Hello world!",
            "introduction_text" => "This is the skeleton for a Flight app."
        ];

        Flight::view()->display("hello.twig", $context);
    }
}
