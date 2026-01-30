-- Opciones de card-header: por defecto, un color o gradiente (2 colores), texto
-- Ejecutar si las columnas no existen
ALTER TABLE `usuario` ADD COLUMN `card_header_por_defecto` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=estilo Bootstrap original, 0=colores personalizados';
ALTER TABLE `usuario` ADD COLUMN `card_header_es_gradiente` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=un color, 1=gradiente 2 colores';
ALTER TABLE `usuario` ADD COLUMN `color_card_header_bg2` VARCHAR(7) NULL DEFAULT NULL COMMENT 'Segundo color para gradiente (hex)';
