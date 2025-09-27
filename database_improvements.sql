-- Mejoras para la tabla servicio
ALTER TABLE `servicio` 
ADD COLUMN `categoria` VARCHAR(100) DEFAULT NULL AFTER `nombre`,
ADD COLUMN `icono` VARCHAR(100) DEFAULT NULL AFTER `categoria`,
ADD COLUMN `caracteristicas` TEXT DEFAULT NULL AFTER `descripcionLarga`,
ADD COLUMN `beneficios` TEXT DEFAULT NULL AFTER `caracteristicas`,
ADD COLUMN `tiempo_estimado` VARCHAR(50) DEFAULT NULL AFTER `beneficios`,
ADD COLUMN `garantia` VARCHAR(100) DEFAULT NULL AFTER `tiempo_estimado`,
ADD COLUMN `orden` INT DEFAULT 0 AFTER `garantia`,
ADD COLUMN `destacado` CHAR(1) DEFAULT 'N' AFTER `orden`,
ADD COLUMN `precio_desde` DECIMAL(10,2) DEFAULT NULL AFTER `garantia`,
ADD COLUMN `precio_hasta` DECIMAL(10,2) DEFAULT NULL AFTER `precio_desde`,
ADD COLUMN `mostrar_precio` CHAR(1) DEFAULT 'S' AFTER `precio_hasta`,
ADD COLUMN `slug` VARCHAR(150) DEFAULT NULL AFTER `mostrar_precio`;

-- Crear tabla de categorías de servicios
CREATE TABLE IF NOT EXISTS `servicio_categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `orden` int DEFAULT 0,
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insertar categorías por defecto
INSERT INTO `servicio_categoria` (`nombre`, `descripcion`, `icono`, `color`, `orden`) VALUES
('Construcción Residencial', 'Casas, condominios y proyectos habitacionales', 'icofont-home', '#3498db', 1),
('Construcción Comercial', 'Edificios de oficinas y locales comerciales', 'icofont-building', '#e74c3c', 2),
('Remodelaciones', 'Transformación de espacios existentes', 'icofont-tools', '#f39c12', 3),
('Ampliaciones', 'Extensión de construcciones existentes', 'icofont-expand', '#27ae60', 4),
('Consultoría', 'Asesoría técnica y de proyectos', 'icofont-lightbulb', '#9b59b6', 5);

-- Corregir el submenú de servicios
UPDATE `modulo_detalle` 
SET `descripcion` = 'Listar Servicios' 
WHERE `descripcion` = 'Listar Modulo' 
AND `modulo_id` = (SELECT `id` FROM `modulo` WHERE `nombre` = 'Servicios' LIMIT 1);
