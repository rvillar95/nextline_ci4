-- ============================================================================
-- SISTEMA DE PAQUETES DE MÓDULOS - NextLine V2
-- Integrado con sistema de PODERES y MULTI-TENANT
-- ============================================================================
-- 
-- ARQUITECTURA:
-- 1. Super Admin (poder=3): Ve TODO, gestiona empresas y asigna paquetes
-- 2. Admin Empresa (poder=2): Ve solo módulos de su paquete asignado
-- 3. Usuarios Empresa (poder<2): Permisos definidos por Admin Empresa
--
-- FLUJO:
-- 1. Super Admin crea Empresa y le asigna un Paquete
-- 2. Super Admin crea Usuario Admin para esa Empresa
-- 3. Usuario Admin solo ve módulos incluidos en el paquete de su empresa
-- 4. Sistema valida: PODER del perfil + PAQUETE de la empresa
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
-- 4.1 MÓDULOS SUPER ADMIN (sa='S') - NO SE ASIGNAN A PAQUETES
-- ============================================================================
-- Estos módulos SOLO los ve el Super Admin independiente del paquete:
-- - Modulo (id=4) - Gestión de módulos del sistema
-- - Modulo Detalle (id=7) - Gestión de permisos de módulos
--
-- NOTA: Usuario (id=1), Perfil (id=3) y Perfil Detalle (id=6) 
-- SÍ deben estar disponibles para Admin de Empresa para que puedan
-- gestionar usuarios y roles de su propia empresa

-- ============================================================================
-- 4.2 MÓDULOS COMUNES A TODOS LOS PAQUETES (Admin Empresa puede verlos)
-- ============================================================================

-- Módulo Inicio (id=2) - Todos los paquetes
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 2, 'S'),  -- Presencia
(2, 2, 'S'),  -- Gestión
(3, 2, 'S'); -- Custom

-- Módulos de Gestión de Usuarios y Perfiles - TODOS LOS PAQUETES
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- Usuario (id=1)
(1, 1, 'S'),  -- Presencia
(2, 1, 'S'),  -- Gestión
(3, 1, 'S'),  -- Custom
-- Perfil (id=3)
(1, 3, 'S'),  -- Presencia
(2, 3, 'S'),  -- Gestión
(3, 3, 'S'),  -- Custom
-- Perfil Detalle (id=6)
(1, 6, 'S'),  -- Presencia
(2, 6, 'S'),  -- Gestión
(3, 6, 'S'); -- Custom

-- Módulo Empresa (id=31) - Todos los paquetes (para editar datos de su empresa)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 31, 'S'),  -- Presencia
(2, 31, 'S'),  -- Gestión
(3, 31, 'S'); -- Custom

-- ============================================================================
-- 4.3 PAQUETE PRESENCIA (id=1) - Módulos de "Mostrar"
-- ============================================================================

INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- Módulos Web de Presencia
(1, 8, 'S'),   -- Servicios
(1, 17, 'S'),  -- Categorías de Servicios
(1, 14, 'S'),  -- Galería
(1, 16, 'S'),  -- Categorías de Galería
(1, 18, 'S'),  -- Proyectos
(1, 26, 'S'),  -- Testimonios
(1, 19, 'S'),  -- Contactos (Leads)
(1, 30, 'S'); -- Ubicación (regiones/comunas - auxiliar)

-- Módulos BLOQUEADOS en Presencia
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 28, 'N'),  -- Clientes (bloqueado)
(1, 29, 'N'); -- Cotizaciones (bloqueado)

-- ============================================================================
-- 4.4 PAQUETE GESTIÓN (id=2) - Presencia + Gestión
-- ============================================================================

INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- Módulos Web de Presencia
(2, 8, 'S'),   -- Servicios
(2, 17, 'S'),  -- Categorías de Servicios
(2, 14, 'S'),  -- Galería
(2, 16, 'S'),  -- Categorías de Galería
(2, 18, 'S'),  -- Proyectos
(2, 26, 'S'),  -- Testimonios
(2, 19, 'S'),  -- Contactos (Leads)
(2, 30, 'S'),  -- Ubicación

-- Módulos de GESTIÓN (adicionales)
(2, 28, 'S'),  -- Clientes (incluido)
(2, 29, 'S'); -- Cotizaciones (incluido)

