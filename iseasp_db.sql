-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 04:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================
-- Database: `iseasp_db`
-- ============================================

-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- Drop tables in correct order (if they exist)
-- ============================================
DROP TABLE IF EXISTS `tbl_system_logs`;
DROP TABLE IF EXISTS `tbl_application_log`;
DROP TABLE IF EXISTS `tbl_notifications`;
DROP TABLE IF EXISTS `tbl_renewal_applications`;
DROP TABLE IF EXISTS `tbl_scholarship_history`;
DROP TABLE IF EXISTS `tbl_scholarship_awards`;
DROP TABLE IF EXISTS `tbl_evaluation`;
DROP TABLE IF EXISTS `tbl_interview`;
DROP TABLE IF EXISTS `tbl_exam_results`;
DROP TABLE IF EXISTS `tbl_applicant_requirement_checklist`;
DROP TABLE IF EXISTS `tbl_document_tracking`;
DROP TABLE IF EXISTS `tbl_educational_background`;
DROP TABLE IF EXISTS `tbl_family_background`;
DROP TABLE IF EXISTS `tbl_alumni`;
DROP TABLE IF EXISTS `tbl_applicants`;
DROP TABLE IF EXISTS `tbl_requirement`;
DROP TABLE IF EXISTS `tbl_municipalities`;
DROP TABLE IF EXISTS `tbl_admin`;
DROP TABLE IF EXISTS `tblusers`;

-- ============================================
-- Table structure for table `tblusers`
-- ============================================

CREATE TABLE `tblusers` (
  `USERID` int(11) NOT NULL AUTO_INCREMENT,
  `FULLNAME` varchar(150) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASS` varchar(255) NOT NULL,
  `ROLE` enum('Super Admin','Admin','Evaluator','Staff') DEFAULT 'Admin',
  `PICLOCATION` varchar(255) DEFAULT NULL,
  `EMAIL` varchar(150) DEFAULT NULL,
  `DATECREATED` datetime DEFAULT current_timestamp(),
  `LAST_LOGIN` datetime DEFAULT NULL,
  `LAST_ACTIVITY` datetime DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`USERID`),
  UNIQUE KEY `USERNAME` (`USERNAME`),
  KEY `idx_username` (`USERNAME`),
  KEY `idx_role` (`ROLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers` (with plain text passwords commented)
-- Usernames and passwords:
-- admin     : admin123
-- kevin     : kevin123  
-- pika      : pika123
--

INSERT INTO `tblusers` (`USERID`, `FULLNAME`, `USERNAME`, `PASS`, `ROLE`, `PICLOCATION`, `EMAIL`, `DATECREATED`, `LAST_LOGIN`, `LAST_ACTIVITY`, `IP_ADDRESS`) VALUES
(1, 'Jack Sparrow', 'admin', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'Super Admin', 'photos/Random_person_image.png', NULL, '2026-01-26 00:26:09', NULL, NULL, NULL),
(2, 'Kevin', 'kevin', '802d3be54d783f4bc3ebcfd38dc0a1b9ffa1ef3d', 'Staff', 'user_1772985538.png', NULL, '2026-03-08 23:49:28', NULL, NULL, NULL),
(3, 'Pika Chu', 'pika', 'f9b5bbdb54e8458a349c5ad3d20543f3b588324a', 'Admin', 'user_1772985409.png', NULL, '2026-03-08 23:56:49', NULL, NULL, NULL);

-- ============================================
-- Table structure for table `tbl_admin`
-- ============================================

CREATE TABLE `tbl_admin` (
  `ADMIN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) NOT NULL,
  `STATUS` enum('Active','Inactive') DEFAULT 'Active',
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `UPDATED_AT` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`ADMIN_ID`),
  KEY `USERID` (`USERID`),
  CONSTRAINT `fk_admin_user` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`ADMIN_ID`, `USERID`, `STATUS`, `CREATED_AT`, `UPDATED_AT`) VALUES
(1, 1, 'Active', '2026-03-08 15:32:37', NULL),
(2, 2, 'Active', '2026-03-08 15:49:28', NULL),
(3, 3, 'Active', '2026-03-08 15:56:49', NULL);

-- ============================================
-- Table structure for table `tbl_alumni`
-- ============================================

