-- MySQL dump 10.13  Distrib 8.0.39, for macos14 (arm64)
--
-- Host: localhost    Database: crownele_inventory
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ic_customers`
--

DROP TABLE IF EXISTS `ic_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint unsigned DEFAULT NULL,
  `state` bigint unsigned DEFAULT NULL,
  `city` bigint unsigned DEFAULT NULL,
  `zipcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_address` text COLLATE utf8mb4_unicode_ci,
  `billing_same` tinyint(1) NOT NULL DEFAULT '0',
  `b_first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_country` bigint unsigned DEFAULT NULL,
  `b_state` bigint unsigned DEFAULT NULL,
  `b_city` bigint unsigned DEFAULT NULL,
  `b_zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_short_address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `opening_balance` decimal(16,2) NOT NULL DEFAULT '0.00',
  `total_wallet_amount` decimal(20,3) NOT NULL,
  `is_verified` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'verified',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  PRIMARY KEY (`id`),
  KEY `ic_customers_country_foreign` (`country`),
  KEY `ic_customers_state_foreign` (`state`),
  KEY `ic_customers_city_foreign` (`city`),
  KEY `ic_customers_b_country_foreign` (`b_country`),
  KEY `ic_customers_b_state_foreign` (`b_state`),
  KEY `ic_customers_b_city_foreign` (`b_city`),
  KEY `ic_customers_created_by_foreign` (`created_by`),
  KEY `ic_customers_updated_by_foreign` (`updated_by`),
  KEY `ic_customers_customer_id_foreign` (`customer_id`),
  KEY `ic_customers_company_id_foreign` (`company_id`),
  CONSTRAINT `ic_customers_b_city_foreign` FOREIGN KEY (`b_city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_b_country_foreign` FOREIGN KEY (`b_country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_b_state_foreign` FOREIGN KEY (`b_state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `ic_companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_customers_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_customers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_customers`
--

LOCK TABLES `ic_customers` WRITE;
/*!40000 ALTER TABLE `ic_customers` DISABLE KEYS */;
INSERT INTO `ic_customers` VALUES (1,NULL,'Test','Customer','testcustomer@gmail.com',NULL,NULL,'+8801745468682','ডি. কে এন্টারপ্রাইজ',NULL,'সোনালী আইস ক্রিম এর মোড়, বরিশাল',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,'+1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',-1232.00,0.000,'verified',NULL,3,NULL,'2026-06-04 03:11:08','2026-06-04 03:11:34',NULL,'customer');
/*!40000 ALTER TABLE `ic_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_suppliers`
--

DROP TABLE IF EXISTS `ic_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint unsigned DEFAULT NULL,
  `state` bigint unsigned DEFAULT NULL,
  `city` bigint unsigned DEFAULT NULL,
  `zipcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `opening_balance` decimal(16,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_suppliers_country_foreign` (`country`),
  KEY `ic_suppliers_state_foreign` (`state`),
  KEY `ic_suppliers_city_foreign` (`city`),
  KEY `ic_suppliers_created_by_foreign` (`created_by`),
  KEY `ic_suppliers_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_suppliers_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_suppliers_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_suppliers_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_suppliers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_suppliers`
--

LOCK TABLES `ic_suppliers` WRITE;
/*!40000 ALTER TABLE `ic_suppliers` DISABLE KEYS */;
INSERT INTO `ic_suppliers` VALUES (1,'Juran','Wholesaler','asdasdasd@app.com','+1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'active',0.00,3,NULL,'2026-06-02 05:16:46','2026-06-02 05:16:46');
/*!40000 ALTER TABLE `ic_suppliers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-09 10:47:38
