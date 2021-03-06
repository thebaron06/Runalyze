-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 25. Mai 2015 um 14:52
-- Server Version: 5.6.21
-- PHP-Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `runalyze`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_account`
--

CREATE TABLE IF NOT EXISTS `runalyze_account` (
`id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `mail` varchar(100) NOT NULL,
  `language` varchar(3) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `salt` char(64) NOT NULL DEFAULT '',
  `session_id` varchar(32) DEFAULT NULL,
  `registerdate` int(11) NOT NULL DEFAULT '0',
  `lastaction` int(11) NOT NULL DEFAULT '0',
  `lastlogin` int(11) NOT NULL DEFAULT '0',
  `autologin_hash` varchar(32) NOT NULL DEFAULT '',
  `changepw_hash` varchar(32) NOT NULL DEFAULT '',
  `changepw_timelimit` int(11) NOT NULL DEFAULT '0',
  `activation_hash` varchar(32) NOT NULL DEFAULT '',
  `deletion_hash` varchar(32) NOT NULL DEFAULT '',
  `allow_mails` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Trigger `runalyze_account`
--
DROP TRIGGER IF EXISTS `del_tr_train`;
DELIMITER //
CREATE TRIGGER `del_tr_train` AFTER DELETE ON `runalyze_account`
 FOR EACH ROW BEGIN
		DELETE FROM runalyze_clothes WHERE accountid = OLD.id;
		DELETE FROM runalyze_conf WHERE accountid = OLD.id;
		DELETE FROM runalyze_dataset WHERE accountid = OLD.id;
		DELETE FROM runalyze_plugin WHERE accountid = OLD.id;
		DELETE FROM runalyze_shoe WHERE accountid = OLD.id;
		DELETE FROM runalyze_sport WHERE accountid = OLD.id;
		DELETE FROM runalyze_training WHERE accountid = OLD.id;
		DELETE FROM runalyze_type WHERE accountid = OLD.id;
		DELETE FROM runalyze_user WHERE accountid = OLD.id;
	END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_clothes`
--

CREATE TABLE IF NOT EXISTS `runalyze_clothes` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short` varchar(20) NOT NULL DEFAULT '',
  `order` tinyint(1) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_conf`
--

CREATE TABLE IF NOT EXISTS `runalyze_conf` (
`id` int(11) NOT NULL,
  `category` varchar(32) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_dataset`
--

