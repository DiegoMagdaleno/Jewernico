<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Admin {
    public static function load() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]->getPermissionLevel() < 1) {
            Flight::redirect("/");
        }

        Flight::view()->display("admin.twig", []);
    }

    public static function loadProducts() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]->getPermissionLevel() < 1) {
            Flight::redirect("/");
        }

        $products = \Acme\Jewernico\Command\Database::getProducts();

        Flight::view()->display("admin_products.twig", ["productos"=> $products]);
    }

    public static function addProduct() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]->getPermissionLevel() < 1) {
            Flight::redirect("/");
        }

        $categories = \Acme\Jewernico\Command\Database::getCategories();
        $materials = \Acme\Jewernico\Command\Database::getMaterials();
        Flight::view()->display("admin_add_product.twig", ["categories" => $categories, "materials" => $materials]);
    }

    public static function editProduct($id) {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]->getPermissionLevel() < 1) {
            Flight::redirect("/");
        }

        $product = \Acme\Jewernico\Command\Database::getProduct($id);
        $categories = \Acme\Jewernico\Command\Database::getCategories();
        $materials = \Acme\Jewernico\Command\Database::getMaterials();
        Flight::view()->display("admin_add_product.twig", ["producto" => $product, "categories" => $categories, "materials" => $materials]);
    }
}
?>