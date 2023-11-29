<?php

require 'vendor/autoload.php';
require 'util.php';

define(
    'API_ENDPOINTS_TO_TABLES',
    array(
        'users' => 'usuario',
        'categories' => 'categoria',
        'phones' => 'telefono',
        'materials' => 'material',
        'managers' => 'encargado',
        'products' => 'producto',
        'suppliers' => 'proveedor',
        'supplies' => 'suministra',
        'availibility' => 'disponibleen',
        'locations' => 'sucursal',
        'orders' => 'compra',
        'orderdetails' => 'contener'
    )
);


Flight::register(
    'db',
    'PDO',
    array('mysql:host=localhost;dbname=jewernico', 'root', ''),
    function ($db) {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
);

function getTable($table)
{
    $db = Flight::db();
    $query = "SELECT * FROM $table";
    $result = $db->query($query);
    if ($result->rowCount() > 0) {
        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
            $rows[] = $row;
        return $rows;
    }
    return null;
}

function getIdFromTable($table, $id)
{
    $db = Flight::db();
    $query = "SELECT * FROM $table WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $id));
    $result = $stmt->fetchAll();
    if ($result != null)
        return $result;
    return null;
}

function deleteFromTable($table, $id)
{
    $db = Flight::db();
    $query = "DELETE FROM $table WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $id));
    $rowCount = $stmt->rowCount();

    return $rowCount > 0;
}

Flight::route("GET /users", function () {
    $table = getTable("usuario");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /categories", function () {
    $table = getTable("categoria");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /phones", function () {
    $table = getTable("telefono");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /materials", function () {
    $table = getTable("material");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /managers", function () {
    $table = getTable("encargado");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /products", function () {
    $table = getTable("producto");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /suppliers", function () {
    $table = getTable("proveedor");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /supplies", function () {
    $table = getTable("suministra");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /availibility", function () {
    $table = getTable("disponibleen");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /locations", function () {
    $table = getTable("sucursal");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /orders", function () {
    $table = getTable("compra");
    if ($table != null)
        echo arrayToHTMLTable($table);
});

Flight::route("GET /orderdetails", function () {
    $table = getTable("contener");
    if ($table != null)
        echo arrayToHTMLTable($table);
});


Flight::route("GET /users_and_phones", function () {
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, Usuario.ApellidoMaterno, Usuario.ApellidoPaterno, Telefono.Telefono FROM usuario INNER JOIN Telefono ON Usuario.Id = Telefono.IdUsuario";
    $result = $db->query($query);
    if ($result->rowCount() > 0) {
        $rows = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
            $rows[] = $row;
        echo arrayToHTMLTable($rows);
    }
});

Flight::route("GET /orders/@date", function ($date) {
    $db = Flight::db();
    $query = "SELECT Producto.Nombre, Contener.Cantidad FROM Compra JOIN Contener ON Compra.Id = Contener.IdCompra JOIN Producto ON Contener.IdProducto = Producto.Id WHERE Compra.Fecha = :date";
    $stmt = $db->prepare($query);
    $stmt->execute(array(':date' => $date));
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /orders_at/@location", function ($location) {
    $db = Flight::db();
    $query = "SELECT Producto.Nombre, Contener.Cantidad FROM Compra JOIN Contener ON Compra.Id = Contener.IdCompra JOIN Producto ON Contener.IdProducto = Producto.Id JOIN Sucursal ON Compra.IdSucursal = Sucursal.Id WHERE Sucursal.Ubicacion = :location";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":location" => $location));
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /no_purchases", function(){
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, Usuario.ApellidoMaterno, Usuario.ApellidoPaterno FROM Usuario LEFT JOIN Compra ON Usuario.Id = Compra.IdUsuario WHERE Compra.IdUsuario IS NULL";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /availability_at_every_location", function(){
    $db = Flight::db();
    $query = "SELECT Producto.Nombre, Sucursal.Ubicacion, DisponibleEn.Stock FROM Producto JOIN DisponibleEn ON Producto.Id = DisponibleEn.IdProducto JOIN Sucursal ON DisponibleEn.IdSucursal = Sucursal.Id WHERE DisponibleEn.Stock > 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /purchased_at_multiple", function(){
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, Usuario.CorreoElectronico FROM Usuario JOIN (SELECT IdUsuario FROM Compra GROUP BY IdUsuario HAVING COUNT(DISTINCT IdSucursal) > 1) AS ComprasMultiples ON Usuario.Id = ComprasMultiples.IdUsuario";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /products_categories_and_stock", function(){
    $db = Flight::db();
    $query = "SELECT Producto.Nombre AS NombreProducto, Categoria.Nombre AS NombreCategoria, DisponibleEn.Stock AS StockDisponible FROM Producto JOIN Categoria ON Producto.IdCategoria = Categoria.Id JOIN DisponibleEn ON Producto.Id = DisponibleEn.IdProducto";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /users_and_order_count", function(){
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, Usuario.CorreoElectronico, COUNT(Compra.Id) AS TotalCompras FROM Usuario JOIN Compra ON Usuario.Id = Compra.IdUsuario GROUP BY Usuario.Nombre, Usuario.CorreoElectronico";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
        echo arrayToHTMLTable($result);
});

Flight::route("GET /users_that_have_ordered", function(){
    $db = Flight::db();
    $query = "SELECT DISTINCT Usuario.Nombre, Usuario.CorreoElectronico FROM Usuario JOIN Compra ON Usuario.Id = Compra.IdUsuario";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /products_never_ordered", function(){
    $db = Flight::db();
    $query = "SELECT Producto.Nombre FROM Producto LEFT JOIN Contener ON Producto.Id = Contener.IdProducto WHERE Contener.IdProducto IS NULL";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /average_orders_per_user/@upper", function($upper){
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, AVG(Contener.Cantidad) AS PromedioCompras FROM Usuario JOIN Compra ON Usuario.Id = Compra.IdUsuario JOIN Contener ON Compra.Id = Contener.IdCompra GROUP BY Usuario.Id, Usuario.Nombre HAVING AVG(Contener.Cantidad) > :upper";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":upper" => $upper));
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /product_with_stock_more_than_at/@stock/@id", function($stock, $id){
    $db = Flight::db();
    $query = "SELECT IdSucursal, AVG(Stock) AS StockPromedio FROM DisponibleEn WHERE IdProducto = :id GROUP BY IdSucursal HAVING AVG(Stock) > :stock";
    $stmt = $db->prepare($query);
    $stmt->execute(array(
        ":id" => $id,
        ":stock" => $stock
    ));
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /products_with_more_than_n/@stock", function($stock){
    $db = Flight::db();
    $query = "SELECT Producto.Nombre AS NombreProducto, AVG(DisponibleEn.Stock) AS PromedioStock FROM Producto JOIN DisponibleEn ON Producto.Id = DisponibleEn.IdProducto GROUP BY Producto.Id, Producto.Nombre HAVING AVG(DisponibleEn.Stock) > :stock";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":stock" => $stock));
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /products_with_price_more_than/@price", function($price){
    $db = Flight::db();
    $query = "SELECT Producto.Nombre AS NombreProducto, Producto.Precio AS PrecioProducto FROM Producto WHERE Producto.Precio > :price";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":price" => $price));
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});

