-- Agregar campo para URL de Google Maps en empresa (para consultorio / ubicación)
-- Ejecutar en la base de datos del proyecto.

ALTER TABLE empresa
ADD COLUMN url_google_maps VARCHAR(1000) NULL DEFAULT NULL
COMMENT 'URL de Google Maps del consultorio o ubicación de la empresa'
AFTER direccion;
