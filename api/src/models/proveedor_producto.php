<?php
include_once __DIR__."/../config/conexionDB.php";
class proveedor_producto
{
    public static function all()
    {
        $sql="SELECT * FROM proveedor_producto";
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
        $sql="UPDATE proveedor_producto SET $stringCampos WHERE id=:id";
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
        $sql="INSERT INTO proveedor_producto ($stringCampos) VALUES ($valores)";
        $result=ConexionPDO::execute($sql, $valores,true);
        return $sql;}
    public static function delete($id)
    {
        $sql = "DELETE FROM proveedor_producto WHERE id=:id";
        return ConexionPDO::execute($sql, [':id' => $id], false);
    }
     
}
