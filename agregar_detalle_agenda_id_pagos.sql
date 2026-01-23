-- =====================================================
-- AGREGAR COLUMNA detalle_agenda_id A TABLA pagos
-- =====================================================
-- Para vincular pagos con citas específicas (opcional)
-- =====================================================

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_detalle_agenda_id_pagos//
CREATE PROCEDURE agregar_detalle_agenda_id_pagos()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    DECLARE CONTINUE HANDLER FOR 1061 BEGIN END; -- Error: Duplicate key name
    
    ALTER TABLE `pagos` 
    ADD COLUMN `detalle_agenda_id` INT NULL COMMENT 'ID de la cita (detalle_agenda) asociada al pago' 
    AFTER `paquete_id`;
    
    -- Agregar índice si no existe
    ALTER TABLE `pagos` 
    ADD INDEX `idx_detalle_agenda_pago` (`detalle_agenda_id`);
END//
DELIMITER ;

CALL agregar_detalle_agenda_id_pagos();
DROP PROCEDURE IF EXISTS agregar_detalle_agenda_id_pagos;

-- Agregar foreign key si no existe
DELIMITER //
DROP PROCEDURE IF EXISTS agregar_fk_detalle_agenda_pago//
CREATE PROCEDURE agregar_fk_detalle_agenda_pago()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1022 BEGIN END; -- Error: Duplicate foreign key name
    DECLARE CONTINUE HANDLER FOR 1215 BEGIN END; -- Error: Cannot add foreign key constraint
    
    ALTER TABLE `pagos` 
    ADD CONSTRAINT `fk_pagos_detalle_agenda` 
    FOREIGN KEY (`detalle_agenda_id`) 
    REFERENCES `detalle_agenda` (`id`) 
    ON DELETE SET NULL;
END//
DELIMITER ;

CALL agregar_fk_detalle_agenda_pago();
DROP PROCEDURE IF EXISTS agregar_fk_detalle_agenda_pago;

-- Verificar que se agregó correctamente (sin usar INFORMATION_SCHEMA)
-- Ejecuta manualmente: DESCRIBE pagos; para verificar la columna
