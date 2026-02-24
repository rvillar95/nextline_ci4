-- =====================================================
-- SISTEMA DE PLAN ALIMENTARIO Y CALORIMETRÍA
-- =====================================================
-- Incluye: Calorimetría, Plan Alimentario, Distribución por Comidas
-- Módulo: Plan Alimentario (incluido en paquete base, no add-on)
-- =====================================================

-- =====================================================
-- 1. CREAR MÓDULO "PLAN ALIMENTARIO"
-- =====================================================

-- Crear módulo principal (paquete base, no add-on)
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `es_addon`, `precio_mensual`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(42, 'Plan Alimentario', 'Cálculo de calorimetría, plan alimentario y distribución por comidas', '/dashboard/plan-alimentario', 'A', 'S', 'N', 'N', 0.00, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `es_addon` = 'N',
    `factualizacion` = NOW();

-- Obtener ID real del módulo
SET @modulo_plan_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1);

-- Crear rutas del módulo (modulo_detalle)
-- IMPORTANTE: Las rutas deben ser absolutas (completas) para que el SessionFilter las reconozca
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_plan_id, 'Calcular Calorimetría', '/dashboard/plan-alimentario/calcular-calorimetria', 'editar', 'A', 'N', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Ver Calorimetría', '/dashboard/plan-alimentario/calorimetria', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Obtener Actividades', '/dashboard/plan-alimentario/actividades', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Crear Plan Alimentario', '/dashboard/plan-alimentario/crear-plan', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Ver Plan Alimentario', '/dashboard/plan-alimentario/plan', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Obtener Intercambios', '/dashboard/plan-alimentario/intercambios', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Distribuir por Comidas', '/dashboard/plan-alimentario/distribuir-comidas', 'editar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Ver Comidas', '/dashboard/plan-alimentario/comidas', 'ver', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `estado` = 'A',
    `factualizacion` = NOW();

-- =====================================================
-- 2. CREAR TABLAS
-- =====================================================

-- Tabla: intercambio_porcion (Catálogo de intercambios)
CREATE TABLE IF NOT EXISTS `intercambio_porcion` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `codigo` VARCHAR(10) NOT NULL UNIQUE COMMENT 'cer, ver, fru, etc.',
    `nombre` VARCHAR(100) NOT NULL COMMENT 'Cereales, Verduras, etc.',
    `kcal` DECIMAL(8,2) NOT NULL COMMENT 'Calorías por intercambio',
    `cho_g` DECIMAL(8,2) NOT NULL COMMENT 'Carbohidratos (g) por intercambio',
    `grasa_g` DECIMAL(8,2) NOT NULL COMMENT 'Grasas (g) por intercambio',
    `prot_g` DECIMAL(8,2) NOT NULL COMMENT 'Proteínas (g) por intercambio',
    `empresa_id` INT NULL COMMENT 'NULL = global, o específico por empresa',
    `activo` CHAR(1) DEFAULT 'A',
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_empresa` (`empresa_id`),
    INDEX `idx_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de intercambios/porciones estándar';

