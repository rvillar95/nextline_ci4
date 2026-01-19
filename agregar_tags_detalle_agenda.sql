-- =====================================================
-- AGREGAR CAMPO TAGS A DETALLE_AGENDA
-- =====================================================
-- Este script agrega el campo `tags` (JSON) a la tabla
-- `detalle_agenda` para asociar tags directamente a las citas
-- =====================================================

-- 1. Agregar campo `tags` a tabla `detalle_agenda`
-- Usamos JSON para almacenar array de tags: ["tag1", "tag2", "tag3"]
-- Verificar si la columna ya existe antes de agregarla
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'detalle_agenda'
      AND COLUMN_NAME = 'tags'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `detalle_agenda` ADD COLUMN `tags` JSON NULL COMMENT ''Array de tags en formato JSON'' AFTER `recomendaciones`',
    'SELECT ''La columna tags ya existe'' AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Migrar tags existentes de historial_clinico a detalle_agenda
-- Solo si el historial_clinico tiene detalle_agenda_id y tags
UPDATE `detalle_agenda` da
INNER JOIN `historial_clinico` hc ON hc.detalle_agenda_id = da.id
SET da.tags = hc.tags
WHERE hc.tags IS NOT NULL 
  AND hc.tags != '[]'
  AND hc.tags != 'null'
  AND (da.tags IS NULL OR da.tags = '[]' OR da.tags = 'null');

-- 3. Verificar que el campo se agregó correctamente
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'detalle_agenda'
  AND COLUMN_NAME = 'tags';

-- 4. Mostrar estadísticas de migración
SELECT 
    COUNT(*) as total_detalle_agenda,
    COUNT(da.tags) as con_tags,
    COUNT(*) - COUNT(da.tags) as sin_tags
FROM detalle_agenda da
WHERE da.paciente_id IS NOT NULL;
