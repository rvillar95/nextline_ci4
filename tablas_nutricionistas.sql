-- =====================================================
-- SISTEMA DE NUTRICIONISTAS - TABLAS PRINCIPALES
-- =====================================================
-- Este script crea las tablas necesarias para el sistema
-- de gestión de nutricionistas con agenda, pacientes,
-- documentos, historial clínico y pagos.
-- =====================================================

-- --------------------------------------------------------
-- Tabla: pacientes
-- Descripción: Almacena información de pacientes/clientes
-- Nota: Adaptada desde la tabla 'clientes' pero enfocada en salud
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nutricionista_id` int DEFAULT NULL COMMENT 'ID del usuario nutricionista que atiende',
  `tipo_paciente` enum('particular','convenio','seguro') NOT NULL DEFAULT 'particular',
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `rut_dni` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','O') DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `comuna_id` int DEFAULT NULL,
  `comuna` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `peso_inicial` decimal(5,2) DEFAULT NULL COMMENT 'Peso en kg',
  `altura` decimal(5,2) DEFAULT NULL COMMENT 'Altura en cm',
  `imc_inicial` decimal(4,2) DEFAULT NULL COMMENT 'Índice de masa corporal inicial',
  `objetivo` text COMMENT 'Objetivo del paciente',
  `alergias` text COMMENT 'Alergias conocidas',
  `medicamentos` text COMMENT 'Medicamentos actuales',
  `condiciones_medicas` text COMMENT 'Condiciones médicas preexistentes',
  `observaciones` text,
  `estado` enum('A','I') DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nutricionista` (`nutricionista_id`),
  KEY `idx_rut` (`rut_dni`),
  KEY `idx_nombre` (`nombre`,`apellido`),
  KEY `idx_pacientes_region` (`region_id`),
  KEY `idx_pacientes_comuna` (`comuna_id`),
  CONSTRAINT `fk_pacientes_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pacientes_comuna` FOREIGN KEY (`comuna_id`) REFERENCES `comunas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pacientes_usuario` FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: documentos
-- Descripción: Almacena documentos, pautas nutricionales, recetas, etc.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `documentos`;
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `nutricionista_id` int DEFAULT NULL,
  `tipo_documento` enum('pauta_nutricional','receta','informe','consentimiento','otro') NOT NULL DEFAULT 'pauta_nutricional',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `contenido` longtext COMMENT 'Contenido del documento (HTML o texto)',
  `archivo_ruta` varchar(500) DEFAULT NULL COMMENT 'Ruta al archivo PDF/Word si existe',
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `fecha_documento` date NOT NULL COMMENT 'Fecha del documento',
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'Fecha de vencimiento si aplica',
  `enviado` tinyint(1) DEFAULT '0' COMMENT 'Si fue enviado al paciente',
  `fecha_envio` datetime DEFAULT NULL,
  `metodo_envio` enum('email','whatsapp','sistema','manual') DEFAULT NULL,
  `estado` enum('A','I') DEFAULT 'A',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_nutricionista` (`nutricionista_id`),
  KEY `idx_tipo` (`tipo_documento`),
  KEY `idx_fecha` (`fecha_documento`),
  CONSTRAINT `fk_documentos_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_documentos_usuario` FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: historial_clinico
-- Descripción: Registro de consultas, evoluciones y seguimiento
-- --------------------------------------------------------
DROP TABLE IF EXISTS `historial_clinico`;
CREATE TABLE IF NOT EXISTS `historial_clinico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int NOT NULL,
  `nutricionista_id` int DEFAULT NULL,
  `agenda_id` int DEFAULT NULL COMMENT 'ID de la cita relacionada (si aplica)',
  `tipo_registro` enum('consulta','seguimiento','control','emergencia') NOT NULL DEFAULT 'consulta',
  `fecha_consulta` date NOT NULL,
  `hora_consulta` time DEFAULT NULL,
  `peso_actual` decimal(5,2) DEFAULT NULL COMMENT 'Peso en kg',
  `altura_actual` decimal(5,2) DEFAULT NULL COMMENT 'Altura en cm',
  `imc_actual` decimal(4,2) DEFAULT NULL,
  `circunferencia_cintura` decimal(5,2) DEFAULT NULL COMMENT 'En cm',
  `circunferencia_cadera` decimal(5,2) DEFAULT NULL COMMENT 'En cm',
  `grasa_corporal` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje',
  `masa_muscular` decimal(5,2) DEFAULT NULL COMMENT 'En kg',
  `motivo_consulta` text COMMENT 'Motivo principal de la consulta',
  `anamnesis` text COMMENT 'Historia clínica del paciente',
  `diagnostico` text COMMENT 'Diagnóstico nutricional',
  `plan_tratamiento` text COMMENT 'Plan de tratamiento propuesto',
  `recomendaciones` text COMMENT 'Recomendaciones nutricionales',
  `observaciones` text,
  `proxima_cita` date DEFAULT NULL,
  `estado` enum('A','I') DEFAULT 'A',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_nutricionista` (`nutricionista_id`),
  KEY `idx_agenda` (`agenda_id`),
  KEY `idx_fecha` (`fecha_consulta`),
  CONSTRAINT `fk_historial_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_historial_agenda` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: pagos
-- Descripción: Registro de pagos y transacciones de suscripciones
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID de la empresa/clínica que paga',
  `paquete_id` int DEFAULT NULL COMMENT 'ID del paquete contratado',
  `tipo_pago` enum('setup','mensual','anual','extra') NOT NULL DEFAULT 'mensual',
  `monto` decimal(10,2) NOT NULL,
  `moneda` varchar(3) DEFAULT 'CLP' COMMENT 'CLP, USD, etc.',
  `metodo_pago` enum('transferencia','tarjeta','paypal','otro') DEFAULT NULL,
  `estado_pago` enum('pendiente','procesando','completado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'Para pagos mensuales',
  `periodo_inicio` date DEFAULT NULL COMMENT 'Inicio del período pagado',
  `periodo_fin` date DEFAULT NULL COMMENT 'Fin del período pagado',
  `referencia` varchar(100) DEFAULT NULL COMMENT 'Número de referencia/transacción',
  `comprobante_ruta` varchar(500) DEFAULT NULL COMMENT 'Ruta al comprobante de pago',
  `observaciones` text,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_paquete` (`paquete_id`),
  KEY `idx_estado` (`estado_pago`),
  KEY `idx_fecha` (`fecha_pago`),
  CONSTRAINT `fk_pagos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: whatsapp_mensajes
-- Descripción: Registro de mensajes enviados/recibidos por WhatsApp
-- --------------------------------------------------------
DROP TABLE IF EXISTS `whatsapp_mensajes`;
CREATE TABLE IF NOT EXISTS `whatsapp_mensajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int DEFAULT NULL,
  `nutricionista_id` int DEFAULT NULL,
  `agenda_id` int DEFAULT NULL COMMENT 'ID de la cita relacionada',
  `tipo_mensaje` enum('agendamiento','recordatorio','confirmacion','cancelacion','documento','otro') NOT NULL DEFAULT 'agendamiento',
  `direccion` enum('enviado','recibido') NOT NULL DEFAULT 'enviado',
  `numero_destino` varchar(20) NOT NULL COMMENT 'Número de WhatsApp destino',
  `numero_origen` varchar(20) DEFAULT NULL COMMENT 'Número de WhatsApp origen',
  `mensaje` text NOT NULL,
  `mensaje_id_api` varchar(100) DEFAULT NULL COMMENT 'ID del mensaje en la API de WhatsApp',
  `estado_envio` enum('pendiente','enviado','entregado','leido','error') DEFAULT 'pendiente',
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `fecha_lectura` datetime DEFAULT NULL,
  `error_mensaje` text COMMENT 'Mensaje de error si falló',
  `metadata` json DEFAULT NULL COMMENT 'Datos adicionales en JSON',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_nutricionista` (`nutricionista_id`),
  KEY `idx_agenda` (`agenda_id`),
  KEY `idx_tipo` (`tipo_mensaje`),
  KEY `idx_estado` (`estado_envio`),
  KEY `idx_fecha` (`fecha_envio`),
  CONSTRAINT `fk_whatsapp_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_whatsapp_usuario` FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_whatsapp_agenda` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: suscripciones
