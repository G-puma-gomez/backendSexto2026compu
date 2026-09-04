<?php
require_once "../src/models/proveedores.php";
class ProveedorController{
   public function getAll()
    {
        $proveedor=proveedores::all();
        echo json_encode($proveedor);
    }
    // actualizar proveedor
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
    
        $proveedor=proveedores::update($id,$data);

        if($proveedor)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"proveedor actualizado correctamente"
            ]); 
        return;
        
    }

    echo json_encode($proveedor);
     }
      //adicionar proveedor
    public function add()
    {
        $data = ApiResponse::input(['ci', 'nombre', 'apellidos']);
        if ($data === null) {
            return;
        }
        $proveedor=proveedores::add($data);

        if($proveedor)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"proveedor agregado correctamente",
                "id"=>(int)$proveedor
            ]);

        return;
        
        }

    echo json_encode($proveedor);
     }

    public function delete($id)
    {
        try {
            $proveedor = proveedores::delete($id);
        } catch (\PDOException $error) {
            http_response_code(409);
            echo json_encode([
                "estado" => false,
                "message" => "No se puede eliminar el proveedor porque tiene registros relacionados"
            ]);
            return;
        }

        echo json_encode([
            "estado" => (bool)$proveedor,
            "message" => $proveedor ? "proveedor eliminado correctamente" : "no se pudo eliminar el proveedor"
        ]);
    }
}

    
