--
-- Setup
--
-- @package Coordinator\Queries
-- @author  Manuel Zavatta <manuel.zavatta@gmail.com>
-- @link    http://www.zavynet.org
--
-- Version 1.0.0
--

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `framework__settings`
--

CREATE TABLE IF NOT EXISTS `framework__settings` (
  `setting` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__settings`
--

INSERT INTO `framework__settings` (`setting`, `value`) VALUES
('maintenance', '0'),
('owner', 'Company name'),
('title', 'Coordinator Framework'),
('show', 'logo_title'),
('sessions_authentication_method', 'standard'),
('sessions_idle_timeout', '14400'),
('sessions_ldap_cache', '0'),
('sessions_ldap_dn', ''),
('sessions_ldap_domain', ''),
('sessions_ldap_groups', ''),
('sessions_ldap_hostname', ''),
('sessions_ldap_userfield', ''),
('sessions_multiple', '1'),
('users_level_max', '9'),
('users_password_expiration', '-1'),
('sendmail_asynchronous', '0'),
('sendmail_from_mail', 'company@domain.tdl'),
('sendmail_from_name', 'Coordinator'),
('sendmail_method', 'standard'),
('sendmail_smtp_encryption', ''),
('sendmail_smtp_host', ''),
('sendmail_smtp_hostname', ''),
('sendmail_smtp_password', ''),
('sendmail_smtp_username', ''),
('token_cron', '');
('token_gtag', '');

-- --------------------------------------------------------

--
-- Table structure for table `framework__sessions`
--

CREATE TABLE IF NOT EXISTS `framework__sessions` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fkUser` int(11) unsigned NOT NULL,
  `ipAddress` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `startTimestamp` int(11) unsigned NOT NULL,
  `lastTimestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkUser` (`fkUser`),
  CONSTRAINT `framework__sessions_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__menus`
--

CREATE TABLE IF NOT EXISTS `framework__menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkMenu` int(11) unsigned DEFAULT NULL,
  `order` int(11) unsigned NOT NULL,
  `icon` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label_localizations` text COLLATE utf8_unicode_ci NOT NULL,
  `title_localizations` text COLLATE utf8_unicode_ci,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `script` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tab` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorization` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkMenu` (`fkMenu`),
  CONSTRAINT `framework__menus_ibfk_1` FOREIGN KEY (`fkMenu`) REFERENCES `framework__menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__groups`
--

CREATE TABLE IF NOT EXISTS `framework__groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkGroup` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fkGroup` (`fkGroup`),
  CONSTRAINT `framework__groups_ibfk_1` FOREIGN KEY (`fkGroup`) REFERENCES `framework__groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__groups`
--

INSERT INTO `framework__groups` (`id`, `fkGroup`, `name`, `description`, `addTimestamp`, `addFkUser`, `updTimestamp`, `updFkUser`, `deleted`) VALUES
(1, NULL, 'Administrators', 'Coordinator Administrators', 0, 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `framework__users`
--

CREATE TABLE IF NOT EXISTS `framework__users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `localization` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'man, woman',
  `birthday` date DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `superuser` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(2) unsigned NOT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  `lsaTimestamp` int(11) unsigned DEFAULT NULL COMMENT 'last system access',
  `pwdTimestamp` int(11) unsigned DEFAULT NULL COMMENT 'last password change',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__users`
--

INSERT INTO `framework__users` (`id`, `mail`, `firstname`, `lastname`, `localization`, `timezone`, `password`,`enabled`, `superuser`, `level`, `addTimestamp`, `addFkUser`) VALUES
(1, 'you@domain.tdl', 'Administrator', 'Coordinator', 'en_EN', 'Europe/London', '5f4dcc3b5aa765d61d8327deb882cf99', 1, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `framework__users__groups`
--

CREATE TABLE IF NOT EXISTS `framework__users__groups` (
  `fkUser` int(11) unsigned NOT NULL,
  `fkGroup` int(11) unsigned NOT NULL,
  `main` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`fkUser`,`fkGroup`),
  KEY `fkUser` (`fkUser`),
  KEY `fkGroup` (`fkGroup`),
  CONSTRAINT `framework__users__groups_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `framework__users__groups_ibfk_2` FOREIGN KEY (`fkGroup`) REFERENCES `framework__groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__users__groups`
--

INSERT INTO `framework__users__groups` (`fkUser`, `fkGroup`, `main`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `framework__users__parameters`
--

CREATE TABLE IF NOT EXISTS `framework__users__parameters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkUser` int(11) unsigned NOT NULL,
  `parameter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkUser` (`fkUser`),
  CONSTRAINT `framework__users__parameters_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__users__dashboards`
--

CREATE TABLE IF NOT EXISTS `framework__users__dashboards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fkUser` int(11) unsigned NOT NULL,
  `order` int(11) unsigned NOT NULL,
  `icon` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `counter_function` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkUser` (`fkUser`),
  CONSTRAINT `framework__users__dashboards_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__mails`
--

CREATE TABLE IF NOT EXISTS `framework__mails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipients_to` text COLLATE utf8_unicode_ci,
  `recipients_cc` text COLLATE utf8_unicode_ci,
  `recipients_bcc` text COLLATE utf8_unicode_ci,
  `sender_mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sender_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `attachments` text COLLATE utf8_unicode_ci,
  `template` text COLLATE utf8_unicode_ci,
  `errors` text COLLATE utf8_unicode_ci,
  `status` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'inserted, sended, failed',
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `sndTimestamp` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__attachments`
--

CREATE TABLE IF NOT EXISTS `framework__attachments` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `typology` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) unsigned NOT NULL,
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloads` int(11) unsigned NOT NULL DEFAULT '0',
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework__modules`
--

CREATE TABLE IF NOT EXISTS `framework__modules` (
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__modules`
--

INSERT INTO `framework__modules` (`module`, `version`, `enabled`, `addTimestamp`, `addFkUser`, `updTimestamp`, `updFkUser`) VALUES
('framework', '0.0.1', 1, 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `framework__modules__authorizations`
--

CREATE TABLE IF NOT EXISTS `framework__modules__authorizations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action` (`action`),
  KEY `module` (`module`),
  CONSTRAINT `framework__modules__authorizations_ibfk_1` FOREIGN KEY (`module`) REFERENCES `framework__modules` (`module`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework__modules__authorizations`
--

INSERT IGNORE INTO `framework__modules__authorizations` (`id`, `module`, `action`) VALUES
(null, 'framework', 'framework-settings_manage'),
(null, 'framework', 'framework-menus_manage'),
(null, 'framework', 'framework-modules_manage'),
(null, 'framework', 'framework-users_manage'),
(null, 'framework', 'framework-groups_manage'),
(null, 'framework', 'framework-sessions_manage'),
(null, 'framework', 'framework-mails_manage'),
(null, 'framework', 'framework-attachments_manage');

-- --------------------------------------------------------

--
-- Table structure for table `framework__modules__authorizations__groups`
--

CREATE TABLE IF NOT EXISTS `framework__modules__authorizations__groups` (
  `fkAuthorization` int(11) unsigned NOT NULL,
  `fkGroup` int(11) unsigned NOT NULL,
  `level` tinyint(2) NOT NULL,
  KEY `fkAuthorization` (`fkAuthorization`),
  KEY `fkGroup` (`fkGroup`),
  CONSTRAINT `framework__modules__authorizations__groups_ibfk_1` FOREIGN KEY (`fkAuthorization`) REFERENCES `framework__modules__authorizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `framework__modules__authorizations__groups_ibfk_2` FOREIGN KEY (`fkGroup`) REFERENCES `framework__groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
