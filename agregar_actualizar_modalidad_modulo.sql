-- =====================================================
-- AGREGAR DETALLE DE MÓDULO: Actualizar Modalidad
-- =====================================================
-- Este script agrega el detalle de módulo para la
-- funcionalidad de actualizar la modalidad de un horario
-- en el módulo de Agenda (módulo 33)
-- =====================================================

-- Verificar el último ID usado para el módulo Agenda
-- SELECT MAX(id) FROM modulo_detalle WHERE modulo_id = 33;

-- Agregar el detalle de módulo para actualizarModalidad
INSERT INTO `modulo_detalle` 
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
(205, 33, 'Actualizar Modalidad de Horario', '/actualizarModalidad', 'editar', 'A', 'N', 14, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Actualizar Modalidad de Horario',
    `accion` = 'editar',
    `estado` = 'A',
    `mostrar` = 'N',
    `orden` = 14,
    `factualizacion` = NOW();

-- Verificar que se insertó correctamente
-- SELECT * FROM modulo_detalle WHERE modulo_id = 33 AND ruta = '/actualizarModalidad';