-- Tabla: calorimetria (Cálculo de gasto calórico)
CREATE TABLE IF NOT EXISTS `calorimetria` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `detalle_agenda_id` INT NOT NULL COMMENT 'FK a detalle_agenda',
    `paciente_id` INT NOT NULL,
    `nutricionista_id` INT NOT NULL COMMENT 'FK a usuario (nutricionista)',
    
    -- Inputs
    `peso` DECIMAL(6,2) NOT NULL COMMENT 'kg',
    `talla` DECIMAL(6,2) NOT NULL COMMENT 'cm',
    `edad` INT NOT NULL,
    `sexo` ENUM('M', 'F') NOT NULL,
    
    -- Cálculos TMB
    `tmb_hombres` DECIMAL(8,2) NULL,
    `tmb_mujeres` DECIMAL(8,2) NULL,
    `tmb_usado` DECIMAL(8,2) NOT NULL COMMENT 'TMB seleccionado según sexo',
    
    -- Totales actividades
    `total_minutos` INT DEFAULT 0,
    `cals_habituales` DECIMAL(8,2) DEFAULT 0,
    `cals_entrenamiento` DECIMAL(8,2) DEFAULT 0,
    
    -- Requerimiento final
    `requerimiento_total` DECIMAL(8,2) NOT NULL COMMENT 'TMB + actividades',
    
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_detalle_agenda` (`detalle_agenda_id`),
    INDEX `idx_paciente` (`paciente_id`),
    INDEX `idx_nutricionista` (`nutricionista_id`),
    FOREIGN KEY (`detalle_agenda_id`) REFERENCES `detalle_agenda`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cálculo de calorimetría por consulta';

-- Tabla: calorimetria_actividad (Detalle de actividades)
CREATE TABLE IF NOT EXISTS `calorimetria_actividad` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `calorimetria_id` INT NOT NULL,
    `tipo` ENUM('diaria', 'deporte') NOT NULL,
    `actividad` VARCHAR(100) NOT NULL COMMENT 'Durmiendo, Caminar rápido, etc.',
    `mets` DECIMAL(5,2) NOT NULL,
    `minutos_dia` INT NOT NULL DEFAULT 0,
    `calorias_hombre` DECIMAL(8,2) DEFAULT 0,
    `calorias_mujer` DECIMAL(8,2) DEFAULT 0,
    `orden` INT DEFAULT 0,
    INDEX `idx_calorimetria` (`calorimetria_id`),
    FOREIGN KEY (`calorimetria_id`) REFERENCES `calorimetria`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Actividades registradas en calorimetría';

-- Tabla: plan_alimentario (Plan nutricional)
CREATE TABLE IF NOT EXISTS `plan_alimentario` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `detalle_agenda_id` INT NOT NULL,
    `paciente_id` INT NOT NULL,
    `nutricionista_id` INT NOT NULL,
    `calorimetria_id` INT NULL COMMENT 'FK opcional a calorimetria',
    
    -- Requerimiento objetivo
    `requerimiento_kcal` DECIMAL(8,2) NOT NULL,
    `prot_porcentaje` DECIMAL(5,2) NOT NULL COMMENT '%',
    `grasa_porcentaje` DECIMAL(5,2) NOT NULL COMMENT '%',
    `cho_porcentaje` DECIMAL(5,2) NOT NULL COMMENT '%',
    
    -- Macros objetivo (calculados)
    `prot_gramos` DECIMAL(8,2) NOT NULL,
    `grasa_gramos` DECIMAL(8,2) NOT NULL,
    `cho_gramos` DECIMAL(8,2) NOT NULL,
    
    -- Adecuación
    `adecuacion_min` DECIMAL(8,2) NULL COMMENT '90% del requerimiento',
    `adecuacion_max` DECIMAL(8,2) NULL COMMENT '110% del requerimiento',
    
    -- Totales del plan (calculados desde porciones)
    `total_kcal` DECIMAL(8,2) DEFAULT 0,
    `total_cho` DECIMAL(8,2) DEFAULT 0,
    `total_grasa` DECIMAL(8,2) DEFAULT 0,
    `total_prot` DECIMAL(8,2) DEFAULT 0,
    
    -- Adecuación porcentual (calculada)
    `adecuacion_kcal_porc` DECIMAL(5,2) NULL,
    `adecuacion_cho_porc` DECIMAL(5,2) NULL,
    `adecuacion_grasa_porc` DECIMAL(5,2) NULL,
    `adecuacion_prot_porc` DECIMAL(5,2) NULL,
    
    `observaciones` TEXT NULL,
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_detalle_agenda` (`detalle_agenda_id`),
    INDEX `idx_paciente` (`paciente_id`),
    INDEX `idx_nutricionista` (`nutricionista_id`),
    FOREIGN KEY (`detalle_agenda_id`) REFERENCES `detalle_agenda`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plan alimentario por consulta';

-- Tabla: plan_alimentario_porcion (Porciones por grupo)
CREATE TABLE IF NOT EXISTS `plan_alimentario_porcion` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `plan_alimentario_id` INT NOT NULL,
    `intercambio_porcion_id` INT NOT NULL,
    `porciones` DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad de intercambios',
    
    -- Calculados (porciones × valores del intercambio)
    `calorias` DECIMAL(8,2) DEFAULT 0,
    `cho` DECIMAL(8,2) DEFAULT 0,
    `grasa` DECIMAL(8,2) DEFAULT 0,
    `prot` DECIMAL(8,2) DEFAULT 0,
    
    `orden` INT DEFAULT 0,
    INDEX `idx_plan` (`plan_alimentario_id`),
    INDEX `idx_intercambio` (`intercambio_porcion_id`),
    FOREIGN KEY (`plan_alimentario_id`) REFERENCES `plan_alimentario`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`intercambio_porcion_id`) REFERENCES `intercambio_porcion`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Porciones asignadas por grupo en el plan';

