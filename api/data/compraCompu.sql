-- DROP DATABASE IF EXISTS DBventaCompu;
-- CREATE DATABASE dbventaCompu;
-- uSE dbventaCompu;
CREATE TABLE clientes(
id int not null PRIMARY KEY auto_increment,
ci VARCHAR (20) not null,
nombre VARCHAR (50) not NULL,
apellidos VARCHAR (50) not null,
direccion VARCHAR (250),
telefono VARCHAR (15)
)ENGINE=InnoDB; 

CREATE TABLE usuarios (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_cliente INT NOT NULL UNIQUE, 
    email VARCHAR(100) NOT NULL UNIQUE, 
    password_hash VARCHAR(255) NOT NULL, 
    estado_cuenta VARCHAR(20) DEFAULT 'Activo',
    ultimo_acceso DATETIME,
    
    FOREIGN KEY (cod_cliente) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- creamos la tabla empleado
CREATE TABLE proveedores(
id int not null PRIMARY KEY auto_increment,
ci VARCHAR (20) not null,
nombre VARCHAR (50) not null,
apellidos VARCHAR (50) not NULL
)ENGINE=InnoDB;
-- creamos tablo de producto
CREATE TABLE productos(
id int not null PRIMARY KEY auto_increment,
codbarras VARCHAR (100) not null,
descripcion VARCHAR (100) not NULL,
stock INT not null CHECK(stock>=0),
precio_unitario DECIMAL(10,2) not null
)ENGINE=InnoDB;

CREATE TABLE proveedor_producto (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_proveedor INT NOT NULL,
    cod_producto INT NOT NULL,
    FOREIGN KEY (cod_proveedor) REFERENCES proveedores(id) ON DELETE CASCADE,
    FOREIGN KEY (cod_producto) REFERENCES productos(id)
) ENGINE=InnoDB;

CREATE TABLE pedido(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_cliente INT NOT NULL,
    fecha DATETIME DEFAULT NOW(),
    estado VARCHAR(20) DEFAULT 'Pendiente',  
    FOREIGN KEY(cod_cliente) REFERENCES clientes(id)
) ENGINE=InnoDB;


CREATE TABLE pedido_producto(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cod_pedido INT NOT NULL,
    cod_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK(cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT(0.0),
    FOREIGN KEY(cod_pedido) REFERENCES pedido(id),
    FOREIGN KEY(cod_producto) REFERENCES productos(id)
) ENGINE=InnoDB;
