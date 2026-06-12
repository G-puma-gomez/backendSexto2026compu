<?php
require_once "../src/models/proveedores.php";
class ProveedorController{
    public function getAll()
    {
        $proveedor=proveedores::all();
        echo json_encode($proveedor);
    }
}