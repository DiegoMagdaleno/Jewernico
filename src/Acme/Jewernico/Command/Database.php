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

    public static function getUser($email)
    {
        $db = Flight::db();
        $user = $db->prepare("SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico");
        $user->execute(array(":correoElectronico" => $email));
        return $user->fetch();
    }

    public static function insertUser($name, $firstLastName, $secondLastName, $email, $password)
    {
        $db = Flight::db();
        $password_enc = password_hash($password, PASSWORD_DEFAULT);
        $query = $db->prepare("INSERT INTO usuario (Nombre, ApellidoPaterno, ApellidoMaterno, CorreoElectronico, Password, NivelPermisos, IntentosDeLogin) VALUES (:nombre, :apellidoPaterno, :apellidoMaterno, :correoElectronico, :password, 0, 0)");
        $query->execute(array(
            ":nombre" => $name,
            ":apellidoPaterno" => $firstLastName,
            ":apellidoMaterno" => $secondLastName,
            ":correoElectronico" => $email,
            ":password" => $password_enc,
        ));
        return $query->rowCount();
    }

    public static function linkSecurityQuestion($idQuestion, $email, $answer)
    {
        $db = Flight::db();
        $query = $db->prepare("INSERT INTO responder (IdPregunta, IdUsuario, Respuesta) VALUES (:idPregunta, (SELECT Id FROM usuario WHERE CorreoElectronico = :correoElectronico), :respuesta)");
        $query->execute(array(
            ":idPregunta" => $idQuestion,
            ":correoElectronico" => $email,
            ":respuesta" => $answer
        ));
        return $query->rowCount();
    }

    public static function getSecurityQuestions()
    {
        $db = Flight::db();
        $questions = $db->prepare("SELECT * FROM pregunta");
        $questions->execute();
        return $questions->fetchAll();
    }

    public static function getSecurityQuestionOf($email)
    {
        $db = Flight::db();
        $query = $db->prepare(
            "SELECT Pregunta FROM pregunta WHERE Id = (SELECT IdPregunta FROM responder WHERE IdUsuario = (SELECT Id FROM usuario WHERE CorreoElectronico = :email))");
        $query->execute(array(":email" => $email));
        return $query->fetch();
    }

    public static function getSecurityAnswerOf($email)
    {
        $db = Flight::db();
        $query = $db->prepare(
            "SELECT Respuesta FROM responder WHERE IdUsuario = (SELECT Id FROM usuario WHERE CorreoElectronico = :email)");
        $query->execute(array(":email" => $email));
        return $query->fetch();
    }

    public static function linkCartToUser($email)
    {
        $db = Flight::db();
        $query = $db->prepare(
            "INSERT INTO carrito (IdUsuario) VALUES (
                (SELECT Id FROM usuario WHERE CorreoElectronico = :email)
            )");
        $query->execute(array(":email" => $email));
        return $query->rowCount();
    }

    public static function updatePasswordForUser($email, $password)
    {
        $db = Flight::db();
        $query = $db->prepare("UPDATE usuario SET Password = :password WHERE CorreoElectronico = :correoElectronico");
        $query->execute(
            array(
                ":password" => password_hash($password, PASSWORD_DEFAULT),
                ":correoElectronico" => $email
            )
        );
        return $query->rowCount();
    }

    public static function getLoginAttemps($email)
    {
        $db = Flight::db();
        $query = $db->prepare("SELECT IntentosDeLogin FROM usuario WHERE CorreoElectronico = :correoElectronico");
        $query->execute(array(":correoElectronico" => $email));
        return $query->fetch()["IntentosDeLogin"];
    }

    public static function resetLoginAttemps($email)
    {
        $db = Flight::db();
        $query = $db->prepare("UPDATE usuario SET IntentosDeLogin = 0 WHERE CorreoElectronico = :correoElectronico");
        $query->execute(array(":correoElectronico" => $email));
        return $query->rowCount();
    }

    public static function incrementLoginAttemps($email)
    {
        $db = Flight::db();
        $query = $db->prepare("UPDATE usuario SET IntentosDeLogin = IntentosDeLogin + 1 WHERE CorreoElectronico = :correoElectronico");
        $query->execute(array(":correoElectronico" => $email));
        return $query->rowCount();
    }

    public static function addProduct($name, $description, $material, $category, $discount, $price, $stock)
    {
        $db = Flight::db();
        $query = $db->prepare("INSERT INTO producto (Nombre, Descripcion, IdMaterial, IdCategoria, Descuento, Precio, Stock) VALUES (:nombre, :descripcion, :idMaterial, :idCategoria, :descuento, :precio, :stock)");
        $query->execute(array(
            ":nombre" => $name,
            ":descripcion" => $description,
            ":idMaterial" => $material,
            ":idCategoria" => $category,
            ":descuento" => $discount,
            ":precio" => $price,
            ":stock" => $stock
        ));
        if ($query->rowCount() > 0) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public static function linkProductToImages($product_id, $images)
    {
        $db = Flight::db();
        $query = $db->prepare("INSERT INTO imagen (IdProducto, Ruta) VALUES (:idProducto, :ruta)");
        for ($i = 0; $i < count($images); $i++) {
            $query->execute(array(
                ":idProducto" => $product_id,
                ":ruta" => $images[$i],
            ));
        }
        return ($query->rowCount() > 0);
    }

    public static function pruneImagesForProduct($id)
    {
        $db = Flight::db();
        $query = $db->prepare("DELETE FROM imagen WHERE IdProducto = :idProducto");
        $query->execute(array(":idProducto" => $id));
        return ($query->rowCount() > 0);
    }

    public static function getImageCountForProduct($id)
    {
        $db = Flight::db();
        $query = $db->prepare("SELECT COUNT(*) AS image_count FROM imagen WHERE IdProducto = :idProducto");
        $query->execute(array(":idProducto" => $id));
        return $query->fetchColumn();
    }

    public static function updateProduct($id, $name, $description, $material, $category, $discount, $price, $stock)
    {
        $db = Flight::db();
        $query = $db->prepare("UPDATE producto SET Nombre = :nombre, Descripcion = :descripcion, IdMaterial = :idMaterial, IdCategoria = :idCategoria, Descuento = :descuento, Precio = :precio, Stock = :stock WHERE Id = :id");
        $query->execute(array(
            ":id" => $id,
            ":nombre" => $name,
            ":descripcion" => $description,
            ":idMaterial" => $material,
            ":idCategoria" => $category,
            ":descuento" => $discount,
            ":precio" => $price,
            ":stock" => $stock
        ));
        return ($query->rowCount() > 0);
    }
}
?>