-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 07, 2025 at 05:29 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `health_records`
--

INSERT INTO `health_records` (`record_id`, `person_id`, `diseases`, `medical_conditions`, `family_planning_method`, `pregnancy_history`, `last_checkup_date`, `remarks`) VALUES
(1, 1001, 'None', 'None', 'Pills', 'N/A', '2025-11-10', 'Healthy, advised regular exercise.'),
(2, 1002, 'Hypertension', 'Diabetes', 'N/A', 'N/A', '2025-12-01', 'Needs maintenance medication for BP and blood sugar monitoring.'),
(3, 1003, 'None', 'Hearing Impairment', 'N/A', 'N/A', '2024-06-15', 'Annual checkup done. Needs regular hearing aid maintenance.'),
(4, 1004, 'None', 'None', 'None', 'First Pregnancy', '2025-12-05', 'Confirmed 3 months pregnant. Scheduled for next prenatal visit.'),
(5, 1005, 'Asthma', 'Allergies', 'Natural Family Planning', 'N/A', '2025-10-20', 'Stable condition, provided with updated anti-asthma medication.');

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`household_id`, `household_head`, `housing_ownership`, `water_source`, `toilet_facility`, `electricity_source`, `waste_disposal`, `building_type`) VALUES
(14, 'Delia V. Cruz', 'Rented', 'Piped (Faucet)', 'Flush Type', 'Electric Company', 'Garbage Collector', 'Concrete'),
(15, 'Marco L. Diaz', 'Owned', 'Deep Well', 'Pit Latrine', 'Solar', 'Burning', 'Semi-Concrete'),
(16, 'Rosa M. Lopez', 'Free / Informal', 'Piped (Communal)', 'Flush Type', 'Electric Company', 'Composting', 'Light Materials'),
(17, 'Benigno T. Rivas', 'Mortgaged', 'Deep Well', 'Septic Tank', 'Generator', 'Dumping', 'Concrete'),
(18, 'Lea A. Garcia', 'Owned', 'Spring', 'Pit Latrine', 'Electric Company', 'Garbage Collector', 'Semi-Concrete');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`migration_id`, `person_id`, `moved_in_date`, `moved_out_date`, `reason`, `previous_address`) VALUES
(1, 1001, '2022-03-01', NULL, 'New Residency', 'Caloocan City'),
(2, 1003, NULL, '2025-01-15', 'Transfer to nearby Barangay', 'N/A'),
(3, 1004, '2024-11-20', NULL, 'Relocation', 'Quezon City'),
(4, 1002, NULL, '2024-05-10', 'Job Assignment', 'Manila'),
(5, 1002, '2024-12-01', NULL, 'Return from Job Assignment', 'Manila');

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
) ENGINE=InnoDB AUTO_INCREMENT=1006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`person_id`, `household_id`, `full_name`, `sex`, `birthdate`, `civil_status`, `nationality`, `religion`, `purok`, `address`, `education_level`, `occupation`, `is_senior`, `is_disabled`, `disability_type`, `health_insurance`, `vaccination`, `is_pregnant`, `children_count`) VALUES
(1001, 10, 'Maria S. Dela Cruz', 0, '1998-05-15', 'Single', 'Filipino', 'Catholic', 3, 'Block 2 Lot 5', 'College Graduate', 'Teacher', 0, 0, NULL, 'PhilHealth', 0, 0, 0),
(1002, 10, 'Juan F. Santos', 0, '1955-11-20', 'Married', 'Filipino', 'Catholic', 3, 'Block 2 Lot 5', 'High School Grad', 'Retired', 1, 0, NULL, 'SSS Pension', 0, 0, 3),
(1003, 11, 'Lito C. Reyes', 0, '1970-01-01', 'Married', 'Filipino', 'Protestant', 1, 'Purok 1 St.', 'Elementary', 'Driver', 0, 1, 'Hearing Impaired', 'Private', 0, 0, 2),
(1004, 12, 'Jenny M. Ramos', 0, '2000-08-25', 'Single', 'Filipino', 'Catholic', 5, 'Phase 3, Unit 12', 'College Level', 'Student', 0, 0, NULL, 'None', 0, 1, 0),
(1005, 13, 'Ben A. Lim', 0, '1940-03-10', 'Widowed', 'Chinese', 'Buddhist', 2, '14th Avenue', 'Post Graduate', 'None', 1, 0, NULL, 'Private', 0, 0, 5);

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`) VALUES
(6, 'finance_admin@barangay.ph', 'hashed_password_123', 'Admin'),
(7, 'records_client@barangay.ph', 'hashed_password_123', 'Client'),
(8, 'purok2_admin@barangay.ph', 'hashed_password_123', 'Admin'),
(9, 'client_youth@barangay.ph', 'hashed_password_123', 'Client'),
(10, 'client_health2@barangay.ph', 'hashed_password_123', 'Client');

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
-- Constraints for table `user_log`
--
ALTER TABLE `user_log`
  ADD CONSTRAINT `user_log-resident` FOREIGN KEY (`person_id`) REFERENCES `residents` (`person_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
