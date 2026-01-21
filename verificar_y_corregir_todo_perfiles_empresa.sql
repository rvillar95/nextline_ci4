-- =====================================================
-- VERIFICAR Y CORREGIR TODO EL MÓDULO "PERFILES DE EMPRESA"
-- =====================================================

-- 1. Verificar que el módulo existe
SELECT 
    id,
    nombre,
    ruta AS ruta_modulo,
    sa,
    estado,
    mostrar
FROM modulo
WHERE nombre = 'Perfiles de Empresa';

-- 2. Verificar todas las rutas del módulo
SELECT 
    md.id,
    md.descripcion,
    md.ruta AS ruta_actual,
    m.ruta AS ruta_modulo_padre,
    CASE 
        WHEN md.ruta LIKE '/dashboard/%' THEN 'Ruta absoluta'
        WHEN md.ruta LIKE '/%' THEN 'Ruta relativa (se concatena)'
        ELSE 'Ruta sin / inicial'
    END AS tipo_ruta,
    CASE 
        WHEN md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%' THEN 
            CASE 
                WHEN md.ruta NOT LIKE '/dashboard/perfil-detalle/%' THEN '❌ INCORRECTA - Debe empezar con /dashboard/perfil-detalle/'
                ELSE '✅ CORRECTA'
            END
        ELSE 'N/A'
    END AS estado_correccion
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE m.nombre = 'Perfiles de Empresa'
ORDER BY md.orden;

-- 3. Corregir rutas de Perfil Detalle (deben ser absolutas: /dashboard/perfil-detalle/...)
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/lista'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Permisos de Módulos' OR `descripcion` LIKE '%Permisos%' AND `descripcion` LIKE '%Lista%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/registro'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Asignar Permisos' OR `descripcion` LIKE '%Asignar%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/editar'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Editar Permisos' OR `descripcion` LIKE '%Editar Permisos%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/getPerfilDetalle'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Obtener Permisos' OR `descripcion` LIKE '%Obtener%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/registrar'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Registrar Permiso' OR `descripcion` LIKE '%Registrar Permiso%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/update'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Actualizar Permiso' OR `descripcion` LIKE '%Actualizar Permiso%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/eliminar'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Eliminar Permiso' OR `descripcion` LIKE '%Eliminar Permiso%');

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/updateOrden'
WHERE `modulo_id` = @modulo_id 
  AND (`descripcion` = 'Actualizar Orden' OR `descripcion` LIKE '%Actualizar Orden%');

-- 4. Verificar rutas después de la corrección
SELECT 
    md.descripcion,
    md.ruta,
    CASE 
        WHEN md.ruta LIKE '/dashboard/perfil-detalle/%' THEN '✅ CORRECTA'
        WHEN md.ruta LIKE '/dashboard/%' THEN '⚠️ Ruta absoluta pero no perfil-detalle'
        ELSE '❌ INCORRECTA'
    END AS estado
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_id
  AND (md.descripcion LIKE '%Permisos%' OR md.descripcion LIKE '%Permiso%')
ORDER BY md.orden;
