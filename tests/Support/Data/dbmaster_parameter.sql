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
-- Table structure for table `parameter`
--

DROP TABLE IF EXISTS `parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parameter` (
  `parameter_ID` int NOT NULL AUTO_INCREMENT,
  `parameter_name` varchar(30) DEFAULT NULL,
  `parameter_lower` decimal(10,2) DEFAULT NULL,
  `parameter_upper` decimal(10,2) DEFAULT NULL,
  `question_ID` int DEFAULT NULL,
  `truss_ID` int NOT NULL,
  `student_ID` int DEFAULT NULL,
  `exam_ID` int NOT NULL,
  PRIMARY KEY (`parameter_ID`),
  KEY `question_ID` (`question_ID`),
  KEY `fk_parameter_student` (`student_ID`),
  KEY `fk_parameter_trussimage` (`truss_ID`),
  CONSTRAINT `fk_parameter_student` FOREIGN KEY (`student_ID`) REFERENCES `student` (`student_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_parameter_trussimage` FOREIGN KEY (`truss_ID`) REFERENCES `trussimage` (`truss_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `parameter_ibfk_1` FOREIGN KEY (`question_ID`) REFERENCES `question` (`question_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parameter`
--

LOCK TABLES `parameter` WRITE;
/*!40000 ALTER TABLE `parameter` DISABLE KEYS */;
INSERT INTO `parameter` VALUES (8,'Total Span of Truss (m)',6.00,12.00,NULL,3,NULL,100),(9,'Point Load at Node B (kN)',5.00,20.00,NULL,3,NULL,100),(10,'Yield Strength of Steel (MPa)',250.00,450.00,NULL,3,NULL,100),(11,'Cross-sectional Area (mm2)',200.00,800.00,NULL,3,NULL,100),(12,'Support Reaction at A (kN)',0.00,30.00,NULL,3,NULL,100),(13,'P1',1.00,3.00,NULL,4,NULL,102),(14,'P2',2.00,4.00,NULL,4,NULL,102),(15,'P1',10.00,100.00,NULL,3,NULL,100),(16,'LoadAtJointC_N',800.00,1200.00,NULL,8,NULL,111),(17,'LoadAtJointF_N',1000.00,1500.00,NULL,8,NULL,111),(18,'UniformLoadAtJointC_N',500.00,1000.00,NULL,8,NULL,111),(19,'PointLoadAtTopCenterJoint_N',1000.00,2000.00,NULL,8,NULL,111),(20,'TrussSpan_m',4.00,10.00,NULL,8,NULL,111),(21,'CompressiveForce_kN',10.00,25.00,NULL,8,NULL,111),(22,'YieldStrength_MPa',230.00,300.00,NULL,8,NULL,111),(23,'FactorOfSafety',1.20,2.40,NULL,8,NULL,111),(25,'A',15.20,60.80,NULL,8,NULL,111),(26,'B',10.00,50.00,NULL,8,NULL,111),(27,'1',10.00,100.00,NULL,9,NULL,118);
/*!40000 ALTER TABLE `parameter` ENABLE KEYS */;
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

-- Dump completed on 2025-06-06  0:01:33
