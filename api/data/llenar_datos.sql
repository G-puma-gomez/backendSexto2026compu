INSERT INTO clientes (ci, nombre, apellidos, direccion, telefono) VALUES
('10203040', 'Carlos', 'Mendoza Ruiz', 'Av. Arce #1234, Edif. Los Pinos', '71234567'),
('20304050', 'María Elena', 'Gómez Flores', 'Calle Murillo #456, Zona Central', '60123456'),
('30405060', 'Juan Pablo', 'Mamani Quispe', 'Av. Blanco Galindo Km 5', '72345678'),
('40506070', 'Ana Belén', 'Rodríguez Torrico', 'Calle Linares #789', '65432109'),
('50607080', 'Luis Fernando', 'Vargas Suárez', 'Barrio Sirari, Calle 3 Oeste', '73456789'),
('60708090', 'Claudia', 'Fernández Rojas', 'Av. América E-125, Queru Queru', '60234567'),
('70809010', 'Jorge Luis', 'Gutiérrez Choque', 'Calle Potosí #220, Piso 3', '74567890'),
('80901020', 'Patricia', 'López Miranda', 'Urb. El Remanso, Manzano 12', '71564738'),
('90102030', 'Andrés', 'Castro Benítez', 'Av. 6 de Agosto #987', '61239874'),
('10152025', 'Sofía', 'Morales Justiniano', 'Equipetrol, Calle Las Palmitas', '75678901'),
('11223344', 'Alejandro', 'Pinto Salvatierra', 'Av. Banzer Km 6, Cond. Sevilla', '60781234'),
('22334455', 'Gabriela', 'Chávez Ortiz', 'Calle Sucre #512', '76789012'),
('33445566', 'Ricardo', 'Soliz Mercado', 'Zona Miraflores, Calle Díaz Romero', '71122334'),
('44556677', 'Natalia', 'Ríos Alarcón', 'Av. Circunvalación #445', '60890123'),
('55667788', 'Mauricio', 'Paz Estenssoro', 'Barrio San Gerónimo, Calle Los Lapachos', '77890123'),
('66778899', 'Lucía', 'Medina Zenteno', 'Calle Comercio #1025', '61901234'),
('77889900', 'Daniel', 'Céspedes Aguilera', 'Av. Santos Dumont, Barrio El Paraíso', '78901234'),
('88990011', 'Valeria', 'Camacho Villegas', 'Zona San Pedro, Calle Cañada Strongest', '62012345'),
('99001122', 'Oscar', 'Salazar Baldivieso', 'Av. Pando #320, Edif. El Sol', '79012345'),
('12345678', 'Camila', 'Guzmán Roca', 'Barrio Equipetrol, Calle 8 Este', '63123456');


INSERT INTO empleados (ci, nombre, apellidos) VALUES
('1234567-1A', 'Juan', 'Pérez Gómez'),
('2345678-2B', 'María', 'Rodríguez Martínez'),
('3456789-3C', 'Carlos', 'López Hernández'),
('4567890-4D', 'Ana', 'García Fernández'),
('5678901-5E', 'Luis', 'Martínez González'),
('6789012-6F', 'Laura', 'Sánchez Pérez'),
('7890123-7G', 'José', 'Ramírez Ramírez'),
('8901234-8H', 'Elena', 'Torres Ruiz'),
('9012345-9I', 'Pedro', 'Díaz Flores'),
('0123456-0J', 'Sofía', 'Vázquez Castro'),
('1122334-1K', 'Diego', 'Álvarez Romero'),
('2233445-2L', 'Lucía', 'Suárez Blanco'),
('3344556-3M', 'Manuel', 'Benítez Delgado'),
('4455667-4N', 'Clara', 'Rubio Navarro'),
('5566778-5O', 'Javier', 'Ruiz Morales'),
('6677889-6P', 'Daniela', 'Ortega Medina'),
('7788990-7Q', 'Andrés', 'Castillo Delgado'),
('8899001-8R', 'Beatriz', 'Cano Marín'),
('9900112-9S', 'Francisco', 'Cortés Rubio'),
('1011121-0T', 'Gabriela', 'Morín Serrano');

