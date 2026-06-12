INSERT INTO clientes (ci, nombre, apellidos, direccion, telefono) VALUES
('6845123', 'Carlos', 'Mendoza Suárez', 'Av. Las Américas #450, Zona Central', '71023456'),
('9521478', 'Ana María', 'Gutiérrez Rojas', 'Calle Murillo #12 Christie', '65098712'),
('4125896', 'Jorge Luis', 'Espinoza Torrico', 'Barrio Sirari, Calle 3 Este #15', '77345611'),
('7896541', 'Claudia', 'Benítez Mamani', 'Av. Bush, Condominio El Bosque Dep. 4B', '70012345'),
('5321478', 'Andrés', 'Vargas Villagómez', 'Urb. Los Tocos, Manzano 12 Lote 5', '69055443');

INSERT INTO proveedores(ci, nombre, apellidos) VALUES
('1029384', 'Roberto', 'Gómez Fernández'),
('5647382', 'Martha Elena', 'Salinas Quiroga'),
('8930124', 'Fernando', 'López Justiniano'),
('4756102', 'Lucía', 'Pinto Aramayo'),
('3344556', 'Alejandro', 'Zeballos Castro');

INSERT INTO productos (codbarras, descripcion, stock, precio_unitario) VALUES
('4719331302840', 'Laptop ASUS Vivobook 15" Core i5 16GB RAM 512GB SSD', 15, 6800.00),
('0840006622147', 'Memoria RAM Corsair Vengeance DDR4 16GB (2x8GB) 3200MHz', 45, 450.50),
('4712900697031', 'Tarjeta de Video NVIDIA RTX 4060 Ti 8GB GDDR6', 8, 3850.00),
('0619659185626', 'Disco Duro Sólido SSD Kingston NV2 1TB NVMe PCIe 4.0', 60, 580.00),
('8806094214573', 'Monitor Gamer Samsung Odyssey G3 24" 165Hz 1ms', 12, 1450.90);

SELECT * FROM clientes;
SELECT * FROM proveedores;
SELECT * FROM productos;

INSERT INTO proveedor_producto (cod_proveedor, cod_producto) VALUES
(1, 1), -- Roberto Gómez provee la Laptop ASUS Vivobook
(1, 4), -- Roberto Gómez también provee el Disco Duro Sólido Kingston
(2, 2), -- Martha Elena Salinas provee la Memoria RAM Corsair
(3, 3), -- Fernando López provee la Tarjeta de Video NVIDIA RTX 4060
(4, 5); -- Lucía Pinto provee el Monitor Gamer Samsung

INSERT INTO pedido (cod_cliente, fecha, estado) VALUES
(1, '2026-05-15 10:30:00', 'Entregado'), -- Carlos Mendoza (Cliente 1) hizo un pedido ya entregado
(2, '2026-05-20 14:15:00', 'Procesando'),-- Ana María (Cliente 2) tiene un pedido en camino
(3, '2026-06-01 09:00:00', 'Pendiente'),  -- Jorge Luis (Cliente 3) tiene un pedido pendiente
(4, NOW(), 'Pendiente'),                  -- Claudia (Cliente 4) ordenó justo ahora usando la función NOW()
(5, '2026-06-02 18:45:00', 'Cancelado');  -- Andrés (Cliente 5) canceló este pedido

INSERT INTO pedido_producto (cod_pedido, cod_producto, cantidad, precio_unitario, descuento) VALUES
(1, 1, 1, 6800.00, 200.00), -- El Pedido 1 lleva 1 Laptop ASUS con un descuento de 200
(1, 4, 2, 580.00, 0.00),    -- El Pedido 1 también lleva 2 Discos Duros Kingston (sin descuento)
(2, 5, 1, 1450.90, 50.90),   -- El Pedido 2 lleva 1 Monitor Samsung con descuento de 50.90
(3, 2, 4, 450.50, 0.00),    -- El Pedido 3 lleva 4 Memorias RAM Corsair
(4, 3, 1, 3850.00, 100.00); -- El Pedido 4 lleva 1 Tarjeta de Video NVIDIA con descuento de 100

SELECT * FROM proveedor_producto;
SELECT * FROM pedido;
SELECT * FROM pedido_producto;

INSERT INTO usuarios (cod_cliente, email, password_hash) VALUES
(1, 'carlos.mendoza@email.com', '$2y$10$e0myZXy9x...hash_simulado_1'), -- Cliente 1: Carlos
(2, 'ana.gutierrez@email.com', '$2y$10$e0myZXy9x...hash_simulado_2'),  -- Cliente 2: Ana María
(3, 'jorge.espinoza@email.com', '$2y$10$e0myZXy9x...hash_simulado_3'), -- Cliente 3: Jorge Luis
(4, 'claudia.benitez@email.com', '$2y$10$e0myZXy9x...hash_simulado_4'),-- Cliente 4: Claudia
(5, 'andres.vargas@email.com', '$2y$10$e0myZXy9x...hash_simulado_5');  -- Cliente 5: Andrés

SELECT * FROM usuarios;