-- Corregir restricción de clave foránea para permitir eliminación en cascada
-- Primero eliminar la restricción existente
ALTER TABLE `modulo_detalle` DROP FOREIGN KEY `modulo_detalle_ibfk_1`;

-- Agregar la nueva restricción con CASCADE
ALTER TABLE `modulo_detalle` 
ADD CONSTRAINT `modulo_detalle_ibfk_1` 
FOREIGN KEY (`modulo_id`) REFERENCES `modulo`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

-- También corregir la tabla perfil_modulo si existe la misma restricción
ALTER TABLE `perfil_modulo` DROP FOREIGN KEY IF EXISTS `perfil_modulo_ibfk_1`;
ALTER TABLE `perfil_modulo` 
ADD CONSTRAINT `perfil_modulo_ibfk_1` 
FOREIGN KEY (`modulo_id`) REFERENCES `modulo`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;
