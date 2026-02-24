-- =====================================================
-- ACTUALIZAR RUTAS DE PLAN ALIMENTARIO A FORMATO ABSOLUTO
-- =====================================================
-- Este script corrige las rutas en modulo_detalle para que sean absolutas
-- y el SessionFilter pueda reconocerlas correctamente
-- =====================================================

-- Obtener ID del módulo
SET @modulo_plan_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1);

-- Si el módulo no existe, no hacer nada
SELECT IF(@modulo_plan_id IS NULL, 'ERROR: Módulo Plan Alimentario no encontrado. Ejecuta primero crear_sistema_plan_alimentario.sql', 'OK') as resultado;

-- Actualizar rutas relativas a absolutas
UPDATE `modulo_detalle` 
SET `ruta` = CONCAT('/dashboard/plan-alimentario', `ruta`),
    `factualizacion` = NOW()
WHERE `modulo_id` = @modulo_plan_id
  AND `ruta` NOT LIKE '/dashboard/%'
  AND `ruta` LIKE '/%';

-- Agregar rutas que faltan (actividades e intercambios) si no existen
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_plan_id, 'Obtener Actividades', '/dashboard/plan-alimentario/actividades', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM `modulo_detalle` 
    WHERE `modulo_id` = @modulo_plan_id 
    AND `ruta` = '/dashboard/plan-alimentario/actividades'
);

INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_plan_id, 'Obtener Intercambios', '/dashboard/plan-alimentario/intercambios', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM `modulo_detalle` 
    WHERE `modulo_id` = @modulo_plan_id 
    AND `ruta` = '/dashboard/plan-alimentario/intercambios'
);

-- Verificar resultado
SELECT 
    `id`, 
    `descripcion`, 
    `ruta`, 
    `accion`, 
    `estado`,
    CASE 
        WHEN `ruta` LIKE '/dashboard/%' THEN '✓ Absoluta'
        ELSE '✗ Relativa (necesita corrección)'
    END as estado_ruta
FROM `modulo_detalle` 
WHERE `modulo_id` = @modulo_plan_id
ORDER BY `orden`;

-- =====================================================
-- FIN DE ACTUALIZACIÓN
-- =====================================================