CREATE TABLE `tbl_alumni` (
  `ALUMNI_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `GRADUATION_DATE` date NOT NULL,
  `FINAL_GPA` decimal(5,2) DEFAULT NULL,
  `HONORS` varchar(100) DEFAULT NULL,
  `EMPLOYMENT_STATUS` enum('Employed','Unemployed','Self-Employed','Further Studies') DEFAULT NULL,
  `COMPANY` varchar(200) DEFAULT NULL,
  `POSITION` varchar(100) DEFAULT NULL,
  `CONTACT_NUMBER` varchar(20) DEFAULT NULL,
  `EMAIL` varchar(150) DEFAULT NULL,
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ALUMNI_ID`),
  KEY `APPLICANTID` (`APPLICANTID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_requirement`
-- ============================================

CREATE TABLE `tbl_requirement` (
  `REQUIREMENT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `REQUIREMENT_NAME` varchar(100) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `REQUIRED` enum('Yes','No') DEFAULT 'Yes',
  `CATEGORY` enum('Personal','Academic','Financial','Other') DEFAULT 'Other',
  `DISPLAY_ORDER` int(11) DEFAULT 0,
  `CREATED_BY` int(11) DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`REQUIREMENT_ID`),
  KEY `idx_category` (`CATEGORY`),
  KEY `idx_required` (`REQUIRED`),
  KEY `fk_requirement_creator` (`CREATED_BY`),
  KEY `fk_requirement_updater` (`UPDATED_BY`),
  CONSTRAINT `fk_requirement_creator` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL,
  CONSTRAINT `fk_requirement_updater` FOREIGN KEY (`UPDATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_requirement`
--

INSERT INTO `tbl_requirement` (`REQUIREMENT_ID`, `REQUIREMENT_NAME`, `DESCRIPTION`, `REQUIRED`, `CATEGORY`, `DISPLAY_ORDER`, `CREATED_BY`, `CREATED_AT`, `UPDATED_BY`, `UPDATED_AT`) VALUES
(1, 'PSA Birth Certificate', 'Certified true copy from PSA', 'Yes', 'Personal', 1, NULL, '2026-03-17 22:09:05', NULL, NULL),
(2, 'Certificate of Enrollment', 'Current enrollment certificate from school', 'Yes', 'Academic', 2, NULL, '2026-03-17 22:09:05', NULL, NULL),
(3, 'Certificate of Grades', 'Copy of grades from previous semester/year', 'Yes', 'Academic', 3, NULL, '2026-03-17 22:09:05', NULL, NULL),
(4, 'Barangay Certificate', 'Certificate of residency from Barangay', 'Yes', 'Personal', 4, NULL, '2026-03-17 22:09:05', NULL, NULL),
(5, 'Certificate of Indigency', 'From Municipal Social Welfare and Development', 'Yes', 'Financial', 5, NULL, '2026-03-17 22:09:05', NULL, NULL),
(6, 'Parent\'s Income Tax Return', 'Or Certificate of Tax Exemption from BIR', 'Yes', 'Financial', 6, NULL, '2026-03-17 22:09:05', NULL, NULL),
(7, '4Ps Certificate', 'If 4Ps beneficiary', 'No', 'Financial', 7, NULL, '2026-03-17 22:09:05', NULL, NULL),
(8, 'Indigenous People Certificate', 'From NCIP if Indigenous', 'No', 'Personal', 8, NULL, '2026-03-17 22:09:05', NULL, NULL),
(9, 'Good Moral Certificate', 'From school guidance office', 'Yes', 'Academic', 9, NULL, '2026-03-17 22:09:05', NULL, NULL),
(10, 'Medical Certificate', 'From Rural Health Unit or Hospital', 'Yes', 'Personal', 10, NULL, '2026-03-17 22:09:05', NULL, NULL),
(11, 'Voter\'s Certification', 'From COMELEC (for voters)', 'No', 'Other', 11, NULL, '2026-03-17 22:09:05', NULL, NULL),
(12, '2x2 ID Picture', 'Recent white background photo', 'Yes', 'Personal', 12, NULL, '2026-03-17 22:09:05', NULL, NULL),
(13, 'Application Form', 'Completed and signed application form', 'Yes', 'Other', 13, NULL, '2026-03-17 22:09:05', NULL, NULL),
(14, 'Barangay Certification', 'Certification from the Barangay Chairman that the applicant is a bonafide resident of the barangay for at least one (1) year and has no derogatory record/s', 'Yes', 'Personal', 1, NULL, '2026-03-17 22:09:05', NULL, NULL),
(15, 'Latest Grades', 'Copy of latest grades with General Average of 2.25 or 83%', 'Yes', 'Academic', 2, NULL, '2026-03-17 22:09:05', NULL, NULL),
(16, '1.5x1.5 ID Picture', 'Two (2) copies taken within the last three (3) months with WHITE/RED background', 'Yes', 'Personal', 3, NULL, '2026-03-17 22:09:05', NULL, NULL),
(17, 'Good Moral Character', 'Certificate of Good Moral Character', 'Yes', 'Academic', 4, NULL, '2026-03-17 22:09:05', NULL, NULL),
(18, 'PSA Birth Certificate', 'PSA Birth Certificate (Photocopy)', 'Yes', 'Personal', 5, NULL, '2026-03-17 22:09:05', NULL, NULL),
(19, 'Enrollment Form', 'Enrollment Form/Certificate of Enrollment', 'Yes', 'Academic', 6, NULL, '2026-03-17 22:09:05', NULL, NULL),
(20, 'Income Tax Return', 'Photocopy of Latest Income Tax Return of Parents - If parent is a Permanent Government Employee', 'No', 'Financial', 7, NULL, '2026-03-17 22:09:05', NULL, NULL),
(21, 'BIR Exemption', 'Certification of Exemption from BIR stating the annual gross income', 'No', 'Financial', 8, NULL, '2026-03-17 22:09:05', NULL, NULL),
(22, 'Pension Voucher', 'Retirement Form/Pension Voucher - If Parent/Legal Guardian is a retired employee', 'No', 'Financial', 9, NULL, '2026-03-17 22:09:05', NULL, NULL),
(23, 'Certificate of Indigency', 'Certificate of Indigency from barangay of residency (If parents are not filing income tax)', 'No', 'Financial', 10, NULL, '2026-03-17 22:09:05', NULL, NULL);

-- ============================================
-- Table structure for table `tbl_municipalities`
-- ============================================

CREATE TABLE `tbl_municipalities` (
  `MUNICIPALITY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MUNICIPALITY_NAME` varchar(100) NOT NULL,
  `DISTRICT` varchar(50) NOT NULL,
  `IS_ACTIVE` enum('Yes','No') DEFAULT 'Yes',
  `CREATED_BY` int(11) DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`MUNICIPALITY_ID`),
  UNIQUE KEY `unique_municipality` (`MUNICIPALITY_NAME`),
  KEY `idx_district` (`DISTRICT`),
  KEY `idx_active` (`IS_ACTIVE`),
  KEY `fk_municipality_creator` (`CREATED_BY`),
  KEY `fk_municipality_updater` (`UPDATED_BY`),
  CONSTRAINT `fk_municipality_creator` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL,
  CONSTRAINT `fk_municipality_updater` FOREIGN KEY (`UPDATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_municipalities`
--

INSERT INTO `tbl_municipalities` (`MUNICIPALITY_ID`, `MUNICIPALITY_NAME`, `DISTRICT`, `IS_ACTIVE`, `CREATED_BY`, `CREATED_AT`, `UPDATED_BY`, `UPDATED_AT`) VALUES
(1, 'Vigan City', '1st District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(2, 'Santa Catalina', '1st District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(3, 'Bantay', '1st District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(4, 'Caoayan', '1st District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(5, 'Santa', '1st District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(6, 'Narvacan', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(7, 'Santa Maria', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(8, 'San Esteban', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(9, 'Santiago', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(10, 'Candon City', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(11, 'Tagudin', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(12, 'Suyo', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(13, 'Alilem', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(14, 'Sigay', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(15, 'Gregorio Del Pilar', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(16, 'Cervantes', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(17, 'Quirino', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(18, 'Santa Cruz', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(19, 'Santa Lucia', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(20, 'Salcedo', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(21, 'San Vicente', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL),
(22, 'Galimuyod', '2nd District', 'Yes', NULL, '2026-03-17 22:09:05', NULL, NULL);

-- ============================================
-- Table structure for table `tbl_applicants`
-- ============================================

CREATE TABLE `tbl_applicants` (
  `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT,
  `FIRSTNAME` varchar(100) NOT NULL,
  `MIDDLENAME` varchar(100) DEFAULT NULL,
  `LASTNAME` varchar(100) NOT NULL,
  `SUFFIX` varchar(10) DEFAULT NULL,
  `LRN` varchar(12) DEFAULT NULL,
  `BIRTHDATE` date DEFAULT NULL,
  `BIRTHPLACE` varchar(200) DEFAULT NULL,
  `GENDER` enum('Male','Female','Other') DEFAULT NULL,
  `CIVIL_STATUS` enum('Single','Married','Widowed','Separated') DEFAULT 'Single',
  `RELIGION` varchar(100) DEFAULT NULL,
  `NATIONALITY` varchar(50) DEFAULT 'Filipino',
  `PERMANENT_ADDRESS` text DEFAULT NULL,
  `CURRENT_ADDRESS` text DEFAULT NULL,
  `DISTRICT` varchar(100) DEFAULT NULL,
  `MUNICIPALITY` varchar(100) DEFAULT NULL,
  `BARANGAY` varchar(100) DEFAULT NULL,
  `COURSE` varchar(150) NOT NULL,
  `SCHOOL` varchar(150) NOT NULL,
  `YEARLEVEL` varchar(50) NOT NULL,
  `GPA` decimal(5,2) DEFAULT NULL,
  `CONTACT` varchar(20) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `FACEBOOK_URL` varchar(255) DEFAULT NULL,
  `EMERGENCY_CONTACT_NAME` varchar(150) DEFAULT NULL,
  `EMERGENCY_CONTACT_NUMBER` varchar(20) DEFAULT NULL,
  `EMERGENCY_CONTACT_RELATION` varchar(50) DEFAULT NULL,
  `APPLICATION_TYPE` enum('New Applicant','Renewal') DEFAULT 'New Applicant',
  `SCHOOL_YEAR` varchar(20) DEFAULT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') DEFAULT '1st Semester',
  `IS_4PS_BENEFICIARY` enum('Yes','No') DEFAULT 'No',
  `IS_INDIGENOUS` enum('Yes','No') DEFAULT 'No',
  `FAMILY_ANNUAL_INCOME` decimal(10,2) DEFAULT NULL,
  `PARENT_OCCUPATION` varchar(100) DEFAULT NULL,
  `STATUS` enum('Pending','Approved','Rejected','For Interview','Qualified','Scholar','Graduated') DEFAULT 'Pending',
  `EXAM_STATUS` enum('Pending','Passed','Failed') DEFAULT 'Pending',
  `REQUIREMENT_STATUS` enum('Complete','Incomplete','Pending') DEFAULT 'Pending',
  `REQUIREMENT_DATE` datetime DEFAULT NULL,
  `EXAM_SLIP_NUMBER` varchar(50) DEFAULT NULL,
  `EXAM_SLIP_GENERATED` datetime DEFAULT NULL,
  `EXAM_DATE` date DEFAULT NULL,
  `EXAM_TIME` time DEFAULT NULL,
  `EXAM_VENUE` varchar(255) DEFAULT NULL,
  `EXAM_NOTES` text DEFAULT NULL,
  `CREATED_BY` int(11) NOT NULL,
  `DATECREATED` datetime DEFAULT current_timestamp(),
  `UPDATED_BY` int(11) DEFAULT NULL,
  `LAST_UPDATED` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`APPLICANTID`),
  KEY `CREATED_BY` (`CREATED_BY`),
  KEY `idx_lrn` (`LRN`),
  KEY `idx_status` (`STATUS`),
  KEY `idx_exam_status` (`EXAM_STATUS`),
  KEY `idx_requirement_status` (`REQUIREMENT_STATUS`),
  KEY `idx_school_year` (`SCHOOL_YEAR`),
  KEY `idx_municipality` (`MUNICIPALITY`),
  KEY `idx_district` (`DISTRICT`),
  KEY `idx_datecreated` (`DATECREATED`),
  KEY `idx_updated_by` (`UPDATED_BY`),
  CONSTRAINT `fk_applicant_creator` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`),
  CONSTRAINT `fk_applicant_updater` FOREIGN KEY (`UPDATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_applicants`
--

INSERT INTO `tbl_applicants` (
  `APPLICANTID`, `FIRSTNAME`, `MIDDLENAME`, `LASTNAME`, `SUFFIX`, `LRN`, `BIRTHDATE`, `BIRTHPLACE`, `GENDER`, 
  `CIVIL_STATUS`, `RELIGION`, `NATIONALITY`, `PERMANENT_ADDRESS`, `CURRENT_ADDRESS`, `DISTRICT`, `MUNICIPALITY`, 
  `BARANGAY`, `COURSE`, `SCHOOL`, `YEARLEVEL`, `GPA`, `CONTACT`, `EMAIL`, `FACEBOOK_URL`, 
  `EMERGENCY_CONTACT_NAME`, `EMERGENCY_CONTACT_NUMBER`, `EMERGENCY_CONTACT_RELATION`, `APPLICATION_TYPE`, 
  `SCHOOL_YEAR`, `SEMESTER`, `IS_4PS_BENEFICIARY`, `IS_INDIGENOUS`, `FAMILY_ANNUAL_INCOME`, `PARENT_OCCUPATION`, 
  `STATUS`, `EXAM_STATUS`, `REQUIREMENT_STATUS`, `REQUIREMENT_DATE`, `EXAM_SLIP_NUMBER`, `EXAM_SLIP_GENERATED`, 
  `EXAM_DATE`, `EXAM_TIME`, `EXAM_VENUE`, `EXAM_NOTES`, `CREATED_BY`, `DATECREATED`, `UPDATED_BY`, `LAST_UPDATED`
) VALUES
(4, 'Charmaine', 'Charmaine', 'Ramos', '', NULL, '2026-03-01', 'Vigan City', 'Female', 
 'Single', 'Roman Catholic', 'Filipino', NULL, NULL, '1st District', 'Bantay', 
 NULL, 'Bachelor of Science in Information Technology', 'Ilocos Sur Community College', '2nd Year', 89.00, '03214569875', 'email@email.com', 'na', 
 NULL, NULL, NULL, 'New Applicant', 
 '2025-2026', '1st Semester', 'No', 'No', NULL, NULL, 
 'Scholar', 'Passed', 'Pending', NULL, 'EXAM-2026-00004', '2026-03-08 14:29:18', 
 '2026-03-15', '08:00:00', 'Provincial Capitol, Vigan City', 'Please arrive 30 minutes before the scheduled time.', 1, '2026-03-08 14:28:58', NULL, NULL),

(6, 'Maria', 'Maria', 'Jab', '', NULL, '2026-03-01', 'Vigan City', 'Female', 
 'Single', 'Roman Catholic', 'Filipino', NULL, NULL, '2nd District', 'Narvacan', 
 NULL, 'Bachelor of Science in Information Technology', 'ISPSC', '4th Year', NULL, '03214569875', 'email@email.com', 'na', 
 NULL, NULL, NULL, 'New Applicant', 
 '2025-2026', '1st Semester', 'No', 'No', NULL, NULL, 
 'Pending', 'Pending', 'Pending', NULL, 'EXAM-2026-00006', '2026-03-08 20:53:18', 
 '2026-03-15', '08:00:00', 'Provincial Capitol, Vigan City', 'Please arrive 30 minutes before the scheduled time.', 1, '2026-03-08 20:30:37', NULL, NULL);

-- ============================================
-- Table structure for table `tbl_exam_results`
-- ============================================

CREATE TABLE `tbl_exam_results` (
  `EXAM_RESULT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `EXAMINER_ID` int(11) NOT NULL,
  `EXAM_DATE` datetime DEFAULT current_timestamp(),
  `TOTAL_SCORE` int(11) DEFAULT NULL,
  `PASSING_SCORE` int(11) DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`EXAM_RESULT_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `EXAMINER_ID` (`EXAMINER_ID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_examiner` (`EXAMINER_ID`),
  CONSTRAINT `fk_exam_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_exam_examiner` FOREIGN KEY (`EXAMINER_ID`) REFERENCES `tblusers` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_exam_results`
--

INSERT INTO `tbl_exam_results` (`EXAM_RESULT_ID`, `APPLICANTID`, `EXAMINER_ID`, `EXAM_DATE`, `TOTAL_SCORE`, `PASSING_SCORE`, `REMARKS`, `CREATED_AT`, `UPDATED_AT`) VALUES
(3, 4, 1, '2026-03-08 08:55:00', 80, 75, '', '2026-03-17 22:09:05', NULL);

-- ============================================
-- Table structure for table `tbl_interview`
-- ============================================

CREATE TABLE `tbl_interview` (
  `INTERVIEW_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `INTERVIEWER_ID` int(11) DEFAULT NULL,
  `INTERVIEW_DATE` datetime NOT NULL,
  `INTERVIEW_MODE` enum('Face-to-face','Online','Phone') DEFAULT 'Face-to-face',
  `SCORE` decimal(5,2) DEFAULT NULL,
  `COMMENTS` text DEFAULT NULL,
  `RECOMMENDATION` enum('Pass','Fail','For Review') DEFAULT 'For Review',
  `CREATED_BY` int(11) DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`INTERVIEW_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `INTERVIEWER_ID` (`INTERVIEWER_ID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_interviewer` (`INTERVIEWER_ID`),
  KEY `idx_created_by` (`CREATED_BY`),
  CONSTRAINT `fk_interview_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_interview_creator` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL,
  CONSTRAINT `fk_interview_interviewer` FOREIGN KEY (`INTERVIEWER_ID`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_interview`
--

INSERT INTO `tbl_interview` (`INTERVIEW_ID`, `APPLICANTID`, `INTERVIEWER_ID`, `INTERVIEW_DATE`, `INTERVIEW_MODE`, `SCORE`, `COMMENTS`, `RECOMMENDATION`, `CREATED_BY`, `CREATED_AT`, `UPDATED_BY`, `UPDATED_AT`) VALUES
(7, 4, 1, '2026-03-11 15:55:00', 'Face-to-face', 100.00, 'Awaiting interview schedule', 'Pass', NULL, '2026-03-17 22:09:05', NULL, NULL);

-- ============================================
-- Table structure for table `tbl_evaluation`
-- ============================================

CREATE TABLE `tbl_evaluation` (
  `EVALUATION_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `EVALUATED_BY` int(11) NOT NULL,
  `FINAL_STATUS` enum('For Verification','Pending Requirement','For Interview','Qualified','Not Qualified') DEFAULT NULL,
  `FEEDBACK` text DEFAULT NULL,
  `EVALUATION_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`EVALUATION_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `EVALUATED_BY` (`EVALUATED_BY`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_evaluator` (`EVALUATED_BY`),
  CONSTRAINT `fk_eval_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_eval_evaluator` FOREIGN KEY (`EVALUATED_BY`) REFERENCES `tblusers` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_scholarship_awards`
-- ============================================

CREATE TABLE `tbl_scholarship_awards` (
  `AWARD_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') DEFAULT '1st Semester',
  `AWARD_DATE` date DEFAULT curdate(),
  `AWARDED_BY` int(11) NOT NULL,
  `AMOUNT` decimal(10,2) DEFAULT NULL,
  `STATUS` enum('Active','Inactive','Graduated','Terminated') DEFAULT 'Active',
  `REMARKS` text DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`AWARD_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `AWARDED_BY` (`AWARDED_BY`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_awarded_by` (`AWARDED_BY`),
  KEY `idx_status` (`STATUS`),
  KEY `idx_school_year` (`SCHOOL_YEAR`),
  CONSTRAINT `fk_award_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_award_awarder` FOREIGN KEY (`AWARDED_BY`) REFERENCES `tblusers` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_scholarship_awards`
--

INSERT INTO `tbl_scholarship_awards` (`AWARD_ID`, `APPLICANTID`, `SCHOOL_YEAR`, `SEMESTER`, `AWARD_DATE`, `AWARDED_BY`, `AMOUNT`, `STATUS`, `REMARKS`, `CREATED_AT`, `UPDATED_BY`, `UPDATED_AT`) VALUES
(1, 4, '2026-2027', '2nd Semester', '2026-03-08', 1, 10000.00, 'Active', 'Qualified scholar for the school year', '2026-03-17 22:09:05', NULL, NULL);

-- ============================================
-- Table structure for table `tbl_scholarship_history`
-- ============================================

CREATE TABLE `tbl_scholarship_history` (
  `HISTORY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `STATUS` enum('Applied','Exam Taken','Interviewed','Awarded','Renewed','Graduated') NOT NULL,
  `GPA` decimal(5,2) DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`HISTORY_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `UPDATED_BY` (`UPDATED_BY`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_updated_by` (`UPDATED_BY`),
  CONSTRAINT `fk_history_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_updater` FOREIGN KEY (`UPDATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_scholarship_history`
--

INSERT INTO `tbl_scholarship_history` (`HISTORY_ID`, `APPLICANTID`, `SCHOOL_YEAR`, `SEMESTER`, `STATUS`, `GPA`, `REMARKS`, `UPDATED_BY`, `UPDATED_AT`) VALUES
(1, 4, '2025-2026', '1st Semester', 'Awarded', 0.00, 'Converted to scholar', 1, '2026-03-08 09:58:38'),
(2, 4, '2026-2027', '2nd Semester', 'Renewed', 89.00, 'Renewal Approved', 1, '2026-03-08 10:26:16');

-- ============================================
-- Table structure for table `tbl_renewal_applications`
-- ============================================

CREATE TABLE `tbl_renewal_applications` (
  `RENEWAL_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') DEFAULT '1st Semester',
  `PREVIOUS_GPA` decimal(5,2) DEFAULT NULL,
  `UNITS_COMPLETED` int(11) DEFAULT NULL,
  `STATUS` enum('Pending','Approved','Denied') DEFAULT 'Pending',
  `REVIEWED_BY` int(11) DEFAULT NULL,
  `REVIEW_DATE` datetime DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`RENEWAL_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `REVIEWED_BY` (`REVIEWED_BY`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_reviewer` (`REVIEWED_BY`),
  CONSTRAINT `fk_renewal_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_renewal_reviewer` FOREIGN KEY (`REVIEWED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_renewal_applications`
--

INSERT INTO `tbl_renewal_applications` (`RENEWAL_ID`, `APPLICANTID`, `SCHOOL_YEAR`, `SEMESTER`, `PREVIOUS_GPA`, `UNITS_COMPLETED`, `STATUS`, `REVIEWED_BY`, `REVIEW_DATE`, `REMARKS`, `CREATED_AT`, `UPDATED_AT`) VALUES
(1, 4, '2026-2027', '2nd Semester', 89.00, 24, 'Approved', 1, '2026-03-08 18:26:16', 'GPA meets requirement. Renewal approved.', '2026-03-17 22:09:05', NULL);

-- ============================================
-- Table structure for table `tbl_applicant_requirement_checklist`
-- ============================================

CREATE TABLE `tbl_applicant_requirement_checklist` (
  `CHECKLIST_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `REQUIREMENT_ID` int(11) NOT NULL,
  `IS_SUBMITTED` tinyint(1) DEFAULT 0,
  `IS_VERIFIED` tinyint(1) DEFAULT 0,
  `VERIFIED_BY` int(11) DEFAULT NULL,
  `VERIFIED_DATE` datetime DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`CHECKLIST_ID`),
  UNIQUE KEY `unique_applicant_requirement` (`APPLICANTID`,`REQUIREMENT_ID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_requirement` (`REQUIREMENT_ID`),
  KEY `idx_verified_by` (`VERIFIED_BY`),
  KEY `idx_status` (`IS_SUBMITTED`,`IS_VERIFIED`),
  CONSTRAINT `fk_checklist_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `fk_checklist_requirement` FOREIGN KEY (`REQUIREMENT_ID`) REFERENCES `tbl_requirement` (`REQUIREMENT_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_checklist_verifier` FOREIGN KEY (`VERIFIED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_application_log` (ENHANCED)
-- ============================================

CREATE TABLE `tbl_application_log` (
  `LOG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) DEFAULT NULL,
  `USERID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `USER_ROLE` varchar(50) NOT NULL,
  `ACTION` varchar(100) NOT NULL,
  `ACTION_TYPE` enum('CREATE','UPDATE','DELETE','VIEW','LOGIN','LOGOUT','EXAM','INTERVIEW','EVALUATION','REQUIREMENT','SCHOLAR','OTHER') DEFAULT 'OTHER',
  `DETAILS` text DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  `USER_AGENT` text DEFAULT NULL,
  `LOG_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`LOG_ID`),
  KEY `idx_applicant` (`APPLICANTID`),
  KEY `idx_user` (`USERID`),
  KEY `idx_action_type` (`ACTION_TYPE`),
  KEY `idx_log_date` (`LOG_DATE`),
  KEY `idx_user_role` (`USER_ROLE`),
  CONSTRAINT `fk_log_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE SET NULL,
  CONSTRAINT `fk_log_user` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_system_logs` (NEW)
-- ============================================

CREATE TABLE `tbl_system_logs` (
  `LOG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `USER_ROLE` varchar(50) NOT NULL,
  `ACTION` varchar(100) NOT NULL,
  `ACTION_TYPE` enum('USER','SETTINGS','BACKUP','LOGIN','LOGOUT','OTHER') DEFAULT 'OTHER',
  `DETAILS` text DEFAULT NULL,
  `IP_ADDRESS` varchar(45) DEFAULT NULL,
  `USER_AGENT` text DEFAULT NULL,
  `LOG_DATE` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`LOG_ID`),
  KEY `idx_user` (`USERID`),
  KEY `idx_action_type` (`ACTION_TYPE`),
  KEY `idx_log_date` (`LOG_DATE`),
  CONSTRAINT `fk_system_log_user` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_document_tracking`
-- ============================================

CREATE TABLE `tbl_document_tracking` (
  `TRACKING_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `DOCUMENT_TYPE` varchar(100) NOT NULL,
  `DATE_RECEIVED` date DEFAULT NULL,
  `RECEIVED_BY` int(11) DEFAULT NULL,
  `STATUS` enum('Received','Pending','Returned','Approved') DEFAULT 'Pending',
  `REMARKS` text DEFAULT NULL,
  PRIMARY KEY (`TRACKING_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  KEY `RECEIVED_BY` (`RECEIVED_BY`),
  CONSTRAINT `tbl_document_tracking_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  CONSTRAINT `tbl_document_tracking_ibfk_2` FOREIGN KEY (`RECEIVED_BY`) REFERENCES `tblusers` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_educational_background`
-- ============================================

CREATE TABLE `tbl_educational_background` (
  `EDUC_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `ELEMENTARY_SCHOOL` varchar(150) DEFAULT NULL,
  `ELEMENTARY_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `JUNIOR_HIGH_SCHOOL` varchar(150) DEFAULT NULL,
  `JUNIOR_HIGH_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `SENIOR_HIGH_SCHOOL` varchar(150) DEFAULT NULL,
  `SENIOR_HIGH_TRACK` varchar(100) DEFAULT NULL,
  `SENIOR_HIGH_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `GPA` decimal(5,2) DEFAULT NULL,
  `AWARDS` text DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`EDUC_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  CONSTRAINT `fk_educ_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_family_background`
-- ============================================

CREATE TABLE `tbl_family_background` (
  `FAMILY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `APPLICANTID` int(11) NOT NULL,
  `FATHER_NAME` varchar(150) DEFAULT NULL,
  `FATHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `FATHER_INCOME` decimal(10,2) DEFAULT NULL,
  `MOTHER_NAME` varchar(150) DEFAULT NULL,
  `MOTHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `MOTHER_INCOME` decimal(10,2) DEFAULT NULL,
  `GUARDIAN_NAME` varchar(150) DEFAULT NULL,
  `NUMBER_OF_SIBLINGS` int(11) DEFAULT 0,
  `HOUSEHOLD_INCOME` decimal(10,2) DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `UPDATED_AT` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`FAMILY_ID`),
  KEY `APPLICANTID` (`APPLICANTID`),
  CONSTRAINT `fk_family_applicant` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- Table structure for table `tbl_notifications`
-- ============================================

CREATE TABLE `tbl_notifications` (
  `NOTIF_ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) NOT NULL,
  `TITLE` varchar(200) NOT NULL,
  `MESSAGE` text NOT NULL,
  `TYPE` enum('Application','Exam','Interview','Award','Renewal','System') DEFAULT 'System',
  `IS_READ` tinyint(1) DEFAULT 0,
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `CREATED_BY` int(11) DEFAULT NULL,
  PRIMARY KEY (`NOTIF_ID`),
  KEY `USERID` (`USERID`),
  KEY `idx_user` (`USERID`),
  KEY `idx_read` (`IS_READ`),
  KEY `fk_notif_creator` (`CREATED_BY`),
  CONSTRAINT `fk_notif_creator` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`) ON DELETE SET NULL,
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- TRIGGERS for automation and logging
-- ============================================

DELIMITER $$

-- Trigger to auto-create checklist entries when new applicant is added
DROP TRIGGER IF EXISTS `trg_after_applicant_insert`$$
CREATE TRIGGER `trg_after_applicant_insert`
AFTER INSERT ON `tbl_applicants`
FOR EACH ROW
BEGIN
    INSERT INTO `tbl_applicant_requirement_checklist` 
        (`APPLICANTID`, `REQUIREMENT_ID`, `IS_SUBMITTED`, `IS_VERIFIED`)
    SELECT NEW.APPLICANTID, `REQUIREMENT_ID`, 0, 0
    FROM `tbl_requirement`;
    
    -- Log the creation
    INSERT INTO `tbl_application_log` 
        (`APPLICANTID`, `USERID`, `USERNAME`, `USER_ROLE`, `ACTION`, `ACTION_TYPE`, `DETAILS`)
    SELECT 
        NEW.APPLICANTID,
        NEW.CREATED_BY,
        u.USERNAME,
        u.ROLE,
        'Applicant Created',
        'CREATE',
        CONCAT('New applicant: ', NEW.FIRSTNAME, ' ', NEW.LASTNAME)
    FROM tblusers u
    WHERE u.USERID = NEW.CREATED_BY;
END$$

-- Trigger to auto-create interview when applicant passes exam
DROP TRIGGER IF EXISTS `trg_after_exam_passed`$$
CREATE TRIGGER `trg_after_exam_passed`
AFTER UPDATE ON `tbl_applicants`
FOR EACH ROW
BEGIN
    -- Check if exam status changed to 'Passed'
    IF NEW.EXAM_STATUS = 'Passed' AND (OLD.EXAM_STATUS != 'Passed' OR OLD.EXAM_STATUS IS NULL) THEN
        -- Check if interview doesn't already exist
        IF NOT EXISTS (SELECT 1 FROM `tbl_interview` WHERE `APPLICANTID` = NEW.APPLICANTID) THEN
            -- Create interview record with default values
            INSERT INTO `tbl_interview` 
                (`APPLICANTID`, `INTERVIEW_DATE`, `INTERVIEW_MODE`, `RECOMMENDATION`, `COMMENTS`, `CREATED_BY`)
            VALUES 
                (NEW.APPLICANTID, DATE_ADD(NOW(), INTERVAL 3 DAY), 'Face-to-face', 'For Review', 'Awaiting interview schedule', NEW.UPDATED_BY);
        END IF;
    END IF;
END$$

-- Trigger to log status changes
DROP TRIGGER IF EXISTS `trg_after_applicant_update`$$
CREATE TRIGGER `trg_after_applicant_update`
AFTER UPDATE ON `tbl_applicants`
FOR EACH ROW
BEGIN
    DECLARE v_changes TEXT DEFAULT '';
    DECLARE v_logged_by INT;
    
    SET v_logged_by = COALESCE(NEW.UPDATED_BY, NEW.CREATED_BY, 1);
    
    IF OLD.STATUS != NEW.STATUS THEN
        SET v_changes = CONCAT(v_changes, 'Status: ', OLD.STATUS, ' → ', NEW.STATUS, '; ');
    END IF;
    
    IF OLD.EXAM_STATUS != NEW.EXAM_STATUS THEN
        SET v_changes = CONCAT(v_changes, 'Exam: ', OLD.EXAM_STATUS, ' → ', NEW.EXAM_STATUS, '; ');
    END IF;
    
    IF OLD.REQUIREMENT_STATUS != NEW.REQUIREMENT_STATUS THEN
        SET v_changes = CONCAT(v_changes, 'Requirements: ', OLD.REQUIREMENT_STATUS, ' → ', NEW.REQUIREMENT_STATUS, '; ');
    END IF;
    
    IF v_changes != '' THEN
        INSERT INTO `tbl_application_log` 
            (`APPLICANTID`, `USERID`, `USERNAME`, `USER_ROLE`, `ACTION`, `ACTION_TYPE`, `DETAILS`)
        SELECT 
            NEW.APPLICANTID,
            v_logged_by,
            u.USERNAME,
            u.ROLE,
            'Applicant Updated',
            'UPDATE',
            v_changes
        FROM tblusers u
        WHERE u.USERID = v_logged_by;
    END IF;
END$$

-- Trigger to log exam result entry
DROP TRIGGER IF EXISTS `trg_after_exam_insert`$$
CREATE TRIGGER `trg_after_exam_insert`
AFTER INSERT ON `tbl_exam_results`
FOR EACH ROW
BEGIN
    DECLARE v_result VARCHAR(10);
    SET v_result = IF(NEW.TOTAL_SCORE >= NEW.PASSING_SCORE, 'PASSED', 'FAILED');
    
    INSERT INTO `tbl_application_log` 
        (`APPLICANTID`, `USERID`, `USERNAME`, `USER_ROLE`, `ACTION`, `ACTION_TYPE`, `DETAILS`)
    SELECT 
        NEW.APPLICANTID,
        NEW.EXAMINER_ID,
        u.USERNAME,
        u.ROLE,
        CONCAT('Exam Result: ', v_result),
        'EXAM',
        CONCAT('Score: ', NEW.TOTAL_SCORE, '%, Passing: ', NEW.PASSING_SCORE, '%')
    FROM tblusers u
    WHERE u.USERID = NEW.EXAMINER_ID;
END$$

-- Trigger to log interview updates
DROP TRIGGER IF EXISTS `trg_after_interview_update`$$
CREATE TRIGGER `trg_after_interview_update`
AFTER UPDATE ON `tbl_interview`
FOR EACH ROW
BEGIN
    DECLARE v_logged_by INT;
    SET v_logged_by = COALESCE(NEW.UPDATED_BY, NEW.CREATED_BY, NEW.INTERVIEWER_ID, 1);
    
    IF OLD.SCORE != NEW.SCORE OR OLD.RECOMMENDATION != NEW.RECOMMENDATION THEN
        INSERT INTO `tbl_application_log` 
            (`APPLICANTID`, `USERID`, `USERNAME`, `USER_ROLE`, `ACTION`, `ACTION_TYPE`, `DETAILS`)
        SELECT 
            NEW.APPLICANTID,
            v_logged_by,
            u.USERNAME,
            u.ROLE,
            CONCAT('Interview Result: ', NEW.RECOMMENDATION),
            'INTERVIEW',
            CONCAT('Score: ', NEW.SCORE, '%, Recommendation: ', NEW.RECOMMENDATION)
        FROM tblusers u
        WHERE u.USERID = v_logged_by;
    END IF;
END$$

-- Trigger to log scholarship awards
DROP TRIGGER IF EXISTS `trg_after_award_insert`$$
CREATE TRIGGER `trg_after_award_insert`
AFTER INSERT ON `tbl_scholarship_awards`
FOR EACH ROW
BEGIN
    INSERT INTO `tbl_application_log` 
        (`APPLICANTID`, `USERID`, `USERNAME`, `USER_ROLE`, `ACTION`, `ACTION_TYPE`, `DETAILS`)
    SELECT 
        NEW.APPLICANTID,
        NEW.AWARDED_BY,
        u.USERNAME,
        u.ROLE,
        'Converted to Scholar',
        'SCHOLAR',
        CONCAT('Amount: ₱', NEW.AMOUNT, ', School Year: ', NEW.SCHOOL_YEAR)
    FROM tblusers u
    WHERE u.USERID = NEW.AWARDED_BY;
END$$

DELIMITER ;

-- ============================================
-- CREATE VIEWS for reporting
-- ============================================

-- View for applicant summary
CREATE OR REPLACE VIEW `view_applicant_summary` AS
SELECT 
    a.APPLICANTID,
    CONCAT(a.LASTNAME, ', ', a.FIRSTNAME, ' ', IFNULL(a.MIDDLENAME, ''), ' ', IFNULL(a.SUFFIX, '')) AS FULLNAME,
    a.LRN,
    a.MUNICIPALITY,
    a.DISTRICT,
    a.SCHOOL,
    a.COURSE,
    a.YEARLEVEL,
    a.APPLICATION_TYPE,
    a.STATUS,
    a.REQUIREMENT_STATUS,
    a.EXAM_STATUS,
    (SELECT COUNT(*) FROM tbl_applicant_requirement_checklist WHERE APPLICANTID = a.APPLICANTID AND IS_VERIFIED = 1) AS VERIFIED_REQ,
    (SELECT COUNT(*) FROM tbl_requirement WHERE REQUIRED = 'Yes') AS TOTAL_REQ
FROM tbl_applicants a;

-- View for dashboard statistics
CREATE OR REPLACE VIEW `view_dashboard_stats` AS
SELECT 
    (SELECT COUNT(*) FROM tbl_applicants) AS TOTAL_APPLICANTS,
    (SELECT COUNT(*) FROM tbl_applicants WHERE STATUS = 'Pending') AS PENDING,
    (SELECT COUNT(*) FROM tbl_applicants WHERE STATUS = 'For Interview') AS FOR_INTERVIEW,
    (SELECT COUNT(*) FROM tbl_applicants WHERE STATUS = 'Qualified') AS QUALIFIED,
    (SELECT COUNT(*) FROM tbl_applicants WHERE STATUS = 'Scholar') AS SCHOLARS,
    (SELECT COUNT(*) FROM tbl_applicants WHERE REQUIREMENT_STATUS = 'Complete') AS COMPLETE_REQUIREMENTS,
    (SELECT COUNT(*) FROM tbl_applicants WHERE REQUIREMENT_STATUS = 'Incomplete') AS INCOMPLETE_REQUIREMENTS,
    (SELECT COUNT(*) FROM tbl_exam_results) AS EXAMS_TAKEN,
    (SELECT COUNT(*) FROM tbl_interview) AS INTERVIEWS_SCHEDULED,
    (SELECT COUNT(*) FROM tbl_scholarship_awards WHERE STATUS = 'Active') AS ACTIVE_AWARDS,
    (SELECT SUM(AMOUNT) FROM tbl_scholarship_awards WHERE STATUS = 'Active') AS TOTAL_AWARD_AMOUNT;

-- View for staff activity
CREATE OR REPLACE VIEW `view_staff_activity` AS
SELECT 
    l.USERID,
    l.USERNAME,
    l.USER_ROLE,
    COUNT(*) AS TOTAL_ACTIONS,
    SUM(CASE WHEN l.ACTION_TYPE = 'CREATE' THEN 1 ELSE 0 END) AS CREATES,
    SUM(CASE WHEN l.ACTION_TYPE = 'UPDATE' THEN 1 ELSE 0 END) AS UPDATES,
    SUM(CASE WHEN l.ACTION_TYPE = 'EXAM' THEN 1 ELSE 0 END) AS EXAMS,
    SUM(CASE WHEN l.ACTION_TYPE = 'INTERVIEW' THEN 1 ELSE 0 END) AS INTERVIEWS,
    SUM(CASE WHEN l.ACTION_TYPE = 'EVALUATION' THEN 1 ELSE 0 END) AS EVALUATIONS,
    SUM(CASE WHEN l.ACTION_TYPE = 'SCHOLAR' THEN 1 ELSE 0 END) AS SCHOLARS,
    MAX(l.LOG_DATE) AS LAST_ACTIVITY
FROM tbl_application_log l
GROUP BY l.USERID, l.USERNAME, l.USER_ROLE;

-- ============================================
-- Re-enable foreign key checks
-- ============================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- Success message
-- ============================================
SELECT 'Database update completed successfully with all data preserved!' AS MESSAGE;