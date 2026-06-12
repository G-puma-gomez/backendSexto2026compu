<?php
require_once "../src/models/usuarios.php";
class userController{
    public function getAll()
    {
        $usuarios=usuarios::all();
        echo json_encode($usuarios);
    }
}