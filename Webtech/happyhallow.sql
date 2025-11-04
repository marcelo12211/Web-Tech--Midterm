-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 04, 2025 at 09:58 AM
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
  `ETHNICITY` varchar(100) NOT NULL,
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
  `HOUSEHOLD_HEAD` varchar(100) NOT NULL,
  `HOUSEHOLD_MEMBERS` tinyint NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
