-- =====================================================
-- CORREGIR RUTAS DE PERFIL DETALLE EN MÓDULO "PERFILES DE EMPRESA"
-- =====================================================
-- Este script verifica y corrige las rutas para que funcionen correctamente
-- =====================================================

-- Obtener el ID del módulo
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Verificar las rutas actuales
SELECT 
    md.id,
    md.descripcion,
    md.ruta AS ruta_actual,
    CASE 
        WHEN md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%' THEN '/dashboard/perfil-detalle/lista'
        WHEN md.descripcion = 'Asignar Permisos' THEN '/dashboard/perfil-detalle/registro'
        WHEN md.descripcion = 'Editar Permisos' THEN '/dashboard/perfil-detalle/editar'
        ELSE md.ruta
    END AS ruta_correcta
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
  AND (md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%')
ORDER BY md.orden;

-- Corregir rutas de Perfil Detalle (deben empezar con /dashboard/)
UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/lista'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Permisos de Módulos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/registro'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Asignar Permisos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/editar'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Editar Permisos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/getPerfilDetalle'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Obtener Permisos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/registrar'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Registrar Permiso';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/update'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Actualizar Permiso';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/eliminar'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Eliminar Permiso';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/updateOrden'
WHERE `modulo_id` = @modulo_id 
  AND `descripcion` = 'Actualizar Orden';

-- Verificar que las rutas estén correctas después de la actualización
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    CASE 
        WHEN md.ruta LIKE '/dashboard/%' THEN '✅ Ruta absoluta correcta'
        WHEN md.ruta LIKE '/%' THEN '⚠️ Ruta relativa (puede funcionar)'
        ELSE '❌ Ruta incorrecta'
    END AS estado
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;
