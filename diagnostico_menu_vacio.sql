-- =====================================================
-- DIAGNÓSTICO: Menú Vacío - Verificar Configuración
-- =====================================================

-- 1. Verificar que el perfil existe
SELECT 'PERFIL' as tipo, id, nombre, poder, estado FROM perfil WHERE id = 9;

-- 2. Verificar módulos asignados al perfil
SELECT 
    'PERFIL_MODULO' as tipo,
    pm.id,
    pm.perfil_id,
    pm.modulo_id,
    m.nombre as modulo_nombre,
    m.mostrar as modulo_mostrar,
    m.estado as modulo_estado,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    pm.estado as permiso_estado,
    pm.orden
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9
ORDER BY pm.orden;

-- 3. Verificar que los módulos tienen mostrar = 'S'
SELECT 
    'MODULOS' as tipo,
    id,
    nombre,
    mostrar,
    estado,
    ruta
FROM modulo
WHERE id IN (2, 1, 33, 34, 35, 36, 37)
ORDER BY id;

-- 4. Verificar que el usuario tiene el perfil asignado
SELECT 
    'USUARIO' as tipo,
    u.id,
    u.nombre,
    u.apellido,
    u.correo,
    u.perfil_id,
    p.nombre as perfil_nombre,
    p.estado as perfil_estado
FROM usuario u
LEFT JOIN perfil p ON p.id = u.perfil_id
WHERE u.perfil_id = 9;

-- 5. Verificar detalles de módulos (submenús)
SELECT 
    'MODULO_DETALLE' as tipo,
    md.id,
    md.modulo_id,
    m.nombre as modulo_nombre,
    md.descripcion,
    md.ruta,
    md.accion,
    md.mostrar,
    md.estado,
    md.orden
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id IN (2, 1, 33, 34, 35, 36, 37)
AND md.estado = 'A'
ORDER BY md.modulo_id, md.orden;
