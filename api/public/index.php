<?php
header('Access-Control-Allow-Origin: http://www.2026compra.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

require_once "../src/config/ApiResponse.php";

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
    require_once "../src/router.php";
    require_once "../src/controller/userController.php";
    require_once "../src/controller/ProductoController.php";
    require_once "../src/controller/ProveedorController.php";
    require_once "../src/controller/ClienteController.php";
    require_once "../src/controller/PedidoController.php";
    require_once "../src/controller/ProveedorProductoController.php";
    require_once "../src/controller/PedidoProductoController.php";
    use App\router;

    $route=new router();
// direccion para usuario
    $route->add('GET','/','userController@login');
    $route->add('GET','/usuarios','userController@getAll');
    $route->add('POST','/usuarios','userController@add');
    $route->add('PUT','/usuarios/{id}','userController@update');
    $route->add('DELETE','/usuarios/{id}','userController@delete');
// direccion de productos
    $route->add('GET','/productos','ProductoController@getAll');
    $route->add('POST','/productos','ProductoController@add');
    $route->add('PUT','/productos/{id}','ProductoController@update');
    $route->add('DELETE','/productos/{id}','ProductoController@delete');
// direccion de proveedor
    $route->add('GET','/proveedores','ProveedorController@getAll'); 
    $route->add('POST','/proveedores','ProveedorController@add');
    $route->add('PUT','/proveedores/{id}','ProveedorController@update');
    $route->add('DELETE','/proveedores/{id}','ProveedorController@delete');
// direccion de clientes
    $route->add('GET','/clientes','ClienteController@getAll');
    $route->add('POST','/clientes','ClienteController@add');
    $route->add('PUT','/clientes/{id}','ClienteController@update');
    $route->add('DELETE','/clientes/{id}','ClienteController@delete');
// direccion de pedidos
    $route->add('GET','/pedido','PedidoController@getAll');
    $route->add('POST','/pedido','PedidoController@add');
    $route->add('PUT','/pedido/{id}','PedidoController@update');
    $route->add('DELETE','/pedido/{id}','PedidoController@delete');
// direccion de proveedor_productos
    $route->add('GET','/proveedor_producto','ProveedorProductoController@getAll');
    $route->add('POST','/proveedor_producto','ProveedorProductoController@add');
    $route->add('PUT','/proveedor_producto/{id}','ProveedorProductoController@update');
    $route->add('DELETE','/proveedor_producto/{id}','ProveedorProductoController@delete');
// direccion de pedido_productos
    $route->add('GET','/pedido_producto','PedidoProductoController@getAll');
    $route->add('POST','/pedido_producto','PedidoProductoController@add');
    $route->add('PUT','/pedido_producto/{id}','PedidoProductoController@update');
    $route->add('DELETE','/pedido_producto/{id}','PedidoProductoController@delete');
    try {
        $route->run();
    } catch (Throwable $error) {
        $status = $error instanceof PDOException && (string)$error->getCode() === '23000' ? 409 : 500;
        ApiResponse::error(
            $status === 409
                ? 'No se puede completar la operación porque existen registros relacionados o duplicados.'
                : 'Ocurrió un error al procesar la solicitud.',
            $status
        );
    }
