-- =====================================================
-- CORREGIR PERMISOS PARA AGENDAR CITAS
-- =====================================================
-- Este script asegura que el perfil tenga todos los
-- permisos necesarios para agendar citas
-- =====================================================

-- 1. Asegurar que el módulo 33 está activo y visible
UPDATE modulo 
SET estado = 'A', mostrar = 'S'
WHERE id = 33;

-- 2. Asegurar que el perfil 9 tiene permiso REGISTRAR en módulo 33
UPDATE perfil_modulo 
SET registrar = 1, ver = 1, editar = 1, eliminar = 1, estado = 'A'
WHERE perfil_id = 9 AND modulo_id = 33;

-- Si no existe el registro, crearlo
INSERT INTO perfil_modulo (perfil_id, modulo_id, ver, registrar, editar, eliminar, analizar, estado, orden, fcreacion, factualizacion, feliminacion)
SELECT 9, 33, 1, 1, 1, 1, 0, 'A', 3, NOW(), NOW(), '0000-00-00 00:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM perfil_modulo 
    WHERE perfil_id = 9 AND modulo_id = 33
);

-- 3. Asegurar que el detalle /agendar existe y está activo
UPDATE modulo_detalle
SET estado = 'A', accion = 'registrar'
WHERE modulo_id = 33 AND ruta = '/agendar';

-- 4. Verificar resultado
SELECT 
    'VERIFICACIÓN FINAL' as tipo,
    pm.perfil_id,
    p.nombre as perfil,
    pm.modulo_id,
    m.nombre as modulo,
    m.ruta as modulo_ruta,
    md.ruta as detalle_ruta,
    CONCAT(m.ruta, md.ruta) as ruta_completa,
    md.accion,
    pm.registrar as tiene_permiso_registrar,
    pm.estado as permiso_activo
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id AND md.ruta = '/agendar'
WHERE pm.perfil_id = 9
AND pm.modulo_id = 33;
