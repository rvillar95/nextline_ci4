-- =====================================================
-- AGREGAR MÓDULO DE CONFIGURACIONES DEL SISTEMA
-- =====================================================
-- Este script agrega el módulo de configuraciones
-- donde el nutricionista puede personalizar el comportamiento
-- del sistema (WhatsApp, Email, Calendario, etc.)
-- =====================================================

-- Insertar módulo principal
INSERT INTO `modulo`
(`id`, `nombre`, `ruta`, `icono`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(34, 'Configuraciones', '/configuracion', 'fas fa-cog', 'A', 'S', 100, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `ruta` = VALUES(`ruta`),
    `icono` = VALUES(`icono`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- Insertar rutas del módulo
INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(236, 34, 'Ver Configuraciones', '', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(237, 34, 'Guardar Configuraciones', '/guardar', 'editar', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- Asignar permisos al perfil de Nutricionista (perfil_id = 9)
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `editar`, `eliminar`, `fcreacion`, `factualizacion`)
VALUES
(9, 34, 1, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `editar` = 1,
    `eliminar` = 0,
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
SELECT 
    m.id,
    m.nombre,
    m.ruta,
    m.icono,
    m.estado,
    COUNT(md.id) as cantidad_rutas
FROM modulo m
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id
WHERE m.id = 34
GROUP BY m.id;

-- Verificar permisos
SELECT 
    pm.perfil_id,
    p.nombre as perfil_nombre,
    pm.modulo_id,
    m.nombre as modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id = 34;
