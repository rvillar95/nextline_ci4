-- =====================================================
-- CORREGIR ESTADO_CITA DE HORARIOS DISPONIBLES
-- =====================================================
-- Este script corrige los horarios que están marcados
-- como 'pendiente' cuando deberían estar como NULL (disponible)
-- =====================================================

-- 1. Verificar cuántos horarios tienen estado_cita incorrecto
SELECT 
    estado_cita,
    COUNT(*) as cantidad,
    CASE 
        WHEN paciente_id IS NULL AND estado_cita = 'pendiente' THEN '❌ INCORRECTO - Debe ser NULL'
        WHEN paciente_id IS NULL AND estado_cita IS NULL THEN '✅ CORRECTO'
        WHEN paciente_id IS NOT NULL AND estado_cita = 'pendiente' THEN '✅ CORRECTO'
        ELSE '⚠️ REVISAR'
    END AS estado
FROM detalle_agenda
GROUP BY estado_cita, paciente_id IS NULL
ORDER BY estado_cita, paciente_id IS NULL;

-- 2. Corregir horarios disponibles que están marcados como 'pendiente'
-- Los horarios sin paciente asignado deben tener estado_cita = NULL
UPDATE `detalle_agenda`
SET `estado_cita` = NULL
WHERE `paciente_id` IS NULL
  AND `estado_cita` = 'pendiente';

-- 3. Verificar corrección
SELECT 
    'Horarios disponibles (sin paciente)' AS tipo,
    COUNT(*) as cantidad
FROM detalle_agenda
WHERE paciente_id IS NULL
  AND estado_cita IS NULL;

SELECT 
    'Horarios con paciente pendiente' AS tipo,
    COUNT(*) as cantidad
FROM detalle_agenda
WHERE paciente_id IS NOT NULL
  AND estado_cita = 'pendiente';

-- 4. Mostrar resumen final
SELECT 
    CASE 
        WHEN paciente_id IS NULL THEN 'Disponible (sin paciente)'
        WHEN estado_cita = 'pendiente' THEN 'Pendiente (con paciente)'
        WHEN estado_cita = 'confirmada' THEN 'Confirmada'
        WHEN estado_cita = 'cancelada' THEN 'Cancelada'
        ELSE estado_cita
    END AS estado_display,
    COUNT(*) as cantidad
FROM detalle_agenda
GROUP BY 
    CASE 
        WHEN paciente_id IS NULL THEN 'Disponible (sin paciente)'
        WHEN estado_cita = 'pendiente' THEN 'Pendiente (con paciente)'
        WHEN estado_cita = 'confirmada' THEN 'Confirmada'
        WHEN estado_cita = 'cancelada' THEN 'Cancelada'
        ELSE estado_cita
    END
ORDER BY cantidad DESC;
