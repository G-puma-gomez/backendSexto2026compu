<?php
require_once "../src/models/productos.php";
class ProductoController{
    public function getAll()
    {
        $producto=productos::all();
        echo json_encode($producto);
    }
    public function update()
    {
        $jsonData=file_get_contents('php://input');
        die($jsonData);
        $producto=productos::update();
        echo json_encode($producto);
    }
}