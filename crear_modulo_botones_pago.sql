-- =====================================================
-- CREAR MÓDULO "BOTONES DE PAGO"
-- =====================================================
-- Permite crear y gestionar botones de pago con Mercado Pago
-- La configuración de credenciales aparece en Configuraciones solo si tienen este módulo
-- =====================================================

-- 1) Crear módulo principal
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(42, 'Botones de Pago', 'Crear y gestionar botones de pago con Mercado Pago', '/dashboard/boton-pago', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- Obtener ID real del módulo por ruta
SET @modulo_boton_pago_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/boton-pago' LIMIT 1);

-- 2) Crear rutas del módulo (modulo_detalle)
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_boton_pago_id, 'Lista de Botones de Pago', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Crear Botón de Pago', '/crear', 'registrar', 'A', 'S', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Ver Botón de Pago', '/ver', 'ver', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Generar Botón de Pago (AJAX)', '/generar', 'registrar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Pago Exitoso', '/success', 'ver', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Pago Fallido', '/failure', 'ver', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_boton_pago_id, 'Pago Pendiente', '/pending', 'ver', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `estado` = 'A',
    `factualizacion` = NOW();

-- 3) Asignar módulo a perfiles (ajustar según tus perfiles)
-- Por defecto, asignar a perfiles comunes (puedes ajustar los IDs)
-- Ejemplo: Asignar a perfil "Administrador" (ajusta el perfil_id según tu sistema)

-- Obtener perfiles que normalmente tienen acceso a módulos de pago
-- (Ajusta estos IDs según tu sistema)
SET @perfil_admin = (SELECT id FROM perfil WHERE nombre LIKE '%Admin%' OR nombre LIKE '%Administrador%' LIMIT 1);
SET @perfil_nutricionista = (SELECT id FROM perfil WHERE nombre LIKE '%Nutricionista%' LIMIT 1);

-- Si no encuentra perfiles, usar IDs comunes (ajusta según tu BD)
SET @perfil_admin = IFNULL(@perfil_admin, 1);
SET @perfil_nutricionista = IFNULL(@perfil_nutricionista, 9);

-- Asignar permisos al módulo para los perfiles
-- Perfil Admin: todos los permisos
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@perfil_admin, @modulo_boton_pago_id, 1, 1, 1, 1, 'A', 100, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `registrar` = 1,
    `editar` = 1,
    `eliminar` = 1,
    `estado` = 'A',
    `factualizacion` = NOW();

-- Perfil Nutricionista: ver y crear (ajusta según necesites)
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@perfil_nutricionista, @modulo_boton_pago_id, 1, 1, 0, 0, 'A', 100, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `registrar` = 1,
    `editar` = 0,
    `eliminar` = 0,
    `estado` = 'A',
    `factualizacion` = NOW();

-- 4) NOTA: Este módulo puede estar en paquetes base O ser un addon
-- Para agregarlo a un paquete, usar:
-- INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido, fcreacion) VALUES (X, @modulo_boton_pago_id, 'S', NOW());
-- 
-- Para hacerlo addon, marcar en modulo:
-- UPDATE modulo SET es_addon = 'S', precio_mensual = XX.XX WHERE id = @modulo_boton_pago_id;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 
    m.id,
    m.nombre,
    m.ruta,
    m.estado,
    m.mostrar,
    COUNT(md.id) AS num_rutas
FROM modulo m
LEFT JOIN modulo_detalle md ON m.id = md.modulo_id
WHERE m.ruta = '/dashboard/boton-pago'
GROUP BY m.id;

SELECT 
    pm.perfil_id,
    p.nombre AS perfil,
    pm.modulo_id,
    m.nombre AS modulo,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
INNER JOIN perfil p ON pm.perfil_id = p.id
INNER JOIN modulo m ON pm.modulo_id = m.id
WHERE m.ruta = '/dashboard/boton-pago';
