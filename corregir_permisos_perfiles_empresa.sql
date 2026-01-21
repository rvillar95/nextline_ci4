-- =====================================================
-- CORREGIR PERMISOS DEL MÓDULO "PERFILES DE EMPRESA"
-- =====================================================
-- Este script verifica y asigna los permisos necesarios
-- =====================================================

-- 1. Verificar el ID del módulo "Perfiles de Empresa"
SET @modulo_id = (SELECT id FROM modulo WHERE nombre = 'Perfiles de Empresa' LIMIT 1);

-- Si el módulo no existe, mostrar mensaje
SELECT IF(@modulo_id IS NULL, 
    'ERROR: El módulo "Perfiles de Empresa" no existe. Ejecuta primero crear_modulo_perfiles_empresa.sql',
    CONCAT('Módulo encontrado con ID: ', @modulo_id)) AS resultado;

-- 2. Verificar si está en el paquete Nutrición (id=4)
SELECT 
    IF(COUNT(*) > 0, 
        CONCAT('✅ Módulo está en el paquete Nutrición (ID: ', @modulo_id, ')'),
        CONCAT('❌ Módulo NO está en el paquete. Ejecuta: INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido, fcreacion) VALUES (4, ', @modulo_id, ', ''S'', NOW());')
    ) AS estado_paquete
FROM paquete_modulo
WHERE paquete_id = 4 AND modulo_id = @modulo_id AND incluido = 'S';

-- 3. Verificar si está en el perfil Nutricionista (id=9)
SELECT 
    IF(COUNT(*) > 0, 
        CONCAT('✅ Módulo está en el perfil Nutricionista (ID: ', @modulo_id, ')'),
        CONCAT('❌ Módulo NO está en el perfil. Se agregará automáticamente.')
    ) AS estado_perfil
FROM perfil_modulo
WHERE perfil_id = 9 AND modulo_id = @modulo_id;

-- 4. Obtener el siguiente orden disponible para el perfil Nutricionista
SET @siguiente_orden = (SELECT IFNULL(MAX(orden), 0) + 1 FROM perfil_modulo WHERE perfil_id = 9);

-- 5. INSERTAR o ACTUALIZAR permisos en perfil_modulo
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

-- 6. Verificar que todo esté correcto
SELECT 
    'Verificación Final' AS titulo,
    m.id AS modulo_id,
    m.nombre AS modulo_nombre,
    pm.paquete_id,
    p.nombre AS paquete_nombre,
    pf.perfil_id,
    pr.nombre AS perfil_nombre,
    pf.ver,
    pf.registrar,
    pf.editar,
    pf.eliminar,
    pf.estado AS permiso_estado
FROM modulo m
LEFT JOIN paquete_modulo pm ON pm.modulo_id = m.id AND pm.paquete_id = 4 AND pm.incluido = 'S'
LEFT JOIN perfil_modulo pf ON pf.modulo_id = m.id AND pf.perfil_id = 9
LEFT JOIN paquete p ON p.id = pm.paquete_id
LEFT JOIN perfil pr ON pr.id = pf.perfil_id
WHERE m.nombre = 'Perfiles de Empresa';

-- 7. Mensaje final
SELECT 
    IF(COUNT(*) = 2, 
        '✅ TODO CORRECTO: El módulo está en el paquete Y en el perfil. Cierra sesión y vuelve a iniciar para ver los cambios.',
        '⚠️ REVISAR: Verifica los resultados de la consulta anterior'
    ) AS mensaje_final
FROM (
    SELECT 1 FROM paquete_modulo WHERE paquete_id = 4 AND modulo_id = @modulo_id AND incluido = 'S'
    UNION ALL
    SELECT 1 FROM perfil_modulo WHERE perfil_id = 9 AND modulo_id = @modulo_id AND estado = 'A'
) AS verificacion;
