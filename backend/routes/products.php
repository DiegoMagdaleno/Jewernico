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

            if (!isset($data["descuento"])) {
                $data["descuento"] = 0;
            }

            $query = $db->prepare("INSERT INTO producto (Nombre, IdCategoria, IdMaterial, Descripcion, Precio, Stock, Descuento) VALUES (:nombre, :idCategoria, :idMaterial, :descripcion, :precio, :stock, :descuento)");
            $query->execute(
                array(
                    ":nombre" => $data["nombre"],
                    ":idCategoria" => $data["idCategoria"],
                    ":idMaterial" => $data["idMaterial"],
                    ":descripcion" => $data["descripcion"],
                    ":precio" => $data["precio"],
                    ":stock" => $data["stock"],
                    ":descuento" => $data["descuento"]
                )
            );

            $productId = $db->lastInsertId();
            $productDirectory = "images/products/" . $productId;
            mkdir($productDirectory, 0777, true);


            if (isset($files['files'])) {
                $files = $files['files'];

                for ($i = 0; $i < count($files['name']); $i++) {
                    $newFileName = $productId . "_" . $i . ".jpg";
                    $targetFilePath = $productDirectory . "/" . $newFileName;

                    if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {

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

Flight::route("DELETE /products/@id", function ($id) {
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
            $db = Flight::db();
            $query = $db->prepare("DELETE FROM producto WHERE Id = :id");
            $query->execute(
                array(
                    ":id" => $id
                )
            );

            $productDirectory = "images/products/" . $id;
            $filesOnFolder = glob($productDirectory . '/*');
            foreach ($filesOnFolder as $file) {
                unlink($file);
            }
            rmdir($productDirectory);

            $imagesQuery = $db->prepare("DELETE FROM imagen WHERE IdProducto = :idProducto");
            $imagesQuery->execute(
                array(
                    ":id" => $id
                )
            );

            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Producto eliminado"
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

Flight::route("POST /products/@id", function ($id) {
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
            $db = Flight::db();
            $files = Flight::request()->files->getData();

            if (isset($data["descripcion"])) {
                $data["descripcion"] = htmlspecialchars($data["descripcion"]);
            } else {
                $data["descripcion"] = "";
            }

            $query = $db->prepare("UPDATE producto SET Nombre = :nombre, IdCategoria = :idCategoria, IdMaterial = :idMaterial, Descripcion = :descripcion, Precio = :precio, Stock = :stock WHERE Id = :id");
            $query->execute(
                array(
                    ":nombre" => $data["nombre"],
                    ":idCategoria" => $data["idCategoria"],
                    ":idMaterial" => $data["idMaterial"],
                    ":descripcion" => $data["descripcion"],
                    ":precio" => $data["precio"],
                    ":stock" => $data["stock"],
                    ":id" => $id
                )
            );

            $productDirectory = "images/products/" . $id;

            if (isset($files['files'])) {
                $files = $files['files'];

                $existingFiles = glob($productDirectory . '/*');

                foreach ($existingFiles as $file) {
                    $existingChecksum = md5_file($file);
                    $fileIndex = array_search($existingChecksum, array_map('md5_file', $files['tmp_name']));

                    if ($fileIndex === false) {
                        unlink($file);
                        $query = $db->prepare("DELETE FROM imagen WHERE Ruta = :ruta");
                        $query->execute(
                            array(
                                ":ruta" => $file,
                            )
                        );
                    } else {
                        unset($files["tmp_name"][$fileIndex]);
                        unset($files["name"][$fileIndex]);
                    }
                }

                $files["tmp_name"] = array_values($files["tmp_name"]);
                $files["name"] = array_values($files["name"]);

                $fileNewI = count(glob($productDirectory . "/*"));

                for ($i = 0; $i < count($files['name']); $i++) {
                    $newFileName = $id . "_" . $fileNewI . ".jpg";
                    $targetFilePath = $productDirectory . "/" . $newFileName;

                    if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
                        $query = $db->prepare("INSERT INTO imagen (Ruta, IdProducto) VALUES (:ruta, :idProducto)");
                        $query->execute(
                            array(
                                ":ruta" => $targetFilePath,
                                ":idProducto" => $id
                            )
                        );
                        $fileNewI++;
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
            } else {
                $filesOnFolder = glob($productDirectory . '/*');
                foreach ($filesOnFolder as $file) {
                    unlink($file);
                }
                $query = $db->prepare("DELETE FROM imagen WHERE IdProducto = :idProducto");
                $query->execute(
                    array(
                        ":idProducto" => $id
                    )
                );
            }

            Flight::json(
                array(
                    "status" => 200,
                    "message" => "Producto actualizado"
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




