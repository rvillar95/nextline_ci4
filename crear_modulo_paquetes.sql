-- =====================================================
-- CREAR MÓDULO "PAQUETES"
-- =====================================================
-- Este script crea el módulo de gestión de paquetes (id=40)
-- Solo accesible para Super Admin
-- =====================================================

-- Insertar módulo principal
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(40, 'Paquetes', 'Gestión de paquetes y asignación de módulos a empresas', '/dashboard/paquete', 'A', 'S', 'S', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- Insertar rutas del módulo
INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(241, 40, 'Ver Lista de Paquetes', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(242, 40, 'Obtener Paquetes (DataTable)', '/getPaquetes', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(243, 40, 'Crear Nuevo Paquete', '/registro', 'editar', 'A', 'S', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(244, 40, 'Editar Paquete', '/editar', 'editar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(245, 40, 'Ver Detalle de Paquete', '/detalle', 'ver', 'A', 'S', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(246, 40, 'Gestionar Módulos del Paquete', '/gestionar-modulos', 'editar', 'A', 'S', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(247, 40, 'Registrar Nuevo Paquete', '/registrar', 'editar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00'),
(248, 40, 'Actualizar Paquete', '/update', 'editar', 'A', 'N', 8, NOW(), NOW(), '0000-00-00 00:00:00'),
(249, 40, 'Guardar Módulos del Paquete', '/guardar-modulos', 'editar', 'A', 'N', 9, NOW(), NOW(), '0000-00-00 00:00:00'),
(250, 40, 'Eliminar/Desactivar Paquete', '/eliminar', 'eliminar', 'A', 'N', 10, NOW(), NOW(), '0000-00-00 00:00:00'),
(251, 40, 'Activar Paquete', '/activar', 'editar', 'A', 'N', 11, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- Asignar permisos al perfil de Super Admin (perfil_id = 1, poder = 3)
-- Primero verificamos qué perfil tiene poder = 3
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `editar`, `eliminar`, `fcreacion`, `factualizacion`)
SELECT 
    p.id AS perfil_id,
    40 AS modulo_id,
    1 AS ver,
    1 AS editar,
    1 AS eliminar,
    NOW() AS fcreacion,
    NOW() AS factualizacion
FROM `perfil` p
WHERE p.poder = 3
LIMIT 1
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `editar` = 1,
    `eliminar` = 1,
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que el módulo se creó correctamente
SELECT 
    id,
    nombre,
    descripcion,
    ruta,
    estado,
    mostrar,
    sa
FROM modulo 
WHERE id = 40;

-- Verificar rutas del módulo
SELECT 
    md.id,
    md.modulo_id,
    m.nombre AS modulo_nombre,
    md.descripcion,
    md.ruta,
    md.accion,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = 40
ORDER BY md.orden;

-- Verificar permisos asignados
SELECT 
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    p.poder,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id = 40;

-- =====================================================
-- RESUMEN
-- =====================================================
-- Módulo creado: Paquetes (id=40)
-- Rutas creadas: 11 rutas (lista, getPaquetes, registro, editar, detalle, gestionar-modulos, registrar, update, guardar-modulos, eliminar, activar)
-- Permisos: Asignados al perfil de Super Admin (poder = 3)
-- 
-- Próximos pasos:
-- 1. Verificar que el módulo aparece en el menú para Super Admin
-- 2. Probar crear un nuevo paquete
-- 3. Probar asignar módulos a un paquete
-- 4. Verificar que las empresas pueden ser asignadas a paquetes
-- =====================================================
