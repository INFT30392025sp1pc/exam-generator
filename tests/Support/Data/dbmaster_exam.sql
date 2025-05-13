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
-- Table structure for table `exam`
--

DROP TABLE IF EXISTS `exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam` (
  `exam_ID` int NOT NULL AUTO_INCREMENT,
  `time_created` datetime DEFAULT NULL,
  `exam_year` int DEFAULT NULL,
  `exam_sp` varchar(3) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `is_supplementary` tinyint(1) DEFAULT NULL,
  `student_ID` int DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `exam_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`exam_ID`),
  KEY `subject_code` (`subject_code`),
  KEY `exam_ibfk_1` (`student_ID`),
  CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`student_ID`) REFERENCES `student` (`student_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_ibfk_2` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam`
--

LOCK TABLES `exam` WRITE;
/*!40000 ALTER TABLE `exam` DISABLE KEYS */;
INSERT INTO `exam` VALUES (85,'2025-05-08 00:29:56',2025,'SP1',NULL,0,NULL,'TST01','TEST01'),(88,'2025-05-09 15:37:40',2025,'SP6',NULL,0,NULL,'TST01','Test'),(94,'2025-05-09 15:57:41',2025,'SP3',NULL,0,NULL,'TST01','Culinary Delights'),(95,'2025-05-09 16:44:20',2025,'SP3',NULL,0,NULL,'UOCD1','Culinary Delights'),(96,'2025-05-09 17:38:00',2025,'SP6',NULL,0,NULL,'UOCD1','Culinary Delights'),(97,'2025-05-10 13:23:51',2025,'SP6',NULL,0,NULL,'UOCD1','Exam Test'),(98,'2025-05-10 14:18:20',2025,'SP4',NULL,0,NULL,'CIVL1010','Roof Truss Design 101'),(99,'2025-05-10 15:57:20',2025,'SP1',NULL,0,NULL,'CE101','Civil Truss Exam'),(100,'2025-05-10 16:08:14',2025,'SP1',NULL,0,NULL,'CIVL1010','Civil Truss Master'),(101,'2025-05-12 08:35:48',2025,'SP1',NULL,0,NULL,'CIVL1010','Siamak Test');
/*!40000 ALTER TABLE `exam` ENABLE KEYS */;
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

-- Dump completed on 2025-05-13 19:57:47
