-- =============================================================================
-- Red de Profesionales Judiciales (referencia funcional tipo Perired)
-- Motor: MySQL 8.0+ / MariaDB 10.5+  |  Charset: utf8mb4
-- Ejecutar: mysql -u root -p < database/schema_red_profesionales_judiciales.sql
--
-- Cobertura requerimientos (manual RF):
--   RF-01  -> rj_tipo_profesional.descripcion_publica
--   RF-02  -> rj_usuario + rj_profesional
--   RF-03  -> rj_profesional_documento
--   RF-04  -> rj_profesional (+ secundario)
--   RF-05  -> rj_profesional lat/long, direccion_referencia
--   RF-06  -> filtros en rj_solicitud + índices; búsqueda listados en app
--   RF-07  -> rj_solicitud (datos contacto + caso)
--   RF-08  -> rj_solicitud.estado, confirmacion_enviada, rj_solicitud_evento
--   RF-09  -> rj_mensaje (MVP); teléfono/correo en perfiles según política
--   RF-10  -> rj_resena + estado_moderacion
--   RF-11  -> vista vj_profesionales_por_region_tipo
--   RF-12  -> rj_usuario tipo administrador + revisiones en rj_profesional
--
-- Prefijo tablas: rj_  |  Base de datos: red_profesionales_judiciales
-- (independiente de tablas legacy del proyecto; integre vía segundo schema o DSN)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `red_profesionales_judiciales`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `red_profesionales_judiciales`;

-- -----------------------------------------------------------------------------
-- Catálogos
-- -----------------------------------------------------------------------------

DROP VIEW IF EXISTS `vj_profesionales_por_region_tipo`;
DROP TABLE IF EXISTS `rj_mensaje`;
DROP TABLE IF EXISTS `rj_resena`;
DROP TABLE IF EXISTS `rj_solicitud_evento`;
DROP TABLE IF EXISTS `rj_solicitud_profesional`;
DROP TABLE IF EXISTS `rj_solicitud`;
DROP TABLE IF EXISTS `rj_consulta_general`;
DROP TABLE IF EXISTS `rj_profesional_documento`;
DROP TABLE IF EXISTS `rj_profesional_secundario`;
DROP TABLE IF EXISTS `rj_profesional`;
DROP TABLE IF EXISTS `rj_usuario`;
DROP TABLE IF EXISTS `rj_contenido_legal`;
DROP TABLE IF EXISTS `rj_tipo_profesional`;
DROP TABLE IF EXISTS `rj_region`;

