-- =====================================================
-- CREAR PERFIL BASE "PACIENTE"
-- =====================================================
-- Este script crea un perfil base "Paciente" que puede ser
-- usado como plantilla para crear perfiles de paciente por empresa
-- =====================================================

-- Perfil base "Paciente" (empresa_id = NULL para que sea plantilla)
-- Este perfil tendrá permisos limitados: solo ver sus propios datos
INSERT INTO `perfil` (`nombre`, `poder`, `estado`, `empresa_id`, `fcreacion`, `factualizacion`, `feliminacion`) 
VALUES ('Paciente', 0, 'A', NULL, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE `nombre` = 'Paciente';

-- Obtener el ID del perfil Paciente (ajustar según el ID que se asigne)
-- SELECT id FROM perfil WHERE nombre = 'Paciente' AND empresa_id IS NULL LIMIT 1;

-- Nota: Los módulos para este perfil se asignarán cuando se cree el perfil por empresa
-- usando la función de "copiar perfil base" o asignación manual
