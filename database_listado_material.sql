-- =====================================================
-- Script SQL para crear tablas de Listado de Materiales
-- =====================================================
-- Si la migración de CodeIgniter no funciona,
-- ejecuta este script en phpMyAdmin

-- Tabla principal: listado_material
CREATE TABLE IF NOT EXISTS `listado_material` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_listado` VARCHAR(50) NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `cliente_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `proyecto_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `fecha_listado` DATE NULL DEFAULT NULL,
  `estado` ENUM('borrador','finalizado','enviado','archivado') NOT NULL DEFAULT 'borrador',
  `observaciones` TEXT NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_listado` (`numero_listado`),
  KEY `cliente_id` (`cliente_id`),
  KEY `proyecto_id` (`proyecto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de items: listado_material_item
CREATE TABLE IF NOT EXISTS `listado_material_item` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listado_material_id` INT(11) UNSIGNED NOT NULL,
  `nombre_material` VARCHAR(300) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL,
  `unidad_medida` VARCHAR(50) NULL DEFAULT NULL,
  `cantidad` DECIMAL(10,2) NULL DEFAULT NULL,
  `orden` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `listado_material_id` (`listado_material_id`),
  CONSTRAINT `listado_material_item_ibfk_1` 
    FOREIGN KEY (`listado_material_id`) 
    REFERENCES `listado_material` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Fin del script
-- =====================================================

