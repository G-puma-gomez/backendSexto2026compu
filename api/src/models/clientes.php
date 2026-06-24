<?php
include_once __DIR__."/../config/conexionDB.php";
class clientes
{
   public static function all()
    {
        $sql="SELECT * FROM clientes";
        return ConexionPDO::query($sql);
    }    
    public static function update($id,$data)
    {
       if(isset($data['id']))
        {
           unset($data['id']);
        }
        $campos=[];
        $valores=[];
        // construir datos
        foreach($data as $columna=>$valor)
            {
                $campos[]="$columna=:$columna";
                $valores[":$columna"] = $valor;

            }
             
        $stringCampos=implode(",",$campos);
        // preparamos la consulta 
        $sql="UPDATE clientes SET $stringCampos WHERE id=:id";
        $valores[':id']=$id;
        $result=ConexionPDO::execute($sql, $valores,false);

        return $result;
    }
    public static function add($data)
    {
        $campos=[];
        $valores=[];
        // construir datos
        foreach($data as $columna=>$valor)
            {
                $campos[]="$columna=:$columna";
                $valores[":$columna"] = $valor;

            }
        $stringCampos=implode(",",$campos);
        die($stringCampos);
        // preparamos la consulta 
        $sql="INSERT INTO clientes ($stringCampos) VALUES ($valores)";
        $result=ConexionPDO::execute($sql, $valores,true);
        return $sql;}
     
}