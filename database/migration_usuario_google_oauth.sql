-- =============================================================================
-- OAuth Google para login en tabla `usuario`
-- =============================================================================
-- Regla de acceso: (clave IS NOT NULL) OR (oauth_google_sub IS NOT NULL)
--
-- NOTA: El sistema actual usa login con clave requerida.
-- Este ALTER permite cuentas solo Google dejando clave NULL.
-- =============================================================================

ALTER TABLE `usuario`
  MODIFY `clave` VARCHAR(255) NULL COMMENT 'password_hash; NULL si solo OAuth Google';

ALTER TABLE `usuario`
  ADD COLUMN `oauth_google_sub` VARCHAR(255) NULL COMMENT 'Subject (sub) de Google OAuth' AFTER `correo`,
  ADD COLUMN `oauth_google_email` VARCHAR(150) NULL COMMENT 'Email devuelto por Google al vincular' AFTER `oauth_google_sub`,
  ADD COLUMN `oauth_vinculado_en` DATETIME NULL AFTER `oauth_google_email`;

ALTER TABLE `usuario`
  ADD UNIQUE KEY `uq_usuario_oauth_google_sub` (`oauth_google_sub`);

