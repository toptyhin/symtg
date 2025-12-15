CREATE DATABASE IF NOT EXISTS telegram_connector;
GRANT ALL PRIVILEGES ON telegram_connector.* TO 'symfony'@'%';

/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.0.2-MariaDB, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: telegram_connector
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `doctrine_migration_versions` VALUES
('DoctrineMigrations\\Version20251214115951','2025-12-14 12:00:40',402),
('DoctrineMigrations\\Version20251214170131','2025-12-14 17:02:35',22);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F52993984D16C4DD` (`shop_id`),
  CONSTRAINT `FK_F52993984D16C4DD` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `order` VALUES
(1,'121sd',9880.00,'alk','2025-12-14 14:56:12',1),
(2,'121sd',9880.00,'alk','2025-12-14 14:57:45',1),
(3,'st001',9880.00,'фдл','2025-12-14 15:03:04',1),
(4,'ak34',9880.00,'alk','2025-12-14 15:54:26',1),
(5,'ak34',9880.00,'alk','2025-12-14 15:54:56',1),
(6,'ak44',9880.00,'alk','2025-12-14 15:57:17',1),
(7,'ak4411',9880.00,'alk','2025-12-14 15:57:41',1),
(8,'ak4411',9880.11,'alk','2025-12-14 15:58:53',1),
(9,'UOO9',11234.11,'ROBO','2025-12-14 16:00:34',1),
(10,'11',28.00,'paul','2025-12-14 19:07:29',1),
(11,'234',12534.00,'buba','2025-12-15 06:18:50',2),
(12,'234',7034.12,'Buba','2025-12-15 06:21:45',2),
(13,'230',7032.14,'Buba22','2025-12-15 06:30:30',2),
(14,'230',7032.14,'Buba22','2025-12-15 06:31:02',2),
(15,'230',7032.14,'Buba22','2025-12-15 06:32:18',2),
(16,'230',7032.14,'Buba22','2025-12-15 06:35:31',2),
(17,'212',7032.14,'Buba22','2025-12-15 06:36:14',2),
(18,'212',7032.14,'Buba KO','2025-12-15 06:39:59',2),
(19,'212',7032.14,'Buba KO','2025-12-15 06:41:13',2),
(20,'212',7032.14,'Buba KO','2025-12-15 06:41:58',2),
(22,'212',7032.14,'Buba KO 2234','2025-12-15 06:44:46',2),
(23,'21200',1000.14,'KOTE','2025-12-15 06:49:21',2),
(24,'21200',1000.14,'KOTE','2025-12-15 06:52:29',2),
(25,'21200',1000.14,'KOTEН','2025-12-15 09:19:23',2);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop`
--

LOCK TABLES `shop` WRITE;
/*!40000 ALTER TABLE `shop` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `shop` VALUES
(1,'Point 1'),
(2,'Point 2'),
(3,'Point 3');
/*!40000 ALTER TABLE `shop` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `telegram_integration`
--

DROP TABLE IF EXISTS `telegram_integration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `telegram_integration` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bot_token` varchar(255) NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `enabled` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_telegram_shop` (`shop_id`),
  CONSTRAINT `FK_4D6BE5084D16C4DD` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telegram_integration`
--

LOCK TABLES `telegram_integration` WRITE;
/*!40000 ALTER TABLE `telegram_integration` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `telegram_integration` VALUES
(1,'465456646654','stringlkdfjlkjdflkjdf',1,'2025-12-14 19:46:00','2025-12-15 08:47:47',1),
(2,'7244070539','8692',1,'2025-12-14 19:46:47','2025-12-15 08:48:00',2),
(3,'3232323','80980809809855555',0,'2025-12-15 08:33:54','2025-12-15 08:37:56',3);
/*!40000 ALTER TABLE `telegram_integration` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `telegram_send_log`
--

DROP TABLE IF EXISTS `telegram_send_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `telegram_send_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` longtext NOT NULL,
  `status` varchar(255) NOT NULL,
  `error` varchar(255) NOT NULL,
  `sent_at` datetime NOT NULL,
  `shop_id` int NOT NULL,
  `order_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_77AA2A944D16C4DD` (`shop_id`),
  KEY `IDX_77AA2A948D9F6D38` (`order_id`),
  CONSTRAINT `FK_77AA2A944D16C4DD` FOREIGN KEY (`shop_id`) REFERENCES `shop` (`id`),
  CONSTRAINT `FK_77AA2A948D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telegram_send_log`
--

LOCK TABLES `telegram_send_log` WRITE;
/*!40000 ALTER TABLE `telegram_send_log` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `telegram_send_log` VALUES
(1,'Новый заказ: № 21200, на сумму 1000.14 рублей. Клиент: KOTE','SENT','','2025-12-15 06:52:29',2,24),
(2,'Новый заказ: № 21200, на сумму 1000.14 рублей. Клиент: KOTEН','SENT','','2025-12-15 09:19:23',2,25);
/*!40000 ALTER TABLE `telegram_send_log` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-12-15 12:49:49
