-- ============================================================================
-- SISTEMA DE PAQUETES DE MÓDULOS - NextLine
-- Integrado con la estructura existente de la base de datos
-- ============================================================================

-- ============================================================================
-- 1. CREAR TABLA DE PAQUETES
-- ============================================================================

DROP TABLE IF EXISTS `paquetes`;
CREATE TABLE `paquetes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Ej: NextLine Presencia, NextLine Gestión',
  `slug` varchar(100) NOT NULL UNIQUE COMMENT 'Identificador único del paquete',
  `descripcion` text COMMENT 'Descripción del paquete',
  `precio_setup` decimal(10,2) DEFAULT 0 COMMENT 'Precio de implementación inicial',
  `precio_mensual` decimal(10,2) DEFAULT 0 COMMENT 'Precio mensual del plan',
  `activo` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `orden` int DEFAULT 0 COMMENT 'Orden de visualización',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_unique` (`slug`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================================
-- 2. INSERTAR PAQUETES INICIALES
-- ============================================================================

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `orden`) VALUES
('NextLine Presencia', 'presencia', 'Sitio web profesional para mostrar tu negocio: servicios, galería, proyectos, testimonios', 149000, 19990, 1),
('NextLine Gestión', 'gestion', 'Presencia + Cotizaciones y CRM para gestionar tu negocio', 269000, 49990, 2),
('NextLine Custom', 'custom', 'Solución a medida con desarrollos personalizados', 0, 0, 3);

-- ============================================================================
-- 3. CREAR TABLA DE RELACIÓN PAQUETE-MÓDULO
-- ============================================================================

DROP TABLE IF EXISTS `paquete_modulo`;
CREATE TABLE `paquete_modulo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paquete_id` int NOT NULL COMMENT 'ID del paquete',
  `modulo_id` int NOT NULL COMMENT 'ID del módulo existente en tabla modulo',
  `incluido` char(1) DEFAULT 'S' COMMENT 'S=Incluido, N=Bloqueado',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_modulo` (`paquete_id`, `modulo_id`),
  KEY `paquete_id` (`paquete_id`),
  KEY `modulo_id` (`modulo_id`),
  CONSTRAINT `fk_paquete_modulo_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_paquete_modulo_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================================
-- 4. ASIGNAR MÓDULOS A PAQUETES
-- ============================================================================

-- ============================================================================
-- 4.1 PAQUETE PRESENCIA (id=1)
-- ============================================================================

-- Módulos CORE (siempre visibles)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 1, 'S'),  -- Usuario
(1, 2, 'S'),  -- Inicio
(1, 3, 'S'),  -- Perfil
(1, 4, 'S'),  -- Modulo
(1, 6, 'S'),  -- Perfil Detalle
(1, 7, 'S'); -- Modulo Detalle

-- Módulos de PRESENCIA (incluidos en este paquete)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 8, 'S'),   -- Servicios
(1, 14, 'S'),  -- Galería
(1, 16, 'S'),  -- Categorías de Galería
(1, 17, 'S'),  -- Categorías de Servicios
(1, 18, 'S'),  -- Proyectos
(1, 19, 'S'),  -- Contactos (Leads)
(1, 26, 'S'),  -- Testimonios
(1, 30, 'S'),  -- Ubicación (regiones/comunas)
(1, 31, 'S'); -- Empresa

-- Módulos BLOQUEADOS en Presencia (no incluidos)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 28, 'N'),  -- Clientes (bloqueado)
(1, 29, 'N'); -- Cotizaciones (bloqueado)

-- ============================================================================
-- 4.2 PAQUETE GESTIÓN (id=2) - Incluye TODO
-- ============================================================================

-- Módulos CORE
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(2, 1, 'S'),  -- Usuario
(2, 2, 'S'),  -- Inicio
(2, 3, 'S'),  -- Perfil
(2, 4, 'S'),  -- Modulo
(2, 6, 'S'),  -- Perfil Detalle
(2, 7, 'S'); -- Modulo Detalle

-- Módulos de PRESENCIA
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(2, 8, 'S'),   -- Servicios
(2, 14, 'S'),  -- Galería
(2, 16, 'S'),  -- Categorías de Galería
(2, 17, 'S'),  -- Categorías de Servicios
(2, 18, 'S'),  -- Proyectos
(2, 19, 'S'),  -- Contactos (Leads)
(2, 26, 'S'),  -- Testimonios
(2, 30, 'S'),  -- Ubicación
(2, 31, 'S'); -- Empresa

