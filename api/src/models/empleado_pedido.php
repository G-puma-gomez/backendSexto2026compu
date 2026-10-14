<?php
include_once __DIR__ . "/../config/conexionDB.php";

class empleado_pedido
{
    private const FIELDS = ['cod_pedido', 'cod_empleado', 'rol_en_pedido', 'fecha_accion'];

    public static function all()
    {
        return ConexionPDO::query("SELECT * FROM empleado_pedido");
    }

    public static function add(array $data)
    {
        $fields = array_values(array_intersect(self::FIELDS, array_keys($data)));
        $values = [];
        foreach ($fields as $field) $values[":$field"] = $data[$field];
        $sql = "INSERT INTO empleado_pedido (" . implode(',', $fields) . ") VALUES (" . implode(',', array_map(fn($field) => ":$field", $fields)) . ")";
        return ConexionPDO::execute($sql, $values, true);
    }

    public static function update($id, array $data)
    {
        $fields = array_values(array_intersect(self::FIELDS, array_keys($data)));
        if ($fields === []) return false;
        $sets = array_map(fn($field) => "$field=:$field", $fields);
        $values = [];
        foreach ($fields as $field) $values[":$field"] = $data[$field];
        $values[':id'] = $id;
        return ConexionPDO::execute("UPDATE empleado_pedido SET " . implode(',', $sets) . " WHERE id=:id", $values, false);
    }

    public static function delete($id)
    {
        return ConexionPDO::execute("DELETE FROM empleado_pedido WHERE id=:id", [':id' => $id], false);
    }
}
