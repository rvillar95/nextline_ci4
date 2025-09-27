-- Crear tabla de categorías de servicios
CREATE TABLE IF NOT EXISTS `servicio_categoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL UNIQUE,
    `descripcion` VARCHAR(500) NULL,
    `icono` VARCHAR(100) NULL,
    `color` VARCHAR(20) NULL,
    `estado` CHAR(1) NOT NULL DEFAULT 'A',
    `orden` INT NOT NULL DEFAULT 0,
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `factualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `feliminacion` DATETIME NULL
);

-- Insertar categorías iniciales
INSERT INTO `servicio_categoria` (`nombre`, `descripcion`, `icono`, `color`, `estado`, `orden`) VALUES
('Construcción Residencial', 'Construcción de casas y viviendas familiares', 'fas fa-home', '#33FF57', 'A', 1),
('Construcción Comercial', 'Construcción de edificios comerciales y oficinas', 'fas fa-building', '#3357FF', 'A', 2),
('Remodelaciones', 'Transformación y renovación de espacios existentes', 'fas fa-paint-brush', '#FF33F0', 'A', 3),
('Ampliaciones', 'Extensión de construcciones existentes', 'fas fa-expand-arrows-alt', '#F0FF33', 'A', 4),
('Piscinas', 'Construcción de piscinas y áreas acuáticas', 'fas fa-swimming-pool', '#33F0FF', 'A', 5),
('Jardines', 'Diseño y construcción de jardines y paisajismo', 'fas fa-tree', '#57FF33', 'A', 6),
('Obras Menores', 'Trabajos de menor envergadura y mantenimiento', 'fas fa-tools', '#FF8C33', 'A', 7);

-- Agregar columna categoria_id a la tabla servicio
ALTER TABLE `servicio` ADD COLUMN `categoria_id` INT NULL AFTER `nombre`;

-- Agregar foreign key
ALTER TABLE `servicio` ADD CONSTRAINT `fk_servicio_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `servicio_categoria`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
