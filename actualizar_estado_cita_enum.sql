-- =====================================================
-- ACTUALIZAR ENUM DE estado_cita PARA INCLUIR 'pendiente'
-- =====================================================
-- Este script actualiza el ENUM de estado_cita para
-- incluir 'pendiente' y corregir registros existentes
-- =====================================================

-- Verificar estructura actual
DESCRIBE `detalle_agenda`;

-- Modificar el ENUM para incluir 'pendiente' al inicio
ALTER TABLE `detalle_agenda`
MODIFY COLUMN `estado_cita` ENUM('pendiente','agendada','confirmada','en_proceso','completada','cancelada','no_asistio') NULL DEFAULT 'pendiente';

-- Actualizar registros que tienen 'agendada' a 'pendiente' (si aplica)
-- Solo actualizar los que fueron agendados recientemente y no tienen fecha_confirmacion
UPDATE `detalle_agenda`
SET `estado_cita` = 'pendiente'
WHERE `estado_cita` = 'agendada' 
  AND `paciente_id` IS NOT NULL
  AND `fecha_confirmacion` IS NULL;

-- Actualizar registros con estado_cita NULL a 'pendiente' (si tienen paciente)
UPDATE `detalle_agenda`
SET `estado_cita` = 'pendiente'
WHERE `estado_cita` IS NULL 
  AND `paciente_id` IS NOT NULL;

-- Verificar cambios
SELECT 
    estado_cita, 
    COUNT(*) as cantidad
FROM detalle_agenda
WHERE paciente_id IS NOT NULL
GROUP BY estado_cita;

-- =====================================================
-- VERIFICACIÓN FINAL
-- =====================================================
-- Verificar que no queden registros con estado_cita NULL cuando tienen paciente
SELECT COUNT(*) as registros_sin_estado
FROM detalle_agenda
WHERE paciente_id IS NOT NULL 
  AND estado_cita IS NULL;
