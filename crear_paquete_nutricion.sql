-- =====================================================
-- CREAR PAQUETE "NEXTLINE NUTRICIÓN"
-- =====================================================
-- Este script crea el paquete "NextLine Nutrición" (id=4)
-- y asigna todos los módulos de nutricionistas al paquete
-- =====================================================

-- Verificar qué módulos de nutricionistas existen
SELECT 
    id, 
    nombre, 
    ruta, 
    estado,
    mostrar
FROM modulo 
WHERE id IN (33, 34, 35, 36, 37, 38, 39)
ORDER BY id;

-- Verificar qué paquetes ya existen
SELECT 
    id, 
    nombre, 
    slug, 
    activo,
    orden
FROM paquetes 
WHERE activo = 'A'
ORDER BY id;

-- =====================================================
-- PASO 1: Crear Paquete "NextLine Nutrición"
-- =====================================================
INSERT INTO `paquetes`
(`id`, `nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
VALUES
(4, 'NextLine Nutrición', 'nutricion', 'Sistema completo para nutricionistas: agenda, pacientes, documentos, historial clínico, configuraciones y más', 0.00, 0.00, 'A', 4, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `slug` = VALUES(`slug`),
    `descripcion` = VALUES(`descripcion`),
    `activo` = 'A',
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- =====================================================
-- PASO 2: Asignar Módulos al Paquete
-- =====================================================
-- Módulos verificados y confirmados en la BD:
--   33: Agenda, 34: Pacientes, 35: Documentos, 36: Historial Clínico
--   37: Pagos, 38: Configuraciones, 39: Cancelar Horas

-- Módulo 33: Agenda
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 33, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 34: Pacientes
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 34, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 35: Documentos
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 35, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 36: Historial Clínico
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 36, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 37: Pagos
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 37, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 38: Configuraciones
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 38, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Módulo 39: Cancelar Horas
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(4, 39, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- =====================================================
-- NOTA: Módulos Verificados
-- =====================================================
-- Según la BD actual:
--   - Módulo 33: Agenda
--   - Módulo 34: Pacientes
--   - Módulo 35: Documentos
--   - Módulo 36: Historial Clínico
--   - Módulo 37: Pagos
--   - Módulo 38: Configuraciones
--   - Módulo 39: Cancelar Horas
-- 
-- Todos los módulos están correctamente identificados y asignados arriba.

-- =====================================================
-- PASO 3: Verificación
-- =====================================================
-- Verificar que el paquete se creó correctamente
SELECT 
    id,
    nombre,
    slug,
    descripcion,
    activo,
    orden
FROM paquetes
WHERE id = 4;

-- Verificar módulos asignados al paquete
SELECT 
    pm.id,
    pm.paquete_id,
    p.nombre AS paquete_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    pm.incluido,
    pm.fcreacion
FROM paquete_modulo pm
JOIN paquetes p ON p.id = pm.paquete_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.paquete_id = 4
ORDER BY pm.modulo_id;

-- Verificar en la vista (si existe)
SELECT * 
FROM vista_modulos_por_paquete
WHERE paquete_id = 4
ORDER BY modulo_id;

-- =====================================================
-- RESUMEN
-- =====================================================
-- Paquete creado: NextLine Nutrición (id=4)
-- Módulos asignados:
--   - 33: Agenda
--   - 34: Pacientes
--   - 35: Documentos
--   - 36: Historial Clínico
--   - 37: Pagos
--   - 38: Configuraciones
--   - 39: Cancelar Horas
-- 
-- Próximos pasos:
-- 1. Verificar que todos los módulos estén correctamente asignados
-- 2. Asignar el paquete a empresas de nutricionistas (si aplica)
-- 3. Verificar que el menú se genere dinámicamente según el paquete
-- 4. Probar que el sistema de permisos funcione correctamente
-- =====================================================
