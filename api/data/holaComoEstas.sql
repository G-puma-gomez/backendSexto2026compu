-- ejercicios UNCOMPRESS
SELECT apellidos,nombre,telefono FROM clientes
ORDER BY apellidos,nombre;
-- ejercicio dos 
SELECT * FROM productos
WHERE stock<=50;
-- ejer3
SELECT * FROM empleados
WHERE apellidos LIKE "a%"
-- ejer 4
SELECT c.nombre,c.apellidos,p.fecha_compra,p.cantidad
FROM pedido AS p, clientes as c
WHERE c.Id = p.cod_cliente
-- otra forma
 SELECT c.nombre,c.apellidos,p.fecha_compra,p.cantidad
 FROM pedido AS p
 INNER JOIN clientes c ON c.Id = p.cod_cliente;
-- ejercicio 5
SELECT e.apellidos,e.nombre, COUNT(e.id)AS totalVendido
FROM empleados e 
INNER JOIN pedido p on e.id = p.cod_empleado
GROUP BY e.id

SELECT e.apellidos,e.nombre, COUNT(e.id)AS totalVendido
FROM empleados e 
LEFT JOIN pedido p on e.id = p.cod_empleado
GROUP BY e.id
-- hola ejer 6
SELECT p.codbarras,p.descripcion,p.precio_unitario
FROM productos p
ORDER BY precio_unitario DESC;
-- ejercicio 7: mostrar la descripcion y el stock unicamente de aquellos productos que tengan menos de 40 unidades disponibles
SELECT descripcion,stock
FROM productos 
WHERE stock<40;
-- ejer 8 el departamento de markenting quiere saber que clientes ya realizaron compras,mostrar el nombre del cliente, su apellido y la fecha de compra de sus pedidos
SELECT  c.nombre,c.apellidos,p.fecha_compra
FROM clientes c 
iNNER JOIN pedido as p on c.Id = p.cod_cliente
GROUP BY fecha_compra DESC;


SELECT c.ci,
c.nombre as 'nombreCliente',
c.apellidos as 'apellidoCliente',
p.cantidad,
pp.precio_unitario,
prod.descripcion as'nombreProducto',
e.nombre as'nombreEmpleado',
e.apellidos as'apellidosEmpleado'
FROM clientes c
INNER JOIN pedido p ON p.cod_cliente=c.id
INNER JOIN pedido_producto pp on pp.cod_pedido=p.id
INNER JOIN productos prod on prod.id=pp.cod_producto
INNER JOIN empleados e on e.id=p.cod_empleado
