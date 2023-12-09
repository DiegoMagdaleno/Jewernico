<?php

// -------------------------------------------------- //
// ROUTES
// -------------------------------------------------- //

Flight::route("/", ["\Acme\Jewernico\Controller\Statik", "index"]);
Flight::route("/about-us", ["\Acme\Jewernico\Controller\Statik", "about_us"]);
Flight::route("/products", ['\Acme\Jewernico\Controller\Products', 'load']);
Flight::route('/contact', ['\Acme\Jewernico\Controller\Statik', 'contact']);
Flight::route('/faq', ['\Acme\Jewernico\Controller\Statik', 'faq']);
Flight::route('/products/@id', ['\Acme\Jewernico\Controller\Products', 'loadProduct']);
Flight::route("/login", ["\Acme\Jewernico\Controller\Login", "load"]);
Flight::route("/captcha", ["\Acme\Jewernico\Controller\Captcha", "create"]);
Flight::route("/logout", ["\Acme\Jewernico\Controller\Login", "logout"]);
Flight::route("/admin", ["\Acme\Jewernico\Controller\Admin", "load"]);
Flight::route("/admin/products", ["\Acme\Jewernico\Controller\Admin", "loadProducts"]);

// -------------------------------------------------- //
// API ROUTES
// -------------------------------------------------- //
Flight::route("POST /api/login", ["\Acme\Jewernico\Controller\API\Login", "login"]);
Flight::route("POST /api/propagate", ["\Acme\Jewernico\Controller\API\Login", "propagate"]);

// -------------------------------------------------- //
// MAPPINGS
// -------------------------------------------------- //

Flight::map("notFound", fn() => Flight::view()->display("404.twig", []));

Flight::map("error", fn(Throwable $exception) => $logger->error($exception));
