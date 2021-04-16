-- --------------------------------------------------------

--
-- Query da eseguire a mano solo se si è installato prima della data riportata sopra alle query
--

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

-- 2021-04-16

ALTER TABLE `framework__settings` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

-- --------------------------------------------------------



-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
