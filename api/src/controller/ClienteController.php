<?php
require_once "../src/models/clientes.php";
class ClienteController{
    public function getAll()
    {
        $cliente=clientes::all();
        echo json_encode($cliente);
    }
}