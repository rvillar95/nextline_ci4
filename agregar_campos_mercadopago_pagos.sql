-- =====================================================
-- AGREGAR CAMPOS PARA INTEGRACIÓN CON MERCADO PAGO
-- =====================================================
-- Campos a agregar a la tabla pagos:
-- 1. mp_preference_id - ID de la preferencia de pago de Mercado Pago
-- 2. mp_payment_id - ID del pago cuando se completa
-- 3. mp_status - Estado del pago en Mercado Pago
-- 4. detalle_agenda_id - Para vincular pagos con citas específicas
-- =====================================================

-- Verificar si los campos ya existen antes de agregarlos
SET @col_exists = 0;

-- =====================================================
-- 1. MP_PREFERENCE_ID
-- =====================================================
-- ID de la preferencia de pago creada en Mercado Pago
-- Se usa para generar el botón de pago

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_preference_id//
CREATE PROCEDURE agregar_mp_preference_id()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `pagos` 
    ADD COLUMN `mp_preference_id` VARCHAR(255) NULL COMMENT 'ID de la preferencia de pago de Mercado Pago' 
    AFTER `referencia`;
END//
DELIMITER ;

CALL agregar_mp_preference_id();
DROP PROCEDURE IF EXISTS agregar_mp_preference_id;

-- =====================================================
-- 2. MP_PAYMENT_ID
-- =====================================================
-- ID del pago cuando se completa en Mercado Pago

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_payment_id//
CREATE PROCEDURE agregar_mp_payment_id()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `pagos` 
    ADD COLUMN `mp_payment_id` VARCHAR(255) NULL COMMENT 'ID del pago en Mercado Pago cuando se completa' 
    AFTER `mp_preference_id`;
END//
DELIMITER ;

CALL agregar_mp_payment_id();
DROP PROCEDURE IF EXISTS agregar_mp_payment_id;

-- =====================================================
-- 3. MP_STATUS
-- =====================================================
-- Estado del pago en Mercado Pago (pending, approved, rejected, cancelled, refunded, charged_back)

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_status//
CREATE PROCEDURE agregar_mp_status()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `pagos` 
    ADD COLUMN `mp_status` VARCHAR(50) NULL COMMENT 'Estado del pago en Mercado Pago (pending, approved, rejected, etc.)' 
    AFTER `mp_payment_id`;
END//
DELIMITER ;

CALL agregar_mp_status();
DROP PROCEDURE IF EXISTS agregar_mp_status;

-- =====================================================
-- 4. DETALLE_AGENDA_ID
-- =====================================================
-- Para vincular pagos con citas específicas (opcional)

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_detalle_agenda_id_pagos//
CREATE PROCEDURE agregar_detalle_agenda_id_pagos()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `pagos` 
    ADD COLUMN `detalle_agenda_id` INT NULL COMMENT 'ID de la cita (detalle_agenda) asociada al pago' 
    AFTER `paquete_id`,
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
    ALTER TABLE `pagos` 
    ADD CONSTRAINT `fk_pagos_detalle_agenda` 
    FOREIGN KEY (`detalle_agenda_id`) 
    REFERENCES `detalle_agenda` (`id`) 
    ON DELETE SET NULL;
END//
DELIMITER ;

CALL agregar_fk_detalle_agenda_pago();
DROP PROCEDURE IF EXISTS agregar_fk_detalle_agenda_pago;

-- =====================================================
-- 5. ACTUALIZAR ENUM DE TIPO_PAGO
-- =====================================================
-- Agregar 'cita' como tipo de pago

-- Nota: MySQL no permite modificar ENUM fácilmente, 
-- así que esto se hará manualmente si es necesario
-- ALTER TABLE `pagos` MODIFY COLUMN `tipo_pago` ENUM('setup','mensual','anual','extra','cita') NOT NULL DEFAULT 'mensual';

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
SELECT 
    'Campos agregados exitosamente' AS estado,
    'mp_preference_id, mp_payment_id, mp_status, detalle_agenda_id' AS campos;
