-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2025 at 03:57 AM
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
-- Table structure for table `barangay_health`
--

DROP TABLE IF EXISTS `barangay_health`;
CREATE TABLE IF NOT EXISTS `barangay_health` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `COMMON_DISEASES` text NOT NULL,
  `PRIMARY_NEEDS` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ctc`
--

DROP TABLE IF EXISTS `ctc`;
CREATE TABLE IF NOT EXISTS `ctc` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBERID` bigint NOT NULL,
  `HAS_CTC` tinyint(1) NOT NULL,
  `HAS_ISSUES_CTC` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demographics`
--

DROP TABLE IF EXISTS `demographics`;
CREATE TABLE IF NOT EXISTS `demographics` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `IS_DISABLED` tinyint(1) NOT NULL,
  `IS_REGISTERED_SENIOR` tinyint(1) NOT NULL,
  `HEALTH_INSURANCE` varchar(100) NOT NULL,
  `PUROK` varchar(100) DEFAULT NULL,
  `FACILITY_VISITED` varchar(100) NOT NULL,
  `VISIT_REASON` varchar(500) NOT NULL,
  `PREVIOUS_RESIDENCE` varchar(500) NOT NULL,
  `RESIDED_SINCE` date NOT NULL,
  `RESIDENT_TYPE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disabled_demographic`
--

DROP TABLE IF EXISTS `disabled_demographic`;
CREATE TABLE IF NOT EXISTS `disabled_demographic` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `DISABILITY` varchar(100) NOT NULL,
  `PWD_ID` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resident_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `purpose` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `resident_name`, `file_path`, `purpose`, `created_at`) VALUES
(1, 'Ira', 'uploads/documents/doc_691b5869e752f9.54261882.jpg', 'Certificate of Residency', '2025-11-17 17:16:25');

-- --------------------------------------------------------

--
-- Table structure for table `economic activity`
--

DROP TABLE IF EXISTS `economic activity`;
CREATE TABLE IF NOT EXISTS `economic activity` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `MONTHLY_INCOME` double NOT NULL,
  `INCOME_SOURCE` varchar(100) NOT NULL,
  `WORK_BUSINESS_STATUS` varchar(100) NOT NULL,
  `WORK_BUSINESS_ADDRESS` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `encoding`
--

DROP TABLE IF EXISTS `encoding`;
CREATE TABLE IF NOT EXISTS `encoding` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `DATE_ENCODED` date NOT NULL,
  `ENCODER_NAME` varchar(100) NOT NULL,
  `SUPERVISOR_NAME` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `female_household_members`
--

DROP TABLE IF EXISTS `female_household_members`;
CREATE TABLE IF NOT EXISTS `female_household_members` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `PREGNANCY_COUNT` tinyint NOT NULL,
  `LIVING_CHILDREN` tinyint NOT NULL,
  `USE_FP_METHOD` tinyint(1) NOT NULL,
  `FP_REFUSAL_REASON` text NOT NULL,
  `FP_METHOD` varchar(100) NOT NULL,
  `FP_SOURCE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household_deaths`
--

DROP TABLE IF EXISTS `household_deaths`;
CREATE TABLE IF NOT EXISTS `household_deaths` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `MEMBER_TYPE` smallint NOT NULL,
  `AGE` tinyint NOT NULL,
  `SEX` char(1) NOT NULL,
  `CAUSE_OF_DEATH` varchar(500) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household_info`
--

DROP TABLE IF EXISTS `household_info`;
CREATE TABLE IF NOT EXISTS `household_info` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `HOUSING_OWNERSHIP` varchar(100) NOT NULL,
  `LOT_OWNERSHIP` varchar(100) NOT NULL,
  `FUEL_LIGHTING` varchar(100) NOT NULL,
  `FUEL_COOKING` varchar(100) NOT NULL,
  `DRINKING_WATER_SOURCE` varchar(100) NOT NULL,
  `GARBAGE_DISPOSAL_MEANS` varchar(100) NOT NULL,
  `BUILDING_TYPE` varchar(100) NOT NULL,
  `SEGREGATES_GARBAGE` tinyint(1) NOT NULL,
  `TOILET_FACILITY` varchar(100) NOT NULL,
  `OUTER_WALL_MATERIAL` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