Flight::route("GET /users_with_more_than_one_phone", function(){
    $db = Flight::db();
    $query = "SELECT Usuario.Nombre, Usuario.CorreoElectronico FROM Usuario JOIN Telefono ON Usuario.Id = Telefono.IdUsuario GROUP BY Usuario.Id, Usuario.Nombre, Usuario.CorreoElectronico HAVING COUNT(Telefono.Telefono) > 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result != null)
    echo arrayToHTMLTable($result);
});


Flight::route("GET /users/@id", function ($id) {
    $result = getIdFromTable("usuario", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /categories/@id", function ($id) {
    $result = getIdFromTable("categoria", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /phones/@id", function ($id) {
    $db = Flight::db();
    $query = "SELECT * FROM telefono WHERE Telefono = :telefeno";
    $stmt = $db->prepare($query);
    $stmt->execute(array(":telefono" => $id));
    $result = $stmt->fetchAll();
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /materials/@id", function ($id) {
    $result = getIdFromTable("material", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /managers/@id", function ($id) {
    $result = getIdFromTable("encargado", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /products/@id", function ($id) {
    $result = getIdFromTable("producto", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /suppliers/@id", function ($id) {
    $result = getIdFromTable("proveedor", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /supplies/@id", function ($id) {
    $result = getIdFromTable("suministra", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /availibility/@id", function ($id) {
    $result = getIdFromTable("disponibleen", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /locations/@id", function ($id) {
    $result = getIdFromTable("sucursal", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /orders/@id", function ($id) {
    $result = getIdFromTable("compra", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /orderdetails/@id", function ($id) {
    $result = getIdFromTable("contener", $id);
    if ($result != null)
        Flight::json($result);
});

Flight::route("GET /schema/@table", function ($table) {
    $db = Flight::db();
    $table_name = API_ENDPOINTS_TO_TABLES[$table];
    $query = "DESCRIBE $table_name";
    $result = $db->query($query);

    if ($result) {
        $schema = $result->fetchAll(PDO::FETCH_ASSOC);
        if ($schema) {
            Flight::json($schema);
        } else {
            echo "Table does not exist or has no columns.";
        }
    } else {
        echo "Error fetching table schema.";
    }
});

Flight::route("DELETE /users/@id", function ($id) {
    $result = deleteFromTable("usuario", $id);
    if ($result)
        echo "Deleted user with id $id";
    else
        echo "Could not delete user with id $id";
});

Flight::route("DELETE /categories/@id", function ($id) {
    $result = deleteFromTable("categoria", $id);
    if ($result)
        echo "Deleted category with id $id";
    else
        echo "Could not delete category with id $id";
});

Flight::route("DELETE /phones/@id", function ($id) {
    $db = Flight::db();
    $query = "DELETE FROM telefono WHERE Telefono = :telefono";
    $result = $db->query($query);
    if ($result->rowCount() > 0)
        echo "Deleted phone with id $id";
    else
        echo "Could not delete phone with id $id";
});

Flight::route("DELETE /materials/@id", function ($id) {
    $result = deleteFromTable("material", $id);
    if ($result)
        echo "Deleted material with id $id";
    else
        echo "Could not delete material with id $id";
});

Flight::route("DEELTE /managers/@id", function ($id) {
    $result = deleteFromTable("encargado", $id);
    if ($result)
        echo "Deleted manager with id $id";
    else
        echo "Could not delete manager with id $id";
});

Flight::route("DELETE /products/@id", function ($id) {
    $result = deleteFromTable("producto", $id);
    if ($result)
        echo "Deleted product with id $id";
    else
        echo "Could not delete product with id $id";
});

Flight::route("DELETE /suppliers/@id", function ($id) {
    $result = deleteFromTable("proveedor", $id);
    if ($result)
        echo "Deleted supplier with id $id";
    else
        echo "Could not delete supplier with id $id";
});

Flight::route("DELETE /supplies/@id", function ($id) {
    $result = deleteFromTable("suministra", $id);
    if ($result)
        echo "Deleted supply with id $id";
    else
        echo "Could not delete supply with id $id";
});

Flight::route("DELETE /availibility/@id", function ($id) {
    $result = deleteFromTable("disponibleen", $id);
    if ($result)
        echo "Deleted availibility with id $id";
    else
        echo "Could not delete availibility with id $id";
});

Flight::route("DELETE /locations/@id", function ($id) {
    $result = deleteFromTable("sucursal", $id);
    if ($result)
        echo "Deleted location with id $id";
    else
        echo "Could not delete location with id $id";
});

Flight::route("DELETE /orders/@id", function ($id) {
    $result = deleteFromTable("compra", $id);
    if ($result)
        echo "Deleted order with id $id";
    else
        echo "Could not delete order with id $id";
});

Flight::route("DELETE /orderdetails/@id", function ($id) {
    $result = deleteFromTable("contener", $id);
    if ($result)
        echo "Deleted order detail with id $id";
    else
        echo "Could not delete order detail with id $id";
});

Flight::route("POST /users", function () {
    $db = Flight::db();
    $query = "INSERT INTO usuario (Nombre, ApellidoMaterno, ApellidoPaterno, Direccion, CorreoElectronico, Password, NivelPermisos) VALUES (:nombre, :apellidoMaterno, :apellidoPaterno, :direccion, :correoElectronico, :password, :nivelPermisos)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":apellidoMaterno" => Flight::request()->data->apellidoMaterno,
            ":apellidoPaterno" => Flight::request()->data->apellidoPaterno,
            ":direccion" => Flight::request()->data->direccion,
            ":correoElectronico" => Flight::request()->data->correoElectronico,
            ":password" => Flight::request()->data->password,
            ":nivelPermisos" => Flight::request()->data->nivelPermisos
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted user with id " . $db->lastInsertId();
    } else {
        echo "Could not insert user";
    }
});

Flight::route("POST /categories", function () {
    $db = Flight::db();
    $query = "INSERT INTO categoria (Nombre) VALUES (:nombre)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted category with id " . $db->lastInsertId();
    } else {
        echo "Could not insert category";
    }
});

Flight::route("POST /suppliers", function () {
    $db = Flight::db();
    $query = "INSERT INTO proveedor (Nombre, Direccion, CorreoElectronico, Telefono) VALUES (:nombre, :direccion, :correoElectronico, :telefono)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":direccion" => Flight::request()->data->direccion,
            ":correoElectronico" => Flight::request()->data->correoElectronico,
            ":telefono" => Flight::request()->data->telefono
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted supplier with id " . $db->lastInsertId();
    } else {
        echo "Could not insert supplier";
    }
});

Flight::route("POST /orders", function () {
    $db = Flight::db();
    $query = "INSERT INTO compra (Fecha, IdUsuario, IdSucursal) VALUES (:fecha, :idUsuario, :idSucursal)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":fecha" => Flight::request()->data->fecha,
            ":idUsuario" => Flight::request()->data->idUsuario,
            ":idSucursal" => Flight::request()->data->idSucursal
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted order with id " . $db->lastInsertId();
    } else {
        echo "Could not insert order";
    }
});

Flight::route("POST /orderdetails", function () {
    $db = Flight::db();
    $query = "INSERT INTO contener (IdCompra, IdProducto, Cantidad) VALUES (:idCompra, :idProducto, :cantidad)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idCompra" => Flight::request()->data->idCompra,
            ":idProducto" => Flight::request()->data->idProducto,
            ":cantidad" => Flight::request()->data->cantidad
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted order detail with id " . $db->lastInsertId();
    } else {
        echo "Could not insert order detail";
    }
});

Flight::route("POST /phones", function () {
    $db = Flight::db();
    $query = "INSERT INTO telefono (IdUsuario, Telefono) VALUES (:idUsuario, :telefono)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idUsuario" => Flight::request()->data->idUsuario,
            ":telefono" => Flight::request()->data->telefono
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted phone with id " . $db->lastInsertId();
    } else {
        echo "Could not insert phone";
    }
});

Flight::route("POST /materials", function () {
    $db = Flight::db();
    $query = "INSERT INTO material (Nombre) VALUES (:nombre)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted material with id " . $db->lastInsertId();
    } else {
        echo "Could not insert material";
    }
});

Flight::route("POST /managers", function () {
    $db = Flight::db();
    $query = "INSERT INTO encargado (Nombre, ApellidoPaterno, ApellidoMaterno, Telefono) VALUES (:nombre, :apellidoPaterno, :apellidoMaterno, :telefono)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":apellidoPaterno" => Flight::request()->data->apellidoPaterno,
            ":apellidoMaterno" => Flight::request()->data->apellidoMaterno,
            ":telefono" => Flight::request()->data->telefono
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted manager with id " . $db->lastInsertId();
    } else {
        echo "Could not insert manager";
    }
});

Flight::route("POST /products", function () {
    $db = Flight::db();
    $query = "INSERT INTO producto (Nombre, Precio, Descripcion, IdMaterial, IdCategoria) VALUES (:nombre, :precio, :descripcion, :idMaterial, :idCategoria)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":precio" => Flight::request()->data->precio,
            ":descripcion" => Flight::request()->data->descripcion,
            ":idMaterial" => Flight::request()->data->idMaterial,
            ":idCategoria" => Flight::request()->data->idCategoria
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted product with id" . $db->lastInsertId();
    } else {
        echo "Could not insert product";
    }
});

Flight::route("POST /locations", function () {
    $db = Flight::db();
    $query = "INSERT INTO sucursal (Ubicacion, IdEncargado) VALUES (:ubicacion, :idEncargado)";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":ubicacion" => Flight::request()->data->ubicacion,
            ":idEncargado" => Flight::request()->data->idEncargado
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Inserted location with id" . $db->lastInsertId();
    } else {
        echo "Could not insert location";
    }
});