CREATE TABLE IF NOT EXISTS `runalyze_dataset` (
`id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modus` tinyint(1) NOT NULL DEFAULT '0',
  `class` varchar(25) NOT NULL DEFAULT '',
  `style` varchar(100) NOT NULL DEFAULT '',
  `position` smallint(6) NOT NULL DEFAULT '0',
  `summary` tinyint(1) NOT NULL DEFAULT '0',
  `summary_mode` varchar(3) NOT NULL DEFAULT 'SUM',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_hrv`
--

CREATE TABLE IF NOT EXISTS `runalyze_hrv` (
  `accountid` int(10) unsigned NOT NULL,
  `activityid` int(10) unsigned NOT NULL,
  `data` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_plugin`
--

CREATE TABLE IF NOT EXISTS `runalyze_plugin` (
`id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `type` enum('panel','stat','tool') NOT NULL DEFAULT 'stat',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `order` smallint(6) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_plugin_conf`
--

CREATE TABLE IF NOT EXISTS `runalyze_plugin_conf` (
`id` int(10) unsigned NOT NULL,
  `pluginid` int(10) unsigned NOT NULL,
  `config` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_route`
--

CREATE TABLE IF NOT EXISTS `runalyze_route` (
`id` int(10) unsigned NOT NULL,
  `accountid` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cities` varchar(255) NOT NULL DEFAULT '',
  `distance` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `elevation` smallint(5) unsigned NOT NULL DEFAULT '0',
  `elevation_up` smallint(5) unsigned NOT NULL DEFAULT '0',
  `elevation_down` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lats` longtext,
  `lngs` longtext,
  `elevations_original` longtext,
  `elevations_corrected` longtext,
  `elevations_source` varchar(255) NOT NULL DEFAULT '',
  `startpoint_lat` float(8,5) DEFAULT NULL,
  `startpoint_lng` float(8,5) DEFAULT NULL,
  `endpoint_lat` float(8,5) DEFAULT NULL,
  `endpoint_lng` float(8,5) DEFAULT NULL,
  `min_lat` float(8,5) DEFAULT NULL,
  `min_lng` float(8,5) DEFAULT NULL,
  `max_lat` float(8,5) DEFAULT NULL,
  `max_lng` float(8,5) DEFAULT NULL,
  `in_routenet` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_shoe`
--

CREATE TABLE IF NOT EXISTS `runalyze_shoe` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `since` varchar(10) NOT NULL DEFAULT '01.01.2000',
  `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
  `km` decimal(6,2) NOT NULL DEFAULT '0.00',
  `time` int(11) NOT NULL DEFAULT '0',
  `inuse` tinyint(1) NOT NULL DEFAULT '1',
  `additionalKm` decimal(6,2) NOT NULL DEFAULT '0.00',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_sport`
--

CREATE TABLE IF NOT EXISTS `runalyze_sport` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `img` varchar(100) NOT NULL DEFAULT 'unknown.gif',
  `short` tinyint(1) NOT NULL DEFAULT '0',
  `kcal` smallint(4) NOT NULL DEFAULT '0',
  `HFavg` smallint(3) NOT NULL DEFAULT '120',
  `distances` tinyint(1) NOT NULL DEFAULT '1',
  `speed` varchar(10) NOT NULL DEFAULT 'min/km',
  `types` tinyint(1) NOT NULL DEFAULT '0',
  `power` tinyint(1) NOT NULL DEFAULT '0',
  `outside` tinyint(1) NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_trackdata`
--

CREATE TABLE IF NOT EXISTS `runalyze_trackdata` (
  `accountid` int(10) unsigned NOT NULL,
  `activityid` int(10) unsigned NOT NULL,
  `time` longtext,
  `distance` longtext,
  `heartrate` longtext,
  `cadence` longtext,
  `power` longtext,
  `temperature` longtext,
  `groundcontact` longtext,
  `vertical_oscillation` longtext,
  `pauses` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_training`
--

CREATE TABLE IF NOT EXISTS `runalyze_training` (
`id` int(11) NOT NULL,
  `sportid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `edited` int(11) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_track` tinyint(1) NOT NULL DEFAULT '0',
  `distance` decimal(6,2) NOT NULL DEFAULT '0.00',
  `s` decimal(8,2) NOT NULL DEFAULT '0.00',
  `elapsed_time` int(6) NOT NULL DEFAULT '0',
  `elevation` int(5) NOT NULL DEFAULT '0',
  `kcal` int(5) NOT NULL DEFAULT '0',
  `pulse_avg` int(3) NOT NULL DEFAULT '0',
  `pulse_max` int(3) NOT NULL DEFAULT '0',
  `vdot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `vdot_by_time` decimal(5,2) NOT NULL DEFAULT '0.00',
  `vdot_with_elevation` decimal(5,2) NOT NULL DEFAULT '0.00',
  `use_vdot` tinyint(1) NOT NULL DEFAULT '1',
  `fit_vdot_estimate` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fit_recovery_time` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fit_hrv_analysis` smallint(5) unsigned NOT NULL DEFAULT '0',
  `jd_intensity` smallint(4) NOT NULL DEFAULT '0',
  `trimp` int(4) NOT NULL DEFAULT '0',
  `cadence` int(3) NOT NULL DEFAULT '0',
  `power` int(4) NOT NULL DEFAULT '0',
  `total_strokes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `swolf` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stride_length` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `groundcontact` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vertical_oscillation` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `temperature` tinyint(4) DEFAULT NULL,
  `weatherid` smallint(6) NOT NULL DEFAULT '1',
  `route` text,
  `routeid` int(10) unsigned NOT NULL DEFAULT '0',
  `clothes` varchar(100) NOT NULL DEFAULT '',
  `splits` mediumtext,
  `comment` text,
  `partner` text,
  `abc` smallint(1) NOT NULL DEFAULT '0',
  `shoeid` int(11) NOT NULL DEFAULT '0',
  `notes` text,
  `accountid` int(11) NOT NULL,
  `creator` varchar(100) NOT NULL DEFAULT '',
  `creator_details` tinytext,
  `activity_id` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_type`
--

CREATE TABLE IF NOT EXISTS `runalyze_type` (
`id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL DEFAULT '',
  `sportid` int(11) NOT NULL DEFAULT '0',
  `short` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hr_avg` tinyint(3) unsigned NOT NULL DEFAULT '100',
  `quality_session` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_user`
--

CREATE TABLE IF NOT EXISTS `runalyze_user` (
`id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `weight` decimal(4,1) NOT NULL DEFAULT '0.0',
  `pulse_rest` smallint(3) NOT NULL DEFAULT '0',
  `pulse_max` smallint(3) NOT NULL DEFAULT '0',
  `fat` decimal(3,1) NOT NULL DEFAULT '0.0',
  `water` decimal(3,1) NOT NULL DEFAULT '0.0',
  `muscles` decimal(3,1) NOT NULL DEFAULT '0.0',
  `sleep_duration` smallint(3) unsigned NOT NULL DEFAULT '0',
  `notes` text,
  `accountid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runalyze_swimdata`
--

CREATE TABLE IF NOT EXISTS `runalyze_swimdata` (
  `accountid` int(10) unsigned NOT NULL,
  `activityid` int(10) unsigned NOT NULL,
  `stroke` longtext,
  `stroketype` longtext,
  `pool_length` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `runalyze_account`
--
ALTER TABLE `runalyze_account`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `mail` (`mail`), ADD UNIQUE KEY `session_id` (`session_id`);

--
-- Indizes für die Tabelle `runalyze_clothes`
--
ALTER TABLE `runalyze_clothes`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_conf`
--
ALTER TABLE `runalyze_conf`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_dataset`
--
ALTER TABLE `runalyze_dataset`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_hrv`
--
ALTER TABLE `runalyze_hrv`
 ADD PRIMARY KEY (`activityid`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_plugin`
--
ALTER TABLE `runalyze_plugin`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_plugin_conf`
--
ALTER TABLE `runalyze_plugin_conf`
 ADD PRIMARY KEY (`id`), ADD KEY `pluginid` (`pluginid`);

--
-- Indizes für die Tabelle `runalyze_route`
--
ALTER TABLE `runalyze_route`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_shoe`
--
ALTER TABLE `runalyze_shoe`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_sport`
--
ALTER TABLE `runalyze_sport`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_trackdata`
--
ALTER TABLE `runalyze_trackdata`
 ADD PRIMARY KEY (`activityid`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_swimdata`
--
ALTER TABLE `runalyze_swimdata`
 ADD PRIMARY KEY (`activityid`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_training`
--
ALTER TABLE `runalyze_training`
 ADD PRIMARY KEY (`id`), ADD KEY `time` (`accountid`,`time`), ADD KEY `sportid` (`accountid`,`sportid`), ADD KEY `typeid` (`accountid`,`typeid`);

--
-- Indizes für die Tabelle `runalyze_type`
--
ALTER TABLE `runalyze_type`
 ADD PRIMARY KEY (`id`), ADD KEY `accountid` (`accountid`);

--
-- Indizes für die Tabelle `runalyze_user`
--
ALTER TABLE `runalyze_user`
 ADD PRIMARY KEY (`id`), ADD KEY `time` (`accountid`,`time`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `runalyze_account`
--
ALTER TABLE `runalyze_account`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_clothes`
--
ALTER TABLE `runalyze_clothes`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_conf`
--
ALTER TABLE `runalyze_conf`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_dataset`
--
ALTER TABLE `runalyze_dataset`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_plugin`
--
ALTER TABLE `runalyze_plugin`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_plugin_conf`
--
ALTER TABLE `runalyze_plugin_conf`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_route`
--
ALTER TABLE `runalyze_route`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_shoe`
--
ALTER TABLE `runalyze_shoe`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_sport`
--
ALTER TABLE `runalyze_sport`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_training`
--
ALTER TABLE `runalyze_training`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_type`
--
ALTER TABLE `runalyze_type`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `runalyze_user`
--
ALTER TABLE `runalyze_user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;