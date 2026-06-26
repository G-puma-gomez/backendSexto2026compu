<?php
require_once "../src/models/productos.php";
class ProductoController
{
    public function getAll()
    {
        $producto=productos::all();
        echo json_encode($producto);
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
        if(!isset($data['codbarras']) || trim($data['codbarras'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el codbarras es obligatorio",
                    ]);
                return;
            }
            if(!isset($data['descripcion']) || trim($data['descripcion'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la descripcion es obligatorio",
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
        if(!isset($data['stock']) || trim($data['stock'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el stock es obligatorio", 
                    ]); 
                return;
            }
    
        $producto=productos::update($id,$data);

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
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        if(json_last_error()!=JSON_ERROR_NONE)
            {
                echo json_encode(
                    [
                        "status"=>"error codificacion",
                        "message"=>json_last_error_msg(),
                    ]);
                return;
            }
        $camposObligatorios=[
            "codbarras",
            "descripcion",
            "precio_unitario",
            "stock",
        ];
        $camposFaltantes=[];

        foreach($camposObligatorios as $campo)
            {
                if(!isset($data[$campo]) || trim((string)$data[$campo])=="")
                    {
                        $camposFaltantes[]=$campo;
                    }
            }

        if(count($camposFaltantes)>0)
            {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"debe llenar los datos que faltan",
                        "campos_faltantes"=>$camposFaltantes,
                    ]);
                return;
            }

        $datosProducto=[];
        foreach($camposObligatorios as $campo)
            {
                $datosProducto[$campo]=$data[$campo];
            }

        $producto = productos::add($datosProducto);

        if($producto)
        {
            echo json_encode(
                [
                "estado" => true,
                "message" => "producto agregado correctamente "
            ]);
        return;
        
    }

    echo json_encode($producto);
     }
    // eliminar producto
    public function delete($id)
    {
        $producto=productos::delete($id);

        if($producto)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"producto eliminado correctamente"
            ]);
        return;
        
    }

    echo json_encode(
        [
            "estado"=>false,
            "message"=>"no se pudo eliminar el producto"
        ]);
     }
   }

    
