<?php
include_once __DIR__."/../config/conexionDB.php";
class pedido
{
    public static function all()
    {
        $sql="SELECT * FROM pedido";
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
        $sql="UPDATE pedido SET $stringCampos WHERE id=:id";
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
        $sql="INSERT INTO pedido ($stringCampos) VALUES ($valores)";
        $result=ConexionPDO::execute($sql, $valores,true);
        return $sql;}
    public static function delete($id)
    {
        $sql = "DELETE FROM pedido WHERE id=:id";
        return ConexionPDO::execute($sql, [':id' => $id], false);
    }
     
}
