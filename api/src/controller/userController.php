<?php
require_once "../src/models/users.php";
class userController{
    public function getAll()
    {
        $usuarios=usuarios::all();
        echo json_encode($usuarios);
    }
}