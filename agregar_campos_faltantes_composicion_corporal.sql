-- =====================================================
-- AGREGAR CAMPOS FALTANTES PARA MÉTODOS DE COMPOSICIÓN CORPORAL
-- =====================================================
-- Este script agrega los campos faltantes necesarios para:
-- - Método de 5 componentes: circunferencia_cabeza, diametro_torax_transverso, diametro_torax_anteroposterior
-- =====================================================
-- NOTA: Si algún campo ya existe, se mostrará un error que puedes ignorar
-- =====================================================

-- Agregar circunferencia_cabeza (necesaria para 5 componentes)
-- Si el campo ya existe, se mostrará un error que puedes ignorar
ALTER TABLE `historial_clinico` 
ADD COLUMN `circunferencia_cabeza` DECIMAL(5,2) NULL COMMENT 'Circunferencia cabeza en cm - necesaria para método 5 componentes' 
AFTER `circunferencia_torax`;

-- Agregar diametro_torax_transverso (necesario para 5 componentes)
-- Si el campo ya existe, se mostrará un error que puedes ignorar
ALTER TABLE `historial_clinico` 
ADD COLUMN `diametro_torax_transverso` DECIMAL(5,2) NULL COMMENT 'Diámetro tórax transverso en cm - necesario para método 5 componentes' 
AFTER `diametro_bi_iliocristal`;

-- Agregar diametro_torax_anteroposterior (necesario para 5 componentes)
-- Si el campo ya existe, se mostrará un error que puedes ignorar
ALTER TABLE `historial_clinico` 
ADD COLUMN `diametro_torax_anteroposterior` DECIMAL(5,2) NULL COMMENT 'Diámetro tórax anteroposterior en cm - necesario para método 5 componentes' 
AFTER `diametro_torax_transverso`;

-- =====================================================
-- VERIFICACIÓN (usando SHOW COLUMNS en lugar de INFORMATION_SCHEMA)
-- =====================================================
SHOW COLUMNS FROM `historial_clinico` 
WHERE Field IN ('circunferencia_cabeza', 'diametro_torax_transverso', 'diametro_torax_anteroposterior');
