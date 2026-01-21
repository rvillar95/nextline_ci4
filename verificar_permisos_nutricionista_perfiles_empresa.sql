-- =====================================================
-- VERIFICAR PERMISOS DEL PERFIL NUTRICIONISTA
-- =====================================================

-- 1. Verificar que el perfil Nutricionista (id=9) tenga permisos para el módulo "Perfiles de Empresa" (id=41)
SELECT 
    pm.id,
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar,
    pm.estado,
    pm.orden
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9 
  AND m.nombre = 'Perfiles de Empresa';

-- 2. Si no tiene permisos, insertarlos
-- Obtener el siguiente orden
SET @siguiente_orden = (SELECT IFNULL(MAX(orden), 0) + 1 FROM perfil_modulo WHERE perfil_id = 9);
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Insertar permisos si no existen
INSERT INTO `perfil_modulo` 
    (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES
    (9, @modulo_id, 1, 1, 1, 1, 'A', @siguiente_orden, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE 
    ver = 1, 
    registrar = 1, 
    editar = 1, 
    eliminar = 1, 
    estado = 'A',
    factualizacion = NOW();

-- 3. Verificar todas las rutas del módulo "Perfiles de Empresa" que el perfil puede acceder
SELECT 
    md.id,
    md.descripcion,
    md.ruta AS detalle_ruta,
    m.ruta AS modulo_ruta,
    CASE 
        WHEN md.ruta LIKE '/dashboard/%' THEN md.ruta
        ELSE CONCAT(m.ruta, md.ruta)
    END AS ruta_completa,
    md.accion,
    md.estado,
    md.mostrar
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE m.nombre = 'Perfiles de Empresa'
  AND md.estado = 'A'
ORDER BY md.orden;

-- 4. Verificar que el módulo esté en el paquete Nutrición (id=4)
SELECT 
    pm.paquete_id,
    p.nombre AS paquete_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.incluido
FROM paquete_modulo pm
JOIN paquete p ON p.id = pm.paquete_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE m.nombre = 'Perfiles de Empresa' AND p.id = 4;
