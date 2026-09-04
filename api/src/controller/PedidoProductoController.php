<?php
require_once "../src/models/pedido_producto.php";
class PedidoProductoController{
    public function getAll()
    {
        $pedido_producto=pedido_producto::all();
        echo json_encode($pedido_producto);
    }
    // actualizar producto
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
        if(!isset($data['cod_pedido']) || trim($data['cod_pedido'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el cod_pedido es obligatorio",
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
        if(!isset($data['cantidad']) || trim($data['cantidad'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la cantidad es obligatoria",
                    ]);
                return;
            }
        if(!isset($data['precio_unitario']) || trim($data['precio_unitario'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el precio_unitario es obligatorio",
                    ]);
                return;
            }
        if(!isset($data['descuento']) || trim($data['descuento'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el descuento es obligatorio",
                    ]);
                return;
            }
    
        $producto=pedido_producto::update($id,$data);

        if($producto)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"producto actualizado correctamente"
            ]);
        return;
        
    }

    echo json_encode($producto);
     }
      //adicionar producto
    public function add()
    {
        $data = ApiResponse::input(['cod_pedido', 'cod_producto', 'cantidad', 'precio_unitario', 'descuento']);
        if ($data === null) {
            return;
        }
        $producto=pedido_producto::add($data);

        if($producto)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"producto agregado correctamente",
                "id"=>(int)$producto
            ]);
        return;
        
    }

    echo json_encode($producto);
     }

    public function delete($id)
    {
        $producto = pedido_producto::delete($id);
        echo json_encode([
            "estado" => (bool)$producto,
            "message" => $producto ? "pedido_producto eliminado correctamente" : "no se pudo eliminar el pedido_producto"
        ]);
    }

}
