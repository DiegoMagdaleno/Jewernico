<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Products
{
    public static function add(): void
    {
        $data = Flight::request()->data->getData();

        $product_id = \Acme\Jewernico\Command\Database::addProduct($data["nombre"], $data["descripcion"], $data["idMaterial"], $data["idCategoria"], $data["descuento"], $data["precio"], $data["stock"]);

        if ($product_id === false) {
            Flight::json(["error" => "Error al agregar el producto"], 400);
            return;
        }

        $images = json_decode($data["imagenes"], true);

        $res = \Acme\Jewernico\Command\Database::linkProductToImages($product_id, $images);

        if ($res === false) {
            Flight::json(["error" => "Error al agregar las imágenes"], 400);
            return;
        }
    }

    public static function get($id): void
    {
        $product = \Acme\Jewernico\Command\Database::getProduct($id);
        if ($product === false) {
            Flight::json(["error" => "No existe dicho producto"], 404);
        }

        Flight::json(["producto" => $product], 200);
    }

    public static function edit($id): void
    {
        $data = Flight::request()->data->getData();

        $res = \Acme\Jewernico\Command\Database::updateProduct(
            $id,
            $data["nombre"],
            $data["descripcion"],
            $data["idMaterial"],
            $data["idCategoria"],
            $data["descuento"],
            $data["precio"],
            $data["stock"],
        );

        $images = json_decode($data["imagenes"], true);

        $count = \Acme\Jewernico\Command\Database::getImageCountForProduct($id);

        if ($count > 0) {
            $res = \Acme\Jewernico\Command\Database::pruneImagesForProduct($id);
            if ($res === false) {
                Flight::json(["error" => "Error durante actualizacion de imagenes"], 400);
                return;
            }
        }

        if (!empty($images)) {
            $res = \Acme\Jewernico\Command\Database::linkProductToImages($id, $images);
            if ($res === false) {
                Flight::json(["error" => "Error durante actualizacion de imagenes"], 400);
                return;
            }
        }

        Flight::json(["success" => "Producto actualizado"], 200);
    }

    public static function delete($id): void
    {
        $res = \Acme\Jewernico\Command\Database::deleteProduct($id);
        if ($res === false) {
            Flight::json(["error" => "Error al eliminar el producto"], 400);
            return;
        }

        Flight::json(["success" => "Producto eliminado"], 200);
    }
}

?>