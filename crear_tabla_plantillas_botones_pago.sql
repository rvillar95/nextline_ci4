-- =====================================================
-- CREAR TABLA PARA PLANTILLAS DE BOTONES DE PAGO
-- =====================================================
-- Esta tabla almacena plantillas reutilizables de botones de pago
-- que pueden ser usadas al agendar citas
-- =====================================================

CREATE TABLE IF NOT EXISTS `botones_pago_plantilla` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` INT(11) NOT NULL COMMENT 'ID de la empresa que crea la plantilla',
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título de la plantilla (ej: "Consulta Normal")',
  `descripcion` TEXT NULL COMMENT 'Descripción del tipo de pago',
  `monto` DECIMAL(10,2) NOT NULL COMMENT 'Monto del pago',
  `moneda` VARCHAR(10) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda (CLP, USD, etc.)',
  `activo` CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `orden` INT(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `fcreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_empresa_activo` (`empresa_id`, `activo`),
  INDEX `idx_orden` (`orden`),
  CONSTRAINT `fk_plantilla_empresa` 
    FOREIGN KEY (`empresa_id`) 
    REFERENCES `empresa` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plantillas reutilizables de botones de pago';

-- =====================================================
-- DATOS DE EJEMPLO (OPCIONAL)
-- =====================================================
-- Puedes descomentar estas líneas para crear plantillas de ejemplo
-- 
-- INSERT INTO `botones_pago_plantilla` (`empresa_id`, `titulo`, `descripcion`, `monto`, `moneda`, `orden`) VALUES
-- (1, 'Consulta Normal', 'Consulta nutricional estándar', 15000.00, 'CLP', 1),
-- (1, 'Primera Consulta', 'Consulta inicial con evaluación completa', 25000.00, 'CLP', 2),
-- (1, 'Control', 'Consulta de control y seguimiento', 12000.00, 'CLP', 3);
