-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2014 at 04:44 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `silvermahar_tunnid`
--
CREATE DATABASE IF NOT EXISTS `silvermahar_tunnid` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `silvermahar_tunnid`;

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
(1, 'last_update', '15:19 09.03.2014');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
