<?php
require_once "../src/models/pedido.php";
class PedidoController{
    public function getAll()
    {
        $pedido=pedido::all();
        echo json_encode($pedido);
    }
    // actualizar pedido
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
        if(!isset($data['cod_cliente']) || trim($data['cod_cliente'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el cod_cliente es obligatorio",
                    ]);
                return;
            }
            if(!isset($data['fecha']) || trim($data['fecha'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la fecha es obligatoria",
                    ]);
                return;
            }
        if(!isset($data['estado']) || trim($data['estado'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el estado es obligatorio",
                    ]);
                return;
            }
    
        $pedido=pedido::update($id,$data);

        if($pedido)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"pedido actualizado correctamente"
            ]);
        return;
        
    }

    echo json_encode($pedido);
     }
      //adicionar pedido
      //adicionar producto
    public function add()
    {
        $jsonData=file_get_contents('php://input');
       $data= json_decode($jsonData,true);
        $pedido=pedido::add($data);

        if($pedido)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"pedido agregado correctamente"
            ]);
        return;
        
    }

    echo json_encode($pedido);
     }

    public function delete($id)
    {
        $pedido = pedido::delete($id);
        echo json_encode([
            "estado" => (bool)$pedido,
            "message" => $pedido ? "pedido eliminado correctamente" : "no se pudo eliminar el pedido"
        ]);
    }
}

