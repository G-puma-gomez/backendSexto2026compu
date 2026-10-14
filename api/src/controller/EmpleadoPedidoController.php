<?php
require_once "../src/models/empleado_pedido.php";

class EmpleadoPedidoController
{
    public function getAll()
    {
        echo json_encode(empleado_pedido::all());
    }

    public function add()
    {
        $data = ApiResponse::input(['cod_pedido', 'cod_empleado']);
        if ($data === null) return;
        $id = empleado_pedido::add($data);
        echo json_encode(['estado' => (bool) $id, 'message' => $id ? 'relacion empleado_pedido agregada correctamente' : 'no se pudo agregar la relacion empleado_pedido', 'id' => $id ? (int) $id : null]);
    }

    public function update($id)
    {
        $data = ApiResponse::input(['cod_pedido', 'cod_empleado']);
        if ($data === null) return;
        $updated = empleado_pedido::update($id, $data);
        echo json_encode(['estado' => (bool) $updated, 'message' => $updated ? 'relacion empleado_pedido actualizada correctamente' : 'no se pudo actualizar la relacion empleado_pedido']);
    }

    public function delete($id)
    {
        $deleted = empleado_pedido::delete($id);
        echo json_encode(['estado' => (bool) $deleted, 'message' => $deleted ? 'relacion empleado_pedido eliminada correctamente' : 'no se pudo eliminar la relacion empleado_pedido']);
    }
}
