-- =====================================================
-- AGREGAR DEPORTES FALTANTES A actividad_met
-- Según tabla DEPORTES de referencia
-- Ejecutar en la BD después de crear_sistema_plan_alimentario.sql
-- =====================================================

-- Solo inserta si no existe ya (por tipo + nombre)
INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Danza (Ballet - Moderna)', 6.0, 'Danza ballet o moderna', 25, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Danza (Ballet - Moderna)');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Basketball Entrenamiento', 6.0, 'Entrenamiento basketball', 26, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Basketball Entrenamiento');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Golf', 4.5, 'Golf', 27, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Golf');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Gimnasia', 4.0, 'Gimnasia', 28, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Gimnasia');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Handball', 12.0, 'Handball', 29, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Handball');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Hockey cesped / Hielo', 8.0, 'Hockey césped o hielo', 30, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Hockey cesped / Hielo');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Artes marciales', 10.0, 'Artes marciales', 31, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Artes marciales');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Moto-cross', 4.0, 'Moto-cross', 32, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Moto-cross');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Mountaint bike, BMX', 8.5, 'Mountain bike o BMX', 33, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Mountaint bike, BMX');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Polo', 8.0, 'Polo', 34, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Polo');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Racketball', 10.0, 'Racketball', 35, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Racketball');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Racketball ocasional', 7.0, 'Racketball ocasional', 36, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Racketball ocasional');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Rugby', 10.0, 'Rugby', 37, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Rugby');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Skate', 5.0, 'Skateboard', 38, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Skate');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Roller', 7.0, 'Patinaje en roller', 39, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Roller');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Sky', 7.0, 'Esquí', 40, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Sky');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Fútbol Ocasional', 7.0, 'Fútbol ocasional', 41, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Fútbol Ocasional');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Squash', 12.0, 'Squash', 42, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Squash');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Ping-Pong', 4.0, 'Tenis de mesa', 43, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Ping-Pong');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Tenis', 7.0, 'Tenis', 44, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Tenis');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Volleyball', 4.0, 'Volleyball', 45, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Volleyball');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Volleyball Ocasional', 3.0, 'Volleyball ocasional', 46, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Volleyball Ocasional');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Volley playa', 8.0, 'Vóley playa', 47, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Volley playa');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Subir cerros 0-20 Lbs, 0-44 Kg', 7.2, 'Subir cerros carga ligera', 48, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Subir cerros 0-20 Lbs, 0-44 Kg');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Subir cerros > 21Lbs, Mayor a 44 Kg', 8.5, 'Subir cerros carga pesada', 49, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Subir cerros > 21Lbs, Mayor a 44 Kg');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Canotaje', 7.0, 'Canotaje', 50, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Canotaje');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Kajak o rafting', 5.0, 'Kayak o rafting', 51, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Kajak o rafting');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Buceo Apnea', 5.0, 'Buceo en apnea', 52, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Buceo Apnea');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Surf', 3.0, 'Surf', 53, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Surf');

INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
SELECT 'deporte', 'Waterpolo', 10.0, 'Waterpolo', 54, 'A'
WHERE NOT EXISTS (SELECT 1 FROM `actividad_met` WHERE tipo='deporte' AND nombre='Waterpolo');

-- Ajustar METs según referencia (Escalada 11, Entrenador 4)
UPDATE `actividad_met` SET `mets` = 11.0, `descripcion` = 'Escalada' WHERE `tipo` = 'deporte' AND `nombre` = 'Escalada';
UPDATE `actividad_met` SET `mets` = 4.0 WHERE `tipo` = 'deporte' AND `nombre` = 'Entrenador';

-- =====================================================
-- FIN
-- =====================================================
