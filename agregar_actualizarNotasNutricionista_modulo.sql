-- =====================================================
-- AGREGAR RUTA actualizarNotasNutricionista AL MÓDULO DE AGENDA
-- =====================================================
-- Este script agrega el permiso para actualizar las notas
-- del nutricionista en una cita (detalle_agenda)
-- =====================================================

INSERT INTO `modulo_detalle` 
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
(207, 33, 'Actualizar Notas del Nutricionista', '/actualizarNotasNutricionista', 'editar', 'A', 'N', 16, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Actualizar Notas del Nutricionista',
    `accion` = 'editar',
    `estado` = 'A',
    `mostrar` = 'N',
    `orden` = 16,
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
SELECT id, modulo_id, descripcion, ruta, accion, estado 
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta = '/actualizarNotasNutricionista';
