-- =====================================================
-- AGREGAR RUTA getDetalleCita AL MÓDULO DE AGENDA
-- =====================================================
-- Este script agrega el permiso para obtener información
-- completa de una cita (detalle_agenda) al módulo de agenda
-- =====================================================

-- Verificar si existe y actualizar o insertar
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Agenda' LIMIT 1);
SET @siguiente_orden = (SELECT IFNULL(MAX(orden), 0) + 1 FROM modulo_detalle WHERE modulo_id = @modulo_id);

INSERT INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Ver Detalle Completo de Cita', '/getDetalleCita', 'ver', 'A', 'N', @siguiente_orden, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Ver Detalle Completo de Cita',
    `ruta` = '/getDetalleCita',
    `accion` = 'ver',
    `estado` = 'A',
    `mostrar` = 'N',
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
SELECT id, modulo_id, descripcion, ruta, accion, estado 
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta = '/getDetalleCita';
