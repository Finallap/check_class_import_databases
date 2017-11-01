-- MySQL dump 10.16  Distrib 10.1.16-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: check_class
-- ------------------------------------------------------
-- Server version	10.1.16-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Table structure for table `classroom_information`
--

DROP TABLE IF EXISTS `classroom_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classroom_information` (
  `full_number` varchar(10) NOT NULL,
  `teaching_building_number` int(11) DEFAULT NULL,
  `classroom_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`full_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `course_class_information`
--

DROP TABLE IF EXISTS `course_class_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_class_information` (
  `course_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` varchar(10) DEFAULT NULL,
  `term` int(11) DEFAULT NULL,
  `class_id` varchar(15) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`course_class_id`),
  KEY `course_id` (`course_id`),
  KEY `school_year` (`school_year`),
  KEY `term` (`term`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5482 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `college_information`
--

DROP TABLE IF EXISTS `college_information`;
CREATE TABLE `college_information` (
  `college_id` int(11) NOT NULL,
  `college_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`college_id`),
  KEY `college_id` (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Insert college data for importing class information.
--

INSERT INTO `college_information`(`college_id`,`college_name`) values
('17','海外教育学院'),
('16','贝尔英才学院'),
('15','教育科学与技术学院'),
('14','外国语学院'),
('13','人文与社会科学学院'),
('12','经济学院'),
('11','管理学院'),
('10','传媒与艺术学院'),
('9','地理与生物信息学院'),
('8','理学院'),
('7','物联网学院'),
('6','材料科学与工程学院'),
('5','自动化学院'),
('4','计算机学院、软件学院、网络空间安全学院'),
('3','现代邮政学院'),
('2','电子与光学工程学院'),
('1','通信与信息工程学院');

--
-- Table structure for table `course_information`
--

DROP TABLE IF EXISTS `course_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_information` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(32) DEFAULT NULL,
  `school_year` varchar(10) DEFAULT NULL,
  `term` int(11) DEFAULT NULL,
  `start_week` int(11) DEFAULT NULL,
  `end_week` int(11) DEFAULT NULL,
  `odd_even` int(11) DEFAULT NULL,
  `class_time` int(11) DEFAULT NULL,
  `weekday` int(11) DEFAULT NULL,
  `classroom` varchar(20) DEFAULT NULL,
  `tercher_name` varchar(20) DEFAULT NULL,
  `choices_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  KEY `course_id` (`course_id`),
  KEY `school_year` (`school_year`),
  KEY `term` (`term`),
  KEY `class_time` (`class_time`),
  KEY `weekday` (`weekday`),
  KEY `start_week` (`start_week`),
  KEY `end_week` (`end_week`),
  KEY `odd_even` (`odd_even`),
  KEY `classroom` (`classroom`)
) ENGINE=InnoDB AUTO_INCREMENT=2044 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Create table 'class_information'.
--

DROP TABLE IF EXISTS `class_information`;
CREATE TABLE `class_information` (
  `class_id` varchar(15) NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `class_final_id` int(11) DEFAULT NULL,
  `major` varchar(45) DEFAULT NULL,
  `password` text,
  PRIMARY KEY (`class_id`),
  KEY `class_id` (`class_id`),
  KEY `grade` (`grade`),
  KEY `college_id` (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-14 20:57:23
