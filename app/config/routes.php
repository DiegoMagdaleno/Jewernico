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
Flight::route("/admin/products/add", ["\Acme\Jewernico\Controller\Admin", "addProduct"]);
Flight::route("/admin/products/edit/@id", ["\Acme\Jewernico\Controller\Admin", "editProduct"]);
Flight::route("/register", ["\Acme\Jewernico\Controller\Register", "load"]);
Flight::route("/recover", ["\Acme\Jewernico\Controller\Recover", "load"]);
Flight::route("/cart", ["\Acme\Jewernico\Controller\Cart", "load"]);
Flight::route("/checkout", ["\Acme\Jewernico\Controller\Checkout", "load"]);
Flight::route("/checkout/information", ["\Acme\Jewernico\Controller\Checkout", "information"]);

// -------------------------------------------------- //
// API ROUTES
// -------------------------------------------------- //
Flight::route("POST /api/login", ["\Acme\Jewernico\Controller\API\Login", "login"]);
Flight::route("POST /api/propagate", ["\Acme\Jewernico\Controller\API\Login", "propagate"]);
Flight::route("POST /api/first_coupon", ["\Acme\Jewernico\Controller\API\EmailCoupon", "send"]);
Flight::route("POST /api/register", ["\Acme\Jewernico\Controller\API\Register", "register"]);
Flight::route("POST /api/recover/verify_email", ["\Acme\Jewernico\Controller\API\Recover", "verify_email"]);
Flight::route("POST /api/recover/get_question", ["\Acme\Jewernico\Controller\API\Recover", "get_question"]);
Flight::route("POST /api/recover/verify_question", ["\Acme\Jewernico\Controller\API\Recover", "verify_question"]);
Flight::route("POST /api/recover/update_password", ["\Acme\Jewernico\Controller\API\Recover", "update_password"]);
Flight::route("POST /api/upload/files", ["\Acme\Jewernico\Controller\API\Files", "upload"]);
Flight::route("POST /api/products", ["\Acme\Jewernico\Controller\API\Products", "add"]);
Flight::route("POST /api/products/@id", ["\Acme\Jewernico\Controller\API\Products", "edit"]);
Flight::route("GET /api/products/@id", ["\Acme\Jewernico\Controller\API\Products", "get"]);
Flight::route("DELETE /api/products/@id", ["\Acme\Jewernico\Controller\API\Products", "delete"]);
Flight::route("POST /api/contact", ["\Acme\Jewernico\Controller\API\Contact", "send"]);
Flight::route("POST /api/cart/add", ["\Acme\Jewernico\Controller\API\Cart", "add"]);
Flight::route("POST /api/cart/remove", ["\Acme\Jewernico\Controller\API\Cart", "remove"]);
Flight::route("POST /api/cart/update", ["\Acme\Jewernico\Controller\API\Cart", "update"]);
Flight::route("POST /api/checkout", ["\Acme\Jewernico\Controller\API\Checkout", "checkout"]);   
Flight::route("POST /api/receipt", ["\Acme\Jewernico\Controller\API\Receipt", "send"]);
Flight::route("POST /api/receipt/pdf", ["\Acme\Jewernico\Controller\API\Receipt", "pdf"]);
Flight::route("GET /api/stats/sales/category", ["\Acme\Jewernico\Controller\API\Stats", "salesByCategory"]);
Flight::route("GET /api/stats/sales/month", ["\Acme\Jewernico\Controller\API\Stats", "salesByMonth"]);
Flight::route("GET /api/coupon/@code", ["\Acme\Jewernico\Controller\API\Coupon", "get"]);

// -------------------------------------------------- //
// MAPPINGS
// -------------------------------------------------- //

Flight::map("notFound", fn() => Flight::view()->display("404.twig", []));

Flight::map("error", fn(Throwable $exception) => $logger->error($exception));
