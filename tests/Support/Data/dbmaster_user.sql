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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_ID` int NOT NULL AUTO_INCREMENT,
  `user_password` char(32) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (2,md5('123'),'John','March','test.admin@gmail.com','Administrator'),(9,md5('123'),'test','name','test.coordinator@gmail.com','Coordinator'),(10,'098f6bcd4621d373cade4e832627b4f6','Alice','Smith','alice.smith@gmail.com','Coordinator'),(11,'098f6bcd4621d373cade4e832627b4f6','Alice','Super','alice.super@gmail.com','Coordinator'),(12,'098f6bcd4621d373cade4e832627b4f6','John','Smith','John.Smith@gmail.com','Coordinator'),(22,'25d55ad283aa400af464c76d713c07ad','Will','Smith','test@gamil.com','Administrator'),(23,'25d55ad283aa400af464c76d713c07ad','John','Smith','test_coordinator@gmail.com','Coordinator'),(25,'ee8f507381220f616d153c7eeac7fe88','Disabled','Test','test1@example.com','Test'),(26,'25f9e794323b453885f5181f1b624d0b','Lisa','Donaldson','lisee321@hotmail.co.uk','Coordinator'),(27,'ee8f507381220f616d153c7eeac7fe88','Lisa','Pietsch','donly03f@mymail.unisa.edu.au','Coordinator'),(28,'0de08c9e0130aee7ba17b97d675ad003','Sisi','Liu','sisi.liu@unisa.edu.au','administrator'),(29,'e83af3f9bd8bc4a413971c41a5261796','Danda','Li','danda.li@unisa.edu.au','Administrator'),(30,'e83af3f9bd8bc4a413971c41a5261796','Danda','Li','danda.li@unisa.edu.au','Administrator'),(31,'sisi.liu_coordinator@unisa.edu.a','Sisi','Liu','sisi.liu_coordinator@unisa.edu.au','Coordinator'),(32,'2da74108a58e0a67f037d4de49c0beb8','Sisi','Liu','sisi.liu_coordinator@unisa.edu.au','Coordinator'),(33,'dc95d51d233899656a12a0ea3c694f2a','Danda','Li','danda.li_coordinator@unisa.edu.au','Coordinator');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
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

-- Dump completed on 2025-06-06  0:00:57