-- ============================================================================
-- 4.5 PAQUETE CUSTOM (id=3) - Todos los módulos excepto los de Super Admin
-- ============================================================================

INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`) 
SELECT 3, id, 'S' 
FROM modulo 
WHERE estado = 'A' 
  AND sa = 'N'; -- Solo módulos que NO son exclusivos de Super Admin

-- ============================================================================
-- 5. AGREGAR CAMPO paquete_id A LA TABLA empresa
-- ============================================================================

-- Verificar y agregar columna paquete_id
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
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' int DEFAULT 1 COMMENT ''ID del paquete contratado'' AFTER id')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar foreign key
SET @fk_name = 'fk_empresa_paquete';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (constraint_name = @fk_name)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD CONSTRAINT ', @fk_name, ' FOREIGN KEY (paquete_id) REFERENCES paquetes(id)')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Actualizar empresa existente al paquete Presencia
UPDATE `empresa` SET `paquete_id` = 1 WHERE `paquete_id` IS NULL OR `paquete_id` = 0;

-- ============================================================================
-- 6. AGREGAR CAMPO empresa_id A LA TABLA usuario
-- ============================================================================
-- Para saber a qué empresa pertenece cada usuario Admin

SET @tablename = 'usuario';
SET @columnname = 'empresa_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' int DEFAULT NULL COMMENT ''ID de la empresa (NULL para Super Admin)'' AFTER perfil_id')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar foreign key
SET @fk_name = 'fk_usuario_empresa';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (constraint_name = @fk_name)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD CONSTRAINT ', @fk_name, ' FOREIGN KEY (empresa_id) REFERENCES empresa(id) ON DELETE SET NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Super Admin (poder=3) NO tiene empresa_id (es NULL)
UPDATE usuario u
INNER JOIN perfil p ON p.id = u.perfil_id
SET u.empresa_id = NULL
WHERE p.poder = 3;

-- ============================================================================
-- 7. FUNCIÓN: Obtener módulos según PODER y PAQUETE
-- ============================================================================

DELIMITER $$
DROP FUNCTION IF EXISTS `fn_usuario_puede_ver_modulo` $$
CREATE FUNCTION `fn_usuario_puede_ver_modulo`(
    p_usuario_id INT, 
    p_modulo_id INT
) RETURNS CHAR(1)
DETERMINISTIC
BEGIN
    DECLARE v_poder INT;
    DECLARE v_empresa_id INT;
    DECLARE v_paquete_id INT;
    DECLARE v_es_super_admin CHAR(1);
    DECLARE v_incluido CHAR(1);
    
    -- Obtener poder del perfil del usuario
    SELECT p.poder, u.empresa_id
    INTO v_poder, v_empresa_id
    FROM usuario u
    INNER JOIN perfil p ON p.id = u.perfil_id
    WHERE u.id = p_usuario_id
    LIMIT 1;
    
    -- Si es Super Admin (poder=3) puede ver TODO
    IF v_poder = 3 THEN
        RETURN 'S';
    END IF;
    
    -- Verificar si el módulo es exclusivo de Super Admin
    SELECT sa INTO v_es_super_admin
    FROM modulo
    WHERE id = p_modulo_id
    LIMIT 1;
    
    -- Si el módulo es SA='S' y el usuario NO es Super Admin, bloquear
    IF v_es_super_admin = 'S' AND v_poder < 3 THEN
        RETURN 'N';
    END IF;
    
    -- Si el usuario no tiene empresa asignada (raro pero posible), bloquear
    IF v_empresa_id IS NULL THEN
        RETURN 'N';
    END IF;
    
    -- Obtener paquete de la empresa del usuario
    SELECT paquete_id INTO v_paquete_id
    FROM empresa
    WHERE id = v_empresa_id
    LIMIT 1;
    
    -- Verificar si el módulo está incluido en el paquete
    SELECT incluido INTO v_incluido
    FROM paquete_modulo
    WHERE paquete_id = v_paquete_id
      AND modulo_id = p_modulo_id
    LIMIT 1;
    
    RETURN IFNULL(v_incluido, 'N');
END$$
DELIMITER ;

-- ============================================================================
-- 8. PROCEDIMIENTO: Obtener menú de módulos para un usuario
-- ============================================================================

DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_obtener_menu_usuario` $$
CREATE PROCEDURE `sp_obtener_menu_usuario`(IN p_usuario_id INT)
BEGIN
    DECLARE v_poder INT;
    DECLARE v_empresa_id INT;
    DECLARE v_paquete_id INT;
    
    -- Obtener datos del usuario
    SELECT p.poder, u.empresa_id
    INTO v_poder, v_empresa_id
    FROM usuario u
    INNER JOIN perfil p ON p.id = u.perfil_id
    WHERE u.id = p_usuario_id
    LIMIT 1;
    
    -- Si es Super Admin (poder=3), mostrar TODOS los módulos
    IF v_poder = 3 THEN
        SELECT 
            m.id,
            m.nombre,
            m.descripcion,
            m.ruta,
            m.estado,
            m.mostrar,
            m.sa,
            m.orden,
            'S' as incluido_en_paquete,
            NULL as paquete_nombre
        FROM modulo m
        WHERE m.estado = 'A'
          AND m.mostrar = 'S'
        ORDER BY m.orden, m.id;
    ELSE
        -- Usuario Admin de Empresa: mostrar solo módulos de su paquete
        SELECT paquete_id INTO v_paquete_id
        FROM empresa
        WHERE id = v_empresa_id
        LIMIT 1;
        
        SELECT 
            m.id,
            m.nombre,
            m.descripcion,
            m.ruta,
            m.estado,
            m.mostrar,
            m.sa,
            m.orden,
            pm.incluido as incluido_en_paquete,
            p.nombre as paquete_nombre
        FROM modulo m
        INNER JOIN paquete_modulo pm ON pm.modulo_id = m.id
        INNER JOIN paquetes p ON p.id = pm.paquete_id
        WHERE m.estado = 'A'
          AND m.mostrar = 'S'
          AND m.sa = 'N'  -- Excluir módulos de Super Admin
          AND pm.paquete_id = v_paquete_id
          AND pm.incluido = 'S'
        ORDER BY m.orden, m.id;
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- 9. VISTA: Información completa de usuario con empresa y paquete
-- ============================================================================

