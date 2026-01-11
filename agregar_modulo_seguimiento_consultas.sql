-- =====================================================
-- AGREGAR MÓDULO DETALLE: SEGUIMIENTO DE CONSULTAS
-- =====================================================
-- Este script agrega los detalles de módulo necesarios
-- para el seguimiento de consultas en el módulo Agenda (33)
-- =====================================================

-- Detalles del módulo Agenda - Seguimiento de Consultas
INSERT INTO `modulo_detalle` 
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
(214, 33, 'Consulta en Curso', '/consulta', 'ver', 'A', 'S', 17, NOW(), NOW(), '0000-00-00 00:00:00'),
(215, 33, 'Iniciar Consulta', '/iniciarConsulta', 'editar', 'A', 'N', 18, NOW(), NOW(), '0000-00-00 00:00:00'),
(216, 33, 'Terminar Consulta', '/terminarConsulta', 'editar', 'A', 'N', 19, NOW(), NOW(), '0000-00-00 00:00:00'),
(217, 33, 'Guardar Notas de Consulta', '/guardarNotasConsulta', 'editar', 'A', 'N', 20, NOW(), NOW(), '0000-00-00 00:00:00'),
(218, 33, 'Estadísticas de Consultas', '/estadisticas', 'ver', 'A', 'S', 21, NOW(), NOW(), '0000-00-00 00:00:00'),
(219, 33, 'Obtener Consultas Próximas', '/getConsultasProximas', 'ver', 'A', 'N', 22, NOW(), NOW(), '0000-00-00 00:00:00'),
(220, 33, 'Obtener Estadísticas', '/getEstadisticas', 'ver', 'A', 'N', 23, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT id, modulo_id, descripcion, ruta, accion, estado, mostrar, orden
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta IN ('/consulta', '/iniciarConsulta', '/terminarConsulta', '/guardarNotasConsulta', '/estadisticas', '/getConsultasProximas', '/getEstadisticas')
ORDER BY orden;
