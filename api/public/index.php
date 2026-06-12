<?php
if($_SERVER['REQUEST_METHOD']=='OPTIONS')
    {
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
    $route->add('GET','/','userController@getAll');
    $route->add('GET','/usuarios','userController@getAll');
// direccion de productos
    $route->add('GET','/productos','ProductoController@getAll');
    $route->add('POST','/productos','ProductoController@add');
    $route->add('PUT','/productos','ProductoController@update');
// direccion de proveedor
    $route->add('GET','/proveedores','ProveedorController@getAll'); 
// direccion de clientes
    $route->add('GET','/clientes','ClienteController@getAll');
// direccion de pedidos
    $route->add('GET','/pedido','PedidoController@getAll');
// direccion de proveedor_productos
    $route->add('GET','/proveedor_producto','ProveedorProductoController@getAll');
// direccion de pedido_productos
    $route->add('GET','/pedido_producto','PedidoProductoController@getAll');
    $route->run();