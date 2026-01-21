-- =====================================================
-- VERIFICAR PERMISOS DEL MÓDULO "PERFILES DE EMPRESA"
-- =====================================================

-- 1. Verificar que el módulo existe
SELECT id, nombre, sa, estado, mostrar 
FROM modulo 
WHERE nombre = 'Perfiles de Empresa';

-- 2. Verificar que está en el paquete Nutrición (id=4)
SELECT pm.*, m.nombre AS modulo_nombre, p.nombre AS paquete_nombre
FROM paquete_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
JOIN paquete p ON p.id = pm.paquete_id
WHERE m.nombre = 'Perfiles de Empresa' AND p.id = 4;

-- 3. Verificar permisos del perfil Nutricionista (id=9)
SELECT pm.*, p.nombre AS perfil_nombre, m.nombre AS modulo_nombre
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE m.nombre = 'Perfiles de Empresa' AND p.id = 9;

-- 4. Ver todos los módulos del perfil Nutricionista
SELECT pm.*, m.nombre AS modulo_nombre, m.ruta, pm.ver, pm.registrar, pm.editar, pm.eliminar
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9 AND pm.estado = 'A'
ORDER BY pm.orden ASC;

-- 5. Ver módulos del paquete Nutrición que NO están en el perfil Nutricionista
SELECT m.id, m.nombre, m.ruta
FROM modulo m
INNER JOIN paquete_modulo pm ON pm.modulo_id = m.id AND pm.paquete_id = 4 AND pm.incluido = 'S'
LEFT JOIN perfil_modulo pf ON pf.modulo_id = m.id AND pf.perfil_id = 9
WHERE pf.id IS NULL
ORDER BY m.nombre;
