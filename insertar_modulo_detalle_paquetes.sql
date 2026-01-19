-- =====================================================
-- INSERTAR MODULO_DETALLE PARA MÓDULO PAQUETES (ID=40)
-- =====================================================
-- Este script inserta las rutas del módulo de paquetes
-- que faltan en la tabla modulo_detalle
-- =====================================================

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

-- Verificar que se insertaron correctamente
SELECT 
    md.id,
    md.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    md.descripcion,
    md.ruta AS detalle_ruta,
    CONCAT(m.ruta, md.ruta) AS ruta_completa,
    md.accion,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = 40
ORDER BY md.orden;
