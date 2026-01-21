-- =====================================================
-- VERIFICAR Y CORREGIR RUTAS DEL MÓDULO AGENDA
-- =====================================================
-- Este script verifica y corrige las rutas faltantes
-- del módulo Agenda: getDetalleCita y consulta
-- =====================================================

SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Agenda' LIMIT 1);

-- 1. Verificar todas las rutas del módulo Agenda
SELECT 
    md.id,
    md.descripcion,
    md.ruta AS detalle_ruta,
    m.ruta AS modulo_ruta,
    CASE 
        WHEN md.ruta = '/getDetalleCita' THEN CONCAT(m.ruta, md.ruta)
        WHEN md.ruta = '/consulta' THEN CONCAT(m.ruta, md.ruta)
        ELSE md.ruta
    END AS ruta_completa,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
  AND (md.ruta LIKE '%getDetalleCita%' OR md.ruta LIKE '%consulta%')
ORDER BY md.orden;

-- 2. Verificar si existe getDetalleCita
SELECT COUNT(*) as existe_getDetalleCita
FROM modulo_detalle
WHERE modulo_id = @modulo_id
  AND ruta = '/getDetalleCita';

-- 3. Verificar si existe consulta
SELECT COUNT(*) as existe_consulta
FROM modulo_detalle
WHERE modulo_id = @modulo_id
  AND ruta = '/consulta';

-- 4. Insertar getDetalleCita si no existe
SET @orden_getDetalleCita = (SELECT IFNULL(MAX(orden), 0) + 1 FROM modulo_detalle WHERE modulo_id = @modulo_id);

INSERT INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Ver Detalle Completo de Cita', '/getDetalleCita', 'ver', 'A', 'N', @orden_getDetalleCita, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Ver Detalle Completo de Cita',
    `ruta` = '/getDetalleCita',
    `accion` = 'ver',
    `estado` = 'A',
    `mostrar` = 'N',
    `factualizacion` = NOW();

-- 5. Insertar consulta si no existe
SET @orden_consulta = (SELECT IFNULL(MAX(orden), 0) + 1 FROM modulo_detalle WHERE modulo_id = @modulo_id);

INSERT INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Consulta de Paciente', '/consulta', 'ver', 'A', 'N', @orden_consulta, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Consulta de Paciente',
    `ruta` = '/consulta',
    `accion` = 'ver',
    `estado` = 'A',
    `mostrar` = 'N',
    `factualizacion` = NOW();

-- 6. Verificar resultado final
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    m.ruta AS modulo_ruta,
    CONCAT(m.ruta, md.ruta) AS ruta_completa,
    md.estado,
    md.mostrar,
    '✅ CORRECTA' AS estado_final
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
  AND (md.ruta = '/getDetalleCita' OR md.ruta = '/consulta')
ORDER BY md.orden;
