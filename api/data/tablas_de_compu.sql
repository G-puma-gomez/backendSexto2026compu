-- Clientes (Generará IDs del 1 al 5)
INSERT INTO clientes (ci, nombre, apellidos, direccion, telefono) VALUES
('6845123', 'Carlos', 'Mendoza Suárez', 'Av. Las Américas #450, Zona Central', '71023456'),
('9521478', 'Ana María', 'Gutiérrez Rojas', 'Calle Murillo #12 Christie', '65098712'),
('4125896', 'Jorge Luis', 'Espinoza Torrico', 'Barrio Sirari, Calle 3 Este #15', '77345611'),
('7896541', 'Claudia', 'Benítez Mamani', 'Av. Bush, Condominio El Bosque Dep. 4B', '70012345'),
('5321478', 'Andrés', 'Vargas Villagómez', 'Urb. Los Tocos, Manzano 12 Lote 5', '69055443');

-- Proveedores (Generará IDs del 1 al 5)
INSERT INTO proveedores(ci, nombre, apellidos) VALUES
('1029384', 'Roberto', 'Gómez Fernández'),
('5647382', 'Martha Elena', 'Salinas Quiroga'),
('8930124', 'Fernando', 'López Justiniano'),
('4756102', 'Lucía', 'Pinto Aramayo'),
('3344556', 'Alejandro', 'Zeballos Castro');

-- Productos (Generará IDs del 1 al 5)
INSERT INTO productos(codbarras, descripcion, stock, precio_unitario) VALUES
('4719331302840', 'Laptop ASUS Vivobook 15" Core i5 16GB RAM 512GB SSD', 15, 6800.00),
('0840006622147', 'Memoria RAM Corsair Vengeance DDR4 16GB (2x8GB) 3200MHz', 45, 450.50),
('4712900697031', 'Tarjeta de Video NVIDIA RTX 4060 Ti 8GB GDDR6', 8, 3850.00),
('0619659185626', 'Disco Duro Sólido SSD Kingston NV2 1TB NVMe PCIe 4.0', 60, 580.00),
('8806094214573', 'Monitor Gamer Samsung Odyssey G3 24" 165Hz 1ms', 12, 1451.90);

SELECT * FROM clientes;
SELECT * FROM proveedores;
SELECT * FROM productos;

-- Proveedor_Producto
INSERT INTO proveedor_producto (cod_proveedor, cod_producto) VALUES
(1, 1), (1, 4), (2, 2), (3, 3), (4, 5);

-- Pedidos (Generará IDs del 1 al 5)
INSERT INTO pedido (cod_cliente, fecha, estado) VALUES
(1, '2026-05-15 10:30:00', 'Entregado'),
(2, '2026-05-20 14:15:00', 'Procesando'),
(3, '2026-06-01 09:00:00', 'Pendiente'),
(4, NOW(), 'Pendiente'),
(5, '2026-06-02 18:45:00', 'Cancelado');

-- Pedido_Producto
INSERT INTO pedido_producto (cod_pedido, cod_producto, cantidad, precio_unitario, descuento) VALUES
(1, 1, 1, 6800.00, 200.00),
(1, 4, 2, 580.00, 0.00),
(2, 5, 1, 1450.90, 50.90),
(3, 2, 4, 450.50, 0.00),
(4, 3, 1, 3850.00, 100.00);

SELECT * FROM proveedor_producto;
SELECT * FROM pedido;
SELECT * FROM pedido_producto;

-- Empleados (Generará IDs correlativos automáticos: 1, 2, 3, 4)
INSERT INTO empleados(ci, nombre, apellidos, permiso, salario, fecha_ingreso) VALUES
('5421896', 'Alejandro', 'Torres Rojas', 'Administrador', 5500.00, '2025-01-10'),     -- ID 1
('8741235', 'Paola', 'Suárez Justiniano', 'Vendedor', 3000.00, '2025-06-15'),          -- ID 2
('6325147', 'Ricardo', 'Vaca Villagómez', 'Vendedor', 3000.00, '2025-08-01'),          -- ID 3
('4952187', 'Fernando', 'Ortiz Mamani', 'Técnico', 3500.00, '2026-02-10');             -- ID 4

-- Usuarios (Corregidos los "cod_empleado" para que apunten a los IDs reales 1, 2, 3, 4)
INSERT INTO usuarios (cod_empleado, email, password_hash, estado_cuenta, ultimo_acceso) VALUES
(1, 'alejandro.torres@empresa.com', '$2y$10$7RzX...', 'Activo', '2026-10-16 09:15:00'),
(2, 'paola.suarez@empresa.com', '$2y$10$8XyZ...', 'Activo', '2026-10-15 18:42:11'),
(3, 'ricardo.vaca@empresa.com', '$2y$10$9AbC...', 'Activo', '2026-10-12 11:30:22'),
(4, 'fernando.ortiz@empresa.com', '$2y$10$0DeF...', 'Activo', '2026-10-16 14:05:00');

-- Empleado_Pedido (Corregidos los "cod_empleado" para usar los IDs válidos del 1 al 4)
INSERT INTO empleado_pedido(cod_pedido, cod_empleado, rol_en_pedido, fecha_accion) VALUES
(1, 2, 'Vendedor - Toma de Orden', '2026-05-15 10:30:00'),
(1, 1, 'Administrador - Facturación', '2026-05-15 10:45:00'),
(2, 2, 'Vendedor', '2026-05-20 14:15:00'),
(2, 4, 'Soporte Técnico - Armado', '2026-05-20 14:40:00'),
(3, 3, 'Vendedor', '2026-06-01 09:00:00'),
(4, 3, 'Asignado a Ventas', NOW()),
(5, 2, 'Vendedor', '2026-06-02 18:45:00');


-- =========================================================================
-- 3. VERIFICACIÓN DE DATOS (Consultas SELECT)
-- =========================================================================
SELECT * FROM empleados;
SELECT * FROM usuarios;
SELECT * FROM empleado_pedido;