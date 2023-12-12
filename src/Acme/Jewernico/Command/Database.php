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

    public static function deleteProduct($id)
    {
        $db = Flight::db();
        $query = $db->prepare("DELETE FROM producto WHERE Id = :id");
        $query->execute(array(":id" => $id));
        return ($query->rowCount() > 0);
    }

    public static function getCartIdByUserId($id)
    {
        $db = Flight::db();
        $query = $db->prepare("SELECT Id FROM carrito WHERE IdUsuario = :idUsuario");
        $query->execute(array(":idUsuario" => $id));
        return $query->fetch()["Id"];
    }

    public static function addToCart($userId, $productId, $quantity)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("INSERT INTO detalle_carrito (IdCarrito, IdProducto, Cantidad) VALUES (:idCarrito, :idProducto, :cantidad)");
        $query->execute(array(
            ":idCarrito" => $cartId,
            ":idProducto" => $productId,
            ":cantidad" => $quantity
        ));
        return ($query->rowCount() > 0);
    }

    public static function updateCartItemQuantity($userId, $productId, $newQuantity)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("UPDATE detalle_carrito SET Cantidad = :cantidad WHERE IdCarrito = :idCarrito AND IdProducto = :idProducto");
        $query->execute(array(
            ":cantidad" => $newQuantity,
            ":idCarrito" => $cartId,
            ":idProducto" => $productId
        ));
        return ($query->rowCount() > 0);
    }

    public static function deleteCartItem($userId, $productId)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("DELETE FROM detalle_carrito WHERE IdCarrito = :idCarrito AND IdProducto = :idProducto");
        $query->execute(array(
            ":idCarrito" => $cartId,
            ":idProducto" => $productId
        ));
        return ($query->rowCount() > 0);
    }

    public static function getQuantityOfProductInCart($userId, $productId)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("SELECT Cantidad FROM detalle_carrito WHERE IdCarrito = :idCarrito AND IdProducto = :idProducto");
        $query->execute(array(
            ":idCarrito" => $cartId,
            ":idProducto" => $productId
        ));
        return $query->fetch()["Cantidad"];
    }

    public static function getCart($userId)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("SELECT * FROM cart_view WHERE CarritoId = :idCarrito");

        $query->execute(array(
            ":idCarrito" => $cartId,
        ));
        return $query->fetchAll();
    }

    public static function getTotalCartItems($userId)
    {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("SELECT SUM(Cantidad) AS total FROM detalle_carrito WHERE IdCarrito = :idCarrito");

        $query->execute(array(
            ":idCarrito" => $cartId,
        ));
        return $query->fetch()["total"];
    }

    public static function getCountries() {
        $db = Flight::db();
        $query = $db->prepare("SELECT * FROM pais");
        $query->execute();
        return $query->fetchAll();
    }

    public static function updateProductStock($id, $newStock) {
        $db = Flight::db();
        $query = $db->prepare("UPDATE producto SET Stock = :stock WHERE Id = :id");
        $query->execute(array(
            ":stock" => $newStock,
            ":id" => $id
        ));
        return ($query->rowCount() > 0);
    }

    public static function emptyCart($userId) {
        $db = Flight::db();

        $cartId = self::getCartIdByUserId($userId);

        $query = $db->prepare("DELETE FROM detalle_carrito WHERE IdCarrito = :idCarrito");
        $query->execute(array(
            ":idCarrito" => $cartId,
        ));
        return ($query->rowCount() > 0);
    }

    public static function createOrder($userId) {
        $db = Flight::db();

        $query = $db->prepare("INSERT INTO compra (IdUsuario, Fecha) VALUES (:idUsuario, NOW())");
        $query->execute(array(
            ":idUsuario" => $userId,
        ));
        if ($query->rowCount() > 0) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    public static function registerOrderItem($orderId, $productId, $quantity) {
        $db = Flight::db();

        $query = $db->prepare("INSERT INTO contener (IdCompra, IdProducto, Cantidad) VALUES (:idOrden, :idProducto, :cantidad)");
        $query->execute(array(
            ":idOrden" => $orderId,
            ":idProducto" => $productId,
            ":cantidad" => $quantity
        ));
        return ($query->rowCount() > 0);
    }    
}
?>