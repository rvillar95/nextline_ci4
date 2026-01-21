-- =====================================================
-- VERIFICAR RUTAS COMPLETAS DEL MÓDULO "PERFILES DE EMPRESA"
-- =====================================================
-- Este script muestra cómo se construirán las rutas finales
-- para que el SessionFilter las reconozca correctamente
-- =====================================================

SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Verificar cómo se construirán las rutas finales
SELECT 
    md.id,
    md.descripcion,
    m.ruta AS modulo_ruta,
    md.ruta AS detalle_ruta,
    CASE 
        -- Si detalle_ruta empieza con /dashboard/, es ruta absoluta (usar directamente)
        WHEN md.ruta LIKE '/dashboard/%' THEN md.ruta
        -- Si detalle_ruta empieza con dashboard/, también es absoluta (agregar /)
        WHEN md.ruta LIKE 'dashboard/%' THEN CONCAT('/', md.ruta)
        -- Si detalle_ruta empieza con /, es relativa (concatenar con modulo_ruta)
        WHEN md.ruta LIKE '/%' THEN CONCAT(m.ruta, md.ruta)
        -- Si no tiene /, agregar / y concatenar
        ELSE CONCAT(m.ruta, '/', md.ruta)
    END AS ruta_final_esperada,
    md.accion,
    md.estado,
    md.mostrar,
    CASE 
        WHEN md.ruta LIKE '/dashboard/perfil-detalle/%' THEN '✅ Ruta absoluta correcta'
        WHEN md.ruta LIKE '/dashboard/%' THEN '⚠️ Ruta absoluta pero no perfil-detalle'
        WHEN md.ruta LIKE '/perfil-detalle/%' THEN '❌ FALTA /dashboard/ al inicio'
        WHEN md.ruta LIKE '/%' THEN 'Relativa (se concatena)'
        ELSE '❌ Formato incorrecto'
    END AS estado_ruta
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;

-- Verificar que las rutas de perfil-detalle estén correctas
SELECT 
    md.descripcion,
    md.ruta,
    CASE 
        WHEN md.ruta LIKE '/dashboard/perfil-detalle/%' THEN '✅ CORRECTA'
        ELSE '❌ INCORRECTA - Debe empezar con /dashboard/perfil-detalle/'
    END AS estado
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
  AND (md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%' OR md.descripcion LIKE '%Orden%')
ORDER BY md.orden;
