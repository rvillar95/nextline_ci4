-- =====================================================
-- ASIGNAR PAQUETE "NEXTLINE NUTRICIÓN" A EMPRESAS
-- =====================================================
-- Este script asigna el paquete "NextLine Nutrición" (id=4)
-- a las empresas de nutricionistas
-- =====================================================

-- Verificar empresas existentes
SELECT 
    id,
    nombre,
    paquete_id,
    estado
FROM empresa
WHERE estado = 'A'
ORDER BY id;

-- Verificar qué empresas tienen usuarios nutricionistas
SELECT DISTINCT
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    e.paquete_id,
    COUNT(DISTINCT u.id) AS cantidad_usuarios,
    GROUP_CONCAT(DISTINCT p.nombre) AS perfiles
FROM empresa e
LEFT JOIN usuario u ON u.empresa_id = e.id
LEFT JOIN perfil p ON p.id = u.perfil_id
WHERE e.estado = 'A'
  AND (p.id = 9 OR p.nombre LIKE '%nutricion%' OR e.nombre LIKE '%nutricion%')
GROUP BY e.id, e.nombre, e.paquete_id
ORDER BY e.id;

-- =====================================================
-- ASIGNAR PAQUETE A EMPRESAS
-- =====================================================
-- Opción 1: Asignar a empresas específicas por ID
-- Descomentar y ajustar los IDs según corresponda:

-- UPDATE empresa 
-- SET paquete_id = 4,
--     factualizacion = NOW()
-- WHERE id IN (1, 2, 3)  -- Ajustar IDs según corresponda
--   AND estado = 'A';

-- Opción 2: Asignar a empresas que tienen usuarios con perfil de Nutricionista (perfil_id = 9)
-- Descomentar si quieres asignar automáticamente:

-- UPDATE empresa e
-- SET e.paquete_id = 4,
--     e.factualizacion = NOW()
-- WHERE e.estado = 'A'
--   AND EXISTS (
--       SELECT 1 
--       FROM usuario u 
--       WHERE u.empresa_id = e.id 
--         AND u.perfil_id = 9
--         AND u.estado = 'A'
--   );

-- Opción 3: Asignar a todas las empresas que no tienen paquete asignado
-- Descomentar si quieres asignar a empresas sin paquete:

-- UPDATE empresa 
-- SET paquete_id = 4,
--     factualizacion = NOW()
-- WHERE estado = 'A'
--   AND (paquete_id IS NULL OR paquete_id = 0);

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar empresas con el paquete asignado
SELECT 
    e.id,
    e.nombre AS empresa_nombre,
    e.paquete_id,
    p.nombre AS paquete_nombre,
    p.slug AS paquete_slug,
    COUNT(DISTINCT u.id) AS cantidad_usuarios
FROM empresa e
LEFT JOIN paquetes p ON p.id = e.paquete_id
LEFT JOIN usuario u ON u.empresa_id = e.id AND u.estado = 'A'
WHERE e.paquete_id = 4
  AND e.estado = 'A'
GROUP BY e.id, e.nombre, e.paquete_id, p.nombre, p.slug
ORDER BY e.id;

-- Verificar usuarios de empresas con el paquete
SELECT 
    u.id AS usuario_id,
    u.nombre,
    u.apellido,
    u.correo,
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    p.id AS perfil_id,
    p.nombre AS perfil_nombre,
    paq.id AS paquete_id,
    paq.nombre AS paquete_nombre
FROM usuario u
JOIN empresa e ON e.id = u.empresa_id
LEFT JOIN perfil p ON p.id = u.perfil_id
LEFT JOIN paquetes paq ON paq.id = e.paquete_id
WHERE e.paquete_id = 4
  AND u.estado = 'A'
  AND e.estado = 'A'
ORDER BY e.id, u.id;

-- =====================================================
-- NOTAS
-- =====================================================
-- 1. Ejecutar primero las consultas de verificación para ver qué empresas existen
-- 2. Decidir qué empresas deben tener el paquete "NextLine Nutrición"
-- 3. Descomentar y ajustar la opción correspondiente (1, 2 o 3)
-- 4. Ejecutar el UPDATE correspondiente
-- 5. Verificar con las consultas de verificación final
-- 
-- IMPORTANTE: No ejecutar los UPDATE sin antes verificar qué empresas existen
-- y decidir cuáles deben tener el paquete asignado.
-- =====================================================
