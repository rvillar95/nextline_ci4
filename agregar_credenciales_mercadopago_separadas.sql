-- =====================================================
-- AGREGAR CAMPOS SEPARADOS PARA CREDENCIALES SANDBOX Y PRODUCTION
-- =====================================================
-- Permite guardar credenciales de sandbox y production por separado
-- y cambiar entre ellas solo cambiando el modo
-- =====================================================

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_access_token_sandbox//
CREATE PROCEDURE agregar_mp_access_token_sandbox()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END; -- Error: Duplicate column name
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_access_token_sandbox` VARCHAR(500) NULL COMMENT 'Access Token de Mercado Pago SANDBOX para esta empresa' 
    AFTER `mp_habilitado`;
END//
DELIMITER ;

CALL agregar_mp_access_token_sandbox();
DROP PROCEDURE IF EXISTS agregar_mp_access_token_sandbox;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_public_key_sandbox//
CREATE PROCEDURE agregar_mp_public_key_sandbox()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END;
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_public_key_sandbox` VARCHAR(500) NULL COMMENT 'Public Key de Mercado Pago SANDBOX para esta empresa' 
    AFTER `mp_access_token_sandbox`;
END//
DELIMITER ;

CALL agregar_mp_public_key_sandbox();
DROP PROCEDURE IF EXISTS agregar_mp_public_key_sandbox;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_access_token_production//
CREATE PROCEDURE agregar_mp_access_token_production()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END;
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_access_token_production` VARCHAR(500) NULL COMMENT 'Access Token de Mercado Pago PRODUCTION para esta empresa' 
    AFTER `mp_public_key_sandbox`;
END//
DELIMITER ;

CALL agregar_mp_access_token_production();
DROP PROCEDURE IF EXISTS agregar_mp_access_token_production;

DELIMITER //
DROP PROCEDURE IF EXISTS agregar_mp_public_key_production//
CREATE PROCEDURE agregar_mp_public_key_production()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060 BEGIN END;
    ALTER TABLE `empresa_configuraciones` 
    ADD COLUMN `mp_public_key_production` VARCHAR(500) NULL COMMENT 'Public Key de Mercado Pago PRODUCTION para esta empresa' 
    AFTER `mp_access_token_production`;
END//
DELIMITER ;

CALL agregar_mp_public_key_production();
DROP PROCEDURE IF EXISTS agregar_mp_public_key_production;

-- Migrar datos existentes a los nuevos campos según el modo actual
-- Si el modo es 'sandbox', copiar a campos sandbox
-- Si el modo es 'production', copiar a campos production
UPDATE `empresa_configuraciones`
SET 
    `mp_access_token_sandbox` = CASE 
        WHEN `mp_mode` = 'sandbox' THEN `mp_access_token`
        ELSE `mp_access_token_sandbox`
    END,
    `mp_public_key_sandbox` = CASE 
        WHEN `mp_mode` = 'sandbox' THEN `mp_public_key`
        ELSE `mp_public_key_sandbox`
    END,
    `mp_access_token_production` = CASE 
        WHEN `mp_mode` = 'production' THEN `mp_access_token`
        ELSE `mp_access_token_production`
    END,
    `mp_public_key_production` = CASE 
        WHEN `mp_mode` = 'production' THEN `mp_public_key`
        ELSE `mp_public_key_production`
    END
WHERE `mp_access_token` IS NOT NULL;

-- Verificación
SELECT 
    'Campos de Mercado Pago separados agregados exitosamente' AS estado,
    'mp_access_token_sandbox, mp_public_key_sandbox, mp_access_token_production, mp_public_key_production' AS campos_nuevos,
    'Los datos existentes han sido migrados según el modo actual' AS nota;
