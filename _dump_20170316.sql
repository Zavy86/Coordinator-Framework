-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 16, 2017 at 02:14 PM
-- Server version: 5.5.54
-- PHP Version: 5.4.45-0+deb7u7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `framework`
--

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

--
-- Dumping data for table `framework_sessions`
--

INSERT INTO `framework_sessions` (`id`, `fkUser`, `ipAddress`, `startTimestamp`, `lastTimestamp`) VALUES
('099697cc8693ec03b28a851476818b52', 1, '172.16.75.225', 1489668928, 1489668949),
('50d7141566038012ca6a05151b30c5c9', 1, '172.16.75.225', 1489662771, 1489662828),
('cf4be5124d9e9135cead62bfef54d416', 1, '172.16.67.93', 1489662140, 1489669893);

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
('maintenance', '0'),
('owner', 'Cogne Acciai Speciali s.p.a.'),
('sendmail_asynchronous', '1'),
('sendmail_from_mail', 'coordinator@cogne.com'),
('sendmail_from_name', 'Coordinator CAS'),
('sendmail_method', 'smtp'),
('sendmail_smtp_encryption', ''),
('sendmail_smtp_host', ''),
('sendmail_smtp_hostname', '172.16.65.242'),
('sendmail_smtp_password', ''),
('sendmail_smtp_username', ''),
('sessions_authentication_method', 'standard'),
('sessions_idle_timeout', '14400'),
('sessions_ldap_cache', '0'),
('sessions_ldap_dn', 'DC=CAS,DC=LOCAL'),
('sessions_ldap_domain', '@CAS.LOCAL'),
('sessions_ldap_groups', ''),
('sessions_ldap_hostname', '172.16.65.244'),
('sessions_ldap_userfield', 'SAMACCOUNTNAME'),
('sessions_multiple', '1'),
('title', 'Coordinator Framework'),
('token_cron', 'aedf1bddc63dfa1e6b95779aaf4995c3'),
('users_password_expiration', '2592000');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `framework_users`
--

INSERT INTO `framework_users` (`id`, `mail`, `firstname`, `lastname`, `localization`, `timezone`, `password`, `secret`, `enabled`, `addTimestamp`, `addFkUser`, `updTimestamp`, `updFkUser`, `pwdTimestamp`, `deleted`) VALUES
(1, 'manuel.zavatta@cogne.com', 'Manuel', 'Zavatta', 'it_IT', 'Europe/Rome', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 1, 1488296928, 1, 1489661263, 1, 1489660293, 0),
(2, 'michael.angelini@cogne.com', 'Michael', 'Angelini', 'it_IT', 'Europe/Rome', 'dbc9e3bea2494ddec6ceed015ce51f94', NULL, 1, 1488296928, 1, NULL, NULL, 1489660002, 0),
(3, 'massimiliano.kratter@cogne.com', 'Massimiliano', 'Kratter', 'it_IT', 'Europe/Rome', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, 1, 1488296928, 1, 1489508587, 1, NULL, 1),
(4, 'manuel.zavatta@gmail.com', 'Gmail', 'Zavy', 'it_IT', 'Europe/Rome', 'bb41a010291c8f0b14bb5dc268e9561e', NULL, 1, 1489509528, 1, 1489659731, 1, NULL, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `framework_sessions`
--
ALTER TABLE `framework_sessions`
  ADD CONSTRAINT `framework_sessions_ibfk_1` FOREIGN KEY (`fkUser`) REFERENCES `framework_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
