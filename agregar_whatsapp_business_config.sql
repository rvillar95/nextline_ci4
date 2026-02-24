-- Configuración de WhatsApp / WhatsApp Business por empresa
-- Permite elegir proveedor (twilio / whatsapp_business) y guardar credenciales de WhatsApp Business API
ALTER TABLE `empresa_configuraciones` ADD COLUMN `whatsapp_provider` VARCHAR(30) NULL DEFAULT NULL COMMENT 'twilio o whatsapp_business';
ALTER TABLE `empresa_configuraciones` ADD COLUMN `whatsapp_access_token` TEXT NULL DEFAULT NULL COMMENT 'Access Token de WhatsApp Business API';
ALTER TABLE `empresa_configuraciones` ADD COLUMN `whatsapp_phone_number_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Phone Number ID de Meta';
ALTER TABLE `empresa_configuraciones` ADD COLUMN `whatsapp_business_account_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Business Account ID (opcional)';
ALTER TABLE `empresa_configuraciones` ADD COLUMN `whatsapp_verify_token` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Token para verificación del webhook';