Flight::route("PUT /users/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE usuario SET Nombre = :nombre, ApellidoMaterno = :apellidoMaterno, ApellidoPaterno = :apellidoPaterno, Direccion = :direccion, CorreoElectronico = :correoElectronico, Password = :password, NivelPermisos = :nivelPermisos WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":apellidoMaterno" => Flight::request()->data->apellidoMaterno,
            ":apellidoPaterno" => Flight::request()->data->apellidoPaterno,
            ":direccion" => Flight::request()->data->direccion,
            ":correoElectronico" => Flight::request()->data->correoElectronico,
            ":password" => Flight::request()->data->password,
            ":nivelPermisos" => Flight::request()->data->nivelPermisos,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated user with id $id";
    } else {
        echo "Could not update user with id $id";
    }
});

Flight::route("PUT /categories/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE categoria SET Nombre = :nombre WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated category with id $id";
    } else {
        echo "Could not update category with id $id";
    }
});

Flight::route("PUT /phone/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE telefono SET IdUsuario = :idUsuario, Telefono = :telefono WHERE Telefono = :telefono";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idUsuario" => Flight::request()->data->idUsuario,
            ":telefono" => Flight::request()->data->telefono
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated phone with id $id";
    } else {
        echo "Could not update phone with id $id";
    }
});

