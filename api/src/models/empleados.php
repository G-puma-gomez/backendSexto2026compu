<?php
include_once __DIR__ . "/../config/conexionDB.php";

class empleados
{
    private const FIELDS = ['ci', 'nombre', 'apellidos', 'permiso', 'salario', 'fecha_ingreso'];

    public static function all()
    {
        return ConexionPDO::query("SELECT e.id, e.ci, e.nombre, e.apellidos, e.permiso, e.salario, e.fecha_ingreso, u.email
            FROM empleados e LEFT JOIN usuarios u ON u.cod_empleado = e.id");
    }

    public static function hasAccount($id): bool
    {
        $rows = ConexionPDO::query('SELECT id FROM usuarios WHERE cod_empleado=:id LIMIT 1', [':id' => $id]);
        return !empty($rows[0]['id']);
    }

    public static function add(array $data)
    {
        $db = ConexionPDO::connect();
        $db->beginTransaction();
        try {
            $fields = array_values(array_intersect(self::FIELDS, array_keys($data)));
            $stmt = $db->prepare('INSERT INTO empleados (' . implode(',', $fields) . ') VALUES (:' . implode(',:', $fields) . ')');
            $stmt->execute(array_combine(array_map(fn($field) => ":$field", $fields), array_map(fn($field) => $data[$field], $fields)));
            $id = (int)$db->lastInsertId();
            $account = $db->prepare("INSERT INTO usuarios (cod_empleado,email,password_hash,estado_cuenta) VALUES (:id,:email,:password_hash,'Activo')");
            $account->execute([':id' => $id, ':email' => $data['email'], ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)]);
            $db->commit();
            return $id;
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }

    public static function update($id, array $data)
    {
        $db = ConexionPDO::connect();
        $db->beginTransaction();
        try {
            $fields = array_values(array_intersect(self::FIELDS, array_keys($data)));
            $sets = array_map(fn($field) => "$field=:$field", $fields);
            if ($sets) {
                $stmt = $db->prepare('UPDATE empleados SET ' . implode(',', $sets) . ' WHERE id=:id');
                $values = [];
                foreach ($fields as $field) $values[":$field"] = $data[$field];
                $values[':id'] = $id;
                $stmt->execute($values);
            }
            $stmt = $db->prepare('SELECT id FROM usuarios WHERE cod_empleado=:id');
            $stmt->execute([':id' => $id]);
            $hasAccount = (bool)$stmt->fetchColumn();
            if (!$hasAccount && empty($data['password'])) {
                throw new InvalidArgumentException('Este empleado todavía no tiene cuenta; define una contraseña.');
            }
            if ($hasAccount) {
                $accountFields = ['email=:email'];
                $accountValues = [':email' => $data['email'], ':id' => $id];
                if (!empty($data['password'])) {
                    $accountFields[] = 'password_hash=:password_hash';
                    $accountValues[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                $stmt = $db->prepare('UPDATE usuarios SET ' . implode(',', $accountFields) . ' WHERE cod_empleado=:id');
                $stmt->execute($accountValues);
            } else {
                $stmt = $db->prepare("INSERT INTO usuarios (cod_empleado,email,password_hash,estado_cuenta) VALUES (:id,:email,:hash,'Activo')");
                $stmt->execute([':id' => $id, ':email' => $data['email'], ':hash' => password_hash($data['password'], PASSWORD_DEFAULT)]);
            }
            $db->commit();
            return true;
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }

    public static function delete($id)
    {
        return ConexionPDO::execute("DELETE FROM empleados WHERE id=:id", [':id' => $id], false);
    }
}
