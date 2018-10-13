--
-- Update Coordinator Framework
--
-- Manual updates
--

-- --------------------------------------------------------

ALTER TABLE  `framework__users` CHANGE  `pwdTimestamp`  `pwdTimestamp` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'password change';
ALTER TABLE  `framework__users` ADD  `lsaTimestamp` INT( 11 ) UNSIGNED NOT NULL COMMENT  'last system access' AFTER  `updFkUser`;

-- --------------------------------------------------------

ALTER TABLE `framework__menus` ADD `authorization` VARCHAR(256) NULL AFTER `target`;

-- --------------------------------------------------------