-- --------------------------------------------------------
-- Tabla: usuario_calendar_tokens
-- Descripción: Almacena tokens de OAuth2 para integración con calendarios (Google Calendar, Outlook)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `usuario_calendar_tokens`;
CREATE TABLE IF NOT EXISTS `usuario_calendar_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL COMMENT 'ID del usuario (nutricionista)',
  `provider` enum('google','outlook') NOT NULL DEFAULT 'google' COMMENT 'Proveedor de calendario',
  `access_token` text NOT NULL COMMENT 'Token de acceso OAuth2',
  `refresh_token` text DEFAULT NULL COMMENT 'Token de refresco OAuth2',
  `expires_at` datetime NOT NULL COMMENT 'Fecha de expiración del token',
  `token_type` varchar(50) DEFAULT 'Bearer' COMMENT 'Tipo de token',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_provider` (`usuario_id`, `provider`),
  KEY `idx_usuario` (`usuario_id`),
  CONSTRAINT `fk_calendar_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Agregar columna calendar_event_id a detalle_agenda
-- --------------------------------------------------------
ALTER TABLE `detalle_agenda` 
ADD COLUMN `calendar_event_id` varchar(255) DEFAULT NULL COMMENT 'ID del evento en el calendario (Google/Outlook)' AFTER `agenda_id`;

-- Índice para búsquedas rápidas
ALTER TABLE `detalle_agenda`
ADD KEY `idx_calendar_event` (`calendar_event_id`);
