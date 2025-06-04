-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: ls-d569aff0f012a672548dfa141c2ff6da29f4a426.cx4g6oyqyv3o.ap-southeast-2.rds.amazonaws.com    Database: dbmaster
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
-- SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
-- SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

-- SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question` (
  `question_ID` int NOT NULL AUTO_INCREMENT,
  `time_created` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `contents` text,
  `exam_ID` int DEFAULT NULL,
  PRIMARY KEY (`question_ID`),
  KEY `exam_ID` (`exam_ID`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`exam_ID`) REFERENCES `exam` (`exam_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=707 DEFAULT CHARSET=utf8mb4 COLLATE=  utf8mb4_unicode_ci ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (280,'2025-05-09 17:38:19',NULL,'How much is a litre of milk?\r',94),(281,'2025-05-09 17:38:19',NULL,'What colour do you get if you mix red and gree?\r',94),(282,'2025-05-09 17:38:19',NULL,'Test',94),(309,'2025-05-10 14:26:08',NULL,'Calculate the axial force in member AB of the king post truss using the method of joints.\r',98),(310,'2025-05-10 14:26:08',NULL,'Determine the support reactions at points A and E for the given truss loaded at joints.\r',98),(311,'2025-05-10 14:26:08',NULL,'Using the method of sections, find the force in members CD, DE, and CE of the given roof truss.\r',98),(312,'2025-05-10 14:26:08',NULL,'Explain the difference between zero-force members and redundant members in a statically determinate truss.\r',98),(313,'2025-05-10 14:26:08',NULL,'A roof truss is subjected to a uniform distributed load of 5 kN/m. Determine the maximum tension and compression forces in any member.',98),(319,'2025-05-10 15:58:22',NULL,'Determine the forces in members AB, BC, and AC of the given triangular truss using the method of joints.\r',99),(320,'2025-05-10 15:58:22',NULL,'Identify all zero-force members in the provided truss structure. Explain your reasoning.\r',99),(321,'2025-05-10 15:58:22',NULL,'Using the method of sections, calculate the internal force in member DE. Indicate whether it is in tension or compression.\r',99),(322,'2025-05-10 15:58:22',NULL,'Given a uniformly distributed load on the top chord of the truss, calculate the reaction forces at the supports.\r',99),(323,'2025-05-10 15:58:22',NULL,'Suggest a suitable cross-sectional area for member FG to safely resist a compressive force of 25 kN. Use the provided yield strength.',99),(334,'2025-05-10 16:09:24',NULL,'Determine the forces in members AB, BC, and AC of the given triangular truss using the method of joints.\r',100),(335,'2025-05-10 16:09:24',NULL,'Identify all zero-force members in the provided truss structure. Explain your reasoning.\r',100),(336,'2025-05-10 16:09:24',NULL,'Using the method of sections, calculate the internal force in member DE. Indicate whether it is in tension or compression.\r',100),(337,'2025-05-10 16:09:24',NULL,'Given a uniformly distributed load on the top chord of the truss, calculate the reaction forces at the supports.\r',100),(338,'2025-05-10 16:09:25',NULL,'Suggest a suitable cross-sectional area for member FG to safely resist a compressive force of 25 kN. Use the provided yield strength.',100),(340,'2025-05-12 08:36:03',NULL,'How much for a litre of milk?',101),(343,'2025-05-16 01:07:28',NULL,'This is a test question\r',102),(344,'2025-05-16 01:07:28',NULL,'This is a test question 2',102),(346,'2025-05-27 08:34:51',NULL,'How much for a bottle of milk?',106),(574,'2025-06-01 11:41:57',NULL,'A simple pin-jointed truss is loaded with a vertical force of 1000 N at joint C. Using the method of joints, determine the internal forces in members AC, BC, and CD. Indicate whether the forces are tensile or compressive.\r',111),(575,'2025-06-01 11:41:57',NULL,'For the truss shown, with support A pinned and support E on a roller, calculate the support reactions at A and E. The truss carries a uniform vertical load on joint C. Assume all members are weightless.\r',111),(576,'2025-06-01 11:41:57',NULL,'Using the method of sections, determine the forces in members EF, DF, and DE of the loaded truss. A vertical load of 1200 N is applied at joint F. Clearly show your section and assumptions.\r',111),(577,'2025-06-01 11:41:57',NULL,'A Pratt truss is subjected to vertical loads at the top and bottom chords. Explain how zero-force members are identified in a truss structure and identify all zero-force members in the given configuration.\r',111),(578,'2025-06-01 11:41:57',NULL,'Given a Warren truss with a span of 6 m and equal panel lengths, determine the maximum force in any member when a point load of 1500 N is applied at the center-top joint. Use symmetry in your analysis.\r',111),(579,'2025-06-01 11:41:57',NULL,'Design an appropriate cross-section for a truss member experiencing a compressive force of 18 kN. The material is structural steel with a yield strength of 250 MPa, and a factor of safety of 1.5 is required. Show all calculations including buckling checks.',111),(581,'2025-06-01 11:59:42',NULL,'How much is a litre of milk?\r',88),(582,'2025-06-01 11:59:43',NULL,'How much is a loaf of bread?\r',88),(583,'2025-06-01 11:59:43',NULL,'What was today\'s weather?\r',88),(584,'2025-06-01 11:59:43',NULL,'Test',88),(661,'2025-06-01 14:24:24',NULL,'How long is a piece of string?\r',113),(662,'2025-06-01 14:24:24',NULL,'What is the circumference of the moon?\r',113),(663,'2025-06-01 14:24:24',NULL,'Who was the first Prime Minister of Australia?\r',113),(664,'2025-06-01 14:24:24',NULL,'In what year was the Government controversially dismissed in Australia, causing a constitutional crisis?\r',113),(665,'2025-06-01 14:24:24',NULL,'True or False: Ginger cats are usually male?\r',113),(666,'2025-06-01 14:24:24',NULL,'What year did Princess Diana die?\r',113),(667,'2025-06-01 14:24:24',NULL,'What is \"Banjo\" Patterson\'s real first name?\r',113),(668,'2025-06-01 14:24:24',NULL,'How much wood would a wood chuck cuck if a wood chuck could chuck wood?\r',113),(669,'2025-06-01 14:24:24',NULL,'How many legs does a spider have?\r',113),(670,'2025-06-01 14:24:24',NULL,'Name the Australian egg laying mammal?\r',113),(671,'2025-06-01 14:24:24',NULL,'What is meant by the popular showbiz term \"Jumping the Shark\"?\r',113),(672,'2025-06-01 14:24:24',NULL,'What colour is a red onion?\r',113),(673,'2025-06-01 14:24:24',NULL,'What is the english name for the popular dessert \"Schwarzwalder Kirschtorte\"?',113),(682,'2025-06-01 14:28:25',NULL,'How long is a piece of string?\r',115),(683,'2025-06-01 14:28:25',NULL,'What is the circumference of the moon?\r',115),(684,'2025-06-01 14:28:25',NULL,'Who was the first Prime Minister of Australia?\r',115),(685,'2025-06-01 14:28:25',NULL,'In what year was the Government controversially dismissed in Australia, causing a constitutional crisis?\r',115),(686,'2025-06-01 14:28:25',NULL,'True or False: Ginger cats are usually male?\r',115),(687,'2025-06-01 14:28:25',NULL,'What year did Princess Diana die?\r',115),(688,'2025-06-01 14:28:25',NULL,'What is \"Banjo\" Patterson\'s real first name?\r',115),(689,'2025-06-01 14:28:25',NULL,'How much wood would a wood chuck cuck if a wood chuck could chuck wood?',115),(698,'2025-06-01 15:03:19',NULL,'How long is a piece of string?\r',116),(699,'2025-06-01 15:03:19',NULL,'What is the circumference of the moon?\r',116),(700,'2025-06-01 15:03:19',NULL,'Who was the first Prime Minister of Australia?\r',116),(701,'2025-06-01 15:03:19',NULL,'In what year was the Government controversially dismissed in Australia, causing a constitutional crisis?\r',116),(702,'2025-06-01 15:03:19',NULL,'True or False: Ginger cats are usually male?\r',116),(703,'2025-06-01 15:03:19',NULL,'What year did Princess Diana die?\r',116),(704,'2025-06-01 15:03:19',NULL,'What is \"Banjo\" Patterson\'s real first name?\r',116),(705,'2025-06-01 15:03:19',NULL,'How much wood would a wood chuck cuck if a wood chuck could chuck wood?',116),(706,'2025-06-02 22:39:58',NULL,'Test 2',88);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;
-- SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-04 11:28:50
