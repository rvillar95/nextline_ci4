-- =====================================================
-- MIGRAR CONFIGURACIONES DE USUARIO A EMPRESA
-- =====================================================
-- Este script migra las configuraciones de usuario_configuraciones
-- a empresa_configuraciones, para que sean por empresa en lugar de por usuario
-- =====================================================

-- Paso 1: Crear nueva tabla empresa_configuraciones
DROP TABLE IF EXISTS `empresa_configuraciones`;
CREATE TABLE IF NOT EXISTS `empresa_configuraciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID de la empresa',
  `enviar_whatsapp` tinyint(1) DEFAULT 1 COMMENT '1 = enviar WhatsApp cuando se confirma cita, 0 = no enviar',
  `enviar_email` tinyint(1) DEFAULT 1 COMMENT '1 = enviar email cuando se agenda cita, 0 = no enviar',
  `crear_evento_calendario` tinyint(1) DEFAULT 1 COMMENT '1 = crear evento en calendario cuando se confirma, 0 = no crear',
  `agregar_paciente_como_invitado` tinyint(1) DEFAULT 1 COMMENT '1 = agregar paciente como invitado al evento, 0 = no agregar',
  `enviar_recordatorios_whatsapp` tinyint(1) DEFAULT 1 COMMENT '1 = enviar recordatorios por WhatsApp, 0 = no enviar',
  `horas_antes_recordatorio` int DEFAULT 24 COMMENT 'Horas antes de la cita para enviar recordatorio',
  `mensaje_cancelacion_pendiente` TEXT NULL COMMENT 'Mensaje HTML para cancelar citas en estado pendiente',
  `mensaje_cancelacion_confirmada` TEXT NULL COMMENT 'Mensaje HTML para cancelar citas en estado confirmada',
  `mensaje_cancelacion_en_proceso` TEXT NULL COMMENT 'Mensaje HTML para cancelar citas en estado en_proceso',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_config` (`empresa_id`),
  KEY `idx_empresa` (`empresa_id`),
  CONSTRAINT `fk_config_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Paso 2: Migrar datos de usuario_configuraciones a empresa_configuraciones
-- Para cada empresa, tomamos la configuración del primer usuario (o la más común)
INSERT INTO `empresa_configuraciones` (
    `empresa_id`,
    `enviar_whatsapp`,
    `enviar_email`,
    `crear_evento_calendario`,
    `agregar_paciente_como_invitado`,
    `enviar_recordatorios_whatsapp`,
    `horas_antes_recordatorio`,
    `mensaje_cancelacion_pendiente`,
    `mensaje_cancelacion_confirmada`,
    `mensaje_cancelacion_en_proceso`
)
SELECT DISTINCT
    u.empresa_id,
    COALESCE(MAX(uc.enviar_whatsapp), 1) AS enviar_whatsapp,
    COALESCE(MAX(uc.enviar_email), 1) AS enviar_email,
    COALESCE(MAX(uc.crear_evento_calendario), 1) AS crear_evento_calendario,
    COALESCE(MAX(uc.agregar_paciente_como_invitado), 1) AS agregar_paciente_como_invitado,
    COALESCE(MAX(uc.enviar_recordatorios_whatsapp), 1) AS enviar_recordatorios_whatsapp,
    COALESCE(MAX(uc.horas_antes_recordatorio), 24) AS horas_antes_recordatorio,
    MAX(uc.mensaje_cancelacion_pendiente) AS mensaje_cancelacion_pendiente,
    MAX(uc.mensaje_cancelacion_confirmada) AS mensaje_cancelacion_confirmada,
    MAX(uc.mensaje_cancelacion_en_proceso) AS mensaje_cancelacion_en_proceso
FROM `usuario` u
LEFT JOIN `usuario_configuraciones` uc ON uc.usuario_id = u.id
WHERE u.empresa_id IS NOT NULL
GROUP BY u.empresa_id
ON DUPLICATE KEY UPDATE
    `enviar_whatsapp` = VALUES(`enviar_whatsapp`),
    `enviar_email` = VALUES(`enviar_email`),
    `crear_evento_calendario` = VALUES(`crear_evento_calendario`),
    `agregar_paciente_como_invitado` = VALUES(`agregar_paciente_como_invitado`),
    `enviar_recordatorios_whatsapp` = VALUES(`enviar_recordatorios_whatsapp`),
    `horas_antes_recordatorio` = VALUES(`horas_antes_recordatorio`),
    `mensaje_cancelacion_pendiente` = VALUES(`mensaje_cancelacion_pendiente`),
    `mensaje_cancelacion_confirmada` = VALUES(`mensaje_cancelacion_confirmada`),
    `mensaje_cancelacion_en_proceso` = VALUES(`mensaje_cancelacion_en_proceso`),
    `factualizacion` = NOW();

-- Paso 3: Crear configuraciones por defecto para empresas que no tienen usuarios con configuraciones
INSERT INTO `empresa_configuraciones` (
    `empresa_id`,
    `enviar_whatsapp`,
    `enviar_email`,
    `crear_evento_calendario`,
    `agregar_paciente_como_invitado`,
    `enviar_recordatorios_whatsapp`,
    `horas_antes_recordatorio`
)
SELECT 
    e.id AS empresa_id,
    1 AS enviar_whatsapp,
    1 AS enviar_email,
    1 AS crear_evento_calendario,
    1 AS agregar_paciente_como_invitado,
    1 AS enviar_recordatorios_whatsapp,
    24 AS horas_antes_recordatorio
FROM `empresa` e
WHERE e.estado = 'A'
  AND e.id NOT IN (SELECT empresa_id FROM `empresa_configuraciones`)
ON DUPLICATE KEY UPDATE
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que todas las empresas activas tienen configuración
SELECT 
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    CASE 
        WHEN ec.id IS NULL THEN '❌ Sin configuración'
        ELSE '✅ Con configuración'
    END AS estado_configuracion,
    ec.enviar_whatsapp,
    ec.enviar_email,
    ec.crear_evento_calendario
FROM `empresa` e
LEFT JOIN `empresa_configuraciones` ec ON ec.empresa_id = e.id
WHERE e.estado = 'A'
ORDER BY e.id;

-- Contar configuraciones migradas
SELECT 
    COUNT(*) AS total_empresas_activas,
    SUM(CASE WHEN ec.id IS NOT NULL THEN 1 ELSE 0 END) AS empresas_con_configuracion,
    SUM(CASE WHEN ec.id IS NULL THEN 1 ELSE 0 END) AS empresas_sin_configuracion
FROM `empresa` e
LEFT JOIN `empresa_configuraciones` ec ON ec.empresa_id = e.id
WHERE e.estado = 'A';

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================
-- 1. Después de ejecutar este script, actualizar:
--    - app/Models/UsuarioConfiguracion.php -> EmpresaConfiguracion.php
--    - app/Controllers/Dashboard/ConfiguracionController.php
--    - Todas las referencias a UsuarioConfiguracion en el código
--
-- 2. La tabla usuario_configuraciones puede mantenerse como backup
--    o eliminarse después de verificar que todo funciona correctamente
--
-- 3. Si se elimina usuario_configuraciones, ejecutar:
--    DROP TABLE IF EXISTS `usuario_configuraciones`;
-- =====================================================
