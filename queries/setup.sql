--
-- Setup Coordinator Framework
--
-- Version 1.0.0
--

-- --------------------------------------------------------

SET time_zone = "+00:00";
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `framework_settings`
--

CREATE TABLE IF NOT EXISTS `framework_settings` (
  `setting` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework_settings`
--

INSERT INTO `framework_settings` (`setting`, `value`) VALUES
('version', '1.0.0'),
('maintenance', '0'),
('owner', 'Company name'),
('sendmail_asynchronous', '0'),
('sendmail_from_mail', 'company@domain.tdl'),
('sendmail_from_name', 'Coordinator'),
('sendmail_method', 'standard'),
('sendmail_smtp_encryption', ''),
('sendmail_smtp_host', ''),
('sendmail_smtp_hostname', ''),
('sendmail_smtp_password', ''),
('sendmail_smtp_username', ''),
('sessions_authentication_method', 'standard'),
('sessions_idle_timeout', '14400'),
('sessions_ldap_cache', '0'),
('sessions_ldap_dn', ''),
('sessions_ldap_domain', ''),
('sessions_ldap_groups', ''),
('sessions_ldap_hostname', ''),
('sessions_ldap_userfield', ''),
('sessions_multiple', '1'),
('title', 'Coordinator Framework'),
('token_cron', ''),
('users_password_expiration', '-1');

-- --------------------------------------------------------

--
-- Table structure for table `framework_sessions`
--

CREATE TABLE IF NOT EXISTS `framework_sessions` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fkUser` int(11) unsigned NOT NULL,
  `ipAddress` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `startTimestamp` int(11) unsigned NOT NULL,
  `lastTimestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkUser` (`fkUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users`
--

CREATE TABLE IF NOT EXISTS `framework_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `localization` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL,
  `addTimestamp` int(11) unsigned NOT NULL,
  `addFkUser` int(11) unsigned NOT NULL,
  `updTimestamp` int(11) unsigned DEFAULT NULL,
  `updFkUser` int(11) unsigned DEFAULT NULL,
  `pwdTimestamp` int(11) DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework_users`
--

INSERT INTO `framework_users` (`id`, `mail`, `firstname`, `lastname`, `localization`, `timezone`, `password`, `secret`, `enabled`, `addTimestamp`, `addFkUser`, `updTimestamp`, `updFkUser`, `pwdTimestamp`, `deleted`) VALUES
(1, 'you@domain.tdl', 'Administrator', 'Coordinator', 'en_EN', 'Europe/London', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 1, 1483228800, 1, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `framework_users_join_groups`
--

CREATE TABLE IF NOT EXISTS `framework_users_join_groups` (
  `fkUser` int(11) unsigned NOT NULL,
  `fkGroup` int(11) unsigned NOT NULL,
  `main` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `fkUser` (`fkUser`),
  KEY `fkGroup` (`fkGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_groups`
--

CREATE TABLE IF NOT EXISTS `framework_groups` (
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
  KEY `fkGroup` (`fkGroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `framework_groups`
--

INSERT INTO `framework_groups` (`id`, `fkGroup`, `name`, `description`, `addTimestamp`, `addFkUser`, `updTimestamp`, `updFkUser`, `deleted`) VALUES
(1, NULL, 'Administrators', 'Coordinator Administrators', 1483228800, 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `framework_sessions`
--
ALTER TABLE `framework_sessions`
  ADD CONSTRAINT `framework_sessions_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `framework_users_join_groups`
--
ALTER TABLE `framework_users_join_groups`
  ADD CONSTRAINT `framework_users_join_groups_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `framework_users_join_groups_ibfk_2` FOREIGN KEY (`fkGroup`) REFERENCES `framework_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------