-- =====================================================
-- AGREGAR CAMPOS DE MEDICIONES Y PLIEGUES A HISTORIAL_CLINICO
-- =====================================================
-- Este script agrega:
-- 1. detalle_agenda_id para vincular con la cita específica
-- 2. Campos para pliegues cutáneos (medición con plicómetro)
-- =====================================================

-- Agregar detalle_agenda_id para vincular con la cita específica
ALTER TABLE `historial_clinico`
ADD COLUMN `detalle_agenda_id` INT NULL COMMENT 'ID del detalle_agenda (cita específica) relacionado' AFTER `agenda_id`,
ADD INDEX `idx_detalle_agenda_historial` (`detalle_agenda_id`),
ADD CONSTRAINT `fk_historial_detalle_agenda` FOREIGN KEY (`detalle_agenda_id`) REFERENCES `detalle_agenda` (`id`) ON DELETE SET NULL;

-- Agregar campos para pliegues cutáneos (medición con plicómetro)
-- Los pliegues se miden en milímetros (mm)
ALTER TABLE `historial_clinico`
ADD COLUMN `pliegue_tricipital` DECIMAL(5,2) NULL COMMENT 'Pliegue tricipital en mm' AFTER `masa_muscular`,
ADD COLUMN `pliegue_bicipital` DECIMAL(5,2) NULL COMMENT 'Pliegue bicipital en mm' AFTER `pliegue_tricipital`,
ADD COLUMN `pliegue_subescapular` DECIMAL(5,2) NULL COMMENT 'Pliegue subescapular en mm' AFTER `pliegue_bicipital`,
ADD COLUMN `pliegue_suprailíaco` DECIMAL(5,2) NULL COMMENT 'Pliegue suprailíaco en mm' AFTER `pliegue_subescapular`,
ADD COLUMN `pliegue_abdominal` DECIMAL(5,2) NULL COMMENT 'Pliegue abdominal en mm' AFTER `pliegue_suprailíaco`,
ADD COLUMN `pliegue_muslo_anterior` DECIMAL(5,2) NULL COMMENT 'Pliegue del muslo anterior en mm' AFTER `pliegue_abdominal`,
ADD COLUMN `pliegue_pantorrilla_medial` DECIMAL(5,2) NULL COMMENT 'Pliegue de la pantorrilla medial en mm' AFTER `pliegue_muslo_anterior`,
ADD COLUMN `suma_pliegues` DECIMAL(6,2) NULL COMMENT 'Suma de todos los pliegues medidos (mm)' AFTER `pliegue_pantorrilla_medial`,
ADD COLUMN `grasa_corporal_calculada` DECIMAL(5,2) NULL COMMENT 'Porcentaje de grasa corporal calculado a partir de pliegues' AFTER `suma_pliegues`;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
DESCRIBE `historial_clinico`;

-- Verificar que la relación funciona
SELECT 
    hc.id,
    hc.paciente_id,
    hc.detalle_agenda_id,
    da.hora_inicio,
    da.hora_fin
FROM historial_clinico hc
LEFT JOIN detalle_agenda da ON da.id = hc.detalle_agenda_id
LIMIT 5;
