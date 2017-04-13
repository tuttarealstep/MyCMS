-- phpMyAdmin SQL Dump
-- version 4.6.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Apr 08, 2017 alle 12:14
-- Versione del server: 5.7.17-0ubuntu0.16.04.1
-- Versione PHP: 7.1.3-3+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MyCMS`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `my_blog`
--

CREATE TABLE `my_blog` (
  `postID` int(11) NOT NULL,
  `postTITLE` varchar(255) NOT NULL,
  `postCONT` text NOT NULL,
  `postDATE` datetime NOT NULL,
  `postAUTHOR` varchar(255) NOT NULL,
  `postCATEGORY` varchar(200) NOT NULL,
  `postPOSTED` enum('0','1') NOT NULL,
  `postPERMALINK` varchar(200) NOT NULL,
  `postSTATUS` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_blog_category`
--

CREATE TABLE `my_blog_category` (
  `catID` int(11) NOT NULL,
  `catNAME` varchar(100) NOT NULL,
  `catDESCRIPTION` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_blog_post_comments`
--

CREATE TABLE `my_blog_post_comments` (
  `id` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `comments` varchar(250) NOT NULL,
  `postid` int(11) NOT NULL,
  `date` varchar(100) NOT NULL,
  `enable` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_cms_settings`
--

CREATE TABLE `my_cms_settings` (
  `settings_id` int(11) NOT NULL,
  `settings_name` varchar(100) NOT NULL,
  `settings_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_language`
--

CREATE TABLE `my_language` (
  `language_id` int(11) NOT NULL,
  `language_name` varchar(100) NOT NULL,
  `language_language` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `my_language`
--

INSERT INTO `my_language` (`language_id`, `language_name`, `language_language`) VALUES
(1, 'Italiano - Italian', 'it_IT'),
(2, 'English - English', 'en_US');

-- --------------------------------------------------------

--
-- Struttura della tabella `my_menu`
--

CREATE TABLE `my_menu` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(20) NOT NULL,
  `menu_page_id` varchar(50) NOT NULL,
  `menu_link` varchar(255) NOT NULL,
  `menu_icon` enum('fa','glyphicon','false') NOT NULL DEFAULT 'false',
  `menu_icon_image` varchar(100) NOT NULL,
  `menu_dropdown` enum('0','1') NOT NULL DEFAULT '0',
  `menu_dropdown_parent` int(11) NOT NULL DEFAULT '0',
  `menu_sort` int(11) NOT NULL,
  `menu_enabled` enum('1','0') NOT NULL DEFAULT '1',
  `menu_can_delete` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `my_menu`
--

INSERT INTO `my_menu` (`menu_id`, `menu_name`, `menu_page_id`, `menu_link`, `menu_icon`, `menu_icon_image`, `menu_dropdown`, `menu_dropdown_parent`, `menu_sort`, `menu_enabled`, `menu_can_delete`) VALUES
(1, 'Blog', 'blog', '{@siteURL@}/blog', 'false', '', '0', 0, 2, '1', '0'),
(2, 'Home', '', '{@siteURL@}', 'false', '', '0', 0, 0, '1', '1');

-- --------------------------------------------------------

--
-- Struttura della tabella `my_page`
--

CREATE TABLE `my_page` (
  `pageID` int(11) NOT NULL,
  `pageTITLE` varchar(200) NOT NULL,
  `pageURL` varchar(255) NOT NULL,
  `pagePUBLIC` enum('0','1') NOT NULL DEFAULT '1',
  `pageID_MENU` varchar(200) NOT NULL,
  `pageINTHEME` enum('0','1') NOT NULL DEFAULT '0',
  `pageHTML` text,
  `pageCANDELETE` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `my_page`
--

INSERT INTO `my_page` (`pageID`, `pageTITLE`, `pageURL`, `pagePUBLIC`, `pageID_MENU`, `pageINTHEME`, `pageHTML`, `pageCANDELETE`) VALUES
(1, 'Blog', '{@siteURL@}/blog', '1', 'blog', '1', NULL, '0');

-- --------------------------------------------------------

--
-- Struttura della tabella `my_security_cookie`
--

CREATE TABLE `my_security_cookie` (
  `cookie_id` int(11) NOT NULL,
  `cookie_name` varchar(100) NOT NULL,
  `cookie_value` varchar(300) NOT NULL,
  `cookie_user` int(11) NOT NULL,
  `cookie_expire` varchar(100) NOT NULL,
  `cookie_agent` varchar(200) NOT NULL,
  `cookie_ip` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_style`
--

CREATE TABLE `my_style` (
  `style_id` int(11) NOT NULL,
  `style_name` varchar(100) NOT NULL,
  `style_author` varchar(200) NOT NULL,
  `style_path_name` varchar(200) NOT NULL,
  `style_error_page` varchar(255) NOT NULL,
  `style_maintenance_page` varchar(255) NOT NULL,
  `style_json_file_url` text NOT NULL,
  `style_version` varchar(200) NOT NULL,
  `style_languages` text NOT NULL,
  `style_enable_remove` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `my_style`
--

INSERT INTO `my_style` (`style_id`, `style_name`, `style_author`, `style_path_name`, `style_error_page`, `style_maintenance_page`, `style_json_file_url`, `style_version`, `style_languages`, `style_enable_remove`) VALUES
(1, 'MyCMS Default', 'MyCMS', 'default', '404', 'maintenance', '', '0.0.0.1', 'it_IT,en_US', '0');

-- --------------------------------------------------------

--
-- Struttura della tabella `my_users`
--

CREATE TABLE `my_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `mail` varchar(100) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `rank` int(10) NOT NULL DEFAULT '1',
  `last_access` varchar(20) NOT NULL DEFAULT '0000-00-00 00:00:00',
  `adminColor` varchar(100) NOT NULL DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `my_users_banned`
--

CREATE TABLE `my_users_banned` (
  `id` int(11) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `expire_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `my_blog`
--
ALTER TABLE `my_blog`
  ADD PRIMARY KEY (`postID`);

--
-- Indici per le tabelle `my_blog_category`
--
ALTER TABLE `my_blog_category`
  ADD PRIMARY KEY (`catID`);

--
-- Indici per le tabelle `my_blog_post_comments`
--
ALTER TABLE `my_blog_post_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `my_cms_settings`
--
ALTER TABLE `my_cms_settings`
  ADD PRIMARY KEY (`settings_id`);

--
-- Indici per le tabelle `my_language`
--
ALTER TABLE `my_language`
  ADD PRIMARY KEY (`language_id`);

--
-- Indici per le tabelle `my_menu`
--
ALTER TABLE `my_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indici per le tabelle `my_page`
--
ALTER TABLE `my_page`
  ADD PRIMARY KEY (`pageID`);

--
-- Indici per le tabelle `my_security_cookie`
--
ALTER TABLE `my_security_cookie`
  ADD PRIMARY KEY (`cookie_id`);

--
-- Indici per le tabelle `my_style`
--
ALTER TABLE `my_style`
  ADD PRIMARY KEY (`style_id`);

--
-- Indici per le tabelle `my_users`
--
ALTER TABLE `my_users`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `my_users_banned`
--
ALTER TABLE `my_users_banned`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `my_blog`
--
ALTER TABLE `my_blog`
  MODIFY `postID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_blog_category`
--
ALTER TABLE `my_blog_category`
  MODIFY `catID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_blog_post_comments`
--
ALTER TABLE `my_blog_post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_cms_settings`
--
ALTER TABLE `my_cms_settings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_language`
--
ALTER TABLE `my_language`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `my_menu`
--
ALTER TABLE `my_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `my_page`
--
ALTER TABLE `my_page`
  MODIFY `pageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `my_security_cookie`
--
ALTER TABLE `my_security_cookie`
  MODIFY `cookie_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_style`
--
ALTER TABLE `my_style`
  MODIFY `style_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `my_users`
--
ALTER TABLE `my_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `my_users_banned`
--
ALTER TABLE `my_users_banned`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;