-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: motocity
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `motorbikes`
--

DROP TABLE IF EXISTS `motorbikes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motorbikes` (
  `code` varchar(50) NOT NULL,
  `rentingLocation` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `costPerHour` decimal(10,2) NOT NULL CHECK (`costPerHour` > 0),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`code`),
  KEY `idx_location` (`rentingLocation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Data for table `motorbikes`
--

LOCK TABLES `motorbikes` WRITE;
/*!40000 ALTER TABLE `motorbikes` DISABLE KEYS */;
INSERT INTO `motorbikes` VALUES ('MB001','SIM Campus','Honda CBR500R - Sport bike, 500cc, Red color',20.00,'2026-02-10 08:49:49'),('MB002','Changi Airport Terminal 3','Yamaha MT-07 - Naked bike, 689cc, Blue color, comfortable for long rides',18.00,'2026-02-10 08:49:49'),('MB003','Marina Bay Sands','Kawasaki Ninja 400 - Sport bike, 399cc, Green color, perfect for beginners',12.00,'2026-02-10 08:49:49'),('MB004','East Coast Park','Suzuki V-Strom 650 - Adventure bike, 645cc, White color, great for touring',20.00,'2026-02-10 08:49:49'),('MB005','Clementi Mall','Honda PCX 150 - Scooter, 150cc, Black color, fuel efficient and easy to ride',8.00,'2026-02-10 08:49:49'),('MB006','Raffles Place MRT','Ducati Monster 821 - Naked bike, 821cc, Red color, high performance',25.00,'2026-02-10 08:49:49'),('MB007','VivoCity Shopping Centre','BMW G 310 R - Naked bike, 313cc, White color, premium quality',16.00,'2026-02-10 08:49:49'),('MB008','Jurong East MRT','KTM Duke 390 - Naked bike, 373cc, Orange color, sporty and agile',14.00,'2026-02-10 08:49:49'),('MB009','Novena','testing',25.00,'2026-02-18 14:55:36');
/*!40000 ALTER TABLE `motorbikes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rentals` (
  `rentalId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `motorbikeCode` varchar(50) NOT NULL,
  `startDateTime` datetime NOT NULL,
  `endDateTime` datetime DEFAULT NULL,
  `costPerHourAtStart` decimal(10,2) NOT NULL,
  `status` enum('ACTIVE','COMPLETED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rentalId`),
  KEY `idx_user` (`userId`),
  KEY `idx_motorbike` (`motorbikeCode`),
  KEY `idx_status` (`status`),
  KEY `idx_start_date` (`startDateTime`),
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`motorbikeCode`) REFERENCES `motorbikes` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
INSERT INTO `rentals` VALUES (1,2,'MB001','2024-01-15 09:00:00','2024-01-15 14:00:00',15.00,'COMPLETED','2026-02-10 08:49:49'),(2,3,'MB002','2024-01-16 10:00:00','2024-01-16 16:00:00',18.00,'COMPLETED','2026-02-10 08:49:49'),(3,2,'MB003','2024-01-17 08:00:00','2024-01-17 12:00:00',12.00,'COMPLETED','2026-02-10 08:49:49'),(4,4,'MB005','2024-01-18 11:00:00','2024-01-18 15:00:00',8.00,'COMPLETED','2026-02-10 08:49:49'),(5,5,'MB004','2024-01-19 07:00:00','2024-01-19 19:00:00',20.00,'COMPLETED','2026-02-10 08:49:49'),(8,2,'MB001','2026-02-15 07:58:31','2026-02-15 07:58:58',15.00,'COMPLETED','2026-02-15 06:58:31'),(9,2,'MB007','2026-02-15 08:01:55','2026-02-15 08:03:52',16.00,'COMPLETED','2026-02-15 07:01:55'),(10,2,'MB002','2026-02-15 08:35:12','2026-02-15 08:35:34',18.00,'COMPLETED','2026-02-15 07:35:12'),(11,2,'MB001','2026-02-15 06:39:53',NULL,15.00,'ACTIVE','2026-02-15 07:39:53'),(12,3,'MB002','2026-02-15 05:09:53',NULL,18.00,'ACTIVE','2026-02-15 07:39:53'),(13,4,'MB005','2026-02-15 07:09:53',NULL,8.00,'ACTIVE','2026-02-15 07:39:53'),(14,5,'MB006','2026-02-15 03:39:53',NULL,10.00,'ACTIVE','2026-02-15 07:39:53'),(15,2,'MB003','2026-02-15 08:48:57','2026-02-15 08:48:57',12.00,'COMPLETED','2026-02-15 07:48:57'),(16,2,'MB004','2026-02-18 09:09:01','2026-02-18 09:18:30',20.00,'COMPLETED','2026-02-18 08:09:01'),(17,6,'MB007','2026-02-18 16:00:48',NULL,16.00,'ACTIVE','2026-02-18 15:00:48');
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `type` enum('Administrator','User') NOT NULL DEFAULT 'User',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','Tan','+6591234567','admin@motocity.com','Administrator','$2y$12$lxaKr3F7GpHKp/dM.ykBOu5MzZTJvxb7Z6RyozgrUsANNglpHNf/C','2026-02-10 08:49:49'),(2,'Wei Ming','Lim','+6591234568','weiming.lim@example.com','User','$2y$12$lxaKr3F7GpHKp/dM.ykBOu5MzZTJvxb7Z6RyozgrUsANNglpHNf/C','2026-02-10 08:49:49'),(3,'Siti','Rahman','+6591234569','siti.rahman@example.com','User','$2y$12$lxaKr3F7GpHKp/dM.ykBOu5MzZTJvxb7Z6RyozgrUsANNglpHNf/C','2026-02-10 08:49:49'),(4,'Raj','Kumar','+6591234570','raj.kumar@example.com','User','$2y$12$lxaKr3F7GpHKp/dM.ykBOu5MzZTJvxb7Z6RyozgrUsANNglpHNf/C','2026-02-10 08:49:49'),(5,'Mei Ling','Wong','+6591234571','meiling.wong@example.com','User','$2y$12$lxaKr3F7GpHKp/dM.ykBOu5MzZTJvxb7Z6RyozgrUsANNglpHNf/C','2026-02-10 08:49:49'),(6,'Chloe','C','+6512345678','test1234@gmail.com','User','$2y$10$CRF/2cOO68tpyunt46q2XOr0BVrpbJqZgkCFdSbbxDxM9jpp70c8K','2026-02-15 06:14:38'),(7,'Test','User','+6512345678','testuser1771141736@test.com','User','$2y$10$bxN9pWqq53xuobC3PHXAf.LxuJftL38Sn38Uuv/qL6.la90F4sNDm','2026-02-15 07:48:56'),(8,'Test','Admin','+6587654321','testadmin1771141736@test.com','Administrator','$2y$10$kDW2aGZR6QHdem2dmkZ8M.X49OSGSN1NZ5hnWMw8gwH9tUrE1laK2','2026-02-15 07:48:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
