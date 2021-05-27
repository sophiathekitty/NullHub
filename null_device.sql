-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 19, 2021 at 11:08 AM
-- Server version: 10.3.27-MariaDB-0+deb10u1
-- PHP Version: 7.3.27-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `null_device`
--

-- --------------------------------------------------------

--
-- Table structure for table `Colors`
--

CREATE TABLE `Colors` (
  `id` varchar(20) NOT NULL,
  `color` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Colors`
--

INSERT INTO `Colors` (`id`, `color`) VALUES
('am', '#ffffcc'),
('display_default', '#ffffff'),
('elevenEleven', '#6495ed'),
('fourFiveSix', '#f4a460'),
('fourOhFour', '#adff2f'),
('fourTwenty', '#008000'),
('hum_max', '#054a7f'),
('hum_min', '#b1c577'),
('hybrid', '#83b218'),
('indica', '#6d335e'),
('indicahybrid', '#77bc1f'),
('oneOneOne', '#d87093'),
('oneTwoThreeFour', '#663399'),
('pm', '#ccffff'),
('sativa', '#d34727'),
('sativaHybrid', '#85c723'),
('sevenTen', '#daa520'),
('temp_0', '#8900d3'),
('temp_1', '#8900d3'),
('temp_10', '#f32a1a'),
('temp_11', '#aa0000'),
('temp_2', '#6300e9'),
('temp_3', '#4600fd'),
('temp_4', '#4814f9'),
('temp_5', '#4a80f4'),
('temp_6', '#4cb6f2'),
('temp_7', '#e1db51'),
('temp_8', '#dec21d'),
('temp_9', '#e5921c'),
('wind_max', '#372402'),
('wind_min', '#dcf8e7');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `floor` int(11) NOT NULL,
  `bedtime` time DEFAULT NULL,
  `awake_time` time DEFAULT NULL,
  `activity` int(11) NOT NULL,
  `sunlight_offset` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `mac_address` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `main` tinyint(1) NOT NULL DEFAULT 0,
  `last_ping` datetime NOT NULL,
  `online` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `name` varchar(50) NOT NULL,
  `value` varchar(200) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`, `modified`) VALUES
('check_for_update', '0', '2021-05-19 07:06:28'),
('enabled', '1', '2021-05-13 04:09:44'),
('git', 'https://github.com/sophiathekitty/micro_display.git', '2021-05-19 06:54:14'),
('main', '0', '2021-05-13 04:15:28'),
('name', 'Sophi Micro Display', '2021-05-19 06:47:10'),
('path', '/', '2021-05-19 06:26:43'),
('room_id', '2', '2021-05-19 06:47:14'),
('server', 'pi4', '2021-05-19 06:26:17'),
('type', 'display', '2021-05-13 04:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `Task`
--

CREATE TABLE `Task` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `completed_by` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `skipped` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `due` datetime DEFAULT NULL,
  `completed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `bedroom_id` int(11) DEFAULT NULL,
  `bedtime` time DEFAULT NULL,
  `awake_time` time DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Colors`
--
ALTER TABLE `Colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `Task`
--
ALTER TABLE `Task`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Task`
--
ALTER TABLE `Task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
