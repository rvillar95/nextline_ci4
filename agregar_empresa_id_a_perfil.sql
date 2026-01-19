-- =====================================================
-- AGREGAR EMPRESA_ID A PERFIL
-- =====================================================
-- Este script agrega el campo empresa_id a la tabla perfil
-- para que los perfiles sean por empresa (multi-tenant)
-- =====================================================

-- 1. Agregar campo empresa_id a la tabla perfil
ALTER TABLE `perfil` 
ADD COLUMN `empresa_id` INT NULL DEFAULT NULL AFTER `poder`,
ADD INDEX `idx_empresa_id` (`empresa_id`),
ADD CONSTRAINT `fk_perfil_empresa` 
    FOREIGN KEY (`empresa_id`) 
    REFERENCES `empresa` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;

-- 2. Actualizar perfiles existentes:
--    - Super Admin (poder=3): empresa_id = NULL (global)
--    - Otros perfiles: se asignarán cuando se creen por empresa

-- 3. Crear perfil predefinido "Paciente" para cada empresa existente
--    (Este perfil se creará automáticamente cuando se cree una empresa nueva)
--    Por ahora, creamos uno global que se puede copiar por empresa

-- Nota: Los perfiles con empresa_id = NULL son perfiles globales del sistema
--       Los perfiles con empresa_id != NULL son perfiles específicos de cada empresa
