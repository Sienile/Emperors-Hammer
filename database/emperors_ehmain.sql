-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.emperorshammer.org
-- Generation Time: Sep 10, 2012 at 11:10 AM
-- Server version: 5.1.56
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `emperors_ehmain`
--

-- --------------------------------------------------------

--
-- Table structure for table `EH_Access`
--

DROP TABLE IF EXISTS `EH_Access`;
CREATE TABLE IF NOT EXISTS `EH_Access` (
  `Access_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(75) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Pages` text NOT NULL COMMENT 'pagea;pageb;...',
  PRIMARY KEY (`Access_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Access_Pages`
--

DROP TABLE IF EXISTS `EH_Access_Pages`;
CREATE TABLE IF NOT EXISTS `EH_Access_Pages` (
  `AP_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `needsAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`AP_ID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Alliances`
--

DROP TABLE IF EXISTS `EH_Alliances`;
CREATE TABLE IF NOT EXISTS `EH_Alliances` (
  `Alliance_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Abbr` varchar(5) NOT NULL,
  `Description` tinytext NOT NULL,
  `SiteURL` tinytext NOT NULL,
  `Banner` tinytext NOT NULL,
  `Status` tinyint(4) unsigned NOT NULL COMMENT '1 - ally, 2- Neutral, 3 - Enemy',
  PRIMARY KEY (`Alliance_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Articles`
--

DROP TABLE IF EXISTS `EH_Articles`;
CREATE TABLE IF NOT EXISTS `EH_Articles` (
  `Article_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Publication` tinytext NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Image` tinytext NOT NULL,
  `Link` tinytext NOT NULL,
  `DateReceived` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Article_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Bases`
--

DROP TABLE IF EXISTS `EH_Bases`;
CREATE TABLE IF NOT EXISTS `EH_Bases` (
  `Base_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `BT_ID` tinyint(3) unsigned NOT NULL,
  `Types` varchar(20) NOT NULL COMMENT 'type1;...',
  `Link` varchar(255) NOT NULL,
  `Mission` varchar(255) NOT NULL,
  `Notes` tinytext NOT NULL,
  `Master_ID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`Base_ID`),
  KEY `BT_ID` (`BT_ID`),
  KEY `Master_ID` (`Master_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Bases_Types`
--

DROP TABLE IF EXISTS `EH_Bases_Types`;
CREATE TABLE IF NOT EXISTS `EH_Bases_Types` (
  `BT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `GroupLevel` tinyint(3) unsigned NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`BT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles`
--

DROP TABLE IF EXISTS `EH_Battles`;
CREATE TABLE IF NOT EXISTS `EH_Battles` (
  `Battle_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Platform_ID` smallint(5) unsigned NOT NULL,
  `BattleNumber` smallint(5) unsigned NOT NULL,
  `BC_ID` tinyint(3) unsigned NOT NULL,
  `Name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Description` mediumtext NOT NULL,
  `NumMissions` tinyint(3) unsigned NOT NULL,
  `Released` int(10) unsigned NOT NULL,
  `Last_Updated` int(10) unsigned NOT NULL,
  `Updater_ID` mediumint(8) unsigned NOT NULL,
  `Reward_Name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Reward_Image` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Filename` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Wav_Pack` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Highscore` mediumint(8) unsigned NOT NULL,
  `HS_Holder` mediumint(8) unsigned NOT NULL,
  `Creator_1` mediumint(8) unsigned NOT NULL,
  `Creator_2` mediumint(8) unsigned NOT NULL,
  `Creator_3` mediumint(8) unsigned NOT NULL,
  `Creator_4` mediumint(8) unsigned NOT NULL,
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=disabled,1=active',
  PRIMARY KEY (`Battle_ID`),
  KEY `Platform_ID` (`Platform_ID`),
  KEY `BC_ID` (`BC_ID`),
  KEY `Updater_ID` (`Updater_ID`),
  KEY `HS_Holder` (`HS_Holder`),
  KEY `Creator_1` (`Creator_1`),
  KEY `Creator_2` (`Creator_2`),
  KEY `Creator_3` (`Creator_3`),
  KEY `Creator_4` (`Creator_4`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Bugs`
--

DROP TABLE IF EXISTS `EH_Battles_Bugs`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Bugs` (
  `Bug_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Battle_ID` mediumint(8) unsigned NOT NULL,
  `MissionsAffected` text NOT NULL,
  `Poster_ID` mediumint(8) unsigned NOT NULL,
  `Date_Added` int(10) unsigned NOT NULL,
  `Description` text NOT NULL,
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Bug_ID`),
  KEY `Battle_ID` (`Battle_ID`),
  KEY `Poster_ID` (`Poster_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Bug_Notes`
--

DROP TABLE IF EXISTS `EH_Battles_Bug_Notes`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Bug_Notes` (
  `Note_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Bug_ID` mediumint(8) unsigned NOT NULL,
  `Poster_ID` mediumint(8) unsigned NOT NULL,
  `Date_Added` int(10) unsigned NOT NULL,
  `Note` text,
  `Bug_Type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `New_Status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Note_ID`),
  KEY `Bug_ID` (`Bug_ID`),
  KEY `Poster_ID` (`Poster_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Categories`
--

DROP TABLE IF EXISTS `EH_Battles_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Categories` (
  `BC_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`BC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Complete`
--

DROP TABLE IF EXISTS `EH_Battles_Complete`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Complete` (
  `Complete_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Battle_ID` mediumint(8) unsigned NOT NULL,
  `Date_Completed` int(10) unsigned NOT NULL,
  `Filename` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Pending Review,1=Complete',
  `Scores` text NOT NULL COMMENT 'm1score;m2score;etc.',
  `TACStatus` tinyint(3) unsigned NOT NULL,
  `Rec_ID` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`Complete_ID`),
  KEY `Battle_ID` (`Battle_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Missions`
--

DROP TABLE IF EXISTS `EH_Battles_Missions`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Missions` (
  `Mission_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Battle_ID` mediumint(8) unsigned NOT NULL,
  `Mission_Num` tinyint(3) unsigned NOT NULL,
  `Name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Highscore` mediumint(8) unsigned NOT NULL,
  `HS_Holder` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`Mission_ID`),
  KEY `Battle_ID` (`Battle_ID`),
  KEY `HS_Holder` (`HS_Holder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Patches`
--

DROP TABLE IF EXISTS `EH_Battles_Patches`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Patches` (
  `BP_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Battle_ID` mediumint(8) unsigned NOT NULL,
  `Patch_ID` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`BP_ID`),
  KEY `Battle_ID` (`Battle_ID`),
  KEY `Patch_ID` (`Patch_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Battles_Reviews`
--

DROP TABLE IF EXISTS `EH_Battles_Reviews`;
CREATE TABLE IF NOT EXISTS `EH_Battles_Reviews` (
  `Review_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Battle_ID` mediumint(8) unsigned NOT NULL,
  `Poster_ID` mediumint(8) unsigned NOT NULL,
  `Date_Added` int(10) unsigned NOT NULL,
  `Description` text NOT NULL,
  `Rating` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `Status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Review_ID`),
  KEY `Battle_ID` (`Battle_ID`),
  KEY `Poster_ID` (`Poster_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Benefactors`
--

DROP TABLE IF EXISTS `EH_Benefactors`;
CREATE TABLE IF NOT EXISTS `EH_Benefactors` (
  `Benefactor_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Amount` float unsigned NOT NULL,
  `DateGiven` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Benefactor_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_ChatSystems`
--

DROP TABLE IF EXISTS `EH_ChatSystems`;
CREATE TABLE IF NOT EXISTS `EH_ChatSystems` (
  `Chat_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Image` varchar(50) NOT NULL,
  `LinkFormat` tinytext NOT NULL,
  PRIMARY KEY (`Chat_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Combat_Ratings`
--

DROP TABLE IF EXISTS `EH_Combat_Ratings`;
CREATE TABLE IF NOT EXISTS `EH_Combat_Ratings` (
  `CR_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Points` smallint(5) unsigned NOT NULL,
  `StatsColor` varchar(7) NOT NULL,
  PRIMARY KEY (`CR_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Competitions`
--

DROP TABLE IF EXISTS `EH_Competitions`;
CREATE TABLE IF NOT EXISTS `EH_Competitions` (
  `Comp_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Admin_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `StartDate` int(10) unsigned NOT NULL,
  `EndDate` int(10) unsigned NOT NULL,
  `Scope` text NOT NULL,
  `Awards` text NOT NULL,
  `Description` text NOT NULL,
  `Approved` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`Comp_ID`),
  KEY `Admin_ID` (`Admin_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Competitions_Participants`
--

DROP TABLE IF EXISTS `EH_Competitions_Participants`;
CREATE TABLE IF NOT EXISTS `EH_Competitions_Participants` (
  `CP_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Comp_ID` smallint(5) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Unit_ID` smallint(5) unsigned NOT NULL,
  `DateParticipated` int(10) unsigned NOT NULL,
  `Comments` text NOT NULL,
  `Score` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`CP_ID`),
  KEY `Comp_ID` (`Comp_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Unit_ID` (`Unit_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_ConvertInfo`
--

DROP TABLE IF EXISTS `EH_ConvertInfo`;
CREATE TABLE IF NOT EXISTS `EH_ConvertInfo` (
  `CI_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Group_ID` int(11) NOT NULL,
  `Table` varchar(255) NOT NULL,
  `OriginalValue` int(11) NOT NULL,
  `NewValue` int(11) NOT NULL,
  PRIMARY KEY (`CI_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `OriginalValue` (`OriginalValue`),
  KEY `NewValue` (`NewValue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_FCHG`
--

DROP TABLE IF EXISTS `EH_FCHG`;
CREATE TABLE IF NOT EXISTS `EH_FCHG` (
  `FCHG_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) NOT NULL,
  `Abbr` varchar(5) NOT NULL,
  `Points` smallint(5) unsigned NOT NULL,
  `Image` varchar(25) NOT NULL,
  `StatsColor` varchar(7) NOT NULL,
  PRIMARY KEY (`FCHG_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Fiction`
--

DROP TABLE IF EXISTS `EH_Fiction`;
CREATE TABLE IF NOT EXISTS `EH_Fiction` (
  `Fiction_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Title` text NOT NULL,
  `Body` longtext NOT NULL,
  `DatePosted` int(10) unsigned NOT NULL,
  `Approved` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`Fiction_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Files`
--

DROP TABLE IF EXISTS `EH_Files`;
CREATE TABLE IF NOT EXISTS `EH_Files` (
  `File_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Filename` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `FC_ID` smallint(5) unsigned NOT NULL,
  `DateAdded` int(10) unsigned NOT NULL,
  PRIMARY KEY (`File_ID`),
  KEY `FC_ID` (`FC_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Files_Categories`
--

DROP TABLE IF EXISTS `EH_Files_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Files_Categories` (
  `FC_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`FC_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Groups`
--

DROP TABLE IF EXISTS `EH_Groups`;
CREATE TABLE IF NOT EXISTS `EH_Groups` (
  `Group_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `ShortDesc` text NOT NULL,
  `LongDesc` longtext NOT NULL,
  `Active` tinyint(3) unsigned NOT NULL,
  `Banner` varchar(50) NOT NULL,
  `ProfileTabs` tinytext NOT NULL COMMENT 'GT_ID;GT_ID;...',
  `MedalBrackets` varchar(2) NOT NULL,
  `MedalSeperator` varchar(2) NOT NULL,
  `MedalGroupBrackets` varchar(10) NOT NULL,
  `RankTypeDisplayName` varchar(5) NOT NULL,
  `IDLineFormat` text NOT NULL COMMENT '@@P - Position Group, R-Rank Group, N Name, U Unit Info, F FCHG, C Combat Rating, M Medals T Training',
  `PositionSeparator` varchar(10) NOT NULL,
  `UnitSeparator` varchar(10) NOT NULL,
  `CSAbbrL1` varchar(10) NOT NULL COMMENT 'ID Line Abbr for CS',
  `CSAbbrL2` varchar(10) NOT NULL COMMENT 'Level 2 CS Abbr',
  `CSAbbrL3` varchar(10) NOT NULL COMMENT 'Level 3 CS Abbr',
  `UniType` tinyint(3) unsigned NOT NULL COMMENT '1-upload, 2-assembled, 3-rank based',
  `GroupJoinContact` mediumint(8) unsigned NOT NULL COMMENT 'Position',
  `JoinMailBlurb` longtext NOT NULL,
  `CompAdmin` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Groups_Tabs`
--

DROP TABLE IF EXISTS `EH_Groups_Tabs`;
CREATE TABLE IF NOT EXISTS `EH_Groups_Tabs` (
  `GT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(18) NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`GT_ID`),
  UNIQUE KEY `SortOrder` (`SortOrder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Heroes`
--

DROP TABLE IF EXISTS `EH_Heroes`;
CREATE TABLE IF NOT EXISTS `EH_Heroes` (
  `Hero_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Reason` text NOT NULL,
  PRIMARY KEY (`Hero_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_History`
--

DROP TABLE IF EXISTS `EH_History`;
CREATE TABLE IF NOT EXISTS `EH_History` (
  `History_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Title` tinytext NOT NULL,
  `Base_ID` smallint(5) unsigned NOT NULL,
  `Body` longtext NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Approved` tinyint(1) unsigned NOT NULL,
  `DateApproved` int(10) unsigned NOT NULL,
  PRIMARY KEY (`History_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Base_ID` (`Base_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Images`
--

DROP TABLE IF EXISTS `EH_Images`;
CREATE TABLE IF NOT EXISTS `EH_Images` (
  `Images_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `IC_ID` tinyint(3) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `DateSubmitted` int(10) unsigned NOT NULL,
  `Approved` tinyint(1) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `ImageData` longblob NOT NULL,
  PRIMARY KEY (`Images_ID`),
  KEY `IC_ID` (`IC_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Images_Categories`
--

DROP TABLE IF EXISTS `EH_Images_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Images_Categories` (
  `IC_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Display` tinyint(3) unsigned NOT NULL COMMENT '1',
  `SortOrder` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`IC_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_IP_Access`
--

DROP TABLE IF EXISTS `EH_IP_Access`;
CREATE TABLE IF NOT EXISTS `EH_IP_Access` (
  `Access_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned DEFAULT NULL,
  `Document_ID` mediumint(8) unsigned DEFAULT NULL,
  `Target` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IP` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `Status` tinyint(1) NOT NULL,
  `Date` datetime DEFAULT NULL,
  `Note` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Access_ID`),
  KEY `IP` (`IP`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Document_ID` (`Document_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EH_IP_Tracker`
--

DROP TABLE IF EXISTS `EH_IP_Tracker`;
CREATE TABLE IF NOT EXISTS `EH_IP_Tracker` (
  `Track_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned DEFAULT NULL,
  `IP` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `Script` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `Date` datetime NOT NULL,
  `Is_Login` tinyint(1) DEFAULT NULL COMMENT 'Is this IP logging in',
  `Warning_Flag` tinyint(1) DEFAULT NULL COMMENT 'Suspiscious activity possible',
  `System_Note` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Note of whats going on from system',
  PRIMARY KEY (`Track_ID`),
  KEY `Member_ID` (`Member_ID`,`IP`,`Date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Used to track IP access';

-- --------------------------------------------------------

--
-- Table structure for table `EH_Items`
--

DROP TABLE IF EXISTS `EH_Items`;
CREATE TABLE IF NOT EXISTS `EH_Items` (
  `Item_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `IC_ID` tinyint(3) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Description` text NOT NULL,
  `Cost` double NOT NULL,
  `Image` varchar(255) NOT NULL,
  `Training` varchar(50) NOT NULL COMMENT '; deliminted',
  `MinPos` smallint(5) unsigned NOT NULL,
  `MinRank` smallint(5) unsigned NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `NumAvail` mediumint(9) NOT NULL,
  `Devalue` double NOT NULL,
  `WeeklyCost` double NOT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `IC_ID` (`IC_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `MinPos` (`MinPos`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Items_Categories`
--

DROP TABLE IF EXISTS `EH_Items_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Items_Categories` (
  `IC_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`IC_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Links`
--

DROP TABLE IF EXISTS `EH_Links`;
CREATE TABLE IF NOT EXISTS `EH_Links` (
  `Link_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `URL` tinytext NOT NULL,
  `LC_ID` tinyint(3) unsigned NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`Link_ID`),
  KEY `LC_ID` (`LC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Links_Categories`
--

DROP TABLE IF EXISTS `EH_Links_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Links_Categories` (
  `LC_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Description` text NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`LC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Links_Comments`
--

DROP TABLE IF EXISTS `EH_Links_Comments`;
CREATE TABLE IF NOT EXISTS `EH_Links_Comments` (
  `LCo_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Link_ID` smallint(5) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `DatePosted` int(10) unsigned NOT NULL,
  `Comment` text NOT NULL,
  PRIMARY KEY (`LCo_ID`),
  KEY `Link_ID` (`Link_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Medals`
--

DROP TABLE IF EXISTS `EH_Medals`;
CREATE TABLE IF NOT EXISTS `EH_Medals` (
  `Medal_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) NOT NULL,
  `Abbr` varchar(4) NOT NULL,
  `MG_ID` tinyint(3) unsigned NOT NULL,
  `MT_ID` tinyint(3) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Image` varchar(75) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `ShowOnID` tinyint(1) NOT NULL,
  `SortOrder` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`Medal_ID`),
  KEY `MG_ID` (`MG_ID`),
  KEY `MT_ID` (`MT_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Medals_Complete`
--

DROP TABLE IF EXISTS `EH_Medals_Complete`;
CREATE TABLE IF NOT EXISTS `EH_Medals_Complete` (
  `MC_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Medal_ID` smallint(5) unsigned NOT NULL,
  `Awarder_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `DateAwarded` int(10) unsigned NOT NULL,
  `Reason` text NOT NULL,
  `Status` tinyint(3) unsigned NOT NULL COMMENT '1 - approved, 0 - pending rec, 2 - rejected, 3- award',
  `RejectReason` text NOT NULL,
  PRIMARY KEY (`MC_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Medal_ID` (`Medal_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Awarder_ID` (`Awarder_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Medals_Groups`
--

DROP TABLE IF EXISTS `EH_Medals_Groups`;
CREATE TABLE IF NOT EXISTS `EH_Medals_Groups` (
  `MG_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Abbr` varchar(5) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`MG_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Medals_Types`
--

DROP TABLE IF EXISTS `EH_Medals_Types`;
CREATE TABLE IF NOT EXISTS `EH_Medals_Types` (
  `MT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(16) NOT NULL,
  PRIMARY KEY (`MT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Medals_Upgrades`
--

DROP TABLE IF EXISTS `EH_Medals_Upgrades`;
CREATE TABLE IF NOT EXISTS `EH_Medals_Upgrades` (
  `MU_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Medal_ID` smallint(5) unsigned NOT NULL,
  `Name` varchar(75) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Lower` mediumint(8) unsigned NOT NULL,
  `Upper` mediumint(8) unsigned NOT NULL,
  `Recycles` tinyint(1) NOT NULL,
  PRIMARY KEY (`MU_ID`),
  KEY `Medal_ID` (`Medal_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Meetings`
--

DROP TABLE IF EXISTS `EH_Meetings`;
CREATE TABLE IF NOT EXISTS `EH_Meetings` (
  `Meeting_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `MeetTimeDesc` text NOT NULL,
  PRIMARY KEY (`Meeting_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Meetings_Logs`
--

DROP TABLE IF EXISTS `EH_Meetings_Logs`;
CREATE TABLE IF NOT EXISTS `EH_Meetings_Logs` (
  `ML_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `DateofLog` int(10) unsigned NOT NULL,
  `Meeting_ID` tinyint(3) unsigned NOT NULL,
  `Log` longtext NOT NULL,
  PRIMARY KEY (`ML_ID`),
  KEY `Meeting_ID` (`Meeting_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members`
--

DROP TABLE IF EXISTS `EH_Members`;
CREATE TABLE IF NOT EXISTS `EH_Members` (
  `Member_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `UserPassword` varchar(128) NOT NULL,
  `Quote` tinytext NOT NULL,
  `URL` tinytext NOT NULL,
  PRIMARY KEY (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_ChatProfile`
--

DROP TABLE IF EXISTS `EH_Members_ChatProfile`;
CREATE TABLE IF NOT EXISTS `EH_Members_ChatProfile` (
  `EMCP_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Chat_ID` smallint(5) unsigned NOT NULL,
  `Chat_Handle` varchar(50) NOT NULL,
  PRIMARY KEY (`EMCP_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Chat_ID` (`Chat_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Groups`
--

DROP TABLE IF EXISTS `EH_Members_Groups`;
CREATE TABLE IF NOT EXISTS `EH_Members_Groups` (
  `EMG_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `isPrimary` tinyint(1) NOT NULL,
  `JoinDate` int(10) unsigned NOT NULL,
  `Credits` mediumint(9) NOT NULL,
  PRIMARY KEY (`EMG_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_History`
--

DROP TABLE IF EXISTS `EH_Members_History`;
CREATE TABLE IF NOT EXISTS `EH_Members_History` (
  `MH_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `History_Type` tinyint(4) NOT NULL,
  `MemberChange` tinytext NOT NULL COMMENT 'table:id1-id2',
  `Reason` text NOT NULL,
  `Occured` int(10) unsigned NOT NULL,
  PRIMARY KEY (`MH_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_History_Types`
--

DROP TABLE IF EXISTS `EH_Members_History_Types`;
CREATE TABLE IF NOT EXISTS `EH_Members_History_Types` (
  `MHT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Table` varchar(9) NOT NULL,
  PRIMARY KEY (`MHT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_INPR`
--

DROP TABLE IF EXISTS `EH_Members_INPR`;
CREATE TABLE IF NOT EXISTS `EH_Members_INPR` (
  `MI_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `UpdateDate` int(10) unsigned NOT NULL,
  `Gender` varchar(8) NOT NULL,
  `Species` varchar(255) NOT NULL,
  `Birthdate` varchar(255) NOT NULL,
  `PlaceBirth` varchar(255) NOT NULL,
  `Relationship` varchar(255) NOT NULL,
  `Family` mediumtext NOT NULL,
  `Social` mediumtext NOT NULL,
  `SigYouth` mediumtext NOT NULL,
  `SigAdult` mediumtext NOT NULL,
  `AlignAtt` mediumtext NOT NULL,
  `Previous` mediumtext NOT NULL,
  `Hobbies` mediumtext NOT NULL,
  `Traggedies` mediumtext NOT NULL,
  `PhobiaAllergy` mediumtext NOT NULL,
  `View` mediumtext NOT NULL,
  `Enlisting` mediumtext NOT NULL,
  `Comments` mediumtext NOT NULL,
  PRIMARY KEY (`MI_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Items`
--

DROP TABLE IF EXISTS `EH_Members_Items`;
CREATE TABLE IF NOT EXISTS `EH_Members_Items` (
  `MI_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Item_ID` smallint(5) unsigned NOT NULL,
  `Status` tinyint(4) unsigned NOT NULL COMMENT '0 pending, 1 bought, 2 awarded',
  `DayApproved` int(10) unsigned NOT NULL,
  `Value` double NOT NULL,
  PRIMARY KEY (`MI_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Item_ID` (`Item_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Platforms`
--

DROP TABLE IF EXISTS `EH_Members_Platforms`;
CREATE TABLE IF NOT EXISTS `EH_Members_Platforms` (
  `EMP_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Platform_ID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`EMP_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Platform_ID` (`Platform_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Positions`
--

DROP TABLE IF EXISTS `EH_Members_Positions`;
CREATE TABLE IF NOT EXISTS `EH_Members_Positions` (
  `EMP_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Position_ID` smallint(5) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `isGroupPrimary` tinyint(1) NOT NULL,
  `PositionDate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EMP_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Position_ID` (`Position_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Ranks`
--

DROP TABLE IF EXISTS `EH_Members_Ranks`;
CREATE TABLE IF NOT EXISTS `EH_Members_Ranks` (
  `EMR_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Rank_ID` smallint(5) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `PromotionDate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EMR_ID`),
  KEY `Rank_ID` (`Rank_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Skills`
--

DROP TABLE IF EXISTS `EH_Members_Skills`;
CREATE TABLE IF NOT EXISTS `EH_Members_Skills` (
  `EMS_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Skill_ID` smallint(5) unsigned NOT NULL,
  `SkillLevel` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`EMS_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Skill_ID` (`Skill_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Special_Areas`
--

DROP TABLE IF EXISTS `EH_Members_Special_Areas`;
CREATE TABLE IF NOT EXISTS `EH_Members_Special_Areas` (
  `EMSA_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `SA_ID` tinyint(3) unsigned NOT NULL,
  `Value` varchar(100) NOT NULL,
  PRIMARY KEY (`EMSA_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `SA_ID` (`SA_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Uniforms`
--

DROP TABLE IF EXISTS `EH_Members_Uniforms`;
CREATE TABLE IF NOT EXISTS `EH_Members_Uniforms` (
  `EMU_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Filename` varchar(75) NOT NULL,
  `UniformDate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EMU_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Members_Units`
--

DROP TABLE IF EXISTS `EH_Members_Units`;
CREATE TABLE IF NOT EXISTS `EH_Members_Units` (
  `EMU_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Unit_ID` smallint(5) unsigned NOT NULL,
  `UnitPosition` tinyint(3) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `UnitDate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EMU_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Unit_ID` (`Unit_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Merged_Profiles`
--

DROP TABLE IF EXISTS `EH_Merged_Profiles`;
CREATE TABLE IF NOT EXISTS `EH_Merged_Profiles` (
  `MP_ID` int(11) NOT NULL AUTO_INCREMENT,
  `To_ID` int(11) NOT NULL,
  `From_ID` int(11) NOT NULL,
  `Approved` smallint(2) NOT NULL,
  PRIMARY KEY (`MP_ID`),
  KEY `To_ID` (`To_ID`),
  KEY `From_ID` (`From_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_News`
--

DROP TABLE IF EXISTS `EH_News`;
CREATE TABLE IF NOT EXISTS `EH_News` (
  `News_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Topic` text NOT NULL,
  `Poster` text NOT NULL,
  `Poster_ID` mediumint(8) unsigned NOT NULL,
  `DatePosted` int(10) unsigned NOT NULL,
  `Body` longtext NOT NULL,
  PRIMARY KEY (`News_ID`),
  KEY `Poster_ID` (`Poster_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Newsletters`
--

DROP TABLE IF EXISTS `EH_Newsletters`;
CREATE TABLE IF NOT EXISTS `EH_Newsletters` (
  `NL_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Title` varchar(50) NOT NULL,
  `OriginalFile` varchar(50) NOT NULL,
  `SortOrder` smallint(5) unsigned NOT NULL,
  `PDFFile` varchar(50) NOT NULL,
  `DateReleased` int(10) unsigned NOT NULL,
  PRIMARY KEY (`NL_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Pages`
--

DROP TABLE IF EXISTS `EH_Pages`;
CREATE TABLE IF NOT EXISTS `EH_Pages` (
  `Page_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ShortName` varchar(25) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Body` longtext NOT NULL,
  `DateUpdated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Page_ID`),
  UNIQUE KEY `ShortName` (`ShortName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Patches`
--

DROP TABLE IF EXISTS `EH_Patches`;
CREATE TABLE IF NOT EXISTS `EH_Patches` (
  `Patch_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Ship_ID` smallint(5) unsigned NOT NULL,
  `Name` tinytext NOT NULL,
  `Filename` varchar(50) NOT NULL,
  `PC_ID` tinyint(3) unsigned NOT NULL,
  `Platform_ID` smallint(5) unsigned NOT NULL,
  `Creator` text NOT NULL,
  `ReleasedDate` int(10) unsigned NOT NULL,
  `UpdatedDate` int(10) unsigned NOT NULL,
  `Image` tinytext NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`Patch_ID`),
  KEY `Ship_ID` (`Ship_ID`),
  KEY `Platform_ID` (`Platform_ID`),
  KEY `PC_ID` (`PC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Patches_Bugs`
--

DROP TABLE IF EXISTS `EH_Patches_Bugs`;
CREATE TABLE IF NOT EXISTS `EH_Patches_Bugs` (
  `PB_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Patch_ID` mediumint(8) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Status` tinyint(3) unsigned NOT NULL COMMENT '0=Unresolved;1=Pending;2=Resolved',
  `Description` text NOT NULL,
  `DateReported` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PB_ID`),
  KEY `Patch_ID` (`Patch_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Patches_Categories`
--

DROP TABLE IF EXISTS `EH_Patches_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Patches_Categories` (
  `PC_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`PC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Patches_Reviews`
--

DROP TABLE IF EXISTS `EH_Patches_Reviews`;
CREATE TABLE IF NOT EXISTS `EH_Patches_Reviews` (
  `PR_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Patch_ID` mediumint(8) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Review` text NOT NULL,
  `DatePosted` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PR_ID`),
  KEY `Patch_ID` (`Patch_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Platforms`
--

DROP TABLE IF EXISTS `EH_Platforms`;
CREATE TABLE IF NOT EXISTS `EH_Platforms` (
  `Platform_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Abbr` varchar(6) NOT NULL DEFAULT '',
  `FileExt` varchar(10) NOT NULL,
  PRIMARY KEY (`Platform_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Positions`
--

DROP TABLE IF EXISTS `EH_Positions`;
CREATE TABLE IF NOT EXISTS `EH_Positions` (
  `Position_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Abbr` varchar(15) NOT NULL,
  `Description` text NOT NULL,
  `Banner` tinytext NOT NULL,
  `SiteURL` varchar(255) NOT NULL,
  `isCS` tinyint(3) unsigned NOT NULL COMMENT '1:CS, 2:A:CS, 3:Support',
  `CSOrder` varchar(10) NOT NULL,
  `Base_ID` smallint(5) unsigned NOT NULL,
  `MinRank` smallint(5) unsigned NOT NULL,
  `MaxRank` smallint(5) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Access_ID` smallint(5) unsigned NOT NULL,
  `MaxPromotableRank` smallint(5) unsigned NOT NULL,
  `MedalsAwardable` text NOT NULL,
  `SortOrder` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`Position_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Base_ID` (`Base_ID`),
  KEY `MinRank` (`MinRank`),
  KEY `MaxRank` (`MaxRank`),
  KEY `MaxPromotableRank` (`MaxPromotableRank`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Promotion_Recs`
--

DROP TABLE IF EXISTS `EH_Promotion_Recs`;
CREATE TABLE IF NOT EXISTS `EH_Promotion_Recs` (
  `PR_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `For_ID` mediumint(8) unsigned NOT NULL,
  `From_ID` mediumint(8) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Type` tinyint(1) NOT NULL COMMENT '0:Rec, 1:Award',
  `Reason` text NOT NULL,
  PRIMARY KEY (`PR_ID`),
  KEY `For_ID` (`For_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `From_ID` (`From_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ranks`
--

DROP TABLE IF EXISTS `EH_Ranks`;
CREATE TABLE IF NOT EXISTS `EH_Ranks` (
  `Rank_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(75) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `RT_ID` tinyint(3) unsigned NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `SortOrder` smallint(5) unsigned NOT NULL,
  `UniformRankBased` varchar(40) NOT NULL,
  PRIMARY KEY (`Rank_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `RT_ID` (`RT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ranks_Types`
--

DROP TABLE IF EXISTS `EH_Ranks_Types`;
CREATE TABLE IF NOT EXISTS `EH_Ranks_Types` (
  `RT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`RT_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Reports`
--

DROP TABLE IF EXISTS `EH_Reports`;
CREATE TABLE IF NOT EXISTS `EH_Reports` (
  `Report_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Poster` text NOT NULL,
  `Report` longtext NOT NULL,
  `ReportNum` smallint(6) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Unit_ID` smallint(5) unsigned NOT NULL,
  `Position_ID` smallint(5) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `ReportDate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Report_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Unit_ID` (`Unit_ID`),
  KEY `Position_ID` (`Position_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Security_Docs`
--

DROP TABLE IF EXISTS `EH_Security_Docs`;
CREATE TABLE IF NOT EXISTS `EH_Security_Docs` (
  `Document_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Submitter_ID` mediumint(8) unsigned NOT NULL,
  `Date_Added` datetime NOT NULL,
  `Aliases` text COLLATE utf8_unicode_ci,
  `Last_IP` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Last_Location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Previous_IP` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Notes` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Document_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Submitter_ID` (`Submitter_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Security_Docs_Profiles`
--

DROP TABLE IF EXISTS `EH_Security_Docs_Profiles`;
CREATE TABLE IF NOT EXISTS `EH_Security_Docs_Profiles` (
  `Profile_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Document_ID` int(11) NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Date_Added` datetime DEFAULT NULL,
  PRIMARY KEY (`Profile_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Servers`
--

DROP TABLE IF EXISTS `EH_Servers`;
CREATE TABLE IF NOT EXISTS `EH_Servers` (
  `Server_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `ServerType` tinyint(1) NOT NULL COMMENT '1-Game, 0-Comm',
  `Address` varchar(50) NOT NULL,
  `Port` varchar(10) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Notes` text NOT NULL,
  `Platform_ID` smallint(5) unsigned NOT NULL,
  `URL` tinytext NOT NULL,
  PRIMARY KEY (`Server_ID`),
  KEY `Platform_ID` (`Platform_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ships`
--

DROP TABLE IF EXISTS `EH_Ships`;
CREATE TABLE IF NOT EXISTS `EH_Ships` (
  `Ship_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Manufacturer` mediumtext NOT NULL,
  `SS_ID` tinyint(3) unsigned NOT NULL,
  `ST_ID` tinyint(3) unsigned NOT NULL,
  `Crew` text NOT NULL,
  `Fighters` text NOT NULL,
  `Length` text NOT NULL,
  `Cargo` text NOT NULL,
  `Description` text NOT NULL,
  `Power` text NOT NULL,
  `RPGName` text NOT NULL,
  `RPGWeapons` text NOT NULL,
  `RPGSynopsis` text NOT NULL,
  PRIMARY KEY (`Ship_ID`),
  KEY `SS_ID` (`SS_ID`),
  KEY `ST_ID` (`ST_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ships_Images`
--

DROP TABLE IF EXISTS `EH_Ships_Images`;
CREATE TABLE IF NOT EXISTS `EH_Ships_Images` (
  `SI_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `FileName` varchar(255) NOT NULL,
  `SIT_ID` tinyint(3) unsigned NOT NULL,
  `Ship_ID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`SI_ID`),
  KEY `SIT_ID` (`SIT_ID`),
  KEY `Ship_ID` (`Ship_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ships_Images_Types`
--

DROP TABLE IF EXISTS `EH_Ships_Images_Types`;
CREATE TABLE IF NOT EXISTS `EH_Ships_Images_Types` (
  `SIT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`SIT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ships_Supplement`
--

DROP TABLE IF EXISTS `EH_Ships_Supplement`;
CREATE TABLE IF NOT EXISTS `EH_Ships_Supplement` (
  `SS_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`SS_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Ships_Types`
--

DROP TABLE IF EXISTS `EH_Ships_Types`;
CREATE TABLE IF NOT EXISTS `EH_Ships_Types` (
  `PT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`PT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Site_Awards`
--

DROP TABLE IF EXISTS `EH_Site_Awards`;
CREATE TABLE IF NOT EXISTS `EH_Site_Awards` (
  `SA_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Description` text NOT NULL,
  `Reason` text NOT NULL,
  `DateAwarded` int(10) unsigned NOT NULL,
  `Banner` tinytext NOT NULL,
  `Link` tinytext NOT NULL,
  PRIMARY KEY (`SA_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Skills`
--

DROP TABLE IF EXISTS `EH_Skills`;
CREATE TABLE IF NOT EXISTS `EH_Skills` (
  `Skill_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Icon` varchar(255) NOT NULL,
  PRIMARY KEY (`Skill_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Special_Areas`
--

DROP TABLE IF EXISTS `EH_Special_Areas`;
CREATE TABLE IF NOT EXISTS `EH_Special_Areas` (
  `SA_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(28) NOT NULL,
  `Abbr` varchar(4) NOT NULL,
  PRIMARY KEY (`SA_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_SSType`
--

DROP TABLE IF EXISTS `EH_SSType`;
CREATE TABLE IF NOT EXISTS `EH_SSType` (
  `SSType_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Cert_ID` smallint(5) unsigned NOT NULL,
  `Image` varchar(50) NOT NULL,
  PRIMARY KEY (`SSType_ID`),
  KEY `Cert_ID` (`Cert_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training`
--

DROP TABLE IF EXISTS `EH_Training`;
CREATE TABLE IF NOT EXISTS `EH_Training` (
  `Training_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `TC_ID` smallint(5) unsigned NOT NULL,
  `TAc_ID` tinyint(3) unsigned NOT NULL,
  `Available` tinyint(1) NOT NULL,
  `Description` text NOT NULL,
  `SortOrder` mediumint(8) unsigned NOT NULL,
  `Min_Training_ID` mediumint(8) unsigned NOT NULL,
  `Min_Rank_ID` smallint(5) unsigned NOT NULL,
  `Min_Pos_ID` smallint(5) unsigned NOT NULL,
  `Min_Time` smallint(5) unsigned NOT NULL,
  `MinPoints` smallint(5) unsigned NOT NULL,
  `MaxPoints` smallint(5) unsigned NOT NULL,
  `NotesFile` varchar(100) NOT NULL,
  `Rewards` text NOT NULL,
  `Grader` mediumint(8) unsigned NOT NULL,
  `Ribbon` varchar(50) NOT NULL,
  PRIMARY KEY (`Training_ID`),
  KEY `TC_ID` (`TC_ID`),
  KEY `TAc_ID` (`TAc_ID`),
  KEY `Grader` (`Grader`),
  KEY `Min_Training_ID` (`Min_Training_ID`),
  KEY `Min_Pos_ID` (`Min_Pos_ID`),
  KEY `Min_Rank_ID` (`Min_Rank_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Academies`
--

DROP TABLE IF EXISTS `EH_Training_Academies`;
CREATE TABLE IF NOT EXISTS `EH_Training_Academies` (
  `TAc_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Abbr` varchar(10) NOT NULL,
  `Description` text NOT NULL,
  `SortOrder` tinyint(3) unsigned NOT NULL,
  `EntryBrackets` varchar(10) NOT NULL,
  `ExitBrackets` varchar(10) NOT NULL,
  `Seperator` varchar(5) NOT NULL,
  `DefaultNoCourse` varchar(10) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Leader` smallint(5) unsigned NOT NULL,
  `Deputy` smallint(5) unsigned NOT NULL,
  `Trainers` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`TAc_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Leader` (`Leader`),
  KEY `Deputy` (`Deputy`),
  KEY `Trainers` (`Trainers`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Awards`
--

DROP TABLE IF EXISTS `EH_Training_Awards`;
CREATE TABLE IF NOT EXISTS `EH_Training_Awards` (
  `TA_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Training_ID` mediumint(8) unsigned NOT NULL,
  `Score` smallint(5) unsigned NOT NULL,
  `TAT_ID` tinyint(3) unsigned NOT NULL,
  `Award_ID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`TA_ID`),
  KEY `Training_ID` (`Training_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Awards_Types`
--

DROP TABLE IF EXISTS `EH_Training_Awards_Types`;
CREATE TABLE IF NOT EXISTS `EH_Training_Awards_Types` (
  `TAT_ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(5) NOT NULL,
  PRIMARY KEY (`TAT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Categories`
--

DROP TABLE IF EXISTS `EH_Training_Categories`;
CREATE TABLE IF NOT EXISTS `EH_Training_Categories` (
  `TC_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `Abbr` varchar(5) NOT NULL,
  `Master_ID` smallint(5) unsigned NOT NULL,
  `TCa_ID` tinyint(3) unsigned NOT NULL,
  `Description` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `IDLineGroup` tinyint(4) unsigned NOT NULL COMMENT '1-HF Group, 0 - all in cat display, 2 - Display Cat ABBR/1/2/...,; 3 - Prefix Cat Abbr',
  `SortOrder` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`TC_ID`),
  KEY `Master_ID` (`Master_ID`),
  KEY `TCa_ID` (`TCa_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Complete`
--

DROP TABLE IF EXISTS `EH_Training_Complete`;
CREATE TABLE IF NOT EXISTS `EH_Training_Complete` (
  `CT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Training_ID` mediumint(8) unsigned NOT NULL,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `DateComplete` int(10) unsigned NOT NULL,
  `Score` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`CT_ID`),
  KEY `Training_ID` (`Training_ID`),
  KEY `Member_ID` (`Member_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Exams`
--

DROP TABLE IF EXISTS `EH_Training_Exams`;
CREATE TABLE IF NOT EXISTS `EH_Training_Exams` (
  `TE_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Question` text NOT NULL,
  `Type` tinyint(4) unsigned NOT NULL COMMENT '2=Text, 1=textarea, 0=Multiple Choice',
  `Answer` text NOT NULL,
  `Training_ID` mediumint(8) unsigned NOT NULL,
  `Choices` text NOT NULL COMMENT ', seperate answers',
  `SortOrder` mediumint(8) unsigned NOT NULL,
  `Points` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`TE_ID`),
  KEY `Training_ID` (`Training_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Exams_Complete`
--

DROP TABLE IF EXISTS `EH_Training_Exams_Complete`;
CREATE TABLE IF NOT EXISTS `EH_Training_Exams_Complete` (
  `TEC_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Member_ID` mediumint(8) unsigned NOT NULL,
  `Training_ID` mediumint(8) unsigned NOT NULL,
  `TE_ID` mediumint(8) unsigned NOT NULL,
  `Answer` longtext NOT NULL,
  `Score` smallint(5) unsigned NOT NULL,
  `Status` tinyint(4) unsigned NOT NULL COMMENT '1=In Progress, 2 = Submitted, 3=Graded',
  `DateSubmitted` int(10) unsigned NOT NULL,
  PRIMARY KEY (`TEC_ID`),
  KEY `Member_ID` (`Member_ID`),
  KEY `Training_ID` (`Training_ID`),
  KEY `TE_ID` (`TE_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Training_Notes`
--

DROP TABLE IF EXISTS `EH_Training_Notes`;
CREATE TABLE IF NOT EXISTS `EH_Training_Notes` (
  `TN_ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `SectionName` tinytext NOT NULL,
  `SectionText` longtext NOT NULL,
  `SortOrder` mediumint(8) unsigned NOT NULL,
  `Training_ID` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`TN_ID`),
  KEY `Training_ID` (`Training_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Units`
--

DROP TABLE IF EXISTS `EH_Units`;
CREATE TABLE IF NOT EXISTS `EH_Units` (
  `Unit_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `UT_ID` tinyint(3) unsigned NOT NULL,
  `Master_ID` smallint(5) unsigned NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `Base_ID` smallint(5) unsigned NOT NULL,
  `SiteURL` text NOT NULL,
  `MessageBoard` text NOT NULL,
  `Banner` text NOT NULL,
  `Motto` text NOT NULL,
  `Nickname` text NOT NULL,
  `MissionRoll` text NOT NULL,
  `Craft` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`Unit_ID`),
  KEY `Master_ID` (`Master_ID`),
  KEY `Group_ID` (`Group_ID`),
  KEY `Craft` (`Craft`),
  KEY `Base_ID` (`Base_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EH_Units_Types`
--

DROP TABLE IF EXISTS `EH_Units_Types`;
CREATE TABLE IF NOT EXISTS `EH_Units_Types` (
  `UT_ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Group_ID` tinyint(3) unsigned NOT NULL,
  `SortOrder` smallint(5) unsigned NOT NULL,
  `DisplayUnitBase` tinyint(1) unsigned NOT NULL COMMENT '1-display base with unit, else display CS pos, with pos base',
  `PrefixPostfixType` tinyint(4) unsigned NOT NULL COMMENT '0  none, 1 prefix, 2 postfix',
  `DisplayUT` tinyint(1) unsigned NOT NULL,
  `SelectorDisplayMasterUnit` tinyint(4) unsigned NOT NULL COMMENT '0 no, 1- pre, 2 post',
  `DisplayMasterUnit` tinyint(4) unsigned NOT NULL COMMENT '0 - no, 1 - Pre, 2- postfix',
  `Position` tinyint(1) unsigned NOT NULL,
  `MaxPos` tinyint(4) unsigned NOT NULL,
  `PageSections` text NOT NULL COMMENT 'TrainingCerts - @TC@, BattleCerts - @BC@, MB - @MB@, Name - @NA@, Motto - @MT@, Base - @BA@, Site - @SU@, Banner - @BN@, Nickname - @NN@, MissionRoll - @MR@',
  PRIMARY KEY (`UT_ID`),
  KEY `Group_ID` (`Group_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
