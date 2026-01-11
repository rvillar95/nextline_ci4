-- =====================================================
-- AGREGAR RUTA getDetalleCita AL MÓDULO DE AGENDA
-- =====================================================
-- Este script agrega el permiso para obtener información
-- completa de una cita (detalle_agenda) al módulo de agenda
-- =====================================================

INSERT INTO `modulo_detalle` 
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
(206, 33, 'Ver Detalle Completo de Cita', '/getDetalleCita', 'ver', 'A', 'N', 15, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Ver Detalle Completo de Cita',
    `accion` = 'ver',
    `estado` = 'A',
    `mostrar` = 'N',
    `orden` = 15,
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
SELECT id, modulo_id, descripcion, ruta, accion, estado 
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta = '/getDetalleCita';
