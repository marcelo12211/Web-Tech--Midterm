-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 15, 2026 at 11:49 AM
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance_profiles`
--

INSERT INTO `maintenance_profiles` (`id`, `resident_name`, `medical_condition`, `medicine`, `last_checkup`, `status`, `created_at`) VALUES
(2, 'Alcasid, Regine V.', 'secret', 'biogesic', '2026-01-02', 'Active Intake', '2026-01-15 11:43:33');

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
) ENGINE=InnoDB AUTO_INCREMENT=2094 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`person_id`, `household_id`, `first_name`, `middle_name`, `surname`, `suffix`, `sex`, `birthdate`, `civil_status`, `nationality`, `religion`, `purok`, `address`, `residency_start_date`, `education_level`, `occupation`, `is_senior`, `is_disabled`, `health_insurance`, `vaccination`, `is_pregnant`, `children_count`) VALUES
(2039, 0, 'Juan', 'Perez', 'Dela Cruz', '', 'Male', '1990-05-15', 'Married', 'Filipino', 'Catholic', 0, '123 Main St', '2010-01-01', 'College Graduate', 'Engineer', 0, 0, 'PhilHealth', 0, 0, 2),
(2040, 0, 'Maria', 'Santos', 'Reyes', '', 'Female', '1985-08-20', 'Single', 'Filipino', 'Catholic', 0, '456 Side St', '2015-03-12', 'High School', 'Vendor', 0, 0, 'PhilHealth', 0, 0, 0),
(2041, 0, 'Jose', 'Rizal', 'Protacio', '', 'Male', '1955-06-19', 'Widowed', 'Filipino', 'Catholic', 0, '789 Old Rd', '1980-11-20', 'Elementary', 'Retired', 1, 0, 'None', 0, 0, 5),
(2042, 0, 'Elena', 'Gomez', 'Salvador', '', 'Female', '1998-12-01', 'Single', 'Filipino', 'Iglesia ni Cristo', 0, '321 New St', '2020-05-05', 'College Level', 'Student', 0, 0, 'PhilHealth', 1, 0, 0),
(2043, 0, 'Ricardo', 'Dalisay', 'Cardo', '', 'Male', '1982-11-11', 'Married', 'Filipino', 'Catholic', 0, '001 Action Ave', '2018-09-30', 'Vocational', 'Police', 0, 0, 'PhilHealth', 0, 0, 1),
(2044, 0, 'Liza', 'Soberano', 'Hope', '', 'Female', '1998-01-04', 'Single', 'Filipino', 'Catholic', 0, '222 Star Blvd', '2021-01-15', 'College Graduate', 'Actress', 0, 0, 'HMO', 0, 0, 0),
(2045, 0, 'Benny', 'Abante', 'Santos', 'Jr', 'Male', '1970-02-25', 'Married', 'Filipino', 'Catholic', 0, '555 Hills Dr', '1995-06-10', 'High School', 'Driver', 0, 1, 'PhilHealth', 0, 0, 4),
(2046, 0, 'Ana', 'Marie', 'Cruz', '', 'Female', '1960-04-10', 'Married', 'Filipino', 'Catholic', 0, '999 Garden St', '1985-12-01', 'Elementary', 'Housewife', 1, 0, 'PhilHealth', 0, 0, 3),
(2047, 0, 'Kenzo', 'Morales', 'Soriano', '', 'Male', '2000-09-15', 'Single', 'Filipino', 'Other', 0, '101 Tech Way', '2022-03-01', 'College Graduate', 'IT Specialist', 0, 0, 'HMO', 0, 0, 0),
(2048, 0, 'Enrique', 'Gil', 'Moran', '', 'Male', '1992-03-30', 'Single', 'Filipino', 'Catholic', 0, '333 Talent Ln', '2019-10-10', 'College Level', 'Staff', 0, 0, 'PhilHealth', 0, 0, 0),
(2049, 0, 'Gardo', 'Versoza', 'Cupido', '', 'Male', '1969-11-08', 'Married', 'Filipino', 'Catholic', 0, '444 Gym Rd', '2005-01-01', 'High School', 'Fitness Coach', 0, 0, 'None', 0, 0, 2),
(2051, 0, 'Tirso', 'Cruz', 'Pimentel', 'III', 'Male', '1952-04-01', 'Married', 'Filipino', 'Catholic', 0, '888 Stage Rd', '1980-05-05', 'College Graduate', 'Retired', 1, 0, 'PhilHealth', 0, 0, 3),
(2052, 0, 'Vilma', 'Santos', 'Recto', '', 'Female', '1953-11-03', 'Married', 'Filipino', 'Catholic', 0, '222 Gov St', '1990-01-01', 'College Graduate', 'Politician', 1, 0, 'PhilHealth', 0, 0, 2),
(2053, 0, 'Piolo', 'Pascual', 'Jose', '', 'Male', '1977-01-12', 'Single', 'Filipino', 'Born Again', 0, '111 Heart Ave', '2000-01-01', 'College Graduate', 'Actor', 0, 0, 'HMO', 0, 0, 0),
(2054, 0, 'Bea', 'Alonzo', 'Phylbert', '', 'Female', '1987-10-17', 'Single', 'Filipino', 'Catholic', 0, '555 Movie Ln', '2010-01-01', 'High School', 'Businesswoman', 0, 0, 'HMO', 0, 0, 0),
(2055, 0, 'John', 'Lloyd', 'Cruz', '', 'Male', '1983-06-24', 'Single', 'Filipino', 'Catholic', 0, '444 Acting Way', '2005-01-01', 'High School', 'Freelancer', 0, 0, 'PhilHealth', 0, 0, 1),
(2056, 0, 'Angel', 'Locsin', 'Colmenares', '', 'Female', '1985-04-23', 'Married', 'Filipino', 'Catholic', 0, '777 Hero Rd', '2015-01-01', 'College Level', 'Volunteer', 0, 0, 'PhilHealth', 0, 0, 0),
(2057, 0, 'Dingdong', 'Dantes', 'Jose', '', 'Male', '1980-08-02', 'Married', 'Filipino', 'Catholic', 0, '999 Primetime St', '2014-01-01', 'College Graduate', 'Director', 0, 0, 'HMO', 0, 0, 2),
(2058, 0, 'Marian', 'Rivera', 'Gracia', '', 'Female', '1984-08-12', 'Married', 'Filipino', 'Catholic', 0, '999 Primetime St', '2014-01-01', 'College Graduate', 'Model', 0, 0, 'HMO', 0, 0, 2),
(2059, 0, 'Vic', 'Sotto', 'Magno', '', 'Male', '1954-04-28', 'Married', 'Filipino', 'Catholic', 0, '123 Eat Bldg', '1979-01-01', 'College Graduate', 'Host', 1, 0, 'PhilHealth', 0, 0, 5),
(2060, 0, 'Joey', 'De Leon', 'Rodriguez', '', 'Male', '1946-10-14', 'Married', 'Filipino', 'Catholic', 0, '123 Eat Bldg', '1979-01-01', 'College Graduate', 'Comedian', 1, 0, 'PhilHealth', 0, 0, 5),
(2061, 0, 'Tito', 'Sotto', 'Castelo', '', 'Male', '1948-08-24', 'Married', 'Filipino', 'Catholic', 0, '123 Eat Bldg', '1979-01-01', 'College Graduate', 'Retired', 1, 0, 'PhilHealth', 0, 0, 4),
(2062, 0, 'Catriona', 'Gray', 'Magnayon', '', 'Female', '1994-01-06', 'Single', 'Filipino', 'Christian', 0, '1 Miss Universe', '2018-01-01', 'College Graduate', 'Singer', 0, 0, 'HMO', 0, 0, 0),
(2063, 0, 'Pia', 'Wurtzbach', 'Alonzo', '', 'Female', '1989-09-24', 'Married', 'Filipino', 'Catholic', 0, '2 Miss Universe', '2015-01-01', 'College Graduate', 'Influencer', 0, 0, 'HMO', 0, 0, 0),
(2064, 0, 'Gloria', 'Diaz', 'Aspillera', '', 'Female', '1951-03-10', 'Widowed', 'Filipino', 'Catholic', 0, '3 Miss Universe', '1969-01-01', 'College Graduate', 'Actress', 1, 0, 'PhilHealth', 0, 0, 3),
(2065, 0, 'Manny', 'Pacquiao', 'Dapidran', '', 'Male', '1978-12-17', 'Married', 'Filipino', 'Evangelical', 0, '888 Boxing St', '1995-01-01', 'College Graduate', 'Athlete', 0, 0, 'PhilHealth', 0, 0, 5),
(2066, 0, 'Jinkee', 'Pacquiao', 'Jamora', '', 'Female', '1979-01-12', 'Married', 'Filipino', 'Evangelical', 0, '888 Boxing St', '1995-01-01', 'College Level', 'Businesswoman', 0, 0, 'PhilHealth', 0, 0, 5),
(2067, 0, 'Bamboo', 'Mañalac', 'Francisco', '', 'Male', '1976-03-21', 'Single', 'Filipino', 'Catholic', 0, '456 Rock Rd', '2000-01-01', 'College Level', 'Musician', 0, 0, 'None', 0, 0, 0),
(2068, 0, 'Lea', 'Salonga', 'Chiongbian', '', 'Female', '1971-02-22', 'Married', 'Filipino', 'Catholic', 0, 'Broadway Ave', '1980-01-01', 'College Graduate', 'Singer', 0, 0, 'HMO', 0, 0, 1),
(2069, 0, 'Vice', 'Ganda', 'Viceral', '', 'Male', '1976-03-31', 'Married', 'Filipino', 'Catholic', 0, 'Showtime St', '2009-01-01', 'College Level', 'Host', 0, 0, 'HMO', 0, 0, 0),
(2070, 0, 'Anne', 'Curtis', 'Smith', '', 'Female', '1985-02-17', 'Married', 'Filipino', 'Catholic', 0, 'Showtime St', '2000-01-01', 'College Level', 'Host', 0, 0, 'HMO', 0, 0, 1),
(2071, 0, 'Vhong', 'Navarro', 'Perez', '', 'Male', '1977-01-04', 'Married', 'Filipino', 'Catholic', 0, 'Showtime St', '1995-01-01', 'High School', 'Host', 0, 0, 'PhilHealth', 0, 0, 2),
(2072, 0, 'Gary', 'Valenciano', 'Santiago', '', 'Male', '1964-08-06', 'Married', 'Filipino', 'Christian', 0, 'Pure Energy Ln', '1983-01-01', 'College Level', 'Singer', 1, 0, 'HMO', 0, 0, 3),
(2073, 0, 'Martin', 'Nievera', 'Ramirez', '', 'Male', '1962-02-05', 'Single', 'Filipino', 'Catholic', 0, 'Concert Dr', '1982-01-01', 'College Level', 'Singer', 1, 0, 'HMO', 0, 0, 2),
(2074, 0, 'Regine', 'Velasquez', 'Alcasid', '', 'Female', '1970-04-22', 'Married', 'Filipino', 'Catholic', 0, 'Songbird Way', '1986-01-01', 'High School', 'Singer', 0, 0, 'PhilHealth', 0, 0, 1),
(2075, 0, 'Ogie', 'Alcasid', 'Herminio', '', 'Male', '1967-08-27', 'Married', 'Filipino', 'Catholic', 0, 'Songbird Way', '2010-01-01', 'College Graduate', 'Singer', 0, 0, 'PhilHealth', 0, 0, 3),
(2076, 0, 'Daniel', 'Padilla', 'Ford', '', 'Male', '1995-04-26', 'Single', 'Filipino', 'Catholic', 0, 'Kathniel St', '2011-01-01', 'High School', 'Actor', 0, 0, 'PhilHealth', 0, 0, 0),
(2077, 0, 'Kathryn', 'Bernardo', 'Chandria', '', 'Female', '1996-03-26', 'Single', 'Filipino', 'Catholic', 0, 'Kathniel St', '2003-01-01', 'College Graduate', 'Actress', 0, 0, 'HMO', 0, 0, 0),
(2078, 0, 'James', 'Reid', 'Robert', '', 'Male', '1993-05-11', 'Single', 'Filipino', 'Catholic', 0, 'Careless Ave', '2010-01-01', 'High School', 'Singer', 0, 0, 'None', 0, 0, 0),
(2079, 0, 'Nadine', 'Lustre', 'Alexis', '', 'Female', '1993-10-31', 'Single', 'Filipino', 'Catholic', 0, 'Island Rd', '2005-01-01', 'High School', 'Actress', 0, 0, 'None', 0, 0, 0),
(2080, 0, 'Alden', 'Richards', 'Faulkerson', '', 'Male', '1992-01-02', 'Single', 'Filipino', 'Catholic', 0, 'AlDub Ln', '2010-01-01', 'College Level', 'Actor', 0, 0, 'PhilHealth', 0, 0, 0),
(2081, 0, 'Maine', 'Mendoza', 'Capili', '', 'Female', '1995-03-03', 'Married', 'Filipino', 'Catholic', 0, 'AlDub Ln', '2015-01-01', 'College Graduate', 'Actress', 0, 0, 'PhilHealth', 0, 0, 0),
(2082, 0, 'Coco', 'Martin', 'Nacianceno', '', 'Male', '1981-11-01', 'Single', 'Filipino', 'Catholic', 0, 'Probinsyano St', '2005-01-01', 'College Graduate', 'Actor', 0, 0, 'PhilHealth', 0, 0, 0),
(2083, 0, 'Judy', 'Ann', 'Santos', '', 'Female', '1978-05-11', 'Married', 'Filipino', 'Catholic', 0, 'Kitchen Ave', '1986-01-01', 'High School', 'Chef', 0, 0, 'HMO', 0, 0, 3),
(2084, 0, 'Ryan', 'Agoncillo', 'Quinto', '', 'Male', '1979-04-10', 'Married', 'Filipino', 'Catholic', 0, 'Kitchen Ave', '2000-01-01', 'College Graduate', 'Host', 0, 0, 'HMO', 0, 0, 3),
(2085, 0, 'Kim', 'Chiu', 'Luy', '', 'Female', '1990-04-19', 'Single', 'Filipino', 'Catholic', 0, 'PBB House', '2006-01-01', 'College Level', 'Actress', 0, 0, 'PhilHealth', 0, 0, 0),
(2086, 0, 'Gerald', 'Anderson', 'Randolph', '', 'Male', '1989-03-07', 'Single', 'Filipino', 'Catholic', 0, 'PBB House', '2006-01-01', 'High School', 'Actor', 0, 0, 'PhilHealth', 0, 0, 0),
(2087, 0, 'Enchong', 'Dee', 'Sebastian', '', 'Male', '1988-11-05', 'Single', 'Filipino', 'Catholic', 0, 'Pool Side', '2006-01-01', 'College Graduate', 'Swimmer', 0, 0, 'PhilHealth', 0, 0, 0),
(2088, 0, 'Maja', 'Salvador', 'Ross', '', 'Female', '1988-10-05', 'Married', 'Filipino', 'Catholic', 0, 'Dance Floor', '2003-01-01', 'High School', 'Dancer', 0, 0, 'PhilHealth', 0, 0, 0),
(2092, 1005, 'qqweqwe', '', 'qweqwe', '', 'Male', '2026-01-14', 'Single', 'Filipino', '', 5, 'asdasd', '2026-01-11', 'College', 'Student', 0, 0, 'N/A', 0, 0, 0),
(2093, 1005, 'jerald', '', 'fajardo', '', 'Male', '2000-02-02', 'Single', 'Filipino', NULL, 4, 'asdasd', NULL, 'College', 'Student', 1, 0, NULL, 0, 0, 0);

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
(9006, 'Jerald Fajardo', 'Jeraldfajardo@gmail.com', 'password123', 'admin', 'active');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vaccination_records`
--

INSERT INTO `vaccination_records` (`id`, `resident_name`, `vaccine_type`, `dose`, `date_administered`, `status`, `created_at`) VALUES
(1, 'Alexis, Nadine L.', 'vax', '1st dose', '2026-01-09', 'Pending', '2026-01-15 11:43:56');

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
