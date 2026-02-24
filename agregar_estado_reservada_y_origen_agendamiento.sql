-- =====================================================
-- RESERVA POR PACIENTE: estado 'reservada' y origen_agendamiento
-- =====================================================
-- 1. Añade el estado 'reservada' al ENUM estado_cita
-- 2. Añade la columna origen_agendamiento
--    Si origen_agendamiento ya existe, omitir el segundo ALTER (saldrá error "Duplicate column").
-- =====================================================

-- 1. Incluir 'reservada' en el ENUM de estado_cita
ALTER TABLE `detalle_agenda`
MODIFY COLUMN `estado_cita` ENUM(
  'pendiente',
  'reservada',
  'agendada',
  'confirmada',
  'en_proceso',
  'completada',
  'cancelada',
  'no_asistio'
) NULL DEFAULT NULL;

-- 2. Añadir columna origen_agendamiento
-- Si ya existe la columna, este ALTER fallará con "Duplicate column name 'origen_agendamiento'"; en ese caso no hagas nada.
ALTER TABLE `detalle_agenda`
ADD COLUMN `origen_agendamiento` ENUM('paciente','nutricionista') NULL DEFAULT 'nutricionista'
COMMENT 'Quién tomó la hora: paciente (link público) o nutricionista (dashboard)';
