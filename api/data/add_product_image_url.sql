-- Ejecutar una sola vez en la base de datos existente.
ALTER TABLE productos ADD COLUMN image_url VARCHAR(2048) NULL AFTER descripcion;
