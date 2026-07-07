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
-- Table structure for table `ic_accounts`
--

DROP TABLE IF EXISTS `ic_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('cash','bank','mobile_banking') COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_accounts_created_by_foreign` (`created_by`),
  KEY `ic_accounts_updated_by_foreign` (`updated_by`),
  KEY `ic_accounts_type_is_active_index` (`type`,`is_active`),
  KEY `ic_accounts_name_index` (`name`),
  CONSTRAINT `ic_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_accounts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_accounts`
--

LOCK TABLES `ic_accounts` WRITE;
/*!40000 ALTER TABLE `ic_accounts` DISABLE KEYS */;
INSERT INTO `ic_accounts` VALUES (3,'Main Cash Account','cash','CASH-001',NULL,NULL,47750.00,1,3,3,'2026-05-22 07:46:44','2026-05-30 17:38:20'),(4,'Business Bank Account','bank','2100-0012345678','Dutch-Bangla Bank','Motijheel Branch',195035.00,1,3,3,'2026-05-22 07:46:44','2026-05-26 04:07:18');
/*!40000 ALTER TABLE `ic_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_addons`
--

DROP TABLE IF EXISTS `ic_addons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_addons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_addons_name_unique` (`name`),
  KEY `ic_addons_created_by_foreign` (`created_by`),
  KEY `ic_addons_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_addons_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_addons_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_addons`
--

LOCK TABLES `ic_addons` WRITE;
/*!40000 ALTER TABLE `ic_addons` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_addons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_attribute_item_variation`
--

DROP TABLE IF EXISTS `ic_attribute_item_variation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_attribute_item_variation` (
  `variation_id` bigint unsigned NOT NULL,
  `attribute_item_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`variation_id`,`attribute_item_id`),
  KEY `ic_attribute_item_variation_attribute_item_id_foreign` (`attribute_item_id`),
  CONSTRAINT `ic_attribute_item_variation_attribute_item_id_foreign` FOREIGN KEY (`attribute_item_id`) REFERENCES `ic_attribute_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_attribute_item_variation_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `ic_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_attribute_item_variation`
--

LOCK TABLES `ic_attribute_item_variation` WRITE;
/*!40000 ALTER TABLE `ic_attribute_item_variation` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_attribute_item_variation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_attribute_items`
--

DROP TABLE IF EXISTS `ic_attribute_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_attribute_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_attribute_items_attribute_id_foreign` (`attribute_id`),
  KEY `ic_attribute_items_created_by_foreign` (`created_by`),
  KEY `ic_attribute_items_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_attribute_items_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_attribute_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_attribute_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_attribute_items`
--

LOCK TABLES `ic_attribute_items` WRITE;
/*!40000 ALTER TABLE `ic_attribute_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_attribute_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_attribute_platforms`
--

DROP TABLE IF EXISTS `ic_attribute_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_attribute_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_attribute_platforms_attribute_id_foreign` (`attribute_id`),
  KEY `ic_attribute_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_attribute_platforms_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_attribute_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_attribute_platforms`
--

LOCK TABLES `ic_attribute_platforms` WRITE;
/*!40000 ALTER TABLE `ic_attribute_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_attribute_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_attributes`
--

DROP TABLE IF EXISTS `ic_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_attributes_name_index` (`name`),
  KEY `ic_attributes_created_by_foreign` (`created_by`),
  KEY `ic_attributes_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_attributes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_attributes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_attributes`
--

LOCK TABLES `ic_attributes` WRITE;
/*!40000 ALTER TABLE `ic_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_banks`
--

DROP TABLE IF EXISTS `ic_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_banks_created_by_foreign` (`created_by`),
  KEY `ic_banks_updated_by_foreign` (`updated_by`),
  KEY `ic_banks_name_is_active_index` (`name`,`is_active`),
  CONSTRAINT `ic_banks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_banks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_banks`
--

LOCK TABLES `ic_banks` WRITE;
/*!40000 ALTER TABLE `ic_banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_brands`
--

DROP TABLE IF EXISTS `ic_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_brands_name_index` (`name`),
  KEY `ic_brands_created_by_foreign` (`created_by`),
  KEY `ic_brands_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_brands_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_brands_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_brands`
--

LOCK TABLES `ic_brands` WRITE;
/*!40000 ALTER TABLE `ic_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_capital_payments`
--

DROP TABLE IF EXISTS `ic_capital_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_capital_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `capital_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_capital_payments_capital_id_foreign` (`capital_id`),
  KEY `ic_capital_payments_account_id_foreign` (`account_id`),
  KEY `ic_capital_payments_created_by_foreign` (`created_by`),
  CONSTRAINT `ic_capital_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `ic_accounts` (`id`),
  CONSTRAINT `ic_capital_payments_capital_id_foreign` FOREIGN KEY (`capital_id`) REFERENCES `ic_capitals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_capital_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_capital_payments`
--

LOCK TABLES `ic_capital_payments` WRITE;
/*!40000 ALTER TABLE `ic_capital_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_capital_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_capitals`
--

DROP TABLE IF EXISTS `ic_capitals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_capitals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `capital_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `investor_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `investor_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `investor_address` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `capital_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('active','partially_paid','fully_paid','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_capitals_capital_no_unique` (`capital_no`),
  KEY `ic_capitals_created_by_foreign` (`created_by`),
  KEY `ic_capitals_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_capitals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_capitals_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_capitals`
--

LOCK TABLES `ic_capitals` WRITE;
/*!40000 ALTER TABLE `ic_capitals` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_capitals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_companies`
--

DROP TABLE IF EXISTS `ic_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_companies_name_unique` (`name`),
  KEY `ic_companies_created_by_foreign` (`created_by`),
  KEY `ic_companies_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_companies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_companies_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_companies`
--

LOCK TABLES `ic_companies` WRITE;
/*!40000 ALTER TABLE `ic_companies` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_coupon_products`
--

DROP TABLE IF EXISTS `ic_coupon_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_coupon_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_coupon_products_coupon_id_foreign` (`coupon_id`),
  KEY `ic_coupon_products_product_id_foreign` (`product_id`),
  CONSTRAINT `ic_coupon_products_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `ic_coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_coupon_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_coupon_products`
--

LOCK TABLES `ic_coupon_products` WRITE;
/*!40000 ALTER TABLE `ic_coupon_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_coupon_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_coupons`
--

DROP TABLE IF EXISTS `ic_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner` text COLLATE utf8mb4_unicode_ci,
  `minimum_shopping` int DEFAULT '0',
  `maximum_discount` double DEFAULT NULL,
  `discount_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_coupons_created_by_foreign` (`created_by`),
  KEY `ic_coupons_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_coupons_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_coupons_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_coupons`
--

LOCK TABLES `ic_coupons` WRITE;
/*!40000 ALTER TABLE `ic_coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_customer_platforms`
--

DROP TABLE IF EXISTS `ic_customer_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_customer_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_customer_platforms_customer_id_platform_id_unique` (`customer_id`,`platform_id`),
  KEY `ic_customer_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_customer_platforms_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_customer_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_customer_platforms`
--

LOCK TABLES `ic_customer_platforms` WRITE;
/*!40000 ALTER TABLE `ic_customer_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_customer_platforms` ENABLE KEYS */;
UNLOCK TABLES;

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
/*!40000 ALTER TABLE `ic_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_draft_invoice_items`
--

DROP TABLE IF EXISTS `ic_draft_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_draft_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `draft_invoice_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `tax` int NOT NULL DEFAULT '0',
  `discount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_draft_invoice_items_draft_invoice_id_index` (`draft_invoice_id`),
  KEY `ic_draft_invoice_items_product_id_foreign` (`product_id`),
  KEY `ic_draft_invoice_items_created_by_foreign` (`created_by`),
  KEY `ic_draft_invoice_items_updated_by_foreign` (`updated_by`),
  KEY `ic_draft_invoice_items_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_draft_invoice_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_draft_invoice_items_draft_invoice_id_foreign` FOREIGN KEY (`draft_invoice_id`) REFERENCES `ic_draft_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_draft_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_draft_invoice_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_draft_invoice_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_draft_invoice_items`
--

LOCK TABLES `ic_draft_invoice_items` WRITE;
/*!40000 ALTER TABLE `ic_draft_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_draft_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_draft_invoices`
--

DROP TABLE IF EXISTS `ic_draft_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_draft_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `customer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `billing_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `shipping_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `bank_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `items_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tax_amount` decimal(14,2) DEFAULT NULL,
  `discount_amount` decimal(14,2) DEFAULT NULL,
  `global_discount` decimal(14,2) DEFAULT '0.00',
  `global_discount_type` varchar(191) DEFAULT NULL,
  `total` decimal(14,2) DEFAULT NULL,
  `total_paid` decimal(14,2) DEFAULT NULL,
  `last_paid` decimal(14,2) NOT NULL DEFAULT '0.00',
  `payment_type` varchar(50) DEFAULT NULL,
  `notes` text,
  `status` varchar(20) DEFAULT NULL,
  `invoice_created_from` varchar(191) DEFAULT 'admin',
  `token` varchar(191) DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_draft_invoices_warehouse_id_foreign` (`warehouse_id`),
  KEY `ic_draft_invoices_customer_id_foreign` (`customer_id`),
  KEY `ic_draft_invoices_created_by_foreign` (`created_by`),
  KEY `ic_draft_invoices_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_draft_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_draft_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_draft_invoices_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_draft_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_draft_invoices`
--

LOCK TABLES `ic_draft_invoices` WRITE;
/*!40000 ALTER TABLE `ic_draft_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_draft_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_expenses`
--

DROP TABLE IF EXISTS `ic_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(20,3) NOT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expense_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_expenses_title_index` (`title`),
  KEY `ic_expenses_category_id_foreign` (`category_id`),
  KEY `ic_expenses_created_by_foreign` (`created_by`),
  KEY `ic_expenses_updated_by_foreign` (`updated_by`),
  KEY `ic_expenses_expense_by_foreign` (`expense_by`),
  CONSTRAINT `ic_expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ic_expenses_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_expenses_expense_by_foreign` FOREIGN KEY (`expense_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_expenses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_expenses`
--

LOCK TABLES `ic_expenses` WRITE;
/*!40000 ALTER TABLE `ic_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_expenses_categories`
--

DROP TABLE IF EXISTS `ic_expenses_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_expenses_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_expenses_categories_name_index` (`name`),
  KEY `ic_expenses_categories_created_by_foreign` (`created_by`),
  KEY `ic_expenses_categories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_expenses_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_expenses_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_expenses_categories`
--

LOCK TABLES `ic_expenses_categories` WRITE;
/*!40000 ALTER TABLE `ic_expenses_categories` DISABLE KEYS */;
INSERT INTO `ic_expenses_categories` VALUES (1,'Office Supplies','Stationery, printing, and general office supplies','active',3,3,'2026-05-22 07:46:44','2026-05-22 07:46:44');
/*!40000 ALTER TABLE `ic_expenses_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_expenses_files`
--

DROP TABLE IF EXISTS `ic_expenses_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_expenses_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expenses_id` bigint unsigned DEFAULT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_expenses_files_expenses_id_foreign` (`expenses_id`),
  CONSTRAINT `ic_expenses_files_expenses_id_foreign` FOREIGN KEY (`expenses_id`) REFERENCES `ic_expenses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_expenses_files`
--

LOCK TABLES `ic_expenses_files` WRITE;
/*!40000 ALTER TABLE `ic_expenses_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_expenses_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_expenses_items`
--

DROP TABLE IF EXISTS `ic_expenses_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_expenses_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expenses_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_qty` mediumint NOT NULL DEFAULT '1',
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_expenses_items_expenses_id_foreign` (`expenses_id`),
  CONSTRAINT `ic_expenses_items_expenses_id_foreign` FOREIGN KEY (`expenses_id`) REFERENCES `ic_expenses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_expenses_items`
--

LOCK TABLES `ic_expenses_items` WRITE;
/*!40000 ALTER TABLE `ic_expenses_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_expenses_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_failed_jobs`
--

DROP TABLE IF EXISTS `ic_failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_failed_jobs`
--

LOCK TABLES `ic_failed_jobs` WRITE;
/*!40000 ALTER TABLE `ic_failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_invoice_items`
--

DROP TABLE IF EXISTS `ic_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `variation_id` bigint unsigned DEFAULT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `tax` int NOT NULL DEFAULT '0',
  `discount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_invoice_items_invoice_id_index` (`invoice_id`),
  KEY `ic_invoice_items_product_id_foreign` (`product_id`),
  KEY `ic_invoice_items_created_by_foreign` (`created_by`),
  KEY `ic_invoice_items_updated_by_foreign` (`updated_by`),
  KEY `ic_invoice_items_product_stock_id_foreign` (`product_stock_id`),
  KEY `ic_invoice_items_variation_id_foreign` (`variation_id`),
  CONSTRAINT `ic_invoice_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_invoice_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoice_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoice_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `ic_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_invoice_items`
--

LOCK TABLES `ic_invoice_items` WRITE;
/*!40000 ALTER TABLE `ic_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_invoice_payments`
--

DROP TABLE IF EXISTS `ic_invoice_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_invoice_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `date` varchar(191) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `notes` varchar(191) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  KEY `ic_invoice_payments_invoice_id_index` (`invoice_id`),
  KEY `ic_invoice_payments_created_by_foreign` (`created_by`),
  KEY `ic_invoice_payments_updated_by_foreign` (`updated_by`),
  KEY `ic_invoice_payments_customer_id_foreign` (`customer_id`),
  CONSTRAINT `ic_invoice_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoice_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_invoice_payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_invoice_payments`
--

LOCK TABLES `ic_invoice_payments` WRITE;
/*!40000 ALTER TABLE `ic_invoice_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_invoice_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_invoice_platforms`
--

DROP TABLE IF EXISTS `ic_invoice_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_invoice_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_invoice_platforms_invoice_id_foreign` (`invoice_id`),
  KEY `ic_invoice_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_invoice_platforms_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_invoice_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_invoice_platforms`
--

LOCK TABLES `ic_invoice_platforms` WRITE;
/*!40000 ALTER TABLE `ic_invoice_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_invoice_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_invoices`
--

DROP TABLE IF EXISTS `ic_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `customer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `billing_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `shipping_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `items_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `additional_charge_name` varchar(255) DEFAULT NULL,
  `additional_charge_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `tax_amount` decimal(14,2) DEFAULT NULL,
  `discount_amount` decimal(14,2) DEFAULT NULL,
  `global_discount` decimal(14,2) DEFAULT '0.00',
  `global_discount_type` varchar(191) DEFAULT NULL,
  `total` decimal(14,2) DEFAULT NULL,
  `total_paid` decimal(14,2) DEFAULT NULL,
  `last_paid` decimal(14,2) NOT NULL DEFAULT '0.00',
  `payment_type` varchar(50) DEFAULT NULL,
  `notes` text,
  `status` varchar(20) DEFAULT NULL,
  `is_withdrawal` tinyint(1) NOT NULL DEFAULT '0',
  `delivery_status` varchar(191) DEFAULT 'delivered',
  `invoice_created_from` varchar(191) DEFAULT 'admin',
  `delivered_at` timestamp NULL DEFAULT '2026-05-12 11:05:42',
  `canceled_at` timestamp NULL DEFAULT '2026-05-12 11:05:42',
  `token` varchar(191) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_invoices_customer_id_foreign` (`customer_id`),
  KEY `ic_invoices_created_by_foreign` (`created_by`),
  KEY `ic_invoices_updated_by_foreign` (`updated_by`),
  KEY `ic_invoices_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `ic_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoices_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_invoices`
--

LOCK TABLES `ic_invoices` WRITE;
/*!40000 ALTER TABLE `ic_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_jobs`
--

DROP TABLE IF EXISTS `ic_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_jobs`
--

LOCK TABLES `ic_jobs` WRITE;
/*!40000 ALTER TABLE `ic_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_lc_expenses`
--

DROP TABLE IF EXISTS `ic_lc_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_lc_expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lc_id` bigint unsigned NOT NULL,
  `expense_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_lc_expenses_lc_id_index` (`lc_id`),
  CONSTRAINT `ic_lc_expenses_lc_id_foreign` FOREIGN KEY (`lc_id`) REFERENCES `ic_lcs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_lc_expenses`
--

LOCK TABLES `ic_lc_expenses` WRITE;
/*!40000 ALTER TABLE `ic_lc_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_lc_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_lcs`
--

DROP TABLE IF EXISTS `ic_lcs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_lcs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dollar_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `usd_rate` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `lc_amount_bdt` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_expense` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `per_dollar_cost` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_lcs_created_by_foreign` (`created_by`),
  KEY `ic_lcs_updated_by_foreign` (`updated_by`),
  KEY `ic_lcs_name_index` (`name`),
  CONSTRAINT `ic_lcs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_lcs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_lcs`
--

LOCK TABLES `ic_lcs` WRITE;
/*!40000 ALTER TABLE `ic_lcs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_lcs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_loan_payments`
--

DROP TABLE IF EXISTS `ic_loan_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_loan_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_loan_payments_loan_id_foreign` (`loan_id`),
  KEY `ic_loan_payments_account_id_foreign` (`account_id`),
  KEY `ic_loan_payments_created_by_foreign` (`created_by`),
  CONSTRAINT `ic_loan_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `ic_accounts` (`id`),
  CONSTRAINT `ic_loan_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `ic_loans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_loan_payments`
--

LOCK TABLES `ic_loan_payments` WRITE;
/*!40000 ALTER TABLE `ic_loan_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_loan_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_loans`
--

DROP TABLE IF EXISTS `ic_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `loan_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `borrower_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `borrower_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `borrower_address` text COLLATE utf8mb4_unicode_ci,
  `loan_type` enum('given','taken') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'taken' COMMENT 'given=আমরা দিয়েছি, taken=আমরা নিয়েছি',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `loan_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('active','partially_paid','fully_paid','written_off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_loans_loan_no_unique` (`loan_no`),
  KEY `ic_loans_created_by_foreign` (`created_by`),
  KEY `ic_loans_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_loans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_loans_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_loans`
--

LOCK TABLES `ic_loans` WRITE;
/*!40000 ALTER TABLE `ic_loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_manufacturers`
--

DROP TABLE IF EXISTS `ic_manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_manufacturers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_manufacturers_name_index` (`name`),
  KEY `ic_manufacturers_created_by_foreign` (`created_by`),
  KEY `ic_manufacturers_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_manufacturers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_manufacturers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_manufacturers`
--

LOCK TABLES `ic_manufacturers` WRITE;
/*!40000 ALTER TABLE `ic_manufacturers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_manufacturers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_measurement_units`
--

DROP TABLE IF EXISTS `ic_measurement_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_measurement_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_measurement_units_name_index` (`name`),
  KEY `ic_measurement_units_created_by_foreign` (`created_by`),
  KEY `ic_measurement_units_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_measurement_units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_measurement_units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_measurement_units`
--

LOCK TABLES `ic_measurement_units` WRITE;
/*!40000 ALTER TABLE `ic_measurement_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_measurement_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_migrations`
--

DROP TABLE IF EXISTS `ic_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_migrations`
--

LOCK TABLES `ic_migrations` WRITE;
/*!40000 ALTER TABLE `ic_migrations` DISABLE KEYS */;
INSERT INTO `ic_migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2020_12_31_055154_create_permission_tables',1),(5,'2021_07_17_050049_create_system_countries_table',1),(6,'2021_07_18_050700_create_system_states_table',1),(7,'2021_07_19_050948_create_system_cities_table',1),(8,'2021_08_17_045916_create_warehouses_table',1),(9,'2021_08_18_085126_create_brands_table',1),(10,'2021_08_19_043411_create_manufacturers_table',1),(11,'2021_08_19_054121_create_weight_units_table',1),(12,'2021_08_19_071558_create_measurement_units_table',1),(13,'2021_08_19_092718_create_product_categories_table',1),(14,'2021_08_26_043158_create_attributes_table',1),(15,'2021_08_26_055628_create_attribute_items_table',1),(16,'2021_08_30_051232_create_products_table',1),(17,'2021_08_30_095212_create_product_attributes_table',1),(18,'2021_08_31_103032_create_product_stocks_table',1),(19,'2021_09_02_041005_create_customers_table',1),(20,'2021_09_02_084554_create_suppliers_table',1),(21,'2021_09_02_094612_create_expenses_categories_table',1),(22,'2021_09_12_044901_create_expenses_table',1),(23,'2021_09_12_054539_create_expenses_items_table',1),(24,'2021_09_12_055040_create_expenses_files_table',1),(25,'2021_09_12_084843_create_purchases_table',1),(26,'2021_09_12_085621_create_purchase_items_table',1),(27,'2021_09_12_095850_create_purchase_returns_table',1),(28,'2021_09_12_095915_create_purchase_return_items_table',1),(29,'2021_09_14_115607_create_purchase_receives_table',1),(30,'2021_09_14_115611_create_purchase_item_receives_table',1),(31,'2021_10_28_104330_add_tax_to_product',1),(32,'2021_11_01_054626_create_invoices_table',1),(33,'2021_11_01_104452_create_invoice_items_table',1),(34,'2021_11_01_104531_create_invoice_payments_table',1),(35,'2021_11_04_103443_create_sale_returns_table',1),(36,'2021_11_04_112115_create_sale_return_items_table',1),(37,'2021_11_07_052114_add_stock_column_to_products',1),(38,'2021_11_09_053542_create_system_settings_table',1),(39,'2021_11_10_103702_add_bank_to_invoice',1),(40,'2021_11_10_103814_add_bank_to_invoice_payment',1),(41,'2022_04_10_063011_add_short_address_column_to_purchases',1),(42,'2022_04_10_073127_add_short_address_to_customers',1),(43,'2022_04_10_081049_add_short_address_to_suppliers',1),(44,'2022_04_12_060629_add_expense_by_to_expenses',1),(45,'2022_04_12_075552_add_split_sale_to_products',1),(46,'2022_06_16_094219_change_total_rage_to_purchases',1),(47,'2022_06_16_094450_change_total_rage_to_purchase_items',1),(48,'2022_06_16_094907_change_total_rage_to_purchase_receives',1),(49,'2022_06_16_095244_change_total_rage_to_purchase_item_receives',1),(50,'2022_06_26_105012_add_warehouse_id_to_invoices',1),(51,'2022_06_30_065842_add_alert_quantity_to_products',1),(52,'2022_07_26_045153_change_total_limit_to_invoices',1),(53,'2022_07_26_050002_change_decimal_limit_invoice_items',1),(54,'2022_07_26_050421_change_decimal_limit_invoice_payments',1),(55,'2022_07_31_104035_change_date_type_to_invoices',1),(56,'2022_09_18_051147_add_position_to_product_categories_table',1),(57,'2022_09_21_083000_add_password_to_customers_table',1),(58,'2022_09_21_101736_create_coupons_table',1),(59,'2022_09_22_061954_alter_table_products_change_some_column_type',1),(60,'2022_09_22_075729_create_coupon_products_table',1),(61,'2022_10_18_101603_add_customer_price_to_products_table',1),(62,'2022_10_19_051713_create_sale_return_requests_table',1),(63,'2022_10_19_052408_create_sale_return_item_requests_table',1),(64,'2022_10_20_112627_add_column_to_expense_items_table',1),(65,'2022_10_20_112627_add_column_to_invoices_table',1),(66,'2022_10_24_064859_create_draft_invoices_table',1),(67,'2022_10_24_064927_create_draft_invoice_items_table',1),(68,'2022_11_13_105243_update_decimal_to_sale_return_items_table',1),(69,'2022_11_13_105744_update_decimal_to_sale_returns_table',1),(70,'2022_11_13_110110_update_decimal_to_purchase_returns_table',1),(71,'2022_11_13_111102_update_decimal_to_purchase_return_items_table',1),(72,'2022_11_13_111338_update_decimal_to_purchase_items_table',1),(73,'2022_11_29_064452_add_warehouse_id_to_sale_return_requests_table',1),(74,'2022_12_11_143715_add_attribute_wise_price_to_product_stocks_table',1),(75,'2024_04_01_000001_create_banks_table',1),(76,'2024_04_01_100001_create_accounts_table',1),(77,'2024_04_01_100002_create_transactions_table',1),(78,'2024_08_28_092444_create_user_warehouses_table',1),(79,'2024_08_28_110424_add_customer_id_and_type_to_customers_table',1),(80,'2024_08_29_150107_create_product_stock_histories_table',1),(81,'2024_09_30_222646_create_notifications_table',1),(82,'2024_10_22_031427_add_total_wallet_amount_to_customers_table',1),(83,'2024_10_22_031854_create_user_wallet_histories_table',1),(84,'2024_12_11_133517_create_addons_table',1),(85,'2024_12_15_132554_create_platforms_table',1),(86,'2024_12_15_135957_create_product_platforms_table',1),(87,'2024_12_15_144810_create_customer_platforms_table',1),(88,'2024_12_15_145154_create_attribute_platforms_table',1),(89,'2024_12_15_145844_create_invoice_platforms_table',1),(90,'2024_12_18_090043_create_product_category_platforms_table',1),(91,'2025_02_13_091027_update_decimal_precision',1),(92,'2025_04_01_000000_create_variations_table',1),(93,'2025_04_02_000001_add_variation_id_to_invoice_items_table',1),(94,'2025_05_07_112847_add_is_batch_product_to_products_table',1),(95,'2025_05_14_104607_add_batch_and_expiry_date_to_product_stocks_table',1),(96,'2025_05_14_160222_add_batch_to_invoice_items_table',1),(97,'2025_06_25_150512_create_companies_table',1),(98,'2025_06_25_180015_add_company_id_to_customers_table',1),(99,'2025_06_26_110840_add_is_withdrawal_to_invoices_table',1),(100,'2025_07_08_164953_create_ic_stock_transaction_history_view',1),(101,'2025_07_23_154612_add_supplier_invoice_number_to_purchase_receives_table',1),(102,'2025_08_30_144349_create_jobs_table',1),(103,'2025_09_08_225259_create_progress_tracking_table',1),(104,'2025_09_09_110209_add_backorders_allowed_to_product_stocks_table',1),(105,'2025_09_11_111516_create_attribute_item_variation_table',1),(106,'2025_09_11_111725_add_variation_id_to_product_stocks_table',1),(107,'2025_09_17_062738_create_variation_platforms_table',1),(108,'2025_09_24_182304_add_variation_id_to_purchase_items_table',1),(109,'2026_03_29_154454_add_parts_no_to_products_table',1),(110,'2026_03_29_173454_create_purchase_payments_table',1),(111,'2026_03_29_180057_add_additional_charge_to_invoices_table',1),(112,'2026_04_09_000001_create_loans_table',1),(113,'2026_04_09_000002_create_loan_payments_table',1),(114,'2026_04_09_000003_create_capitals_table',1),(115,'2026_04_09_000004_create_capital_payments_table',1),(116,'2026_04_09_115335_add_account_id_to_purchase_payments_table',1),(117,'2026_04_13_112410_add_opening_balance_to_customers_and_suppliers_table',1),(118,'2026_04_26_150028_alter_invoice_payments_table_for_opening_balance',1),(119,'2026_05_14_144804_add_bank_details_to_accounts_table',2),(120,'2026_05_14_154033_create_lcs_table',3),(121,'2026_05_19_000001_drop_priority_from_warehouses_table',4),(122,'2026_05_22_131103_create_product_relations_table',5),(123,'2026_05_22_184545_create_product_cartons_table',6),(124,'2026_05_22_190728_add_is_free_to_invoice_items_and_stock_histories',7),(125,'2026_05_22_202729_extend_notes_column_in_invoices_table',8),(126,'2026_05_22_205439_create_warehouse_transfers_table',9),(127,'2026_05_22_223830_add_damage_lost_qty_to_sale_return_items_table',10),(128,'2026_06_06_000000_make_last_name_and_email_nullable_on_customers_and_suppliers',11);
/*!40000 ALTER TABLE `ic_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_model_has_permissions`
--

DROP TABLE IF EXISTS `ic_model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `ic_model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `ic_permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_model_has_permissions`
--

LOCK TABLES `ic_model_has_permissions` WRITE;
/*!40000 ALTER TABLE `ic_model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_model_has_roles`
--

DROP TABLE IF EXISTS `ic_model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `ic_model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `ic_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_model_has_roles`
--

LOCK TABLES `ic_model_has_roles` WRITE;
/*!40000 ALTER TABLE `ic_model_has_roles` DISABLE KEYS */;
INSERT INTO `ic_model_has_roles` VALUES (1,'App\\Models\\User',3);
/*!40000 ALTER TABLE `ic_model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_notifications`
--

DROP TABLE IF EXISTS `ic_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `ic_notifications_created_by_foreign` (`created_by`),
  KEY `ic_notifications_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_notifications_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_notifications`
