-- =====================================================
-- ACTUALIZAR FLUJO DE ESTADOS DE CITA
-- =====================================================
-- Este script actualiza el ENUM de estado_cita para
-- incluir todos los estados necesarios según el flujo:
-- 1. Disponible (NULL o sin paciente)
-- 2. en_proceso (al agendar con botón de pago)
-- 3. pendiente (al confirmar desde email)
-- 4. agendada (al completar el pago)
-- =====================================================

-- Verificar estructura actual
DESCRIBE `detalle_agenda`;

-- Modificar el ENUM para incluir todos los estados
ALTER TABLE `detalle_agenda`
MODIFY COLUMN `estado_cita` ENUM('pendiente','agendada','confirmada','en_proceso','completada','cancelada','no_asistio') NULL DEFAULT NULL;

-- =====================================================
-- NOTAS SOBRE EL FLUJO:
-- =====================================================
-- 1. Estado inicial: NULL (disponible, sin paciente)
-- 2. Al agendar con pago: 'en_proceso' (nutricionista selecciona paciente + hora + botón de pago)
-- 3. Al confirmar desde email: 'pendiente' (paciente confirma desde el correo)
-- 4. Al pagar: 'agendada' (webhook de Mercado Pago actualiza cuando se completa el pago)
-- 5. Otros estados: 'confirmada', 'en_proceso', 'completada', 'cancelada', 'no_asistio'
-- =====================================================
