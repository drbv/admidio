-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 
-- Erstellungszeit: 20. Feb 2019 um 16:12
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
-- Datenbank: `Admidio`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_announcements`
--

CREATE TABLE `adm_announcements` (
  `ann_id` int(10) UNSIGNED NOT NULL,
  `ann_org_shortname` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `ann_global` tinyint(1) NOT NULL DEFAULT '0',
  `ann_headline` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ann_description` text COLLATE utf8_unicode_ci,
  `ann_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `ann_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ann_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `ann_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_auto_login`
--

CREATE TABLE `adm_auto_login` (
  `atl_id` int(10) UNSIGNED NOT NULL,
  `atl_session_id` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `atl_org_id` int(10) UNSIGNED NOT NULL,
  `atl_usr_id` int(10) UNSIGNED NOT NULL,
  `atl_last_login` timestamp NULL DEFAULT NULL,
  `atl_ip_address` varchar(39) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_categories`
--

CREATE TABLE `adm_categories` (
  `cat_id` int(10) UNSIGNED NOT NULL,
  `cat_org_id` int(10) UNSIGNED DEFAULT NULL,
  `cat_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cat_name_intern` varchar(110) COLLATE utf8_unicode_ci NOT NULL,
  `cat_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cat_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `cat_system` tinyint(1) NOT NULL DEFAULT '0',
  `cat_default` tinyint(1) NOT NULL DEFAULT '0',
  `cat_sequence` smallint(6) NOT NULL,
  `cat_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `cat_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cat_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `cat_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_dates`
--

CREATE TABLE `adm_dates` (
  `dat_id` int(10) UNSIGNED NOT NULL,
  `dat_cat_id` int(10) UNSIGNED NOT NULL,
  `dat_rol_id` int(10) UNSIGNED DEFAULT NULL,
  `dat_room_id` int(10) UNSIGNED DEFAULT NULL,
  `dat_global` tinyint(1) NOT NULL DEFAULT '0',
  `dat_begin` timestamp NULL DEFAULT NULL,
  `dat_end` timestamp NULL DEFAULT NULL,
  `dat_all_day` tinyint(1) NOT NULL DEFAULT '0',
  `dat_highlight` tinyint(1) NOT NULL DEFAULT '0',
  `dat_description` text COLLATE utf8_unicode_ci,
  `dat_location` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_headline` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dat_max_members` int(11) NOT NULL DEFAULT '0',
  `dat_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `dat_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dat_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `dat_timestamp_change` timestamp NULL DEFAULT NULL,
  `dat_sk_s` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_j` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_c` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_b` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_a` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_bwh` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_bwo` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_bwj` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_frm` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_frj` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_frl` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_frg` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_fbm` tinyint(1) NOT NULL DEFAULT '0',
  `dat_tl` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_tform` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_ansprechpartner` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_mail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_link` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_sk_bsp` tinyint(1) NOT NULL DEFAULT '0',
  `dat_sk_frs` tinyint(1) NOT NULL DEFAULT '0',
  `dat_verein` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_vereinsnummer` smallint(6) NOT NULL DEFAULT '0',
  `dat_tform_international` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'National',
  `dat_ansprechpartner_anschrift` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_tel` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_fax` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_handy` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_turniernummer` int(7) NOT NULL DEFAULT '0',
  `dat_sk_bwh_b` tinyint(1) DEFAULT '0',
  `dat_sk_bwo_b` tinyint(1) DEFAULT '0',
  `dat_tform_cupserie` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_location_wo` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_location_str` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_location_plz` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_location_ort` varchar(99) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dat_quali` tinyint(1) NOT NULL DEFAULT '0',
  `dat_notiz` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_date_role`
--

CREATE TABLE `adm_date_role` (
  `dtr_id` int(10) UNSIGNED NOT NULL,
  `dtr_dat_id` int(10) UNSIGNED NOT NULL,
  `dtr_rol_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_drbv_wertungen`
--

CREATE TABLE `adm_drbv_wertungen` (
  `id` int(11) NOT NULL,
  `platz` int(11) NOT NULL,
  `startnr` int(11) NOT NULL,
  `avw` int(11) NOT NULL,
  `a1` int(11) NOT NULL,
  `a2` int(11) NOT NULL,
  `a3` int(11) NOT NULL,
  `a4` int(11) NOT NULL,
  `summe_a` int(11) NOT NULL,
  `t1` int(11) NOT NULL,
  `t2` int(11) NOT NULL,
  `t3` int(11) NOT NULL,
  `t4` int(11) NOT NULL,
  `summe_t` int(11) NOT NULL,
  `obs1` int(11) NOT NULL,
  `obs2` int(11) NOT NULL,
  `summe_abzuege` int(11) NOT NULL,
  `gesamtpunkte` int(11) NOT NULL,
  `startbuchnr` int(11) NOT NULL,
  `aufstiegspunkte` int(11) NOT NULL,
  `turniername` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `turnierdatum` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_files`
--

CREATE TABLE `adm_files` (
  `fil_id` int(10) UNSIGNED NOT NULL,
  `fil_fol_id` int(10) UNSIGNED NOT NULL,
  `fil_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fil_description` text COLLATE utf8_unicode_ci,
  `fil_locked` tinyint(1) NOT NULL DEFAULT '0',
  `fil_counter` int(11) DEFAULT NULL,
  `fil_usr_id` int(10) UNSIGNED DEFAULT NULL,
  `fil_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_folders`
--

CREATE TABLE `adm_folders` (
  `fol_id` int(10) UNSIGNED NOT NULL,
  `fol_org_id` int(10) UNSIGNED NOT NULL,
  `fol_fol_id_parent` int(10) UNSIGNED DEFAULT NULL,
  `fol_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fol_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fol_description` text COLLATE utf8_unicode_ci,
  `fol_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fol_locked` tinyint(1) NOT NULL DEFAULT '0',
  `fol_public` tinyint(1) NOT NULL DEFAULT '0',
  `fol_usr_id` int(10) UNSIGNED DEFAULT NULL,
  `fol_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_folder_roles`
--

CREATE TABLE `adm_folder_roles` (
  `flr_fol_id` int(10) UNSIGNED NOT NULL,
  `flr_rol_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_guestbook`
--

CREATE TABLE `adm_guestbook` (
  `gbo_id` int(10) UNSIGNED NOT NULL,
  `gbo_org_id` int(10) UNSIGNED NOT NULL,
  `gbo_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `gbo_text` text COLLATE utf8_unicode_ci NOT NULL,
  `gbo_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gbo_homepage` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gbo_ip_address` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `gbo_locked` tinyint(1) NOT NULL DEFAULT '0',
  `gbo_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `gbo_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gbo_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `gbo_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_guestbook_comments`
--

CREATE TABLE `adm_guestbook_comments` (
  `gbc_id` int(10) UNSIGNED NOT NULL,
  `gbc_gbo_id` int(10) UNSIGNED NOT NULL,
  `gbc_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `gbc_text` text COLLATE utf8_unicode_ci NOT NULL,
  `gbc_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gbc_ip_address` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `gbc_locked` tinyint(1) NOT NULL DEFAULT '0',
  `gbc_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `gbc_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gbc_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `gbc_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_links`
--

CREATE TABLE `adm_links` (
  `lnk_id` int(10) UNSIGNED NOT NULL,
  `lnk_cat_id` int(10) UNSIGNED NOT NULL,
  `lnk_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lnk_description` text COLLATE utf8_unicode_ci,
  `lnk_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lnk_counter` int(11) NOT NULL DEFAULT '0',
  `lnk_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `lnk_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lnk_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `lnk_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_lists`
--

CREATE TABLE `adm_lists` (
  `lst_id` int(10) UNSIGNED NOT NULL,
  `lst_org_id` int(10) UNSIGNED NOT NULL,
  `lst_usr_id` int(10) UNSIGNED NOT NULL,
  `lst_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lst_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lst_global` tinyint(1) NOT NULL DEFAULT '0',
  `lst_default` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_list_columns`
--

CREATE TABLE `adm_list_columns` (
  `lsc_id` int(10) UNSIGNED NOT NULL,
  `lsc_lst_id` int(10) UNSIGNED NOT NULL,
  `lsc_number` smallint(6) NOT NULL,
  `lsc_usf_id` int(10) UNSIGNED DEFAULT NULL,
  `lsc_special_field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lsc_sort` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lsc_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_members`
--

CREATE TABLE `adm_members` (
  `mem_id` int(10) UNSIGNED NOT NULL,
  `mem_rol_id` int(10) UNSIGNED NOT NULL,
  `mem_usr_id` int(10) UNSIGNED NOT NULL,
  `mem_begin` date NOT NULL,
  `mem_end` date NOT NULL DEFAULT '9999-12-31',
  `mem_leader` tinyint(1) NOT NULL DEFAULT '0',
  `mem_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `mem_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mem_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `mem_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_organizations`
--

CREATE TABLE `adm_organizations` (
  `org_id` int(10) UNSIGNED NOT NULL,
  `org_longname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `org_shortname` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `org_org_id_parent` int(10) UNSIGNED DEFAULT NULL,
  `org_homepage` varchar(60) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_photos`
--

CREATE TABLE `adm_photos` (
  `pho_id` int(10) UNSIGNED NOT NULL,
  `pho_org_shortname` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `pho_quantity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `pho_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pho_begin` date NOT NULL,
  `pho_end` date NOT NULL,
  `pho_photographers` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pho_locked` tinyint(1) NOT NULL DEFAULT '0',
  `pho_pho_id_parent` int(10) UNSIGNED DEFAULT NULL,
  `pho_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `pho_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pho_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `pho_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_preferences`
--

CREATE TABLE `adm_preferences` (
  `prf_id` int(10) UNSIGNED NOT NULL,
  `prf_org_id` int(10) UNSIGNED NOT NULL,
  `prf_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `prf_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_registrations`
--

CREATE TABLE `adm_registrations` (
  `reg_id` int(10) UNSIGNED NOT NULL,
  `reg_org_id` int(10) UNSIGNED NOT NULL,
  `reg_usr_id` int(10) UNSIGNED NOT NULL,
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_roles`
--

CREATE TABLE `adm_roles` (
  `rol_id` int(10) UNSIGNED NOT NULL,
  `rol_cat_id` int(10) UNSIGNED NOT NULL,
  `rol_lst_id` int(10) UNSIGNED DEFAULT NULL,
  `rol_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `rol_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rol_assign_roles` tinyint(1) NOT NULL DEFAULT '0',
  `rol_approve_users` tinyint(1) NOT NULL DEFAULT '0',
  `rol_announcements` tinyint(1) NOT NULL DEFAULT '0',
  `rol_dates` tinyint(1) NOT NULL DEFAULT '0',
  `rol_download` tinyint(1) NOT NULL DEFAULT '0',
  `rol_edit_user` tinyint(1) NOT NULL DEFAULT '0',
  `rol_guestbook` tinyint(1) NOT NULL DEFAULT '0',
  `rol_guestbook_comments` tinyint(1) NOT NULL DEFAULT '0',
  `rol_mail_to_all` tinyint(1) NOT NULL DEFAULT '0',
  `rol_mail_this_role` smallint(6) NOT NULL DEFAULT '0',
  `rol_photo` tinyint(1) NOT NULL DEFAULT '0',
  `rol_profile` tinyint(1) NOT NULL DEFAULT '0',
  `rol_weblinks` tinyint(1) NOT NULL DEFAULT '0',
  `rol_this_list_view` smallint(6) NOT NULL DEFAULT '0',
  `rol_all_lists_view` tinyint(1) NOT NULL DEFAULT '0',
  `rol_default_registration` tinyint(1) NOT NULL DEFAULT '0',
  `rol_leader_rights` smallint(6) NOT NULL DEFAULT '0',
  `rol_start_date` date DEFAULT NULL,
  `rol_start_time` time DEFAULT NULL,
  `rol_end_date` date DEFAULT NULL,
  `rol_end_time` time DEFAULT NULL,
  `rol_weekday` smallint(6) DEFAULT NULL,
  `rol_location` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rol_max_members` int(11) DEFAULT NULL,
  `rol_cost` float UNSIGNED DEFAULT NULL,
  `rol_cost_period` smallint(6) DEFAULT NULL,
  `rol_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `rol_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rol_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `rol_timestamp_change` timestamp NULL DEFAULT NULL,
  `rol_valid` tinyint(1) NOT NULL DEFAULT '1',
  `rol_system` tinyint(1) NOT NULL DEFAULT '0',
  `rol_visible` tinyint(1) NOT NULL DEFAULT '1',
  `rol_webmaster` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_role_dependencies`
--

CREATE TABLE `adm_role_dependencies` (
  `rld_rol_id_parent` int(10) UNSIGNED NOT NULL,
  `rld_rol_id_child` int(10) UNSIGNED NOT NULL,
  `rld_comment` text COLLATE utf8_unicode_ci,
  `rld_usr_id` int(10) UNSIGNED DEFAULT NULL,
  `rld_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_rooms`
--

CREATE TABLE `adm_rooms` (
  `room_id` int(10) UNSIGNED NOT NULL,
  `room_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `room_description` text COLLATE utf8_unicode_ci,
  `room_capacity` int(11) NOT NULL,
  `room_overhang` int(11) DEFAULT NULL,
  `room_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `room_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `room_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `room_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_sessions`
--

CREATE TABLE `adm_sessions` (
  `ses_id` int(10) UNSIGNED NOT NULL,
  `ses_usr_id` int(10) UNSIGNED DEFAULT NULL,
  `ses_org_id` int(10) UNSIGNED NOT NULL,
  `ses_session_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ses_device_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ses_begin` timestamp NULL DEFAULT NULL,
  `ses_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ses_ip_address` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `ses_binary` blob,
  `ses_renew` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_texts`
--

CREATE TABLE `adm_texts` (
  `txt_id` int(10) UNSIGNED NOT NULL,
  `txt_org_id` int(10) UNSIGNED NOT NULL,
  `txt_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `txt_text` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_users`
--

CREATE TABLE `adm_users` (
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_login_name` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usr_password` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usr_new_password` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usr_photo` blob,
  `usr_text` text COLLATE utf8_unicode_ci,
  `usr_activation_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usr_last_login` timestamp NULL DEFAULT NULL,
  `usr_actual_login` timestamp NULL DEFAULT NULL,
  `usr_number_login` int(11) NOT NULL DEFAULT '0',
  `usr_date_invalid` timestamp NULL DEFAULT NULL,
  `usr_number_invalid` smallint(6) NOT NULL DEFAULT '0',
  `usr_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `usr_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usr_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `usr_timestamp_change` timestamp NULL DEFAULT NULL,
  `usr_valid` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_user_data`
--

CREATE TABLE `adm_user_data` (
  `usd_id` int(10) UNSIGNED NOT NULL,
  `usd_usr_id` int(10) UNSIGNED NOT NULL,
  `usd_usf_id` int(10) UNSIGNED NOT NULL,
  `usd_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_user_fields`
--

CREATE TABLE `adm_user_fields` (
  `usf_id` int(10) UNSIGNED NOT NULL,
  `usf_cat_id` int(10) UNSIGNED NOT NULL,
  `usf_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `usf_name_intern` varchar(110) COLLATE utf8_unicode_ci NOT NULL,
  `usf_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `usf_description` text COLLATE utf8_unicode_ci,
  `usf_value_list` mediumtext COLLATE utf8_unicode_ci,
  `usf_icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usf_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usf_system` tinyint(1) NOT NULL DEFAULT '0',
  `usf_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `usf_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `usf_mandatory` tinyint(1) NOT NULL DEFAULT '0',
  `usf_sequence` smallint(6) NOT NULL,
  `usf_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `usf_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usf_usr_id_change` int(10) UNSIGNED DEFAULT NULL,
  `usf_timestamp_change` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_user_log`
--

CREATE TABLE `adm_user_log` (
  `usl_id` int(11) NOT NULL,
  `usl_usr_id` int(10) UNSIGNED NOT NULL,
  `usl_usf_id` int(10) UNSIGNED NOT NULL,
  `usl_value_old` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usl_value_new` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usl_usr_id_create` int(10) UNSIGNED DEFAULT NULL,
  `usl_timestamp_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usl_comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `adm_announcements`
--
ALTER TABLE `adm_announcements`
  ADD PRIMARY KEY (`ann_id`),
  ADD KEY `adm_FK_ANN_ORG` (`ann_org_shortname`),
  ADD KEY `adm_FK_ANN_USR_CREATE` (`ann_usr_id_create`),
  ADD KEY `adm_FK_ANN_USR_CHANGE` (`ann_usr_id_change`);

--
-- Indizes für die Tabelle `adm_auto_login`
--
ALTER TABLE `adm_auto_login`
  ADD PRIMARY KEY (`atl_id`),
  ADD KEY `adm_FK_ATL_USR` (`atl_usr_id`),
  ADD KEY `adm_FK_ATL_ORG` (`atl_org_id`);

--
-- Indizes für die Tabelle `adm_categories`
--
ALTER TABLE `adm_categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `adm_FK_CAT_ORG` (`cat_org_id`),
  ADD KEY `adm_FK_CAT_USR_CREATE` (`cat_usr_id_create`),
  ADD KEY `adm_FK_CAT_USR_CHANGE` (`cat_usr_id_change`);

--
-- Indizes für die Tabelle `adm_dates`
--
ALTER TABLE `adm_dates`
  ADD PRIMARY KEY (`dat_id`),
  ADD KEY `adm_FK_DAT_CAT` (`dat_cat_id`),
  ADD KEY `adm_FK_DAT_ROL` (`dat_rol_id`),
  ADD KEY `adm_FK_DAT_ROOM` (`dat_room_id`),
  ADD KEY `adm_FK_DAT_USR_CREATE` (`dat_usr_id_create`),
  ADD KEY `adm_FK_DAT_USR_CHANGE` (`dat_usr_id_change`);

--
-- Indizes für die Tabelle `adm_date_role`
--
ALTER TABLE `adm_date_role`
  ADD PRIMARY KEY (`dtr_id`),
  ADD KEY `adm_FK_DTR_DAT` (`dtr_dat_id`),
  ADD KEY `adm_FK_DTR_ROL` (`dtr_rol_id`);

--
-- Indizes für die Tabelle `adm_drbv_wertungen`
--
ALTER TABLE `adm_drbv_wertungen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `adm_files`
--
ALTER TABLE `adm_files`
  ADD PRIMARY KEY (`fil_id`),
  ADD KEY `adm_FK_FIL_FOL` (`fil_fol_id`),
  ADD KEY `adm_FK_FIL_USR` (`fil_usr_id`);

--
-- Indizes für die Tabelle `adm_folders`
--
ALTER TABLE `adm_folders`
  ADD PRIMARY KEY (`fol_id`),
  ADD KEY `adm_FK_FOL_ORG` (`fol_org_id`),
  ADD KEY `adm_FK_FOL_FOL_PARENT` (`fol_fol_id_parent`),
  ADD KEY `adm_FK_FOL_USR` (`fol_usr_id`);

--
-- Indizes für die Tabelle `adm_folder_roles`
--
ALTER TABLE `adm_folder_roles`
  ADD PRIMARY KEY (`flr_fol_id`,`flr_rol_id`),
  ADD KEY `adm_FK_FLR_ROL` (`flr_rol_id`);

--
-- Indizes für die Tabelle `adm_guestbook`
--
ALTER TABLE `adm_guestbook`
  ADD PRIMARY KEY (`gbo_id`),
  ADD KEY `adm_FK_GBO_ORG` (`gbo_org_id`),
  ADD KEY `adm_FK_GBO_USR_CREATE` (`gbo_usr_id_create`),
  ADD KEY `adm_FK_GBO_USR_CHANGE` (`gbo_usr_id_change`);

--
-- Indizes für die Tabelle `adm_guestbook_comments`
--
ALTER TABLE `adm_guestbook_comments`
  ADD PRIMARY KEY (`gbc_id`),
  ADD KEY `adm_FK_GBC_GBO` (`gbc_gbo_id`),
  ADD KEY `adm_FK_GBC_USR_CREATE` (`gbc_usr_id_create`),
  ADD KEY `adm_FK_GBC_USR_CHANGE` (`gbc_usr_id_change`);

--
-- Indizes für die Tabelle `adm_links`
--
ALTER TABLE `adm_links`
  ADD PRIMARY KEY (`lnk_id`),
  ADD KEY `adm_FK_LNK_CAT` (`lnk_cat_id`),
  ADD KEY `adm_FK_LNK_USR_CREATE` (`lnk_usr_id_create`),
  ADD KEY `adm_FK_LNK_USR_CHANGE` (`lnk_usr_id_change`);

--
-- Indizes für die Tabelle `adm_lists`
--
ALTER TABLE `adm_lists`
  ADD PRIMARY KEY (`lst_id`),
  ADD KEY `adm_FK_LST_USR` (`lst_usr_id`),
  ADD KEY `adm_FK_LST_ORG` (`lst_org_id`);

--
-- Indizes für die Tabelle `adm_list_columns`
--
ALTER TABLE `adm_list_columns`
  ADD PRIMARY KEY (`lsc_id`),
  ADD KEY `adm_FK_LSC_LST` (`lsc_lst_id`),
  ADD KEY `adm_FK_LSC_USF` (`lsc_usf_id`);

--
-- Indizes für die Tabelle `adm_members`
--
ALTER TABLE `adm_members`
  ADD PRIMARY KEY (`mem_id`),
  ADD KEY `IDX_MEM_ROL_USR_ID` (`mem_rol_id`,`mem_usr_id`),
  ADD KEY `adm_FK_MEM_USR` (`mem_usr_id`),
  ADD KEY `adm_FK_MEM_USR_CREATE` (`mem_usr_id_create`),
  ADD KEY `adm_FK_MEM_USR_CHANGE` (`mem_usr_id_change`);

--
-- Indizes für die Tabelle `adm_organizations`
--
ALTER TABLE `adm_organizations`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `ak_shortname` (`org_shortname`),
  ADD KEY `adm_FK_ORG_ORG_PARENT` (`org_org_id_parent`);

--
-- Indizes für die Tabelle `adm_photos`
--
ALTER TABLE `adm_photos`
  ADD PRIMARY KEY (`pho_id`),
  ADD KEY `adm_FK_PHO_PHO_PARENT` (`pho_pho_id_parent`),
  ADD KEY `adm_FK_PHO_ORG` (`pho_org_shortname`),
  ADD KEY `adm_FK_PHO_USR_CREATE` (`pho_usr_id_create`),
  ADD KEY `adm_FK_PHO_USR_CHANGE` (`pho_usr_id_change`);

--
-- Indizes für die Tabelle `adm_preferences`
--
ALTER TABLE `adm_preferences`
  ADD PRIMARY KEY (`prf_id`),
  ADD UNIQUE KEY `IDX_PRF_ORG_ID_NAME` (`prf_org_id`,`prf_name`);

--
-- Indizes für die Tabelle `adm_registrations`
--
ALTER TABLE `adm_registrations`
  ADD PRIMARY KEY (`reg_id`),
  ADD KEY `adm_FK_REG_ORG` (`reg_org_id`),
  ADD KEY `adm_FK_REG_USR` (`reg_usr_id`);

--
-- Indizes für die Tabelle `adm_roles`
--
ALTER TABLE `adm_roles`
  ADD PRIMARY KEY (`rol_id`),
  ADD KEY `adm_FK_ROL_CAT` (`rol_cat_id`),
  ADD KEY `adm_FK_ROL_LST_ID` (`rol_lst_id`),
  ADD KEY `adm_FK_ROL_USR_CREATE` (`rol_usr_id_create`),
  ADD KEY `adm_FK_ROL_USR_CHANGE` (`rol_usr_id_change`);

--
-- Indizes für die Tabelle `adm_role_dependencies`
--
ALTER TABLE `adm_role_dependencies`
  ADD PRIMARY KEY (`rld_rol_id_parent`,`rld_rol_id_child`),
  ADD KEY `adm_FK_RLD_ROL_CHILD` (`rld_rol_id_child`),
  ADD KEY `adm_FK_RLD_USR` (`rld_usr_id`);

--
-- Indizes für die Tabelle `adm_rooms`
--
ALTER TABLE `adm_rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `adm_FK_ROOM_USR_CREATE` (`room_usr_id_create`),
  ADD KEY `adm_FK_ROOM_USR_CHANGE` (`room_usr_id_change`);

--
-- Indizes für die Tabelle `adm_sessions`
--
ALTER TABLE `adm_sessions`
  ADD PRIMARY KEY (`ses_id`),
  ADD KEY `IDX_SESSION_ID` (`ses_session_id`),
  ADD KEY `adm_FK_SES_ORG` (`ses_org_id`),
  ADD KEY `adm_FK_SES_USR` (`ses_usr_id`);

--
-- Indizes für die Tabelle `adm_texts`
--
ALTER TABLE `adm_texts`
  ADD PRIMARY KEY (`txt_id`),
  ADD KEY `adm_FK_TXT_ORG` (`txt_org_id`);

--
-- Indizes für die Tabelle `adm_users`
--
ALTER TABLE `adm_users`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `IDX_USR_LOGIN_NAME` (`usr_login_name`),
  ADD KEY `adm_FK_USR_USR_CREATE` (`usr_usr_id_create`),
  ADD KEY `adm_FK_USR_USR_CHANGE` (`usr_usr_id_change`);

--
-- Indizes für die Tabelle `adm_user_data`
--
ALTER TABLE `adm_user_data`
  ADD PRIMARY KEY (`usd_id`),
  ADD UNIQUE KEY `IDX_USD_USR_USF_ID` (`usd_usr_id`,`usd_usf_id`),
  ADD KEY `adm_FK_USD_USF` (`usd_usf_id`);

--
-- Indizes für die Tabelle `adm_user_fields`
--
ALTER TABLE `adm_user_fields`
  ADD PRIMARY KEY (`usf_id`),
  ADD UNIQUE KEY `IDX_USF_NAME_INTERN` (`usf_name_intern`),
  ADD KEY `adm_FK_USF_CAT` (`usf_cat_id`),
  ADD KEY `adm_FK_USF_USR_CREATE` (`usf_usr_id_create`),
  ADD KEY `adm_FK_USF_USR_CHANGE` (`usf_usr_id_change`);

--
-- Indizes für die Tabelle `adm_user_log`
--
ALTER TABLE `adm_user_log`
  ADD PRIMARY KEY (`usl_id`),
  ADD KEY `adm_FK_USER_LOG_1` (`usl_usr_id`),
  ADD KEY `adm_FK_USER_LOG_2` (`usl_usr_id_create`),
  ADD KEY `adm_FK_USER_LOG_3` (`usl_usf_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `adm_announcements`
--
ALTER TABLE `adm_announcements`
  MODIFY `ann_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_auto_login`
--
ALTER TABLE `adm_auto_login`
  MODIFY `atl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_categories`
--
ALTER TABLE `adm_categories`
  MODIFY `cat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_dates`
--
ALTER TABLE `adm_dates`
  MODIFY `dat_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_date_role`
--
ALTER TABLE `adm_date_role`
  MODIFY `dtr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_drbv_wertungen`
--
ALTER TABLE `adm_drbv_wertungen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_files`
--
ALTER TABLE `adm_files`
  MODIFY `fil_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_folders`
--
ALTER TABLE `adm_folders`
  MODIFY `fol_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_guestbook`
--
ALTER TABLE `adm_guestbook`
  MODIFY `gbo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_guestbook_comments`
--
ALTER TABLE `adm_guestbook_comments`
  MODIFY `gbc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_links`
--
ALTER TABLE `adm_links`
  MODIFY `lnk_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_lists`
--
ALTER TABLE `adm_lists`
  MODIFY `lst_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_list_columns`
--
ALTER TABLE `adm_list_columns`
  MODIFY `lsc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_members`
--
ALTER TABLE `adm_members`
  MODIFY `mem_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_organizations`
--
ALTER TABLE `adm_organizations`
  MODIFY `org_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_photos`
--
ALTER TABLE `adm_photos`
  MODIFY `pho_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_preferences`
--
ALTER TABLE `adm_preferences`
  MODIFY `prf_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_registrations`
--
ALTER TABLE `adm_registrations`
  MODIFY `reg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_roles`
--
ALTER TABLE `adm_roles`
  MODIFY `rol_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_rooms`
--
ALTER TABLE `adm_rooms`
  MODIFY `room_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_sessions`
--
ALTER TABLE `adm_sessions`
  MODIFY `ses_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_texts`
--
ALTER TABLE `adm_texts`
  MODIFY `txt_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_users`
--
ALTER TABLE `adm_users`
  MODIFY `usr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_user_data`
--
ALTER TABLE `adm_user_data`
  MODIFY `usd_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_user_fields`
--
ALTER TABLE `adm_user_fields`
  MODIFY `usf_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `adm_user_log`
--
ALTER TABLE `adm_user_log`
  MODIFY `usl_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `adm_announcements`
--
ALTER TABLE `adm_announcements`
  ADD CONSTRAINT `adm_FK_ANN_ORG` FOREIGN KEY (`ann_org_shortname`) REFERENCES `adm_organizations` (`org_shortname`),
  ADD CONSTRAINT `adm_FK_ANN_USR_CHANGE` FOREIGN KEY (`ann_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_ANN_USR_CREATE` FOREIGN KEY (`ann_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_auto_login`
--
ALTER TABLE `adm_auto_login`
  ADD CONSTRAINT `adm_FK_ATL_ORG` FOREIGN KEY (`atl_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_ATL_USR` FOREIGN KEY (`atl_usr_id`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_categories`
--
ALTER TABLE `adm_categories`
  ADD CONSTRAINT `adm_FK_CAT_ORG` FOREIGN KEY (`cat_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_CAT_USR_CHANGE` FOREIGN KEY (`cat_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_CAT_USR_CREATE` FOREIGN KEY (`cat_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_dates`
--
ALTER TABLE `adm_dates`
  ADD CONSTRAINT `adm_FK_DAT_CAT` FOREIGN KEY (`dat_cat_id`) REFERENCES `adm_categories` (`cat_id`),
  ADD CONSTRAINT `adm_FK_DAT_ROL` FOREIGN KEY (`dat_rol_id`) REFERENCES `adm_roles` (`rol_id`),
  ADD CONSTRAINT `adm_FK_DAT_ROOM` FOREIGN KEY (`dat_room_id`) REFERENCES `adm_rooms` (`room_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_DAT_USR_CHANGE` FOREIGN KEY (`dat_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_DAT_USR_CREATE` FOREIGN KEY (`dat_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_date_role`
--
ALTER TABLE `adm_date_role`
  ADD CONSTRAINT `adm_FK_DTR_DAT` FOREIGN KEY (`dtr_dat_id`) REFERENCES `adm_dates` (`dat_id`),
  ADD CONSTRAINT `adm_FK_DTR_ROL` FOREIGN KEY (`dtr_rol_id`) REFERENCES `adm_roles` (`rol_id`);

--
-- Constraints der Tabelle `adm_files`
--
ALTER TABLE `adm_files`
  ADD CONSTRAINT `adm_FK_FIL_FOL` FOREIGN KEY (`fil_fol_id`) REFERENCES `adm_folders` (`fol_id`),
  ADD CONSTRAINT `adm_FK_FIL_USR` FOREIGN KEY (`fil_usr_id`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_folders`
--
ALTER TABLE `adm_folders`
  ADD CONSTRAINT `adm_FK_FOL_FOL_PARENT` FOREIGN KEY (`fol_fol_id_parent`) REFERENCES `adm_folders` (`fol_id`),
  ADD CONSTRAINT `adm_FK_FOL_ORG` FOREIGN KEY (`fol_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_FOL_USR` FOREIGN KEY (`fol_usr_id`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_folder_roles`
--
ALTER TABLE `adm_folder_roles`
  ADD CONSTRAINT `adm_FK_FLR_FOL` FOREIGN KEY (`flr_fol_id`) REFERENCES `adm_folders` (`fol_id`),
  ADD CONSTRAINT `adm_FK_FLR_ROL` FOREIGN KEY (`flr_rol_id`) REFERENCES `adm_roles` (`rol_id`);

--
-- Constraints der Tabelle `adm_guestbook`
--
ALTER TABLE `adm_guestbook`
  ADD CONSTRAINT `adm_FK_GBO_ORG` FOREIGN KEY (`gbo_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_GBO_USR_CHANGE` FOREIGN KEY (`gbo_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_GBO_USR_CREATE` FOREIGN KEY (`gbo_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_guestbook_comments`
--
ALTER TABLE `adm_guestbook_comments`
  ADD CONSTRAINT `adm_FK_GBC_GBO` FOREIGN KEY (`gbc_gbo_id`) REFERENCES `adm_guestbook` (`gbo_id`),
  ADD CONSTRAINT `adm_FK_GBC_USR_CHANGE` FOREIGN KEY (`gbc_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_GBC_USR_CREATE` FOREIGN KEY (`gbc_usr_id_create`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_links`
--
ALTER TABLE `adm_links`
  ADD CONSTRAINT `adm_FK_LNK_CAT` FOREIGN KEY (`lnk_cat_id`) REFERENCES `adm_categories` (`cat_id`),
  ADD CONSTRAINT `adm_FK_LNK_USR_CHANGE` FOREIGN KEY (`lnk_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_LNK_USR_CREATE` FOREIGN KEY (`lnk_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_lists`
--
ALTER TABLE `adm_lists`
  ADD CONSTRAINT `adm_FK_LST_ORG` FOREIGN KEY (`lst_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_LST_USR` FOREIGN KEY (`lst_usr_id`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_list_columns`
--
ALTER TABLE `adm_list_columns`
  ADD CONSTRAINT `adm_FK_LSC_LST` FOREIGN KEY (`lsc_lst_id`) REFERENCES `adm_lists` (`lst_id`),
  ADD CONSTRAINT `adm_FK_LSC_USF` FOREIGN KEY (`lsc_usf_id`) REFERENCES `adm_user_fields` (`usf_id`);

--
-- Constraints der Tabelle `adm_members`
--
ALTER TABLE `adm_members`
  ADD CONSTRAINT `adm_FK_MEM_ROL` FOREIGN KEY (`mem_rol_id`) REFERENCES `adm_roles` (`rol_id`),
  ADD CONSTRAINT `adm_FK_MEM_USR` FOREIGN KEY (`mem_usr_id`) REFERENCES `adm_users` (`usr_id`),
  ADD CONSTRAINT `adm_FK_MEM_USR_CHANGE` FOREIGN KEY (`mem_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_MEM_USR_CREATE` FOREIGN KEY (`mem_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_organizations`
--
ALTER TABLE `adm_organizations`
  ADD CONSTRAINT `adm_FK_ORG_ORG_PARENT` FOREIGN KEY (`org_org_id_parent`) REFERENCES `adm_organizations` (`org_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_photos`
--
ALTER TABLE `adm_photos`
  ADD CONSTRAINT `adm_FK_PHO_ORG` FOREIGN KEY (`pho_org_shortname`) REFERENCES `adm_organizations` (`org_shortname`),
  ADD CONSTRAINT `adm_FK_PHO_PHO_PARENT` FOREIGN KEY (`pho_pho_id_parent`) REFERENCES `adm_photos` (`pho_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_PHO_USR_CHANGE` FOREIGN KEY (`pho_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_PHO_USR_CREATE` FOREIGN KEY (`pho_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_preferences`
--
ALTER TABLE `adm_preferences`
  ADD CONSTRAINT `adm_FK_PRF_ORG` FOREIGN KEY (`prf_org_id`) REFERENCES `adm_organizations` (`org_id`);

--
-- Constraints der Tabelle `adm_registrations`
--
ALTER TABLE `adm_registrations`
  ADD CONSTRAINT `adm_FK_REG_ORG` FOREIGN KEY (`reg_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_REG_USR` FOREIGN KEY (`reg_usr_id`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_roles`
--
ALTER TABLE `adm_roles`
  ADD CONSTRAINT `adm_FK_ROL_CAT` FOREIGN KEY (`rol_cat_id`) REFERENCES `adm_categories` (`cat_id`),
  ADD CONSTRAINT `adm_FK_ROL_LST_ID` FOREIGN KEY (`rol_lst_id`) REFERENCES `adm_lists` (`lst_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `adm_FK_ROL_USR_CHANGE` FOREIGN KEY (`rol_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_ROL_USR_CREATE` FOREIGN KEY (`rol_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_role_dependencies`
--
ALTER TABLE `adm_role_dependencies`
  ADD CONSTRAINT `adm_FK_RLD_ROL_CHILD` FOREIGN KEY (`rld_rol_id_child`) REFERENCES `adm_roles` (`rol_id`),
  ADD CONSTRAINT `adm_FK_RLD_ROL_PARENT` FOREIGN KEY (`rld_rol_id_parent`) REFERENCES `adm_roles` (`rol_id`),
  ADD CONSTRAINT `adm_FK_RLD_USR` FOREIGN KEY (`rld_usr_id`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_rooms`
--
ALTER TABLE `adm_rooms`
  ADD CONSTRAINT `adm_FK_ROOM_USR_CHANGE` FOREIGN KEY (`room_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_ROOM_USR_CREATE` FOREIGN KEY (`room_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_sessions`
--
ALTER TABLE `adm_sessions`
  ADD CONSTRAINT `adm_FK_SES_ORG` FOREIGN KEY (`ses_org_id`) REFERENCES `adm_organizations` (`org_id`),
  ADD CONSTRAINT `adm_FK_SES_USR` FOREIGN KEY (`ses_usr_id`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_texts`
--
ALTER TABLE `adm_texts`
  ADD CONSTRAINT `adm_FK_TXT_ORG` FOREIGN KEY (`txt_org_id`) REFERENCES `adm_organizations` (`org_id`);

--
-- Constraints der Tabelle `adm_users`
--
ALTER TABLE `adm_users`
  ADD CONSTRAINT `adm_FK_USR_USR_CHANGE` FOREIGN KEY (`usr_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_USR_USR_CREATE` FOREIGN KEY (`usr_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_user_data`
--
ALTER TABLE `adm_user_data`
  ADD CONSTRAINT `adm_FK_USD_USF` FOREIGN KEY (`usd_usf_id`) REFERENCES `adm_user_fields` (`usf_id`),
  ADD CONSTRAINT `adm_FK_USD_USR` FOREIGN KEY (`usd_usr_id`) REFERENCES `adm_users` (`usr_id`);

--
-- Constraints der Tabelle `adm_user_fields`
--
ALTER TABLE `adm_user_fields`
  ADD CONSTRAINT `adm_FK_USF_CAT` FOREIGN KEY (`usf_cat_id`) REFERENCES `adm_categories` (`cat_id`),
  ADD CONSTRAINT `adm_FK_USF_USR_CHANGE` FOREIGN KEY (`usf_usr_id_change`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adm_FK_USF_USR_CREATE` FOREIGN KEY (`usf_usr_id_create`) REFERENCES `adm_users` (`usr_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `adm_user_log`
--
ALTER TABLE `adm_user_log`
  ADD CONSTRAINT `adm_FK_USER_LOG_1` FOREIGN KEY (`usl_usr_id`) REFERENCES `adm_users` (`usr_id`),
  ADD CONSTRAINT `adm_FK_USER_LOG_2` FOREIGN KEY (`usl_usr_id_create`) REFERENCES `adm_users` (`usr_id`),
  ADD CONSTRAINT `adm_FK_USER_LOG_3` FOREIGN KEY (`usl_usf_id`) REFERENCES `adm_user_fields` (`usf_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