--

LOCK TABLES `ic_notifications` WRITE;
/*!40000 ALTER TABLE `ic_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_password_resets`
--

DROP TABLE IF EXISTS `ic_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `ic_password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_password_resets`
--

LOCK TABLES `ic_password_resets` WRITE;
/*!40000 ALTER TABLE `ic_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_permissions`
--

DROP TABLE IF EXISTS `ic_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_permissions`
--

LOCK TABLES `ic_permissions` WRITE;
/*!40000 ALTER TABLE `ic_permissions` DISABLE KEYS */;
INSERT INTO `ic_permissions` VALUES (1,NULL,'Dashboard','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(2,1,'Total Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(3,1,'Total Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(4,1,'Total Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(5,1,'Total Sale','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(6,1,'Total Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(7,1,'Total Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(8,1,'Total Sale Amount','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(9,1,'Total purchase Amount','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(10,1,'Total Expenses Amount','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(11,1,'Total Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(12,1,'Total Sale Return Request','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(13,1,'Total Pending Sale Return Request','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(14,1,'Total Stock','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(15,1,'Total Invoice By Auth User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(16,1,'Total Sale By Auth User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(17,1,'Total Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(18,1,'Active Coupons','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(19,1,'Total Sale Return','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(20,1,'Sale Report Charts','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(21,1,'Top Products','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(22,1,'Best Items','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(23,1,'Latest Sales','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(24,NULL,'User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(25,24,'Add User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(26,24,'Edit User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(27,24,'Show User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(28,24,'List User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(29,24,'Delete User','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(30,NULL,'Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(31,30,'Add Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(32,30,'Edit Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(33,30,'Show Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(34,30,'List Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(35,30,'Delete Role','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(36,NULL,'Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(37,36,'Add Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(38,36,'Edit Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(39,36,'Stock Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(40,36,'List Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(41,36,'Delete Product','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(42,36,'Stock Out Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(43,NULL,'Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(44,43,'Add Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(45,43,'Edit Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(46,43,'Show Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(47,43,'List Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(48,43,'Delete Warehouse','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(49,NULL,'Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(50,49,'Add Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(51,49,'Edit Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(52,49,'List Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(53,49,'Delete Product Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(54,NULL,'Brand','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(55,54,'Add Brand','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(56,54,'Edit Brand','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(57,54,'List Brand','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(58,54,'Delete Brand','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(59,NULL,'Manufacturer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(60,59,'Add Manufacturer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(61,59,'Edit Manufacturer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(62,59,'List Manufacturer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(63,59,'Delete Manufacturer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(64,NULL,'Weight Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(65,64,'Add Weight Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(66,64,'Edit Weight Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(67,64,'List Weight Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(68,64,'Delete Weight Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(69,NULL,'Measurement Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(70,69,'Add Measurement Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(71,69,'Edit Measurement Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(72,69,'List Measurement Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(73,69,'Delete Measurement Unit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(74,NULL,'Attribute','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(75,74,'Add Attribute','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(76,74,'Edit Attribute','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(77,74,'List Attribute','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(78,74,'Delete Attribute','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(79,NULL,'Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(80,79,'Add Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(81,79,'Edit Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(82,79,'Show Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(83,79,'List Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(84,79,'Cancel Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(85,79,'Receive Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(86,79,'Confirm Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(87,79,'Return Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(88,79,'Delete Purchase','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(89,79,'Purchase Receive List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(90,79,'Purchase Return List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(91,79,'Add Purchase Payment','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(92,79,'View Purchase Payment','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(93,79,'Delete Purchase Payment','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(94,NULL,'Coupon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(95,94,'Add Coupon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(96,94,'Edit Coupon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(97,94,'List Coupon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(98,94,'Delete Coupon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(99,NULL,'Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(100,99,'Add Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(101,99,'Edit Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(102,99,'List Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(103,99,'Delete Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(104,99,'Verify Customer','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(105,NULL,'Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(106,105,'Add Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(107,105,'Edit Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(108,105,'List Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(109,105,'Delete Supplier','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(110,NULL,'Expenses Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(111,110,'Add Expenses Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(112,110,'Edit Expenses Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(113,110,'List Expenses Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(114,110,'Delete Expenses Category','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(115,NULL,'Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(116,115,'Add Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(117,115,'Edit Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(118,115,'Show Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(119,115,'List Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(120,115,'Delete Expenses','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(121,NULL,'Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(122,121,'List Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(123,121,'Add Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(124,121,'Edit Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(125,121,'Show Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(126,121,'Return Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(127,121,'View Payment Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(128,121,'Make Payment Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(129,121,'Download Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(130,121,'Send Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(131,121,'Link Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(132,121,'Delete Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(133,NULL,'Draft Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(134,133,'List Draft Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(135,133,'Add Draft Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(136,133,'Show Draft Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(137,133,'Delete Draft Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(138,133,'Convert Draft To Invoice','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(139,NULL,'Sale Return','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(140,139,'Show Sale Return','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(141,139,'Return Sale Return','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(142,139,'Sale Return List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(143,139,'Sale Return Request List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(144,NULL,'Reports','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(145,144,'Expenses Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(146,144,'Expired Products Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(147,144,'Sales Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(148,144,'Purchases Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(149,144,'Payments Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(150,144,'Warehouse Selling Price Report','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(151,NULL,'Settings','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(152,151,'Site Settings','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(153,NULL,'Account','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(154,153,'Account List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(155,153,'Add Account','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(156,153,'Edit Account','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(157,153,'Show Account','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(158,153,'Delete Account','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(159,153,'Account Statement','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(160,NULL,'Transaction','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(161,160,'Transaction List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(162,160,'Add Transaction','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(163,160,'Show Transaction','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(164,160,'Transfer Balance','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(165,NULL,'Loan','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(166,165,'Loan List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(167,165,'Loan Create','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(168,165,'Loan Edit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(169,165,'Loan Delete','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(170,165,'Loan Payment','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(171,165,'Loan Payment Delete','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(172,165,'Loan Transaction History','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(173,NULL,'Capital','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(174,173,'Capital List','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(175,173,'Capital Create','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(176,173,'Capital Edit','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(177,173,'Capital Delete','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(178,173,'Capital Payment','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(179,173,'Capital Payment Delete','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(180,173,'Capital Transaction History','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(181,NULL,'Addon','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(182,181,'List Addon','web','2026-05-12 11:05:46','2026-05-12 11:05:46');
/*!40000 ALTER TABLE `ic_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_platforms`
--

DROP TABLE IF EXISTS `ic_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_webhook_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_connected` tinyint(1) NOT NULL DEFAULT '1',
  `consumer_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consumer_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_platforms_type_store_name_unique` (`type`,`store_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_platforms`
--

LOCK TABLES `ic_platforms` WRITE;
/*!40000 ALTER TABLE `ic_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_attributes`
--

DROP TABLE IF EXISTS `ic_product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned DEFAULT NULL,
  `attribute_id` bigint unsigned DEFAULT NULL,
  `attribute_item_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_product_attributes_product_id_foreign` (`product_id`),
  KEY `ic_product_attributes_attribute_id_foreign` (`attribute_id`),
  KEY `ic_product_attributes_attribute_item_id_foreign` (`attribute_item_id`),
  CONSTRAINT `ic_product_attributes_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_attributes_attribute_item_id_foreign` FOREIGN KEY (`attribute_item_id`) REFERENCES `ic_attribute_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_attributes`
--

LOCK TABLES `ic_product_attributes` WRITE;
/*!40000 ALTER TABLE `ic_product_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_cartons`
--

DROP TABLE IF EXISTS `ic_product_cartons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_cartons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `carton_product_id` bigint unsigned NOT NULL,
  `qty_per_carton` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_product_cartons_product_id_unique` (`product_id`),
  KEY `ic_product_cartons_carton_product_id_foreign` (`carton_product_id`),
  CONSTRAINT `ic_product_cartons_carton_product_id_foreign` FOREIGN KEY (`carton_product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_product_cartons_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_cartons`
--

LOCK TABLES `ic_product_cartons` WRITE;
/*!40000 ALTER TABLE `ic_product_cartons` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_cartons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_categories`
--

DROP TABLE IF EXISTS `ic_product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `parent_id` bigint unsigned DEFAULT NULL,
  `position` int DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_product_categories_name_index` (`name`),
  KEY `ic_product_categories_parent_id_foreign` (`parent_id`),
  KEY `ic_product_categories_created_by_foreign` (`created_by`),
  KEY `ic_product_categories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_product_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ic_product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_categories`
--

LOCK TABLES `ic_product_categories` WRITE;
/*!40000 ALTER TABLE `ic_product_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_category_platforms`
--

DROP TABLE IF EXISTS `ic_product_category_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_category_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_category_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_product_category_platforms_product_category_id_foreign` (`product_category_id`),
  KEY `ic_product_category_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_product_category_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_category_platforms_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `ic_product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_category_platforms`
--

LOCK TABLES `ic_product_category_platforms` WRITE;
/*!40000 ALTER TABLE `ic_product_category_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_category_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_platforms`
--

DROP TABLE IF EXISTS `ic_product_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_product_platforms_product_id_foreign` (`product_id`),
  KEY `ic_product_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_product_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_platforms_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_platforms`
--

LOCK TABLES `ic_product_platforms` WRITE;
/*!40000 ALTER TABLE `ic_product_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_relations`
--

DROP TABLE IF EXISTS `ic_product_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_product_id` bigint unsigned NOT NULL,
  `related_product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_product_relations_parent_product_id_related_product_id_unique` (`parent_product_id`,`related_product_id`),
  KEY `ic_product_relations_related_product_id_foreign` (`related_product_id`),
  CONSTRAINT `ic_product_relations_parent_product_id_foreign` FOREIGN KEY (`parent_product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_product_relations_related_product_id_foreign` FOREIGN KEY (`related_product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_relations`
--

LOCK TABLES `ic_product_relations` WRITE;
/*!40000 ALTER TABLE `ic_product_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_stock_histories`
--

DROP TABLE IF EXISTS `ic_product_stock_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_stock_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `from_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'in, out, transfer',
  `action_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT '0',
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_product_stock_histories_product_stock_id_foreign` (`product_stock_id`),
  KEY `ic_product_stock_histories_warehouse_id_foreign` (`warehouse_id`),
  KEY `ic_product_stock_histories_product_id_foreign` (`product_id`),
  KEY `ic_product_stock_histories_from_type_from_id_index` (`from_type`,`from_id`),
  KEY `ic_product_stock_histories_created_by_foreign` (`created_by`),
  KEY `ic_product_stock_histories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_product_stock_histories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_stock_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_product_stock_histories_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_stock_histories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_stock_histories_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_stock_histories`
--

LOCK TABLES `ic_product_stock_histories` WRITE;
/*!40000 ALTER TABLE `ic_product_stock_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_stock_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_product_stocks`
--

DROP TABLE IF EXISTS `ic_product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_product_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quantity` int NOT NULL DEFAULT '0',
  `backorders_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `manage_stock` tinyint(1) NOT NULL DEFAULT '0',
  `product_id` bigint unsigned DEFAULT NULL,
  `variation_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `batch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `attribute_id` bigint unsigned DEFAULT NULL,
  `attribute_item_id` bigint unsigned DEFAULT NULL,
  `price` double DEFAULT NULL,
  `customer_buying_price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_warehouse_batch` (`product_id`,`warehouse_id`,`batch`),
  KEY `ic_product_stocks_warehouse_id_foreign` (`warehouse_id`),
  KEY `ic_product_stocks_attribute_id_foreign` (`attribute_id`),
  KEY `ic_product_stocks_attribute_item_id_foreign` (`attribute_item_id`),
  KEY `ic_product_stocks_variation_id_foreign` (`variation_id`),
  KEY `ic_product_stocks_product_id_variation_id_index` (`product_id`,`variation_id`),
  CONSTRAINT `ic_product_stocks_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_stocks_attribute_item_id_foreign` FOREIGN KEY (`attribute_item_id`) REFERENCES `ic_attribute_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_product_stocks_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `ic_variations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_product_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_product_stocks`
--

LOCK TABLES `ic_product_stocks` WRITE;
/*!40000 ALTER TABLE `ic_product_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_product_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_products`
--

DROP TABLE IF EXISTS `ic_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parts_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_buying_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `dimension_l` float DEFAULT NULL,
  `dimension_w` float DEFAULT NULL,
  `dimension_d` float DEFAULT NULL,
  `thumb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci,
  `is_variant` tinyint(1) NOT NULL DEFAULT '0',
  `is_batch_product` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `available_for` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `category_id` bigint unsigned DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `manufacturer_id` bigint unsigned DEFAULT NULL,
  `weight_unit_id` bigint unsigned DEFAULT NULL,
  `measurement_unit_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'included',
  `custom_tax` float DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `split_sale` tinyint(1) DEFAULT NULL,
  `stock_alert_quantity` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_products_name_index` (`name`),
  KEY `ic_products_sku_index` (`sku`),
  KEY `ic_products_barcode_index` (`barcode`),
  KEY `ic_products_category_id_foreign` (`category_id`),
  KEY `ic_products_brand_id_foreign` (`brand_id`),
  KEY `ic_products_manufacturer_id_foreign` (`manufacturer_id`),
  KEY `ic_products_weight_unit_id_foreign` (`weight_unit_id`),
  KEY `ic_products_measurement_unit_id_foreign` (`measurement_unit_id`),
  KEY `ic_products_created_by_foreign` (`created_by`),
  KEY `ic_products_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `ic_brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ic_product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `ic_manufacturers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_measurement_unit_id_foreign` FOREIGN KEY (`measurement_unit_id`) REFERENCES `ic_measurement_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_products_weight_unit_id_foreign` FOREIGN KEY (`weight_unit_id`) REFERENCES `ic_weight_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_products`
--

LOCK TABLES `ic_products` WRITE;
/*!40000 ALTER TABLE `ic_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_progress_tracking`
--

DROP TABLE IF EXISTS `ic_progress_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_progress_tracking` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `total` int NOT NULL DEFAULT '0',
  `processed` int NOT NULL DEFAULT '0',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_progress_tracking`
--

LOCK TABLES `ic_progress_tracking` WRITE;
/*!40000 ALTER TABLE `ic_progress_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_progress_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_item_receives`
--

DROP TABLE IF EXISTS `ic_purchase_item_receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_item_receives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_receive_id` bigint unsigned NOT NULL,
  `purchase_item_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_item_receives_purchase_receive_id_foreign` (`purchase_receive_id`),
  KEY `ic_purchase_item_receives_purchase_item_id_foreign` (`purchase_item_id`),
  KEY `ic_purchase_item_receives_product_id_foreign` (`product_id`),
  KEY `ic_purchase_item_receives_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_purchase_item_receives_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_purchase_item_receives_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_item_receives_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `ic_purchase_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_purchase_item_receives_purchase_receive_id_foreign` FOREIGN KEY (`purchase_receive_id`) REFERENCES `ic_purchase_receives` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_item_receives`
--

LOCK TABLES `ic_purchase_item_receives` WRITE;
/*!40000 ALTER TABLE `ic_purchase_item_receives` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_item_receives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_items`
--

DROP TABLE IF EXISTS `ic_purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `variation_id` bigint unsigned DEFAULT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` float NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` float NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `ic_purchase_items_product_id_foreign` (`product_id`),
  KEY `ic_purchase_items_created_by_foreign` (`created_by`),
  KEY `ic_purchase_items_updated_by_foreign` (`updated_by`),
  KEY `ic_purchase_items_product_stock_id_foreign` (`product_stock_id`),
  KEY `ic_purchase_items_variation_id_foreign` (`variation_id`),
  CONSTRAINT `ic_purchase_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_purchase_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  CONSTRAINT `ic_purchase_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `ic_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_items`
--

LOCK TABLES `ic_purchase_items` WRITE;
/*!40000 ALTER TABLE `ic_purchase_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_payments`
--

DROP TABLE IF EXISTS `ic_purchase_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `payment_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_payments_purchase_id_foreign` (`purchase_id`),
  KEY `ic_purchase_payments_created_by_foreign` (`created_by`),
  KEY `ic_purchase_payments_account_id_foreign` (`account_id`),
  CONSTRAINT `ic_purchase_payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `ic_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_purchase_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_payments`
--

LOCK TABLES `ic_purchase_payments` WRITE;
/*!40000 ALTER TABLE `ic_purchase_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_receives`
--

DROP TABLE IF EXISTS `ic_purchase_receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_receives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `supplier_invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receive_date` date NOT NULL,
  `total` decimal(14,2) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_receives_purchase_id_foreign` (`purchase_id`),
  KEY `ic_purchase_receives_created_by_foreign` (`created_by`),
  KEY `ic_purchase_receives_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_purchase_receives_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_receives_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  CONSTRAINT `ic_purchase_receives_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_receives`
--

LOCK TABLES `ic_purchase_receives` WRITE;
/*!40000 ALTER TABLE `ic_purchase_receives` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_receives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_return_items`
--

DROP TABLE IF EXISTS `ic_purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `purchase_item_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(20,3) NOT NULL,
  `sub_total` decimal(20,3) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `ic_purchase_return_items_purchase_item_id_foreign` (`purchase_item_id`),
  KEY `ic_purchase_return_items_product_id_foreign` (`product_id`),
  KEY `ic_purchase_return_items_created_by_foreign` (`created_by`),
  KEY `ic_purchase_return_items_updated_by_foreign` (`updated_by`),
  KEY `ic_purchase_return_items_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_purchase_return_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_purchase_return_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_return_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `ic_purchase_items` (`id`),
  CONSTRAINT `ic_purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `ic_purchase_returns` (`id`),
  CONSTRAINT `ic_purchase_return_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_return_items`
--

LOCK TABLES `ic_purchase_return_items` WRITE;
/*!40000 ALTER TABLE `ic_purchase_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchase_returns`
--

DROP TABLE IF EXISTS `ic_purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(20,3) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_purchase_returns_purchase_id_foreign` (`purchase_id`),
  KEY `ic_purchase_returns_created_by_foreign` (`created_by`),
  KEY `ic_purchase_returns_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_purchase_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  CONSTRAINT `ic_purchase_returns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchase_returns`
--

LOCK TABLES `ic_purchase_returns` WRITE;
/*!40000 ALTER TABLE `ic_purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_purchases`
--

DROP TABLE IF EXISTS `ic_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(14,2) NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint unsigned DEFAULT NULL,
  `state` bigint unsigned DEFAULT NULL,
  `city` bigint unsigned DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `received` tinyint(1) DEFAULT NULL,
  `cancel_date` date DEFAULT NULL,
  `cancel_by` bigint unsigned DEFAULT NULL,
  `cancel_note` text COLLATE utf8mb4_unicode_ci,
  `short_address` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_purchases_purchase_number_unique` (`purchase_number`),
  KEY `ic_purchases_supplier_id_foreign` (`supplier_id`),
  KEY `ic_purchases_warehouse_id_foreign` (`warehouse_id`),
  KEY `ic_purchases_country_foreign` (`country`),
  KEY `ic_purchases_state_foreign` (`state`),
  KEY `ic_purchases_city_foreign` (`city`),
  KEY `ic_purchases_created_by_foreign` (`created_by`),
  KEY `ic_purchases_updated_by_foreign` (`updated_by`),
  KEY `ic_purchases_cancel_by_foreign` (`cancel_by`),
  CONSTRAINT `ic_purchases_cancel_by_foreign` FOREIGN KEY (`cancel_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `ic_suppliers` (`id`),
  CONSTRAINT `ic_purchases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_purchases_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_purchases`
--

LOCK TABLES `ic_purchases` WRITE;
/*!40000 ALTER TABLE `ic_purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_role_has_permissions`
--

DROP TABLE IF EXISTS `ic_role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `ic_role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `ic_role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `ic_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `ic_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_role_has_permissions`
--

LOCK TABLES `ic_role_has_permissions` WRITE;
/*!40000 ALTER TABLE `ic_role_has_permissions` DISABLE KEYS */;
INSERT INTO `ic_role_has_permissions` VALUES (1,1),(2,1),(3,1),(7,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(86,1),(87,1),(88,1),(89,1),(90,1),(91,1),(92,1),(93,1),(94,1),(95,1),(96,1),(97,1),(98,1),(99,1),(100,1),(101,1),(102,1),(103,1),(104,1),(105,1),(106,1),(107,1),(108,1),(109,1),(110,1),(111,1),(112,1),(113,1),(114,1),(115,1),(116,1),(117,1),(118,1),(119,1),(120,1),(121,1),(122,1),(123,1),(124,1),(125,1),(126,1),(127,1),(128,1),(129,1),(130,1),(131,1),(132,1),(133,1),(134,1),(135,1),(136,1),(137,1),(138,1),(139,1),(140,1),(141,1),(142,1),(143,1),(144,1),(145,1),(146,1),(147,1),(148,1),(149,1),(150,1),(151,1),(152,1),(153,1),(154,1),(155,1),(156,1),(157,1),(158,1),(159,1),(160,1),(161,1),(162,1),(163,1),(164,1),(165,1),(166,1),(167,1),(168,1),(169,1),(170,1),(171,1),(172,1),(173,1),(174,1),(175,1),(176,1),(177,1),(178,1),(179,1),(180,1),(181,1),(182,1),(1,2),(2,2),(3,2),(7,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,2),(17,2),(18,2),(19,2),(20,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,2),(27,2),(28,2),(29,2),(30,2),(31,2),(32,2),(33,2),(34,2),(35,2),(36,2),(37,2),(38,2),(39,2),(40,2),(41,2),(42,2),(43,2),(44,2),(45,2),(46,2),(47,2),(48,2),(49,2),(50,2),(51,2),(52,2),(53,2),(54,2),(55,2),(56,2),(57,2),(58,2),(59,2),(60,2),(61,2),(62,2),(63,2),(64,2),(65,2),(66,2),(67,2),(68,2),(69,2),(70,2),(71,2),(72,2),(73,2),(74,2),(75,2),(76,2),(77,2),(78,2),(79,2),(80,2),(81,2),(82,2),(83,2),(84,2),(85,2),(86,2),(87,2),(88,2),(89,2),(90,2),(91,2),(92,2),(93,2),(94,2),(95,2),(96,2),(97,2),(98,2),(99,2),(100,2),(101,2),(102,2),(103,2),(104,2),(105,2),(106,2),(107,2),(108,2),(109,2),(110,2),(111,2),(112,2),(113,2),(114,2),(115,2),(116,2),(117,2),(118,2),(119,2),(120,2),(121,2),(122,2),(123,2),(124,2),(125,2),(126,2),(127,2),(128,2),(129,2),(130,2),(131,2),(132,2),(133,2),(134,2),(135,2),(136,2),(137,2),(138,2),(139,2),(140,2),(141,2),(142,2),(143,2),(144,2),(145,2),(146,2),(147,2),(148,2),(149,2),(150,2),(151,2),(152,2),(153,2),(154,2),(155,2),(156,2),(157,2),(158,2),(159,2),(160,2),(161,2),(162,2),(163,2),(164,2),(165,2),(166,2),(167,2),(168,2),(169,2),(170,2),(171,2),(172,2),(173,2),(174,2),(175,2),(176,2),(177,2),(178,2),(179,2),(180,2),(181,2),(182,2);
/*!40000 ALTER TABLE `ic_role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_roles`
--

DROP TABLE IF EXISTS `ic_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_roles`
--

LOCK TABLES `ic_roles` WRITE;
/*!40000 ALTER TABLE `ic_roles` DISABLE KEYS */;
INSERT INTO `ic_roles` VALUES (1,'Super Admin','web','2026-05-12 11:05:46','2026-05-12 11:05:46'),(2,'Manager','web','2026-05-12 11:05:46','2026-05-12 11:05:46');
/*!40000 ALTER TABLE `ic_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_sale_return_item_requests`
--

DROP TABLE IF EXISTS `ic_sale_return_item_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_sale_return_item_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_return_request_id` bigint unsigned NOT NULL,
  `invoice_item_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_qty` int NOT NULL,
  `return_price` double NOT NULL,
  `return_sub_total` double NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_sale_return_item_requests_sale_return_request_id_foreign` (`sale_return_request_id`),
  KEY `ic_sale_return_item_requests_invoice_item_id_foreign` (`invoice_item_id`),
  KEY `ic_sale_return_item_requests_product_id_foreign` (`product_id`),
  KEY `ic_sale_return_item_requests_created_by_foreign` (`created_by`),
  KEY `ic_sale_return_item_requests_updated_by_foreign` (`updated_by`),
  KEY `ic_sale_return_item_requests_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_sale_return_item_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_item_requests_invoice_item_id_foreign` FOREIGN KEY (`invoice_item_id`) REFERENCES `ic_invoice_items` (`id`),
  CONSTRAINT `ic_sale_return_item_requests_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_sale_return_item_requests_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_item_requests_sale_return_request_id_foreign` FOREIGN KEY (`sale_return_request_id`) REFERENCES `ic_sale_return_requests` (`id`),
  CONSTRAINT `ic_sale_return_item_requests_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_sale_return_item_requests`
--

LOCK TABLES `ic_sale_return_item_requests` WRITE;
/*!40000 ALTER TABLE `ic_sale_return_item_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_sale_return_item_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_sale_return_items`
--

DROP TABLE IF EXISTS `ic_sale_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_sale_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_return_id` bigint unsigned NOT NULL,
  `invoice_item_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_qty` int NOT NULL,
  `damage_qty` int unsigned NOT NULL DEFAULT '0',
  `lost_qty` int unsigned NOT NULL DEFAULT '0',
  `return_price` decimal(20,3) NOT NULL,
  `return_sub_total` decimal(20,3) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  KEY `ic_sale_return_items_invoice_item_id_foreign` (`invoice_item_id`),
  KEY `ic_sale_return_items_product_id_foreign` (`product_id`),
  KEY `ic_sale_return_items_created_by_foreign` (`created_by`),
  KEY `ic_sale_return_items_updated_by_foreign` (`updated_by`),
  KEY `ic_sale_return_items_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_sale_return_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_items_invoice_item_id_foreign` FOREIGN KEY (`invoice_item_id`) REFERENCES `ic_invoice_items` (`id`),
  CONSTRAINT `ic_sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  CONSTRAINT `ic_sale_return_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `ic_sale_returns` (`id`),
  CONSTRAINT `ic_sale_return_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_sale_return_items`
--

LOCK TABLES `ic_sale_return_items` WRITE;
/*!40000 ALTER TABLE `ic_sale_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_sale_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_sale_return_requests`
--

DROP TABLE IF EXISTS `ic_sale_return_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_sale_return_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `return_note` text COLLATE utf8mb4_unicode_ci,
  `return_total_amount` double NOT NULL,
  `items_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint unsigned DEFAULT NULL,
  `status_updated_by` bigint unsigned DEFAULT NULL,
  `status_updated_at` timestamp NULL DEFAULT '2026-05-12 11:05:42',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_sale_return_requests_invoice_id_foreign` (`invoice_id`),
  KEY `ic_sale_return_requests_requested_by_foreign` (`requested_by`),
  KEY `ic_sale_return_requests_status_updated_by_foreign` (`status_updated_by`),
  KEY `ic_sale_return_requests_created_by_foreign` (`created_by`),
  KEY `ic_sale_return_requests_updated_by_foreign` (`updated_by`),
  KEY `ic_sale_return_requests_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `ic_sale_return_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_requests_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`),
  CONSTRAINT `ic_sale_return_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_requests_status_updated_by_foreign` FOREIGN KEY (`status_updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_requests_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_return_requests_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_sale_return_requests`
--

LOCK TABLES `ic_sale_return_requests` WRITE;
/*!40000 ALTER TABLE `ic_sale_return_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_sale_return_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_sale_returns`
--

DROP TABLE IF EXISTS `ic_sale_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_sale_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `return_note` text COLLATE utf8mb4_unicode_ci,
  `return_total_amount` decimal(20,3) NOT NULL,
  `items_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_sale_returns_invoice_id_foreign` (`invoice_id`),
  KEY `ic_sale_returns_created_by_foreign` (`created_by`),
  KEY `ic_sale_returns_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_sale_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_sale_returns_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`),
  CONSTRAINT `ic_sale_returns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_sale_returns`
--

LOCK TABLES `ic_sale_returns` WRITE;
/*!40000 ALTER TABLE `ic_sale_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_sale_returns` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `ic_suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_system_cities`
--

DROP TABLE IF EXISTS `ic_system_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_system_cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_system_cities_created_by_foreign` (`created_by`),
  KEY `ic_system_cities_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_system_cities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_system_cities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_system_cities`
--

LOCK TABLES `ic_system_cities` WRITE;
/*!40000 ALTER TABLE `ic_system_cities` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_system_cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_system_countries`
--

DROP TABLE IF EXISTS `ic_system_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_system_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shortname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_system_countries_created_by_foreign` (`created_by`),
  KEY `ic_system_countries_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_system_countries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_system_countries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_system_countries`
--

LOCK TABLES `ic_system_countries` WRITE;
/*!40000 ALTER TABLE `ic_system_countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_system_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_system_settings`
--

DROP TABLE IF EXISTS `ic_system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `settings_key` varchar(191) NOT NULL,
  `settings_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_system_settings`
--

LOCK TABLES `ic_system_settings` WRITE;
/*!40000 ALTER TABLE `ic_system_settings` DISABLE KEYS */;
INSERT INTO `ic_system_settings` VALUES (1,'purchase_info','{\"domain\": \"https://itclan-inventory-alpha.test\", \"install_at\": \"2026-05-12 17:05:46\", \"purchase_code\": \"25055423-8ab6-4663-8039-f6617ea869ea\"}','2026-05-12 11:05:46','2026-05-12 11:05:46'),(2,'general','{\"site_title\":\"Bulb Inventory\",\"timezone\":\"Asia\\/Dhaka\",\"primary_color\":\"#28aaa9\",\"secondary_color\":\"#2b2d5d\",\"currency_symbol\":\"\\u09f3\",\"currency_exchange_rate\":\"1\",\"currency_exchange_from\":\"BDT\",\"default_tax\":null,\"currency_convert_form_api\":\"no\",\"default_language\":\"en\",\"is_logo_show_in_invoice\":\"yes\",\"store_name\":\"Business Global\",\"brand_slogan\":\"Honesty is our power\",\"store_address\":\"Pallabi, Dhaka, Bangladesh.\",\"store_mobile\":null,\"store_website\":null,\"invoice_footer\":null,\"tin\":null,\"terms_and_conditions\":null,\"login_message_system\":null,\"favicon\":\"17809817767170.png\",\"site_logo\":\"17809817681675.png\"}','2026-05-22 08:25:08','2026-06-09 05:09:36'),(3,'paypal','{\"paypal.baseUrl\":null,\"paypal.clientId\":null,\"paypal.secret\":null}','2026-05-22 14:00:22','2026-05-25 13:45:40'),(4,'stripe','{\"stripe.public_key\": null, \"stripe.secret_key\": null}','2026-05-22 14:00:22','2026-05-22 14:00:22'),(5,'mail','{\"mail.mailers.smtp.host\":null,\"mail.mailers.smtp.port\":null,\"mail.mailers.smtp.encryption\":null,\"mail.mailers.smtp.username\":null,\"mail.mailers.smtp.password\":null,\"mail.from.address\":null,\"mail.from.name\":null}','2026-05-22 14:00:22','2026-05-25 13:45:40'),(6,'product_setting','{\"sku.auto\":\"yes\",\"sku.editable\":\"yes\",\"sku.prefix\":null,\"sku.suffix\":null}','2026-05-22 14:00:22','2026-05-25 13:45:40'),(7,'api_key','[]','2026-05-22 14:00:22','2026-05-25 13:15:46'),(8,'pusher','{\"app_id\": null, \"app_key\": null, \"app_secret\": null, \"app_cluster\": null}','2026-05-22 14:00:22','2026-05-22 14:00:22'),(9,'formating','{\"decimal_separator\":null,\"no_of_decimals\":null}','2026-05-22 14:00:22','2026-05-25 14:06:29');
/*!40000 ALTER TABLE `ic_system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_system_states`
--

DROP TABLE IF EXISTS `ic_system_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_system_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_system_states_created_by_foreign` (`created_by`),
  KEY `ic_system_states_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_system_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_system_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_system_states`
--

LOCK TABLES `ic_system_states` WRITE;
/*!40000 ALTER TABLE `ic_system_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_system_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_transactions`
--

DROP TABLE IF EXISTS `ic_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint unsigned NOT NULL,
  `type` enum('add','reduce','transfer_in','transfer_out','invoice_payment','due_collection','opening_balance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `from_account_id` bigint unsigned DEFAULT NULL,
  `to_account_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reference_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_transactions_from_account_id_foreign` (`from_account_id`),
  KEY `ic_transactions_to_account_id_foreign` (`to_account_id`),
  KEY `ic_transactions_created_by_foreign` (`created_by`),
  KEY `ic_transactions_account_id_type_index` (`account_id`,`type`),
  KEY `ic_transactions_reference_id_reference_type_index` (`reference_id`,`reference_type`),
  KEY `ic_transactions_created_at_index` (`created_at`),
  CONSTRAINT `ic_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `ic_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_transactions_from_account_id_foreign` FOREIGN KEY (`from_account_id`) REFERENCES `ic_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_transactions_to_account_id_foreign` FOREIGN KEY (`to_account_id`) REFERENCES `ic_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_transactions`
--

LOCK TABLES `ic_transactions` WRITE;
/*!40000 ALTER TABLE `ic_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_user_wallet_histories`
--

DROP TABLE IF EXISTS `ic_user_wallet_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_user_wallet_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `from_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'invoice, credit, debit',
  `amount` decimal(15,2) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_user_wallet_histories_customer_id_foreign` (`customer_id`),
  KEY `ic_user_wallet_histories_from_type_from_id_index` (`from_type`,`from_id`),
  KEY `ic_user_wallet_histories_created_by_foreign` (`created_by`),
  KEY `ic_user_wallet_histories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_user_wallet_histories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_user_wallet_histories_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_user_wallet_histories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_user_wallet_histories`
--

LOCK TABLES `ic_user_wallet_histories` WRITE;
/*!40000 ALTER TABLE `ic_user_wallet_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_user_wallet_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_user_warehouses`
--

DROP TABLE IF EXISTS `ic_user_warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_user_warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_user_warehouses_user_id_foreign` (`user_id`),
  KEY `ic_user_warehouses_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `ic_user_warehouses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `ic_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_user_warehouses_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_user_warehouses`
--

LOCK TABLES `ic_user_warehouses` WRITE;
/*!40000 ALTER TABLE `ic_user_warehouses` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_user_warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_users`
--

DROP TABLE IF EXISTS `ic_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_users_email_unique` (`email`),
  KEY `ic_users_name_index` (`name`),
  KEY `ic_users_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_users`
--

LOCK TABLES `ic_users` WRITE;
/*!40000 ALTER TABLE `ic_users` DISABLE KEYS */;
INSERT INTO `ic_users` VALUES (3,'Admin','admin@inventory.com','+1',NULL,'$2y$10$LWJ7adIj6vNxoj01NePntu1A28/e9rdayzisPPeXcYPom0AjOUh2W','17809805402763.png','active',NULL,'2026-05-16 09:00:35','2026-06-09 04:49:00');
/*!40000 ALTER TABLE `ic_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_variation_platforms`
--

DROP TABLE IF EXISTS `ic_variation_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_variation_platforms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `variation_id` bigint unsigned NOT NULL,
  `platform_id` bigint unsigned DEFAULT NULL,
  `platform_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ecommerce_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_variation_platforms_variation_id_foreign` (`variation_id`),
  KEY `ic_variation_platforms_platform_id_foreign` (`platform_id`),
  CONSTRAINT `ic_variation_platforms_platform_id_foreign` FOREIGN KEY (`platform_id`) REFERENCES `ic_platforms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_variation_platforms_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `ic_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_variation_platforms`
--

LOCK TABLES `ic_variation_platforms` WRITE;
/*!40000 ALTER TABLE `ic_variation_platforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_variation_platforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_variations`
--

DROP TABLE IF EXISTS `ic_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_variations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `customer_buying_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regular_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci,
  `thumb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int DEFAULT NULL,
  `stock_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manage_stock` tinyint(1) DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `weight_unit_id` bigint unsigned DEFAULT NULL,
  `dimension_l` int DEFAULT NULL,
  `dimension_w` int DEFAULT NULL,
  `dimension_d` int DEFAULT NULL,
  `measurement_unit_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_variations_product_id_foreign` (`product_id`),
  KEY `ic_variations_sku_index` (`sku`),
  KEY `ic_variations_weight_unit_id_foreign` (`weight_unit_id`),
  KEY `ic_variations_measurement_unit_id_foreign` (`measurement_unit_id`),
  CONSTRAINT `ic_variations_measurement_unit_id_foreign` FOREIGN KEY (`measurement_unit_id`) REFERENCES `ic_measurement_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_variations_weight_unit_id_foreign` FOREIGN KEY (`weight_unit_id`) REFERENCES `ic_weight_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_variations`
--

LOCK TABLES `ic_variations` WRITE;
/*!40000 ALTER TABLE `ic_variations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_warehouse_transfer_items`
--

DROP TABLE IF EXISTS `ic_warehouse_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_warehouse_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_transfer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_stock_id` bigint unsigned DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_warehouse_transfer_items_warehouse_transfer_id_foreign` (`warehouse_transfer_id`),
  KEY `ic_warehouse_transfer_items_product_id_foreign` (`product_id`),
  KEY `ic_warehouse_transfer_items_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `ic_warehouse_transfer_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_warehouse_transfer_items_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `ic_product_stocks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_warehouse_transfer_items_warehouse_transfer_id_foreign` FOREIGN KEY (`warehouse_transfer_id`) REFERENCES `ic_warehouse_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_warehouse_transfer_items`
--

LOCK TABLES `ic_warehouse_transfer_items` WRITE;
/*!40000 ALTER TABLE `ic_warehouse_transfer_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_warehouse_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_warehouse_transfers`
--

DROP TABLE IF EXISTS `ic_warehouse_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_warehouse_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_warehouse_id` bigint unsigned NOT NULL,
  `to_warehouse_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ic_warehouse_transfers_transfer_number_unique` (`transfer_number`),
  KEY `ic_warehouse_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `ic_warehouse_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `ic_warehouse_transfers_created_by_foreign` (`created_by`),
  CONSTRAINT `ic_warehouse_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_warehouse_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ic_warehouse_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_warehouse_transfers`
--

LOCK TABLES `ic_warehouse_transfers` WRITE;
/*!40000 ALTER TABLE `ic_warehouse_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_warehouse_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_warehouses`
--

DROP TABLE IF EXISTS `ic_warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_warehouses_name_index` (`name`),
  KEY `ic_warehouses_created_by_foreign` (`created_by`),
  KEY `ic_warehouses_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_warehouses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_warehouses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_warehouses`
--

LOCK TABLES `ic_warehouses` WRITE;
/*!40000 ALTER TABLE `ic_warehouses` DISABLE KEYS */;
INSERT INTO `ic_warehouses` VALUES (4,'Main Warehouse','main@bulbinventory.test','01700000001','Bulb Inventory Co.','123 Warehouse Street','Industrial Area',NULL,NULL,1,'active',3,3,'2026-05-22 07:46:44','2026-05-25 09:07:59');
/*!40000 ALTER TABLE `ic_warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ic_weight_units`
--

DROP TABLE IF EXISTS `ic_weight_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ic_weight_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ic_weight_units_name_index` (`name`),
  KEY `ic_weight_units_created_by_foreign` (`created_by`),
  KEY `ic_weight_units_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ic_weight_units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ic_weight_units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ic_weight_units`
--

LOCK TABLES `ic_weight_units` WRITE;
/*!40000 ALTER TABLE `ic_weight_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `ic_weight_units` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-09 12:05:36
