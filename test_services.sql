-- Script de prueba para verificar que los servicios funcionen
-- Ejecutar este script después de database_improvements.sql

-- Insertar un servicio de prueba
INSERT INTO `servicio` (
    `nombre`, 
    `categoria`, 
    `descripcionCorta`, 
    `descripcionLarga`, 
    `caracteristicas`, 
    `beneficios`, 
    `tiempo_estimado`, 
    `garantia`, 
    `precio_desde`, 
    `precio_hasta`, 
    `mostrar_precio`, 
    `foto`, 
    `estado`, 
    `orden`, 
    `destacado`, 
    `slug`
) VALUES (
    'Construcción de Casa Residencial',
    'Construcción Residencial',
    'Construcción completa de casa residencial con acabados de primera calidad',
    'Ofrecemos servicios completos de construcción residencial, desde la cimentación hasta los acabados finales. Incluye diseño arquitectónico, permisos municipales, construcción y entrega llave en mano.',
    'Cimentación reforzada\nEstructura de hormigón armado\nInstalaciones eléctricas y sanitarias\nAcabados de primera calidad',
    'Garantía de 2 años\nSupervisión técnica constante\nMateriales de primera calidad\nCumplimiento de plazos',
    '6-8 meses',
    '2 años',
    500000.00,
    800000.00,
    'S',
    'lib/images/default-service.jpg',
    'A',
    1,
    'S',
    'construccion-casa-residencial'
);

-- Insertar otro servicio de prueba
INSERT INTO `servicio` (
    `nombre`, 
    `categoria`, 
    `descripcionCorta`, 
    `descripcionLarga`, 
    `caracteristicas`, 
    `beneficios`, 
    `tiempo_estimado`, 
    `garantia`, 
    `precio_desde`, 
    `precio_hasta`, 
    `mostrar_precio`, 
    `foto`, 
    `estado`, 
    `orden`, 
    `destacado`, 
    `slug`
) VALUES (
    'Remodelación de Cocina',
    'Remodelaciones',
    'Remodelación completa de cocina con diseño moderno',
    'Transformamos tu cocina con un diseño moderno y funcional. Incluye diseño, demolición, instalaciones nuevas y acabados.',
    'Diseño personalizado\nDemolición y limpieza\nInstalaciones nuevas\nAcabados modernos',
    'Diseño incluido\nGarantía de materiales\nInstalación profesional\nResultado garantizado',
    '3-4 semanas',
    '1 año',
    NULL,
    NULL,
    'N',
    'lib/images/default-service.jpg',
    'A',
    2,
    'N',
    'remodelacion-cocina'
);
