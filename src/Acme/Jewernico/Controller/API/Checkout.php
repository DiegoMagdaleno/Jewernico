<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Checkout {
    public static function checkout() {
        $cart = \Acme\Jewernico\Command\Database::getCart($_SESSION["user"]->getId());

        $order = \Acme\Jewernico\Command\Database::createOrder($_SESSION["user"]->getId());
        if (!$order) {
            Flight::json(array("error" => "Error al crear la orden"), 500);
        }
        
        foreach ($cart as $item) {
            $product = \Acme\Jewernico\Command\Database::getProduct($item["ProductoId"]);
            $currentStock = $product["Stock"];
            $newStock = $currentStock - $item["CarritoCantidad"];
            \Acme\Jewernico\Command\Database::updateProductStock($item["ProductoId"], $newStock);
            \Acme\Jewernico\Command\Database::registerOrderItem($order, $item["ProductoId"], $item["CarritoCantidad"]);
        }

        \Acme\Jewernico\Command\Database::emptyCart($_SESSION["user"]->getId());

        Flight::json(array("success" => "¡Orden realizada!"), 200);
    }
}