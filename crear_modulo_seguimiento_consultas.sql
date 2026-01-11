-- =====================================================
-- MÓDULO DE SEGUIMIENTO DE CONSULTAS
-- =====================================================
-- Este script agrega campos a detalle_agenda para
-- permitir seguimiento completo de las consultas:
-- - Notas de lo hablado durante la consulta
-- - Objetivos establecidos
-- - Control de inicio/fin real de consulta
-- - Duración real de la consulta
-- =====================================================

-- Agregar campos de seguimiento de consulta a detalle_agenda
ALTER TABLE `detalle_agenda`
ADD COLUMN `fecha_inicio_real` DATETIME NULL COMMENT 'Fecha y hora real en que inició la consulta' AFTER `notas_nutricionista`,
ADD COLUMN `fecha_fin_real` DATETIME NULL COMMENT 'Fecha y hora real en que terminó la consulta' AFTER `fecha_inicio_real`,
ADD COLUMN `duracion_real` INT NULL COMMENT 'Duración real de la consulta en minutos' AFTER `fecha_fin_real`,
ADD COLUMN `notas_consulta` TEXT NULL COMMENT 'Notas de lo hablado durante la consulta' AFTER `duracion_real`,
ADD COLUMN `objetivos` TEXT NULL COMMENT 'Objetivos establecidos en la consulta' AFTER `notas_consulta`,
ADD COLUMN `plan_alimentacion` TEXT NULL COMMENT 'Plan de alimentación acordado' AFTER `objetivos`,
ADD COLUMN `recomendaciones` TEXT NULL COMMENT 'Recomendaciones adicionales' AFTER `plan_alimentacion`,
ADD COLUMN `proxima_cita_recomendada` DATE NULL COMMENT 'Fecha recomendada para próxima cita' AFTER `recomendaciones`,
ADD INDEX `idx_fecha_inicio_real` (`fecha_inicio_real`),
ADD INDEX `idx_estado_cita_activa` (`estado_cita`, `fecha_inicio_real`);

-- =====================================================
-- CREAR TABLA DE NOTIFICACIONES (opcional, para historial)
-- =====================================================
CREATE TABLE IF NOT EXISTS `notificaciones_agenda` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `detalle_agenda_id` INT NOT NULL,
  `tipo` ENUM('recordatorio_proxima', 'consulta_iniciada', 'consulta_finalizada', 'recordatorio_objetivos') NOT NULL,
  `mensaje` TEXT NULL,
  `enviado` TINYINT(1) DEFAULT '0',
  `fecha_envio` DATETIME NULL,
  `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_detalle_agenda` (`detalle_agenda_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_enviado` (`enviado`),
  CONSTRAINT `fk_notif_detalle_agenda` FOREIGN KEY (`detalle_agenda_id`) REFERENCES `detalle_agenda` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregaron los campos
DESCRIBE `detalle_agenda`;

-- Verificar tabla de notificaciones
DESCRIBE `notificaciones_agenda`;
