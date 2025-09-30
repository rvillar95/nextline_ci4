-- Script para insertar categorías de servicios faltantes
-- Base de datos: nextline_pyme

-- Categorías existentes en la BD:
-- ID 2: Construcción Comercial
-- ID 3: Remodelaciones  
-- ID 4: Ampliaciones
-- ID 5: Consultoría
-- ID 7: Construcción Residencial

-- Insertar categorías faltantes
INSERT INTO servicio_categoria (nombre, slug, descripcion, icono, color, estado, orden, fcreacion, factualizacion) VALUES
('Servicios Especializados', 'servicios-especializados', 'Servicios técnicos especializados como piscinas, quinchos, pavimentación e instalaciones', 'fas fa-cogs', '#17a2b8', 'A', 6, NOW(), NOW()),

('Instalaciones', 'instalaciones', 'Servicios de instalaciones eléctricas, sanitarias, de gas y sistemas especializados', 'fas fa-plug', '#6f42c1', 'A', 7, NOW(), NOW()),

('Diseño y Arquitectura', 'diseno-arquitectura', 'Servicios de diseño arquitectónico, planos técnicos y asesoría de proyectos', 'fas fa-drafting-compass', '#fd7e14', 'A', 8, NOW(), NOW()),

('Permisos y Trámites', 'permisos-tramites', 'Gestión de permisos municipales, trámites legales y documentación técnica', 'fas fa-file-alt', '#20c997', 'A', 9, NOW(), NOW()),

('Mantenimiento', 'mantenimiento', 'Servicios de mantenimiento, reparaciones y mejoras de construcciones existentes', 'fas fa-tools', '#dc3545', 'A', 10, NOW(), NOW()),

('Proyectos Industriales', 'proyectos-industriales', 'Construcción y desarrollo de proyectos industriales, bodegas y complejos comerciales', 'fas fa-industry', '#6c757d', 'A', 11, NOW(), NOW()),

('Infraestructura', 'infraestructura', 'Proyectos de infraestructura urbana, pavimentación y obras públicas', 'fas fa-road', '#28a745', 'A', 12, NOW(), NOW()),

('Sustentabilidad', 'sustentabilidad', 'Servicios de construcción sustentable, eficiencia energética y tecnologías verdes', 'fas fa-leaf', '#198754', 'A', 13, NOW(), NOW());

-- Actualizar el slug de la categoría "Consultoría" que está NULL
UPDATE servicio_categoria SET slug = 'consultoria', factualizacion = NOW() WHERE id = 5 AND slug IS NULL;
