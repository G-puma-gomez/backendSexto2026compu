<?php
include_once __DIR__."/../config/conexionDB.php";
class pedido_producto
{
    public static function all(){
        $sql="SELECT * FROM pedido_producto";
        return ConexionPDO::query($sql);//self::$users;
    }
}