-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 
-- Erstellungszeit: 20. Feb 2019 um 16:28
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
-- Datenbank: `turnierergebnisse`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auswertung`
--

CREATE TABLE `auswertung` (
  `id` int(11) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `aus_id` int(3) NOT NULL,
  `pr_id` int(3) NOT NULL,
  `wr_id` int(2) NOT NULL,
  `punkte` double DEFAULT NULL,
  `platz` double NOT NULL,
  `reihenfolge` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `majoritaet`
--

CREATE TABLE `majoritaet` (
  `id` int(11) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `RT_ID` int(3) NOT NULL,
  `TP_ID` int(3) NOT NULL,
  `DQ_ID` int(3) NOT NULL,
  `PA_ID` int(3) NOT NULL,
  `Anmerkung` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `WR1_ID` int(3) NOT NULL,
  `WR1_Orig_Punkte` double DEFAULT NULL,
  `WR1_Orig_Platz` int(3) NOT NULL,
  `WR1_Punkte` double DEFAULT NULL,
  `WR1_Platz` int(3) NOT NULL,
  `WR1` double DEFAULT NULL,
  `WR2_ID` int(3) NOT NULL,
  `WR2_Orig_Punkte` double DEFAULT NULL,
  `WR2_Orig_Platz` int(3) NOT NULL,
  `WR2_Punkte` double DEFAULT NULL,
  `WR2_Platz` int(3) NOT NULL,
  `WR2` double DEFAULT NULL,
  `WR3_ID` int(3) NOT NULL,
  `WR3_Orig_Punkte` double DEFAULT NULL,
  `WR3_Orig_Platz` int(3) NOT NULL,
  `WR3_Punkte` double DEFAULT NULL,
  `WR3_Platz` int(3) NOT NULL,
  `WR3` double DEFAULT NULL,
  `WR4_ID` int(3) NOT NULL,
  `WR4_Orig_Punkte` double DEFAULT NULL,
  `WR4_Orig_Platz` int(3) NOT NULL,
  `WR4_Punkte` double DEFAULT NULL,
  `WR4_Platz` int(3) NOT NULL,
  `WR4` double DEFAULT NULL,
  `WR5_ID` int(3) NOT NULL,
  `WR5_Orig_Punkte` double DEFAULT NULL,
  `WR5_Orig_Platz` int(3) NOT NULL,
  `WR5_Punkte` double DEFAULT NULL,
  `WR5_Platz` int(3) NOT NULL,
  `WR5` double DEFAULT NULL,
  `WR6_ID` int(3) NOT NULL,
  `WR6_Orig_Punkte` double DEFAULT NULL,
  `WR6_Orig_Platz` int(3) NOT NULL,
  `WR6_Punkte` double DEFAULT NULL,
  `WR6_Platz` int(3) NOT NULL,
  `WR6` double DEFAULT NULL,
  `WR7_ID` int(3) NOT NULL,
  `WR7_Orig_Punkte` double DEFAULT NULL,
  `WR7_Orig_Platz` int(3) NOT NULL,
  `WR7_Punkte` double DEFAULT NULL,
  `WR7_Platz` int(3) NOT NULL,
  `WR7` double DEFAULT NULL,
  `Platz` int(2) NOT NULL,
  `Platz_Orig` int(2) NOT NULL,
  `RT_ID_weiter` int(2) NOT NULL,
  `Runde_Report` int(2) NOT NULL,
  `KO_Sieger` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `paare`
--

CREATE TABLE `paare` (
  `id_paare` int(11) NOT NULL,
  `paar_id_tlp` int(3) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `startklasse` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `startnummer` int(3) NOT NULL,
  `dame` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `herr` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `team` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `startbuch` int(5) NOT NULL,
  `boogie_sb_herr` int(5) NOT NULL,
  `boogie_sb_dame` int(5) NOT NULL,
  `platz` int(3) NOT NULL,
  `punkte` int(2) NOT NULL,
  `rl_punkte` int(3) NOT NULL,
  `RT_ID_Ausgeschieden` int(2) NOT NULL,
  `Akro1_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert1_VR` float NOT NULL,
  `Akro2_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert2_VR` float NOT NULL,
  `Akro3_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert3_VR` float NOT NULL,
  `Akro4_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert4_VR` float NOT NULL,
  `Akro5_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert5_VR` float NOT NULL,
  `Akro6_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert6_VR` float NOT NULL,
  `Akro7_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert7_VR` float NOT NULL,
  `Akro8_VR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert8_VR` float NOT NULL,
  `Akro1_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert1_ZR` float NOT NULL,
  `Akro2_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert2_ZR` float NOT NULL,
  `Akro3_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert3_ZR` float NOT NULL,
  `Akro4_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert4_ZR` float NOT NULL,
  `Akro5_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert5_ZR` float NOT NULL,
  `Akro6_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert6_ZR` float NOT NULL,
  `Akro7_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert7_ZR` float NOT NULL,
  `Akro8_ZR` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert8_ZR` float NOT NULL,
  `Akro1_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert1_ER` float NOT NULL,
  `Akro2_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert2_ER` float NOT NULL,
  `Akro3_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert3_ER` float NOT NULL,
  `Akro4_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert4_ER` float NOT NULL,
  `Akro5_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert5_ER` float NOT NULL,
  `Akro6_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert6_ER` float NOT NULL,
  `Akro7_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert7_ER` float NOT NULL,
  `Akro8_ER` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Wert8_ER` float NOT NULL,
  `anzahl_taenzer` int(2) DEFAULT NULL,
  `verein` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `cup_serie` varchar(15) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pwd_db`