DROP TABLE IF EXISTS `household_members`;
CREATE TABLE IF NOT EXISTS `household_members` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `RELATIONSHIP_TO_HEAD` varchar(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `SEX` char(1) NOT NULL,
  `BIRTHDATE` date NOT NULL,
  `AGE` tinyint NOT NULL,
  `BIRTHPLACE` varchar(100) NOT NULL,
  `NATIONALITY` varchar(100) NOT NULL,
  `ETHNICITY` varchar(255) DEFAULT NULL,
  `RELIGION` varchar(100) NOT NULL,
  `MARITAL-STATUS` varchar(50) NOT NULL,
  `HIGHEST_ATTAINED_EDUCATION` varchar(100) NOT NULL,
  `IS_ENROLLED` tinyint(1) NOT NULL,
  `SCHOOL_LEVEL` varchar(100) NOT NULL,
  `SCHOOL_ADDRESS` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `identification`
--

DROP TABLE IF EXISTS `identification`;
CREATE TABLE IF NOT EXISTS `identification` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `PROVINCE` varchar(50) NOT NULL,
  `MUNICIPALITY` varchar(50) NOT NULL,
  `BARANGAY` varchar(50) NOT NULL,
  `ADDRESS` varchar(100) NOT NULL,
  `RESPONDENT_NAME` varchar(100) NOT NULL,
  `GENDER` varchar(20) DEFAULT NULL,
  `BIRTHDATE` date DEFAULT NULL,
  `CIVIL_STATUS` varchar(50) DEFAULT NULL,
  `CITIZENSHIP` varchar(100) DEFAULT NULL,
  `HOUSEHOLD_HEAD` varchar(100) NOT NULL,
  `HOUSEHOLD_MEMBERS` tinyint NOT NULL,
  `isPWD` tinyint(1) DEFAULT '0',
  `pwdImage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `identification`
--

INSERT INTO `identification` (`ID`, `PROVINCE`, `MUNICIPALITY`, `BARANGAY`, `ADDRESS`, `RESPONDENT_NAME`, `GENDER`, `BIRTHDATE`, `CIVIL_STATUS`, `CITIZENSHIP`, `HOUSEHOLD_HEAD`, `HOUSEHOLD_MEMBERS`, `isPWD`, `pwdImage`) VALUES
(1, 'Ifugao', 'Baguio', 'Km 4', '145A', 'Ira D Marcelo', NULL, NULL, NULL, NULL, 'Father', 4, 0, NULL),
(2, 'fsf', 'sfsf', 'sff', 'sfsf', 'dfss dfsdfsdf sdfsd fsdfsd', NULL, NULL, NULL, NULL, 'sfs', 127, 0, NULL),
(3, '131', '3123', 'qweqweq', 'weqwe', '12313 1231 123123 qwqeqwe', NULL, NULL, NULL, NULL, 'qweqwe', 127, 0, NULL),
(4, 'rw', 'erwer', 'werwer', 'werw', 'werwer werwer erwerwe rwerw', NULL, NULL, NULL, NULL, 'erwer', 5, 0, NULL),
(5, 'rw', 'erwer', 'werwer', 'werw', 'werwer werwer erwerwe rwerw', NULL, NULL, NULL, NULL, 'erwer', 5, 0, NULL),
(6, 'rw', 'erwer', 'werwer', 'werw', 'werwer werwer erwerwe rwerw', NULL, NULL, NULL, NULL, 'erwer', 5, 0, NULL),
(7, 'Ifugao', 'Baguio City', 'Km 4', 'Km 4, Baguio City, Ifugao', 'Ira D Marcleo Jr', NULL, NULL, NULL, NULL, 'Ira D Marcleo Jr', 0, 0, NULL),
(8, 'Ifugao', 'Baguio City', 'Km 4', 'Km 4, Baguio City, Ifugao', 'Ira D Marcleo Jr', NULL, NULL, NULL, NULL, 'Ira D Marcleo Jr', 0, 0, NULL),
(9, 'Ifugao', 'Baguio City', 'Km 4', 'Km 4, Baguio City, Ifugao', 'Ira D Marcleo Jr', NULL, NULL, NULL, NULL, 'Ira D Marcleo Jr', 0, 0, NULL),
(10, 'Ifugao', 'Baguio City', 'Km 4', 'Km 4, Baguio City, Ifugao', 'Ira D Marcleo Jr', 'Male', '2003-03-17', 'Married', 'British', 'Ira D Marcleo Jr', 1, 0, NULL),
(11, 'Ifugao', 'Baguio City', 'Purok 1', 'Purok 1, Baguio City, Ifugao', 'Le M On jr', 'Male', '2025-11-01', 'Single', 'Canadian', 'Le M On jr', 1, 1, 'uploads/pwd_ids/1763402216_Screenshot 2025-11-09 195446.jpg'),
(12, 'Nueva Ecija', 'San Leonardo', 'Rizal', 'Rizal, San Leonardo, Nueva Ecija', 'jerald jaucian fajardo NA', 'Male', '2000-12-12', 'Single', 'Filipino', 'jerald jaucian fajardo NA', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `infant_household_member`
--

DROP TABLE IF EXISTS `infant_household_member`;
CREATE TABLE IF NOT EXISTS `infant_household_member` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `DELIVERY_PLACE` varchar(100) NOT NULL,
  `BIRTH_ATTENDANT` varchar(100) NOT NULL,
  `LAST_VACCINE` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_info`
--

DROP TABLE IF EXISTS `interview_info`;
CREATE TABLE IF NOT EXISTS `interview_info` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `IDENTIFICATION_ID` bigint NOT NULL,
  `VISIT_DATE` date NOT NULL,
  `TIME_START` timestamp NOT NULL,
  `TIME_END` timestamp NOT NULL,
  `RESULT` varchar(100) NOT NULL,
  `NEXT_VISIT_DATE` date NOT NULL,
  `INTERVIEWER_NAME` varchar(100) NOT NULL,
  `SUPERVISOR_NAME` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
CREATE TABLE IF NOT EXISTS `migration` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBERID` bigint NOT NULL,
  `TRANSFER_DATE` date NOT NULL,
  `REASON_OF_TRANSFER` text NOT NULL,
  `PLANS_TO_RETURN` tinyint(1) NOT NULL,
  `LEAVE_RESIDENCE_REASON` text NOT NULL,
  `STAY_DURATION` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_demographics`
--

DROP TABLE IF EXISTS `senior_demographics`;
CREATE TABLE IF NOT EXISTS `senior_demographics` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBER_ID` bigint NOT NULL,
  `SENIOR_TYPE` varchar(100) NOT NULL,
  `SENIOR_ID` int NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
CREATE TABLE IF NOT EXISTS `skills` (
  `ID` bigint NOT NULL AUTO_INCREMENT,
  `MEMBERID` bigint NOT NULL,
  `INTERESTED_SKILLS` varchar(100) NOT NULL,
  `CURRENT_SKILLS` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`) VALUES