-- Descripción: Control de suscripciones activas de empresas
-- --------------------------------------------------------
DROP TABLE IF EXISTS `suscripciones`;
CREATE TABLE IF NOT EXISTS `suscripciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `paquete_id` int NOT NULL,
  `estado` enum('activa','suspendida','cancelada','expirada') NOT NULL DEFAULT 'activa',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL COMMENT 'NULL = sin fecha de fin (renovación automática)',
  `fecha_proximo_pago` date DEFAULT NULL,
  `renovacion_automatica` tinyint(1) DEFAULT '1',
  `monto_mensual` decimal(10,2) NOT NULL,
  `observaciones` text,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_empresa_activa` (`empresa_id`, `estado`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_paquete` (`paquete_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_fin` (`fecha_fin`),
  CONSTRAINT `fk_suscripciones_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_suscripciones_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: agenda_paciente
-- Descripción: Relación entre agenda y pacientes (citas)
-- Nota: Esta tabla conecta detalle_agenda con pacientes
-- --------------------------------------------------------
DROP TABLE IF EXISTS `agenda_paciente`;
CREATE TABLE IF NOT EXISTS `agenda_paciente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `detalle_agenda_id` int NOT NULL,
  `paciente_id` int NOT NULL,
  `nutricionista_id` int DEFAULT NULL,
  `tipo_consulta` enum('primera_vez','control','seguimiento','emergencia') DEFAULT 'control',
  `motivo` text COMMENT 'Motivo de la consulta',
  `estado_cita` enum('agendada','confirmada','en_proceso','completada','cancelada','no_asistio') NOT NULL DEFAULT 'agendada',
  `fecha_confirmacion` datetime DEFAULT NULL,
  `fecha_cancelacion` datetime DEFAULT NULL,
  `motivo_cancelacion` text,
  `recordatorio_enviado` tinyint(1) DEFAULT '0',
  `fecha_recordatorio` datetime DEFAULT NULL,
  `observaciones` text,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_detalle_paciente` (`detalle_agenda_id`, `paciente_id`),
  KEY `idx_detalle_agenda` (`detalle_agenda_id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_nutricionista` (`nutricionista_id`),
  KEY `idx_estado` (`estado_cita`),
  CONSTRAINT `fk_agenda_paciente_detalle` FOREIGN KEY (`detalle_agenda_id`) REFERENCES `detalle_agenda` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agenda_paciente_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agenda_paciente_usuario` FOREIGN KEY (`nutricionista_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
-- Notas:
-- 1. Las tablas están relacionadas con las existentes:
--    - pacientes -> usuario (nutricionista_id)
--    - pacientes -> regiones, comunas
--    - documentos -> pacientes
--    - historial_clinico -> pacientes, agenda
--    - pagos -> empresa, paquetes
--    - whatsapp_mensajes -> pacientes, agenda
--    - suscripciones -> empresa, paquetes
--    - agenda_paciente -> detalle_agenda, pacientes
--
-- 2. Próximos pasos:
--    - Crear modelos para cada tabla
--    - Crear controladores
--    - Crear vistas con diseño moderno
--    - Integrar WhatsApp API
-- =====================================================
