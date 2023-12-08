<?php

// -------------------------------------------------- //
// ROUTES
// -------------------------------------------------- //

Flight::route("/", ["\Acme\Jewernico\Controller\Jewernico", "index"]);


// -------------------------------------------------- //
// API ROUTES
// -------------------------------------------------- //
Flight::route("/api/message", ["\Acme\Jewernico\Controller\API", "message"]);


// -------------------------------------------------- //
// MAPPINGS
// -------------------------------------------------- //

Flight::map("notFound", fn() => Flight::view()->display("404.twig", []));

Flight::map("error", fn(Throwable $exception) => $logger->error($exception));
