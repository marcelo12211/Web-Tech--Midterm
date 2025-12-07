-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 05, 2025 at 07:36 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `happyhallow`
--

-- --------------------------------------------------------

--
-- Table structure for table `health_records`
--

DROP TABLE IF EXISTS `health_records`;
CREATE TABLE IF NOT EXISTS `health_records` (
  `record_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `diseases` varchar(100) DEFAULT NULL,
  `medical_conditions` varchar(100) DEFAULT NULL,
  `family_planning_method` varchar(50) DEFAULT NULL,
  `pregnancy_history` varchar(50) DEFAULT NULL,
  `last_checkup_date` date DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`record_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

DROP TABLE IF EXISTS `household`;
CREATE TABLE IF NOT EXISTS `household` (
  `household_id` int NOT NULL AUTO_INCREMENT,
  `household_head` varchar(100) NOT NULL,
  `housing_ownership` varchar(50) DEFAULT NULL,
  `water_source` varchar(50) DEFAULT NULL,
  `toilet_facility` varchar(50) DEFAULT NULL,
  `electricity_source` varchar(50) DEFAULT NULL,
  `waste_disposal` varchar(50) DEFAULT NULL,
  `building_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`household_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `migration_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `moved_in_date` date DEFAULT NULL,
  `moved_out_date` date DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `previous_address` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`migration_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

DROP TABLE IF EXISTS `residents`;
CREATE TABLE IF NOT EXISTS `residents` (
  `person_id` int NOT NULL AUTO_INCREMENT,
  `household_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `sex` int NOT NULL,
  `birthdate` date NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `nationality` varchar(20) NOT NULL,
  `religion` varchar(20) NOT NULL,
  `purok` int NOT NULL,
  `address` varchar(150) NOT NULL,
  `education_level` varchar(20) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `is_senior` tinyint(1) NOT NULL,
  `is_disabled` tinyint(1) NOT NULL,
  `disability_type` varchar(20) DEFAULT NULL,
  `health_insurance` varchar(100) DEFAULT NULL,
  `vaccination` int DEFAULT NULL,
  `is_pregnant` tinyint(1) NOT NULL,
  `children_count` int DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `household_id` (`household_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `date_encoded` datetime NOT NULL,
  `encoded_by` varchar(100) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `health_records`
--
ALTER TABLE `health_records`
  ADD CONSTRAINT `health_record-resident` FOREIGN KEY (`person_id`) REFERENCES `residents` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `migration`
--
ALTER TABLE `migration`
  ADD CONSTRAINT `migration-resident` FOREIGN KEY (`person_id`) REFERENCES `residents` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `residents`
--
ALTER TABLE `residents`
  ADD CONSTRAINT `household_id` FOREIGN KEY (`household_id`) REFERENCES `household` (`household_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_log`
--
ALTER TABLE `user_log`
  ADD CONSTRAINT `user_log-resident` FOREIGN KEY (`person_id`) REFERENCES `residents` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
