-- =====================================================
-- CREAR TIPO DE AGENDA
-- =====================================================
-- Este script crea el tipo de agenda básico si no existe
-- =====================================================

-- Verificar si la tabla tipo_agenda existe y tiene datos
-- SELECT * FROM tipo_agenda;

-- Insertar tipo de agenda básico si no existe
INSERT INTO `tipo_agenda` (`id`, `nombre`) 
VALUES (1, 'Consulta Normal')
ON DUPLICATE KEY UPDATE `nombre` = 'Consulta Normal';

-- Verificar que se insertó correctamente
-- SELECT * FROM tipo_agenda WHERE id = 1;
