-- =====================================================
-- VERIFICAR Y CORREGIR PERMISOS DEL MÓDULO PAQUETES
-- =====================================================

-- 1. Verificar que el módulo existe
SELECT 
    id,
    nombre,
    ruta,
    estado,
    mostrar,
    sa
FROM modulo 
WHERE id = 40;

-- 2. Verificar rutas del módulo
SELECT 
    md.id,
    md.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    md.descripcion,
    md.ruta AS detalle_ruta,
    CONCAT(m.ruta, md.ruta) AS ruta_completa,
    md.accion,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = 40
ORDER BY md.orden;

-- 3. Verificar permisos del perfil de Super Admin (poder = 3)
SELECT 
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    p.poder,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id = 40
  AND p.poder = 3;

-- 4. Si no hay permisos, crearlos
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `editar`, `eliminar`, `fcreacion`, `factualizacion`)
SELECT 
    p.id AS perfil_id,
    40 AS modulo_id,
    1 AS ver,
    1 AS editar,
    1 AS eliminar,
    NOW() AS fcreacion,
    NOW() AS factualizacion
FROM `perfil` p
WHERE p.poder = 3
  AND p.id NOT IN (SELECT perfil_id FROM perfil_modulo WHERE modulo_id = 40)
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `editar` = 1,
    `eliminar` = 1,
    `factualizacion` = NOW();

-- 5. Verificar que la ruta completa sea correcta
-- La ruta debería ser: /dashboard/paquete/getPaquetes
SELECT 
    CONCAT(m.ruta, md.ruta) AS ruta_completa_esperada,
    '/dashboard/paquete/getPaquetes' AS ruta_que_busca_datatables
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = 40
  AND md.ruta = '/getPaquetes';