Flight::route("PUT /materials/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE material SET Nombre = :nombre WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated material with id $id";
    } else {
        echo "Could not update material with id $id";
    }
});

Flight::route("PUT /managers/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE encargado SET Nombre = :nombre, ApellidoPaterno = :apellidoPaterno, ApellidoMaterno = :apellidoMaterno, Telefono = :telefono WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":apellidoPaterno" => Flight::request()->data->apellidoPaterno,
            ":apellidoMaterno" => Flight::request()->data->apellidoMaterno,
            ":telefono" => Flight::request()->data->telefono,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated manager with id $id";
    } else {
        echo "Could not update manager with id $id";
    }
});

Flight::route("PUT /products/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE producto SET Nombre = :nombre, Precio = :precio, Descripcion = :descripcion, IdMaterial = :idMaterial, IdCategoria = :idCategoria WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":precio" => Flight::request()->data->precio,
            ":descripcion" => Flight::request()->data->descripcion,
            ":idMaterial" => Flight::request()->data->idMaterial,
            ":idCategoria" => Flight::request()->data->idCategoria,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated product with id $id";
    } else {
        echo "Could not update product with id $id";
    }
});

Flight::route("PUT /suppliers/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE proveedor SET Nombre = :nombre, Direccion = :direccion, CorreoElectronico = :correoElectronico, Telefono = :telefono WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":nombre" => Flight::request()->data->nombre,
            ":direccion" => Flight::request()->data->direccion,
            ":correoElectronico" => Flight::request()->data->correoElectronico,
            ":telefono" => Flight::request()->data->telefono,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated supplier with id $id";
    } else {
        echo "Could not update supplier with id $id";
    }
});

