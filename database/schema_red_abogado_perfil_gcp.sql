-- =============================================================================
-- Extensión: perfil abogado, multi región/comuna, tribunales, estudios + GCP
-- Depende de: database/schema_red_profesionales_judiciales.sql (rj_usuario, rj_region)
-- Motor: MySQL 8.0+ / MariaDB 10.5+
--
-- Cuenta (punto 1): mismo `rj_usuario.correo` como identificador único; acceso
--   con contraseña y/o Google (`oauth_google_sub`). Regla en aplicación:
--   (clave IS NOT NULL) OR (oauth_google_sub IS NOT NULL).
--
-- Almacenamiento GCP (punto 5): solo metadatos en BD; el binario vive en el bucket.
--   Flujo típico: cliente pide URL firmada (upload) -> sube a GCS -> app inserta
--   rj_documento_gcs con bucket + object_key; descarga con URL firmada de lectura.
--   No guardar service account en repo; usar Secret Manager / env en runtime.
--
-- Tabla `contacto`: lead desde botón “Contactar” en perfil público del abogado
--   (nombre, apellido, correo, tel visitante) + seguimiento y copia del destino.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE `red_profesionales_judiciales`;

-- -----------------------------------------------------------------------------
-- Ajuste usuario: correo + contraseña opcional si hay Google
-- -----------------------------------------------------------------------------

ALTER TABLE `rj_usuario`
  MODIFY `clave` VARCHAR(255) NULL COMMENT 'password_hash; NULL si solo OAuth Google';

ALTER TABLE `rj_usuario`
  ADD COLUMN `oauth_google_sub` VARCHAR(255) NULL COMMENT 'Subject (sub) de Google OAuth' AFTER `correo`,
  ADD COLUMN `oauth_google_email` VARCHAR(150) NULL COMMENT 'Email devuelto por Google al vincular' AFTER `oauth_google_sub`,
  ADD COLUMN `oauth_vinculado_en` DATETIME NULL AFTER `oauth_google_email`;

ALTER TABLE `rj_usuario`
  ADD UNIQUE KEY `uq_rj_usuario_oauth_google_sub` (`oauth_google_sub`);

-- -----------------------------------------------------------------------------
-- Comunas (catálogo; poblar desde fuente oficial / carga masiva)
-- -----------------------------------------------------------------------------

DROP TABLE IF EXISTS `contacto`;
DROP TABLE IF EXISTS `rj_abogado_tribunal`;
DROP TABLE IF EXISTS `rj_abogado_estudio`;
DROP TABLE IF EXISTS `rj_abogado_comuna`;
DROP TABLE IF EXISTS `rj_abogado_region`;
DROP TABLE IF EXISTS `rj_abogado`;
DROP TABLE IF EXISTS `rj_documento_gcs`;
DROP TABLE IF EXISTS `rj_tribunal`;
DROP TABLE IF EXISTS `rj_tipo_estudio`;
DROP TABLE IF EXISTS `rj_comuna`;

