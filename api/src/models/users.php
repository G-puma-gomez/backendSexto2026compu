<?php
include_once __DIR__ . '/../config/conexionDB.php';

class usuarios
{
    public static function all(): array
    {
        $columns = ConexionPDO::connect()->query('SHOW COLUMNS FROM empleados')->fetchAll(PDO::FETCH_COLUMN, 0);
        $roleColumn = in_array('permiso', $columns, true) ? 'permiso' : 'cargo';
        return ConexionPDO::query("SELECT u.id, u.cod_empleado, u.email, u.estado_cuenta, u.ultimo_acceso,
            e.nombre, e.apellidos, e.`$roleColumn` AS permiso
            FROM usuarios u LEFT JOIN empleados e ON e.id=u.cod_empleado");
    }

    public static function findConflict($codEmpleado, $email, $exceptId = null): ?array
    {
        $sql = 'SELECT id, cod_empleado, email FROM usuarios WHERE (cod_empleado=:cod_empleado OR email=:email)';
        $params = [':cod_empleado' => $codEmpleado, ':email' => $email];
        if ($exceptId !== null) {
            $sql .= ' AND id<>:id';
            $params[':id'] = $exceptId;
        }
        $sql .= ' LIMIT 1';
        $rows = ConexionPDO::query($sql, $params);
        return $rows[0] ?? null;
    }

    public static function add(array $data)
    {
        $db = ConexionPDO::connect();
        $fields = ['cod_empleado', 'email', 'password_hash', 'estado_cuenta'];
        $values = [
            ':cod_empleado' => $data['cod_empleado'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':estado_cuenta' => $data['estado_cuenta'] ?? 'Activo',
        ];
        $stmt = $db->prepare('INSERT INTO usuarios (' . implode(',', $fields) . ') VALUES (:cod_empleado,:email,:password_hash,:estado_cuenta)');
        $stmt->execute($values);
        return (int)$db->lastInsertId();
    }

    public static function update($id, array $data): bool
    {
        $allowed = ['cod_empleado', 'email', 'password_hash', 'estado_cuenta'];
        $values = array_intersect_key($data, array_flip($allowed));
        if ($values === []) return false;
        $sets = [];
        $params = [':id' => $id];
        foreach ($values as $field => $value) {
            if ($field === 'password_hash' && $value === '') continue;
            $sets[] = "$field=:$field";
            $params[":$field"] = $value;
        }
        if ($sets === []) return true;
        return ConexionPDO::execute('UPDATE usuarios SET ' . implode(',', $sets) . ' WHERE id=:id', $params, false);
    }

    public static function delete($id)
    {
        return ConexionPDO::execute('DELETE FROM usuarios WHERE id=:id', [':id' => $id], false);
    }
}
