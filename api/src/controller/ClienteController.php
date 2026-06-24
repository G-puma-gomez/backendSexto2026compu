<?php
require_once "../src/models/clientes.php";
class ClienteController{
    public function getAll()
    {
        $cliente=clientes::all();
        echo json_encode($cliente);
    }
    // actualizar cliente
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
        if(!isset($data['ci']) || trim($data['ci'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el ci es obligatorio",
                    ]);
                return;
            }
            if(!isset($data['nombre']) || trim($data['nombre'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el nombre es obligatorio",
                    ]);
                return;
            }
        if(!isset($data['apellidos']) || trim($data['apellidos'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el apellidos es obligatorio",
                    ]);
                return;
            }
        if(!isset($data['direccion']) || trim($data['direccion'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la direccion es obligatoria",
                    ]);
                return;
            }
        if(!isset($data['telefono']) || trim($data['telefono'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el telefono es obligatorio",
                    ]);
                return;
            }
    
        $cliente=clientes::update($id,$data);

        if($cliente)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"cliente actualizado correctamente"
            ]);
        return;
        
    }

    echo json_encode($cliente);
     }
      //adicionar cliente
    public function add()
    {
        $jsonData=file_get_contents('php://input');
       $data= json_decode($jsonData,true);
        $cliente=clientes::add($data);

        if($cliente)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"cliente agregado correctamente"
            ]);
        return;
        
    }

    echo json_encode($cliente);
     }  

    

}