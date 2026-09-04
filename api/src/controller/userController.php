<?php
require_once "../src/models/users.php";
class userController{
    public function getAll()
    {
        $usuarios=usuarios::all();
        echo json_encode($usuarios);
    }

 // actualizar usuarios
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
                        "message"=>"el código del cliente es obligatorio",
                    ]);
                return;
            }
            if(!isset($data['email']) || trim($data['email'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el email es obligatorio",
                    ]);
                return;
            }
        if(!isset($data['password_hash']) || trim($data['password_hash'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la contraseña es obligatoria",
                    ]);
                return;
            }
             if(!isset($data['estado_cuenta']) || trim($data['estado_cuenta'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"el estado de la cuenta es obligatorio",
                    ]);
                return;
            }
             if(!isset($data['ultimo_acceso']) || trim($data['ultimo_acceso'])=="")
                  {
                echo json_encode(
                    [
                        "status"=>"error",
                        "message"=>"la fecha del último acceso es obligatoria",
                    ]);
                return;
            }
    
        $usuario=usuarios::update($id,$data);

        if($usuario)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"usuario actualizado correctamente"
            ]);
        return;
        
    }

    echo json_encode($usuario);
     }
      //adicionar usuario
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
            "cod_cliente",
            "email",
            "password_hash",
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

        $datosUsuario=[];
        foreach($camposObligatorios as $campo)
            {
                $datosUsuario[$campo]=$data[$campo];
            }

        $conflicto = usuarios::findConflict($datosUsuario['cod_cliente'], $datosUsuario['email']);
        if ($conflicto) {
            http_response_code(409);
            $campo = (string)$conflicto['cod_cliente'] === (string)$datosUsuario['cod_cliente']
                ? 'cod_cliente'
                : 'email';
            echo json_encode([
                'estado' => false,
                'status' => 'error',
                'message' => $campo === 'cod_cliente'
                    ? 'El cliente ya tiene un usuario registrado.'
                    : 'El correo electrónico ya está registrado.',
                'campo' => $campo,
            ]);
            return;
        }

        try {
            $usuario = usuarios::add($datosUsuario);
        } catch (\PDOException $error) {
            // Mantiene una respuesta útil incluso si dos solicitudes se procesan a la vez.
            if ((int)($error->errorInfo[1] ?? 0) === 1062) {
                http_response_code(409);
                echo json_encode([
                    'estado' => false,
                    'status' => 'error',
                    'message' => 'El cliente o el correo electrónico ya está registrado.',
                ]);
                return;
            }
            throw $error;
        }

        if($usuario)
        {
            echo json_encode(
                [
                "estado" => true,
                "message" => "usuario agregado correctamente "
            ]);
        return;
        
    }

    echo json_encode($usuario);
     }
    // eliminar usuario
    public function delete($id)
    {
        $usuario=usuarios::delete($id);

        if($usuario)
        {
            echo json_encode(
                [
                "estado"=>true,
                "message"=>"usuario eliminado correctamente"
            ]);
        return;
        
    }

    echo json_encode(
        [
            "estado"=>false,
            "message"=>"no se pudo eliminar el usuario"
        ]);
     }
   }
