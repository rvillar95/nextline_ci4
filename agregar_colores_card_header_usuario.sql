-- Colores de encabezados de tarjetas (card-header) por usuario
-- Ejecutar si las columnas no existen
ALTER TABLE `usuario` ADD COLUMN `color_card_header_bg` VARCHAR(7) NULL DEFAULT '#6c757d' COMMENT 'Fondo del card-header (hex)';
ALTER TABLE `usuario` ADD COLUMN `color_card_header_text` VARCHAR(7) NULL DEFAULT '#ffffff' COMMENT 'Texto del card-header (hex)';