-- Tabla: plan_alimentario_comida (Distribución por comidas)
CREATE TABLE IF NOT EXISTS `plan_alimentario_comida` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `plan_alimentario_id` INT NOT NULL,
    `comida` ENUM('desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros') NOT NULL,
    `porcentaje_vct` DECIMAL(5,2) NULL COMMENT '% del VCT',
    `minuta` TEXT NULL COMMENT 'Descripción de la preparación',
    
    -- Totales por comida (calculados)
    `total_kcal` DECIMAL(8,2) DEFAULT 0,
    `total_cho` DECIMAL(8,2) DEFAULT 0,
    `total_grasa` DECIMAL(8,2) DEFAULT 0,
    `total_prot` DECIMAL(8,2) DEFAULT 0,
    
    `orden` INT DEFAULT 0,
    INDEX `idx_plan` (`plan_alimentario_id`),
    FOREIGN KEY (`plan_alimentario_id`) REFERENCES `plan_alimentario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comidas del plan alimentario';

-- Tabla: plan_alimentario_item (Items por comida)
CREATE TABLE IF NOT EXISTS `plan_alimentario_item` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `plan_alimentario_comida_id` INT NOT NULL,
    `intercambio_porcion_id` INT NOT NULL,
    `ingrediente` VARCHAR(200) NULL COMMENT 'Nombre del alimento/ingrediente',
    `porciones` DECIMAL(6,2) NOT NULL DEFAULT 0,
    `medida_casera` VARCHAR(100) NULL COMMENT '1/2 pan, 1 taza, etc.',
    `gramaje` VARCHAR(50) NULL COMMENT '50g, etc.',
    
    -- Calculados
    `calorias` DECIMAL(8,2) DEFAULT 0,
    `cho` DECIMAL(8,2) DEFAULT 0,
    `grasa` DECIMAL(8,2) DEFAULT 0,
    `prot` DECIMAL(8,2) DEFAULT 0,
    
    `costo_promedio` DECIMAL(10,2) NULL,
    `cantidad_comprada` VARCHAR(100) NULL,
    `observacion` TEXT NULL,
    `orden` INT DEFAULT 0,
    INDEX `idx_comida` (`plan_alimentario_comida_id`),
    INDEX `idx_intercambio` (`intercambio_porcion_id`),
    FOREIGN KEY (`plan_alimentario_comida_id`) REFERENCES `plan_alimentario_comida`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`intercambio_porcion_id`) REFERENCES `intercambio_porcion`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Items específicos por comida';

-- Tabla: actividad_met (Catálogo de actividades con METs)
CREATE TABLE IF NOT EXISTS `actividad_met` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `tipo` ENUM('diaria', 'deporte') NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `mets` DECIMAL(5,2) NOT NULL,
    `descripcion` TEXT NULL,
    `orden` INT DEFAULT 0,
    `activo` CHAR(1) DEFAULT 'A',
    `fcreacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de actividades con valores METs';

-- =====================================================
-- 3. INSERTAR DATOS INICIALES
-- =====================================================

-- Insertar intercambios/porciones estándar
INSERT INTO `intercambio_porcion` (`codigo`, `nombre`, `kcal`, `cho_g`, `grasa_g`, `prot_g`, `empresa_id`, `activo`)
VALUES
('cer', 'Cereales', 140.00, 30.00, 1.00, 3.00, NULL, 'A'),
('ver', 'Verduras', 30.00, 5.00, 0.00, 2.00, NULL, 'A'),
('vdl', 'Verduras de libre consumo', 10.00, 2.50, 0.00, 0.00, NULL, 'A'),
('fru', 'Frutas', 65.00, 15.00, 0.00, 1.00, NULL, 'A'),
('lag', 'Lácteos altos en grasa', 110.00, 9.00, 6.00, 5.00, NULL, 'A'),
('lmg', 'Lácteos medios en grasa', 85.00, 9.00, 3.00, 5.00, NULL, 'A'),
('lbg', 'Lácteos bajos en grasa', 70.00, 10.00, 0.00, 7.00, NULL, 'A'),
('pag', 'Proteínas altas en grasa', 120.00, 1.00, 8.00, 11.00, NULL, 'A'),
('pbg', 'Proteínas bajas en grasa', 65.00, 1.00, 2.00, 11.00, NULL, 'A'),
('ls', 'Legumbres secas', 170.00, 30.00, 1.00, 11.00, NULL, 'A'),
('ayg', 'Aceite y grasas', 180.00, 0.00, 20.00, 0.00, NULL, 'A'),
('rl', 'Alimentos ricos en grasas', 175.00, 5.00, 15.00, 5.00, NULL, 'A'),
('azu', 'Azúcares', 20.00, 5.00, 0.00, 0.00, NULL, 'A')
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `kcal` = VALUES(`kcal`),
    `cho_g` = VALUES(`cho_g`),
    `grasa_g` = VALUES(`grasa_g`),
    `prot_g` = VALUES(`prot_g`);

