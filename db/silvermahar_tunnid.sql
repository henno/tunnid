-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2014 at 11:41 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `silvermahar_tunnid`
--
CREATE DATABASE IF NOT EXISTS `tunnid` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tunnid`;

-- --------------------------------------------------------

--
-- Table structure for table `daybooks`
--

CREATE TABLE IF NOT EXISTS `daybooks` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `planned` int(10) unsigned NOT NULL,
  `students` int(10) unsigned NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `groupname` varchar(255) NOT NULL,
  `p1c` int(11) NOT NULL,
  `p2c` int(11) NOT NULL,
  `p3c` int(11) NOT NULL,
  `p4c` int(11) NOT NULL,
  `p5c` int(11) NOT NULL,
  `p6c` int(11) NOT NULL,
  `p7c` int(11) NOT NULL,
  `p8c` int(11) NOT NULL,
  `p1p` int(11) NOT NULL,
  `p2p` int(11) NOT NULL,
  `p3p` int(11) NOT NULL,
  `p4p` int(11) NOT NULL,
  `p5p` int(11) NOT NULL,
  `p6p` int(11) NOT NULL,
  `p7p` int(11) NOT NULL,
  `p8p` int(11) NOT NULL,
  `theory` int(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `daybooks`
--

INSERT INTO `daybooks` (`id`, `name`, `planned`, `students`, `fullname`, `groupname`, `p1c`, `p2c`, `p3c`, `p4c`, `p5c`, `p6c`, `p7c`, `p8c`, `p1p`, `p2p`, `p3p`, `p4p`, `p5p`, `p6p`, `p7p`, `p8p`, `theory`) VALUES
(12163, 'UNIX operatsioonisüsteemid', 40, 24, 'UNIX operatsioonisüsteemid (80); teoreetiline töö; 40 tundi', 'AV12', 0, 0, 10, 10, 0, 0, 0, 0, 0, 0, 20, 20, 0, 0, 0, 0, 1),
(12164, 'UNIX operatsioonisüsteemid', 40, 11, 'UNIX operatsioonisüsteemid (80); praktiline töö; 40 tundi', 'AV12', 0, 0, 18, 22, 0, 0, 0, 0, 0, 0, 20, 20, 0, 0, 0, 0, 0),
(12165, 'UNIX operatsioonisüsteemid', 40, 13, 'UNIX operatsioonisüsteemid (80); praktiline töö; 40 tundi', 'AV12', 0, 0, 24, 14, 0, 0, 0, 0, 0, 0, 20, 20, 0, 0, 0, 0, 0),
(12419, 'UNIX operatsioonisüsteemid', 80, 13, 'UNIX operatsioonisüsteemid (80); teoreetiline töö, praktiline töö; 80 tundi', 'AVP111', 0, 30, 30, 0, 0, 0, 0, 0, 0, 40, 40, 0, 0, 0, 0, 0, 0),
(12439, 'UNIX operatsioonisüsteemid', 80, 14, 'UNIX operatsioonisüsteemid (80); teoreetiline töö, praktiline töö; 80 tundi', 'AVP211', 0, 0, 0, 0, 30, 6, 0, 0, 0, 0, 0, 0, 40, 40, 0, 0, 0),
(12537, 'Veebiprogrammeerimine', 80, 21, 'Veebiprogrammeerimine (80); praktiline töö; 80 tundi', 'TAH13', 0, 0, 0, 20, 8, 0, 0, 0, 0, 0, 0, 40, 20, 20, 0, 0, 0),
(12553, 'Veebiarendus', 80, 16, 'Veebiarendus (160); teoreetiline töö, praktiline töö; 80 tundi', 'VS12', 30, 30, 0, 0, 0, 0, 0, 0, 40, 40, 0, 0, 0, 0, 0, 0, 0),
(12558, 'Veebitehnoloogiad', 80, 15, 'Veebitehnoloogiad (80); teoreetiline töö, praktiline töö; 80 tundi', 'VS12', 30, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 40, 0, 0),
(12656, 'Veebirakendused', 80, 9, 'Veebirakendused (80); praktiline töö; 80 tundi', '*2014-TTT13', 6, 2, 8, 2, 0, 0, 0, 0, 38, 2, 38, 2, 0, 0, 0, 0, 0),
(14941, 'Linux tööjaamade haldamine', 30, 0, 'Linux tööjaamade haldamine (40); teoreetiline töö; 30 tundi', 'ISP113', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30, 1),
(14942, 'Linux tööjaamade haldamine', 10, 0, 'Linux tööjaamade haldamine (40); praktiline töö; 10 tundi', 'ISP113', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0),
(14943, 'Linux tööjaamade haldamine', 10, 0, 'Linux tööjaamade haldamine (40); praktiline töö; 10 tundi', 'ISP113', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0),
(15441, 'Linux tööjaamade haldamine', 40, 0, 'Linux tööjaamade haldamine (40); teoreetiline töö, praktiline töö; 40 tundi', 'ISP213', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0),
(15765, 'Linux tööjaamade haldamine', 20, 30, 'Linux tööjaamade haldamine (40); teoreetiline töö; 20 tundi', 'IS13', 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 0, 1),
(15766, 'Linux tööjaamade haldamine', 20, 0, 'Linux tööjaamade haldamine (40); praktiline töö; 20 tundi', 'IS13', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 0, 0),
(15767, 'Linux tööjaamade haldamine', 20, 0, 'Linux tööjaamade haldamine (40); praktiline töö; 20 tundi', 'IS13', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 20, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE IF NOT EXISTS `grades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gradecount` int(11) NOT NULL,
  `daybook_id` int(10) unsigned NOT NULL,
  `period` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `gradecount`, `daybook_id`, `period`) VALUES
(1, 9, 12656, 1),
(2, 9, 12656, 2),
(3, 11, 12164, 1),
(4, 11, 12164, 2),
(5, 13, 12165, 1),
(6, 13, 12165, 2),
(7, 24, 12163, 1),
(8, 24, 12163, 2),
(9, 13, 12419, 1),
(10, 13, 12419, 2),
(11, 0, 12439, 1),
(12, 0, 12439, 2),
(13, 30, 15765, 1),
(14, 21, 12537, 1),
(15, 21, 12537, 2),
(16, 1, 12537, 3),
(17, 16, 12553, 1),
(18, 16, 12553, 2),
(19, 15, 12558, 1),
(20, 0, 12558, 2),
(21, 1, 12558, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pagedata`
--

CREATE TABLE IF NOT EXISTS `pagedata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `pagedata`
--

INSERT INTO `pagedata` (`id`, `name`, `content`) VALUES
(1, 'last_update', '18:56 15.03.2014');

-- --------------------------------------------------------

--
-- Table structure for table `tunnid`
--

CREATE TABLE IF NOT EXISTS `tunnid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `lessondate` date NOT NULL,
  `dayname` varchar(255) NOT NULL,
  `groupname` varchar(255) DEFAULT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `theory` tinyint(1) unsigned NOT NULL,
  `room` varchar(255) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `subject_id` int(10) unsigned DEFAULT NULL,
  `lesson_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_combo` (`subject`,`lessondate`,`starttime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
