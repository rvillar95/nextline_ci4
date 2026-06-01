-- =============================================================================
-- Seed: perfil Alumno + módulos Gym (modulo/modulo_detalle/perfil_modulo)
-- =============================================================================
-- Ajusta IDs según tu data real si ya existen perfiles/módulos con mismos nombres.
-- Recomendación: ejecutar en una transacción y validar en ambiente dev.
-- =============================================================================

START TRANSACTION;

-- -----------------------------------------------------------------------------
-- Perfil Alumno (poder=1). Si ya existe un perfil similar, reutilizarlo.
-- -----------------------------------------------------------------------------
INSERT INTO `perfil` (`nombre`, `descripcion`, `poder`, `empresa_id`, `estado`, `fcreacion`)
SELECT 'Alumno', 'Acceso portal alumno (programas y suscripción)', 1, NULL, 'A', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `perfil` WHERE `nombre` = 'Alumno' AND `poder` = 1);

-- -----------------------------------------------------------------------------
-- Módulo padre: Gimnasio
-- -----------------------------------------------------------------------------
INSERT INTO `modulo` (`nombre`, `icono`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT 'Gimnasio', 'dumbbell', '/dashboard/gym', 'S', 90, 'A', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym');

-- -----------------------------------------------------------------------------
-- Paquete (SaaS) opcional para habilitar módulo Gimnasio vía paquetes
-- -----------------------------------------------------------------------------
INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`)
SELECT 'Gimnasio', 'gimnasio', 'Módulos de gestión gimnasio (rutinas, programas, alumnos)', 0, 0, 'A', 90, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'gimnasio');

-- Vincular módulo Gimnasio al paquete Gimnasio (si existe la tabla paquete_modulo)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.ruta = '/dashboard/gym'
WHERE p.slug = 'gimnasio'
  AND NOT EXISTS (
    SELECT 1 FROM `paquete_modulo` pm
    WHERE pm.paquete_id = p.id AND pm.modulo_id = m.id
  );

-- -----------------------------------------------------------------------------
-- Módulos detalle (rutas). Usamos rutas absolutas como el sistema existente.
-- -----------------------------------------------------------------------------
INSERT INTO `modulo_detalle` (`modulo_id`, `nombre`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT m.id, 'Ejercicios', '/dashboard/gym/ejercicio/lista', 'S', 1, 'A', NOW()
FROM `modulo` m
WHERE m.ruta = '/dashboard/gym'
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` md WHERE md.ruta = '/dashboard/gym/ejercicio/lista');

INSERT INTO `modulo_detalle` (`modulo_id`, `nombre`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT m.id, 'Rutinas', '/dashboard/gym/rutina/lista', 'S', 2, 'A', NOW()
FROM `modulo` m
WHERE m.ruta = '/dashboard/gym'
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` md WHERE md.ruta = '/dashboard/gym/rutina/lista');

INSERT INTO `modulo_detalle` (`modulo_id`, `nombre`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT m.id, 'Programas', '/dashboard/gym/programa/lista', 'S', 3, 'A', NOW()
FROM `modulo` m
WHERE m.ruta = '/dashboard/gym'
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` md WHERE md.ruta = '/dashboard/gym/programa/lista');

INSERT INTO `modulo_detalle` (`modulo_id`, `nombre`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT m.id, 'Alumnos', '/dashboard/gym/alumno/lista', 'S', 4, 'A', NOW()
FROM `modulo` m
WHERE m.ruta = '/dashboard/gym'
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` md WHERE md.ruta = '/dashboard/gym/alumno/lista');

INSERT INTO `modulo_detalle` (`modulo_id`, `nombre`, `ruta`, `mostrar`, `orden`, `estado`, `fcreacion`)
SELECT m.id, 'Asignaciones', '/dashboard/gym/asignacion/lista', 'S', 5, 'A', NOW()
FROM `modulo` m
WHERE m.ruta = '/dashboard/gym'
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` md WHERE md.ruta = '/dashboard/gym/asignacion/lista');

COMMIT;

