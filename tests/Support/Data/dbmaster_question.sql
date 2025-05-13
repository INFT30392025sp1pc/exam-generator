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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

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
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (268,'2025-05-08 01:01:17',NULL,'This is a test question 3\r',85),(269,'2025-05-08 01:01:17',NULL,'This is a test question 4',85),(271,'2025-05-09 15:38:09',NULL,'How much is a litre of milk?',88),(280,'2025-05-09 17:38:19',NULL,'How much is a litre of milk?\r',94),(281,'2025-05-09 17:38:19',NULL,'What colour do you get if you mix red and gree?\r',94),(282,'2025-05-09 17:38:19',NULL,'Test',94),(283,'2025-05-10 13:23:59',NULL,'',97),(309,'2025-05-10 14:26:08',NULL,'Calculate the axial force in member AB of the king post truss using the method of joints.\r',98),(310,'2025-05-10 14:26:08',NULL,'Determine the support reactions at points A and E for the given truss loaded at joints.\r',98),(311,'2025-05-10 14:26:08',NULL,'Using the method of sections, find the force in members CD, DE, and CE of the given roof truss.\r',98),(312,'2025-05-10 14:26:08',NULL,'Explain the difference between zero-force members and redundant members in a statically determinate truss.\r',98),(313,'2025-05-10 14:26:08',NULL,'A roof truss is subjected to a uniform distributed load of 5 kN/m. Determine the maximum tension and compression forces in any member.',98),(319,'2025-05-10 15:58:22',NULL,'Determine the forces in members AB, BC, and AC of the given triangular truss using the method of joints.\r',99),(320,'2025-05-10 15:58:22',NULL,'Identify all zero-force members in the provided truss structure. Explain your reasoning.\r',99),(321,'2025-05-10 15:58:22',NULL,'Using the method of sections, calculate the internal force in member DE. Indicate whether it is in tension or compression.\r',99),(322,'2025-05-10 15:58:22',NULL,'Given a uniformly distributed load on the top chord of the truss, calculate the reaction forces at the supports.\r',99),(323,'2025-05-10 15:58:22',NULL,'Suggest a suitable cross-sectional area for member FG to safely resist a compressive force of 25 kN. Use the provided yield strength.',99),(334,'2025-05-10 16:09:24',NULL,'Determine the forces in members AB, BC, and AC of the given triangular truss using the method of joints.\r',100),(335,'2025-05-10 16:09:24',NULL,'Identify all zero-force members in the provided truss structure. Explain your reasoning.\r',100),(336,'2025-05-10 16:09:24',NULL,'Using the method of sections, calculate the internal force in member DE. Indicate whether it is in tension or compression.\r',100),(337,'2025-05-10 16:09:24',NULL,'Given a uniformly distributed load on the top chord of the truss, calculate the reaction forces at the supports.\r',100),(338,'2025-05-10 16:09:25',NULL,'Suggest a suitable cross-sectional area for member FG to safely resist a compressive force of 25 kN. Use the provided yield strength.',100),(340,'2025-05-12 08:36:03',NULL,'How much for a litre of milk?',101);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;
;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-13 19:58:13