-- Insertar actividades diarias con METs
INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
VALUES
('diaria', 'Durmiendo', 0.9, 'Sueño', 1, 'A'),
('diaria', 'Acostado despierto', 1.1, 'Reposo despierto', 2, 'A'),
('diaria', 'Sentado', 1.2, 'Actividad sentada', 3, 'A'),
('diaria', 'De pie', 1.3, 'Actividad de pie', 4, 'A'),
('diaria', 'Viendo TV', 1.0, 'Ver televisión', 5, 'A'),
('diaria', 'En clases o estudiar', 1.5, 'Estudio o clases', 6, 'A'),
('diaria', 'Trabajo of. Sentado', 1.5, 'Trabajo de oficina sentado', 7, 'A'),
('diaria', 'Trabajo of. Movimiento', 2.0, 'Trabajo de oficina con movimiento', 8, 'A'),
('diaria', 'Cocinar, instrum, o juego niño sent.', 2.0, 'Cocinar o actividades ligeras', 9, 'A'),
('diaria', 'Barrer, W de hospital, carga, actor', 3.0, 'Actividades moderadas', 10, 'A'),
('diaria', 'Planchar', 2.3, 'Planchar ropa', 11, 'A'),
('diaria', 'Lavar vajilla', 2.0, 'Lavar platos', 12, 'A'),
('diaria', 'Caminar lento', 2.0, 'Caminata lenta', 13, 'A'),
('diaria', 'Caminar Moderado', 3.5, 'Caminata moderada', 14, 'A'),
('diaria', 'Caminar Rápido', 5.5, 'Caminata rápida', 15, 'A'),
('diaria', 'Subir 1 piso o child care', 4.0, 'Subir escaleras o cuidado de niños', 16, 'A')
ON DUPLICATE KEY UPDATE
    `mets` = VALUES(`mets`),
    `descripcion` = VALUES(`descripcion`);

-- Insertar deportes/fitness con METs
INSERT INTO `actividad_met` (`tipo`, `nombre`, `mets`, `descripcion`, `orden`, `activo`)
VALUES
('deporte', 'Bicicleta < 10 mph o 16 km/h', 4.0, 'Ciclismo lento', 1, 'A'),
('deporte', 'Bicicleta 10-11,9 mph o 19km/h', 6.0, 'Ciclismo moderado', 2, 'A'),
('deporte', 'Bicicleta 12-13,9 mph o 22,2 km/h', 8.0, 'Ciclismo rápido', 3, 'A'),
('deporte', 'Bicicleta 14-15,9 mph o 25,4 km/h', 10.0, 'Ciclismo muy rápido', 4, 'A'),
('deporte', 'Bicicleta 16-19 mph o 30,4 km/h', 12.0, 'Ciclismo intenso', 5, 'A'),
('deporte', 'Trote 5 mph o 8 km/h', 8.0, 'Trote lento', 6, 'A'),
('deporte', 'Trote 7 mph o 11,2 km/h', 11.5, 'Trote moderado', 7, 'A'),
('deporte', 'Trote 9 mph o 14,4 km/h', 14.5, 'Trote rápido', 8, 'A'),
('deporte', 'Trote 11 mph o 17,6 km/h', 18.0, 'Trote muy rápido', 9, 'A'),
('deporte', 'Escalador - Eliptico', 7.0, 'Máquina escaladora o elíptica', 10, 'A'),
('deporte', 'Pesas - Máquinas (ligero)', 3.0, 'Pesas ligeras', 11, 'A'),
('deporte', 'Pesas - Máquinas (moderado)', 5.0, 'Pesas moderadas', 12, 'A'),
('deporte', 'Aeróbica', 6.0, 'Aeróbicos', 13, 'A'),
('deporte', 'Aeróbica en agua', 4.0, 'Aqua aeróbicos', 14, 'A'),
('deporte', 'Streching', 2.5, 'Estiramiento', 15, 'A'),
('deporte', 'Danza / Baile', 4.5, 'Baile', 16, 'A'),
('deporte', 'Basketball', 8.0, 'Baloncesto', 17, 'A'),
('deporte', 'Boxeo', 12.0, 'Boxeo', 18, 'A'),
('deporte', 'Escalada', 8.0, 'Escalada', 19, 'A'),
('deporte', 'Fútbol', 10.0, 'Fútbol', 20, 'A'),
('deporte', 'Natación', 10.0, 'Natación', 21, 'A'),
('deporte', 'Rafting', 5.0, 'Rafting', 22, 'A'),
('deporte', 'Entrenador', 6.0, 'Entrenamiento guiado', 23, 'A'),
('deporte', 'Otras Actividades', 5.0, 'Otras actividades deportivas', 24, 'A')
ON DUPLICATE KEY UPDATE
    `mets` = VALUES(`mets`),
    `descripcion` = VALUES(`descripcion`);

-- =====================================================
-- 4. ASIGNAR MÓDULO A PERFILES (Opcional - ajustar según necesidad)
-- =====================================================

-- Asignar a perfil Nutricionista (ajustar ID según tu sistema)
-- INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `incluido`)
-- SELECT p.id, @modulo_plan_id, 'S'
-- FROM `perfil` p
-- WHERE p.nombre LIKE '%Nutricionista%' OR p.id = 2; -- Ajustar según tu estructura

-- =====================================================
-- FIN DE MIGRACIÓN
-- =====================================================
