-- =====================================================
-- SCRIPT PARA SIMPLIFICAR ESTRUCTURA DE AGENDA
-- =====================================================
-- Este script:
-- 1. Agrega usuario_id (nutricionista) a tabla agenda
-- 2. Agrega fecha a tabla detalle_agenda
-- 3. Agrega campos de paciente a detalle_agenda
-- 4. Migra datos de agenda_paciente a detalle_agenda
-- 5. Elimina tabla agenda_paciente (opcional, comentado)
-- =====================================================

-- PASO 1: Agregar usuario_id (nutricionista) a tabla agenda
ALTER TABLE `agenda` 
ADD COLUMN `usuario_id` INT NULL AFTER `tipo_id`,
ADD INDEX `idx_usuario_agenda` (`usuario_id`),
ADD CONSTRAINT `fk_agenda_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

-- Actualizar agendas existentes con el usuario_id del detalle_agenda
UPDATE `agenda` a
INNER JOIN `detalle_agenda` da ON da.agenda_id = a.id
SET a.usuario_id = da.usuario_id
WHERE a.usuario_id IS NULL
LIMIT 1;

-- PASO 2: Agregar fecha a tabla detalle_agenda
ALTER TABLE `detalle_agenda` 
ADD COLUMN `fecha` DATE NULL AFTER `agenda_id`,
ADD INDEX `idx_fecha_detalle` (`fecha`);

-- Actualizar fechas existentes desde agenda
UPDATE `detalle_agenda` da
INNER JOIN `agenda` a ON a.id = da.agenda_id
SET da.fecha = a.fecha
WHERE da.fecha IS NULL;

-- PASO 3: Agregar campos de paciente a detalle_agenda
ALTER TABLE `detalle_agenda`
ADD COLUMN `paciente_id` INT NULL AFTER `fecha`,
ADD COLUMN `tipo_consulta` ENUM('primera_vez','control','seguimiento','emergencia') NULL DEFAULT 'control' AFTER `paciente_id`,
ADD COLUMN `motivo` TEXT NULL COMMENT 'Motivo de la consulta' AFTER `tipo_consulta`,
ADD COLUMN `estado_cita` ENUM('agendada','confirmada','en_proceso','completada','cancelada','no_asistio') NULL DEFAULT 'agendada' AFTER `motivo`,
ADD COLUMN `fecha_confirmacion` DATETIME NULL AFTER `estado_cita`,
ADD COLUMN `fecha_cancelacion` DATETIME NULL AFTER `fecha_confirmacion`,
ADD COLUMN `motivo_cancelacion` TEXT NULL AFTER `fecha_cancelacion`,
ADD COLUMN `recordatorio_enviado` TINYINT(1) DEFAULT '0' AFTER `motivo_cancelacion`,
ADD COLUMN `fecha_recordatorio` DATETIME NULL AFTER `recordatorio_enviado`,
ADD COLUMN `observaciones` TEXT NULL AFTER `fecha_recordatorio`,
ADD INDEX `idx_paciente_detalle` (`paciente_id`),
ADD INDEX `idx_estado_cita` (`estado_cita`),
ADD CONSTRAINT `fk_detalle_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL;

-- PASO 4: Migrar datos de agenda_paciente a detalle_agenda
UPDATE `detalle_agenda` da
INNER JOIN `agenda_paciente` ap ON ap.detalle_agenda_id = da.id
SET 
    da.paciente_id = ap.paciente_id,
    da.tipo_consulta = ap.tipo_consulta,
    da.motivo = ap.motivo,
    da.estado_cita = ap.estado_cita,
    da.fecha_confirmacion = ap.fecha_confirmacion,
    da.fecha_cancelacion = ap.fecha_cancelacion,
    da.motivo_cancelacion = ap.motivo_cancelacion,
    da.recordatorio_enviado = ap.recordatorio_enviado,
    da.fecha_recordatorio = ap.fecha_recordatorio,
    da.observaciones = ap.observaciones;

-- PASO 5: Actualizar estado en detalle_agenda según si tiene paciente o no
-- Si tiene paciente, el estado del bloque es "ocupado" (estado = 2)
-- Si no tiene paciente, el estado es "disponible" (estado = 1)
UPDATE `detalle_agenda`
SET `estado` = CASE 
    WHEN `paciente_id` IS NOT NULL THEN 2  -- Ocupado
    ELSE 1  -- Disponible
END;

-- PASO 6: (OPCIONAL) Eliminar tabla agenda_paciente
-- Descomentar solo después de verificar que todo funciona correctamente
-- DROP TABLE IF EXISTS `agenda_paciente`;

-- =====================================================
-- VERIFICACIONES
-- =====================================================

-- Verificar que todas las agendas tengan usuario_id
SELECT COUNT(*) as agendas_sin_usuario 
FROM agenda 
WHERE usuario_id IS NULL;

-- Verificar que todos los detalle_agenda tengan fecha
SELECT COUNT(*) as detalles_sin_fecha 
FROM detalle_agenda 
WHERE fecha IS NULL;

-- Verificar migración de datos
SELECT 
    (SELECT COUNT(*) FROM agenda_paciente) as citas_en_agenda_paciente,
    (SELECT COUNT(*) FROM detalle_agenda WHERE paciente_id IS NOT NULL) as citas_en_detalle_agenda;
