-- Script para agregar campos region_id y comuna_id a la tabla clientes
-- Ejecutar después de crear las tablas de regiones y comunas

-- Agregar campos region_id y comuna_id a la tabla clientes
ALTER TABLE `clientes` 
ADD COLUMN `region_id` INT(11) NULL AFTER `direccion`,
ADD COLUMN `comuna_id` INT(11) NULL AFTER `region_id`;

-- Agregar índices para mejorar el rendimiento
ALTER TABLE `clientes` 
ADD INDEX `idx_clientes_region` (`region_id`),
ADD INDEX `idx_clientes_comuna` (`comuna_id`);

-- Agregar claves foráneas para mantener integridad referencial
ALTER TABLE `clientes` 
ADD CONSTRAINT `fk_clientes_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `fk_clientes_comuna` FOREIGN KEY (`comuna_id`) REFERENCES `comunas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Opcional: Migrar datos existentes si hay datos en los campos region/comuna
-- UPDATE clientes SET region_id = (SELECT id FROM regiones WHERE nombre LIKE CONCAT('%', clientes.region, '%') LIMIT 1) WHERE region IS NOT NULL AND region != '';
-- UPDATE clientes SET comuna_id = (SELECT id FROM comunas WHERE nombre LIKE CONCAT('%', clientes.comuna, '%') LIMIT 1) WHERE comuna IS NOT NULL AND comuna != '';

-- Mostrar resultado
SELECT 'Campos region_id y comuna_id agregados exitosamente a la tabla clientes' as resultado;
