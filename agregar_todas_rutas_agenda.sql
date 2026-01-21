-- =====================================================
-- AGREGAR TODAS LAS RUTAS FALTANTES DEL MÓDULO AGENDA
-- =====================================================
-- Este script agrega todas las rutas del módulo Agenda
-- que están definidas en Routes.php pero faltan en modulo_detalle
-- =====================================================

SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Agenda' LIMIT 1);

-- 1. Verificar qué rutas existen actualmente
SELECT 
    md.descripcion,
    md.ruta,
    '✅ EXISTE' AS estado
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;

-- 2. Lista de todas las rutas que deberían existir (según Routes.php)
-- Rutas GET:
-- - lista
-- - gestionar
-- - calendario
-- - getAgendas
-- - getEventos
-- - getDetalleCita
-- - getAgenda
-- - consulta
-- - estadisticas
-- - getConsultasProximas
-- - getEstadisticas
-- - calendario/connect
-- - calendario/verificar-token

-- Rutas POST:
-- - agendar
-- - crearHorarios
-- - eliminarHorarios
-- - actualizarModalidad
-- - actualizarNotasNutricionista
-- - iniciarConsulta
-- - terminarConsulta
-- - guardarNotasConsulta
-- - guardarMediciones
-- - confirmarCita
-- - cancelarCita

-- 3. Obtener el siguiente orden disponible
SET @orden_base = (SELECT IFNULL(MAX(orden), 0) FROM modulo_detalle WHERE modulo_id = @modulo_id);

-- 4. Insertar todas las rutas faltantes
-- Rutas GET (usar INSERT IGNORE para evitar errores de duplicados)
INSERT IGNORE INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Lista de Agendas', '/lista', 'ver', 'A', 'S', @orden_base + 1, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Gestionar Agenda', '/gestionar', 'ver', 'A', 'S', @orden_base + 2, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Calendario', '/calendario', 'ver', 'A', 'S', @orden_base + 3, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Obtener Agendas', '/getAgendas', 'ver', 'A', 'N', @orden_base + 4, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Obtener Eventos', '/getEventos', 'ver', 'A', 'N', @orden_base + 5, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Ver Detalle de Cita', '/getDetalleCita', 'ver', 'A', 'N', @orden_base + 6, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Obtener Agenda', '/getAgenda', 'ver', 'A', 'N', @orden_base + 7, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Consulta de Paciente', '/consulta', 'ver', 'A', 'N', @orden_base + 8, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Estadísticas', '/estadisticas', 'ver', 'A', 'S', @orden_base + 9, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Consultas Próximas', '/getConsultasProximas', 'ver', 'A', 'N', @orden_base + 10, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Obtener Estadísticas', '/getEstadisticas', 'ver', 'A', 'N', @orden_base + 11, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Conectar Calendario', '/calendario/connect', 'ver', 'A', 'N', @orden_base + 12, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Verificar Token Calendario', '/calendario/verificar-token', 'ver', 'A', 'N', @orden_base + 13, NOW(), NOW(), '0000-00-00 00:00:00');

-- Actualizar rutas existentes para asegurar que estén activas
UPDATE `modulo_detalle`
SET `estado` = 'A', `factualizacion` = NOW()
WHERE `modulo_id` = @modulo_id
  AND `ruta` IN ('/lista', '/gestionar', '/calendario', '/getAgendas', '/getEventos', '/getDetalleCita', '/getAgenda', '/consulta', '/estadisticas', '/getConsultasProximas', '/getEstadisticas', '/calendario/connect', '/calendario/verificar-token');

-- Rutas POST (usar INSERT IGNORE para evitar errores de duplicados)
INSERT IGNORE INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Agendar Cita', '/agendar', 'registrar', 'A', 'N', @orden_base + 14, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Crear Horarios', '/crearHorarios', 'registrar', 'A', 'N', @orden_base + 15, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Eliminar Horarios', '/eliminarHorarios', 'eliminar', 'A', 'N', @orden_base + 16, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Actualizar Modalidad', '/actualizarModalidad', 'editar', 'A', 'N', @orden_base + 17, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Actualizar Notas Nutricionista', '/actualizarNotasNutricionista', 'editar', 'A', 'N', @orden_base + 18, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Iniciar Consulta', '/iniciarConsulta', 'editar', 'A', 'N', @orden_base + 19, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Terminar Consulta', '/terminarConsulta', 'editar', 'A', 'N', @orden_base + 20, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Guardar Notas Consulta', '/guardarNotasConsulta', 'editar', 'A', 'N', @orden_base + 21, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Guardar Mediciones', '/guardarMediciones', 'editar', 'A', 'N', @orden_base + 22, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Confirmar Cita', '/confirmarCita', 'editar', 'A', 'N', @orden_base + 23, NOW(), NOW(), '0000-00-00 00:00:00'),
    (@modulo_id, 'Cancelar Cita', '/cancelarCita', 'editar', 'A', 'N', @orden_base + 24, NOW(), NOW(), '0000-00-00 00:00:00');

-- Actualizar rutas POST existentes para asegurar que estén activas
UPDATE `modulo_detalle`
SET `estado` = 'A', `factualizacion` = NOW()
WHERE `modulo_id` = @modulo_id
  AND `ruta` IN ('/agendar', '/crearHorarios', '/eliminarHorarios', '/actualizarModalidad', '/actualizarNotasNutricionista', '/iniciarConsulta', '/terminarConsulta', '/guardarNotasConsulta', '/guardarMediciones', '/confirmarCita', '/cancelarCita');

-- 5. Verificar resultado final - todas las rutas del módulo Agenda
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    m.ruta AS modulo_ruta,
    CONCAT(m.ruta, md.ruta) AS ruta_completa,
    md.accion,
    md.estado,
    md.mostrar,
    CASE 
        WHEN md.estado = 'A' THEN '✅ ACTIVA'
        ELSE '❌ INACTIVA'
    END AS estado_final
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;

-- 6. Contar rutas por tipo
SELECT 
    md.accion,
    COUNT(*) as cantidad
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
  AND md.estado = 'A'
GROUP BY md.accion;
