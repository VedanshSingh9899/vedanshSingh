-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 07, 2025 at 05:26 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Takshshila`
--

-- --------------------------------------------------------

--
-- Table structure for table `Test_data`
--

CREATE TABLE `Test_data` (
  `T_id` varchar(20) NOT NULL,
  `total_points` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE `user_data` (
  `uid` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email_id` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_password`
--

CREATE TABLE `user_password` (
  `uid` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `mid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_test_progress`
--

CREATE TABLE `user_test_progress` (
  `mid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `T_id` varchar(20) NOT NULL,
  `scored_points` int(11) DEFAULT 0,
  `marks_obtained` int(11) DEFAULT 0,
  `result` enum('Pass','Fail') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Test_data`
--
ALTER TABLE `Test_data`
  ADD PRIMARY KEY (`T_id`);

--
-- Indexes for table `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email_id` (`email_id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `user_password`
--
ALTER TABLE `user_password`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`mid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `user_test_progress`
--
ALTER TABLE `user_test_progress`
  ADD PRIMARY KEY (`mid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `T_id` (`T_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_password`
--
ALTER TABLE `user_password`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_test_progress`
--
ALTER TABLE `user_test_progress`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_data`
--
ALTER TABLE `user_data`
  ADD CONSTRAINT `user_data_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user_password` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user_password` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `user_test_progress`
--
ALTER TABLE `user_test_progress`
  ADD CONSTRAINT `user_test_progress_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user_password` (`uid`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_test_progress_ibfk_2` FOREIGN KEY (`T_id`) REFERENCES `Test_data` (`T_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