CREATE TABLE `rj_comuna` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `region_id`     SMALLINT UNSIGNED NOT NULL,
  `codigo`        VARCHAR(20) NULL COMMENT 'Opcional: código oficial si existe',
  `nombre`        VARCHAR(120) NOT NULL,
  `activo`        TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_comuna_region_nombre` (`region_id`, `nombre`),
  KEY `idx_rj_comuna_region` (`region_id`),
  CONSTRAINT `fk_rj_comuna_region` FOREIGN KEY (`region_id`) REFERENCES `rj_region` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Tipo de estudio (catálogo)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_tipo_estudio` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(120) NOT NULL,
  `orden`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `activo`      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_tipo_estudio_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rj_tipo_estudio` (`nombre`, `orden`, `activo`) VALUES
('Carrera universitaria', 1, 1),
('Diplomado', 2, 1),
('Magíster', 3, 1),
('Doctorado', 4, 1),
('Cursos', 5, 1),
('Certificaciones', 6, 1);

-- -----------------------------------------------------------------------------
-- Tribunales (cargar todos; filtrar en app por tipo — ej. garantía / oral penal)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_tribunal` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`             VARCHAR(300) NOT NULL,
  `descripcion`        TEXT NULL,
  `region_id`          SMALLINT UNSIGNED NOT NULL,
  `comuna_id`          SMALLINT UNSIGNED NULL,
  `tipo_codigo`        VARCHAR(64) NOT NULL DEFAULT 'otro' COMMENT 'garantia | juicio_oral_penal | otro (extensible)',
  `estado`             ENUM('activo','inactivo','fusionado') NOT NULL DEFAULT 'activo',
  `codigo_externo`     VARCHAR(64) NULL COMMENT 'ID en fuente PJUD u otra, si existe',
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_tribunal_region_comuna` (`region_id`, `comuna_id`),
  KEY `idx_rj_tribunal_tipo` (`tipo_codigo`),
  KEY `idx_rj_tribunal_estado` (`estado`),
  CONSTRAINT `fk_rj_tribunal_region` FOREIGN KEY (`region_id`) REFERENCES `rj_region` (`id`),
  CONSTRAINT `fk_rj_tribunal_comuna` FOREIGN KEY (`comuna_id`) REFERENCES `rj_comuna` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Perfil abogado (1:1 con usuario profesional; regla de negocio en aplicación)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_abogado` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`         INT UNSIGNED NOT NULL,
  `rut`                VARCHAR(12) NOT NULL COMMENT 'Sin puntos; guión DV',
  `nombres`            VARCHAR(150) NOT NULL,
  `apellidos`          VARCHAR(150) NOT NULL,
  `habilidades`        TEXT NOT NULL COMMENT 'Tope ~500 palabras en validación app',
  `experiencia`        TEXT NOT NULL COMMENT 'Tope ~500 palabras en validación app',
  `estado_perfil`      ENUM('borrador','pendiente','aprobado','rechazado','suspendido') NOT NULL DEFAULT 'borrador',
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_abogado_usuario` (`usuario_id`),
  UNIQUE KEY `uq_rj_abogado_rut` (`rut`),
  KEY `idx_rj_abogado_estado` (`estado_perfil`),
  CONSTRAINT `fk_rj_abogado_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_abogado_region` (
  `abogado_id`   INT UNSIGNED NOT NULL,
  `region_id`    SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`abogado_id`, `region_id`),
  KEY `idx_rj_abogado_region_region` (`region_id`),
  CONSTRAINT `fk_rj_ar_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_ar_region` FOREIGN KEY (`region_id`) REFERENCES `rj_region` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rj_abogado_comuna` (
  `abogado_id`   INT UNSIGNED NOT NULL,
  `comuna_id`    SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`abogado_id`, `comuna_id`),
  KEY `idx_rj_abogado_comuna_comuna` (`comuna_id`),
  CONSTRAINT `fk_rj_ac_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_ac_comuna` FOREIGN KEY (`comuna_id`) REFERENCES `rj_comuna` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Archivo en Google Cloud Storage (solo referencia; sin BLOB)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_documento_gcs` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `abogado_id`         INT UNSIGNED NOT NULL,
  `nombre`             VARCHAR(200) NOT NULL COMMENT 'Nombre legible del archivo',
  `descripcion`        VARCHAR(500) NULL,
  `bucket`             VARCHAR(128) NOT NULL COMMENT 'Nombre del bucket GCS',
  `object_key`         VARCHAR(512) NOT NULL COMMENT 'Clave objeto dentro del bucket',
  `content_type`       VARCHAR(120) NOT NULL,
  `tamano_bytes`       BIGINT UNSIGNED NOT NULL,
  `sha256_hex`         CHAR(64) NULL COMMENT 'Integridad opcional post-subida',
  `gcs_generation`     BIGINT UNSIGNED NULL COMMENT 'generation de GCS si se usa',
  `subido_por_usuario_id` INT UNSIGNED NULL,
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_doc_gcs_bucket_key` (`bucket`, `object_key`),
  KEY `idx_rj_doc_gcs_abogado` (`abogado_id`),
  CONSTRAINT `fk_rj_doc_gcs_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_doc_gcs_usuario` FOREIGN KEY (`subido_por_usuario_id`) REFERENCES `rj_usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Estudios: siempre con documento (FK NOT NULL). Varios del mismo tipo permitidos.
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_abogado_estudio` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `abogado_id`         INT UNSIGNED NOT NULL,
  `nombre`             VARCHAR(200) NOT NULL,
  `descripcion`        TEXT NULL,
  `universidad`        VARCHAR(200) NOT NULL,
  `anio_titulacion`    SMALLINT UNSIGNED NOT NULL,
  `tipo_estudio_id`    SMALLINT UNSIGNED NOT NULL,
  `documento_id`       BIGINT UNSIGNED NOT NULL,
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rj_abogado_estudio_abogado` (`abogado_id`),
  KEY `idx_rj_abogado_estudio_tipo` (`tipo_estudio_id`),
  CONSTRAINT `fk_rj_ae_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_ae_tipo` FOREIGN KEY (`tipo_estudio_id`) REFERENCES `rj_tipo_estudio` (`id`),
  CONSTRAINT `fk_rj_ae_documento` FOREIGN KEY (`documento_id`) REFERENCES `rj_documento_gcs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Abogado ↔ Tribunal (varios tribunales; estado de la relación)
