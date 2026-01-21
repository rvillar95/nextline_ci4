-- =====================================================
-- CREAR SISTEMA DE MÉTODOS DE CÁLCULO DE COMPOSICIÓN CORPORAL
-- =====================================================
-- Este script crea:
-- 1. Tabla metodos_calculo (catálogo de métodos)
-- 2. Rutas en modulo_detalle para cada método
-- 3. Asignación de métodos a paquetes (usando paquete_modulo)
-- =====================================================

-- =====================================================
-- 1. CREAR TABLA metodos_calculo
-- =====================================================
CREATE TABLE IF NOT EXISTS `metodos_calculo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del método',
  `slug` VARCHAR(50) NOT NULL COMMENT 'Slug único',
  `descripcion` TEXT COMMENT 'Descripción del método',
  `componentes` INT NOT NULL COMMENT 'Número de componentes (2, 3, 4, 5, 6, 7)',
  `formula` TEXT COMMENT 'Fórmula o referencia al método',
  `requiere_datos` JSON COMMENT 'Array de campos requeridos',
  `precio_mensual` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Precio mensual si es add-on premium',
  `es_addon` ENUM('S', 'N') DEFAULT 'N' COMMENT 'S=Es add-on premium, N=Incluido en plan base',
  `activo` ENUM('A', 'I') DEFAULT 'A',
  `orden` INT DEFAULT 0,
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar métodos de cálculo
INSERT INTO `metodos_calculo` (`nombre`, `slug`, `descripcion`, `componentes`, `requiere_datos`, `precio_mensual`, `es_addon`, `activo`, `orden`) VALUES
('Masa Magra / Masa Adiposa', '2-componentes', 'Método de 2 componentes que separa masa magra y masa adiposa. Ideal para evaluaciones básicas de composición corporal.', 2, '["peso_actual", "altura_actual", "pliegue_tricipital", "pliegue_subescapular", "pliegue_suprailíaco"]', 0.00, 'N', 'A', 1),
('Grasa / Músculo / Hueso / Residual', '4-componentes', 'Método de 4 componentes (Fisionutdep). Divide el cuerpo en grasa, músculo, hueso y masa residual. Requiere mediciones adicionales.', 4, '["peso_actual", "altura_actual", "altura_sentado", "diametro_humero", "diametro_femur", "circunferencia_brazo_contraido", "circunferencia_pantorrilla"]', 9990.00, 'S', 'A', 2),
('Grasa / Músculo / Hueso / Residual / Piel', '5-componentes', 'Método de 5 componentes (Francis Holway). El método más completo que incluye también la masa de la piel. Requiere todas las mediciones antropométricas.', 5, '["peso_actual", "altura_actual", "altura_sentado", "diametro_biacromial", "diametro_bi_iliocristal", "diametro_humero", "diametro_femur", "pliegue_pectoral", "pliegue_axilar_medio"]', 14990.00, 'S', 'A', 3),
('Somatotipo (Heath-Carter)', 'somatotipo', 'Cálculo de somatotipo según método Heath-Carter. Determina endomorfia, mesomorfia y ectomorfia del individuo.', 3, '["peso_actual", "altura_actual", "altura_sentado", "pliegue_tricipital", "pliegue_subescapular", "pliegue_suprailíaco", "diametro_humero", "diametro_femur", "circunferencia_brazo_contraido", "circunferencia_pantorrilla"]', 9990.00, 'S', 'A', 4)
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `componentes` = VALUES(`componentes`),
    `requiere_datos` = VALUES(`requiere_datos`),
    `precio_mensual` = VALUES(`precio_mensual`),
    `es_addon` = VALUES(`es_addon`),
    `activo` = 'A',
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- =====================================================
-- 2. CREAR RUTAS EN modulo_detalle PARA CADA MÉTODO
-- =====================================================
-- Obtener ID del módulo Historial Clínico
SET @modulo_historial_id = (SELECT id FROM modulo WHERE nombre = 'Historial Clínico' LIMIT 1);

-- Si no existe, usar el ID 36 (según la estructura actual)
SET @modulo_historial_id = IFNULL(@modulo_historial_id, 36);

-- Obtener siguiente orden disponible
SET @siguiente_orden = (SELECT IFNULL(MAX(orden), 0) + 1 FROM modulo_detalle WHERE modulo_id = @modulo_historial_id);

-- Insertar rutas de métodos de cálculo
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_historial_id, 'Calcular Composición 2 Componentes', '/calcular-2-componentes', 'ver', 'A', 'N', @siguiente_orden, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Composición 4 Componentes', '/calcular-4-componentes', 'ver', 'A', 'N', @siguiente_orden + 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Composición 5 Componentes', '/calcular-5-componentes', 'ver', 'A', 'N', @siguiente_orden + 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Somatotipo', '/calcular-somatotipo', 'ver', 'A', 'N', @siguiente_orden + 3, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `factualizacion` = NOW();

-- =====================================================
-- 3. CREAR TABLA paquete_modulo_detalle (para controlar rutas específicas)
-- =====================================================
-- Esta tabla relaciona paquetes con rutas específicas (modulo_detalle)
-- Permite controlar qué métodos de cálculo están disponibles en cada paquete
CREATE TABLE IF NOT EXISTS `paquete_modulo_detalle` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `paquete_id` INT NOT NULL COMMENT 'ID del paquete',
  `modulo_detalle_id` INT NOT NULL COMMENT 'ID de la ruta específica (modulo_detalle)',
  `incluido` ENUM('S', 'N') DEFAULT 'S' COMMENT 'S=Incluido, N=Bloqueado',
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`),
  KEY `idx_paquete` (`paquete_id`),
  KEY `idx_modulo_detalle` (`modulo_detalle_id`),
  CONSTRAINT `fk_paquete_modulo_detalle_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_paquete_modulo_detalle_modulo_detalle` FOREIGN KEY (`modulo_detalle_id`) REFERENCES `modulo_detalle` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 4. ASIGNAR MÉTODOS A PAQUETES (usando paquete_modulo_detalle)
