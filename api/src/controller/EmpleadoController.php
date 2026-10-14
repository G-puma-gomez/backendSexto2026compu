<?php
require_once "../src/models/empleados.php";

class EmpleadoController
{
    public function getAll()
    {
        echo json_encode(empleados::all());
    }

    public function add()
    {
        $data = ApiResponse::input(['ci', 'nombre', 'apellidos', 'salario']);
        if ($data === null) return;
        $id = empleados::add($data);
        echo json_encode(['estado' => (bool) $id, 'message' => $id ? 'empleado agregado correctamente' : 'no se pudo agregar el empleado', 'id' => $id ? (int) $id : null]);
    }

    public function update($id)
    {
        $data = ApiResponse::input(['ci', 'nombre', 'apellidos', 'salario']);
        if ($data === null) return;
        $updated = empleados::update($id, $data);
        echo json_encode(['estado' => (bool) $updated, 'message' => $updated ? 'empleado actualizado correctamente' : 'no se pudo actualizar el empleado']);
    }

    public function delete($id)
    {
        $deleted = empleados::delete($id);
        echo json_encode(['estado' => (bool) $deleted, 'message' => $deleted ? 'empleado eliminado correctamente' : 'no se pudo eliminar el empleado']);
    }
}
