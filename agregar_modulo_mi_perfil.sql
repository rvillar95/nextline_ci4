-- =====================================================
-- MÓDULO MI PERFIL (foto del nutricionista + tema claro/oscuro)
-- =====================================================
-- 1. Agregar columnas a usuario (foto, tema)
-- 2. Insertar módulo y rutas
-- 3. Asignar a perfil Nutricionista (9) y paquete (3)
-- =====================================================

-- 1. Columnas en usuario (ejecutar solo si no existen; si falla por "Duplicate column", ignorar)
ALTER TABLE `usuario` ADD COLUMN `foto` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ruta relativa de la foto de perfil';
ALTER TABLE `usuario` ADD COLUMN `tema` VARCHAR(20) NOT NULL DEFAULT 'claro' COMMENT 'claro|oscuro';

-- 2. Módulo principal (id 39)
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(39, 'Mi perfil', 'Foto de perfil y preferencias de apariencia (tema claro/oscuro)', '/dashboard/mi-perfil', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `sa` = VALUES(`sa`),
    `factualizacion` = NOW();

-- 3. Rutas del módulo (modulo_detalle)
INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(241, 39, 'Ver Mi perfil', '', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(242, 39, 'Guardar Mi perfil', '/guardar', 'editar', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(243, 39, 'Subir foto', '/subir-foto', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- 4. Permisos perfil Nutricionista (perfil_id = 9)
INSERT INTO `perfil_modulo`
(`perfil_id`, `modulo_id`, `ver`, `editar`, `eliminar`, `fcreacion`, `factualizacion`)
VALUES
(9, 39, 1, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `ver` = 1,
    `editar` = 1,
    `eliminar` = 0,
    `factualizacion` = NOW();

-- 5. Incluir en paquete (paquete_id = 3, NextLine Custom)
INSERT INTO `paquete_modulo`
(`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
VALUES
(3, 39, 'S', NOW())
ON DUPLICATE KEY UPDATE
    `incluido` = 'S',
    `fcreacion` = NOW();

-- Opcional: asignar a todos los paquetes activos
-- INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
-- SELECT p.id, 39, 'S', NOW() FROM paquetes p WHERE p.activo = 'A'
-- ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT m.id, m.nombre, m.ruta, m.estado, COUNT(md.id) AS cantidad_rutas
FROM modulo m
LEFT JOIN modulo_detalle md ON md.modulo_id = m.id
WHERE m.id = 39
GROUP BY m.id;
