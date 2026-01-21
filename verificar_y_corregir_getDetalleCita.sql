-- =====================================================
-- VERIFICAR Y CORREGIR RUTA getDetalleCita
-- =====================================================
-- Este script verifica si la ruta getDetalleCita existe
-- en modulo_detalle y la corrige si es necesario
-- =====================================================

-- 1. Verificar si existe la ruta getDetalleCita en modulo_detalle
SELECT 
    md.id,
    md.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    md.descripcion,
    md.ruta AS detalle_ruta,
    CASE 
        WHEN md.ruta = '/getDetalleCita' THEN 'Relativa (correcta)'
        WHEN md.ruta = '/dashboard/agenda/getDetalleCita' THEN 'Absoluta (también funciona)'
        WHEN md.ruta LIKE '%getDetalleCita%' THEN 'Existe pero formato diferente'
        ELSE 'No existe'
    END AS estado
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE m.nombre = 'Agenda'
  AND (md.ruta LIKE '%getDetalleCita%' OR md.descripcion LIKE '%Detalle%' AND md.descripcion LIKE '%Cita%');

-- 2. Si no existe, insertarla
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Agenda' LIMIT 1);
SET @siguiente_orden = (SELECT IFNULL(MAX(orden), 0) + 1 FROM modulo_detalle WHERE modulo_id = @modulo_id);

INSERT INTO `modulo_detalle` 
    (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@modulo_id, 'Ver Detalle Completo de Cita', '/getDetalleCita', 'ver', 'A', 'N', @siguiente_orden, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = 'Ver Detalle Completo de Cita',
    `ruta` = '/getDetalleCita',
    `accion` = 'ver',
    `estado` = 'A',
    `mostrar` = 'N',
    `factualizacion` = NOW();

-- 3. Verificar que el perfil Nutricionista tenga permisos para esta ruta
SELECT 
    pm.id,
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE p.nombre = 'Nutricionista'
  AND m.nombre = 'Agenda';

-- 4. Si el perfil Nutricionista no tiene permisos, agregarlos
SET @perfil_id = (SELECT id FROM perfil WHERE nombre = 'Nutricionista' LIMIT 1);
SET @modulo_agenda_id = (SELECT id FROM modulo WHERE nombre = 'Agenda' LIMIT 1);

INSERT INTO `perfil_modulo` 
    (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (@perfil_id, @modulo_agenda_id, 1, 1, 1, 1, 'A', 1, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `registrar` = 1,
    `editar` = 1,
    `eliminar` = 1,
    `estado` = 'A',
    `factualizacion` = NOW();

-- 5. Verificar resultado final
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    m.ruta AS modulo_ruta,
    CASE 
        WHEN md.ruta = '/getDetalleCita' THEN CONCAT(m.ruta, md.ruta)
        ELSE md.ruta
    END AS ruta_completa,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE m.nombre = 'Agenda'
  AND md.ruta LIKE '%getDetalleCita%';
