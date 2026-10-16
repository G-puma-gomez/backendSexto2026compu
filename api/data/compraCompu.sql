DROP DATABASE IF EXISTS defaultdb; 
CREATE DATABASE defaultdb; 
USE defaultdb; 

-- 1. Tabla de Clientes
CREATE TABLE IF NOT EXISTS clientes(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ci VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    direccion VARCHAR(250),
    telefono VARCHAR(15)
) ENGINE=InnoDB;

-- 3. Tabla de Empleados (Se movió arriba para poder referenciarla en Usuarios)
CREATE TABLE IF NOT EXISTS empleados(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ci VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    permiso VARCHAR(50) DEFAULT 'Vendedor',
    salario DECIMAL(10,2) NOT NULL CHECK(salario > 0),
    fecha_ingreso DATE DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB;

-- 2. Tabla de Usuarios (CORREGIDA: Ahora referenica a Empleados)
CREATE TABLE IF NOT EXISTS usuarios(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_empleado INT NOT NULL UNIQUE, -- Cambio de cliente a empleado
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado_cuenta VARCHAR(20) DEFAULT 'Activo',
    ultimo_acceso DATETIME,
    FOREIGN KEY (cod_empleado) REFERENCES empleados(id) ON DELETE CASCADE -- Llave foránea corregida
) ENGINE=InnoDB;

-- 4. Tabla de Proveedores
CREATE TABLE IF NOT EXISTS proveedores(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ci VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 5. Tabla de Productos
CREATE TABLE IF NOT EXISTS productos(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    codbarras VARCHAR(100) NOT NULL,
    descripcion VARCHAR(100) NOT NULL,
    stock INT NOT NULL CHECK(stock >= 0),
    precio_unitario DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- 6. Tabla Intermedia: Proveedor_Producto
CREATE TABLE IF NOT EXISTS proveedor_producto (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_proveedor INT NOT NULL,
    cod_producto INT NOT NULL,
    FOREIGN KEY (cod_proveedor) REFERENCES proveedores(id) ON DELETE CASCADE,
    FOREIGN KEY (cod_producto) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Tabla de Pedidos
CREATE TABLE IF NOT EXISTS pedido(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_cliente INT NOT NULL,
    fecha DATETIME DEFAULT NOW(),
    estado VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY(cod_cliente) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Tabla Intermedia: Empleado_Pedido
CREATE TABLE IF NOT EXISTS empleado_pedido (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_pedido INT NOT NULL,
    cod_empleado INT NOT NULL,
    rol_en_pedido VARCHAR(50) DEFAULT 'Vendedor',
    fecha_accion DATETIME DEFAULT NOW(),
    FOREIGN KEY (cod_pedido) REFERENCES pedido(id) ON DELETE CASCADE,
    FOREIGN KEY (cod_empleado) REFERENCES empleados(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 9. Tabla de Pedidos_Productos
CREATE TABLE IF NOT EXISTS pedido_producto(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_pedido INT NOT NULL,
    cod_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK(cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0.0,
    FOREIGN KEY(cod_pedido) REFERENCES pedido(id) ON DELETE CASCADE,
    FOREIGN KEY(cod_producto) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
