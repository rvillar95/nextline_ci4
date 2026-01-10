-- =====================================================
-- CORRECCIÓN RÁPIDA: Menú Vacío
-- =====================================================
-- Este script corrige los problemas más comunes
-- =====================================================

-- 1. Asegurar que los módulos tienen mostrar = 'S' y están activos
UPDATE modulo 
SET mostrar = 'S', estado = 'A'
WHERE id IN (2, 1, 33, 34, 35, 36, 37);

-- 2. Asegurar que los permisos del perfil están activos
UPDATE perfil_modulo 
SET estado = 'A'
WHERE perfil_id = 9;

-- 3. Asegurar que los detalles principales de cada módulo tienen mostrar = 'S'
-- Módulo 2 (Inicio) - puede no tener detalles, está bien
-- Módulo 1 (Usuario) - asegurar que el detalle principal se muestre
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 1 
AND (ruta = '/lista' OR orden = 1)
AND estado = 'A';

-- Módulo 33 (Agenda) - asegurar que lista y calendario se muestren
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 33 
AND (ruta IN ('/lista', '/calendario') OR orden IN (1, 2))
AND estado = 'A';

-- Módulo 34 (Pacientes) - asegurar que lista se muestre
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 34 
AND (ruta = '/lista' OR orden = 1)
AND estado = 'A';

-- Módulo 35 (Documentos) - asegurar que lista se muestre
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 35 
AND (ruta = '/lista' OR orden = 1)
AND estado = 'A';

-- Módulo 36 (Historial) - asegurar que lista se muestre
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 36 
AND (ruta = '/lista' OR orden = 1)
AND estado = 'A';

-- Módulo 37 (Pagos) - asegurar que lista se muestre
UPDATE modulo_detalle
SET mostrar = 'S', estado = 'A'
WHERE modulo_id = 37 
AND (ruta = '/lista' OR orden = 1)
AND estado = 'A';

-- 4. Verificar resultado
SELECT 
    'RESUMEN' as tipo,
    m.id as modulo_id,
    m.nombre as modulo,
    m.mostrar,
    m.estado,
    COUNT(pm.id) as permisos_asignados,
    COUNT(md.id) as detalles_totales,
    SUM(CASE WHEN md.mostrar = 'S' THEN 1 ELSE 0 END) as detalles_visibles
FROM modulo m
LEFT JOIN perfil_modulo pm ON pm.modulo_id = m.id AND pm.perfil_id = 9 AND pm.estado = 'A'
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id AND md.estado = 'A'
WHERE m.id IN (2, 1, 33, 34, 35, 36, 37)
GROUP BY m.id, m.nombre, m.mostrar, m.estado
ORDER BY m.id;
