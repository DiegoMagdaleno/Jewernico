import { Option, Argument } from "./models.js";

export const OPTIONS = {
    0: [
        new Option("Clientes", "users", []),
        new Option("Proveedores", "suppliers", []),
        new Option("Categorias", "categories", []),
        new Option("Compras", "orders", []),
        new Option("Contiene", "orderdetails", []),
        new Option("DisponibleEn", "availibility", []),
        new Option("Encargados", "managers", []),
        new Option("Materiales", "materials", []),
        new Option("Productos", "products", []),
        new Option("Sucursales", "locations", []),
        new Option("Telefonos", "phones", []),
    ],
    1: [
        new Option("Usuarios y sus telefonos", "users_and_phones", []),
        new Option("Compras en un dia determinado", "orders", [new Argument("Fecha", "fecha", "date")]),
        new Option("Compras en cierta ubicacion", "orders_at", [new Argument("Ubicacion", "ubicacion", "varchar")]),
        new Option("Usuarios que no han comprado", "no_purchases", []),
        new Option("Productos y su stock disponible en cada ubicacion", "availability_at_every_location", []),
    ],
    2: [
        new Option("Consultar clientes que han comprado en varias sucrusales", "purchased_at_multiple", []),
        new Option("Consultar todos los productos, su categoria y stock", "products_categories_and_stock", []),
        new Option("Usuarios y total de compras realizadas", "users_and_order_count", []),
        new Option("Usuarios que han realizado alguna compra", "users_that_have_ordered", []),
        new Option("Productos que nunca se han comprado", "products_never_ordered", []),
    ],
    3: [
        new Option("Cantidad promedio de compras por cada usuario y solo aquellos cuya cantidad sea menor a un cierto valor", "average_orders_per_user", [new Argument("Cantidad", "cantidad", "int")]),
        new Option("Producto con un stock promedio mayor a cierto valor", "product_with_stock_more_than_at", [new Argument("Cantidad", "cantidad", "int"), new Argument("IdProducto", "id_producto", "int")]),
        new Option("Productos con stock mayor a cierto valor", "products_with_more_than_n", [new Argument("Cantidad", "cantidad", "int")]),
        new Option("Productos con precio mayor a cierto valor", "products_with_price_more_than", [new Argument("Precio", "precio", "int")]),
        new Option("Usuarios con mas de un telefono", "users_with_more_than_one_phone", []),
    ],
    4: [],
}
