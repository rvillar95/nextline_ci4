-- =====================================================
-- ACTUALIZAR METs DE ACTIVIDADES DIARIAS
-- Según tabla ACTIVIDADES DIARIAS de referencia
-- Ejecutar en la BD para igualar METs a la referencia
-- =====================================================

UPDATE `actividad_met` SET `mets` = 1.4  WHERE `tipo` = 'diaria' AND `nombre` = 'De pie';
UPDATE `actividad_met` SET `mets` = 0.9  WHERE `tipo` = 'diaria' AND `nombre` = 'Viendo TV';
UPDATE `actividad_met` SET `mets` = 1.8  WHERE `tipo` = 'diaria' AND `nombre` = 'En clases o estudiar';
UPDATE `actividad_met` SET `mets` = 1.3  WHERE `tipo` = 'diaria' AND `nombre` = 'Trabajo of. Sentado';
UPDATE `actividad_met` SET `mets` = 1.6  WHERE `tipo` = 'diaria' AND `nombre` = 'Trabajo of. Movimiento';
UPDATE `actividad_met` SET `mets` = 2.5  WHERE `tipo` = 'diaria' AND `nombre` = 'Cocinar, instrum, o juego niño sent.';
UPDATE `actividad_met` SET `mets` = 1.4  WHERE `tipo` = 'diaria' AND `nombre` = 'Planchar';
UPDATE `actividad_met` SET `mets` = 1.7  WHERE `tipo` = 'diaria' AND `nombre` = 'Lavar vajilla';
UPDATE `actividad_met` SET `mets` = 2.2  WHERE `tipo` = 'diaria' AND `nombre` = 'Caminar lento';
UPDATE `actividad_met` SET `mets` = 2.9  WHERE `tipo` = 'diaria' AND `nombre` = 'Caminar Moderado';
UPDATE `actividad_met` SET `mets` = 3.5  WHERE `tipo` = 'diaria' AND `nombre` = 'Subir 1 piso o child care';

-- Verificación: ejecutar verificar_mets_diarias.sql después para confirmar que todo quede OK
