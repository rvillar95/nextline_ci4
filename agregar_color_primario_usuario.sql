-- Color de acento del usuario (Mi perfil)
-- Ejecutar si la columna no existe
ALTER TABLE `usuario` ADD COLUMN `color_primario` VARCHAR(7) NULL DEFAULT '#6aff99' COMMENT 'Color de acento en hex (ej: #6aff99)';