INSERT INTO productos (codbarras, descripcion, stock, precio_unitario) VALUES
('750105531001', 'Monitor LED 24 Pulgadas Full HD', 15, 2499.50),
('750105531002', 'Teclado Mecánico RGB Gamer', 30, 850.00),
('750105531003', 'Mouse Óptico Inalámbrico', 45, 350.00),
('750105531004', 'Auriculares Bluetooth con Cancelación de Ruido', 20, 1200.99),
('750105531005', 'Memoria USB 64GB 3.0', 100, 180.00),
('750105531006', 'Disco Duro Externo 1TB', 12, 1150.00),
('750105531007', 'Cable HDMI 2 Metros', 75, 95.50),
('750105531008', 'Impresora Multifuncional Tinta Continua', 8, 3899.00),
('750105531009', 'Silla Ergonómica de Oficina', 5, 2100.00),
('750105531010', 'Escritorio de Madera Moderno', 3, 3450.00),
('750105531011', 'Lámpara de Escritorio LED Inteligente', 25, 420.00),
('750105531012', 'Mochila Impermeable para Laptop', 40, 650.00),
('750105531013', 'Power Bank 20000mAh Fast Charge', 50, 499.00),
('750105531014', 'Adaptador USB-C a Ethernet/HDMI', 18, 320.00),
('750105531015', 'Cargador de Pared Carga Rápida 20W', 60, 250.00),
('750105531016', 'Soporte de Aluminio para Laptop', 22, 380.00),
('750105531017', 'Cámara Web 1080p con Micrófono', 14, 750.00),
('750105531018', 'Paquete de Hojas Tamaño Carta (500 hjs)', 200, 115.00),
('750105531019', 'Organizador de Cables de Escritorio', 85, 45.00),
('750105531020', 'Limpiador en Spray para Pantallas', 35, 85.00);

SELECT * FROM clientes;
SELECT * FROM empleados;
SELECT * FROM productos;

INSERT INTO pedido (cod_cliente, fecha_compra, cantidad, cod_empleado) VALUES
(1, '2026-05-20 10:15:00', 3, 1),
(2, '2026-05-20 11:30:00', 1, 2),
(3, '2026-05-21 09:00:00', 5, 3),
(4, '2026-05-21 14:20:00', 2, 4),
(5, '2026-05-22 16:45:00', 1, 5),
(6, '2026-05-22 18:00:00', 4, 1),
(7, '2026-05-23 11:10:00', 2, 2),
(8, '2026-05-23 15:35:00', 10, 3),
(9, '2026-05-24 12:00:00', 1, 4),
(10, '2026-05-24 17:25:00', 3, 5);

INSERT INTO pedido_producto (cod_producto, cod_pedido, cantidad, precio_unitario, descuento) VALUES
(1, 1, 1, 2499.50, 0.00),  -- Pedido 1: Monitor
(3, 1, 2, 350.00, 10.00),   -- Pedido 1: 2 Mouses con descuento
(2, 2, 1, 850.00, 0.00),    -- Pedido 2: Teclado
(4, 3, 2, 1200.99, 50.00),  -- Pedido 3: 2 Auriculares
(5, 3, 3, 180.00, 0.00),    -- Pedido 3: 3 Memorias USB
(7, 4, 2, 95.50, 0.00),     -- Pedido 4: 2 Cables HDMI
(6, 5, 1, 1150.00, 100.00), -- Pedido 5: Disco Duro
(12, 6, 4, 650.00, 0.00),   -- Pedido 6: 4 Mochilas
(13, 7, 2, 499.00, 20.00),  -- Pedido 7: 2 Power Banks
(18, 8, 10, 115.00, 5.00);  -- Pedido 8: 10 Paquetes de hojas

INSERT INTO empleado_pedidos (cod_pedido, cod_empleado, fecha) VALUES
(1, 1, '2026-05-20'),
(2, 2, '2026-05-20'),
(3, 3, '2026-05-21'),
(4, 4, '2026-05-21'),
(5, 5, '2026-05-22'),
(6, 1, '2026-05-22'),
(7, 2, '2026-05-23'),
(8, 3, '2026-05-23'),
(9, 4, '2026-05-24'),
(10, 5, '2026-05-24');

SELECT * FROM pedido;
SELECT * FROM pedido_producto;
SELECT * FROM empleado_pedidos;