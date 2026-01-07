-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 07, 2026 at 01:33 PM
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
(19, 'D-014', 'Ramon Garcia', 68, '2024-06-25', 'Kidney Failure', 'no', 'yes', NULL, 'OSCA-1005', 'NCSC-9805', '2025-12-11 18:17:35', 14, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=1009 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`household_id`, `household_head`, `housing_ownership`, `water_source`, `toilet_facility`, `electricity_source`, `waste_disposal`, `building_type`) VALUES
(1001, 'Aguilar, Neil R', 'Owned', 'BAWADI', 'Water Sealed', 'BENECO', 'Collected by Garbage Truck', 'Concrete'),
(1002, 'Lim, Cathy J', 'Rented', 'BAWADI', 'Pit Latrine', 'Solar', 'Burning', 'Wood'),
(1003, 'Baltazar, Rico T', 'Owned', 'BAWADI', 'Water Sealed', 'Solar', 'Collected by Garbage Truck', 'Concrete'),
(1004, 'Garcia, Ana C', 'Rented', 'BAWADI', 'Water Sealed', 'Solar', 'Recycling', 'Concrete'),
(1005, 'Aquino, Dennis L', 'Owned', 'BAWADI', 'Water Sealed', 'BENECO', 'Collected by Garbage Truck', 'Concrete'),
(1006, 'Bautista, Helen E', 'Rented', 'BAWADI', 'Water Sealed', 'Solar', 'Collected by Garbage Truck', 'Semi-Concrete'),
(1007, NULL, 'Owned', 'BAWADI', 'Water Sealed', 'BENECO', 'Collected by Garbage Truck', 'Concrete'),
(1008, NULL, 'Rented', 'BAWADI', 'Water Sealed', 'BENECO', 'Collected by Garbage Truck', 'Concrete');

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
) ENGINE=InnoDB AUTO_INCREMENT=2090 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`person_id`, `household_id`, `first_name`, `middle_name`, `surname`, `suffix`, `sex`, `birthdate`, `civil_status`, `nationality`, `religion`, `purok`, `address`, `residency_start_date`, `education_level`, `occupation`, `is_senior`, `is_disabled`, `health_insurance`, `vaccination`, `is_pregnant`, `children_count`) VALUES
(2000, 1001, 'Juan', 'D', 'Cruz', NULL, 'M', '1985-01-12', 'Married', 'Filipino', 'Catholic', 1, '12 Mabini Street', '2014-02-10', 'College', 'Driver', 0, 0, 'PhilHealth', 1, 0, 2),
(2001, 1002, 'Maria', 'L', 'Santos', NULL, 'F', '1990-03-25', 'Married', 'Filipino', 'Catholic', 2, '45 Rizal Avenue', '2016-06-18', 'College', 'Teacher', 0, 0, 'PhilHealth', 1, 0, 1),
(2002, 1003, 'Pedro', 'M', 'Reyes', NULL, 'M', '1972-07-09', 'Married', 'Filipino', 'Catholic', 3, '78 Del Pilar Road', '2010-08-01', 'High School', 'Farmer', 0, 0, 'PhilHealth', 1, 0, 4),
(2003, 1004, 'Ana', 'C', 'Garcia', NULL, 'F', '2001-11-30', 'Single', 'Filipino', 'Catholic', 4, '9 Bonifacio Street', '2021-01-15', 'College', 'Student', 0, 0, 'None', 1, 0, 0),
(2004, 1005, 'Jose', 'R', 'Lopez', 'Jr', 'M', '1961-04-12', 'Married', 'Filipino', 'Catholic', 5, '101 Quezon Avenue', '2004-05-20', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 5),
(2005, 1006, 'Liza', 'T', 'Mendoza', NULL, 'F', '1994-08-19', 'Single', 'Filipino', 'Christian', 1, '33 Luna Street', '2018-09-10', 'College', 'Office Staff', 0, 0, 'PhilHealth', 1, 0, 0),
(2006, 1001, 'Ramon', 'B', 'Torres', NULL, 'M', '1983-12-03', 'Married', 'Filipino', 'Catholic', 2, '56 Burgos Road', '2013-06-06', 'High School', 'Mechanic', 0, 0, 'PhilHealth', 1, 0, 3),
(2007, 1002, 'Grace', 'N', 'Flores', NULL, 'F', '1988-01-25', 'Married', 'Filipino', 'Catholic', 3, '18 Laurel Street', '2012-10-15', 'College', 'Nurse', 0, 0, 'Private', 1, 0, 2),
(2008, 1003, 'Mark', 'S', 'Villanueva', NULL, 'M', '1997-07-09', 'Single', 'Filipino', 'Catholic', 4, '72 Jacinto Lane', '2019-11-01', 'College', 'Agent', 0, 0, 'PhilHealth', 1, 0, 0),
(2009, 1004, 'Joy', 'A', 'Perez', NULL, 'F', '1976-03-17', 'Married', 'Filipino', 'Catholic', 5, '6 Malvar Street', '2008-08-08', 'High School', 'Housewife', 0, 0, 'PhilHealth', 1, 0, 4),
(2010, 1005, 'Noel', 'E', 'Ramos', NULL, 'M', '1966-05-22', 'Married', 'Filipino', 'Catholic', 1, '90 Aguinaldo Highway', '2003-04-19', 'High School', 'Guard', 1, 0, 'PhilHealth', 1, 0, 3),
(2011, 1006, 'Ella', 'P', 'Castro', NULL, 'F', '2002-10-11', 'Single', 'Filipino', 'Catholic', 2, '14 Salonga Street', '2021-01-05', 'Senior High', 'Student', 0, 0, 'None', 1, 0, 0),
(2012, 1001, 'Ben', 'H', 'Navarro', NULL, 'M', '1993-06-30', 'Single', 'Filipino', 'Catholic', 3, '27 Abad Santos Road', '2017-12-12', 'College', 'Salesman', 0, 0, 'PhilHealth', 1, 0, 0),
(2013, 1002, 'Cathy', 'J', 'Lim', NULL, 'F', '1984-09-18', 'Married', 'Filipino', 'Buddhist', 4, '88 Gomez Street', '2011-03-23', 'College', 'Entrepreneur', 0, 0, 'Private', 1, 0, 2),
(2014, 1003, 'Alfred', 'K', 'Diaz', NULL, 'M', '1991-01-15', 'Single', 'Filipino', 'Catholic', 5, '41 Roxas Boulevard', '2016-02-10', 'College', 'Technician', 0, 0, 'PhilHealth', 1, 0, 0),
(2015, 1004, 'Rhea', 'M', 'Cortez', NULL, 'F', '1987-04-22', 'Married', 'Filipino', 'Catholic', 1, '63 Evangelista Street', '2013-05-18', 'College', 'Clerk', 0, 0, 'PhilHealth', 1, 0, 2),
(2016, 1005, 'Dennis', 'L', 'Aquino', NULL, 'M', '1979-07-09', 'Married', 'Filipino', 'Catholic', 2, '25 Kalayaan Avenue', '2009-08-01', 'High School', 'Electrician', 0, 0, 'PhilHealth', 1, 0, 3),
(2017, 1006, 'Sheila', 'R', 'Morales', NULL, 'F', '1999-12-02', 'Single', 'Filipino', 'Catholic', 3, '7 Fajardo Street', '2021-06-20', 'College', 'Student', 0, 0, 'None', 1, 0, 0),
(2018, 1001, 'Arnold', 'S', 'Pascual', NULL, 'M', '1962-10-27', 'Married', 'Filipino', 'Catholic', 4, '110 Narra Road', '2006-11-14', 'Elementary', 'Driver', 1, 0, 'PhilHealth', 1, 0, 4),
(2019, 1002, 'Kim', 'A', 'Ocampo', NULL, 'F', '1994-03-11', 'Single', 'Filipino', 'Christian', 5, '52 Sampaguita Street', '2017-04-09', 'College', 'Staff', 0, 0, 'Private', 1, 0, 0),
(2020, 1003, 'Leo', 'B', 'Manalo', NULL, 'M', '1981-06-05', 'Married', 'Filipino', 'Catholic', 1, '84 Tandang Sora Avenue', '2012-07-30', 'High School', 'Welder', 0, 0, 'PhilHealth', 1, 0, 3),
(2021, 1004, 'Nina', 'C', 'Valdez', NULL, 'F', '1989-09-14', 'Married', 'Filipino', 'Catholic', 2, '19 Pineda Street', '2014-10-12', 'College', 'Accountant', 0, 0, 'Private', 1, 0, 1),
(2022, 1005, 'Paolo', 'D', 'Razon', NULL, 'M', '1997-02-18', 'Single', 'Filipino', 'Catholic', 3, '67 Osmena Highway', '2019-03-21', 'College', 'Designer', 0, 0, 'PhilHealth', 1, 0, 0),
(2023, 1006, 'Helen', 'E', 'Bautista', NULL, 'F', '1972-05-26', 'Married', 'Filipino', 'Catholic', 4, '5 Yakal Street', '2007-06-15', 'High School', 'Housewife', 0, 0, 'PhilHealth', 1, 0, 5),
(2024, 1001, 'Victor', 'F', 'Salazar', NULL, 'M', '1968-08-03', 'Married', 'Filipino', 'Catholic', 5, '92 Molave Road', '2004-09-17', 'High School', 'Caretaker', 0, 0, 'PhilHealth', 1, 0, 2),
(2025, 1002, 'Mika', 'G', 'Tan', NULL, 'F', '2001-11-19', 'Single', 'Filipino', 'Catholic', 1, '16 Jade Street', '2022-01-08', 'College', 'Student', 0, 0, 'None', 1, 0, 0),
(2026, 1003, 'Ryan', 'H', 'Delos Santos', NULL, 'M', '1986-04-07', 'Married', 'Filipino', 'Catholic', 2, '39 Pearl Drive', '2011-05-11', 'College', 'Supervisor', 0, 0, 'PhilHealth', 1, 0, 2),
(2027, 1004, 'Faith', 'I', 'Marquez', NULL, 'F', '1992-07-28', 'Single', 'Filipino', 'Christian', 3, '73 Diamond Street', '2016-08-19', 'College', 'Staff', 0, 0, 'Private', 1, 0, 0),
(2028, 1005, 'Oscar', 'J', 'Natividad', NULL, 'M', '1974-10-10', 'Married', 'Filipino', 'Catholic', 4, '21 Ruby Lane', '2008-11-05', 'High School', 'Driver', 0, 0, 'PhilHealth', 1, 0, 4),
(2029, 1006, 'Lourdes', 'K', 'Rosales', NULL, 'F', '1963-01-23', 'Widowed', 'Filipino', 'Catholic', 5, '58 Emerald Road', '2002-02-14', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 6),
(2030, 1001, 'Jason', 'L', 'Ilagan', NULL, 'M', '1995-05-16', 'Single', 'Filipino', 'Catholic', 1, '11 Sapphire Street', '2018-06-09', 'College', 'Support', 0, 0, 'PhilHealth', 1, 0, 0),
(2031, 1002, 'Carla', 'M', 'Fernandez', NULL, 'F', '1983-08-29', 'Married', 'Filipino', 'Catholic', 2, '66 Topaz Avenue', '2010-09-27', 'College', 'Teller', 0, 0, 'Private', 1, 0, 2),
(2032, 1003, 'Tony', 'N', 'Uy', NULL, 'M', '1979-12-04', 'Married', 'Filipino', 'Buddhist', 3, '95 Opal Road', '2009-01-18', 'College', 'Owner', 0, 0, 'Private', 1, 0, 3),
(2033, 1004, 'April', 'O', 'Pineda', NULL, 'F', '1998-03-06', 'Single', 'Filipino', 'Catholic', 4, '28 Amethyst Street', '2020-04-22', 'College', 'Seller', 0, 0, 'PhilHealth', 1, 0, 0),
(2034, 1005, 'Gilbert', 'P', 'Soriano', NULL, 'M', '1966-06-12', 'Married', 'Filipino', 'Catholic', 5, '102 Garnet Lane', '2003-07-01', 'High School', 'Staff', 0, 0, 'PhilHealth', 1, 0, 3),
(2035, 1006, 'Donna', 'Q', 'Velasco', NULL, 'F', '1985-09-01', 'Married', 'Filipino', 'Catholic', 1, '44 Onyx Street', '2012-10-20', 'College', 'Officer', 0, 0, 'Private', 1, 0, 2),
(2036, 1001, 'Neil', 'R', 'Aguilar', NULL, 'M', '1990-11-17', 'Single', 'Filipino', 'Catholic', 2, '37 Quartz Road', '2015-12-05', 'College', 'Auditor', 0, 0, 'PhilHealth', 1, 0, 0),
(2037, 1002, 'Jessa', 'S', 'Padilla', NULL, 'F', '2003-02-24', 'Single', 'Filipino', 'Catholic', 3, '8 Beryl Street', '2023-03-10', 'Senior High', 'Student', 0, 0, 'None', 1, 0, 0),
(2038, 1003, 'Rico', 'T', 'Baltazar', NULL, 'M', '1971-07-31', 'Married', 'Filipino', 'Catholic', 4, '59 Citrine Avenue', '2006-08-16', 'High School', 'Worker', 0, 0, 'PhilHealth', 1, 0, 5),
(2039, 1004, 'Elaine', 'U', 'Montes', NULL, 'F', '1996-04-19', 'Single', 'Filipino', 'Catholic', 5, '13 Peridot Street', '2019-05-27', 'College', 'Assistant', 0, 0, 'PhilHealth', 1, 0, 0),
(2040, 1001, 'Manuel', 'A', 'Rivera', NULL, 'M', '1959-02-14', 'Married', 'Filipino', 'Catholic', 1, '12 Mabuhay Street', '2005-01-10', 'High School', 'Driver', 1, 0, 'PhilHealth', 1, 0, 4),
(2041, 1002, 'Carmen', 'B', 'Delos Reyes', NULL, 'F', '1961-05-03', 'Widowed', 'Filipino', 'Catholic', 1, '45 Pagasa Road', '2003-06-22', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 5),
(2042, 1003, 'Rodolfo', 'C', 'Mateo', NULL, 'M', '1957-11-19', 'Married', 'Filipino', 'Catholic', 1, '78 Liwanag Avenue', '2000-08-18', 'High School', 'Security', 1, 0, 'PhilHealth', 1, 0, 3),
(2043, 1004, 'Jenny', 'D', 'Flores', NULL, 'F', '1994-06-21', 'Married', 'Filipino', 'Catholic', 1, '9 Maligaya Street', '2020-03-01', 'College', 'Housewife', 0, 0, 'PhilHealth', 1, 1, 1),
(2044, 1005, 'Eduardo', 'E', 'Panganiban', NULL, 'M', '1956-09-08', 'Married', 'Filipino', 'Catholic', 1, '101 Sampaguita Lane', '1999-04-12', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 6),
(2045, 1006, 'Lourdes', 'F', 'Cruz', NULL, 'F', '1963-12-02', 'Married', 'Filipino', 'Catholic', 1, '33 Rosal Street', '2006-07-14', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2046, 1001, 'Arnel', 'G', 'Villamor', NULL, 'M', '1987-01-25', 'Single', 'Filipino', 'Catholic', 1, '56 Narra Road', '2014-09-05', 'College', 'Technician', 0, 1, 'PhilHealth', 1, 0, 0),
(2047, 1002, 'Melissa', 'H', 'Aquino', NULL, 'F', '1998-08-30', 'Married', 'Filipino', 'Catholic', 1, '18 Ilang-Ilang Street', '2021-02-11', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 0),
(2048, 1003, 'Rolando', 'I', 'Abella', NULL, 'M', '1955-04-17', 'Married', 'Filipino', 'Catholic', 1, '72 Banaba Avenue', '1998-10-09', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 7),
(2049, 1004, 'Estela', 'J', 'Moreno', NULL, 'F', '1962-07-29', 'Widowed', 'Filipino', 'Catholic', 1, '6 Mabini Road', '2004-11-20', 'High School', 'Seamstress', 1, 0, 'PhilHealth', 1, 0, 3),
(2050, 1005, 'Ricardo', 'K', 'Soriano', NULL, 'M', '1958-03-12', 'Married', 'Filipino', 'Catholic', 1, '90 Rizal Avenue', '2001-05-06', 'High School', 'Guard', 1, 0, 'PhilHealth', 1, 0, 5),
(2051, 1006, 'Aileen', 'L', 'Montoya', NULL, 'F', '1996-10-05', 'Single', 'Filipino', 'Catholic', 1, '14 Camia Street', '2019-08-14', 'College', 'None', 0, 1, 'PhilHealth', 1, 0, 0),
(2052, 1001, 'Leonardo', 'M', 'Espino', NULL, 'M', '1960-06-01', 'Married', 'Filipino', 'Catholic', 1, '27 Acacia Road', '2002-09-17', 'High School', 'Driver', 1, 0, 'PhilHealth', 1, 0, 4),
(2053, 1002, 'Rose', 'N', 'Fabian', NULL, 'F', '1993-02-18', 'Married', 'Filipino', 'Catholic', 1, '63 Gumamela Street', '2018-12-10', 'College', 'Clerk', 0, 0, 'PhilHealth', 1, 1, 2),
(2054, 1003, 'Benjamin', 'O', 'Tolentino', NULL, 'M', '1954-11-11', 'Married', 'Filipino', 'Catholic', 1, '88 Molave Drive', '1997-03-22', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 6),
(2055, 1004, 'Nancy', 'P', 'Robles', NULL, 'F', '1961-09-04', 'Widowed', 'Filipino', 'Catholic', 1, '41 Kamagong Road', '2005-01-30', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2056, 1005, 'Jerome', 'Q', 'Padua', NULL, 'M', '1989-05-26', 'Single', 'Filipino', 'Catholic', 1, '19 Talisay Street', '2016-06-11', 'College', 'Staff', 0, 1, 'PhilHealth', 1, 0, 0),
(2057, 1006, 'Hazel', 'R', 'Santiago', NULL, 'F', '1997-12-19', 'Married', 'Filipino', 'Catholic', 1, '52 Orchid Street', '2020-09-09', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 1),
(2058, 1001, 'Francisco', 'S', 'Lazaro', NULL, 'M', '1959-01-07', 'Married', 'Filipino', 'Catholic', 2, '7 Mango Street', '2003-02-15', 'High School', 'Carpenter', 1, 0, 'PhilHealth', 1, 0, 4),
(2059, 1002, 'Irene', 'T', 'Quinto', NULL, 'F', '1964-04-22', 'Married', 'Filipino', 'Catholic', 2, '39 Santan Road', '2007-05-08', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 3),
(2060, 1003, 'Alvin', 'U', 'Barrera', NULL, 'M', '1986-07-16', 'Single', 'Filipino', 'Catholic', 2, '81 Chico Avenue', '2013-10-20', 'College', 'Technician', 0, 1, 'PhilHealth', 1, 0, 0),
(2061, 1004, 'Daisy', 'V', 'Medina', NULL, 'F', '1995-09-27', 'Married', 'Filipino', 'Catholic', 2, '22 Sampaloc Street', '2021-04-02', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 1),
(2062, 1005, 'Teodoro', 'W', 'Ibañez', NULL, 'M', '1956-02-03', 'Married', 'Filipino', 'Catholic', 2, '64 Yakal Road', '1999-06-17', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 6),
(2063, 1006, 'Marissa', 'X', 'Salcedo', NULL, 'F', '1960-08-14', 'Widowed', 'Filipino', 'Catholic', 2, '95 Avocado Lane', '2004-09-23', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2064, 1001, 'Paulo', 'Y', 'Nicolas', NULL, 'M', '1991-11-09', 'Single', 'Filipino', 'Catholic', 2, '16 Kamias Street', '2017-12-01', 'College', 'Staff', 0, 1, 'PhilHealth', 1, 0, 0),
(2065, 1002, 'Rina', 'Z', 'Estrella', NULL, 'F', '1998-03-31', 'Married', 'Filipino', 'Catholic', 2, '53 Dahlia Road', '2022-02-18', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 0),
(2066, 1003, 'Julio', 'A', 'Calderon', NULL, 'M', '1955-05-18', 'Married', 'Filipino', 'Catholic', 2, '77 Atis Avenue', '1998-08-06', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 7),
(2067, 1004, 'Evelyn', 'B', 'Cordero', NULL, 'F', '1962-10-10', 'Married', 'Filipino', 'Catholic', 2, '28 Guyabano Street', '2006-11-11', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 3),
(2068, 1005, 'Nestor', 'C', 'Barrios', NULL, 'M', '1984-01-24', 'Married', 'Filipino', 'Catholic', 2, '90 Bayabas Road', '2010-04-07', 'High School', 'Worker', 0, 1, 'PhilHealth', 1, 0, 2),
(2069, 1006, 'Sheryl', 'D', 'Magno', NULL, 'F', '1992-06-13', 'Single', 'Filipino', 'Catholic', 2, '11 Calachuchi Street', '2018-07-19', 'College', 'Assistant', 0, 0, 'PhilHealth', 1, 1, 0),
(2070, 1001, 'Ismael', 'E', 'Dizon', NULL, 'M', '1957-03-09', 'Married', 'Filipino', 'Catholic', 3, '14 Kamagong Street', '2001-01-15', 'High School', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 5),
(2071, 1002, 'Linda', 'F', 'Mercado', NULL, 'F', '1960-06-27', 'Widowed', 'Filipino', 'Catholic', 3, '62 Mahogany Road', '2004-03-18', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2072, 1003, 'Joel', 'G', 'Samson', NULL, 'M', '1988-10-02', 'Single', 'Filipino', 'Catholic', 3, '8 Narra Lane', '2015-09-11', 'College', 'Staff', 0, 1, 'PhilHealth', 1, 0, 0),
(2073, 1004, 'Karen', 'H', 'Valencia', NULL, 'F', '1996-12-21', 'Married', 'Filipino', 'Catholic', 3, '71 Ilang-Ilang Road', '2021-07-03', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 1),
(2074, 1005, 'Federico', 'I', 'Ramos', NULL, 'M', '1954-01-30', 'Married', 'Filipino', 'Catholic', 3, '93 Molave Avenue', '1996-02-09', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 6),
(2075, 1006, 'Rosita', 'J', 'Campos', NULL, 'F', '1963-04-16', 'Widowed', 'Filipino', 'Catholic', 3, '25 Acacia Street', '2005-10-01', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2076, 1001, 'Dennis', 'K', 'Ong', NULL, 'M', '1990-07-14', 'Single', 'Filipino', 'Catholic', 3, '37 Banaba Road', '2016-11-22', 'College', 'Clerk', 0, 1, 'PhilHealth', 1, 0, 0),
(2077, 1002, 'Marjorie', 'L', 'Yu', NULL, 'F', '1999-02-05', 'Married', 'Filipino', 'Catholic', 3, '54 Sampaguita Street', '2023-01-17', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 0),
(2078, 1003, 'Rene', 'M', 'Tolosa', NULL, 'M', '1958-09-19', 'Married', 'Filipino', 'Catholic', 3, '69 Talisay Road', '2002-04-26', 'High School', 'Driver', 1, 0, 'PhilHealth', 1, 0, 3),
(2079, 1004, 'Alma', 'N', 'Rosario', NULL, 'F', '1961-11-28', 'Widowed', 'Filipino', 'Catholic', 4, '10 Yakal Street', '2004-06-13', 'Elementary', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2080, 1005, 'Rogelio', 'O', 'Macapagal', NULL, 'M', '1956-05-04', 'Married', 'Filipino', 'Catholic', 4, '82 Kamias Avenue', '1999-09-21', 'High School', 'Guard', 1, 0, 'PhilHealth', 1, 0, 5),
(2081, 1006, 'Cynthia', 'P', 'Agustin', NULL, 'F', '1994-08-18', 'Married', 'Filipino', 'Catholic', 4, '47 Avocado Road', '2019-10-10', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 1),
(2082, 1001, 'Edgar', 'Q', 'Manansala', NULL, 'M', '1985-12-09', 'Single', 'Filipino', 'Catholic', 4, '33 Santol Street', '2012-02-14', 'College', 'Staff', 0, 1, 'PhilHealth', 1, 0, 0),
(2083, 1002, 'Milagros', 'R', 'Fajardo', NULL, 'F', '1962-03-22', 'Widowed', 'Filipino', 'Catholic', 4, '61 Calamansi Lane', '2006-05-19', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2084, 1003, 'Oscar', 'S', 'Lorenzo', NULL, 'M', '1957-07-07', 'Married', 'Filipino', 'Catholic', 4, '99 Banaba Drive', '2001-08-02', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 6),
(2085, 1004, 'Lani', 'T', 'De Vera', NULL, 'F', '1997-01-13', 'Single', 'Filipino', 'Catholic', 4, '18 Chico Street', '2020-03-09', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 0),
(2086, 1005, 'Gregorio', 'U', 'Peña', NULL, 'M', '1955-10-26', 'Married', 'Filipino', 'Catholic', 5, '5 Mabolo Road', '1998-11-14', 'Elementary', 'Farmer', 1, 0, 'PhilHealth', 1, 0, 7),
(2087, 1006, 'Fe', 'V', 'Sarmiento', NULL, 'F', '1960-02-01', 'Widowed', 'Filipino', 'Catholic', 5, '42 Duhat Street', '2003-04-25', 'High School', 'Vendor', 1, 0, 'PhilHealth', 1, 0, 4),
(2088, 1001, 'Allan', 'W', 'Go', NULL, 'M', '1989-09-15', 'Single', 'Filipino', 'Catholic', 5, '76 Lanzones Road', '2015-07-18', 'College', 'Staff', 0, 1, 'PhilHealth', 1, 0, 0),
(2089, 1002, 'Joyce', 'X', 'Lim', NULL, 'F', '1995-05-28', 'Married', 'Filipino', 'Catholic', 5, '21 Mangosteen Lane', '2021-06-12', 'College', 'None', 0, 0, 'PhilHealth', 1, 1, 1);

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
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `password`, `role`) VALUES
(5004, '', 'staff4@example.com', 'password123', 'staff'),
(5005, '', 'staff5@example.com', 'password123', 'staff'),
(9001, '', 'admin101@example.com', 'password123', 'staff'),
(9002, '', 'admin102@example.com', 'password123', 'admin'),
(9003, '', 'admin103@example.com', 'password123', 'admin');

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