(3, '', 'pol@gmail.com', '$2y$10$t/7G.9t9y4SYCwJNF4vRCOtH9NiixrnteB97AT3DJ6MoZyc0dCIim', 'client'),
(4, '', 'doe@gmail.com', '$2y$10$rxsTL3Nfkk5hx/PB9Txc2OYXRbrG2FBMu1Of.riV.7ElwPMhPZDsO', 'client'),
(5, '', 'levii@gmail.com', '$2y$10$K8XoXrP3y7fT6N2VEUbh3OY/5F3x84o6eMB8zfvZdaXjKQaIb8psq', 'client'),
(6, '', 'ira@gmail.com', '$2y$10$t9rboloTnnSu2d1E1u1a.eN0ymMwlc/ot8GG/t/ytt9bcQPw9K7l.', 'client'),
(7, '', 'hatdog@gmail.com', '$2y$10$zuUt6foa6TbnrYhAHYAZle4/YeV4zyDoN6lqxGfIq2rSy7TGE9zxS', 'client'),
(8, '', 'add@gmail.com', '$2y$10$e.1gXLWIXa/gakdQLPLnb.nZuZd42q/1/oDYGNzcKWJd3FgEam.py', 'client'),
(10, '', 'testt@gmail.com', '$2y$10$JJO3ub5MT1vS5Dhnge4EfeCNYO1jz9Gb0SjOSfq7GhNfSTHhXSdZy', 'client'),
(16, '', 'hell@gmail.com', '$2y$10$1Nvx4SRvRgvktgz1PHWPiuzziaAv0QCamQlxi9nTi2w.7DULQTvT.', 'admin'),
(18, 'Jordan Bel', 'jordan@gmail.com', '$2y$10$5qJQlmdfLtHPLA1kdQ0truq3W3LFxKVqVjQUh2gmvF6H3hWJPuaqC', 'admin'),
(19, 'Connorrrrrrrrrrr', 'connor@gmail.com', '$2y$10$v3Fj7HfKjMSX5z.8rPETaOCioiDRiO1fF.nlNHf3ebyP77Pf.be5G', 'client'),
(21, 'Suss', 'suss@gmail.com', '$2y$10$GSMp7rNBdKojO4BA/4HPEevdGbEALnec32WdsKsOe2dYsRoD7lAee', 'client');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
