<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Cart {
    public static function add() {
        $data = Flight::request()->data->getData();

        $product = $data["idProducto"];
        $quantity = $data["cantidad"];
        $id = $_SESSION["user"]->getId();
        $_SESSION["cartCount"] += $quantity;

        $res = \Acme\Jewernico\Command\Database::addToCart($id, $product, $quantity);
        
        $cartQuantity = \Acme\Jewernico\Command\Database::getQuantityOfProductInCart($id, $product);

        if ($res) {
            Flight::json(array("success" => "Producto agregado al carrito"), 200);
        } else {
            Flight::json(array("error" => "Error al agregar producto al carrito"), 500);
        }
    }

    public static function remove() {
        $data = Flight::request()->data->getData();

        $product = $data["idProducto"];
        $id = $_SESSION["user"]->getId();

        $quantity = \Acme\Jewernico\Command\Database::getQuantityOfProductInCart($id, $product);

        $res = \Acme\Jewernico\Command\Database::deleteCartItem($id, $product);
        if ($res) {
            Flight::json(array("success" => "Producto eliminado del carrito", "cartCount" => $_SESSION["cartCount"]), 200);
        } else {
            $_SESSION["cartCount"] -= $quantity;
            Flight::json(array("error" => "Error al eliminar producto del carrito"), 500);
        }
    }


    public static function update() {
        $data = Flight::request()->data->getData();

        $product = $data["idProducto"];
        $quantity = $data["cantidad"];
        $id = $_SESSION["user"]->getId();

        $res = \Acme\Jewernico\Command\Database::updateCartItemQuantity($id, $product, $quantity);
        if ($res) {
            Flight::json(array("success" => "Producto actualizado en el carrito", "cartCount" => $_SESSION["cartCount"]
        ), 200);
        } else {
            $_SESSION["cartCount"] += $quantity;
            Flight::json(array("error" => "Error al actualizar producto en el carrito"), 500);
        }
    }
}
?>