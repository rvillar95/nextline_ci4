-- =====================================================
-- Permitir guardar fecha + hora en "Próxima cita recomendada"
-- =====================================================
-- La columna pasa de DATE a VARCHAR(50) para almacenar
-- solo fecha (DD-MM-YYYY) o fecha y hora (DD-MM-YYYY HH:mm - HH:mm).
-- Valores existentes en DATE se convertirán a string (YYYY-MM-DD).
-- =====================================================

ALTER TABLE `detalle_agenda`
MODIFY COLUMN `proxima_cita_recomendada` VARCHAR(50) NULL
COMMENT 'Fecha recomendada (DD-MM-YYYY) o fecha y hora (DD-MM-YYYY HH:mm - HH:mm)';
