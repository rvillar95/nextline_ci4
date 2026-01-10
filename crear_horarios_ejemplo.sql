-- =====================================================
-- CREAR HORARIOS DE EJEMPLO PARA AGENDA
-- =====================================================
-- Este script crea horarios disponibles para los
-- próximos 30 días para que puedas probar el sistema
-- =====================================================

-- Nota: Ajusta el usuario_id según tu usuario de nutricionista
-- SELECT id FROM usuario WHERE perfil_id = 9 LIMIT 1;

-- Crear horarios para los próximos 30 días
-- Horarios de lunes a viernes: 9:00-13:00 y 15:00-18:00
-- Horarios de sábado: 9:00-13:00

SET @usuario_id = (SELECT id FROM usuario WHERE perfil_id = 9 LIMIT 1);
SET @fecha_inicio = CURDATE();
SET @fecha_fin = DATE_ADD(CURDATE(), INTERVAL 30 DAY);

-- Crear registros en agenda y detalle_agenda
-- Para cada día hábil, crear bloques de horarios

INSERT INTO `agenda` (`fecha`, `hora_inicio`, `hora_fin`, `estado_id`, `tipo_id`)
SELECT 
    fecha,
    '09:00:00' as hora_inicio,
    '18:00:00' as hora_fin,
    NULL as estado_id,
    1 as tipo_id
FROM (
    SELECT DATE_ADD(@fecha_inicio, INTERVAL seq.seq DAY) as fecha
    FROM (
        SELECT 0 as seq UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
        SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
        SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL
        SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
        SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL
        SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29
    ) seq
    WHERE DATE_ADD(@fecha_inicio, INTERVAL seq.seq DAY) <= @fecha_fin
    AND WEEKDAY(DATE_ADD(@fecha_inicio, INTERVAL seq.seq DAY)) < 6  -- Lunes a Sábado (0=Lunes, 5=Sábado)
) dias
ON DUPLICATE KEY UPDATE hora_inicio = '09:00:00', hora_fin = '18:00:00';

-- Crear detalle_agenda (horarios específicos) para cada día
-- Horarios de 30 minutos cada uno

INSERT INTO `detalle_agenda` (`agenda_id`, `usuario_id`, `orden`, `hora_inicio`, `hora_fin`, `estado`, `estado_solicitud_id`, `modalidad_id`, `forma_asignacion`)
SELECT 
    a.id as agenda_id,
    @usuario_id as usuario_id,
    @orden := @orden + 1 as orden,
    ADDTIME('09:00:00', SEC_TO_TIME((@orden - 1) * 1800)) as hora_inicio,  -- 30 minutos = 1800 segundos
    ADDTIME('09:00:00', SEC_TO_TIME(@orden * 1800)) as hora_fin,
    1 as estado,  -- Disponible
    1 as estado_solicitud_id,  -- Pendiente
    3 as modalidad_id,  -- Presencial (ajustar según tu sistema)
    'Manual' as forma_asignacion
FROM agenda a
CROSS JOIN (
    SELECT @orden := 0
) r
WHERE a.fecha >= @fecha_inicio
AND a.fecha <= @fecha_fin
AND WEEKDAY(a.fecha) < 6  -- Lunes a Sábado
AND NOT EXISTS (
    SELECT 1 FROM detalle_agenda da 
    WHERE da.agenda_id = a.id 
    AND da.usuario_id = @usuario_id
)
AND ADDTIME('09:00:00', SEC_TO_TIME((@orden) * 1800)) < 
    CASE 
        WHEN WEEKDAY(a.fecha) = 5 THEN '13:00:00'  -- Sábado hasta 13:00
        ELSE '18:00:00'  -- Lunes a Viernes hasta 18:00
    END;

-- Verificar los horarios creados
SELECT 
    'HORARIOS CREADOS' as tipo,
    COUNT(*) as total_horarios,
    MIN(a.fecha) as fecha_inicio,
    MAX(a.fecha) as fecha_fin
FROM detalle_agenda da
JOIN agenda a ON a.id = da.agenda_id
WHERE da.usuario_id = @usuario_id
AND a.fecha >= @fecha_inicio;
