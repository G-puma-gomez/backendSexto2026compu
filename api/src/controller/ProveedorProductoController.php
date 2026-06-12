<?php
require_once "../src/models/proveedor_producto.php";
class ProveedorProductoController{
    public function getAll()
    {
        $proveedor_producto=proveedor_producto::all();
        echo json_encode($proveedor_producto);
    }
}