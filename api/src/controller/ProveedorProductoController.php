
<?php
require_once "../src/models/proveedor_producto.php";
class ProveedorProductoController{
    public function getAll()
    {
        $proveedor_producto=proveedor_producto::all();
        echo json_encode($proveedor_producto);
    }
    // actualizar proveedor_producto
    public function update($id)
    {
        $jsonData=file_get_contents('php://input');
       $data= json_decode($jsonData,true);
         if(json_last_error()!=JSON_ERROR_NONE)
                    {
                echo json_encode(
                    [
                        "status"=>"error codificacion",
                        "message"=>json_last_error_msg(),
                    ]);
                return;
            }
        if(!isset($data['cod_proveedor']) || trim($data['cod_proveedor'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el cod_proveedor es obligatorio",
                    ]);
                return;
            }
            if(!isset($data['cod_producto']) || trim($data['cod_producto'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el cod_producto es obligatorio",
                    ]);
                return;
            }
        $proveedor_producto=proveedor_producto::update($id,$data);

        if($proveedor_producto)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"proveedor_producto actualizado correctamente"
            ]);
        return;
        
    }

    echo json_encode($proveedor_producto);
     }
      //adicionar proveedor_producto
    public function add()
    {
        $jsonData=file_get_contents('php://input');
       $data= json_decode($jsonData,true);
        $proveedor_producto=proveedor_producto::add($data);

        if($proveedor_producto)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"proveedor_producto agregado correctamente"
            ]);
        return;
        
    }

    echo json_encode($proveedor_producto);
     }

    public function delete($id)
    {
        $proveedorProducto = proveedor_producto::delete($id);
        echo json_encode([
            "estado" => (bool)$proveedorProducto,
            "message" => $proveedorProducto ? "proveedor_producto eliminado correctamente" : "no se pudo eliminar el proveedor_producto"
        ]);
    }
   }