Flight::route("PUT /supplies/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE suministra SET IdProveedor = :idProveedor, IdProducto = :idProducto WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idProveedor" => Flight::request()->data->idProveedor,
            ":idProducto" => Flight::request()->data->idProducto,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated supply with id $id";
    } else {
        echo "Could not update supply with id $id";
    }
});

Flight::route("PUT /availibility/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE disponibleen SET IdProducto = :idProducto, IdSucursal = :idSucursal, Cantidad = :cantidad WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idProducto" => Flight::request()->data->idProducto,
            ":idSucursal" => Flight::request()->data->idSucursal,
            ":cantidad" => Flight::request()->data->cantidad,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated availibility with id $id";
    } else {
        echo "Could not update availibility with id $id";
    }
});

Flight::route("PUT /locations/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE sucursal SET Ubicacion = :ubicacion, IdEncargado = :idEncargado WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":ubicacion" => Flight::request()->data->ubicacion,
            ":idEncargado" => Flight::request()->data->idEncargado,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated location with id $id";
    } else {
        echo "Could not update location with id $id";
    }
});

Flight::route("PUT /orders/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE compra SET Fecha = :fecha, IdUsuario = :idUsuario, IdSucursal = :idSucursal WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":fecha" => Flight::request()->data->fecha,
            ":idUsuario" => Flight::request()->data->idUsuario,
            ":idSucursal" => Flight::request()->data->idSucursal,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated order with id $id";
    } else {
        echo "Could not update order with id $id";
    }
});

Flight::route("PUT /orderdetails/@id", function ($id) {
    $db = Flight::db();
    $query = "UPDATE contener SET IdCompra = :idCompra, IdProducto = :idProducto, Cantidad = :cantidad WHERE Id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(
        array(
            ":idCompra" => Flight::request()->data->idCompra,
            ":idProducto" => Flight::request()->data->idProducto,
            ":cantidad" => Flight::request()->data->cantidad,
            ":id" => $id
        )
    );
    if ($stmt->rowCount() > 0) {
        echo "Updated order detail with id $id";
    } else {
        echo "Could not update order detail with id $id";
    }
});

Flight::start();

?>