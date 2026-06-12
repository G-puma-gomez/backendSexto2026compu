<?php
require_once "../src/models/pedido_producto.php";
class PedidoProductoController{
    public function getAll()
    {
        $pedido_producto=pedido_producto::all();
        echo json_encode($pedido_producto);
    }
}