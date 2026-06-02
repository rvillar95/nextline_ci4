-- =============================================================================
-- Sistema Gimnasio (MVP) - Tablas dominio gym_*
-- Motor: MySQL 8.0+ / MariaDB 10.5+  |  Charset: utf8mb4
--
-- Nota: Este schema se integra a la BD principal del proyecto (p.ej. nextline_pyme)
-- Compatible con empresa.id y usuario.id (INT signed en NutriNext)
-- =============================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Catálogos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_grupo_muscular` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(80) NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_grupo_muscular_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gym_tipo_base_ejercicio` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(32) NOT NULL COMMENT 'peso_libre | maquina | peso_corporal | banda | cable | otro',
  `nombre` VARCHAR(80) NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_tipo_base_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Ejercicios (biblioteca por gimnasio/empresa)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_ejercicio` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `nombre` VARCHAR(160) NOT NULL,
  `grupo_muscular_principal_id` SMALLINT UNSIGNED NOT NULL,
  `grupo_muscular_secundario_id` SMALLINT UNSIGNED NULL,
  `tipo_base_id` SMALLINT UNSIGNED NOT NULL,
  `instrucciones` TEXT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_ejercicio_empresa` (`empresa_id`),
  KEY `idx_gym_ejercicio_principal` (`grupo_muscular_principal_id`),
  KEY `idx_gym_ejercicio_secundario` (`grupo_muscular_secundario_id`),
  KEY `idx_gym_ejercicio_tipo_base` (`tipo_base_id`),
  CONSTRAINT `fk_gym_ejercicio_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `fk_gym_ejercicio_gm_principal` FOREIGN KEY (`grupo_muscular_principal_id`) REFERENCES `gym_grupo_muscular` (`id`),
  CONSTRAINT `fk_gym_ejercicio_gm_secundario` FOREIGN KEY (`grupo_muscular_secundario_id`) REFERENCES `gym_grupo_muscular` (`id`),
  CONSTRAINT `fk_gym_ejercicio_tipo_base` FOREIGN KEY (`tipo_base_id`) REFERENCES `gym_tipo_base_ejercicio` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Rutinas y detalle ejercicios
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_rutina` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `creado_por_usuario_id` INT NOT NULL,
  `nombre` VARCHAR(160) NOT NULL,
  `descripcion` TEXT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_rutina_empresa` (`empresa_id`),
  KEY `idx_gym_rutina_creado_por` (`creado_por_usuario_id`),
  CONSTRAINT `fk_gym_rutina_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `fk_gym_rutina_usuario` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gym_rutina_ejercicio` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rutina_id` INT NOT NULL,
  `ejercicio_id` INT NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `series` SMALLINT UNSIGNED NULL,
  `repeticiones` VARCHAR(32) NULL COMMENT 'ej: 12, 8-10, AMRAP',
  `descanso_seg` SMALLINT UNSIGNED NULL,
  `notas` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_rutina_ejercicio_orden` (`rutina_id`, `orden`),
  KEY `idx_gym_rutina_ejercicio_rutina` (`rutina_id`),
  KEY `idx_gym_rutina_ejercicio_ejercicio` (`ejercicio_id`),
  CONSTRAINT `fk_gym_rutina_ejercicio_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `gym_rutina` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_rutina_ejercicio_ejercicio` FOREIGN KEY (`ejercicio_id`) REFERENCES `gym_ejercicio` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Programas y relación M:N con rutinas
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_programa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `creado_por_usuario_id` INT NOT NULL,
  `nombre` VARCHAR(160) NOT NULL,
  `descripcion` TEXT NULL,
  `duracion_semanas` SMALLINT UNSIGNED NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_programa_empresa` (`empresa_id`),
  KEY `idx_gym_programa_creado_por` (`creado_por_usuario_id`),
  CONSTRAINT `fk_gym_programa_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `fk_gym_programa_usuario` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gym_programa_rutina` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `programa_id` INT NOT NULL,
  `rutina_id` INT NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `dia_semana` TINYINT UNSIGNED NULL COMMENT '1=Lun ... 7=Dom',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_programa_rutina_orden` (`programa_id`, `orden`),
  UNIQUE KEY `uq_gym_programa_rutina_unique` (`programa_id`, `rutina_id`),
  KEY `idx_gym_programa_rutina_programa` (`programa_id`),
  KEY `idx_gym_programa_rutina_rutina` (`rutina_id`),
  CONSTRAINT `fk_gym_programa_rutina_programa` FOREIGN KEY (`programa_id`) REFERENCES `gym_programa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_programa_rutina_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `gym_rutina` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Asignación de programas a alumnos (usuario)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_programa_usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `programa_id` INT NOT NULL,
  `usuario_id` INT NOT NULL COMMENT 'Alumno (usuario.perfil_id = perfil Alumno)',
  `asignado_por_usuario_id` INT NOT NULL,
  `fecha_inicio` DATE NULL,
  `fecha_fin` DATE NULL,
  `estado` ENUM('activa','pausada','finalizada') NOT NULL DEFAULT 'activa',
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_programa_usuario_unique` (`programa_id`, `usuario_id`),
  KEY `idx_gym_programa_usuario_programa` (`programa_id`),
  KEY `idx_gym_programa_usuario_usuario` (`usuario_id`),
  CONSTRAINT `fk_gym_programa_usuario_programa` FOREIGN KEY (`programa_id`) REFERENCES `gym_programa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_programa_usuario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_programa_usuario_asignador` FOREIGN KEY (`asignado_por_usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Suscripciones mensuales (alumno)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gym_plan_suscripcion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `descripcion` TEXT NULL,
  `monto_mensual` DECIMAL(10,2) NOT NULL,
  `moneda` VARCHAR(8) NOT NULL DEFAULT 'CLP',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_plan_empresa` (`empresa_id`),
  CONSTRAINT `fk_gym_plan_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gym_suscripcion_alumno` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `plan_id` INT NOT NULL,
  `estado` ENUM('pendiente','activa','suspendida','cancelada','vencida') NOT NULL DEFAULT 'pendiente',
  `fecha_inicio` DATE NULL,
  `fecha_fin` DATE NULL,
  `fecha_proximo_pago` DATE NULL,
  `renovacion_automatica` TINYINT(1) NOT NULL DEFAULT 0,
  `mp_preapproval_id` VARCHAR(255) NULL,
  `mp_ultimo_payment_id` VARCHAR(255) NULL,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_suscripcion_usuario` (`usuario_id`),
  KEY `idx_gym_suscripcion_empresa` (`empresa_id`),
  KEY `idx_gym_suscripcion_plan` (`plan_id`),
  CONSTRAINT `fk_gym_suscripcion_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `fk_gym_suscripcion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_suscripcion_plan` FOREIGN KEY (`plan_id`) REFERENCES `gym_plan_suscripcion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gym_pago_suscripcion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  `plan_id` INT NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `moneda` VARCHAR(8) NOT NULL DEFAULT 'CLP',
  `estado` ENUM('pendiente','aprobado','rechazado','cancelado','devuelto') NOT NULL DEFAULT 'pendiente',
  `mp_preference_id` VARCHAR(255) NULL,
  `mp_payment_id` VARCHAR(255) NULL,
  `external_reference` VARCHAR(64) NULL,
  `detalle` TEXT NULL,
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gym_pago_empresa` (`empresa_id`),
  KEY `idx_gym_pago_usuario` (`usuario_id`),
  KEY `idx_gym_pago_plan` (`plan_id`),
  KEY `idx_gym_pago_mp_payment` (`mp_payment_id`),
  CONSTRAINT `fk_gym_pago_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `fk_gym_pago_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gym_pago_plan` FOREIGN KEY (`plan_id`) REFERENCES `gym_plan_suscripcion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Seeds mínimos catálogos
-- -----------------------------------------------------------------------------
INSERT INTO `gym_tipo_base_ejercicio` (`codigo`, `nombre`, `orden`, `activo`) VALUES
  ('peso_libre', 'Peso libre', 1, 1),
  ('maquina', 'Máquina', 2, 1),
  ('peso_corporal', 'Peso corporal', 3, 1),
  ('banda', 'Banda', 4, 1),
  ('cable', 'Cable', 5, 1),
  ('otro', 'Otro', 99, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `orden` = VALUES(`orden`), `activo` = VALUES(`activo`);

INSERT INTO `gym_grupo_muscular` (`nombre`, `orden`, `activo`) VALUES
  ('Pecho', 1, 1),
  ('Espalda', 2, 1),
  ('Hombros', 3, 1),
  ('Bíceps', 4, 1),
  ('Tríceps', 5, 1),
  ('Piernas', 6, 1),
  ('Glúteos', 7, 1),
  ('Core', 8, 1),
  ('Cardio', 9, 1),
  ('Cuerpo completo', 10, 1)
ON DUPLICATE KEY UPDATE `orden` = VALUES(`orden`), `activo` = VALUES(`activo`);

SET FOREIGN_KEY_CHECKS = 1;

