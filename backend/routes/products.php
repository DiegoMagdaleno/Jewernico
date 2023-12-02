<?php

require_once 'vendor/autoload.php';
require_once 'tokens.php';

Flight::route("GET /products", function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto");
    $query->execute();
    $products = $query->fetchAll();
    Flight::json($products);
});

Flight::route("GET /products/@id", function ($id) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto WHERE Id = :id");
    $query->execute(
        array(
            ":id" => $id
        )
    );
    $product = $query->fetch();
    Flight::json($product);
});

Flight::route("POST /products", function () {
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");

        if ($token->data->nivelPermisos < 1) {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "No autorizado"
                ),
                403
            );
        } else {
            $data = Flight::request()->data->getData();
            $files = Flight::request()->files->getData();
            $db = Flight::db();

            if (isset($data["descripcion"])) {
                $data["descripcion"] = htmlspecialchars($data["descripcion"]);
            } else {
                $data["descripcion"] = "";
            }

            $query = $db->prepare("INSERT INTO producto (Nombre, IdCategoria, IdMaterial, Descripcion, Precio, Stock) VALUES (:nombre, :idCategoria, :idMaterial, :descripcion, :precio, :stock)");
            $query->execute(
                array(
                    ":nombre" => $data["nombre"],
                    ":idCategoria" => $data["idCategoria"],
                    ":idMaterial" => $data["idMaterial"],
                    ":descripcion" => $data["descripcion"],
                    ":precio" => $data["precio"],
                    ":stock" => $data["stock"]
                )
            );
            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Producto agregado"
                ),
                200
            );
        }
    } catch (Exception $e) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "No autorizado"
            ),
            403
        );
    }
});
