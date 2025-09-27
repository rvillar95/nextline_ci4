-- Script para corregir el nombre del módulo de categorías de galería
-- Ejecutar en phpMyAdmin o cliente MySQL

USE nextline_pyme;

-- Cambiar el nombre del módulo para que quepa mejor en el menú
UPDATE modulo 
SET nombre = 'Categorías Galería' 
WHERE nombre = 'Categorías de Galería';

-- Verificar el cambio
SELECT id, nombre, descripcion, ruta FROM modulo WHERE nombre LIKE '%Categorías%';