-- Módulos de GESTIÓN (adicionales)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(2, 28, 'S'),  -- Clientes (incluido)
(2, 29, 'S'); -- Cotizaciones (incluido)

-- ============================================================================
-- 4.3 PAQUETE CUSTOM (id=3) - Todos los módulos disponibles
-- ============================================================================

INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) 
SELECT 3, id, 'S' FROM modulo WHERE estado = 'A';

-- ============================================================================
-- 5. AGREGAR CAMPO paquete_id A LA TABLA empresa
-- ============================================================================

-- Verificar si la columna ya existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'empresa';
SET @columnname = 'paquete_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1', -- La columna ya existe, no hacer nada
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' int DEFAULT 1 AFTER id')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar foreign key si no existe
SET @fk_name = 'fk_empresa_paquete';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (constraint_name = @fk_name)
  ) > 0,
  'SELECT 1', -- FK ya existe
  CONCAT('ALTER TABLE ', @tablename, ' ADD CONSTRAINT ', @fk_name, ' FOREIGN KEY (paquete_id) REFERENCES paquetes(id)')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Actualizar empresas existentes al paquete "Presencia" por defecto
UPDATE `empresa` SET `paquete_id` = 1 WHERE `paquete_id` IS NULL OR `paquete_id` = 0;

-- ============================================================================
-- 6. VISTAS ÚTILES PARA CONSULTAS
-- ============================================================================

-- Vista: Módulos por paquete
CREATE OR REPLACE VIEW `vista_paquete_modulos` AS
SELECT 
    p.id as paquete_id,
    p.nombre as paquete_nombre,
    p.slug as paquete_slug,
    m.id as modulo_id,
    m.nombre as modulo_nombre,
    m.ruta as modulo_ruta,
    m.descripcion as modulo_descripcion,
    pm.incluido,
    m.orden as modulo_orden
FROM paquetes p
INNER JOIN paquete_modulo pm ON pm.paquete_id = p.id
INNER JOIN modulo m ON m.id = pm.modulo_id
WHERE p.activo = 'A' 
  AND m.estado = 'A'
  AND pm.incluido = 'S'
ORDER BY p.orden, m.orden;

-- Vista: Paquete actual de la empresa
CREATE OR REPLACE VIEW `vista_empresa_paquete` AS
SELECT 
    e.id as empresa_id,
    e.nombre as empresa_nombre,
    p.id as paquete_id,
    p.nombre as paquete_nombre,
    p.slug as paquete_slug,
    p.precio_setup,
    p.precio_mensual,
    p.descripcion as paquete_descripcion
FROM empresa e
LEFT JOIN paquetes p ON p.id = e.paquete_id
WHERE e.estado = 'A';

-- ============================================================================
-- 7. DATOS DE PRUEBA Y VERIFICACIÓN
-- ============================================================================

-- Ver todos los paquetes disponibles
SELECT * FROM paquetes;

-- Ver módulos del paquete Presencia
SELECT 
    m.id,
    m.nombre,
    m.ruta,
    m.descripcion,
    pm.incluido,
    CASE 
        WHEN pm.incluido = 'S' THEN '✅ Incluido'
        ELSE '❌ Bloqueado'
    END as estado_modulo
FROM paquete_modulo pm
INNER JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.paquete_id = 1  -- Paquete Presencia
ORDER BY 
    CASE WHEN pm.incluido = 'S' THEN 0 ELSE 1 END,
    m.orden, m.id;

-- Ver módulos del paquete Gestión
SELECT 
    m.id,
    m.nombre,
    m.ruta,
    m.descripcion,
    pm.incluido,
    CASE 
        WHEN pm.incluido = 'S' THEN '✅ Incluido'
        ELSE '❌ Bloqueado'
    END as estado_modulo
FROM paquete_modulo pm
INNER JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.paquete_id = 2  -- Paquete Gestión
ORDER BY 
    CASE WHEN pm.incluido = 'S' THEN 0 ELSE 1 END,
    m.orden, m.id;

-- Ver configuración actual de la empresa
SELECT * FROM vista_empresa_paquete;

-- ============================================================================
-- 8. PROCEDIMIENTOS ALMACENADOS ÚTILES
-- ============================================================================

