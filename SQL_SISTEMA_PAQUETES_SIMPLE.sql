-- ============================================================================
-- SISTEMA DE PAQUETES DE MÓDULOS - NextLine SIMPLE
-- Versión mono-empresa: 1 empresa activa en el sistema
-- ============================================================================
-- 
-- ARQUITECTURA SIMPLIFICADA:
-- 1. Super Admin (poder=3): Ve TODO, gestiona paquetes
-- 2. Admin Empresa (poder=2): Ve solo módulos del paquete asignado a LA empresa
-- 3. Usuarios Empresa (poder<2): Permisos definidos por Admin Empresa
--
-- FLUJO:
-- 1. Sistema tiene UNA empresa activa con un paquete asignado
-- 2. Todos los usuarios Admin ven los módulos del paquete de esa empresa
-- 3. Super Admin ve todos los módulos y puede cambiar el paquete
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

-- MÓDULOS COMUNES A TODOS LOS PAQUETES
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- Inicio (id=2)
(1, 2, 'S'),  -- Presencia
(2, 2, 'S'),  -- Gestión
(3, 2, 'S'),  -- Custom
-- Usuario (id=1)
(1, 1, 'S'),
(2, 1, 'S'),
(3, 1, 'S'),
-- Perfil (id=3)
(1, 3, 'S'),
(2, 3, 'S'),
(3, 3, 'S'),
-- Perfil Detalle (id=6)
(1, 6, 'S'),
(2, 6, 'S'),
(3, 6, 'S'),
-- Empresa (id=31)
(1, 31, 'S'),
(2, 31, 'S'),
(3, 31, 'S');

-- MÓDULOS NEXTLINE PRESENCIA
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 5, 'S'),   -- Servicio
(1, 8, 'S'),   -- Servicio Detalle
(1, 9, 'S'),   -- Galería
(1, 10, 'S'),  -- Galería Categoría
(1, 11, 'S'),  -- Proyecto
(1, 12, 'S'),  -- Proyecto Categoría
(1, 30, 'S'),  -- Testimonio
(1, 29, 'S');  -- Lead

-- MÓDULOS NEXTLINE GESTIÓN (incluye todo de Presencia + extras)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- Módulos de Presencia
(2, 5, 'S'),   -- Servicio
(2, 8, 'S'),   -- Servicio Detalle
(2, 9, 'S'),   -- Galería
(2, 10, 'S'),  -- Galería Categoría
(2, 11, 'S'),  -- Proyecto
(2, 12, 'S'),  -- Proyecto Categoría
(2, 30, 'S'),  -- Testimonio
(2, 29, 'S'),  -- Lead
-- Módulos adicionales de Gestión
(2, 13, 'S'),  -- Cliente
(2, 14, 'S'),  -- Cotización
(2, 15, 'S');  -- Cotización Detalle

-- NEXTLINE CUSTOM (todo)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(3, 5, 'S'),   -- Servicio
(3, 8, 'S'),   -- Servicio Detalle
(3, 9, 'S'),   -- Galería
(3, 10, 'S'),  -- Galería Categoría
(3, 11, 'S'),  -- Proyecto
(3, 12, 'S'),  -- Proyecto Categoría
(3, 13, 'S'),  -- Cliente
(3, 14, 'S'),  -- Cotización
(3, 15, 'S'),  -- Cotización Detalle
(3, 30, 'S'),  -- Testimonio
(3, 29, 'S');  -- Lead

-- ============================================================================
-- 5. AGREGAR CAMPO paquete_id A LA TABLA empresa
-- ============================================================================

SET @tablename = 'empresa';
SET @columnname = 'paquete_id';

-- Verificar si la columna existe
SET @column_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
);

-- Si no existe, agregar la columna
SET @sql = IF(
  @column_exists = 0,
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' int NULL COMMENT ''ID del paquete contratado'' AFTER rut'),
  'SELECT ''La columna ya existe'' AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 6. AGREGAR FOREIGN KEY de empresa.paquete_id → paquetes.id
-- ============================================================================

SET @fk_name = 'fk_empresa_paquete';

-- Verificar si el FK ya existe
SET @fk_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = @fk_name
);

-- Si no existe, crear el FK
SET @sql = IF(
  @fk_exists = 0,
  CONCAT('ALTER TABLE ', @tablename, ' ADD CONSTRAINT ', @fk_name, ' FOREIGN KEY (paquete_id) REFERENCES paquetes(id) ON DELETE SET NULL'),
  'SELECT ''El FK ya existe'' AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 7. ASIGNAR PAQUETE POR DEFECTO A LA EMPRESA ACTIVA
-- ============================================================================

-- Asignar "NextLine Presencia" (id=1) a la empresa activa del sistema
UPDATE empresa 
SET paquete_id = 1
WHERE estado = 'A' 
  AND (paquete_id IS NULL OR paquete_id = 0)
LIMIT 1;

-- ============================================================================
-- 8. MARCAR MÓDULOS EXCLUSIVOS DE SUPER ADMIN
-- ============================================================================

-- Marcar módulos que solo puede ver el Super Admin
UPDATE modulo SET sa = 'S' WHERE id IN (4, 7);  -- Modulo, Modulo Detalle
UPDATE modulo SET sa = 'N' WHERE id NOT IN (4, 7);

-- ============================================================================
-- 9. VERIFICACIÓN FINAL
-- ============================================================================

SELECT '=== PAQUETES CREADOS ===' as check_paso;
SELECT * FROM paquetes;

SELECT '=== EMPRESA CON PAQUETE ASIGNADO ===' as check_paso;
SELECT id, nombre, rut, paquete_id, estado FROM empresa WHERE estado = 'A';

SELECT '=== MÓDULOS POR PAQUETE ===' as check_paso;
SELECT 
    p.nombre as paquete, 
    m.nombre as modulo,
    m.sa as super_admin_only
FROM paquete_modulo pm
JOIN paquetes p ON pm.paquete_id = p.id
JOIN modulo m ON pm.modulo_id = m.id
WHERE pm.incluido = 'S'
ORDER BY p.orden, m.nombre;

SELECT '=== MÓDULOS SOLO SUPER ADMIN ===' as check_paso;
SELECT id, nombre, sa FROM modulo WHERE sa = 'S';

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================

