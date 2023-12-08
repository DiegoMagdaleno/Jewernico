<?php

// -------------------------------------------------- //
// ROUTES
// -------------------------------------------------- //

Flight::route("/", ["\Acme\Jewernico\Controller\Statik", "index"]);
Flight::route("/about-us", ["\Acme\Jewernico\Controller\Statik", "about_us"]);
Flight::route("/products", ['\Acme\Jewernico\Controller\Products', 'load']);
Flight::route('/contact', ['\Acme\Jewernico\Controller\Statik', 'contact']);

// -------------------------------------------------- //
// API ROUTES
// -------------------------------------------------- //
Flight::route("/api/message", ["\Acme\Jewernico\Controller\API", "message"]);


// -------------------------------------------------- //
// MAPPINGS
// -------------------------------------------------- //

Flight::map("notFound", fn() => Flight::view()->display("404.twig", []));

Flight::map("error", fn(Throwable $exception) => $logger->error($exception));
