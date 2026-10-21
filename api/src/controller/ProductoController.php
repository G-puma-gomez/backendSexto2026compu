<?php
require_once "../src/models/productos.php";
class ProductoController
{
    public function uploadImage()
    {
        if (!AdminAuth::requireAdmin()) return;
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['estado' => false, 'message' => 'Selecciona una imagen válida.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $file = $_FILES['image'];
        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['estado' => false, 'message' => 'La imagen no puede superar 5 MB.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!isset($extensions[$mime])) {
            http_response_code(400);
            echo json_encode(['estado' => false, 'message' => 'Usa una imagen JPG, PNG, WEBP o GIF.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $directory = __DIR__ . '/../../public/uploads';
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            http_response_code(500);
            echo json_encode(['estado' => false, 'message' => 'No se pudo preparar la carpeta de imágenes.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            http_response_code(500);
            echo json_encode(['estado' => false, 'message' => 'No se pudo guardar la imagen.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $apiPrefix = strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/api/') === 0 ? '/api' : '';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        echo json_encode(['estado' => true, 'url' => $scheme . '://' . $_SERVER['HTTP_HOST'] . $apiPrefix . '/uploads/' . $filename], JSON_UNESCAPED_UNICODE);
    }

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

        if (array_key_exists('image_url', $data) && $this->hasInvalidImageUrl($data['image_url'])) {
            http_response_code(400);
            echo json_encode(['estado' => false, 'message' => 'La imagen debe tener una URL válida con protocolo HTTP o HTTPS.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (array_key_exists('image_url', $data)) {
            $data['image_url'] = trim((string)$data['image_url']) ?: null;
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

        if ($this->hasInvalidImageUrl($data['image_url'] ?? null)) {
            http_response_code(400);
            echo json_encode(['estado' => false, 'message' => 'La imagen debe tener una URL válida con protocolo HTTP o HTTPS.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $datosProducto['image_url'] = trim((string)($data['image_url'] ?? '')) ?: null;

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

    private function hasInvalidImageUrl($value): bool
    {
        $url = trim((string)$value);
        if ($url === '') return false;
        if (filter_var($url, FILTER_VALIDATE_URL) === false) return true;
        return !in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }
   }

    
