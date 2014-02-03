-- phpMyAdmin SQL Dump
-- version 3.4.11deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 26, 2012 at 11:29 PM
-- Server version: 5.1.45
-- PHP Version: 5.3.3-7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `poolc_addons`
--

-- --------------------------------------------------------

--
-- Table structure for table `pzen_category`
--

DROP TABLE IF EXISTS `pzen_category`;
CREATE TABLE IF NOT EXISTS `pzen_category` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`index`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `pzen_category_view`
--
DROP VIEW IF EXISTS `pzen_category_view`;
CREATE TABLE IF NOT EXISTS `pzen_category_view` (
`index` int(11)
,`name` varchar(80)
,`count` bigint(21)
);
-- --------------------------------------------------------

--
-- Table structure for table `pzen_comment`
--

DROP TABLE IF EXISTS `pzen_comment`;
CREATE TABLE IF NOT EXISTS `pzen_comment` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `threadIndex` int(11) NOT NULL,
  `userIndex` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` text COLLATE utf8_unicode_ci NOT NULL,
  `editdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `regdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pzen_comment_attach`
--

DROP TABLE IF EXISTS `pzen_comment_attach`;
CREATE TABLE IF NOT EXISTS `pzen_comment_attach` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `commentIndex` int(11) NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(240) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'application/octet-stream',
  `size` int(11) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pzen_comment_tags`
--

DROP TABLE IF EXISTS `pzen_comment_tags`;
CREATE TABLE IF NOT EXISTS `pzen_comment_tags` (
  `tag` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pzen_thread`
--

DROP TABLE IF EXISTS `pzen_thread`;
CREATE TABLE IF NOT EXISTS `pzen_thread` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `categoryIndex` int(11) NOT NULL DEFAULT '1',
  `userIndex` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(240) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` text COLLATE utf8_unicode_ci,
  `editdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `regdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pzen_thread_attach`
--

DROP TABLE IF EXISTS `pzen_thread_attach`;
CREATE TABLE IF NOT EXISTS `pzen_thread_attach` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `threadIndex` int(11) NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(240) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'application/octet-stream',
  `size` int(11) NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pzen_thread_tags`
--

DROP TABLE IF EXISTS `pzen_thread_tags`;
CREATE TABLE IF NOT EXISTS `pzen_thread_tags` (
  `tag` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `pzen_category_view`
--
DROP TABLE IF EXISTS `pzen_category_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`imaso`@`localhost` SQL SECURITY DEFINER VIEW `pzen_category_view` AS select `c`.`index` AS `index`,`c`.`name` AS `name`,count(`t`.`index`) AS `count` from (`pzen_thread` `t` join `pzen_category` `c`) where (`t`.`categoryIndex` = `c`.`index`) group by `t`.`categoryIndex`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
