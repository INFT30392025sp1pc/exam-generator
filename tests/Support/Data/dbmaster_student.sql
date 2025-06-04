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
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `student_ID` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `student_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`student_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1208 DEFAULT CHARSET=utf8mb4 COLLATE=  utf8mb4_unicode_ci ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,'test','test',''),(1143,'John','Hammond','jhamm01@mymail.unisa.edu.au'),(1144,'Test_Student','Nobody','nosuchman@email.com'),(1145,'Liam','Andy','landerson@mymail.unisa.edu.au'),(1146,'Noah','Clark','nclark@mymail.unisa.edu.au'),(1147,'Olivia','Davies','odavies@mymail.unisa.edu.au'),(1148,'William','Evans','wevans@mymail.unisa.edu.au'),(1149,'Ava','Foster','afoster@mymail.unisa.edu.au'),(1150,'James','Gibson','jgibson@mymail.unisa.edu.au'),(1151,'Isabella','Hall','ihall@mymail.unisa.edu.au'),(1152,'Lucas','Ingram','lingram@mymail.unisa.edu.au'),(1153,'Mia','Jones','mjones@mymail.unisa.edu.au'),(1154,'Ethan','King','eking@mymail.unisa.edu.au'),(1155,'Amelia','Lewis','alewis@mymail.unisa.edu.au'),(1156,'Logan','Mitchell','lmitchell@mymail.unisa.edu.au'),(1157,'Charlotte','Nelson','cnelson@mymail.unisa.edu.au'),(1158,'Henry','Owen','howen@mymail.unisa.edu.au'),(1159,'Sophia','Parker','sparker@mymail.unisa.edu.au'),(1160,'Benjamin','Quinn','bquinn@mymail.unisa.edu.au'),(1161,'Emily','Reid','ereid@mymail.unisa.edu.au'),(1162,'Jack','Stewart','jstewart@mymail.unisa.edu.au'),(1163,'Chloe','Taylor','ctaylor@mymail.unisa.edu.au'),(1164,'Sebastian','Underwood','sunderwood@mymail.unisa.edu.au'),(1165,'Zoe','Vaughn','zvaughn@mymail.unisa.edu.au'),(1166,'Alexander','White','awhite@mymail.unisa.edu.au'),(1167,'Grace','Young','gyoung@mymail.unisa.edu.au'),(1168,'Jackson','Zimmerman','jzimmerman@mymail.unisa.edu.au'),(1169,'Ella','Barker','ebarker@mymail.unisa.edu.au'),(1170,'Levi','Chambers','lchambers@mymail.unisa.edu.au'),(1171,'Aria','Dixon','adixon@mymail.unisa.edu.au'),(1172,'Daniel','Ellis','dellis@mymail.unisa.edu.au'),(1173,'Lily','Fleming','lfleming@mymail.unisa.edu.au'),(1174,'Owen','Griffin','ogriffin@mymail.unisa.edu.au'),(1175,'Hannah','Harvey','hharvey@mymail.unisa.edu.au'),(1176,'Matthew','Irwin','mirwin@mymail.unisa.edu.au'),(1177,'Sophie','Jennings','sjennings@mymail.unisa.edu.au'),(1178,'Nathan','Kerr','nkerr@mymail.unisa.edu.au'),(1179,'Lucy','Lambert','llambert@mymail.unisa.edu.au'),(1180,'Elijah','Marshall','emarshall@mymail.unisa.edu.au'),(1181,'Scarlett','Norris','snorris@mymail.unisa.edu.au'),(1182,'Gabriel','Osborne','gosborne@mymail.unisa.edu.au'),(1183,'Layla','Palmer','lpalmer@mymail.unisa.edu.au'),(1184,'Ryan','Payne','rpayne@mymail.unisa.edu.au'),(1185,'Sienna','Roberts','sroberts@mymail.unisa.edu.au'),(1186,'Mason','Saunders','msaunders@mymail.unisa.edu.au'),(1187,'Abigail','Turner','aturner@mymail.unisa.edu.au'),(1188,'Blake','Vance','bvance@mymail.unisa.edu.au'),(1189,'Eva','Walker','ewalker@mymail.unisa.edu.au'),(1190,'Jayden','Xavier','jxavier@mymail.unisa.edu.au'),(1191,'Ruby','York','ryork@mymail.unisa.edu.au'),(1192,'Caleb','Zane','czane@mymail.unisa.edu.au'),(1193,'Madeline','Abbott','mabbott@mymail.unisa.edu.au'),(1194,'test','test','test@mail.com'),(1195,'test','smith','tsmith@gmail.com'),(1196,'Mary','Jane','mjane@mymail.unisa.edu.au'),(1197,'Jamel','Mann','jmann@mymail.unisa.edu.au'),(1198,'Frederic','Wilkinson','fwilkinson@mymail.unisa.edu.au'),(1199,'Lemuel','Decker','ldecker@mymail.unisa.edu.au'),(1200,'Sarah','Duggie','sduggie@mymail.unisa.edu.au'),(1201,'Jane','Hawker','jhawker@mymail.unisa.edu.au'),(1202,'John','Test','john.test@gmail.com'),(1203,'Jill','Test','jill.test@gmail.com'),(1204,'Alice','Nguyen','alice.nguyen@example.com'),(1205,'Benjamin','Clark','ben.clark@example.com'),(1206,'Joe','Bloggs','Joe.bloggs@gmail.com'),(1207,'Michael','Jackson','Michael.Jackson@gmail.com');
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
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

-- Dump completed on 2025-06-04 11:28:35
