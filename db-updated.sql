-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 04:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iseasp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `USERID` int(11) NOT NULL,
  `FULLNAME` varchar(150) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASS` varchar(255) NOT NULL,
  `ROLE` enum('Super Admin','Admin','Evaluator','Staff') DEFAULT 'Admin',
  `PICLOCATION` varchar(255) DEFAULT NULL,
  `DATECREATED` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `ADMIN_ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `STATUS` enum('Active','Inactive') DEFAULT 'Active',
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_municipalities`
--

CREATE TABLE `tbl_municipalities` (
  `MUNICIPALITY_ID` int(11) NOT NULL,
  `MUNICIPALITY_NAME` varchar(100) NOT NULL,
  `DISTRICT` varchar(50) NOT NULL,
  `IS_ACTIVE` enum('Yes','No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_requirement`
--

CREATE TABLE `tbl_requirement` (
  `REQUIREMENT_ID` int(11) NOT NULL,
  `REQUIREMENT_NAME` varchar(100) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `REQUIRED` enum('Yes','No') DEFAULT 'Yes',
  `CATEGORY` enum('Personal','Academic','Financial','Other') DEFAULT 'Other',
  `DISPLAY_ORDER` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applicants`
--

CREATE TABLE `tbl_applicants` (
  `APPLICANTID` int(11) NOT NULL,
  `FIRSTNAME` varchar(100) NOT NULL,
  `MIDDLENAME` varchar(100) DEFAULT NULL,
  `LASTNAME` varchar(100) NOT NULL,
  `SUFFIX` varchar(10) DEFAULT NULL,
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
  `STATUS` enum('Pending','Approved','Rejected','For Interview','Qualified','Scholar') DEFAULT 'Pending',
  `EXAM_STATUS` enum('Pending','Passed','Failed') DEFAULT 'Pending',
  `EXAM_SLIP_NUMBER` varchar(50) DEFAULT NULL,
  `EXAM_SLIP_GENERATED` datetime DEFAULT NULL,
  `EXAM_DATE` date DEFAULT NULL,
  `EXAM_TIME` time DEFAULT NULL,
  `EXAM_VENUE` varchar(255) DEFAULT NULL,
  `EXAM_NOTES` text DEFAULT NULL,
  `CREATED_BY` int(11) NOT NULL,
  `DATECREATED` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applicant_requirement_checklist`
--

CREATE TABLE `tbl_applicant_requirement_checklist` (
  `CHECKLIST_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `REQUIREMENT_ID` int(11) NOT NULL,
  `IS_SUBMITTED` tinyint(1) DEFAULT 0,
  `IS_VERIFIED` tinyint(1) DEFAULT 0,
  `VERIFIED_BY` int(11) DEFAULT NULL,
  `VERIFIED_DATE` datetime DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_application_log`
--

CREATE TABLE `tbl_application_log` (
  `LOG_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `ACTION` varchar(100) NOT NULL,
  `ACTION_BY` int(11) NOT NULL,
  `LOG_DATE` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_family_background`
--

CREATE TABLE `tbl_family_background` (
  `FAMILY_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `FATHER_NAME` varchar(150) DEFAULT NULL,
  `FATHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `FATHER_INCOME` decimal(10,2) DEFAULT NULL,
  `MOTHER_NAME` varchar(150) DEFAULT NULL,
  `MOTHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `MOTHER_INCOME` decimal(10,2) DEFAULT NULL,
  `GUARDIAN_NAME` varchar(150) DEFAULT NULL,
  `NUMBER_OF_SIBLINGS` int(11) DEFAULT NULL,
  `HOUSEHOLD_INCOME` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_educational_background`
--

CREATE TABLE `tbl_educational_background` (
  `EDUC_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `ELEMENTARY_SCHOOL` varchar(150) DEFAULT NULL,
  `ELEMENTARY_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `JUNIOR_HIGH_SCHOOL` varchar(150) DEFAULT NULL,
  `JUNIOR_HIGH_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `SENIOR_HIGH_SCHOOL` varchar(150) DEFAULT NULL,
  `SENIOR_HIGH_TRACK` varchar(100) DEFAULT NULL,
  `SENIOR_HIGH_YEAR_GRADUATED` year(4) DEFAULT NULL,
  `GPA` decimal(5,2) DEFAULT NULL,
  `AWARDS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_exam_results`
--

CREATE TABLE `tbl_exam_results` (
  `EXAM_RESULT_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `EXAMINER_ID` int(11) NOT NULL,
  `EXAM_DATE` datetime DEFAULT current_timestamp(),
  `TOTAL_SCORE` int(11) DEFAULT NULL,
  `PASSING_SCORE` int(11) DEFAULT NULL,
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_interview`
--

CREATE TABLE `tbl_interview` (
  `INTERVIEW_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `INTERVIEWER_ID` int(11) NOT NULL,
  `INTERVIEW_DATE` datetime NOT NULL,
  `INTERVIEW_MODE` enum('Face-to-face','Online','Phone') DEFAULT 'Face-to-face',
  `SCORE` decimal(5,2) DEFAULT NULL,
  `COMMENTS` text DEFAULT NULL,
  `RECOMMENDATION` enum('Pass','Fail','For Review') DEFAULT 'For Review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_evaluation`
--

CREATE TABLE `tbl_evaluation` (
  `EVALUATION_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `EVALUATED_BY` int(11) NOT NULL,
  `FINAL_STATUS` enum('For Verification','Pending Requirement','For Interview','Qualified','Not Qualified') DEFAULT NULL,
  `FEEDBACK` text DEFAULT NULL,
  `EVALUATION_DATE` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scholarship_awards`
--

CREATE TABLE `tbl_scholarship_awards` (
  `AWARD_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') DEFAULT '1st Semester',
  `AWARD_DATE` date DEFAULT curdate(),
  `AWARDED_BY` int(11) NOT NULL,
  `AMOUNT` decimal(10,2) DEFAULT NULL,
  `STATUS` enum('Active','Inactive','Graduated','Terminated') DEFAULT 'Active',
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scholarship_history`
--

CREATE TABLE `tbl_scholarship_history` (
  `HISTORY_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `STATUS` enum('Applied','Exam Taken','Interviewed','Awarded','Renewed','Graduated') NOT NULL,
  `GPA` decimal(5,2) DEFAULT NULL,
  `REMARKS` text DEFAULT NULL,
  `UPDATED_BY` int(11) DEFAULT NULL,
  `UPDATED_AT` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_renewal_applications`
--

CREATE TABLE `tbl_renewal_applications` (
  `RENEWAL_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `SEMESTER` enum('1st Semester','2nd Semester','Summer') DEFAULT '1st Semester',
  `PREVIOUS_GPA` decimal(5,2) DEFAULT NULL,
  `UNITS_COMPLETED` int(11) DEFAULT NULL,
  `STATUS` enum('Pending','Approved','Denied') DEFAULT 'Pending',
  `REVIEWED_BY` int(11) DEFAULT NULL,
  `REVIEW_DATE` datetime DEFAULT NULL,
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notifications`
--

CREATE TABLE `tbl_notifications` (
  `NOTIF_ID` int(11) NOT NULL,
  `USERID` int(11) NOT NULL,
  `TITLE` varchar(200) NOT NULL,
  `MESSAGE` text NOT NULL,
  `TYPE` enum('Application','Exam','Interview','Award','Renewal','System') DEFAULT 'System',
  `IS_READ` tinyint(1) DEFAULT 0,
  `CREATED_AT` timestamp DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_document_tracking`
--

CREATE TABLE `tbl_document_tracking` (
  `TRACKING_ID` int(11) NOT NULL,
  `APPLICANTID` int(11) NOT NULL,
  `DOCUMENT_TYPE` varchar(100) NOT NULL,
  `DATE_RECEIVED` date DEFAULT NULL,
  `RECEIVED_BY` int(11) DEFAULT NULL,
  `STATUS` enum('Received','Pending','Returned','Approved') DEFAULT 'Pending',
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`USERID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`),
  ADD KEY `idx_user_role` (`ROLE`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`ADMIN_ID`),
  ADD KEY `USERID` (`USERID`);

--
-- Indexes for table `tbl_municipalities`
--
ALTER TABLE `tbl_municipalities`
  ADD PRIMARY KEY (`MUNICIPALITY_ID`);

--
-- Indexes for table `tbl_requirement`
--
ALTER TABLE `tbl_requirement`
  ADD PRIMARY KEY (`REQUIREMENT_ID`);

--
-- Indexes for table `tbl_applicants`
--
ALTER TABLE `tbl_applicants`
  ADD PRIMARY KEY (`APPLICANTID`),
  ADD KEY `CREATED_BY` (`CREATED_BY`),
  ADD KEY `idx_applicant_status` (`STATUS`,`EXAM_STATUS`),
  ADD KEY `idx_application_dates` (`DATECREATED`),
  ADD KEY `idx_municipality` (`MUNICIPALITY`,`DISTRICT`);

--
-- Indexes for table `tbl_applicant_requirement_checklist`
--
ALTER TABLE `tbl_applicant_requirement_checklist`
  ADD PRIMARY KEY (`CHECKLIST_ID`),
  ADD UNIQUE KEY `unique_applicant_requirement` (`APPLICANTID`,`REQUIREMENT_ID`),
  ADD KEY `REQUIREMENT_ID` (`REQUIREMENT_ID`),
  ADD KEY `VERIFIED_BY` (`VERIFIED_BY`);

--
-- Indexes for table `tbl_application_log`
--
ALTER TABLE `tbl_application_log`
  ADD PRIMARY KEY (`LOG_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `ACTION_BY` (`ACTION_BY`);

--
-- Indexes for table `tbl_family_background`
--
ALTER TABLE `tbl_family_background`
  ADD PRIMARY KEY (`FAMILY_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`);

--
-- Indexes for table `tbl_educational_background`
--
ALTER TABLE `tbl_educational_background`
  ADD PRIMARY KEY (`EDUC_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`);

--
-- Indexes for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  ADD PRIMARY KEY (`EXAM_RESULT_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `EXAMINER_ID` (`EXAMINER_ID`);

--
-- Indexes for table `tbl_interview`
--
ALTER TABLE `tbl_interview`
  ADD PRIMARY KEY (`INTERVIEW_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `INTERVIEWER_ID` (`INTERVIEWER_ID`);

--
-- Indexes for table `tbl_evaluation`
--
ALTER TABLE `tbl_evaluation`
  ADD PRIMARY KEY (`EVALUATION_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `EVALUATED_BY` (`EVALUATED_BY`);

--
-- Indexes for table `tbl_scholarship_awards`
--
ALTER TABLE `tbl_scholarship_awards`
  ADD PRIMARY KEY (`AWARD_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `AWARDED_BY` (`AWARDED_BY`);

--
-- Indexes for table `tbl_scholarship_history`
--
ALTER TABLE `tbl_scholarship_history`
  ADD PRIMARY KEY (`HISTORY_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `UPDATED_BY` (`UPDATED_BY`);

--
-- Indexes for table `tbl_renewal_applications`
--
ALTER TABLE `tbl_renewal_applications`
  ADD PRIMARY KEY (`RENEWAL_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `REVIEWED_BY` (`REVIEWED_BY`);

--
-- Indexes for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD PRIMARY KEY (`NOTIF_ID`),
  ADD KEY `USERID` (`USERID`);

--
-- Indexes for table `tbl_document_tracking`
--
ALTER TABLE `tbl_document_tracking`
  ADD PRIMARY KEY (`TRACKING_ID`),
  ADD KEY `APPLICANTID` (`APPLICANTID`),
  ADD KEY `RECEIVED_BY` (`RECEIVED_BY`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `USERID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `ADMIN_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_municipalities`
--
ALTER TABLE `tbl_municipalities`
  MODIFY `MUNICIPALITY_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_requirement`
--
ALTER TABLE `tbl_requirement`
  MODIFY `REQUIREMENT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_applicants`
--
ALTER TABLE `tbl_applicants`
  MODIFY `APPLICANTID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_applicant_requirement_checklist`
--
ALTER TABLE `tbl_applicant_requirement_checklist`
  MODIFY `CHECKLIST_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_application_log`
--
ALTER TABLE `tbl_application_log`
  MODIFY `LOG_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_family_background`
--
ALTER TABLE `tbl_family_background`
  MODIFY `FAMILY_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_educational_background`
--
ALTER TABLE `tbl_educational_background`
  MODIFY `EDUC_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  MODIFY `EXAM_RESULT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_interview`
--
ALTER TABLE `tbl_interview`
  MODIFY `INTERVIEW_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_evaluation`
--
ALTER TABLE `tbl_evaluation`
  MODIFY `EVALUATION_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_scholarship_awards`
--
ALTER TABLE `tbl_scholarship_awards`
  MODIFY `AWARD_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_scholarship_history`
--
ALTER TABLE `tbl_scholarship_history`
  MODIFY `HISTORY_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_renewal_applications`
--
ALTER TABLE `tbl_renewal_applications`
  MODIFY `RENEWAL_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `NOTIF_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_document_tracking`
--
ALTER TABLE `tbl_document_tracking`
  MODIFY `TRACKING_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD CONSTRAINT `tbl_admin_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_applicants`
--
ALTER TABLE `tbl_applicants`
  ADD CONSTRAINT `tbl_applicants_ibfk_1` FOREIGN KEY (`CREATED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_applicant_requirement_checklist`
--
ALTER TABLE `tbl_applicant_requirement_checklist`
  ADD CONSTRAINT `tbl_applicant_checklist_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_applicant_checklist_ibfk_2` FOREIGN KEY (`REQUIREMENT_ID`) REFERENCES `tbl_requirement` (`REQUIREMENT_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_applicant_checklist_ibfk_3` FOREIGN KEY (`VERIFIED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_application_log`
--
ALTER TABLE `tbl_application_log`
  ADD CONSTRAINT `tbl_application_log_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_application_log_ibfk_2` FOREIGN KEY (`ACTION_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_family_background`
--
ALTER TABLE `tbl_family_background`
  ADD CONSTRAINT `tbl_family_background_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_educational_background`
--
ALTER TABLE `tbl_educational_background`
  ADD CONSTRAINT `tbl_educational_background_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_exam_results`
--
ALTER TABLE `tbl_exam_results`
  ADD CONSTRAINT `tbl_exam_results_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_exam_results_ibfk_2` FOREIGN KEY (`EXAMINER_ID`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_interview`
--
ALTER TABLE `tbl_interview`
  ADD CONSTRAINT `tbl_interview_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_interview_ibfk_2` FOREIGN KEY (`INTERVIEWER_ID`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_evaluation`
--
ALTER TABLE `tbl_evaluation`
  ADD CONSTRAINT `tbl_evaluation_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_evaluation_ibfk_2` FOREIGN KEY (`EVALUATED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_scholarship_awards`
--
ALTER TABLE `tbl_scholarship_awards`
  ADD CONSTRAINT `tbl_scholarship_awards_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_scholarship_awards_ibfk_2` FOREIGN KEY (`AWARDED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_scholarship_history`
--
ALTER TABLE `tbl_scholarship_history`
  ADD CONSTRAINT `tbl_scholarship_history_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_scholarship_history_ibfk_2` FOREIGN KEY (`UPDATED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_renewal_applications`
--
ALTER TABLE `tbl_renewal_applications`
  ADD CONSTRAINT `tbl_renewal_applications_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_renewal_applications_ibfk_2` FOREIGN KEY (`REVIEWED_BY`) REFERENCES `tblusers` (`USERID`);

--
-- Constraints for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD CONSTRAINT `tbl_notifications_ibfk_1` FOREIGN KEY (`USERID`) REFERENCES `tblusers` (`USERID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_document_tracking`
--
ALTER TABLE `tbl_document_tracking`
  ADD CONSTRAINT `tbl_document_tracking_ibfk_1` FOREIGN KEY (`APPLICANTID`) REFERENCES `tbl_applicants` (`APPLICANTID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_document_tracking_ibfk_2` FOREIGN KEY (`RECEIVED_BY`) REFERENCES `tblusers` (`USERID`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;