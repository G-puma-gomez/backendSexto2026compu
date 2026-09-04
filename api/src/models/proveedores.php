<?php
include_once __DIR__."/../config/conexionDB.php";
class proveedores
{
    public static function all()
    {
        $sql="SELECT * FROM proveedores";
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
        $sql="UPDATE proveedores SET $stringCampos WHERE id=:id";
        $valores[':id']=$id;
        $result=ConexionPDO::execute($sql, $valores,false);

        return $result;
    }
    public static function add($data)
    {
        $campos = ['ci', 'nombre', 'apellidos'];
        $placeholders = array_map(fn($campo) => ":$campo", $campos);
        $valores = [];
        foreach ($campos as $campo) {
            $valores[":$campo"] = $data[$campo];
        }

        $sql = "INSERT INTO proveedores (" . implode(',', $campos) . ") VALUES (" . implode(',', $placeholders) . ")";
        return ConexionPDO::execute($sql, $valores, true);
    }
    public static function delete($id)
    {
        $db = ConexionPDO::connect();

        try {
            $db->beginTransaction();

            // Las filas de esta tabla solo representan la asociación entre
            // proveedor y producto; los productos no deben eliminarse.
            $deleteRelations = $db->prepare(
                "DELETE FROM proveedor_producto WHERE cod_proveedor = :id"
            );
            $deleteRelations->execute([':id' => $id]);

            $deleteProveedor = $db->prepare(
                "DELETE FROM proveedores WHERE id = :id"
            );
            $deleteProveedor->execute([':id' => $id]);

            $deleted = $deleteProveedor->rowCount() > 0;
            $db->commit();

            return $deleted;
        } catch (\Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $error;
        }
    }
     
}
