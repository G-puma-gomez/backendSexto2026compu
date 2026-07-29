<?php
include_once __DIR__."/../config/conexionDB.php";
class usuarios
{
    public static function all(){
        $sql="SELECT * FROM usuarios";
        return ConexionPDO::query($sql);
        //self::$usuarios;
    }
}