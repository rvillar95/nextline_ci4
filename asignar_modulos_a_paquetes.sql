-- =====================================================
-- ASIGNAR NUEVOS MÓDULOS A PAQUETES
-- =====================================================
-- Este script asigna los nuevos módulos (34: Configuraciones, 38: Cancelar Horas)
-- a los paquetes correspondientes
-- =====================================================

-- Verificar qué módulos tenemos
SELECT id, nombre, ruta, estado 
FROM modulo 
WHERE id IN (34, 38)
ORDER BY id;

-- Verificar qué paquetes existen
SELECT id, nombre, slug, activo 
FROM paquetes 
WHERE activo = 'A'
ORDER BY id;

-- =====================================================
-- ASIGNAR MÓDULOS A PAQUETES
-- =====================================================
-- Opción 1: Asignar solo a NextLine Custom (paquete_id = 3)
-- Esto es para módulos especializados de nutricionistas

-- Módulo 34: Configuraciones
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(3, 34, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 38: Cancelar Horas
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(3, 38, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- =====================================================
-- OPCIÓN 2: Asignar a todos los paquetes (si son módulos generales)
-- =====================================================
-- Si quieres que estos módulos estén disponibles en todos los paquetes,
-- descomenta las siguientes líneas:

-- INSERT INTO `paquete_modulo`
-- (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
-- SELECT p.id, 34, 'S', NOW()
-- FROM paquetes p
-- WHERE p.activo = 'A'
--   AND NOT EXISTS (
--       SELECT 1 FROM paquete_modulo pm 
--       WHERE pm.paquete_id = p.id AND pm.modulo_id = 34
--   );

-- INSERT INTO `paquete_modulo`
-- (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
-- SELECT p.id, 38, 'S', NOW()
-- FROM paquetes p
-- WHERE p.activo = 'A'
--   AND NOT EXISTS (
--       SELECT 1 FROM paquete_modulo pm 
--       WHERE pm.paquete_id = p.id AND pm.modulo_id = 38
--   );

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se asignaron correctamente
SELECT 
    pm.id,
    p.id AS paquete_id,
    p.nombre AS paquete_nombre,
    m.id AS modulo_id,
    m.nombre AS modulo_nombre,
    pm.incluido,
    pm.fcreacion
FROM paquete_modulo pm
JOIN paquetes p ON p.id = pm.paquete_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.modulo_id IN (34, 38)
ORDER BY pm.paquete_id, pm.modulo_id;

-- Verificar en la vista
SELECT * 
FROM vista_modulos_por_paquete
WHERE modulo_id IN (34, 38)
ORDER BY paquete_id, modulo_id;
