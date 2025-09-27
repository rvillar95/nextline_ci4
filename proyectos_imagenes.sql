-- Script para crear tablas de Proyectos e Imágenes
-- Opción 1: Tabla de imágenes separada con tipo y entidad_id

-- Tabla de imágenes (flexible para proyectos, galería, servicios)
CREATE TABLE IF NOT EXISTS `imagenes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta` VARCHAR(500) NOT NULL,
    `tipo` ENUM('galeria', 'proyecto', 'servicio') NOT NULL,
    `entidad_id` INT NOT NULL,
    `es_portada` BOOLEAN DEFAULT FALSE,
    `orden` INT DEFAULT 0,
    `descripcion` VARCHAR(500) NULL,
    `estado` CHAR(1) DEFAULT 'A',
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `factualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `feliminacion` DATETIME NULL,
    INDEX `idx_tipo_entidad` (`tipo`, `entidad_id`),
    INDEX `idx_portada` (`es_portada`),
    INDEX `idx_orden` (`orden`)
);

-- Tabla de proyectos
CREATE TABLE IF NOT EXISTS `proyectos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `cliente` VARCHAR(255) NULL,
    `tipo_proyecto` ENUM('residencial', 'comercial', 'industrial', 'institucional', 'otro') NOT NULL DEFAULT 'residencial',
    `ubicacion` VARCHAR(255) NULL,
    `direccion` VARCHAR(500) NULL,
    `fecha_inicio` DATE NULL,
    `fecha_finalizacion` DATE NULL,
    `presupuesto` DECIMAL(15,2) NULL,
    `mostrar_presupuesto` BOOLEAN DEFAULT FALSE,
    `estado` ENUM('en_progreso', 'completado', 'en_pausa', 'cancelado') NOT NULL DEFAULT 'en_progreso',
    `descripcion_corta` VARCHAR(500) NULL,
    `descripcion_detallada` TEXT NULL,
    `caracteristicas_tecnicas` TEXT NULL,
    `area_construida` DECIMAL(10,2) NULL,
    `materiales_principales` VARCHAR(500) NULL,
    `testimonio_cliente` TEXT NULL,
    `nombre_cliente` VARCHAR(255) NULL,
    `destacado` BOOLEAN DEFAULT FALSE,
    `meta_titulo` VARCHAR(255) NULL,
    `meta_descripcion` VARCHAR(500) NULL,
    `meta_keywords` VARCHAR(500) NULL,
    `estado_publico` CHAR(1) DEFAULT 'A',
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `factualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `feliminacion` DATETIME NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_destacado` (`destacado`),
    INDEX `idx_tipo` (`tipo_proyecto`)
);

-- Insertar algunos proyectos de ejemplo
INSERT INTO `proyectos` (`nombre`, `slug`, `cliente`, `tipo_proyecto`, `ubicacion`, `direccion`, `fecha_inicio`, `fecha_finalizacion`, `presupuesto`, `mostrar_presupuesto`, `estado`, `descripcion_corta`, `descripcion_detallada`, `caracteristicas_tecnicas`, `area_construida`, `materiales_principales`, `destacado`, `estado_publico`) VALUES
('Casa Familiar Los Robles', 'casa-familiar-los-robles', 'Familia González', 'residencial', 'Santiago', 'Av. Los Robles 1234, Las Condes', '2024-01-15', '2024-06-30', 85000000, 0, 'completado', 'Hermosa casa familiar de 3 dormitorios con diseño moderno', 'Proyecto completo de construcción de casa familiar de 180m² con diseño contemporáneo. Incluye 3 dormitorios, 2 baños, living comedor integrado, cocina moderna y terraza. Construcción con materiales de primera calidad y acabados de lujo.', 'Casa de 2 pisos, estructura de hormigón armado, techumbre de tejas, ventanas de PVC, piso flotante, cocina empotrada', 180.50, 'Hormigón armado, Tejas cerámicas, PVC, Porcelanato', 1, 'A'),
('Edificio Comercial Centro', 'edificio-comercial-centro', 'Inmobiliaria Central', 'comercial', 'Santiago', 'Av. Libertador 567, Santiago Centro', '2024-03-01', '2024-12-15', 250000000, 0, 'en_progreso', 'Edificio comercial de 8 pisos con locales y oficinas', 'Construcción de edificio comercial de 8 pisos con locales comerciales en los primeros 2 pisos y oficinas en los pisos superiores. Incluye estacionamientos subterráneos y sistemas modernos de climatización.', 'Edificio de 8 pisos, estructura de hormigón armado, fachada de vidrio y aluminio, ascensores, estacionamientos subterráneos', 2500.00, 'Hormigón armado, Vidrio templado, Aluminio, Acero', 1, 'A'),
('Ampliación Residencial', 'ampliacion-residencial', 'Familia Martínez', 'residencial', 'Providencia', 'Calle Providencia 890, Providencia', '2024-02-10', '2024-05-20', 35000000, 0, 'completado', 'Ampliación de casa existente con nuevo dormitorio y baño', 'Ampliación de casa existente agregando un dormitorio principal con baño en suite y walk-in closet. Manteniendo la armonía arquitectónica con el diseño original de la casa.', 'Ampliación de 1 piso, estructura de hormigón armado, techumbre a dos aguas, ventanas de aluminio', 45.00, 'Hormigón armado, Tejas cerámicas, Aluminio, Porcelanato', 0, 'A');
