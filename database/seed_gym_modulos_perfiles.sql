-- =============================================================================

-- Seed Gym: 5 módulos independientes + detalles por acción (patrón Pacientes)

-- =============================================================================



START TRANSACTION;



INSERT INTO `perfil` (`nombre`, `poder`, `empresa_id`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Alumno', 1, NULL, 'A', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL

WHERE NOT EXISTS (SELECT 1 FROM `perfil` WHERE `nombre` = 'Alumno' AND `poder` = 1 LIMIT 1);



SET @perfil_alumno_id = (SELECT `id` FROM `perfil` WHERE `nombre` = 'Alumno' AND `poder` = 1 ORDER BY `id` ASC LIMIT 1);



INSERT INTO `menu_grupo` (`slug`, `etiqueta`, `orden`, `estado`, `fcreacion`, `factualizacion`)

SELECT 'gimnasio', 'Gimnasio', 32, 'A', NOW(), NOW()

FROM DUAL

WHERE NOT EXISTS (SELECT 1 FROM `menu_grupo` WHERE `slug` = 'gimnasio' LIMIT 1);



SET @mg_id = (SELECT `id` FROM `menu_grupo` WHERE `slug` = 'gimnasio' LIMIT 1);



-- Módulos gym (ruta base del controlador)

INSERT INTO `modulo` (`nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Ejercicios', 'Catálogo de ejercicios', 'N', '/dashboard/gym/ejercicio', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym/ejercicio' LIMIT 1);



INSERT INTO `modulo` (`nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Rutinas', 'Rutinas de entrenamiento', 'N', '/dashboard/gym/rutina', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym/rutina' LIMIT 1);



INSERT INTO `modulo` (`nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Programas', 'Programas de entrenamiento', 'N', '/dashboard/gym/programa', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym/programa' LIMIT 1);



INSERT INTO `modulo` (`nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Alumnos gym', 'Alumnos del gimnasio', 'N', '/dashboard/gym/alumno', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym/alumno' LIMIT 1);



INSERT INTO `modulo` (`nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Asignaciones gym', 'Asignar programas a alumnos', 'N', '/dashboard/gym/asignacion', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/dashboard/gym/asignacion' LIMIT 1);



SET @m_ejercicio  = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/gym/ejercicio' LIMIT 1);

SET @m_rutina     = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/gym/rutina' LIMIT 1);

SET @m_programa   = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/gym/programa' LIMIT 1);

SET @m_alumno     = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/gym/alumno' LIMIT 1);

SET @m_asignacion = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/gym/asignacion' LIMIT 1);



UPDATE `modulo` SET `menu_grupo_id` = @mg_id

WHERE `id` IN (@m_ejercicio, @m_rutina, @m_programa, @m_alumno, @m_asignacion) AND @mg_id IS NOT NULL;



-- Desactivar módulo padre único si existía

UPDATE `modulo` SET `estado` = 'I', `mostrar` = 'N' WHERE `ruta` = '/dashboard/gym';



-- Paquete

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)

SELECT 'Gimnasio', 'gimnasio', 'Ejercicios, rutinas, programas, alumnos y asignaciones', 0, 0, 'A', 90, NOW(), NOW()

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'gimnasio' LIMIT 1);



SET @paquete_gym = (SELECT `id` FROM `paquetes` WHERE `slug` = 'gimnasio' LIMIT 1);



INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)

SELECT @paquete_gym, m.`id`, 'S', NOW()

FROM `modulo` m

WHERE @paquete_gym IS NOT NULL

  AND m.`ruta` IN (

    '/dashboard/gym/ejercicio', '/dashboard/gym/rutina', '/dashboard/gym/programa',

    '/dashboard/gym/alumno', '/dashboard/gym/asignacion'

  )

  AND NOT EXISTS (SELECT 1 FROM `paquete_modulo` pm WHERE pm.`paquete_id` = @paquete_gym AND pm.`modulo_id` = m.`id`);



-- Detalles: ver database/migration_gym_modulos_split.sql (mismos INSERT)

-- Perfiles con Agenda → gym completo

INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT pm.`perfil_id`, m.`id`, 1, 1, 1, 1, 'A',
       CASE m.`ruta`
         WHEN '/dashboard/gym/ejercicio' THEN 91 WHEN '/dashboard/gym/rutina' THEN 92
         WHEN '/dashboard/gym/programa' THEN 93 WHEN '/dashboard/gym/alumno' THEN 94
         WHEN '/dashboard/gym/asignacion' THEN 95 ELSE 90 END,
       NOW(), NOW(), '0000-00-00 00:00:00'

FROM `perfil_modulo` pm

INNER JOIN `modulo` ma ON ma.id = pm.modulo_id AND ma.ruta LIKE '%agenda%' AND ma.sa = 'N'

INNER JOIN `modulo` m ON m.`ruta` IN (

    '/dashboard/gym/ejercicio', '/dashboard/gym/rutina', '/dashboard/gym/programa',

    '/dashboard/gym/alumno', '/dashboard/gym/asignacion'

)

WHERE pm.`estado` = 'A' AND pm.`ver` = 1

  AND NOT EXISTS (SELECT 1 FROM `perfil_modulo` x WHERE x.`perfil_id` = pm.`perfil_id` AND x.`modulo_id` = m.`id`);



-- Super admin

INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT p.`id`, m.`id`, 1, 1, 1, 1, 'A', 90, NOW(), NOW(), '0000-00-00 00:00:00'

FROM `perfil` p

CROSS JOIN `modulo` m

WHERE p.`poder` = 3 AND p.`estado` = 'A'

  AND m.`ruta` IN (

    '/dashboard/gym/ejercicio', '/dashboard/gym/rutina', '/dashboard/gym/programa',

    '/dashboard/gym/alumno', '/dashboard/gym/asignacion'

  )

  AND NOT EXISTS (SELECT 1 FROM `perfil_modulo` x WHERE x.`perfil_id` = p.`id` AND x.`modulo_id` = m.`id`);



-- Entrenador

INSERT INTO `perfil` (`nombre`, `poder`, `empresa_id`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT 'Entrenador', 1, NULL, 'A', NOW(), NOW(), '0000-00-00 00:00:00'

FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `perfil` WHERE `nombre` = 'Entrenador' LIMIT 1);



SET @perfil_entrenador_id = (SELECT `id` FROM `perfil` WHERE `nombre` = 'Entrenador' ORDER BY `id` ASC LIMIT 1);



INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT @perfil_entrenador_id, m.`id`, 1, 0, 1, 0, 'A', 10, NOW(), NOW(), '0000-00-00 00:00:00'

FROM `modulo` m

WHERE @perfil_entrenador_id IS NOT NULL AND m.`ruta` IN ('/dashboard/menu', '/dashboard/mi-perfil')

  AND NOT EXISTS (SELECT 1 FROM `perfil_modulo` x WHERE x.`perfil_id` = @perfil_entrenador_id AND x.`modulo_id` = m.`id`);



INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)

SELECT @perfil_entrenador_id, m.`id`, 1, 1, 1, 1, 'A',

       CASE m.`ruta`

         WHEN '/dashboard/gym/ejercicio' THEN 91 WHEN '/dashboard/gym/rutina' THEN 92

         WHEN '/dashboard/gym/programa' THEN 93 WHEN '/dashboard/gym/alumno' THEN 94

         WHEN '/dashboard/gym/asignacion' THEN 95 ELSE 90 END,

       NOW(), NOW(), '0000-00-00 00:00:00'

FROM `modulo` m

WHERE @perfil_entrenador_id IS NOT NULL

  AND m.`ruta` IN (

    '/dashboard/gym/ejercicio', '/dashboard/gym/rutina', '/dashboard/gym/programa',

    '/dashboard/gym/alumno', '/dashboard/gym/asignacion'

  )

  AND NOT EXISTS (SELECT 1 FROM `perfil_modulo` x WHERE x.`perfil_id` = @perfil_entrenador_id AND x.`modulo_id` = m.`id`);



COMMIT;



SELECT @perfil_alumno_id AS perfil_alumno_id, @perfil_entrenador_id AS perfil_entrenador_id;

