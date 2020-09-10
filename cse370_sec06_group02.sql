-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 10, 2020 at 04:36 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cse370_sec06_group02`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `Name` varchar(128) NOT NULL,
  `Email` varchar(128) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `Deadline` date NOT NULL,
  PRIMARY KEY (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Name`, `Email`, `Password`, `Deadline`) VALUES
('Admin1', 'admin@cse370.edu', '12345678', '2020-10-31');

-- --------------------------------------------------------

--
-- Table structure for table `bachelors_aid`
--

DROP TABLE IF EXISTS `bachelors_aid`;
CREATE TABLE IF NOT EXISTS `bachelors_aid` (
  `Type` varchar(20) NOT NULL,
  `Scholarship_ID` int(11) NOT NULL,
  PRIMARY KEY (`Scholarship_ID`),
  UNIQUE KEY `Scholarship_ID` (`Scholarship_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grad_apply`
--

DROP TABLE IF EXISTS `grad_apply`;
CREATE TABLE IF NOT EXISTS `grad_apply` (
  `Status` char(1) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  `Scholarship_ID` int(11) NOT NULL,
  KEY `Scholarship_ID` (`Scholarship_ID`),
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grad_student`
--

DROP TABLE IF EXISTS `grad_student`;
CREATE TABLE IF NOT EXISTS `grad_student` (
  `Bachelor_CGPA` decimal(6,2) DEFAULT NULL,
  `GRE_Score` decimal(6,2) DEFAULT NULL,
  `IELTS_Score` decimal(6,2) DEFAULT NULL,
  `Student_ID` int(8) NOT NULL,
  PRIMARY KEY (`Student_ID`),
  UNIQUE KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grad_student`
--


-- --------------------------------------------------------

--
-- Table structure for table `masters_aid`
--

DROP TABLE IF EXISTS `masters_aid`;
CREATE TABLE IF NOT EXISTS `masters_aid` (
  `Type` varchar(20) NOT NULL,
  `Scholarship_ID` int(11) NOT NULL,
  PRIMARY KEY (`Scholarship_ID`),
  UNIQUE KEY `Scholarship_ID` (`Scholarship_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `phone_no`
--

DROP TABLE IF EXISTS `phone_no`;
CREATE TABLE IF NOT EXISTS `phone_no` (
  `PhoneNumber` varchar(11) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `phone_no`
--


-- --------------------------------------------------------

--
-- Table structure for table `scholarship`
--

DROP TABLE IF EXISTS `scholarship`;
CREATE TABLE IF NOT EXISTS `scholarship` (
  `Name` varchar(128) NOT NULL,
  `Reason` varchar(128) DEFAULT NULL,
  `Scholarship_ID` int(8) NOT NULL,
  `Student_ID` int(8) NOT NULL,
  `Semester` int(2) NOT NULL,
  `Amount` int(11) NOT NULL,
  `RequestedAt` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Scholarship_ID`),
  UNIQUE KEY `Scholarship_ID` (`Scholarship_ID`),
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `Street` varchar(128) DEFAULT NULL,
  `House` varchar(128) DEFAULT NULL,
  `City` varchar(128) DEFAULT NULL,
  `SSN` varchar(9) DEFAULT NULL,
  `Student_ID` int(8) NOT NULL,
  `Email` varchar(128) NOT NULL,
  `Fname` varchar(128) NOT NULL,
  `Lname` varchar(128) NOT NULL,
  `Type` varchar(6) NOT NULL,
  `Semester` int(2) NOT NULL,
  `CGPA` decimal(6,2) NOT NULL,
  `Enrollment_date` date DEFAULT NULL,
  `Password` varchar(128) NOT NULL,
  PRIMARY KEY (`Student_ID`),
  UNIQUE KEY `Student_ID` (`Student_ID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `SSN` (`SSN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--


-- --------------------------------------------------------

--
-- Table structure for table `student_majors`
--

DROP TABLE IF EXISTS `student_majors`;
CREATE TABLE IF NOT EXISTS `student_majors` (
  `Majors` varchar(64) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student_majors`
--

-- --------------------------------------------------------

--
-- Table structure for table `undergrad_apply`
--

DROP TABLE IF EXISTS `undergrad_apply`;
CREATE TABLE IF NOT EXISTS `undergrad_apply` (
  `Status` char(1) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  `Scholarship_ID` int(11) NOT NULL,
  KEY `Student_ID` (`Student_ID`),
  KEY `Scholarship_ID` (`Scholarship_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `undergrad_student`
--

DROP TABLE IF EXISTS `undergrad_student`;
CREATE TABLE IF NOT EXISTS `undergrad_student` (
  `Highschool_Result` decimal(6,2) DEFAULT NULL,
  `Admission_Result` decimal(6,2) DEFAULT NULL,
  `Student_ID` int(8) NOT NULL,
  PRIMARY KEY (`Student_ID`),
  UNIQUE KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `undergrad_student`
--

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bachelors_aid`
--
ALTER TABLE `bachelors_aid`
  ADD CONSTRAINT `bachelors_aid_ibfk_1` FOREIGN KEY (`Scholarship_ID`) REFERENCES `scholarship` (`Scholarship_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grad_apply`
--
ALTER TABLE `grad_apply`
  ADD CONSTRAINT `grad_apply_ibfk_1` FOREIGN KEY (`Scholarship_ID`) REFERENCES `masters_aid` (`Scholarship_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grad_apply_ibfk_2` FOREIGN KEY (`Student_ID`) REFERENCES `grad_student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grad_student`
--
ALTER TABLE `grad_student`
  ADD CONSTRAINT `grad_student_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `masters_aid`
--
ALTER TABLE `masters_aid`
  ADD CONSTRAINT `masters_aid_ibfk_1` FOREIGN KEY (`Scholarship_ID`) REFERENCES `scholarship` (`Scholarship_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phone_no`
--
ALTER TABLE `phone_no`
  ADD CONSTRAINT `phone_no_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD CONSTRAINT `scholarship_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_majors`
--
ALTER TABLE `student_majors`
  ADD CONSTRAINT `student_majors_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `undergrad_apply`
--
ALTER TABLE `undergrad_apply`
  ADD CONSTRAINT `undergrad_apply_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `undergrad_student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `undergrad_apply_ibfk_2` FOREIGN KEY (`Scholarship_ID`) REFERENCES `bachelors_aid` (`Scholarship_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `undergrad_student`
--
ALTER TABLE `undergrad_student`
  ADD CONSTRAINT `undergrad_student_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `student` (`Student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