-- -----------------------------------------------------------------------------

CREATE TABLE `rj_abogado_tribunal` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `abogado_id`     INT UNSIGNED NOT NULL,
  `tribunal_id`    INT UNSIGNED NOT NULL,
  `estado`         ENUM('activo','inactivo','pendiente_revision') NOT NULL DEFAULT 'activo',
  `fcreacion`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rj_abogado_tribunal` (`abogado_id`, `tribunal_id`),
  KEY `idx_rj_at_tribunal` (`tribunal_id`),
  KEY `idx_rj_at_estado` (`estado`),
  CONSTRAINT `fk_rj_at_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rj_at_tribunal` FOREIGN KEY (`tribunal_id`) REFERENCES `rj_tribunal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Contacto desde perfil público del abogado (seguimiento + redirección a tel/WA)
-- Flujo app: formulario nombre, apellido, correo, teléfono -> confirmación ->
-- INSERT aquí -> redirect a `tel:` o `https://wa.me/` con número del abogado.
-- -----------------------------------------------------------------------------

CREATE TABLE `contacto` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `abogado_id`         INT UNSIGNED NOT NULL,
  `nombre`             VARCHAR(120) NOT NULL,
  `apellido`           VARCHAR(120) NOT NULL,
  `correo`             VARCHAR(150) NOT NULL,
  `telefono`           VARCHAR(30) NOT NULL COMMENT 'Teléfono del visitante (E.164 o formato normalizado en app)',
  `consentimiento_datos` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si aceptó tratamiento/contacto',
  `canal_redireccion`  ENUM('whatsapp','telefono') NOT NULL DEFAULT 'whatsapp' COMMENT 'A dónde se envió al usuario',
  `destino_e164`       VARCHAR(20) NULL COMMENT 'Número abogado usado en el enlace (copia al confirmar)',
  `estado_seguimiento` ENUM('nuevo','visto','en_gestion','cerrado') NOT NULL DEFAULT 'nuevo',
  `notas_internas`     VARCHAR(500) NULL COMMENT 'Uso panel abogado/admin',
  `ip_origen`          VARCHAR(45) NULL COMMENT 'IPv4 o IPv6 textual',
  `user_agent`         VARCHAR(400) NULL,
  `fcreacion`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacto_abogado_fecha` (`abogado_id`, `fcreacion`),
  KEY `idx_contacto_estado` (`estado_seguimiento`),
  CONSTRAINT `fk_contacto_abogado` FOREIGN KEY (`abogado_id`) REFERENCES `rj_abogado` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Notas de implementación GCP (aplicación, no SQL)
-- -----------------------------------------------------------------------------
-- object_key sugerido: abogados/{abogado_id}/estudios/{uuid}.{ext}
-- IAM: cuenta de servicio con permisos mínimos al bucket; rotación de claves.
-- Lectura pública: desaconsejado para documentos personales; preferir signed URL.
-- Tras INSERT en rj_documento_gcs, opcional: verificación asíncrona sha256 vs GCS.
-- Orden transaccional estudio+documento: 1) INSERT documento_gcs 2) INSERT estudio
--   (documento_id). Si falla (2), borrar objeto en GCS o job de limpieza.
-- -----------------------------------------------------------------------------
