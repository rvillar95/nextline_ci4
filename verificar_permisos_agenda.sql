-- =====================================================
-- VERIFICAR PERMISOS PARA AGENDA
-- =====================================================
-- Este script verifica que el perfil tenga los permisos
-- correctos para agendar citas
-- =====================================================

-- 1. Verificar que el módulo 33 (Agenda) existe y está activo
SELECT 
    'MÓDULO AGENDA' as tipo,
    id,
    nombre,
    ruta,
    mostrar,
    estado
FROM modulo
WHERE id = 33;

-- 2. Verificar que el perfil 9 tiene permisos en el módulo 33
SELECT 
    'PERMISOS PERFIL' as tipo,
    pm.id,
    pm.perfil_id,
    p.nombre as perfil_nombre,
    pm.modulo_id,
    m.nombre as modulo_nombre,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    pm.estado
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9
AND pm.modulo_id = 33;

-- 3. Verificar que existe el detalle /agendar con acción registrar
SELECT 
    'DETALLE AGENDAR' as tipo,
    md.id,
    md.modulo_id,
    m.nombre as modulo_nombre,
    md.descripcion,
    md.ruta,
    md.accion,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = 33
AND md.ruta = '/agendar';

-- 4. Verificar la ruta completa que debería estar permitida
SELECT 
    'RUTA ESPERADA' as tipo,
    CONCAT(m.ruta, md.ruta) as ruta_completa,
    md.accion,
    pm.registrar as permiso_registrar
FROM modulo m
JOIN modulo_detalle md ON md.modulo_id = m.id
LEFT JOIN perfil_modulo pm ON pm.modulo_id = m.id AND pm.perfil_id = 9
WHERE m.id = 33
AND md.ruta = '/agendar';

-- 5. Si el permiso registrar no está en 1, corregirlo:
-- UPDATE perfil_modulo 
-- SET registrar = 1 
-- WHERE perfil_id = 9 AND modulo_id = 33;
