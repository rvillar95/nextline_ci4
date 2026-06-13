-- =============================================================================
-- Solo crear tabla `contacto` (bases ya creadas con scripts anteriores).
-- Ejecutar una vez si no va a re-correr schema_red_abogado_perfil_gcp.sql
-- =============================================================================

SET NAMES utf8mb4;

USE `red_profesionales_judiciales`;

CREATE TABLE IF NOT EXISTS `contacto` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `abogado_id`         INT UNSIGNED NOT NULL,
  `nombre`             VARCHAR(120) NOT NULL,
  `apellido`           VARCHAR(120) NOT NULL,
  `correo`             VARCHAR(150) NOT NULL,
  `telefono`           VARCHAR(30) NOT NULL COMMENT 'Teléfono del visitante',
  `consentimiento_datos` TINYINT(1) NOT NULL DEFAULT 0,
  `canal_redireccion`  ENUM('whatsapp','telefono') NOT NULL DEFAULT 'whatsapp',
  `destino_e164`       VARCHAR(20) NULL COMMENT 'Número del abogado usado en el enlace',
  `estado_seguimiento` ENUM('nuevo','visto','en_gestion','cerrado') NOT NULL DEFAULT 'nuevo',
  `notas_internas`     VARCHAR(500) NULL,
  `ip_origen`          VARCHAR(45) NULL,
  `user_agent`         VARCHAR(400) NULL,
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacto_abogado_fecha` (`abogado_id`, `fcreacion`),
  KEY `idx_contacto_estado` (`estado_seguimiento`),
  CONSTRAINT `fk_contacto_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
