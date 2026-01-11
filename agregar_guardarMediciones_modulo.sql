-- =====================================================
-- AGREGAR PERMISO PARA GUARDAR MEDICIONES
-- =====================================================
-- Este script agrega el permiso necesario para guardar
-- mediciones corporales desde la vista de consulta
-- =====================================================

INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(221, 33, 'Guardar Mediciones Corporales', '/guardarMediciones', 'editar', 'A', 'N', 24, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();
