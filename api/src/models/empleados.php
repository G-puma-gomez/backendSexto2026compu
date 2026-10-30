<?php
include_once __DIR__ . '/../config/conexionDB.php';

class empleados
{
    private const FIELDS = ['ci', 'nombre', 'apellidos', 'permiso', 'salario', 'email', 'fecha_ingreso'];
    private static ?array $employeeColumns = null;
    private static ?array $userColumns = null;

    private static function columns(string $table): array
    {
        $property = $table === 'empleados' ? 'employeeColumns' : 'userColumns';
        if (self::${$property} === null) {
            $rows = ConexionPDO::connect()->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN, 0);
            self::${$property} = $rows;
        }
        return self::${$property};
    }

    private static function hasColumn(string $table, string $column): bool
    {
        return in_array($column, self::columns($table), true);
    }

    private static function usesEmployeeAccounts(): bool
    {
        return self::hasColumn('empleados', 'email') && self::hasColumn('empleados', 'password_hash');
    }

    public static function all(): array
    {
        if (self::usesEmployeeAccounts()) {
            return ConexionPDO::query('SELECT id, ci, nombre, apellidos, permiso, salario, fecha_ingreso, email FROM empleados');
        }

        $roleColumn = self::hasColumn('empleados', 'permiso') ? 'permiso' : 'cargo';
        $emailColumn = self::hasColumn('usuarios', 'email') ? 'u.email' : 'NULL';
        return ConexionPDO::query("SELECT e.id, e.ci, e.nombre, e.apellidos, e.`$roleColumn` AS permiso,
            e.salario, e.fecha_ingreso, $emailColumn AS email
            FROM empleados e LEFT JOIN usuarios u ON u.cod_empleado=e.id");
    }

    public static function hasPassword($id): bool
    {
        if (self::usesEmployeeAccounts()) {
            $rows = ConexionPDO::query("SELECT id FROM empleados WHERE id=:id AND password_hash IS NOT NULL AND password_hash<>'' LIMIT 1", [':id' => $id]);
        } else {
            $rows = ConexionPDO::query("SELECT id FROM usuarios WHERE cod_empleado=:id AND password_hash IS NOT NULL AND password_hash<>'' LIMIT 1", [':id' => $id]);
        }
        return !empty($rows[0]['id']);
    }

    public static function add(array $data)
    {
        $db = ConexionPDO::connect();
        $db->beginTransaction();
        try {
            $roleColumn = self::hasColumn('empleados', 'permiso') ? 'permiso' : 'cargo';
            $values = [
                'ci' => $data['ci'],
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                $roleColumn => $data['permiso'],
                'salario' => $data['salario'],
            ];
            if (isset($data['fecha_ingreso']) && self::hasColumn('empleados', 'fecha_ingreso')) {
                $values['fecha_ingreso'] = $data['fecha_ingreso'];
            }
            if (self::usesEmployeeAccounts()) {
                $values['email'] = $data['email'];
                $values['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            $fields = array_keys($values);
            $stmt = $db->prepare('INSERT INTO empleados (' . implode(',', $fields) . ') VALUES (:' . implode(',:', $fields) . ')');
            $params = [];
            foreach ($values as $field => $value) $params[":$field"] = $value;
            $stmt->execute($params);
            $id = (int)$db->lastInsertId();

            if (!self::usesEmployeeAccounts()) {
                $account = [
                    'cod_empleado' => $id,
                    'email' => $data['email'],
                    'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ];
                if (self::hasColumn('usuarios', 'estado_cuenta')) $account['estado_cuenta'] = 'Activo';
                $accountFields = array_keys($account);
                $stmt = $db->prepare('INSERT INTO usuarios (' . implode(',', $accountFields) . ') VALUES (:' . implode(',:', $accountFields) . ')');
                $params = [];
                foreach ($account as $field => $value) $params[":$field"] = $value;
                $stmt->execute($params);
            }

            $db->commit();
            return $id;
        } catch (Throwable $error) {
            if ($db->inTransaction()) $db->rollBack();
            throw $error;
        }
    }

    public static function update($id, array $data)
    {
        $db = ConexionPDO::connect();
        $db->beginTransaction();
        try {
            $roleColumn = self::hasColumn('empleados', 'permiso') ? 'permiso' : 'cargo';
            $values = [
                'ci' => $data['ci'],
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                $roleColumn => $data['permiso'],
                'salario' => $data['salario'],
            ];
            if (array_key_exists('fecha_ingreso', $data) && self::hasColumn('empleados', 'fecha_ingreso')) {
                $values['fecha_ingreso'] = $data['fecha_ingreso'];
            }
            if (self::usesEmployeeAccounts()) $values['email'] = $data['email'];
            $sets = [];
            $params = [':id' => $id];
            foreach ($values as $field => $value) {
                $sets[] = "`$field`=:$field";
                $params[":$field"] = $value;
            }
            $stmt = $db->prepare('UPDATE empleados SET ' . implode(',', $sets) . ' WHERE id=:id');
            $stmt->execute($params);

            if (!self::usesEmployeeAccounts()) {
                $accountValues = [':email' => $data['email'], ':id' => $id];
                $set = 'email=:email';
                if (!empty($data['password'])) {
                    $set .= ', password_hash=:password_hash';
                    $accountValues[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                $stmt = $db->prepare('UPDATE usuarios SET ' . $set . ' WHERE cod_empleado=:id');
                $stmt->execute($accountValues);
                if ($stmt->rowCount() === 0 && !self::hasPassword($id)) {
                    if (empty($data['password'])) throw new InvalidArgumentException('Define una contraseña para este empleado.');
                    $account = ['cod_empleado' => $id, 'email' => $data['email'], 'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT)];
                    if (self::hasColumn('usuarios', 'estado_cuenta')) $account['estado_cuenta'] = 'Activo';
                    $accountFields = array_keys($account);
                    $stmt = $db->prepare('INSERT INTO usuarios (' . implode(',', $accountFields) . ') VALUES (:' . implode(',:', $accountFields) . ')');
                    $params = [];
                    foreach ($account as $field => $value) $params[":$field"] = $value;
                    $stmt->execute($params);
                }
            } elseif (!empty($data['password'])) {
                $stmt = $db->prepare('UPDATE empleados SET password_hash=:hash WHERE id=:id');
                $stmt->execute([':hash' => password_hash($data['password'], PASSWORD_DEFAULT), ':id' => $id]);
            }

            $db->commit();
            return true;
        } catch (Throwable $error) {
            if ($db->inTransaction()) $db->rollBack();
            throw $error;
        }
    }

    public static function delete($id)
    {
        return ConexionPDO::execute('DELETE FROM empleados WHERE id=:id', [':id' => $id], false);
    }
}
