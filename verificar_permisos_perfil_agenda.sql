-- =====================================================
-- VERIFICAR Y CORREGIR PERMISOS DEL PERFIL PARA AGENDA
-- =====================================================
-- Este script verifica que el perfil tenga permisos
-- de "editar" en el módulo 33 (Agenda) para poder
-- usar rutas con acción "editar" como /guardarMediciones
-- =====================================================

-- 1. Verificar qué perfiles tienen acceso al módulo 33
SELECT 
    pm.id,
    p.id AS perfil_id,
    p.nombre AS perfil_nombre,
    m.id AS modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    pm.estado
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id = 33
ORDER BY p.nombre;

-- 2. Verificar si algún perfil NO tiene permiso de editar
SELECT 
    p.id AS perfil_id,
    p.nombre AS perfil_nombre,
    pm.editar,
    CASE 
        WHEN pm.editar = 0 THEN '❌ NO tiene permiso de editar'
        WHEN pm.editar = 1 THEN '✅ Tiene permiso de editar'
        ELSE '⚠️ Estado desconocido'
    END AS estado_permiso
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
WHERE pm.modulo_id = 33
ORDER BY p.nombre;

-- 3. Si necesitas dar permiso de editar a todos los perfiles que tienen acceso a Agenda:
-- (Descomenta y ajusta el perfil_id según necesites)

-- UPDATE perfil_modulo
-- SET editar = 1,
--     factualizacion = NOW()
-- WHERE modulo_id = 33
--   AND editar = 0;

-- 4. Verificar que el permiso /guardarMediciones esté disponible para perfiles con editar
SELECT 
    p.id AS perfil_id,
    p.nombre AS perfil_nombre,
    m.nombre AS modulo_nombre,
    md.descripcion AS detalle_descripcion,
    md.ruta AS detalle_ruta,
    md.accion AS detalle_accion,
    pm.editar AS tiene_permiso_editar,
    CASE 
        WHEN pm.editar = 1 AND md.accion = 'editar' THEN '✅ Puede usar esta ruta'
        WHEN pm.editar = 0 AND md.accion = 'editar' THEN '❌ NO puede usar esta ruta (falta permiso editar)'
        ELSE '⚠️ Verificar'
    END AS estado_acceso
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id AND md.ruta = '/guardarMediciones'
WHERE pm.modulo_id = 33
  AND pm.estado = 'A'
ORDER BY p.nombre;
