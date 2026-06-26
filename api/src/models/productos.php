<?php
include_once __DIR__."/../config/conexionDB.php";
class productos
{
    public static function all()
    {
        $sql="SELECT * FROM productos";
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
        $sql="UPDATE productos SET $stringCampos WHERE id=:id";
        $valores[':id']=$id;
        $result=ConexionPDO::execute($sql, $valores,false);

        return $result;
    }
    public static function add($data)
    {
        if(isset($data['id']))
        {
           unset($data['id']);
        }
        $campos=[];
        $valores=[];
        $placeholders=[];
        // construir datos
        foreach($data as $columna=>$valor)
            {
                $campos[]=$columna;
                $placeholders[]=":$columna";
                $valores[":$columna"] = $valor;

            }
        $stringCampos=implode(",",$campos);
        $stringPlaceholders=implode(",",$placeholders);
        // preparamos la consulta 
        $sql="INSERT INTO productos ($stringCampos) VALUES ($stringPlaceholders)";
        $result=ConexionPDO::execute($sql, $valores,true);
        return $result;
     }
    public static function delete($id)
    {
        $sql="DELETE FROM productos WHERE id=:id";
        $valores=[':id'=>$id];
        $result=ConexionPDO::execute($sql, $valores,false);
        return $result;
     }
     
}
