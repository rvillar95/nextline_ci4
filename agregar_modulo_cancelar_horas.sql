-- =====================================================
-- AGREGAR MÓDULO DE CANCELAR HORAS MASIVAMENTE
-- =====================================================
-- Este script agrega el módulo para cancelar horas
-- en caso de emergencia o enfermedad del nutricionista
-- =====================================================

-- Insertar módulo principal
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(38, 'Cancelar Horas', 'Cancelar horas masivamente en caso de emergencia o enfermedad', '/dashboard/agenda/cancelar-horas', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- Insertar rutas del módulo (detalles)
INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(238, 38, 'Ver Cancelar Horas', '', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(239, 38, 'Obtener Citas', '/obtener-citas', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(240, 38, 'Procesar Cancelación', '/procesar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00')
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
(9, 38, 1, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `editar` = 1,
    `eliminar` = 0,
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó el módulo correctamente
SELECT 
    m.id,
    m.nombre,
    m.descripcion,
    m.ruta,
    m.estado,
    COUNT(md.id) as cantidad_rutas
FROM modulo m
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id
WHERE m.id = 38
GROUP BY m.id;

-- Verificar detalles del módulo
SELECT 
    id, 
    modulo_id, 
    descripcion, 
    ruta, 
    accion, 
    estado, 
    mostrar,
    orden
FROM modulo_detalle 
WHERE modulo_id = 38
ORDER BY orden;

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
WHERE pm.modulo_id = 38;
