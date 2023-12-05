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
    $product = $query->fetchAll();
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

            $productId = $db->lastInsertId();
            $productDirectory = "images/products/" . $productId;
            mkdir($productDirectory, 0777, true);
            $savedFilesPath = [];

            $files = $files['files'];

            print_r($files['name']);


            for ($i = 0; $i < count($files['name']); $i++) {
                $newFileName = $productId . "_" . $i . ".jpg";
                $targetFilePath = $productDirectory . "/" . $newFileName;

                if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
                    $savedFilesPath[] = $targetFilePath;

                    $query = $db->prepare("INSERT INTO imagen (Ruta, IdProducto) VALUES (:ruta, :idProducto)");
                    $query->execute(
                        array(
                            ":ruta" => $targetFilePath,
                            ":idProducto" => $productId
                        )
                    );
                } else {
                    Flight::json(
                        array(
                            "status" => 500,
                            "message" => "Error al subir archivos"
                        ),
                        500
                    );
                    return;
                }
            }

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
                "message" => "No autorizado, no token",
                "e" => $e->getMessage()
            ),
            403
        );
    }
});

Flight::route("GET /products/@category", function ($category) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto WHERE IdCategoria = :idCategoria");
    $query->execute(
        array(
            ":idCategoria" => $category
        )
    );
    $products = $query->fetchAll();
    Flight::json($products);
});

Flight::route("GET /products/material/@material", function ($material) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto WHERE IdMaterial = :idMaterial");
    $query->execute(
        array(
            ":idMaterial" => $material
        )
    );
    $products = $query->fetchAll();
    Flight::json($products);
});

Flight::route("GET /products/minPrice/@minPrice", function ($minPrice) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto WHERE Precio >= :minPrice");
    $query->execute(
        array(
            ":minPrice" => $minPrice
        )
    );
    $products = $query->fetchAll();
    Flight::json($products);
});

Flight::route("GET /products/maxPrice/@maxPrice", function ($maxPrice) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM producto WHERE Precio <= maxPrice");
    $query->execute(
        array(
            ":maxPrice" => $maxPrice
        )
    );
    $products = $query->fetchAll();
    Flight::json($products);
});




