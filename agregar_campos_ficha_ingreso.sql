-- =====================================================
-- Ficha de Ingreso Adulto: campos en historial_clinico
-- y tabla de exámenes bioquímicos
-- =====================================================
-- Ejecutar después de revisar docs/DONDE_GUARDAR_FICHA_INGRESO.md
-- =====================================================

-- 1) Historial clínico: anamnesis clínica (tabaco, alcohol, drogas, medicamentos, sueño, etc.)
ALTER TABLE `historial_clinico`
ADD COLUMN `anamnesis_clinica` TEXT NULL COMMENT 'Anamnesis clínica: tabaco, alcohol, drogas, enfermedad base/RCV, signos/síntomas, tránsito intestinal, medicamentos, suplementos, ingesta hídrica, actividad física, sueño' AFTER `anamnesis`;

-- 2) Historial clínico: anamnesis alimentaria (relación familiar, apetito, dieta restrictiva, ansiedad)
ALTER TABLE `historial_clinico`
ADD COLUMN `anamnesis_alimentaria` TEXT NULL COMMENT 'Anamnesis alimentaria: relación familiar, apetito, dieta restrictiva, ansiedad con/sin comida' AFTER `anamnesis_clinica`;

-- 3) Historial clínico: tendencia de consumo (preferencia y alergias por grupo de alimentos, JSON)
ALTER TABLE `historial_clinico`
ADD COLUMN `tendencia_consumo` TEXT NULL COMMENT 'JSON: [{grupo, preferencia, alergia_intolerancia}, ...] por grupo de alimentos' AFTER `anamnesis_alimentaria`;

-- 4) Historial clínico: recordatorio 24 h (desayuno, colación, almuerzo, once, cena)
ALTER TABLE `historial_clinico`
ADD COLUMN `recordatorio_24h` TEXT NULL COMMENT 'Recordatorio 24h: texto libre o JSON con comidas y horarios' AFTER `tendencia_consumo`;

-- 5) Tabla de exámenes bioquímicos por historial (una fila por examen)
CREATE TABLE IF NOT EXISTS `historial_examen_bioquimico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `historial_clinico_id` int NOT NULL,
  `nombre` varchar(150) DEFAULT NULL COMMENT 'Nombre del examen (ej. Glicemia, Hemoglobina)',
  `valor` varchar(100) DEFAULT NULL COMMENT 'Valor del resultado',
  `fecha_interpretacion` varchar(255) DEFAULT NULL COMMENT 'Fecha y/o interpretación en texto',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_historial_examen` (`historial_clinico_id`),
  CONSTRAINT `fk_examen_historial` FOREIGN KEY (`historial_clinico_id`) REFERENCES `historial_clinico` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Exámenes bioquímicos por consulta/historial';

-- 6) Opcional: ocupación en paciente (como en la ficha Excel)
ALTER TABLE `pacientes`
ADD COLUMN `ocupacion` varchar(100) DEFAULT NULL COMMENT 'Ocupación del paciente' AFTER `email`;
