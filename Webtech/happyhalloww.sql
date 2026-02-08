-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 08, 2026 at 04:39 PM
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(20, 'D-015', 'Nena Lim', 50, '2024-07-12', 'Heart Attack', 'no', 'no', NULL, NULL, NULL, '2025-12-11 18:17:35', 15, NULL),
(22, 'D-016', 'Fajardo, jeje J.', 50, '2026-01-01', 'Old Age (Natural)', 'no', 'no', '', '', '', '2026-01-22 07:04:23', NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_profiles`
--

DROP TABLE IF EXISTS `health_profiles`;
CREATE TABLE IF NOT EXISTS `health_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resident_id` int NOT NULL,
  `medical_condition` varchar(255) DEFAULT NULL,
  `maintenance_meds` varchar(255) DEFAULT NULL,
  `last_checkup` date DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`id`),
  KEY `resident_id` (`resident_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Table structure for table `maintenance_profiles`
--

DROP TABLE IF EXISTS `maintenance_profiles`;
CREATE TABLE IF NOT EXISTS `maintenance_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resident_name` varchar(255) NOT NULL,
  `medical_condition` varchar(255) DEFAULT NULL,
  `medicine` varchar(255) DEFAULT NULL,
  `last_checkup` date DEFAULT NULL,
  `status` enum('Active Intake','For Refill','Completed') DEFAULT 'Active Intake',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance_profiles`
--

INSERT INTO `maintenance_profiles` (`id`, `resident_name`, `medical_condition`, `medicine`, `last_checkup`, `status`, `created_at`) VALUES
(9, 'Fajardo, jeje J.', 'Sick', 'Biogesic', '2026-01-06', 'Active Intake', '2026-01-22 06:55:41');

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
) ENGINE=InnoDB AUTO_INCREMENT=2095 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`person_id`, `household_id`, `first_name`, `middle_name`, `surname`, `suffix`, `sex`, `birthdate`, `civil_status`, `nationality`, `religion`, `purok`, `address`, `residency_start_date`, `education_level`, `occupation`, `is_senior`, `is_disabled`, `health_insurance`, `vaccination`, `is_pregnant`, `children_count`) VALUES
(18, 301, 'Ricardo', 'Dalisay', 'Dalisay', NULL, 'Male', '1985-04-12', 'Married', 'Filipino', 'Catholic', 3, 'Poblacion', '2010-05-20', 'College Graduate', 'Police Officer', 0, 0, 'PhilHealth', 0, 0, 2),
(19, 301, 'Alyana', 'Arevalo', 'Dalisay', NULL, 'Female', '1988-11-25', 'Married', 'Filipino', 'Catholic', 3, 'Poblacion', '2012-08-15', 'College Graduate', 'Reporter', 0, 0, 'HMO', 0, 1, 2),
(20, 302, 'Teresita', 'Cruz', 'Magbanua', NULL, 'Female', '1952-01-30', 'Widowed', 'Filipino', 'Christian', 1, '14 Old Road', '1975-02-10', 'Elementary Graduate', 'Retired', 1, 0, 'PhilHealth', 0, 0, 5),
(21, 303, 'Mark', 'Anthony', 'Fernandez', 'Jr.', 'Male', '1995-06-14', 'Single', 'Filipino', 'Catholic', 5, '99 Riverside', '2018-01-05', 'Vocational', 'Mechanic', 0, 0, 'None', 0, 0, 0),
(22, 304, 'Luzviminda', 'Santos', 'Reyes', NULL, 'Female', '1970-05-22', 'Married', 'Filipino', 'Catholic', 2, '45 Malaya St.', '1995-12-01', 'High School Graduate', 'Vendor', 0, 0, 'PhilHealth', 0, 0, 3),
(23, 305, 'Esteban', 'Gomez', 'Quinto', NULL, 'Male', '1948-12-03', 'Married', 'Filipino', 'Catholic', 4, 'Sec 2 Lot 9', '1980-11-12', 'High School Graduate', 'Farmer', 1, 1, 'PhilHealth', 0, 0, 4),
(24, 306, 'Samantha', 'Lee', 'Tan', NULL, 'Female', '1992-09-08', 'Single', 'Filipino-Chinese', 'Buddhist', 1, '12 Orchid Lane', '2015-04-20', 'Post-Graduate', 'Accountant', 0, 0, 'Private', 0, 0, 0),
(25, 307, 'Rodrigo', 'Duterte', 'Roa', NULL, 'Male', '1945-03-28', 'Separated', 'Filipino', 'Catholic', 5, 'Davao St.', '2020-01-01', 'College Graduate', 'Public Servant', 1, 0, 'PhilHealth', 0, 0, 4),
(26, 308, 'Maria', 'Clara', 'Ibarra', NULL, 'Female', '2001-02-14', 'Single', 'Filipino', 'Catholic', 2, '77 History Ave.', '2001-02-14', 'College Student', 'Student', 0, 0, 'Private', 0, 0, 0),
(27, 309, 'Jonathan', 'Wick', 'Mendoza', NULL, 'Male', '1980-07-07', 'Widowed', 'Filipino', 'None', 4, 'Hidden Valley', '2019-03-15', 'College Graduate', 'Security', 0, 0, 'HMO', 0, 0, 0),
(28, 310, 'Gabriela', 'Silang', 'Estrada', NULL, 'Female', '1994-10-10', 'Married', 'Filipino', 'Catholic', 3, 'Freedom St.', '2016-05-10', 'College Graduate', 'Teacher', 0, 0, 'PhilHealth', 0, 0, 1),
(29, 311, 'Fernando', 'Poe', 'Agila', 'III', 'Male', '1965-08-20', 'Married', 'Filipino', 'Catholic', 1, 'Film St.', '1990-10-12', 'High School Graduate', 'Driver', 0, 0, 'PhilHealth', 0, 0, 3),
(30, 312, 'Catriona', 'Magnayon', 'Gray', NULL, 'Female', '1994-01-06', 'Single', 'Filipino', 'Christian', 5, 'Lava Walk', '2018-02-14', 'College Graduate', 'Model', 0, 0, 'Private', 0, 0, 0),
(31, 313, 'Emilio', 'Aguinaldo', 'Famy', NULL, 'Male', '1950-03-22', 'Married', 'Filipino', 'Catholic', 2, 'Kawit Road', '1970-11-20', 'High School Graduate', 'Retired Clerk', 1, 0, 'PhilHealth', 0, 0, 6),
(32, 314, 'Leonor', 'Rivera', 'Mercado', NULL, 'Female', '1990-04-11', 'Single', 'Filipino', 'Catholic', 4, 'Calamba St.', '2012-01-10', 'College Graduate', 'Nurse', 0, 0, 'HMO', 0, 0, 0),
(33, 315, 'Andres', 'Bonifacio', 'Castro', NULL, 'Male', '1983-11-30', 'Married', 'Filipino', 'Catholic', 3, 'Tondo Ave.', '2005-05-05', 'Vocational', 'Laborer', 0, 0, 'None', 0, 0, 2),
(34, 316, 'Melchora', 'Aquino', 'Ramos', NULL, 'Female', '1935-01-06', 'Widowed', 'Filipino', 'Catholic', 1, 'Banlat Rd.', '1960-09-09', 'None', 'Retired Vendor', 1, 1, 'PhilHealth', 0, 0, 7),
(35, 317, 'Jose', 'Protacio', 'Rizal', NULL, 'Male', '1988-06-19', 'Single', 'Filipino', 'Catholic', 5, 'Bagumbayan', '2015-04-12', 'Post-Graduate', 'Doctor', 0, 0, 'Private', 0, 0, 0),
(36, 318, 'Pia', 'Alonzo', 'Wurtzbach', NULL, 'Female', '1989-09-24', 'Married', 'Filipino', 'Catholic', 2, 'Stuttgart St.', '2023-06-20', 'College Graduate', 'Influencer', 0, 0, 'Private', 0, 0, 0),
(37, 319, 'Vicente', 'Sotto', 'Tito', 'IV', 'Male', '1970-08-24', 'Married', 'Filipino', 'Catholic', 4, 'White Plains', '1998-02-28', 'College Graduate', 'Musician', 0, 0, 'HMO', 0, 0, 4),
(38, 320, 'Leni', 'Gerona', 'Robredo', NULL, 'Female', '1965-04-23', 'Widowed', 'Filipino', 'Catholic', 3, 'Naga Lane', '2022-01-01', 'Post-Graduate', 'Lawyer', 0, 0, 'PhilHealth', 0, 0, 3),
(39, 321, 'Ramon', 'Magsaysay', 'Del Fierro', NULL, 'Male', '1953-08-31', 'Married', 'Filipino', 'Christian', 1, 'Zambales Dr.', '1985-03-10', 'College Graduate', 'Mechanic', 1, 0, 'PhilHealth', 0, 0, 3),
(40, 322, 'Corazon', 'Cojuangco', 'Aquino', NULL, 'Female', '1933-01-25', 'Widowed', 'Filipino', 'Catholic', 5, 'Times St.', '1960-12-14', 'College Graduate', 'Retired', 1, 0, 'Private', 0, 0, 5),
(41, 323, 'Benigno', 'Simeon', 'Aquino', 'III', 'Male', '1960-02-08', 'Single', 'Filipino', 'Catholic', 2, 'Times St.', '1985-06-01', 'College Graduate', 'Economist', 0, 0, 'Private', 0, 0, 0),
(42, 324, 'Gloria', 'Macapagal', 'Arroyo', NULL, 'Female', '1947-04-05', 'Married', 'Filipino', 'Catholic', 4, 'Pampanga Rd.', '1998-04-28', 'Post-Graduate', 'Economist', 1, 0, 'HMO', 0, 0, 3),
(43, 325, 'Joseph', 'Ejercito', 'Estrada', NULL, 'Male', '1937-04-19', 'Married', 'Filipino', 'Catholic', 1, 'San Juan St.', '2014-12-30', 'College Undergraduat', 'Actor', 1, 0, 'PhilHealth', 0, 0, 3),
(44, 326, 'Imelda', 'Remedios', 'Marcos', NULL, 'Female', '1929-07-02', 'Widowed', 'Filipino', 'Catholic', 3, 'Leyte Dr.', '2014-12-30', 'College Graduate', 'Retired', 1, 0, 'Private', 0, 0, 3),
(45, 327, 'Ferdinand', 'Emmanuel', 'Marcos', 'Jr.', 'Male', '1957-09-13', 'Married', 'Filipino', 'Catholic', 5, 'Ilocos St.', '2022-06-30', 'Post-Graduate', 'President', 1, 0, 'PhilHealth', 0, 0, 3),
(46, 328, 'Sara', 'Zimmerman', 'Duterte', NULL, 'Female', '1978-05-31', 'Married', 'Filipino', 'Christian', 2, 'Davao Central', '2022-06-30', 'Post-Graduate', 'Lawyer', 0, 0, 'PhilHealth', 0, 0, 3),
(47, 329, 'Manny', 'Dapidran', 'Pacquiao', NULL, 'Male', '1978-12-17', 'Married', 'Filipino', 'Christian', 4, 'GenSan', '2000-01-01', 'College Graduate', 'Athlete', 0, 0, 'Private', 0, 0, 5),
(48, 330, 'Isabel', 'Daza', 'Semblat', NULL, 'Female', '1988-03-06', 'Married', 'Filipino', 'Catholic', 1, 'Makati', '2015-08-07', 'College Graduate', 'Businesswoman', 0, 0, 'Private', 0, 0, 2),
(49, 331, 'Robin', 'Cariño', 'Padilla', NULL, 'Male', '1969-11-23', 'Married', 'Filipino', 'Islam', 5, 'Fairview', '2018-05-15', 'High School Graduate', 'Senator', 0, 0, 'None', 0, 0, 4),
(50, 332, 'Risa', 'Baraquel', 'Hontiveros', NULL, 'Female', '1966-02-24', 'Widowed', 'Filipino', 'Catholic', 3, 'Pasig', '2010-01-10', 'College Graduate', 'Senator', 0, 0, 'HMO', 0, 0, 4),
(51, 333, 'Vico', 'Nubla', 'Sotto', NULL, 'Male', '1989-06-17', 'Single', 'Filipino', 'Catholic', 2, 'Pasig City Hall', '2019-01-01', 'Post-Graduate', 'Mayor', 0, 0, 'PhilHealth', 0, 0, 0),
(52, 334, 'Hidilyn', 'Francisco', 'Diaz', NULL, 'Female', '1991-02-20', 'Married', 'Filipino', 'Catholic', 4, 'Zamboanga', '2021-05-20', 'College Graduate', 'Military', 0, 0, 'Private', 0, 0, 0),
(53, 335, 'Bambam', 'Pineda', 'Sotto', NULL, 'Male', '2015-05-05', 'Single', 'Filipino', 'Christian', 1, 'Mandaluyong', '2015-06-12', 'Elementary Student', 'None', 0, 0, 'PhilHealth', 0, 0, 0),
(54, 336, 'Maria', 'Leonora', 'Theresa', NULL, 'Female', '2022-12-25', 'Single', 'Filipino', 'Catholic', 5, 'New Birth Rd.', '2022-12-25', 'Infant', 'None', 0, 0, 'PhilHealth', 0, 0, 0),
(55, 337, 'Juan', 'Luna', 'Novicio', NULL, 'Male', '1975-10-23', 'Married', 'Filipino', 'Catholic', 2, 'Spoliarium St.', '2010-11-12', 'College Graduate', 'Painter', 0, 0, 'HMO', 0, 0, 2),
(56, 338, 'Melanie', 'Marquez', 'Lawyer', NULL, 'Female', '1964-07-16', 'Married', 'Filipino', 'Christian', 4, 'Beauty St.', '2000-11-12', 'High School Graduate', 'Trainer', 0, 0, 'Private', 0, 0, 6),
(57, 339, 'Efren', 'Bata', 'Reyes', NULL, 'Male', '1954-08-26', 'Married', 'Filipino', 'Catholic', 3, 'Cue Road', '1980-01-01', 'High School Graduate', 'Professional', 1, 0, 'HMO', 0, 0, 3),
(58, 340, 'Miriam', 'Defensor', 'Santiago', NULL, 'Female', '1945-06-15', 'Married', 'Filipino', 'Catholic', 1, 'Quezon City', '1970-01-01', 'Post-Graduate', 'Judge', 1, 0, 'Private', 0, 0, 2),
(59, 341, 'Paolo', 'Ballesteros', 'Ty', NULL, 'Male', '1982-11-29', 'Single', 'Filipino', 'Catholic', 5, 'Antipolo', '2005-01-01', 'College Graduate', 'Artist', 0, 0, 'Private', 0, 0, 1),
(60, 342, 'Maine', 'Mendoza', 'Mendoza', NULL, 'Female', '1995-03-03', 'Married', 'Filipino', 'Catholic', 2, 'Bulacan', '2015-07-04', 'College Graduate', 'Host', 0, 0, 'Private', 0, 0, 0),
(61, 343, 'Arjo', 'Atayde', 'Atayde', NULL, 'Male', '1990-11-05', 'Married', 'Filipino', 'Catholic', 4, 'Quezon City', '2023-07-28', 'College Graduate', 'Politician', 0, 0, 'Private', 0, 0, 0),
(62, 344, 'Coco', 'Martin', 'Martin', NULL, 'Male', '1981-11-01', 'Single', 'Filipino', 'Catholic', 3, 'Fairview', '2005-01-01', 'College Graduate', 'Actor', 0, 0, 'HMO', 0, 0, 0),
(63, 345, 'Vice', 'Ganda', 'Viceral', NULL, 'Male', '1976-03-31', 'Married', 'Filipino', 'Catholic', 1, 'Quezon City', '2000-01-01', 'College Graduate', 'Host', 0, 0, 'Private', 0, 0, 0),
(64, 346, 'Anne', 'Curtis', 'Smith', NULL, 'Female', '1985-02-17', 'Married', 'Filipino', 'Catholic', 5, 'Makati', '2010-01-01', 'College Graduate', 'Endorser', 0, 0, 'Private', 0, 0, 1),
(65, 347, 'Billy', 'Crawford', 'Crawford', NULL, 'Male', '1982-05-16', 'Married', 'Filipino', 'Christian', 2, 'Laguna', '2018-01-01', 'High School Graduate', 'Singer', 0, 0, 'Private', 0, 0, 1),
(66, 348, 'Coleen', 'Garcia', 'Crawford', NULL, 'Female', '1992-09-24', 'Married', 'Filipino', 'Christian', 4, 'Laguna', '2018-09-24', 'College Graduate', 'Actress', 0, 0, 'Private', 0, 0, 1),
(67, 349, 'Vhong', 'Navarro', 'Navarro', NULL, 'Male', '1977-01-04', 'Married', 'Filipino', 'Catholic', 3, 'Quezon City', '2000-01-01', 'High School Graduate', 'Dancer', 0, 0, 'HMO', 0, 0, 2),
(68, 350, 'Kim', 'Chiu', 'Chiu', NULL, 'Female', '1990-04-19', 'Single', 'Filipino', 'Catholic', 1, 'Cebu', '2006-01-01', 'College Graduate', 'Actress', 0, 0, 'Private', 0, 0, 0),
(69, 351, 'Xian', 'Lim', 'Lim', NULL, 'Male', '1989-07-12', 'Single', 'Filipino', 'Christian', 5, 'Pasig', '2010-01-01', 'College Graduate', 'Actor', 0, 0, 'HMO', 0, 0, 0),
(70, 352, 'Judy', 'Ann', 'Santos', NULL, 'Female', '1978-05-11', 'Married', 'Filipino', 'Catholic', 2, 'Antipolo', '2009-04-28', 'College Graduate', 'Chef', 0, 0, 'Private', 0, 0, 3),
(71, 353, 'Ryan', 'Agoncillo', 'Agoncillo', NULL, 'Male', '1979-04-10', 'Married', 'Filipino', 'Catholic', 4, 'Antipolo', '2009-04-28', 'College Graduate', 'Host', 0, 0, 'Private', 0, 0, 3),
(72, 354, 'Piolo', 'Pascual', 'Pascual', NULL, 'Male', '1977-01-12', 'Single', 'Filipino', 'Christian', 3, 'Batangas', '2000-01-01', 'College Graduate', 'Singer', 0, 0, 'HMO', 0, 0, 1),
(73, 355, 'Angel', 'Locsin', 'Arce', NULL, 'Female', '1985-04-23', 'Married', 'Filipino', 'Catholic', 1, 'Bukidnon', '2021-08-07', 'College Graduate', 'Activist', 0, 0, 'Private', 0, 0, 0),
(74, 356, 'John', 'Lloyd', 'Cruz', NULL, 'Male', '1983-06-24', 'Single', 'Filipino', 'Catholic', 5, 'Cebu', '2018-05-15', 'High School Graduate', 'Actor', 0, 0, 'None', 0, 0, 1),
(75, 357, 'Bea', 'Alonzo', 'Alonzo', NULL, 'Female', '1987-10-17', 'Single', 'Filipino', 'Catholic', 2, 'Zambales', '2020-01-10', 'College Graduate', 'Vlogger', 0, 0, 'HMO', 0, 0, 0),
(76, 358, 'Daniel', 'Padilla', 'Padilla', NULL, 'Male', '1995-04-26', 'Single', 'Filipino', 'Catholic', 4, 'Fairview', '2010-01-01', 'High School Graduate', 'Actor', 0, 0, 'HMO', 0, 0, 0),
(77, 359, 'Kathryn', 'Bernardo', 'Bernardo', NULL, 'Female', '1996-03-26', 'Single', 'Filipino', 'Catholic', 3, 'Quezon City', '2012-05-20', 'College Graduate', 'Business', 0, 0, 'Private', 0, 0, 0),
(78, 360, 'Toni', 'Gonzaga', 'Soriano', NULL, 'Female', '1984-01-20', 'Married', 'Filipino', 'Christian', 1, 'Taytay', '2015-06-12', 'College Graduate', 'Producer', 0, 0, 'Private', 0, 0, 2),
(79, 360, 'Paul', 'Soriano', 'Soriano', NULL, 'Male', '1981-10-17', 'Married', 'Filipino', 'Christian', 1, 'Taytay', '2015-06-12', 'Post-Graduate', 'Director', 0, 0, 'Private', 0, 0, 2),
(80, 361, 'Isko', 'Moreno', 'Domagoso', NULL, 'Male', '1974-10-24', 'Married', 'Filipino', 'Catholic', 5, 'Tondo', '1974-10-24', 'Post-Graduate', 'Public Servant', 0, 0, 'PhilHealth', 0, 0, 5),
(81, 362, 'Mel', 'Tiangco', 'Tiangco', NULL, 'Female', '1955-08-10', 'Single', 'Filipino', 'Catholic', 2, 'GMA', '1980-05-20', 'College Graduate', 'Anchor', 1, 0, 'Private', 0, 0, 0),
(82, 363, 'Jessica', 'Soho', 'Soho', NULL, 'Female', '1964-03-27', 'Single', 'Filipino', 'Catholic', 4, 'La Union', '1985-01-01', 'College Graduate', 'Broadcaster', 1, 0, 'Private', 0, 0, 0),
(83, 364, 'Kara', 'David', 'David', NULL, 'Female', '1973-09-12', 'Married', 'Filipino', 'Christian', 3, 'UP', '1995-03-15', 'Post-Graduate', 'Professor', 0, 0, 'PhilHealth', 0, 0, 1),
(84, 365, 'Atom', 'Araullo', 'Araullo', NULL, 'Male', '1982-10-19', 'Single', 'Filipino', 'Catholic', 1, 'Loyola', '1982-10-19', 'College Graduate', 'Journalist', 0, 0, 'HMO', 0, 0, 0),
(85, 366, 'Karen', 'Davila', 'Davila', NULL, 'Female', '1970-11-21', 'Married', 'Filipino', 'Christian', 5, 'Forbes', '1995-08-10', 'College Graduate', 'Broadcaster', 0, 0, 'Private', 0, 0, 2),
(86, 367, 'Korina', 'Sanchez', 'Roxas', NULL, 'Female', '1964-10-05', 'Married', 'Filipino', 'Catholic', 2, 'Araneta', '2009-10-27', 'College Graduate', 'Host', 1, 0, 'HMO', 0, 0, 2),
(87, 368, 'Noli', 'De Castro', 'De Castro', NULL, 'Male', '1949-07-06', 'Widowed', 'Filipino', 'Catholic', 4, 'Mindoro', '1970-01-01', 'College Graduate', 'Commentator', 1, 0, 'PhilHealth', 0, 0, 3),
(88, 369, 'Bong', 'Revilla', 'Revilla', 'Jr.', 'Male', '1966-09-25', 'Married', 'Filipino', 'Catholic', 3, 'Cavite', '1966-09-25', 'High School Graduate', 'Senator', 0, 0, 'Private', 0, 0, 6),
(89, 370, 'Jinggoy', 'Estrada', 'Estrada', NULL, 'Male', '1963-02-17', 'Married', 'Filipino', 'Catholic', 1, 'Greenhills', '1980-01-01', 'College Graduate', 'Senator', 1, 0, 'PhilHealth', 0, 0, 4),
(90, 371, 'Bong', 'Go', 'Go', NULL, 'Male', '1974-06-14', 'Married', 'Filipino', 'Christian', 5, 'Davao', '1990-01-01', 'College Graduate', 'Senator', 0, 0, 'Private', 0, 0, 3),
(91, 372, 'Bato', 'Dela Rosa', 'Dela Rosa', NULL, 'Male', '1962-01-21', 'Married', 'Filipino', 'Catholic', 2, 'Davao', '1980-01-01', 'Post-Graduate', 'Senator', 1, 0, 'PhilHealth', 0, 0, 3),
(92, 373, 'Cynthia', 'Villar', 'Villar', NULL, 'Female', '1950-07-29', 'Married', 'Filipino', 'Catholic', 4, 'Las Piñas', '1975-01-01', 'Post-Graduate', 'Senator', 1, 0, 'Private', 0, 0, 3),
(93, 374, 'Raffy', 'Tulfo', 'Tulfo', NULL, 'Male', '1960-03-12', 'Married', 'Filipino', 'Christian', 3, 'TV5', '1990-01-01', 'College Graduate', 'Senator', 1, 0, 'Private', 0, 0, 5),
(94, 375, 'Imee', 'Marcos', 'Marcos', NULL, 'Female', '1955-11-12', 'Single', 'Filipino', 'Catholic', 1, 'Ilocos', '1955-11-12', 'Post-Graduate', 'Senator', 1, 0, 'PhilHealth', 0, 0, 3),
(95, 376, 'Joel', 'Villanueva', 'Villanueva', NULL, 'Male', '1975-08-02', 'Married', 'Filipino', 'Christian', 5, 'Bulacan', '1975-08-02', 'Post-Graduate', 'Senator', 0, 0, 'Private', 0, 0, 2),
(96, 377, 'Mark', 'Villar', 'Villar', NULL, 'Male', '1978-08-14', 'Married', 'Filipino', 'Catholic', 2, 'Vista', '2000-01-01', 'Post-Graduate', 'Senator', 0, 0, 'Private', 0, 0, 1),
(97, 378, 'Win', 'Gatchalian', 'Gatchalian', NULL, 'Male', '1974-04-06', 'Single', 'Filipino', 'Christian', 4, 'Valenzuela', '1990-01-01', 'College Graduate', 'Senator', 0, 0, 'Private', 0, 0, 0),
(98, 379, 'Nancy', 'Binay', 'Binay', NULL, 'Female', '1973-05-12', 'Married', 'Filipino', 'Catholic', 3, 'Makati', '1973-05-12', 'College Graduate', 'Senator', 0, 0, 'PhilHealth', 0, 0, 4),
(99, 380, 'Koko', 'Pimentel', 'Pimentel', NULL, 'Male', '1964-01-20', 'Married', 'Filipino', 'Catholic', 1, 'CDO', '1985-01-01', 'Post-Graduate', 'Senator', 1, 0, 'PhilHealth', 0, 0, 2),
(100, 381, 'Migz', 'Zubiri', 'Zubiri', NULL, 'Male', '1969-04-13', 'Married', 'Filipino', 'Catholic', 5, 'Bukidnon', '1985-01-01', 'Post-Graduate', 'Senator', 0, 0, 'Private', 0, 0, 3);

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `senior_citizens`
--

INSERT INTO `senior_citizens` (`senior_id`, `resident_id`, `senior_gov_id`, `id_picture_path`, `date_registered`, `status`, `remarks`) VALUES
(1, 2093, '', NULL, '2026-01-14', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `password`, `role`, `status`) VALUES
(5002, 'Enrique Moran', 'Enriquemoran@gmail.com', 'password123', 'staff', 'active'),
(5003, 'Kenzo Soriano', 'Kenzosoriano@gmail.com', 'password123', 'staff', 'active'),
(9005, 'LJ De Guzman', 'LJDeGuzman@gmail.com', 'password123', 'admin', 'active'),
(9006, 'Jerald J. Fajardo', 'Jeraldfajardo@gmail.com', 'password123', 'admin', 'active');

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

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_records`
--

DROP TABLE IF EXISTS `vaccination_records`;
CREATE TABLE IF NOT EXISTS `vaccination_records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resident_name` varchar(255) NOT NULL,
  `vaccine_type` varchar(255) DEFAULT NULL,
  `dose` varchar(50) DEFAULT NULL,
  `date_administered` date DEFAULT NULL,
  `status` enum('Completed','Pending') DEFAULT 'Completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vaccination_records`
--

INSERT INTO `vaccination_records` (`id`, `resident_name`, `vaccine_type`, `dose`, `date_administered`, `status`, `created_at`) VALUES
(2, 'Tito Sotto Castelo', 'covid', '1st dose', '2026-01-08', 'Pending', '2026-01-15 13:39:23');

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
