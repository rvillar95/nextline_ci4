-- Mensaje configurable para email "Reserva recibida" (paciente reservó desde link público).
-- Variables: [NOMBRE_PACIENTE], [FECHA], [HORA], [NOMBRE_NUTRICIONISTA]
-- Si la columna ya existe, omitir este ALTER.

ALTER TABLE `empresa_configuraciones`
ADD COLUMN `mensaje_reserva_recibida` TEXT NULL
COMMENT 'Mensaje para email cuando el paciente reserva desde link público (pendiente de aprobación)';
