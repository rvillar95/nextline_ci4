-- =====================================================
-- COMPARAR METs DE DEPORTES: BD vs Tabla de referencia
-- Ejecutar en phpMyAdmin o cliente MySQL
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
    SELECT 'Danza / Baile' AS nombre, 4.5 AS mets_ref UNION ALL
    SELECT 'Danza (Ballet - Moderna)', 6.0 UNION ALL
    SELECT 'Basketball', 8.0 UNION ALL
    SELECT 'Basketball Entrenamiento', 6.0 UNION ALL
    SELECT 'Boxeo', 12.0 UNION ALL
    SELECT 'Golf', 4.5 UNION ALL
    SELECT 'Gimnasia', 4.0 UNION ALL
    SELECT 'Handball', 12.0 UNION ALL
    SELECT 'Hockey cesped / Hielo', 8.0 UNION ALL
    SELECT 'Artes marciales', 10.0 UNION ALL
    SELECT 'Moto-cross', 4.0 UNION ALL
    SELECT 'Mountaint bike, BMX', 8.5 UNION ALL
    SELECT 'Polo', 8.0 UNION ALL
    SELECT 'Racketball', 10.0 UNION ALL
    SELECT 'Racketball ocasional', 7.0 UNION ALL
    SELECT 'Escalada', 11.0 UNION ALL
    SELECT 'Rugby', 10.0 UNION ALL
    SELECT 'Skate', 5.0 UNION ALL
    SELECT 'Roller', 7.0 UNION ALL
    SELECT 'Sky', 7.0 UNION ALL
    SELECT 'Fútbol', 10.0 UNION ALL
    SELECT 'Fútbol Ocasional', 7.0 UNION ALL
    SELECT 'Squash', 12.0 UNION ALL
    SELECT 'Ping-Pong', 4.0 UNION ALL
    SELECT 'Tenis', 7.0 UNION ALL
    SELECT 'Volleyball', 4.0 UNION ALL
    SELECT 'Volleyball Ocasional', 3.0 UNION ALL
    SELECT 'Volley playa', 8.0 UNION ALL
    SELECT 'Subir cerros 0-20 Lbs, 0-44 Kg', 7.2 UNION ALL
    SELECT 'Subir cerros > 21Lbs, Mayor a 44 Kg', 8.5 UNION ALL
    SELECT 'Canotaje', 7.0 UNION ALL
    SELECT 'Kajak o rafting', 5.0 UNION ALL
    SELECT 'Buceo Apnea', 5.0 UNION ALL
    SELECT 'Surf', 3.0 UNION ALL
    SELECT 'Natación', 10.0 UNION ALL
    SELECT 'Waterpolo', 10.0 UNION ALL
    SELECT 'Rafting', 5.0 UNION ALL
    SELECT 'Entrenador', 4.0 UNION ALL
    SELECT 'Otras Acividades', 1.4
) AS ref
LEFT JOIN actividad_met a ON a.tipo = 'deporte' AND TRIM(a.nombre) = TRIM(ref.nombre)
ORDER BY
    CASE
        WHEN a.id IS NULL THEN 1
        WHEN ABS(COALESCE(a.mets, 0) - ref.mets_ref) >= 0.01 THEN 2
        ELSE 3
    END,
    ref.nombre;

-- =====================================================
-- Solo los que DIFIEREN o FALTAN (resumen para corregir)
-- =====================================================
/*
SELECT
    ref.nombre,
    ref.mets_ref AS debe_ser,
    a.mets      AS tiene_bd,
    CASE WHEN a.id IS NULL THEN 'FALTA' ELSE 'DIFIERE' END AS accion
FROM (
    SELECT 'Danza / Baile' AS nombre, 4.5 AS mets_ref UNION ALL
    SELECT 'Danza (Ballet - Moderna)', 6.0 UNION ALL
    SELECT 'Basketball', 8.0 UNION ALL
    SELECT 'Basketball Entrenamiento', 6.0 UNION ALL
    SELECT 'Boxeo', 12.0 UNION ALL
    SELECT 'Golf', 4.5 UNION ALL
    SELECT 'Gimnasia', 4.0 UNION ALL
    SELECT 'Handball', 12.0 UNION ALL
    SELECT 'Hockey cesped / Hielo', 8.0 UNION ALL
    SELECT 'Artes marciales', 10.0 UNION ALL
    SELECT 'Moto-cross', 4.0 UNION ALL
    SELECT 'Mountaint bike, BMX', 8.5 UNION ALL
    SELECT 'Polo', 8.0 UNION ALL
    SELECT 'Racketball', 10.0 UNION ALL
    SELECT 'Racketball ocasional', 7.0 UNION ALL
    SELECT 'Escalada', 11.0 UNION ALL
    SELECT 'Rugby', 10.0 UNION ALL
    SELECT 'Skate', 5.0 UNION ALL
    SELECT 'Roller', 7.0 UNION ALL
    SELECT 'Sky', 7.0 UNION ALL
    SELECT 'Fútbol', 10.0 UNION ALL
    SELECT 'Fútbol Ocasional', 7.0 UNION ALL
    SELECT 'Squash', 12.0 UNION ALL
    SELECT 'Ping-Pong', 4.0 UNION ALL
    SELECT 'Tenis', 7.0 UNION ALL
    SELECT 'Volleyball', 4.0 UNION ALL
    SELECT 'Volleyball Ocasional', 3.0 UNION ALL
    SELECT 'Volley playa', 8.0 UNION ALL
    SELECT 'Subir cerros 0-20 Lbs, 0-44 Kg', 7.2 UNION ALL
    SELECT 'Subir cerros > 21Lbs, Mayor a 44 Kg', 8.5 UNION ALL
    SELECT 'Canotaje', 7.0 UNION ALL
    SELECT 'Kajak o rafting', 5.0 UNION ALL
    SELECT 'Buceo Apnea', 5.0 UNION ALL
    SELECT 'Surf', 3.0 UNION ALL
    SELECT 'Natación', 10.0 UNION ALL
    SELECT 'Waterpolo', 10.0 UNION ALL
    SELECT 'Rafting', 5.0 UNION ALL
    SELECT 'Entrenador', 4.0 UNION ALL
    SELECT 'Otras Acividades', 1.4
) AS ref
LEFT JOIN actividad_met a ON a.tipo = 'deporte' AND TRIM(a.nombre) = TRIM(ref.nombre)
WHERE a.id IS NULL OR ABS(COALESCE(a.mets, 0) - ref.mets_ref) >= 0.01
ORDER BY ref.nombre;
*/
