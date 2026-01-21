-- =====================================================
-- AGREGAR CAMPOS COMPLETOS PARA COMPOSICIÓN CORPORAL
-- =====================================================
-- Este script agrega todos los campos necesarios para
-- implementar métodos de composición corporal de 2, 3, 4, 5, 6, 7 componentes
-- y cálculo de somatotipo (Heath-Carter)
-- =====================================================
-- Campos agregados:
-- 1. Altura sentado (para métodos 4, 5, 6, 7 componentes y somatotipo)
-- 2. Perímetros adicionales (6 campos)
-- 3. Diámetros óseos (6 campos)
-- 4. Pliegues cutáneos adicionales (3 campos)
-- =====================================================

-- Verificar si los campos ya existen antes de agregarlos
SET @col_exists = 0;

-- =====================================================
-- 1. ALTURA SENTADO
-- =====================================================
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'altura_sentado';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `altura_sentado` DECIMAL(5,2) NULL COMMENT ''Altura sentado en cm'' AFTER `altura_actual`',
    'SELECT ''Campo altura_sentado ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 2. PERÍMETROS/CIRCUNFERENCIAS ADICIONALES
-- =====================================================

-- Brazo relajado
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_brazo_relajado';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_brazo_relajado` DECIMAL(5,2) NULL COMMENT ''Brazo relajado en cm'' AFTER `circunferencia_cadera`',
    'SELECT ''Campo circunferencia_brazo_relajado ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Brazo contraído
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_brazo_contraido';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_brazo_contraido` DECIMAL(5,2) NULL COMMENT ''Brazo contraído/flexionado en cm'' AFTER `circunferencia_brazo_relajado`',
    'SELECT ''Campo circunferencia_brazo_contraido ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Muslo medio
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_muslo_medio';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_muslo_medio` DECIMAL(5,2) NULL COMMENT ''Muslo medio en cm'' AFTER `circunferencia_brazo_contraido`',
    'SELECT ''Campo circunferencia_muslo_medio ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Pantorrilla
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_pantorrilla';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_pantorrilla` DECIMAL(5,2) NULL COMMENT ''Pantorrilla en cm'' AFTER `circunferencia_muslo_medio`',
    'SELECT ''Campo circunferencia_pantorrilla ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cuello
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_cuello';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_cuello` DECIMAL(5,2) NULL COMMENT ''Cuello en cm'' AFTER `circunferencia_pantorrilla`',
    'SELECT ''Campo circunferencia_cuello ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tórax
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'circunferencia_torax';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `circunferencia_torax` DECIMAL(5,2) NULL COMMENT ''Tórax/pecho en cm'' AFTER `circunferencia_cuello`',
    'SELECT ''Campo circunferencia_torax ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 3. DIÁMETROS ÓSEOS
-- =====================================================

-- Diámetro biacromial
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_biacromial';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_biacromial` DECIMAL(5,2) NULL COMMENT ''Diámetro biacromial (ancho hombros) en cm'' AFTER `circunferencia_torax`',
    'SELECT ''Campo diametro_biacromial ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Diámetro bi-iliocristal
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_bi_iliocristal';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_bi_iliocristal` DECIMAL(5,2) NULL COMMENT ''Diámetro bi-iliocristal (ancho cadera) en cm'' AFTER `diametro_biacromial`',
    'SELECT ''Campo diametro_bi_iliocristal ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Diámetro húmero
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_humero';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_humero` DECIMAL(5,2) NULL COMMENT ''Diámetro biepicondilar húmero (codo) en cm'' AFTER `diametro_bi_iliocristal`',
    'SELECT ''Campo diametro_humero ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Diámetro fémur
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_femur';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_femur` DECIMAL(5,2) NULL COMMENT ''Diámetro biepicondilar fémur (rodilla) en cm'' AFTER `diametro_humero`',
    'SELECT ''Campo diametro_femur ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Diámetro muñeca
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_muneca';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_muneca` DECIMAL(5,2) NULL COMMENT ''Diámetro muñeca en cm'' AFTER `diametro_femur`',
    'SELECT ''Campo diametro_muneca ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Diámetro tobillo
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'diametro_tobillo';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `diametro_tobillo` DECIMAL(5,2) NULL COMMENT ''Diámetro tobillo en cm'' AFTER `diametro_muneca`',
    'SELECT ''Campo diametro_tobillo ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- 4. PLIEGUES CUTÁNEOS ADICIONALES
-- =====================================================

-- Pliegue pectoral
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'pliegue_pectoral';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `pliegue_pectoral` DECIMAL(5,2) NULL COMMENT ''Pliegue pectoral en mm'' AFTER `pliegue_pantorrilla_medial`',
    'SELECT ''Campo pliegue_pectoral ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Pliegue axilar medio
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'pliegue_axilar_medio';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `pliegue_axilar_medio` DECIMAL(5,2) NULL COMMENT ''Pliegue axilar medio en mm'' AFTER `pliegue_pectoral`',
    'SELECT ''Campo pliegue_axilar_medio ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Pliegue muslo medial
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME = 'pliegue_muslo_medial';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `historial_clinico` ADD COLUMN `pliegue_muslo_medial` DECIMAL(5,2) NULL COMMENT ''Pliegue medial muslo en mm'' AFTER `pliegue_axilar_medio`',
    'SELECT ''Campo pliegue_muslo_medial ya existe'' AS mensaje');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que todos los campos fueron agregados
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_TYPE,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME IN (
    'altura_sentado',
    'circunferencia_brazo_relajado',
    'circunferencia_brazo_contraido',
    'circunferencia_muslo_medio',
    'circunferencia_pantorrilla',
    'circunferencia_cuello',
    'circunferencia_torax',
    'diametro_biacromial',
    'diametro_bi_iliocristal',
    'diametro_humero',
    'diametro_femur',
    'diametro_muneca',
    'diametro_tobillo',
    'pliegue_pectoral',
    'pliegue_axilar_medio',
    'pliegue_muslo_medial'
  )
ORDER BY ORDINAL_POSITION;

-- =====================================================
-- RESUMEN
-- =====================================================
SELECT 
    'Campos agregados exitosamente' AS estado,
    COUNT(*) AS total_campos
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'historial_clinico'
  AND COLUMN_NAME IN (
    'altura_sentado',
    'circunferencia_brazo_relajado',
    'circunferencia_brazo_contraido',
    'circunferencia_muslo_medio',
    'circunferencia_pantorrilla',
    'circunferencia_cuello',
    'circunferencia_torax',
    'diametro_biacromial',
    'diametro_bi_iliocristal',
    'diametro_humero',
    'diametro_femur',
    'diametro_muneca',
    'diametro_tobillo',
    'pliegue_pectoral',
    'pliegue_axilar_medio',
    'pliegue_muslo_medial'
  );