-- Procedimiento: Obtener módulos de un paquete
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_obtener_modulos_paquete` $$
CREATE PROCEDURE `sp_obtener_modulos_paquete`(IN p_paquete_id INT)
BEGIN
    SELECT 
        m.id,
        m.nombre,
        m.descripcion,
        m.ruta,
        m.estado,
        m.mostrar,
        m.orden,
        pm.incluido
    FROM paquete_modulo pm
    INNER JOIN modulo m ON m.id = pm.modulo_id
    WHERE pm.paquete_id = p_paquete_id
      AND pm.incluido = 'S'
      AND m.estado = 'A'
    ORDER BY m.orden, m.id;
END$$
DELIMITER ;

-- Procedimiento: Verificar si un módulo está disponible en el paquete de la empresa
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_modulo_disponible` $$
CREATE PROCEDURE `sp_modulo_disponible`(IN p_modulo_id INT)
BEGIN
    SELECT 
        CASE 
            WHEN pm.incluido = 'S' THEN 1
            ELSE 0
        END as disponible
    FROM empresa e
    INNER JOIN paquete_modulo pm ON pm.paquete_id = e.paquete_id
    WHERE pm.modulo_id = p_modulo_id
      AND e.estado = 'A'
    LIMIT 1;
END$$
DELIMITER ;

-- ============================================================================
-- 9. FUNCIONES ÚTILES
-- ============================================================================

-- Función: Verificar si módulo está incluido en paquete
DELIMITER $$
DROP FUNCTION IF EXISTS `fn_modulo_en_paquete` $$
CREATE FUNCTION `fn_modulo_en_paquete`(p_paquete_id INT, p_modulo_id INT)
RETURNS CHAR(1)
DETERMINISTIC
BEGIN
    DECLARE v_incluido CHAR(1);
    
    SELECT incluido INTO v_incluido
    FROM paquete_modulo
    WHERE paquete_id = p_paquete_id
      AND modulo_id = p_modulo_id
    LIMIT 1;
    
    RETURN IFNULL(v_incluido, 'N');
END$$
DELIMITER ;

-- ============================================================================
-- 10. EJEMPLOS DE USO
-- ============================================================================

/*
-- Ejemplo 1: Ver módulos del paquete Presencia
CALL sp_obtener_modulos_paquete(1);

-- Ejemplo 2: Verificar si el módulo de Clientes (id=28) está disponible
CALL sp_modulo_disponible(28);

-- Ejemplo 3: Cambiar empresa a paquete Gestión
UPDATE empresa SET paquete_id = 2 WHERE id = 1;

-- Ejemplo 4: Ver módulos disponibles usando la vista
SELECT * FROM vista_paquete_modulos WHERE paquete_id = 1;

-- Ejemplo 5: Usar la función para verificar inclusión
SELECT fn_modulo_en_paquete(1, 28) as clientes_en_presencia;  -- Debería retornar 'N'
SELECT fn_modulo_en_paquete(2, 28) as clientes_en_gestion;    -- Debería retornar 'S'
*/

-- ============================================================================
-- 11. SCRIPT DE ROLLBACK (OPCIONAL - SOLO SI NECESITAS DESHACER CAMBIOS)
-- ============================================================================

/*
-- CUIDADO: Este script elimina todo el sistema de paquetes
-- Ejecutar solo si necesitas deshacer los cambios

-- Eliminar FK de empresa
ALTER TABLE empresa DROP FOREIGN KEY fk_empresa_paquete;
ALTER TABLE empresa DROP COLUMN paquete_id;

-- Eliminar procedimientos y funciones
DROP PROCEDURE IF EXISTS sp_obtener_modulos_paquete;
DROP PROCEDURE IF EXISTS sp_modulo_disponible;
DROP FUNCTION IF EXISTS fn_modulo_en_paquete;

-- Eliminar vistas
DROP VIEW IF EXISTS vista_paquete_modulos;
DROP VIEW IF EXISTS vista_empresa_paquete;

-- Eliminar tablas
DROP TABLE IF EXISTS paquete_modulo;
DROP TABLE IF EXISTS paquetes;
*/

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================

-- Verificación final
SELECT '✅ Sistema de Paquetes instalado correctamente' as resultado;
SELECT COUNT(*) as total_paquetes FROM paquetes;
SELECT COUNT(*) as total_relaciones FROM paquete_modulo;
SELECT COUNT(*) as empresas_con_paquete FROM empresa WHERE paquete_id IS NOT NULL;

