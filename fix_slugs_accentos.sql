-- Script para corregir slugs con problemas de acentos
-- Actualizar slugs existentes en servicio_categoria

UPDATE `servicio_categoria` SET `slug` = 'construccion-residencial' WHERE `slug` = 'construcci-n-residencial';
UPDATE `servicio_categoria` SET `slug` = 'construccion-comercial' WHERE `slug` = 'construcci-n-comercial';
UPDATE `servicio_categoria` SET `slug` = 'remodelaciones' WHERE `slug` = 'remodelaci-nes';
UPDATE `servicio_categoria` SET `slug` = 'ampliaciones' WHERE `slug` = 'ampliaci-nes';
UPDATE `servicio_categoria` SET `slug` = 'reparaciones' WHERE `slug` = 'reparaci-nes';

-- Actualizar slugs existentes en galeria_categoria
UPDATE `galeria_categoria` SET `slug` = 'construccion-residencial' WHERE `slug` = 'construcci-n-residencial';
UPDATE `galeria_categoria` SET `slug` = 'construccion-comercial' WHERE `slug` = 'construcci-n-comercial';
UPDATE `galeria_categoria` SET `slug` = 'remodelaciones' WHERE `slug` = 'remodelaci-nes';
UPDATE `galeria_categoria` SET `slug` = 'ampliaciones' WHERE `slug` = 'ampliaci-nes';
UPDATE `galeria_categoria` SET `slug` = 'quinchos' WHERE `slug` = 'quinchos';

-- Verificar que no haya duplicados después de la corrección
-- Si hay duplicados, agregar sufijo numérico
UPDATE `servicio_categoria` SET `slug` = 'construccion-residencial-1' WHERE `slug` = 'construccion-residencial' AND `id` != (SELECT MIN(`id`) FROM `servicio_categoria` WHERE `slug` = 'construccion-residencial');
UPDATE `galeria_categoria` SET `slug` = 'construccion-residencial-1' WHERE `slug` = 'construccion-residencial' AND `id` != (SELECT MIN(`id`) FROM `galeria_categoria` WHERE `slug` = 'construccion-residencial');
