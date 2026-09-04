<?php
include_once __DIR__."/../config/conexionDB.php";
class usuarios
{
    public static function all(){
        $sql="SELECT * FROM usuarios";
        return ConexionPDO::query($sql);
        //self::$usuarios;
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
        $sql="UPDATE usuarios SET $stringCampos WHERE id=:id";
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
        $sql="INSERT INTO usuarios ($stringCampos) VALUES ($stringPlaceholders)";
        $result=ConexionPDO::execute($sql, $valores,true);
        return $result;
     }
    public static function findConflict($codCliente, $email)
    {
        $sql = "SELECT cod_cliente, email FROM usuarios
                WHERE cod_cliente = :cod_cliente OR email = :email
                LIMIT 1";
        $usuarios = ConexionPDO::query($sql, [
            ':cod_cliente' => $codCliente,
            ':email' => $email,
        ]);

        return $usuarios[0] ?? null;
    }
    public static function delete($id)
    {
        $sql="DELETE FROM usuarios WHERE id=:id";
        $valores=[':id'=>$id];
        $result=ConexionPDO::execute($sql, $valores,false);
        return $result;
     }
     
}
