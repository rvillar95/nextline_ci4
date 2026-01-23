-- =====================================================
-- AGREGAR CAMPOS DE MERCADO PAGO A empresa_configuraciones
-- =====================================================
-- Cada empresa tendrá sus propias credenciales de Mercado Pago
-- =====================================================

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_access_token//
CREATE PROCEDURE agregar_mp_access_token()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_access_token` VARCHAR(500) NULL COMMENT 'Access Token de Mercado Pago para esta empresa' 
    AFTER `mensaje_cancelacion_en_proceso`;
END//
DELIMITER ;

CALL agregar_mp_access_token();
DROP PROCEDURE IF EXISTS agregar_mp_access_token;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_public_key//
CREATE PROCEDURE agregar_mp_public_key()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_public_key` VARCHAR(500) NULL COMMENT 'Public Key de Mercado Pago para esta empresa' 
    AFTER `mp_access_token`;
END//
DELIMITER ;

CALL agregar_mp_public_key();
DROP PROCEDURE IF EXISTS agregar_mp_public_key;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_mode//
CREATE PROCEDURE agregar_mp_mode()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_mode` ENUM('sandbox', 'production') DEFAULT 'sandbox' COMMENT 'Modo de Mercado Pago: sandbox o production' 
    AFTER `mp_public_key`;
END//
DELIMITER ;

CALL agregar_mp_mode();
DROP PROCEDURE IF EXISTS agregar_mp_mode;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_habilitado//
CREATE PROCEDURE agregar_mp_habilitado()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_habilitado` TINYINT(1) DEFAULT 0 COMMENT '1 = Mercado Pago habilitado para esta empresa, 0 = deshabilitado' 
    AFTER `mp_mode`;
END//
DELIMITER ;

CALL agregar_mp_habilitado();
DROP PROCEDURE IF EXISTS agregar_mp_habilitado;

-- Verificación
SELECT 
    'Campos de Mercado Pago agregados exitosamente' AS estado,
    'mp_access_token, mp_public_key, mp_mode, mp_habilitado' AS campos;
