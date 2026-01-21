-- =====================================================
-- ACTUALIZAR RUTAS DEL MÓDULO "PERFILES DE EMPRESA"
-- =====================================================
-- Este script actualiza las rutas para que funcionen correctamente
-- como submódulos dentro de "Perfiles de Empresa"
-- =====================================================

-- Obtener el ID del módulo
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Las rutas deben ser relativas desde dashboard/perfil para los submódulos de perfil
-- Y rutas completas desde dashboard/ para perfil-detalle (que es independiente)

-- Actualizar rutas de Perfil (submódulos del módulo principal)
UPDATE `modulo_detalle` 
SET `ruta` = '/lista'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Lista de Perfiles';

UPDATE `modulo_detalle` 
SET `ruta` = '/registro'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Crear Perfil';

UPDATE `modulo_detalle` 
SET `ruta` = '/editar'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Editar Perfil';

-- Actualizar rutas de Perfil Detalle (rutas completas desde dashboard/)
-- Estas rutas NO se concatenan con la ruta del módulo padre
UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/lista'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Permisos de Módulos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/registro'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Asignar Permisos';

UPDATE `modulo_detalle` 
SET `ruta` = '/dashboard/perfil-detalle/editar'
WHERE `modulo_id` = @modulo_id AND `descripcion` = 'Editar Permisos';

-- Verificar las rutas actualizadas
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    CONCAT(m.ruta, md.ruta) AS ruta_completa
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id = @modulo_id
ORDER BY md.orden;
