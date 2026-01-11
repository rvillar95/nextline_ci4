-- =====================================================
-- AGREGAR CAMPO DE NOTAS/RECORDATORIOS DEL NUTRICIONISTA
-- =====================================================
-- Este script agrega un campo para que el nutricionista
-- pueda agregar notas, recordatorios o puntos clave
-- para cada cita (detalle_agenda)
-- =====================================================

ALTER TABLE `detalle_agenda`
ADD COLUMN `notas_nutricionista` TEXT NULL COMMENT 'Notas, recordatorios o puntos clave del nutricionista para esta cita' AFTER `observaciones`;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
DESCRIBE `detalle_agenda`;
