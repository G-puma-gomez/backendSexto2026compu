<?php
require_once "../src/models/productos.php";
class ProductoController{
    public function getAll()
    {
        $producto=productos::all();
        echo json_encode($producto);
    }
}