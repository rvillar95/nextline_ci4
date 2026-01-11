-- =====================================================
-- AGREGAR RUTAS DE COMPARACIÓN DE HISTORIALES CLÍNICOS
-- =====================================================
-- Este script agrega los permisos necesarios para el
-- módulo de comparación de historiales clínicos
-- =====================================================

INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(232, 36, 'Comparar Historiales Clínicos', '/comparar', 'ver', 'A', 'S', 10, NOW(), NOW(), '0000-00-00 00:00:00'),
(233, 36, 'Obtener Historiales de Paciente', '/getHistorialesPaciente', 'ver', 'A', 'N', 11, NOW(), NOW(), '0000-00-00 00:00:00'),
(234, 36, 'Comparar Múltiples Historiales', '/compararHistoriales', 'ver', 'A', 'N', 12, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregaron correctamente
SELECT id, modulo_id, descripcion, ruta, accion, estado, mostrar 
FROM modulo_detalle 
WHERE modulo_id = 36 AND ruta IN ('/comparar', '/getHistorialesPaciente', '/compararHistoriales')
ORDER BY orden;

-- =====================================================
