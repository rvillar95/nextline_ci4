-- =====================================================
-- CAMBIAR FORMATO DE FECHA EN historial_clinico
-- =====================================================
-- Este script cambia la columna fecha_consulta de DATE a VARCHAR
-- para permitir formato DD-MM-YYYY como en agenda y detalle_agenda
-- =====================================================

-- Cambiar columna fecha_consulta en tabla historial_clinico de DATE a VARCHAR
ALTER TABLE `historial_clinico` 
MODIFY COLUMN `fecha_consulta` VARCHAR(10) NOT NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- Convertir fechas existentes de YYYY-MM-DD a DD-MM-YYYY (si las hay)
UPDATE `historial_clinico` 
SET `fecha_consulta` = DATE_FORMAT(STR_TO_DATE(`fecha_consulta`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `fecha_consulta` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

-- También cambiar proxima_cita si existe
ALTER TABLE `historial_clinico` 
MODIFY COLUMN `proxima_cita` VARCHAR(10) NULL COMMENT 'Fecha en formato DD-MM-YYYY';

-- Convertir fechas existentes de proxima_cita
UPDATE `historial_clinico` 
SET `proxima_cita` = DATE_FORMAT(STR_TO_DATE(`proxima_cita`, '%Y-%m-%d'), '%d-%m-%Y')
WHERE `proxima_cita` IS NOT NULL 
  AND `proxima_cita` REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Después de ejecutar, verificar que las fechas estén en formato DD-MM-YYYY:
-- SELECT fecha_consulta, proxima_cita FROM historial_clinico LIMIT 5;
-- =====================================================
