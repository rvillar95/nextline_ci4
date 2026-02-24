-- =====================================================
-- VERIFICAR RUTAS DE PLAN ALIMENTARIO
-- =====================================================
-- Este script verifica si las rutas están correctamente configuradas
-- =====================================================

-- 1. Verificar que el módulo existe
SELECT 
    id, 
    nombre, 
    ruta, 
    estado,
    CASE 
        WHEN ruta = '/dashboard/plan-alimentario' THEN '✓ Correcto'
        ELSE '✗ Incorrecto'
    END as estado_ruta
FROM `modulo` 
WHERE `ruta` = '/dashboard/plan-alimentario';

-- 2. Verificar rutas del módulo (deben ser absolutas)
SET @modulo_plan_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1);

SELECT 
    `id`, 
    `descripcion`, 
    `ruta`, 
    `accion`, 
    `estado`,
    CASE 
        WHEN `ruta` LIKE '/dashboard/plan-alimentario/%' THEN '✓ Absoluta'
        WHEN `ruta` LIKE '/dashboard/%' THEN '✓ Absoluta (otra ruta)'
        WHEN `ruta` LIKE '/%' THEN '✗ Relativa (necesita corrección)'
        ELSE '✗ Formato desconocido'
    END as estado_ruta
FROM `modulo_detalle` 
WHERE `modulo_id` = @modulo_plan_id
ORDER BY `orden`;

-- 3. Verificar permisos del perfil (ajusta el perfil_id según tu sistema)
-- Típicamente: 1 = Super Admin, 2 = Nutricionista
SELECT 
    pm.`perfil_id`,
    p.`nombre` as perfil_nombre,
    pm.`modulo_id`,
    m.`nombre` as modulo_nombre,
    pm.`incluido`,
    CASE 
        WHEN pm.`incluido` = 'S' THEN '✓ Incluido'
        ELSE '✗ No incluido'
    END as estado_permiso
FROM `perfil_modulo` pm
JOIN `perfil` p ON p.id = pm.perfil_id
JOIN `modulo` m ON m.id = pm.modulo_id
WHERE pm.`modulo_id` = @modulo_plan_id
ORDER BY pm.`perfil_id`;

-- 4. Verificar permisos específicos del perfil en modulo_detalle
-- (Esto requiere que exista una tabla de permisos detallados, ajusta según tu esquema)
SELECT 
    'Verificar manualmente los permisos en perfil_modulo_detalle si existe' as nota;

-- 5. Listar todas las rutas permitidas para el perfil Nutricionista (ID 2)
-- Esto muestra qué rutas tiene acceso el perfil
SELECT 
    md.`id`,
    md.`descripcion`,
    md.`ruta`,
    md.`accion`,
    md.`estado`,
    CASE 
        WHEN md.`ruta` LIKE '/dashboard/plan-alimentario/%' THEN '✓ Ruta de Plan Alimentario'
        ELSE 'Otra ruta'
    END as tipo
FROM `modulo_detalle` md
JOIN `modulo` m ON m.id = md.modulo_id
JOIN `perfil_modulo` pm ON pm.modulo_id = m.id
WHERE pm.`perfil_id` = 2  -- Ajusta según tu perfil de Nutricionista
  AND pm.`incluido` = 'S'
  AND md.`estado` = 'A'
  AND m.`ruta` = '/dashboard/plan-alimentario'
ORDER BY md.`orden`;

-- =====================================================
-- FIN DE VERIFICACIÓN
-- =====================================================
