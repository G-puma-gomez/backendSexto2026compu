<?php
include_once __DIR__."/../config/conexionDB.php";
class users
{
    public static function all(){
        $sql="SELECT * FROM users";
        return ConexionPDO::query($sql);
        //self::$usuarios;
    }
}