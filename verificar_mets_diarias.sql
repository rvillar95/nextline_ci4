-- =====================================================
-- COMPARAR METs DE ACTIVIDADES DIARIAS: BD vs Referencia
-- Según tabla ACTIVIDADES DIARIAS de referencia
-- =====================================================

SELECT
    ref.nombre                    AS referencia_nombre,
    ref.mets_ref                  AS mets_referencia,
    a.mets                        AS mets_bd,
    a.id                          AS id_bd,
    CASE
        WHEN a.id IS NULL THEN 'FALTA EN BD'
        WHEN ABS(COALESCE(a.mets, 0) - ref.mets_ref) < 0.01 THEN 'OK'
        ELSE 'DIFIERE'
    END                           AS estado,
    ROUND(COALESCE(a.mets, 0) - ref.mets_ref, 2) AS diferencia
FROM (
    SELECT 'Durmiendo' AS nombre, 0.9 AS mets_ref UNION ALL
    SELECT 'Acostado despierto', 1.1 UNION ALL
    SELECT 'Sentado', 1.2 UNION ALL
    SELECT 'De pie', 1.4 UNION ALL
    SELECT 'Viendo TV', 0.9 UNION ALL
    SELECT 'En clases o estudiar', 1.8 UNION ALL
    SELECT 'Trabajo of. Sentado', 1.3 UNION ALL
    SELECT 'Trabajo of. Movimiento', 1.6 UNION ALL
    SELECT 'Cocinar, instrum, o juego niño sent.', 2.5 UNION ALL
    SELECT 'Barrer, W de hospital, carga, actor', 3.0 UNION ALL
    SELECT 'Planchar', 1.4 UNION ALL
    SELECT 'Lavar vajilla', 1.7 UNION ALL
    SELECT 'Caminar lento', 2.2 UNION ALL
    SELECT 'Caminar Moderado', 2.9 UNION ALL
    SELECT 'Caminar Rápido', 5.5 UNION ALL
    SELECT 'Subir 1 piso o child care', 3.5
) AS ref
LEFT JOIN actividad_met a ON a.tipo = 'diaria' AND TRIM(a.nombre) = TRIM(ref.nombre)
ORDER BY
    CASE
        WHEN a.id IS NULL THEN 1
        WHEN ABS(COALESCE(a.mets, 0) - ref.mets_ref) >= 0.01 THEN 2
        ELSE 3
    END,
    ref.nombre;
