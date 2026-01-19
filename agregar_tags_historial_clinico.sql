-- =====================================================
-- AGREGAR SISTEMA DE TAGS A HISTORIAL CLÍNICO
-- =====================================================
-- Este script agrega:
-- 1. Campo `tags` (JSON) a tabla `historial_clinico` para almacenar tags
-- 2. Tabla `historial_tags` para normalizar tags y mejorar búsqueda
-- =====================================================

-- 1. Agregar campo `tags` a tabla `historial_clinico`
-- Usamos JSON para almacenar array de tags: ["tag1", "tag2", "tag3"]
-- Verificar si la columna ya existe antes de agregarla
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'historial_clinico'
      AND COLUMN_NAME = 'tags'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `tags` JSON NULL COMMENT ''Array de tags en formato JSON'' AFTER `observaciones`',
    'SELECT ''La columna tags ya existe'' AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Nota: No podemos crear índice FULLTEXT en columnas virtuales generadas
-- La búsqueda se hará usando JSON_SEARCH directamente en el campo JSON
-- MySQL 5.7+ soporta funciones JSON nativas para búsqueda eficiente

-- 2. Crear tabla `historial_tags` para normalizar tags
-- Esta tabla permite:
-- - Búsqueda más eficiente
-- - Sugerencias de tags basadas en uso
-- - Estadísticas de tags más usados
CREATE TABLE IF NOT EXISTS `historial_tags` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `tag` VARCHAR(100) NOT NULL COMMENT 'Nombre del tag (normalizado, sin espacios, minúsculas)',
    `tag_display` VARCHAR(100) NOT NULL COMMENT 'Nombre del tag para mostrar (con formato original)',
    `usos` INT NOT NULL DEFAULT 1 COMMENT 'Cantidad de veces que se ha usado este tag',
    `empresa_id` INT NULL COMMENT 'ID de la empresa (opcional, para tags por empresa)',
    `fcreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `factualizacion` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_tag_empresa` (`tag`, `empresa_id`),
    KEY `idx_tag` (`tag`),
    KEY `idx_usos` (`usos`),
    KEY `idx_empresa` (`empresa_id`),
    CONSTRAINT `fk_historial_tags_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Tabla de tags normalizados para historial clínico';

-- 3. Crear función helper para normalizar tags
-- Esta función se puede usar en PHP, pero la dejamos documentada aquí
-- Función PHP equivalente:
-- function normalizarTag($tag) {
--     return strtolower(trim($tag));
-- }

-- 4. Verificar que el campo se agregó correctamente
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'tags';

-- 5. Verificar que la tabla historial_tags se creó correctamente
SHOW CREATE TABLE `historial_tags`;
