-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2026 at 11:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capstonesample_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

CREATE TABLE `access_log` (
  `access_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `device_id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `os_version` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `log_id` int(11) NOT NULL,
  `device_id` int(11) DEFAULT NULL,
  `building` varchar(100) NOT NULL,
  `floor` varchar(20) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `log_id`, `device_id`, `building`, `floor`, `room`, `time_stamp`) VALUES
(1, 1, NULL, 'PGT Building', NULL, NULL, '2026-10-09 14:02:14'),
(2, 2, NULL, 'GPL Building', NULL, NULL, '2026-10-10 13:02:05'),
(3, 3, NULL, 'LRC Building', NULL, NULL, '2026-10-15 09:13:04');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `date_generated` datetime DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `role` enum('admin','guard','staff') NOT NULL DEFAULT 'guard',
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `firstname`, `lastname`, `role`, `email`, `status`, `created_at`) VALUES
(1, 'admin', 'CHANGE_ME_USE_password_hash', 'Admin', 'User', 'admin', 'admin@example.com', 'active', '2026-10-09 14:02:14'),
(2, 'guard1', 'CHANGE_ME_USE_password_hash', 'Guard', 'One', 'guard', 'guard1@example.com', 'active', '2026-10-09 14:02:14');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `visitor_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `company_organization` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `visitor_type` enum('Student','Faculty','Parent','Guest') NOT NULL,
  `birthdate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`visitor_id`, `firstname`, `middlename`, `lastname`, `contact_number`, `email`, `company_organization`, `address`, `id_type`, `id_number`, `photo`, `visitor_type`, `birthdate`, `created_at`) VALUES
(1, 'John', NULL, 'Doe', '09123456789', NULL, NULL, NULL, NULL, NULL, NULL, 'Guest', NULL, '2026-10-09 14:02:14'),
(2, 'afgsag', NULL, '', '214325435436436', NULL, NULL, NULL, NULL, NULL, NULL, 'Student', NULL, '2026-10-10 13:02:05'),
(3, 'afgsag', NULL, '', '214325435436436', NULL, NULL, NULL, NULL, NULL, NULL, 'Student', '2005-06-12', '2026-10-15 09:13:04');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_log`
--

CREATE TABLE `visitor_log` (
  `log_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_time_in` datetime NOT NULL DEFAULT current_timestamp(),
  `date_time_out` datetime DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('active','checked_out') NOT NULL DEFAULT 'active',
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_log`
--

INSERT INTO `visitor_log` (`log_id`, `visitor_id`, `user_id`, `date_time_in`, `date_time_out`, `purpose`, `remarks`, `status`, `qr_code`, `created_at`) VALUES
(1, 1, 2, '2026-10-09 22:02:14', NULL, 'Inquiry', NULL, 'active', NULL, '2026-10-09 14:02:14'),
(2, 2, 2, '2026-10-10 21:02:05', NULL, NULL, NULL, 'active', NULL, '2026-10-10 13:02:05'),
(3, 3, 2, '2026-10-15 17:13:04', NULL, NULL, NULL, 'active', NULL, '2026-10-15 09:13:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_log`
--
ALTER TABLE `access_log`
  ADD PRIMARY KEY (`access_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`device_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `log_id` (`log_id`) USING BTREE,
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitor_id`);

--
-- Indexes for table `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_log`
--
ALTER TABLE `access_log`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `visitor_log`
--
ALTER TABLE `visitor_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_log`
--
ALTER TABLE `access_log`
  ADD CONSTRAINT `access_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `visitor_log` (`log_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_ibfk_2` FOREIGN KEY (`device_id`) REFERENCES `device` (`device_id`) ON DELETE SET NULL;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `visitor_log`
--
ALTER TABLE `visitor_log`
  ADD CONSTRAINT `visitor_log_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitor` (`visitor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
