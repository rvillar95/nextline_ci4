-- =====================================================
-- SCRIPT PARA CAMBIAR FORMATO DE FECHA EN BD
-- =====================================================
-- ADVERTENCIA: Este script cambia las columnas fecha de DATE a VARCHAR
-- para permitir formato DD-MM-YYYY. Esto puede afectar comparaciones
-- y ordenamientos de fechas.
-- =====================================================

-- PASO 1: Cambiar columna fecha en tabla agenda de DATE a VARCHAR
ALTER TABLE `agenda` 
MODIFY COLUMN `fecha` VARCHAR(10) NOT NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- PASO 2: Cambiar columna fecha en tabla detalle_agenda de DATE a VARCHAR
ALTER TABLE `detalle_agenda` 
MODIFY COLUMN `fecha` VARCHAR(10) NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- PASO 3: Convertir fechas existentes de YYYY-MM-DD a DD-MM-YYYY
UPDATE `agenda` 
SET `fecha` = DATE_FORMAT(STR_TO_DATE(`fecha`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `fecha` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

UPDATE `detalle_agenda` 
SET `fecha` = DATE_FORMAT(STR_TO_DATE(`fecha`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `fecha` IS NOT NULL 
AND `fecha` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

-- =====================================================
-- NOTA IMPORTANTE:
-- Después de este cambio, las comparaciones de fechas en WHERE
-- necesitarán usar STR_TO_DATE() para convertir DD-MM-YYYY a DATE
-- antes de comparar, o comparar como strings (menos eficiente).
-- =====================================================
