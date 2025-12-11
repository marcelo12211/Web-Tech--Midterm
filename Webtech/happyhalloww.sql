-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 11, 2025 at 07:47 PM
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
-- Database: `happyhalloww`
--

-- --------------------------------------------------------

--
-- Table structure for table `deaths`
--

DROP TABLE IF EXISTS `deaths`;
CREATE TABLE IF NOT EXISTS `deaths` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_number` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `date_of_death` date NOT NULL,
  `cause_of_death` varchar(255) NOT NULL,
  `is_pwd` enum('yes','no') DEFAULT 'no',
  `is_senior` enum('yes','no') DEFAULT 'no',
  `pwd_id` varchar(50) DEFAULT NULL,
  `osca_id` varchar(50) DEFAULT NULL,
  `ncsc_rrn` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `resident_id` int UNSIGNED DEFAULT NULL,
  `place_of_death` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_number` (`record_number`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `deaths`
--

INSERT INTO `deaths` (`id`, `record_number`, `name`, `age`, `date_of_death`, `cause_of_death`, `is_pwd`, `is_senior`, `pwd_id`, `osca_id`, `ncsc_rrn`, `created_at`, `resident_id`, `place_of_death`) VALUES
(13, 'D-008', 'Pedro Reyes', 45, '2024-03-05', 'Accident', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:17:35', 8, NULL),
(12, 'D-007', 'Maria Santos', 62, '2024-02-15', 'Heart Attack', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:17:35', 7, NULL),
(11, 'D-006', 'Juan Dela Cruz', 78, '2024-01-20', 'Old Age (Natural)', 'no', 'yes', NULL, 'OSCA-1001', 'NCSC-9801', '2025-12-11 18:17:35', 6, NULL),
(6, 'D-001', 'Elena Dela Cruz', 75, '2023-01-15', 'Old Age (Natural)', 'no', 'yes', NULL, 'OSCA-1234', 'NCSC-5432', '2025-12-11 18:06:29', 1, NULL),
(7, 'D-002', 'Robert Sanchez', 52, '2023-03-20', 'Kidney Failure', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:06:29', 2, NULL),
(8, 'D-003', 'Luzviminda Garcia', 88, '2023-05-01', 'Old Age (Natural)', 'yes', 'yes', 'PWD-9876', 'OSCA-6789', 'NCSC-9876', '2025-12-11 18:06:29', 3, NULL),
(9, 'D-004', 'Marlon Ramos', 28, '2023-08-10', 'Accident', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:06:29', 4, NULL),
(10, 'D-005', 'Estella Lim', 65, '2024-02-28', 'Other', 'no', 'yes', NULL, 'OSCA-0101', 'NCSC-2020', '2025-12-11 18:06:29', 5, NULL),
(14, 'D-009', 'Liza Fernandez', 85, '2024-03-28', 'Old Age (Natural)', 'no', 'yes', NULL, 'OSCA-1002', 'NCSC-9802', '2025-12-11 18:17:35', 9, NULL),
(15, 'D-010', 'Jose Pineda', 55, '2024-04-10', 'Kidney Failure', 'yes', 'no', 'PWD-5001', NULL, NULL, '2025-12-11 18:17:35', 10, NULL),
(16, 'D-011', 'Anna Lopez', 70, '2024-05-01', 'Heart Attack', 'no', 'yes', NULL, 'OSCA-1003', 'NCSC-9803', '2025-12-11 18:17:35', 11, NULL),
(17, 'D-012', 'Michael Tan', 30, '2024-05-18', 'Other', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:17:35', 12, NULL),
(18, 'D-013', 'Sophia Cruz', 90, '2024-06-03', 'Old Age (Natural)', 'yes', 'yes', 'PWD-5002', 'OSCA-1004', 'NCSC-9804', '2025-12-11 18:17:35', 13, NULL),
(19, 'D-014', 'Ramon Garcia', 68, '2024-06-25', 'Kidney Failure', 'no', 'yes', NULL, 'OSCA-1005', 'NCSC-9805', '2025-12-11 18:17:35', 14, NULL),
(20, 'D-015', 'Nena Lim', 50, '2024-07-12', 'Heart Attack', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:17:35', 15, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `disabled_persons`
--

DROP TABLE IF EXISTS `disabled_persons`;
CREATE TABLE IF NOT EXISTS `disabled_persons` (
  `pwd_id` int NOT NULL AUTO_INCREMENT,
  `resident_id` int NOT NULL,
  `pwd_gov_id` varchar(100) NOT NULL,
  `disability_type` varchar(150) NOT NULL,
  `classification` varchar(150) DEFAULT NULL,
  `id_picture_path` varchar(255) DEFAULT NULL,
  `date_registered` date DEFAULT (curdate()),
  PRIMARY KEY (`pwd_id`),
  KEY `fk_pwd_resident` (`resident_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resident_id` int NOT NULL,
  `resident_name` varchar(255) NOT NULL,
  `doc_number` varchar(50) NOT NULL,
  `purpose` text,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(10) NOT NULL,
  `doc_type` varchar(100) NOT NULL DEFAULT 'Other Upload',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_number` (`doc_number`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `household_head` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `housing_ownership` varchar(50) DEFAULT NULL,
  `water_source` varchar(50) DEFAULT NULL,
  `toilet_facility` varchar(50) DEFAULT NULL,
  `electricity_source` varchar(50) DEFAULT NULL,
  `waste_disposal` varchar(50) DEFAULT NULL,
  `building_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`household_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1007 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`household_id`, `household_head`, `housing_ownership`, `water_source`, `toilet_facility`, `electricity_source`, `waste_disposal`, `building_type`) VALUES
(1001, NULL, 'Owned', 'Well', 'Flush Toilet', 'Electricity', 'Regular Collection', 'Concrete'),
(1002, NULL, 'Rented', 'River', 'Pit Latrine', 'Solar', 'Burning', 'Wood'),
(1003, NULL, 'Owned', 'Well', 'Flush Toilet', 'Electricity', 'Regular Collection', 'Concrete'),
(1004, NULL, 'Rented', 'River', 'Pit Latrine', 'Solar', 'Burning', 'Wood'),
(1005, NULL, 'Owned', 'Well', 'Flush Toilet', 'Electricity', 'Regular Collection', 'Concrete'),
(1006, NULL, 'Rented', 'River', 'Pit Latrine', 'Solar', 'Burning', 'Wood');

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
  `first_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `sex` text NOT NULL,
  `birthdate` date NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `nationality` varchar(20) NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `purok` int NOT NULL,
  `address` varchar(150) NOT NULL,
  `residency_start_date` date DEFAULT NULL,
  `education_level` varchar(20) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `is_senior` tinyint(1) NOT NULL,
  `is_disabled` tinyint(1) NOT NULL,
  `health_insurance` varchar(100) DEFAULT NULL,
  `vaccination` int DEFAULT NULL,
  `is_pregnant` tinyint(1) DEFAULT NULL,
  `children_count` int DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `household_id` (`household_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2037 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`person_id`, `household_id`, `first_name`, `middle_name`, `surname`, `suffix`, `sex`, `birthdate`, `civil_status`, `nationality`, `religion`, `purok`, `address`, `residency_start_date`, `education_level`, `occupation`, `is_senior`, `is_disabled`, `health_insurance`, `vaccination`, `is_pregnant`, `children_count`) VALUES
(2011, 1001, 'Juan', 'Carlos', 'Dela Cruz', '', 'M', '1985-03-15', 'Single', 'Filipino', 'Catholic', 0, '123 Main St', '2015-08-10', 'College', 'Teacher', 0, 0, 'PhilHealth', 0, 0, 0),
(2012, 1001, 'Maria', 'Lourdes', 'Santos', '', 'F', '1990-07-22', 'Married', 'Filipino', 'Catholic', 0, '124 Main St', '2020-05-01', 'High School', 'Nurse', 0, 0, 'PhilHealth', 0, 1, 2),
(2013, 1002, 'Pedro', 'Antonio', 'Reyes', '', 'M', '1975-11-05', 'Married', 'Filipino', 'Catholic', 0, '125 Main St', '1998-01-15', 'College', 'Farmer', 1, 0, 'None', 0, 0, 3),
(2014, 1003, 'Lucia', 'Fernandez', 'Gomez', '', 'F', '1968-02-18', 'Widowed', 'Filipino', 'Catholic', 0, '126 Main St', '1970-06-20', 'Elementary', 'Retired', 1, 1, 'PhilHealth', 0, 0, 0),
(2015, 1004, 'Carlos', 'Miguel', 'Torres', 'Jr.', 'M', '2000-09-12', 'Single', 'Filipino', 'Catholic', 0, '127 Main St', '2023-03-01', 'College', 'Student', 0, 0, 'PhilHealth', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `senior_citizens`
--

DROP TABLE IF EXISTS `senior_citizens`;
CREATE TABLE IF NOT EXISTS `senior_citizens` (
  `senior_id` int NOT NULL AUTO_INCREMENT,
  `resident_id` int NOT NULL,
  `senior_gov_id` varchar(100) NOT NULL,
  `id_picture_path` varchar(255) DEFAULT NULL,
  `date_registered` date DEFAULT (curdate()),
  `status` varchar(20) DEFAULT 'Active',
  `remarks` text,
  PRIMARY KEY (`senior_id`),
  KEY `fk_senior_resident` (`resident_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=9004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`) VALUES
(5004, 'staff4@example.com', 'password123', 'staff'),
(5005, 'staff5@example.com', 'password123', 'staff'),
(9001, 'admin101@example.com', 'password123', 'staff'),
(9002, 'admin102@example.com', 'password123', 'admin'),
(9003, 'admin103@example.com', 'password123', 'admin');

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
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
