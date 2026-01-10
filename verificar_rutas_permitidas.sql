-- =====================================================
-- VERIFICAR RUTAS PERMITIDAS PARA PERFIL 9
-- =====================================================
-- Este script replica la lógica de getAllowedByPerfil
-- para ver qué rutas deberían estar permitidas
-- =====================================================

SELECT
    'RUTAS PERMITIDAS' as tipo,
    m.ruta            AS modulo_ruta,
    md.ruta           AS detalle_ruta,
    CONCAT(m.ruta, COALESCE(md.ruta, '')) AS ruta_completa,
    md.accion         AS acciones_csv,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    m.estado as modulo_estado,
    md.estado as detalle_estado,
    pm.estado as permiso_estado
FROM perfil_modulo pm
JOIN modulo m
  ON m.id = pm.modulo_id
 AND m.estado = 'A'
LEFT JOIN modulo_detalle md
  ON md.modulo_id = m.id
 AND md.estado = 'A'
WHERE pm.perfil_id = 9
  AND pm.estado = 'A'
  AND (
    -- Filtrar solo rutas de agenda para ver mejor
    m.id = 33
    OR md.ruta LIKE '%agendar%'
  )
ORDER BY pm.orden ASC, md.orden ASC;

-- Verificar específicamente la ruta /dashboard/agenda/agendar
SELECT
    'VERIFICACIÓN ESPECÍFICA' as tipo,
    CASE 
        WHEN m.ruta = '/dashboard/agenda' AND md.ruta = '/agendar' 
        THEN '✓ RUTA ENCONTRADA'
        ELSE '✗ RUTA NO ENCONTRADA'
    END as resultado,
    m.ruta as modulo_ruta_esperada,
    md.ruta as detalle_ruta_esperada,
    CONCAT(m.ruta, md.ruta) as ruta_completa,
    md.accion,
    pm.registrar as tiene_permiso_registrar
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id AND m.estado = 'A'
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id AND md.estado = 'A'
WHERE pm.perfil_id = 9
  AND pm.estado = 'A'
  AND m.id = 33
  AND md.ruta = '/agendar';
