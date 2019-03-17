-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 
-- Erstellungszeit: 20. Feb 2019 um 16:37
-- Server-Version: 5.5.60-0+deb7u1-log
-- PHP-Version: 7.2.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tickets`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_attachments`
--

CREATE TABLE `hesk_attachments` (
  `att_id` mediumint(8) UNSIGNED NOT NULL,
  `ticket_id` varchar(13) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `saved_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `real_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `size` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_banned_emails`
--

CREATE TABLE `hesk_banned_emails` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `banned_by` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_banned_ips`
--

CREATE TABLE `hesk_banned_ips` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `ip_from` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_to` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_display` varchar(100) NOT NULL,
  `banned_by` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_categories`
--

CREATE TABLE `hesk_categories` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cat_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `autoassign` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `type` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `priority` enum('0','1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '3'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_custom_fields`
--

CREATE TABLE `hesk_custom_fields` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `use` enum('0','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `place` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `req` enum('0','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `category` text COLLATE utf8_unicode_ci,
  `name` text COLLATE utf8_unicode_ci,
  `value` text COLLATE utf8_unicode_ci,
  `order` smallint(5) UNSIGNED NOT NULL DEFAULT '10'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_custom_statuses`
--

CREATE TABLE `hesk_custom_statuses` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `can_customers_change` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `order` smallint(5) UNSIGNED NOT NULL DEFAULT '10'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_kb_articles`
--

CREATE TABLE `hesk_kb_articles` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `catid` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` smallint(5) UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `keywords` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `votes` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `views` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `type` enum('0','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `html` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `sticky` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `art_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `history` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `attachments` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_kb_attachments`
--

CREATE TABLE `hesk_kb_attachments` (
  `att_id` mediumint(8) UNSIGNED NOT NULL,
  `saved_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `real_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `size` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_kb_categories`
--

CREATE TABLE `hesk_kb_categories` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent` smallint(5) UNSIGNED NOT NULL,
  `articles` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `articles_private` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `articles_draft` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `cat_order` smallint(5) UNSIGNED NOT NULL,
  `type` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_logins`
--

CREATE TABLE `hesk_logins` (
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `number` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `last_attempt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_mail`
--

CREATE TABLE `hesk_mail` (
  `id` int(10) UNSIGNED NOT NULL,
  `from` smallint(5) UNSIGNED NOT NULL,
  `to` smallint(5) UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `deletedby` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_notes`
--

CREATE TABLE `hesk_notes` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ticket` mediumint(8) UNSIGNED NOT NULL,
  `who` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `attachments` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_online`
--

CREATE TABLE `hesk_online` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tmp` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_pipe_loops`
--

CREATE TABLE `hesk_pipe_loops` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hits` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
  `message_hash` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_replies`
--

CREATE TABLE `hesk_replies` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `replyto` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attachments` mediumtext COLLATE utf8_unicode_ci,
  `staffid` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `rating` enum('1','5') COLLATE utf8_unicode_ci DEFAULT NULL,
  `read` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_reply_drafts`
--

CREATE TABLE `hesk_reply_drafts` (
  `owner` smallint(5) UNSIGNED NOT NULL,
  `ticket` mediumint(8) UNSIGNED NOT NULL,
  `message` mediumtext CHARACTER SET utf8 NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_reset_password`
--

CREATE TABLE `hesk_reset_password` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `user` smallint(5) UNSIGNED NOT NULL,
  `hash` char(40) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_service_messages`
--

CREATE TABLE `hesk_service_messages` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `style` enum('0','1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `type` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `order` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_std_replies`
--

CREATE TABLE `hesk_std_replies` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `reply_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_tickets`
--

CREATE TABLE `hesk_tickets` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `trackid` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `category` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `priority` enum('0','1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '3',
  `subject` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dt` timestamp NOT NULL DEFAULT '1999-12-31 23:00:00',
  `lastchange` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `firstreply` timestamp NULL DEFAULT NULL,
  `closedat` timestamp NULL DEFAULT NULL,
  `articles` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `openedby` mediumint(8) DEFAULT '0',
  `firstreplyby` smallint(5) UNSIGNED DEFAULT NULL,
  `closedby` mediumint(8) DEFAULT NULL,
  `replies` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `staffreplies` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `owner` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `assignedby` mediumint(9) DEFAULT NULL,
  `time_worked` time NOT NULL DEFAULT '00:00:00',
  `lastreplier` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `replierid` smallint(5) UNSIGNED DEFAULT NULL,
  `archive` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `locked` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `attachments` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `merged` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `history` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom1` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom2` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom3` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom4` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom5` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom6` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom7` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom8` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom9` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom10` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom11` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom12` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom13` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom14` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom15` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom16` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom17` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom18` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom19` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom20` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom21` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom22` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom23` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom24` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom25` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom26` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom27` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom28` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom29` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom30` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom31` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom32` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom33` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom34` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom35` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom36` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom37` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom38` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom39` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom40` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom41` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom42` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom43` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom44` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom45` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom46` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom47` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom48` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom49` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `custom50` mediumtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_ticket_templates`
--

CREATE TABLE `hesk_ticket_templates` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `tpl_order` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hesk_users`
--

CREATE TABLE `hesk_users` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `user` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `isadmin` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `signature` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `categories` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `afterreply` enum('0','1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `autostart` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `autoreload` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `notify_customer_new` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_customer_reply` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `show_suggested` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_new_unassigned` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_new_my` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_reply_unassigned` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_reply_my` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_assigned` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_pm` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `notify_note` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `default_list` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `autoassign` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `heskprivileges` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ratingneg` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `ratingpos` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `rating` float NOT NULL DEFAULT '0',
  `replies` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `locks`
--

CREATE TABLE `locks` (
  `token` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `path` varchar(200) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `expires` int(11) NOT NULL DEFAULT '0',
  `owner` varchar(200) COLLATE latin1_german2_ci DEFAULT NULL,
  `recursive` int(11) DEFAULT '0',
  `writelock` int(11) DEFAULT '0',
  `exclusivelock` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `properties`
--

CREATE TABLE `properties` (
  `path` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `name` varchar(120) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `ns` varchar(120) COLLATE latin1_german2_ci NOT NULL DEFAULT 'DAV:',
  `value` text COLLATE latin1_german2_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zertifizierungen`
--

CREATE TABLE `zertifizierungen` (
  `id` int(11) NOT NULL,
  `titel` varchar(30) COLLATE utf8_bin NOT NULL,
  `eingereicht` int(1) DEFAULT NULL,
  `gutachter1_ok` int(1) NOT NULL,
  `gutachter2_ok` int(1) NOT NULL,
  `gutachter3_ok` int(1) NOT NULL,
  `erledigt` int(1) NOT NULL,
  `gutachter1` varchar(20) COLLATE utf8_bin NOT NULL,
  `g1_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g1_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter2` varchar(20) COLLATE utf8_bin NOT NULL,
  `g2_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g2_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter3` varchar(20) COLLATE utf8_bin NOT NULL,
  `g3_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g3_notiz` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zertifizierungen_allg`
--

CREATE TABLE `zertifizierungen_allg` (
  `id` int(11) NOT NULL,
  `titel` varchar(50) COLLATE utf8_bin NOT NULL,
  `eingereicht` int(1) DEFAULT NULL,
  `gutachter1_ok` int(1) NOT NULL,
  `gutachter2_ok` int(1) NOT NULL,
  `gutachter3_ok` int(1) NOT NULL,
  `erledigt` int(1) NOT NULL,
  `gutachter1` varchar(20) COLLATE utf8_bin NOT NULL,
  `g1_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g1_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter2` varchar(20) COLLATE utf8_bin NOT NULL,
  `g2_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g2_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter3` varchar(20) COLLATE utf8_bin NOT NULL,
  `g3_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g3_notiz` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zertifizierungen_bw`
--

CREATE TABLE `zertifizierungen_bw` (
  `id` int(11) NOT NULL,
  `titel` varchar(50) COLLATE utf8_bin NOT NULL,
  `eingereicht` int(1) DEFAULT NULL,
  `gutachter1_ok` int(1) NOT NULL,
  `gutachter2_ok` int(1) NOT NULL,
  `gutachter3_ok` int(1) NOT NULL,
  `erledigt` int(1) NOT NULL,
  `gutachter1` varchar(20) COLLATE utf8_bin NOT NULL,
  `g1_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g1_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter2` varchar(20) COLLATE utf8_bin NOT NULL,
  `g2_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g2_notiz` text COLLATE utf8_bin NOT NULL,
  `gutachter3` varchar(20) COLLATE utf8_bin NOT NULL,
  `g3_musik` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '---',
  `g3_notiz` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `hesk_attachments`
--
ALTER TABLE `hesk_attachments`
  ADD PRIMARY KEY (`att_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indizes für die Tabelle `hesk_banned_emails`
--
ALTER TABLE `hesk_banned_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indizes für die Tabelle `hesk_banned_ips`
--
ALTER TABLE `hesk_banned_ips`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hesk_categories`
--
ALTER TABLE `hesk_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Indizes für die Tabelle `hesk_custom_fields`
--
ALTER TABLE `hesk_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `useType` (`use`,`type`);

--
-- Indizes für die Tabelle `hesk_custom_statuses`
--
ALTER TABLE `hesk_custom_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hesk_kb_articles`
--
ALTER TABLE `hesk_kb_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catid` (`catid`),
  ADD KEY `sticky` (`sticky`),
  ADD KEY `type` (`type`);
ALTER TABLE `hesk_kb_articles` ADD FULLTEXT KEY `subject` (`subject`,`content`,`keywords`);

--
-- Indizes für die Tabelle `hesk_kb_attachments`
--
ALTER TABLE `hesk_kb_attachments`
  ADD PRIMARY KEY (`att_id`);

--
-- Indizes für die Tabelle `hesk_kb_categories`
--
ALTER TABLE `hesk_kb_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `parent` (`parent`);

--
-- Indizes für die Tabelle `hesk_logins`
--
ALTER TABLE `hesk_logins`
  ADD UNIQUE KEY `ip` (`ip`);

--
-- Indizes für die Tabelle `hesk_mail`
--
ALTER TABLE `hesk_mail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from` (`from`),
  ADD KEY `to` (`to`,`read`,`deletedby`);

--
-- Indizes für die Tabelle `hesk_notes`
--
ALTER TABLE `hesk_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticketid` (`ticket`);

--
-- Indizes für die Tabelle `hesk_online`
--
ALTER TABLE `hesk_online`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `dt` (`dt`);

--
-- Indizes für die Tabelle `hesk_pipe_loops`
--
ALTER TABLE `hesk_pipe_loops`
  ADD KEY `email` (`email`,`hits`);

--
-- Indizes für die Tabelle `hesk_replies`
--
ALTER TABLE `hesk_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replyto` (`replyto`),
  ADD KEY `dt` (`dt`),
  ADD KEY `staffid` (`staffid`);

--
-- Indizes für die Tabelle `hesk_reply_drafts`
--
ALTER TABLE `hesk_reply_drafts`
  ADD KEY `owner` (`owner`),
  ADD KEY `ticket` (`ticket`);

--
-- Indizes für die Tabelle `hesk_reset_password`
--
ALTER TABLE `hesk_reset_password`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Indizes für die Tabelle `hesk_service_messages`
--
ALTER TABLE `hesk_service_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Indizes für die Tabelle `hesk_std_replies`
--
ALTER TABLE `hesk_std_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hesk_tickets`
--
ALTER TABLE `hesk_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trackid` (`trackid`),
  ADD KEY `archive` (`archive`),
  ADD KEY `categories` (`category`),
  ADD KEY `statuses` (`status`),
  ADD KEY `owner` (`owner`),
  ADD KEY `openedby` (`openedby`,`firstreplyby`,`closedby`),
  ADD KEY `dt` (`dt`),
  ADD KEY `assignedby` (`assignedby`);

--
-- Indizes für die Tabelle `hesk_ticket_templates`
--
ALTER TABLE `hesk_ticket_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `hesk_users`
--
ALTER TABLE `hesk_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autoassign` (`autoassign`);

--
-- Indizes für die Tabelle `locks`
--
ALTER TABLE `locks`
  ADD PRIMARY KEY (`token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `path` (`path`),
  ADD KEY `expires` (`expires`);

--
-- Indizes für die Tabelle `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`ns`(100),`path`(100),`name`(50)),
  ADD KEY `path` (`path`);

--
-- Indizes für die Tabelle `zertifizierungen`
--
ALTER TABLE `zertifizierungen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titel` (`titel`);

--
-- Indizes für die Tabelle `zertifizierungen_allg`
--
ALTER TABLE `zertifizierungen_allg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titel` (`titel`);

--
-- Indizes für die Tabelle `zertifizierungen_bw`
--
ALTER TABLE `zertifizierungen_bw`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titel` (`titel`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `hesk_attachments`
--
ALTER TABLE `hesk_attachments`
  MODIFY `att_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_banned_emails`
--
ALTER TABLE `hesk_banned_emails`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_banned_ips`
--
ALTER TABLE `hesk_banned_ips`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_categories`
--
ALTER TABLE `hesk_categories`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_kb_articles`
--
ALTER TABLE `hesk_kb_articles`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_kb_attachments`
--
ALTER TABLE `hesk_kb_attachments`
  MODIFY `att_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_kb_categories`
--
ALTER TABLE `hesk_kb_categories`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_mail`
--
ALTER TABLE `hesk_mail`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_notes`
--
ALTER TABLE `hesk_notes`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_replies`
--
ALTER TABLE `hesk_replies`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_reset_password`
--
ALTER TABLE `hesk_reset_password`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_service_messages`
--
ALTER TABLE `hesk_service_messages`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_std_replies`
--
ALTER TABLE `hesk_std_replies`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_tickets`
--
ALTER TABLE `hesk_tickets`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_ticket_templates`
--
ALTER TABLE `hesk_ticket_templates`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `hesk_users`
--
ALTER TABLE `hesk_users`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `zertifizierungen`
--
ALTER TABLE `zertifizierungen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `zertifizierungen_allg`
--
ALTER TABLE `zertifizierungen_allg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `zertifizierungen_bw`
--
ALTER TABLE `zertifizierungen_bw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