-- =====================================================

-- Obtener IDs de las rutas creadas
SET @ruta_2_comp = (SELECT id FROM modulo_detalle WHERE ruta = '/calcular-2-componentes' AND modulo_id = @modulo_historial_id LIMIT 1);
SET @ruta_4_comp = (SELECT id FROM modulo_detalle WHERE ruta = '/calcular-4-componentes' AND modulo_id = @modulo_historial_id LIMIT 1);
SET @ruta_5_comp = (SELECT id FROM modulo_detalle WHERE ruta = '/calcular-5-componentes' AND modulo_id = @modulo_historial_id LIMIT 1);
SET @ruta_somatotipo = (SELECT id FROM modulo_detalle WHERE ruta = '/calcular-somatotipo' AND modulo_id = @modulo_historial_id LIMIT 1);

-- Plan Básico (paquete_id = 4): Solo método 2 componentes (gratis)
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT 4, @ruta_2_comp, 'S', NOW()
WHERE @ruta_2_comp IS NOT NULL
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- Plan Premium (paquete_id = 5): Métodos 2, 4 componentes + somatotipo
-- Primero crear el paquete si no existe
INSERT INTO `paquetes` (`id`, `nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
VALUES (5, 'NextLine Nutrición Premium', 'nutricion-premium', 'Plan premium con métodos avanzados de composición corporal', 0.00, 19990.00, 'A', 5, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `precio_mensual` = VALUES(`precio_mensual`),
    `activo` = 'A',
    `factualizacion` = NOW();

-- Asignar métodos al plan premium
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT 5, id, 'S', NOW()
FROM modulo_detalle
WHERE modulo_id = @modulo_historial_id
  AND ruta IN ('/calcular-2-componentes', '/calcular-4-componentes', '/calcular-somatotipo')
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- Plan Avanzado (paquete_id = 6): Todos los métodos
-- Primero crear el paquete si no existe
INSERT INTO `paquetes` (`id`, `nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
VALUES (6, 'NextLine Nutrición Avanzado', 'nutricion-avanzado', 'Plan avanzado con todos los métodos de composición corporal', 0.00, 39990.00, 'A', 6, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `precio_mensual` = VALUES(`precio_mensual`),
    `activo` = 'A',
    `factualizacion` = NOW();

-- Asignar todos los métodos al plan avanzado
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT 6, id, 'S', NOW()
FROM modulo_detalle
WHERE modulo_id = @modulo_historial_id
  AND ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- =====================================================
-- 5. CREAR TABLA empresa_addon (para add-ons individuales)
-- =====================================================
CREATE TABLE IF NOT EXISTS `empresa_addon` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `tipo` ENUM('modulo', 'metodo_calculo') NOT NULL COMMENT 'Tipo de add-on',
  `referencia_id` INT NOT NULL COMMENT 'ID del módulo (modulo_detalle) o método de cálculo',
  `precio_mensual` DECIMAL(10,2) NOT NULL COMMENT 'Precio mensual del add-on',
  `fecha_inicio` DATE NOT NULL COMMENT 'Fecha de inicio del add-on',
  `fecha_fin` DATE NULL COMMENT 'Fecha de fin (NULL = activo indefinidamente)',
  `estado` ENUM('activo', 'suspendido', 'cancelado') DEFAULT 'activo',
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_empresa_addon` (`empresa_id`, `tipo`, `referencia_id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_tipo_referencia` (`tipo`, `referencia_id`),
  KEY `idx_estado` (`estado`),
  CONSTRAINT `fk_empresa_addon_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- 6. MODIFICAR TABLA pagos PARA SOPORTAR ADD-ONS
-- =====================================================
-- Agregar campo addon_id si no existe (usando procedimiento almacenado temporal)
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_columna_addon_id//
CREATE PROCEDURE agregar_columna_addon_id()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    DECLARE CONTINUE HANDLER FOR 1061 BEGIN END; -- Error: Duplicate key name
    DECLARE CONTINUE HANDLER FOR 1022 BEGIN END; -- Error: Duplicate foreign key name
    
    -- Intentar agregar columna
    ALTER TABLE `pagos` 
    ADD COLUMN `addon_id` INT NULL COMMENT 'ID del add-on si el pago es por add-on' AFTER `paquete_id`;
END//
DELIMITER ;

CALL agregar_columna_addon_id();
DROP PROCEDURE IF EXISTS agregar_columna_addon_id;

-- Agregar índice si no existe
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_indice_addon//
CREATE PROCEDURE agregar_indice_addon()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1061 BEGIN END; -- Error: Duplicate key name
    ALTER TABLE `pagos` ADD KEY `idx_addon` (`addon_id`);
END//
DELIMITER ;

CALL agregar_indice_addon();
DROP PROCEDURE IF EXISTS agregar_indice_addon;

-- Agregar constraint si no existe
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_fk_addon//
CREATE PROCEDURE agregar_fk_addon()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1022 BEGIN END; -- Error: Duplicate foreign key name
    ALTER TABLE `pagos` ADD CONSTRAINT `fk_pagos_addon` FOREIGN KEY (`addon_id`) REFERENCES `empresa_addon` (`id`) ON DELETE SET NULL;
END//
DELIMITER ;

CALL agregar_fk_addon();
DROP PROCEDURE IF EXISTS agregar_fk_addon;

-- =====================================================
-- 7. AGREGAR CAMPOS precio_mensual Y es_addon A modulo
-- =====================================================
-- Agregar campos si no existen (usando procedimiento almacenado temporal)
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_campos_modulo//
CREATE PROCEDURE agregar_campos_modulo()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `modulo` 
    ADD COLUMN `precio_mensual` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Precio mensual si es add-on premium' AFTER `descripcion`,
    ADD COLUMN `es_addon` ENUM('S', 'N') DEFAULT 'N' COMMENT 'S=Es add-on premium, N=Incluido en plan base' AFTER `precio_mensual`;
END//
DELIMITER ;

CALL agregar_campos_modulo();
DROP PROCEDURE IF EXISTS agregar_campos_modulo;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar métodos creados
SELECT 
    id,
    nombre,
    slug,
    componentes,
    precio_mensual,
    es_addon,
    activo
FROM metodos_calculo
ORDER BY orden;

-- Verificar rutas creadas
SELECT 
    md.id,
    m.nombre AS modulo,
    md.descripcion,
    md.ruta,
    md.accion,
    md.estado
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.ruta LIKE '/calcular-%'
ORDER BY md.orden;

-- Verificar asignación a paquetes
SELECT 
    p.nombre AS paquete,
    md.descripcion AS metodo,
    pmd.incluido
FROM paquete_modulo_detalle pmd
JOIN paquetes p ON p.id = pmd.paquete_id
JOIN modulo_detalle md ON md.id = pmd.modulo_detalle_id
WHERE md.ruta LIKE '/calcular-%'
ORDER BY p.id, md.orden;
