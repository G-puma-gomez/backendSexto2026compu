-- creamos la base de datos
DROP DATABASE IF EXISTS DBSexto2026;
CREATE DATABASE DBSexto2026;
-- usamos la base de datos DBSexto2026
USE DBSexto2026;
-- crear tabla de usuario
CREATE TABLE usuarios(
id INT NOT NULL PRIMARY KEY auto_increment,
nombreUsuarios VARCHAR(100)NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(250) NOT NULL
)ENGINE=InnoDB;
-- crear la tabla de imagenes
CREATE TABLE imagenes(
id INT NOT NULL PRIMARY KEY auto_increment,
urlImagen VARCHAR(255) NOT NULL,
usuarioId INT NOT NULL,
CONSTRAINT fk_user_imagen  FOREIGN KEY(usuarioId) REFERENCES usuarios(id)
)ENGINE=InnoDB;



-- adicionar datos a la tabla usuarios
INSERT INTO usuarios(nombreUsuarios,email,password)
values('avril','arelis@gmail.com','quispe123');
INSERT INTO usuarios(nombreUsuarios,email,password)
values('puma','puma@gmail.com','puma123');
INSERT INTO usuarios(nombreUsuarios,email,password)
values('gomez','gomez@gmail.com','gomez123');
-- modificar el nombre del usuarios
UPDATE usuarios SET nombreUsuarios='neo'
WHERE id=2;
UPDATE usuarios SET email='neo@gmail.com'
WHERE id=2;
UPDATE usuarios SET nombreUsuarios='gabriel', email='gabriel@gmail.com', password='gabriel123'
WHERE id=3;
-- eliminar un usuarios
DELETE FROM usuarios 
WHERE id=2;

SELECT * FROM usuarios;