CREATE OR REPLACE VIEW `vista_usuario_empresa_paquete` AS
SELECT 
    u.id as usuario_id,
    u.nombre as usuario_nombre,
    u.apellido as usuario_apellido,
    u.correo,
    p.id as perfil_id,
    p.nombre as perfil_nombre,
    p.poder,
    CASE 
        WHEN p.poder = 3 THEN 'Super Admin'
        WHEN p.poder = 2 THEN 'Admin Empresa'
        WHEN p.poder = 1 THEN 'Usuario Empresa'
        ELSE 'Sin Poder'
    END as tipo_usuario,
    e.id as empresa_id,
    e.nombre as empresa_nombre,
    paq.id as paquete_id,
    paq.nombre as paquete_nombre,
    paq.slug as paquete_slug
FROM usuario u
INNER JOIN perfil p ON p.id = u.perfil_id
LEFT JOIN empresa e ON e.id = u.empresa_id
LEFT JOIN paquetes paq ON paq.id = e.paquete_id
WHERE u.estado = 'A';

-- ============================================================================
-- 10. VISTA: Módulos disponibles por paquete
-- ============================================================================

CREATE OR REPLACE VIEW `vista_modulos_por_paquete` AS
SELECT 
    p.id as paquete_id,
    p.nombre as paquete_nombre,
    p.slug as paquete_slug,
    m.id as modulo_id,
    m.nombre as modulo_nombre,
    m.ruta as modulo_ruta,
    m.descripcion as modulo_descripcion,
    m.sa as solo_super_admin,
    pm.incluido,
    m.orden
FROM paquetes p
INNER JOIN paquete_modulo pm ON pm.paquete_id = p.id
INNER JOIN modulo m ON m.id = pm.modulo_id
WHERE p.activo = 'A' 
  AND m.estado = 'A'
ORDER BY p.orden, m.orden, m.id;

-- ============================================================================
-- 11. DATOS DE EJEMPLO Y VERIFICACIÓN
-- ============================================================================

-- Ver todos los usuarios con su empresa y paquete
SELECT * FROM vista_usuario_empresa_paquete;

