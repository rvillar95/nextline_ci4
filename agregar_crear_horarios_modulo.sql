-- =====================================================
-- AGREGAR DETALLE DE MÓDULO: Crear Horarios Disponibles
-- =====================================================
-- Este script agrega el detalle de módulo para la
-- funcionalidad de crear horarios disponibles masivamente
-- en el módulo de Agenda (módulo 33)
-- =====================================================

-- Verificar el último ID usado para el módulo Agenda
-- SELECT MAX(id) FROM modulo_detalle WHERE modulo_id = 33;

-- Agregar el detalle de módulo para crearHorarios
INSERT INTO `modulo_detalle` 
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
(204, 33, 'Crear Horarios Disponibles', '/crearHorarios', 'registrar', 'A', 'N', 13, NOW(), NOW(), '0000-00-00 00:00:00');

-- Verificar que se insertó correctamente
-- SELECT * FROM modulo_detalle WHERE modulo_id = 33 ORDER BY orden;
