--
-- Update Coordinator Framework
--
-- Aggiornamenti da fare manualmente
--

-- --------------------------------------------------------

ALTER TABLE  `framework_users` CHANGE  `pwdTimestamp`  `pwdTimestamp` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'password change';
ALTER TABLE  `framework_users` ADD  `lsaTimestamp` INT( 11 ) UNSIGNED NOT NULL COMMENT  'last system access' AFTER  `updFkUser`;

-- --------------------------------------------------------
