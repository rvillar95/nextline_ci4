-- Script para corregir la estructura de categorías en la tabla servicio
-- Este script migra de categorías por nombre a categorías por ID

-- 1. Primero, agregar la columna categoria_id si no existe
ALTER TABLE `servicio` ADD COLUMN `categoria_id` INT NULL AFTER `categoria`;

-- 2. Actualizar categoria_id basándose en el nombre de la categoría
UPDATE `servicio` s 
SET `categoria_id` = (
    SELECT sc.id 
    FROM `servicio_categoria` sc 
    WHERE sc.nombre = s.categoria 
    AND sc.estado = 'A'
)
WHERE s.categoria IS NOT NULL;

-- 3. Verificar los resultados
SELECT 
    s.id,
    s.nombre,
    s.categoria as categoria_nombre_actual,
    s.categoria_id,
    sc.nombre as categoria_nombre_correcta
FROM `servicio` s
LEFT JOIN `servicio_categoria` sc ON s.categoria_id = sc.id
ORDER BY s.id;

-- 4. Opcional: Eliminar la columna categoria después de verificar que todo esté correcto
-- ALTER TABLE `servicio` DROP COLUMN `categoria`;
