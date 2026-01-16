-- =====================================================
-- AGREGAR CAMPOS DE MENSAJES DE CANCELACIÓN MASIVA
-- =====================================================
-- Este script agrega campos a la tabla usuario_configuraciones
-- para almacenar los mensajes personalizados por estado
-- =====================================================

-- Agregar columnas para mensajes de cancelación por estado
-- Nota: Si las columnas ya existen, este script fallará. Ejecutar solo si no existen.

-- Verificar y agregar mensaje_cancelacion_pendiente
SET @dbname = DATABASE();
SET @tablename = 'usuario_configuraciones';
SET @columnname = 'mensaje_cancelacion_pendiente';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (table_name = @tablename)
            AND (table_schema = @dbname)
            AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT NULL COMMENT ''Mensaje HTML para cancelar citas en estado pendiente''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar y agregar mensaje_cancelacion_confirmada
SET @columnname = 'mensaje_cancelacion_confirmada';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (table_name = @tablename)
            AND (table_schema = @dbname)
            AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT NULL COMMENT ''Mensaje HTML para cancelar citas en estado confirmada''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar y agregar mensaje_cancelacion_en_proceso
SET @columnname = 'mensaje_cancelacion_en_proceso';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (table_name = @tablename)
            AND (table_schema = @dbname)
            AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT NULL COMMENT ''Mensaje HTML para cancelar citas en estado en_proceso''')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregaron las columnas
DESCRIBE `usuario_configuraciones`;
