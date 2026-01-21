-- =====================================================
-- CORREGIR RUTAS DE PERFIL DETALLE
-- =====================================================
-- Actualizar todas las rutas de perfil-detalle para que empiecen con /dashboard/
-- =====================================================

SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Actualizar todas las rutas que empiezan con /perfil-detalle/ para que empiecen con /dashboard/perfil-detalle/
UPDATE `modulo_detalle` 
SET `ruta` = CONCAT('/dashboard', `ruta`)
WHERE `modulo_id` = @modulo_id 
  AND `ruta` LIKE '/perfil-detalle/%'
  AND `ruta` NOT LIKE '/dashboard/perfil-detalle/%';

-- Verificar que todas las rutas estén correctas
SELECT 
    md.id,
    md.descripcion,
    md.ruta AS ruta_actual,
    CASE 
        WHEN md.ruta LIKE '/dashboard/perfil-detalle/%' THEN '✅ CORRECTA'
        WHEN md.ruta LIKE '/perfil-detalle/%' THEN '❌ FALTA /dashboard/'
        WHEN md.ruta LIKE '/dashboard/%' THEN '⚠️ Ruta absoluta pero no perfil-detalle'
        ELSE '❌ Formato incorrecto'
    END AS estado
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
  AND (md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%' OR md.descripcion LIKE '%Orden%')
ORDER BY md.orden;

-- Mostrar todas las rutas del módulo para verificación completa
SELECT 
    md.orden,
    md.descripcion,
    md.ruta,
    CASE 
        WHEN md.ruta LIKE '/dashboard/perfil-detalle/%' THEN '✅ Absoluta (perfil-detalle)'
        WHEN md.ruta LIKE '/dashboard/%' THEN '✅ Absoluta'
        WHEN md.ruta LIKE '/%' THEN 'Relativa (se concatena)'
        ELSE 'Sin / inicial'
    END AS tipo
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;