--

CREATE TABLE `pwd_db` (
  `id` int(11) NOT NULL,
  `startbuch_nr` int(5) NOT NULL,
  `passwort` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundenquali`
--

CREATE TABLE `rundenquali` (
  `id` int(11) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `pr_id` int(3) NOT NULL,
  `rt_id` int(3) NOT NULL,
  `tp_id` int(3) NOT NULL,
  `auslosung` int(2) NOT NULL,
  `rundennummer` int(2) NOT NULL,
  `anwesend` int(1) NOT NULL,
  `nochmal` int(1) NOT NULL,
  `ko_sieger` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rundentab`
--

CREATE TABLE `rundentab` (
  `rt_id` int(11) NOT NULL,
  `rt_id_tlp` int(2) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `startklasse` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `runde` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `runden_rf` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Turnier`
--

CREATE TABLE `Turnier` (
  `id_turnier` int(11) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `turniername` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `veranstalter_nr` int(5) NOT NULL,
  `veranstalter_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `veranstaltung_ort` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `T_Leiter`
--

CREATE TABLE `T_Leiter` (
  `id_leiter` int(11) NOT NULL,
  `tl_id_tlp` int(2) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `lizenznummer` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `funktion` varchar(10) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wertungen`
--

CREATE TABLE `wertungen` (
  `wert_id` int(11) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `paar_id_tlp` int(3) NOT NULL,
  `rh` int(2) NOT NULL,
  `wr_id` int(2) NOT NULL,
  `rund_tab_id` int(3) NOT NULL,
  `herr_gt` float NOT NULL,
  `herr_halt_dt` float NOT NULL,
  `dame_gt` float NOT NULL,
  `dame_halt_dt` float NOT NULL,
  `choreo` float NOT NULL,
  `tanzfiguren` float NOT NULL,
  `taenz_darbietung` float NOT NULL,
  `grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `grobfehler_summe` float NOT NULL,
  `akro1` float NOT NULL,
  `akro1_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro1_grobfehler_summe` float NOT NULL,
  `akro2` float NOT NULL,
  `akro2_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro2_grobfehler_summe` float NOT NULL,
  `akro3` float NOT NULL,
  `akro3_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro3_grobfehler_summe` float NOT NULL,
  `akro4` float NOT NULL,
  `akro4_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro4_grobfehler_summe` float NOT NULL,
  `akro5` float NOT NULL,
  `akro5_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro5_grobfehler_summe` float NOT NULL,
  `akro6` float NOT NULL,
  `akro6_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro6_grobfehler_summe` float NOT NULL,
  `akro7` float NOT NULL,
  `akro7_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro7_grobfehler_summe` float NOT NULL,
  `akro8` float NOT NULL,
  `akro8_grobfehler_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `akro8_grobfehler_summe` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wertungsrichter`
--

CREATE TABLE `wertungsrichter` (
  `id_wr` int(11) NOT NULL,
  `wr_id_tlp` int(2) NOT NULL,
  `turniernummer` int(7) NOT NULL,
  `lizenznummer` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `kuerzel` varchar(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `auswertung`
--
ALTER TABLE `auswertung`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `majoritaet`
--
ALTER TABLE `majoritaet`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `paare`
--
ALTER TABLE `paare`
  ADD PRIMARY KEY (`id_paare`);

--
-- Indizes für die Tabelle `pwd_db`
--
ALTER TABLE `pwd_db`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `startbuch_nr` (`startbuch_nr`);

--
-- Indizes für die Tabelle `rundenquali`
--
ALTER TABLE `rundenquali`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rundentab`
--
ALTER TABLE `rundentab`
  ADD PRIMARY KEY (`rt_id`);

--
-- Indizes für die Tabelle `Turnier`
--
ALTER TABLE `Turnier`
  ADD PRIMARY KEY (`id_turnier`),
  ADD UNIQUE KEY `id_turnier` (`id_turnier`);

--
-- Indizes für die Tabelle `T_Leiter`
--
ALTER TABLE `T_Leiter`
  ADD PRIMARY KEY (`id_leiter`),
  ADD KEY `turniernummer` (`turniernummer`);

--
-- Indizes für die Tabelle `wertungen`
--
ALTER TABLE `wertungen`
  ADD PRIMARY KEY (`wert_id`);

--
-- Indizes für die Tabelle `wertungsrichter`
--
ALTER TABLE `wertungsrichter`
  ADD PRIMARY KEY (`id_wr`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `auswertung`
--
ALTER TABLE `auswertung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `majoritaet`
--
ALTER TABLE `majoritaet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `paare`
--
ALTER TABLE `paare`
  MODIFY `id_paare` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `pwd_db`
--
ALTER TABLE `pwd_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `rundenquali`
--
ALTER TABLE `rundenquali`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `rundentab`
--
ALTER TABLE `rundentab`
  MODIFY `rt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Turnier`
--
ALTER TABLE `Turnier`
  MODIFY `id_turnier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `T_Leiter`
--
ALTER TABLE `T_Leiter`
  MODIFY `id_leiter` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `wertungen`
--
ALTER TABLE `wertungen`
  MODIFY `wert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `wertungsrichter`
--
ALTER TABLE `wertungsrichter`
  MODIFY `id_wr` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
