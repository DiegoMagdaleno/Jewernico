<?php
namespace Acme\Jewernico\Command;

use Flight;

class Database
{
    public static function getCategories()
    {
        $db = Flight::db();
        $categories = $db->query("SELECT * FROM categoria")->fetchAll();
        return $categories;
    }

    public static function getMaterials()
    {

        $db = Flight::db();
        $materials = $db->query("SELECT * FROM material")->fetchAll();
        return $materials;
    }

    public static function getProducts()
    {
        $db = Flight::db();
        $products = $db->query("SELECT * FROM info_producto")->fetchAll();
        return $products;
    }

    public static function getProduct($id)
    {
        $db = Flight::db();
        $product = $db->prepare("SELECT * FROM info_producto WHERE Id = :id");
        $product->execute(array(":id" => $id));
        $product = $product->fetch();
        return $product;
    }

    public static function getUser($email) {
        $db = Flight::db();
        $user = $db->prepare("SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico");
        $user->execute(array(":correoElectronico"=> $email));
        return $user->fetch();
    }
}
?>