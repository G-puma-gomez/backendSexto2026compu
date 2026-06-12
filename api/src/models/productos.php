<?php
include_once __DIR__."/../config/conexionDB.php";
class productos
{
    public static function all(){
        $sql="SELECT * FROM productos";
        return ConexionPDO::query($sql);//self::$users;
    }
}