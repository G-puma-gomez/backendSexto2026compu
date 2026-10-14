<?php
include_once __DIR__ . "/../config/conexionDB.php";

class empleados
{
    private const FIELDS = ['ci', 'nombre', 'apellidos', 'cargo', 'salario', 'fecha_ingreso'];

    public static function all()
    {
        return ConexionPDO::query("SELECT * FROM empleados");
    }

    public static function add(array $data)
    {
        $fields = array_values(array_intersect(self::FIELDS, array_keys($data)));
        $values = [];
        foreach ($fields as $field) {
            $values[":$field"] = $data[$field];
        }
        $sql = "INSERT INTO empleados (" . implode(',', $fields) . ") VALUES (" . implode(',', array_map(fn($field) => ":$field", $fields)) . ")";
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
        return ConexionPDO::execute("UPDATE empleados SET " . implode(',', $sets) . " WHERE id=:id", $values, false);
    }

    public static function delete($id)
    {
        return ConexionPDO::execute("DELETE FROM empleados WHERE id=:id", [':id' => $id], false);
    }
}
