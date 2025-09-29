-- Script para agregar el campo subtotal_sin_iva a la tabla cotizaciones
-- Este campo almacenará el total antes de aplicar el IVA

ALTER TABLE `cotizaciones`
    ADD COLUMN `subtotal_sin_iva` DECIMAL(15,2) DEFAULT 0.00 AFTER `descuento_monto`;

-- Comentario del campo
ALTER TABLE `cotizaciones`
    MODIFY COLUMN `subtotal_sin_iva` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Subtotal antes de aplicar IVA';
