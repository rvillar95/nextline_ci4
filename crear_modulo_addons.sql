-- =====================================================
-- CREAR MÓDULO "ADD-ONS" (Solo Super Admin)
-- =====================================================
-- Permite asignar add-ons por empresa (métodos de cálculo o módulos premium)
-- Tabla usada: empresa_addon
-- =====================================================

-- 1) Crear módulo principal (ajusta el ID si ya existe)
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(41, 'Add-ons', 'Gestión de add-ons por empresa (métodos premium y módulos)', '/dashboard/addon', 'A', 'S', 'S', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- Obtener ID real del módulo por ruta (por si cambió el ID)
SET @modulo_addon_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/addon' LIMIT 1);

-- 2) Crear rutas del módulo (modulo_detalle)
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_addon_id, 'Ver Add-ons', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_addon_id, 'Obtener Add-ons (DataTable)', '/getAddons', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_addon_id, 'Asignar/Actualizar Add-on', '/registrar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_addon_id, 'Cancelar Add-on', '/cancelar', 'eliminar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_addon_id, 'Activar Add-on', '/activar', 'editar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `estado` = 'A',
    `factualizacion` = NOW();

-- 3) Permisos: asignar el módulo al/los perfiles Super Admin
-- Buscar perfiles globales con poder=3
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT p.id, @modulo_addon_id, 1, 1, 1, 1, 0, 'A', 999, NOW(), NOW(), '0000-00-00 00:00:00'
FROM perfil p
WHERE p.poder = 3
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `registrar` = 1,
    `editar` = 1,
    `eliminar` = 1,
    `estado` = 'A',
    `factualizacion` = NOW();

-- Verificación
SELECT id, nombre, ruta, sa, estado FROM modulo WHERE ruta = '/dashboard/addon';
SELECT * FROM modulo_detalle WHERE modulo_id = @modulo_addon_id ORDER BY orden;

