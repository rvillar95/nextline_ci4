-- =====================================================
-- TABLA DE CONFIGURACIONES DEL SISTEMA POR USUARIO
-- =====================================================
-- Permite a cada nutricionista personalizar el comportamiento
-- del sistema según sus preferencias
-- =====================================================

DROP TABLE IF EXISTS `usuario_configuraciones`;
CREATE TABLE IF NOT EXISTS `usuario_configuraciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL COMMENT 'ID del usuario (nutricionista)',
  `enviar_whatsapp` tinyint(1) DEFAULT 1 COMMENT '1 = enviar WhatsApp cuando se confirma cita, 0 = no enviar',
  `enviar_email` tinyint(1) DEFAULT 1 COMMENT '1 = enviar email cuando se agenda cita, 0 = no enviar',
  `crear_evento_calendario` tinyint(1) DEFAULT 1 COMMENT '1 = crear evento en calendario cuando se confirma, 0 = no crear',
  `agregar_paciente_como_invitado` tinyint(1) DEFAULT 1 COMMENT '1 = agregar paciente como invitado al evento, 0 = no agregar',
  `enviar_recordatorios_whatsapp` tinyint(1) DEFAULT 1 COMMENT '1 = enviar recordatorios por WhatsApp, 0 = no enviar',
  `horas_antes_recordatorio` int DEFAULT 24 COMMENT 'Horas antes de la cita para enviar recordatorio',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_config` (`usuario_id`),
  KEY `idx_usuario` (`usuario_id`),
  CONSTRAINT `fk_config_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- INSERTAR CONFIGURACIONES POR DEFECTO PARA USUARIOS EXISTENTES
-- =====================================================
-- Esto crea configuraciones por defecto (todo activado) para
-- los nutricionistas que ya existen en el sistema
-- =====================================================

INSERT INTO `usuario_configuraciones` (`usuario_id`, `enviar_whatsapp`, `enviar_email`, `crear_evento_calendario`, `agregar_paciente_como_invitado`, `enviar_recordatorios_whatsapp`, `horas_antes_recordatorio`)
SELECT 
    u.id,
    1 as enviar_whatsapp,
    1 as enviar_email,
    1 as crear_evento_calendario,
    1 as agregar_paciente_como_invitado,
    1 as enviar_recordatorios_whatsapp,
    24 as horas_antes_recordatorio
FROM usuario u
WHERE u.estado = 'A'
  AND NOT EXISTS (
      SELECT 1 
      FROM usuario_configuraciones uc 
      WHERE uc.usuario_id = u.id
  );

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se crearon las configuraciones
SELECT 
    uc.id,
    uc.usuario_id,
    u.nombre,
    u.apellido,
    uc.enviar_whatsapp,
    uc.enviar_email,
    uc.crear_evento_calendario,
    uc.agregar_paciente_como_invitado,
    uc.enviar_recordatorios_whatsapp,
    uc.horas_antes_recordatorio
FROM usuario_configuraciones uc
JOIN usuario u ON u.id = uc.usuario_id
ORDER BY uc.usuario_id;
