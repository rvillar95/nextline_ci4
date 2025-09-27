-- Tabla de testimonios
CREATE TABLE IF NOT EXISTS `testimonios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `testimonio` text NOT NULL,
  `calificacion` tinyint(1) NOT NULL DEFAULT 5 COMMENT '1-5 estrellas',
  `proyecto_id` int(11) DEFAULT NULL COMMENT 'ID del proyecto relacionado (opcional)',
  `servicio_id` int(11) DEFAULT NULL COMMENT 'ID del servicio relacionado (opcional)',
  `imagen` varchar(255) DEFAULT NULL COMMENT 'Foto del cliente',
  `estado` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `destacado` enum('S','N') NOT NULL DEFAULT 'N' COMMENT 'S=Destacado, N=Normal',
  `fecha_proyecto` date DEFAULT NULL COMMENT 'Fecha cuando se realizó el proyecto',
  `slug` varchar(150) DEFAULT NULL,
  `meta_titulo` varchar(200) DEFAULT NULL,
  `meta_descripcion` varchar(300) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `fcreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_proyecto` (`proyecto_id`),
  KEY `idx_servicio` (`servicio_id`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos testimonios de ejemplo
INSERT INTO `testimonios` (`nombre`, `cargo`, `empresa`, `testimonio`, `calificacion`, `estado`, `destacado`, `fecha_proyecto`) VALUES
('María González', 'Propietaria', 'Casa Residencial', 'Excelente trabajo en la construcción de nuestra casa. El equipo fue muy profesional y cumplieron con todos los plazos establecidos. La calidad de los materiales y la atención al detalle fueron excepcionales.', 5, 'A', 'S', '2024-08-15'),
('Carlos Rodríguez', 'Director', 'Empresa Comercial S.A.', 'Contratamos a NextLine para la ampliación de nuestras oficinas y quedamos muy satisfechos. El proyecto se completó a tiempo y dentro del presupuesto. Altamente recomendados.', 5, 'A', 'S', '2024-07-20'),
('Ana Martínez', 'Arquitecta', 'Estudio de Arquitectura', 'Como arquitecta, he trabajado con muchas constructoras, pero NextLine se destaca por su profesionalismo y calidad. Siempre cumplen con los estándares más altos.', 5, 'A', 'N', '2024-06-10'),
('Roberto Silva', 'Ingeniero', 'Consultora Técnica', 'La construcción del edificio industrial fue impecable. El equipo técnico de NextLine demostró gran experiencia y conocimiento en cada fase del proyecto.', 4, 'A', 'N', '2024-05-25');