CREATE TABLE `rj_region` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`        VARCHAR(10) NOT NULL COMMENT 'Ej: RM, VIII',
  `numero`        TINYINT UNSIGNED NOT NULL COMMENT 'Orden geográfico norte a sur (1-16), único',
  `nombre`        VARCHAR(100) NOT NULL,
  `activo`        TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_region_codigo` (`codigo`),
  UNIQUE KEY `uq_rj_region_numero` (`numero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_tipo_profesional` (
  `id`                 SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`             VARCHAR(32) NOT NULL COMMENT 'receptor, perito, abogado, procurador, mediador',
  `nombre`             VARCHAR(80) NOT NULL,
  `descripcion_publica` TEXT NOT NULL COMMENT 'RF-01: qué hace el profesional, lenguaje claro',
  `orden`              SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `activo`             TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_tipo_profesional_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Usuarios (solicitante, profesional, administrador)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_usuario` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `correo`          VARCHAR(150) NOT NULL,
  `clave`           VARCHAR(255) NOT NULL COMMENT 'Hash (password_hash)',
  `nombre`          VARCHAR(100) NOT NULL,
  `apellido`        VARCHAR(100) NOT NULL,
  `telefono`        VARCHAR(30) NOT NULL,
  `tipo_cuenta`     ENUM('solicitante','profesional','administrador') NOT NULL,
  `estado`          CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'A=activo, I=inactivo',
  `consentimiento_contacto` TINYINT(1) NOT NULL DEFAULT 0,
  `terminos_aceptados_en`   DATETIME NULL,
  `fcreacion`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_usuario_correo` (`correo`),
  KEY `idx_rj_usuario_tipo_estado` (`tipo_cuenta`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Perfil profesional (RF-02, RF-04, RF-05) + verificación (RF-12)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_profesional` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`              INT UNSIGNED NOT NULL,
  `tipo_profesional_id`     SMALLINT UNSIGNED NOT NULL COMMENT 'Tipo principal',
  `rut`                     VARCHAR(12) NOT NULL COMMENT 'Chile: sin puntos, con guión DV',
  `estado_verificacion`     ENUM('pendiente','aprobado','rechazado','suspendido') NOT NULL DEFAULT 'pendiente',
  `rechazo_motivo`          VARCHAR(500) NULL,
  `revisado_por_usuario_id` INT UNSIGNED NULL,
  `revisado_en`             DATETIME NULL,
  `especialidad_principal`  VARCHAR(200) NULL,
  `habilidades`             TEXT NULL,
  `experiencia`             TEXT NULL,
  `menciones`               TEXT NULL,
  `tarifa_min`              DECIMAL(12,2) NULL,
  `tarifa_max`              DECIMAL(12,2) NULL,
  `moneda`                  CHAR(3) NOT NULL DEFAULT 'CLP',
  `plazo_entrega_dias`      SMALLINT UNSIGNED NULL COMMENT 'Plazo habitual informe / entrega',
  `region_id`               SMALLINT UNSIGNED NOT NULL,
  `direccion_referencia`    VARCHAR(300) NULL COMMENT 'Dirección aproximada para mapa RF-05',
  `comuna`                  VARCHAR(100) NULL,
  `latitud`                 DECIMAL(10,7) NULL,
  `longitud`                DECIMAL(10,7) NULL,
  `foto_path`               VARCHAR(500) NULL,
  `publicado`               TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Solo si aprobado + reglas negocio',
  `fcreacion`               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_profesional_usuario` (`usuario_id`),
  KEY `idx_rj_profesional_tipo_region` (`tipo_profesional_id`, `region_id`),
  KEY `idx_rj_profesional_estado_pub` (`estado_verificacion`, `publicado`),
  CONSTRAINT `fk_rj_profesional_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_profesional_tipo` FOREIGN KEY (`tipo_profesional_id`) REFERENCES `rj_tipo_profesional` (`id`),
  CONSTRAINT `fk_rj_profesional_region` FOREIGN KEY (`region_id`) REFERENCES `rj_region` (`id`),
  CONSTRAINT `fk_rj_profesional_revisor` FOREIGN KEY (`revisado_por_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_profesional_documento` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesional_id`   INT UNSIGNED NOT NULL,
  `tipo_documento`   ENUM('cv','certificado_titulo','otro') NOT NULL DEFAULT 'otro',
  `nombre_original`  VARCHAR(255) NOT NULL,
  `ruta_almacen`     VARCHAR(500) NOT NULL,
  `mime`             VARCHAR(120) NOT NULL,
  `tamano_bytes`     INT UNSIGNED NOT NULL,
  `fcreacion`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_prof_doc_prof` (`profesional_id`),
  CONSTRAINT `fk_rj_prof_doc_prof` FOREIGN KEY (`profesional_id`) REFERENCES `rj_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos adicionales del mismo profesional (opcional; RF permite varias facetas)
CREATE TABLE `rj_profesional_secundario` (
  `profesional_id`         INT UNSIGNED NOT NULL,
  `tipo_profesional_id`    SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`profesional_id`, `tipo_profesional_id`),
  CONSTRAINT `fk_rj_prof_sec_prof` FOREIGN KEY (`profesional_id`) REFERENCES `rj_profesional` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_prof_sec_tipo` FOREIGN KEY (`tipo_profesional_id`) REFERENCES `rj_tipo_profesional` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Solicitudes / cotización (RF-06, RF-07, RF-08)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_solicitud` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_seguimiento`     CHAR(12) NOT NULL COMMENT 'Generado por trigger si se inserta vacío',
  `solicitante_usuario_id` INT UNSIGNED NULL,
  `nombre_contacto`        VARCHAR(120) NOT NULL,
  `correo`                 VARCHAR(150) NOT NULL,
  `telefono`               VARCHAR(30) NOT NULL,
  `descripcion_caso`       TEXT NOT NULL,
  `tipo_profesional_id`    SMALLINT UNSIGNED NOT NULL,
  `region_id`              SMALLINT UNSIGNED NULL,
  `filtro_tarifa_max`      DECIMAL(12,2) NULL,
  `filtro_plazo_max_dias`  SMALLINT UNSIGNED NULL,
  `estado`                 ENUM('recibida','en_revision','derivada','respondida','cerrada','cancelada') NOT NULL DEFAULT 'recibida',
  `confirmacion_enviada`   TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'RF: confirmación recepción',
  `primera_respuesta_en`   DATETIME NULL COMMENT 'Indicador tiempo primera respuesta',
  `cerrada_en`             DATETIME NULL,
  `fcreacion`              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_solicitud_codigo` (`codigo_seguimiento`),
  KEY `idx_rj_solicitud_estado` (`estado`),
  KEY `idx_rj_solicitud_tipo_region` (`tipo_profesional_id`, `region_id`),
  CONSTRAINT `fk_rj_solicitud_usuario` FOREIGN KEY (`solicitante_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rj_solicitud_tipo` FOREIGN KEY (`tipo_profesional_id`) REFERENCES `rj_tipo_profesional` (`id`),
  CONSTRAINT `fk_rj_solicitud_region` FOREIGN KEY (`region_id`) REFERENCES `rj_region` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_solicitud_profesional` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `solicitud_id`     INT UNSIGNED NOT NULL,
  `profesional_id`   INT UNSIGNED NOT NULL,
  `rol`              ENUM('sugerido','elegido','asignado_admin') NOT NULL DEFAULT 'sugerido',
  `orden`            TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `notificado_en`    DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_sol_prof` (`solicitud_id`, `profesional_id`),
  KEY `idx_rj_sol_prof_prof` (`profesional_id`),
  CONSTRAINT `fk_rj_sol_prof_sol` FOREIGN KEY (`solicitud_id`) REFERENCES `rj_solicitud` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_sol_prof_prof` FOREIGN KEY (`profesional_id`) REFERENCES `rj_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_solicitud_evento` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `solicitud_id`  INT UNSIGNED NOT NULL,
  `usuario_id`    INT UNSIGNED NULL,
  `tipo`          VARCHAR(40) NOT NULL COMMENT 'cambio_estado, nota_interna, contacto, etc.',
  `detalle`       TEXT NULL,
  `fcreacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_sol_evt_sol` (`solicitud_id`),
  CONSTRAINT `fk_rj_sol_evt_sol` FOREIGN KEY (`solicitud_id`) REFERENCES `rj_solicitud` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_sol_evt_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Consulta general sitio (formulario “Resolveremos tus dudas” / RF genérico)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_consulta_general` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`       VARCHAR(120) NOT NULL,
  `correo`       VARCHAR(150) NOT NULL,
  `telefono`     VARCHAR(30) NOT NULL,
  `mensaje`      TEXT NOT NULL,
  `estado`       ENUM('nueva','en_gestion','cerrada') NOT NULL DEFAULT 'nueva',
  `fcreacion`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_consulta_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Reseñas (RF-10) + moderación (RF-12)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_resena` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `profesional_id`     INT UNSIGNED NOT NULL,
  `solicitud_id`       INT UNSIGNED NULL,
  `autor_usuario_id`   INT UNSIGNED NULL,
  `autor_nombre`       VARCHAR(120) NULL COMMENT 'Si no hay cuenta',
  `puntuacion`         TINYINT UNSIGNED NOT NULL COMMENT '1-5',
  `comentario`         TEXT NOT NULL,
  `estado_moderacion`  ENUM('pendiente','publicada','rechazada') NOT NULL DEFAULT 'pendiente',
  `moderador_id`     INT UNSIGNED NULL,
  `moderado_en`      DATETIME NULL,
  `fcreacion`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_resena_prof_estado` (`profesional_id`, `estado_moderacion`),
  CONSTRAINT `fk_rj_resena_prof` FOREIGN KEY (`profesional_id`) REFERENCES `rj_profesional` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_resena_sol` FOREIGN KEY (`solicitud_id`) REFERENCES `rj_solicitud` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rj_resena_autor` FOREIGN KEY (`autor_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rj_resena_mod` FOREIGN KEY (`moderador_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Mensajería simple entre usuarios ligada a solicitud (RF-09; MVP)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_mensaje` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `solicitud_id`     INT UNSIGNED NOT NULL,
  `de_usuario_id`    INT UNSIGNED NOT NULL,
  `para_usuario_id`  INT UNSIGNED NOT NULL,
  `cuerpo`           TEXT NOT NULL,
  `leido_en`         DATETIME NULL,
  `fcreacion`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_mensaje_sol` (`solicitud_id`),
  KEY `idx_rj_mensaje_para` (`para_usuario_id`, `leido_en`),
  CONSTRAINT `fk_rj_mensaje_sol` FOREIGN KEY (`solicitud_id`) REFERENCES `rj_solicitud` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_mensaje_de` FOREIGN KEY (`de_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_mensaje_para` FOREIGN KEY (`para_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Contenidos legales versionados (sección 8 manual: T&C, privacidad, etc.)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_contenido_legal` (
  `id`             SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`           VARCHAR(64) NOT NULL COMMENT 'terminos, privacidad, responsabilidad, membresia_profesional',
  `version`        INT UNSIGNED NOT NULL DEFAULT 1,
  `titulo`         VARCHAR(200) NOT NULL,
  `cuerpo_html`    MEDIUMTEXT NOT NULL,
  `vigente_desde`  DATE NOT NULL,
  `activo`         TINYINT(1) NOT NULL DEFAULT 1,
  `fcreacion`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_legal_slug_version` (`slug`, `version`),
  KEY `idx_rj_legal_slug_activo` (`slug`, `activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Vista útil: conteo público por región y tipo (RF-11)
-- -----------------------------------------------------------------------------

CREATE OR REPLACE VIEW `vj_profesionales_por_region_tipo` AS
SELECT
  r.id AS region_id,
  r.nombre AS region_nombre,
  t.id AS tipo_profesional_id,
  t.nombre AS tipo_profesional_nombre,
  COUNT(p.id) AS total_publicados
FROM `rj_region` r
CROSS JOIN `rj_tipo_profesional` t
LEFT JOIN `rj_profesional` p
  ON p.region_id = r.id
 AND p.tipo_profesional_id = t.id
 AND p.estado_verificacion = 'aprobado'
 AND p.publicado = 1
WHERE r.activo = 1 AND t.activo = 1
GROUP BY r.id, r.nombre, t.id, t.nombre;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Datos iniciales: 16 regiones de Chile (orden norte a sur; `numero` único 1-16)
-- -----------------------------------------------------------------------------

INSERT INTO `rj_region` (`codigo`, `numero`, `nombre`, `activo`) VALUES
('XV',   1,  'Región de Arica y Parinacota', 1),
('I',    2,  'Región de Tarapacá', 1),
('II',   3,  'Región de Antofagasta', 1),
('III',  4,  'Región de Atacama', 1),
('IV',   5,  'Región de Coquimbo', 1),
('V',    6,  'Región de Valparaíso', 1),
('RM',   7,  'Región Metropolitana de Santiago', 1),
('VI',   8,  "Región del Libertador General Bernardo O'Higgins", 1),
('VII',  9,  'Región del Maule', 1),
('XVI',  10, 'Región de Ñuble', 1),
('VIII', 11, 'Región del Biobío', 1),
('IX',   12, 'Región de La Araucanía', 1),
('XIV',  13, 'Región de Los Ríos', 1),
('X',    14, 'Región de Los Lagos', 1),
('XI',   15, 'Región de Aysén del General Carlos Ibáñez del Campo', 1),
('XII',  16, 'Región de Magallanes y de la Antártica Chilena', 1);

INSERT INTO `rj_tipo_profesional` (`codigo`, `nombre`, `descripcion_publica`, `orden`, `activo`) VALUES
('receptor', 'Receptor judicial', 'Ministro de fe que practica notificaciones, requerimientos y diligencias fuera de tribunal (embargos, lanzamientos, etc.).', 1, 1),
('perito', 'Perito', 'Experto que analiza hechos y emite dictamen técnico para ayudar al tribunal; puede ser de oficio o de parte.', 2, 1),
('abogado', 'Abogado', 'Representa y asesora en procedimientos judiciales y administrativos; redacta escritos y ejerce acciones legales.', 3, 1),
('procurador', 'Procurador judicial', 'Representación formal y gestión procedimental del expediente ante tribunales.', 4, 1),
('mediador', 'Mediador', 'Tercero imparcial que facilita acuerdos entre partes; instancia relevante en algunas materias.', 5, 1);

-- -----------------------------------------------------------------------------
-- Trigger: código de seguimiento al crear solicitud (si viene vacío)
-- -----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS `tr_rj_solicitud_bi`;

DELIMITER $$

CREATE TRIGGER `tr_rj_solicitud_bi`
BEFORE INSERT ON `rj_solicitud`
FOR EACH ROW
BEGIN
  IF NEW.`codigo_seguimiento` IS NULL OR TRIM(NEW.`codigo_seguimiento`) = '' THEN
    SET NEW.`codigo_seguimiento` = UPPER(SUBSTRING(REPLACE(UUID(), '-', ''), 1, 12));
  END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------------------------------
-- Fin
-- -----------------------------------------------------------------------------
