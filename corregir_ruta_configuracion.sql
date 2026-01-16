-- =====================================================
-- CORREGIR RUTA DEL MÓDULO DE CONFIGURACIONES
-- =====================================================
-- Este script corrige la ruta del módulo de configuraciones
-- de /configuracion a /dashboard/configuracion
-- =====================================================

-- Actualizar la ruta del módulo
UPDATE `modulo`
SET `ruta` = '/dashboard/configuracion',
    `factualizacion` = NOW()
WHERE `id` = 34 AND `nombre` = 'Configuraciones';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se actualizó correctamente
SELECT 
    id,
    nombre,
    ruta,
    estado
FROM modulo 
WHERE id = 34;
