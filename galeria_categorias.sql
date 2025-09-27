-- Crear tabla de categorías para galería
CREATE TABLE IF NOT EXISTS `galeria_categoria` (
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

-- Insertar categorías por defecto para galería
INSERT INTO `galeria_categoria` (`nombre`, `descripcion`, `icono`, `color`, `orden`) VALUES
('Quinchos', 'Construcción de quinchos y áreas de esparcimiento', 'icofont-home', '#e74c3c', 1),
('Casas Residenciales', 'Construcción de casas y viviendas familiares', 'icofont-building', '#3498db', 2),
('Edificios Comerciales', 'Construcción de edificios de oficinas y locales', 'icofont-office', '#f39c12', 3),
('Remodelaciones', 'Transformación y renovación de espacios existentes', 'icofont-tools', '#27ae60', 4),
('Ampliaciones', 'Extensión de construcciones existentes', 'icofont-expand', '#9b59b6', 5),
('Piscinas', 'Construcción de piscinas y áreas acuáticas', 'icofont-water-drop', '#1abc9c', 6),
('Jardines', 'Diseño y construcción de jardines y paisajismo', 'icofont-tree', '#2ecc71', 7),
('Obras Menores', 'Trabajos de menor envergadura y mantenimiento', 'icofont-settings', '#95a5a6', 8);

-- Agregar columna categoria_id a la tabla galeria
ALTER TABLE `galeria` 
ADD COLUMN `categoria_id` int DEFAULT NULL AFTER `nombre`,
ADD KEY `fk_galeria_categoria` (`categoria_id`),
ADD CONSTRAINT `fk_galeria_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `galeria_categoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
