-- =====================================================
-- Tendencia de consumo: tabla normalizada por grupo
-- (más fácil de consultar y procesar después)
-- =====================================================
-- Ejecutar en la misma BD del proyecto.
-- =====================================================

CREATE TABLE IF NOT EXISTS `historial_tendencia_consumo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `historial_clinico_id` int NOT NULL,
  `grupo` varchar(50) NOT NULL COMMENT 'Slug del grupo: pan, cereales_almuerzo, verduras, etc.',
  `preferencia` varchar(255) DEFAULT NULL COMMENT 'Preferencia alimentaria para este grupo',
  `alergia_intolerancia` varchar(255) DEFAULT NULL COMMENT 'Alergia o intolerancia para este grupo',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_historial_grupo` (`historial_clinico_id`, `grupo`),
  KEY `idx_historial_tendencia` (`historial_clinico_id`),
  KEY `idx_grupo` (`grupo`),
  CONSTRAINT `fk_tendencia_historial` FOREIGN KEY (`historial_clinico_id`) REFERENCES `historial_clinico` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tendencia de consumo por grupo de alimentos (por consulta/historial)';