-- Ver módulos del paquete Presencia
SELECT * FROM vista_modulos_por_paquete WHERE paquete_id = 1;

-- Ver módulos del paquete Gestión
SELECT * FROM vista_modulos_por_paquete WHERE paquete_id = 2;

-- ============================================================================
-- 12. EJEMPLOS DE USO
-- ============================================================================

/*
-- Ejemplo 1: Obtener menú para un Super Admin (usuario id=6)
CALL sp_obtener_menu_usuario(6);

-- Ejemplo 2: Obtener menú para un Admin de Empresa
CALL sp_obtener_menu_usuario(17);  -- Cambiar por ID real

-- Ejemplo 3: Verificar si un usuario puede ver un módulo
SELECT fn_usuario_puede_ver_modulo(17, 28) as puede_ver_clientes;

-- Ejemplo 4: Asignar paquete Gestión a una empresa
UPDATE empresa SET paquete_id = 2 WHERE id = 1;

-- Ejemplo 5: Ver qué módulos puede ver cada usuario
SELECT 
    u.nombre,
    u.apellido,
    e.nombre as empresa,
    paq.nombre as paquete,
    m.nombre as modulo,
    fn_usuario_puede_ver_modulo(u.id, m.id) as puede_ver
FROM usuario u
LEFT JOIN empresa e ON e.id = u.empresa_id
LEFT JOIN paquetes paq ON paq.id = e.paquete_id
CROSS JOIN modulo m
WHERE u.estado = 'A'
  AND m.estado = 'A'
  AND m.mostrar = 'S'
ORDER BY u.id, m.orden;
*/

-- ============================================================================
-- 13. RESUMEN DE LA LÓGICA
-- ============================================================================

/*
FLUJO DE PERMISOS:

1. SUPER ADMIN (poder=3):
   ✅ Ve TODOS los módulos (incluso sa='S': Modulo, Modulo Detalle)
   ✅ empresa_id = NULL
   ✅ Gestiona empresas y asigna paquetes
   ✅ Crea usuarios Admin para cada empresa
   ✅ Gestiona el SISTEMA completo

2. ADMIN EMPRESA (poder=2):
   ✅ Ve módulos de su paquete (pm.incluido='S')
   ✅ Ve: Usuario, Perfil, Perfil Detalle (para gestionar su equipo)
   ✅ NO ve: Modulo, Modulo Detalle (exclusivos de Super Admin)
   ✅ empresa_id = ID de su empresa
   ✅ Puede crear usuarios y perfiles con poder < 2
   ✅ NO puede ver perfiles con poder >= 2
   ✅ Gestiona su EMPRESA

3. USUARIO EMPRESA (poder<2):
   ✅ Permisos definidos por perfil_modulo
   ✅ Limitado por paquete de su empresa
   ✅ empresa_id = ID de su empresa
   ✅ Trabaja en su EMPRESA

MÓDULOS POR TIPO:

A) EXCLUSIVOS SUPER ADMIN (sa='S'):
   - Modulo (id=4)
   - Modulo Detalle (id=7)

B) GESTIÓN DE USUARIOS (todos los paquetes):
   - Usuario (id=1)
   - Perfil (id=3)
   - Perfil Detalle (id=6)

C) PAQUETE PRESENCIA:
   - Servicios, Galería, Proyectos, Testimonios, Leads, etc.

D) PAQUETE GESTIÓN (Presencia +):
   - Clientes (id=28)
   - Cotizaciones (id=29)

VALIDACIÓN EN CÓDIGO PHP:
1. Obtener poder del perfil del usuario
2. Si poder=3: mostrar todo
3. Si poder<3: 
   - Obtener empresa_id del usuario
   - Obtener paquete_id de la empresa
   - Filtrar módulos donde pm.incluido='S' AND m.sa='N'
*/

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================

SELECT '✅ Sistema de Paquetes con Poderes instalado correctamente' as resultado;
SELECT COUNT(*) as total_paquetes FROM paquetes;
SELECT COUNT(*) as total_relaciones FROM paquete_modulo;
SELECT COUNT(*) as empresas_con_paquete FROM empresa WHERE paquete_id IS NOT NULL;
SELECT COUNT(*) as usuarios_con_empresa FROM usuario WHERE empresa_id IS NOT NULL;

