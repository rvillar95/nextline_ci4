-- =====================================================
-- CREAR MÓDULO "FACTURACIÓN" (Solo Super Admin)
-- =====================================================
-- Permite ver cuánto debería facturar por empresa (plan + add-ons)
-- y generar reportes mensuales
-- =====================================================

-- 1) Crear módulo principal (ajusta el ID si ya existe)
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(42, 'Facturación', 'Gestión de facturación e ingresos (planes + add-ons por empresa)', '/dashboard/facturacion', 'A', 'S', 'S', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- Obtener ID real del módulo por ruta (por si cambió el ID)
SET @modulo_facturacion_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/facturacion' LIMIT 1);

-- 2) Crear rutas del módulo (modulo_detalle)
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_facturacion_id, 'Ver Facturación', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_facturacion_id, 'Obtener Facturación (DataTable)', '/getFacturacion', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `estado` = 'A',
    `factualizacion` = NOW();

-- 3) Permisos: asignar el módulo al/los perfiles Super Admin
-- Buscar perfiles globales con poder=3
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT p.id, @modulo_facturacion_id, 1, 0, 0, 0, 1, 'A', 999, NOW(), NOW(), '0000-00-00 00:00:00'
FROM perfil p
WHERE p.poder = 3
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `analizar` = 1,
    `estado` = 'A',
    `factualizacion` = NOW();

-- Verificación
SELECT id, nombre, ruta, sa, estado FROM modulo WHERE ruta = '/dashboard/facturacion';
SELECT * FROM modulo_detalle WHERE modulo_id = @modulo_facturacion_id ORDER BY orden;
