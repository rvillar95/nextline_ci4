-- Opciones de main-header (título del módulo): por defecto, un color o gradiente (2 colores), texto
-- Ejecutar si las columnas no existen
ALTER TABLE `usuario` ADD COLUMN `main_header_por_defecto` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=estilo original (gradiente azul-cyan), 0=colores personalizados';
ALTER TABLE `usuario` ADD COLUMN `main_header_es_gradiente` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=un color, 1=gradiente 2 colores';
ALTER TABLE `usuario` ADD COLUMN `color_main_header_bg` VARCHAR(7) NULL DEFAULT '#4A90E2' COMMENT 'Fondo main-header (hex)';
ALTER TABLE `usuario` ADD COLUMN `color_main_header_bg2` VARCHAR(7) NULL DEFAULT NULL COMMENT 'Segundo color para gradiente (hex)';
ALTER TABLE `usuario` ADD COLUMN `color_main_header_text` VARCHAR(7) NULL DEFAULT '#ffffff' COMMENT 'Texto main-header (hex)';
