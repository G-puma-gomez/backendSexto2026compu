<?php
require_once "../src/models/pedido.php";
class PedidoController{
    public function getAll()
    {
        $pedido=pedido::all();
        echo json_encode($pedido);
    }
}