-- =====================================================
-- SCRIPT PARA CAMBIAR FORMATO DE FECHA EN BD
-- =====================================================
-- Este script cambia las columnas fecha de DATE a VARCHAR
-- para permitir formato DD-MM-YYYY como solicitó el usuario
-- =====================================================

-- PASO 1: Cambiar columna fecha en tabla agenda de DATE a VARCHAR
ALTER TABLE `agenda` 
MODIFY COLUMN `fecha` VARCHAR(10) NOT NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- PASO 2: Cambiar columna fecha en tabla detalle_agenda de DATE a VARCHAR
ALTER TABLE `detalle_agenda` 
MODIFY COLUMN `fecha` VARCHAR(10) NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- PASO 3: Convertir fechas existentes de YYYY-MM-DD a DD-MM-YYYY
-- Solo si hay fechas en formato YYYY-MM-DD
UPDATE `agenda` 
SET `fecha` = DATE_FORMAT(STR_TO_DATE(`fecha`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `fecha` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

UPDATE `detalle_agenda` 
SET `fecha` = DATE_FORMAT(STR_TO_DATE(`fecha`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `fecha` IS NOT NULL 
AND `fecha` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Después de ejecutar, verificar que las fechas estén en formato DD-MM-YYYY:
-- SELECT fecha FROM agenda LIMIT 5;
-- SELECT fecha FROM detalle_agenda LIMIT 5;
-- =====================================================
