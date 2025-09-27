-- Agregar campo slug a las tablas de categorías
ALTER TABLE `servicio_categoria` ADD COLUMN `slug` VARCHAR(255) NULL AFTER `nombre`;
ALTER TABLE `galeria_categoria` ADD COLUMN `slug` VARCHAR(255) NULL AFTER `nombre`;

-- Generar slugs para categorías existentes
UPDATE `servicio_categoria` SET `slug` = 'construccion-residencial' WHERE `nombre` = 'Construcción Residencial';
UPDATE `servicio_categoria` SET `slug` = 'construccion-comercial' WHERE `nombre` = 'Construcción Comercial';
UPDATE `servicio_categoria` SET `slug` = 'remodelaciones' WHERE `nombre` = 'Remodelaciones';
UPDATE `servicio_categoria` SET `slug` = 'ampliaciones' WHERE `nombre` = 'Ampliaciones';
UPDATE `servicio_categoria` SET `slug` = 'obras-menores' WHERE `nombre` = 'Obras Menores';

UPDATE `galeria_categoria` SET `slug` = 'casas' WHERE `nombre` = 'Casas';
UPDATE `galeria_categoria` SET `slug` = 'edificios' WHERE `nombre` = 'Edificios';
UPDATE `galeria_categoria` SET `slug` = 'ampliaciones' WHERE `nombre` = 'Ampliaciones';
UPDATE `galeria_categoria` SET `slug` = 'remodelaciones' WHERE `nombre` = 'Remodelaciones';
UPDATE `galeria_categoria` SET `slug` = 'quinchos' WHERE `nombre` = 'Quinchos';

-- Agregar campos SEO a las categorías
ALTER TABLE `servicio_categoria` ADD COLUMN `meta_titulo` VARCHAR(255) NULL AFTER `slug`;
ALTER TABLE `servicio_categoria` ADD COLUMN `meta_descripcion` VARCHAR(500) NULL AFTER `meta_titulo`;
ALTER TABLE `servicio_categoria` ADD COLUMN `meta_keywords` VARCHAR(500) NULL AFTER `meta_descripcion`;

ALTER TABLE `galeria_categoria` ADD COLUMN `meta_titulo` VARCHAR(255) NULL AFTER `slug`;
ALTER TABLE `galeria_categoria` ADD COLUMN `meta_descripcion` VARCHAR(500) NULL AFTER `meta_titulo`;
ALTER TABLE `galeria_categoria` ADD COLUMN `meta_keywords` VARCHAR(500) NULL AFTER `meta_descripcion`;
