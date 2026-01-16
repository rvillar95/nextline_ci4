-- =====================================================
-- VERIFICAR Y CORREGIR PERMISOS DEL MÓDULO CONFIGURACIÓN
-- =====================================================
-- Este script verifica que el módulo de Configuraciones
-- tenga la ruta correcta y que los perfiles tengan permisos
-- =====================================================

-- 1. Verificar que el módulo existe y tiene la ruta correcta
SELECT 
    id,
    nombre,
    ruta,
    estado,
    mostrar
FROM modulo 
WHERE id = 34 OR nombre = 'Configuraciones';

-- 2. Verificar los detalles del módulo (rutas)
SELECT 
    id,
    modulo_id,
    descripcion,
    ruta,
    accion,
    estado,
    mostrar,
    orden
FROM modulo_detalle
WHERE modulo_id = 34
ORDER BY orden;

-- 3. Verificar permisos del perfil de Nutricionista (perfil_id = 9)
SELECT 
    pm.id,
    p.id AS perfil_id,
    p.nombre AS perfil_nombre,
    m.id AS modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    pm.estado
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id = 34
ORDER BY p.nombre;

-- 4. Si el módulo no tiene la ruta correcta, actualizarla
UPDATE modulo
SET ruta = '/dashboard/configuracion',
    factualizacion = NOW()
WHERE id = 34 AND ruta != '/dashboard/configuracion';

-- 5. Si el perfil de Nutricionista no tiene permiso de editar, dárselo
UPDATE perfil_modulo
SET editar = 1,
    factualizacion = NOW()
WHERE modulo_id = 34 
  AND perfil_id = 9
  AND editar = 0;

-- 6. Verificar que el detalle tenga la ruta /guardar con acción editar
UPDATE modulo_detalle
SET ruta = '/guardar',
    accion = 'editar',
    factualizacion = NOW()
WHERE modulo_id = 34 
  AND id = 237
  AND (ruta != '/guardar' OR accion != 'editar');

-- =====================================================
-- VERIFICACIÓN FINAL
-- =====================================================
-- Verificar que todo esté correcto
SELECT 
    'Módulo' AS tipo,
    m.id,
    m.nombre,
    m.ruta,
    NULL AS detalle_ruta,
    NULL AS accion,
    NULL AS perfil_id,
    NULL AS editar
FROM modulo m
WHERE m.id = 34

UNION ALL

SELECT 
    'Detalle' AS tipo,
    md.modulo_id AS id,
    NULL AS nombre,
    NULL AS ruta,
    md.ruta AS detalle_ruta,
    md.accion,
    NULL AS perfil_id,
    NULL AS editar
FROM modulo_detalle md
WHERE md.modulo_id = 34

UNION ALL

SELECT 
    'Permiso' AS tipo,
    pm.modulo_id AS id,
    p.nombre,
    NULL AS ruta,
    NULL AS detalle_ruta,
    NULL AS accion,
    pm.perfil_id,
    pm.editar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
WHERE pm.modulo_id = 34 AND pm.perfil_id = 9;
