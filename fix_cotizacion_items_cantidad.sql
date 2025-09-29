-- Script para cambiar el tipo de dato de cantidad en cotizacion_items
-- de DECIMAL(10,3) a DOUBLE para permitir valores con coma

USE nextline_ci4;

-- Cambiar el tipo de dato de cantidad a DOUBLE
ALTER TABLE cotizacion_items 
MODIFY COLUMN cantidad DOUBLE NOT NULL DEFAULT 1;

-- Verificar el cambio
DESCRIBE cotizacion_items;
