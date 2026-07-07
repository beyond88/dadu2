-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 28, 2022 at 08:53 AM
-- Server version: 5.7.33
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clanvent_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `ic_attributes`
--

CREATE TABLE `ic_attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_attribute_items`
--

CREATE TABLE `ic_attribute_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attribute_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_brands`
--

CREATE TABLE `ic_brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_customers`
--

CREATE TABLE `ic_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint(20) UNSIGNED DEFAULT NULL,
  `state` bigint(20) UNSIGNED DEFAULT NULL,
  `city` bigint(20) UNSIGNED DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_address` text COLLATE utf8mb4_unicode_ci,
  `billing_same` tinyint(1) NOT NULL DEFAULT '0',
  `b_first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_country` bigint(20) UNSIGNED DEFAULT NULL,
  `b_state` bigint(20) UNSIGNED DEFAULT NULL,
  `b_city` bigint(20) UNSIGNED DEFAULT NULL,
  `b_zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_short_address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_expenses`
--

CREATE TABLE `ic_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expense_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_expenses_categories`
--

CREATE TABLE `ic_expenses_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_expenses_files`
--

CREATE TABLE `ic_expenses_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expenses_id` bigint(20) UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_expenses_items`
--

CREATE TABLE `ic_expenses_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expenses_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_failed_jobs`
--

CREATE TABLE `ic_failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_invoices`
--

CREATE TABLE `ic_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer` json DEFAULT NULL,
  `billing_info` json DEFAULT NULL,
  `shipping_info` json DEFAULT NULL,
  `items_data` json DEFAULT NULL,
  `tax_amount` decimal(14,2) DEFAULT NULL,
  `discount_amount` decimal(14,2) DEFAULT NULL,
  `global_discount` decimal(14,2) DEFAULT '0.00',
  `global_discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(14,2) DEFAULT NULL,
  `total_paid` decimal(14,2) DEFAULT NULL,
  `last_paid` decimal(14,2) NOT NULL DEFAULT '0.00',
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_info` json DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_invoice_items`
--

CREATE TABLE `ic_invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `tax` int(11) NOT NULL DEFAULT '0',
  `discount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_invoice_payments`
--

CREATE TABLE `ic_invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_info` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_manufacturers`
--

CREATE TABLE `ic_manufacturers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_measurement_units`
--

CREATE TABLE `ic_measurement_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_migrations`
--

CREATE TABLE `ic_migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_migrations`
--

INSERT INTO `ic_migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2020_12_31_055154_create_permission_tables', 1),
(5, '2021_07_17_050049_create_system_countries_table', 1),
(6, '2021_07_18_050700_create_system_states_table', 1),
(7, '2021_07_19_050948_create_system_cities_table', 1),
(8, '2021_08_17_045916_create_warehouses_table', 1),
(9, '2021_08_18_085126_create_brands_table', 1),
(10, '2021_08_19_043411_create_manufacturers_table', 1),
(11, '2021_08_19_054121_create_weight_units_table', 1),
(12, '2021_08_19_071558_create_measurement_units_table', 1),
(13, '2021_08_19_092718_create_product_categories_table', 1),
(14, '2021_08_26_043158_create_attributes_table', 1),
(15, '2021_08_26_055628_create_attribute_items_table', 1),
(16, '2021_08_30_051232_create_products_table', 1),
(17, '2021_08_30_095212_create_product_attributes_table', 1),
(18, '2021_08_31_103032_create_product_stocks_table', 1),
(19, '2021_09_02_041005_create_customers_table', 1),
(20, '2021_09_02_084554_create_suppliers_table', 1),
(21, '2021_09_02_094612_create_expenses_categories_table', 1),
(22, '2021_09_12_044901_create_expenses_table', 1),
(23, '2021_09_12_054539_create_expenses_items_table', 1),
(24, '2021_09_12_055040_create_expenses_files_table', 1),
(25, '2021_09_12_084843_create_purchases_table', 1),
(26, '2021_09_12_085621_create_purchase_items_table', 1),
(27, '2021_09_12_095850_create_purchase_returns_table', 1),
(28, '2021_09_12_095915_create_purchase_return_items_table', 1),
(29, '2021_09_14_115607_create_purchase_receives_table', 1),
(30, '2021_09_14_115611_create_purchase_item_receives_table', 1),
(31, '2021_10_28_104330_add_tax_to_product', 1),
(32, '2021_11_01_054626_create_invoices_table', 1),
(33, '2021_11_01_104452_create_invoice_items_table', 1),
(34, '2021_11_01_104531_create_invoice_payments_table', 1),
(35, '2021_11_04_103443_create_sale_returns_table', 1),
(36, '2021_11_04_112115_create_sale_return_items_table', 1),
(37, '2021_11_07_052114_add_stock_column_to_products', 1),
(38, '2021_11_09_053542_create_system_settings_table', 1),
(39, '2021_11_10_103702_add_bank_to_invoice', 1),
(40, '2021_11_10_103814_add_bank_to_invoice_payment', 1),
(41, '2022_04_10_063011_add_short_address_column_to_purchases', 1),
(42, '2022_04_10_073127_add_short_address_to_customers', 1),
(43, '2022_04_10_081049_add_short_address_to_suppliers', 1),
(44, '2022_04_12_060629_add_expense_by_to_expenses', 1),
(45, '2022_04_12_075552_add_split_sale_to_products', 1),
(46, '2022_06_16_094219_change_total_rage_to_purchases', 1),
(47, '2022_06_16_094450_change_total_rage_to_purchase_items', 1),
(48, '2022_06_16_094907_change_total_rage_to_purchase_receives', 1),
(49, '2022_06_16_095244_change_total_rage_to_purchase_item_receives', 1),
(50, '2022_06_26_105012_add_warehouse_id_to_invoices', 1),
(51, '2022_06_30_065842_add_alert_quantity_to_products', 1),
(52, '2022_07_26_045153_change_total_limit_to_invoices', 1),
(53, '2022_07_26_050002_change_decimal_limit_invoice_items', 1),
(54, '2022_07_26_050421_change_decimal_limit_invoice_payments', 1),
(55, '2022_07_31_104035_change_date_type_to_invoices', 1),
(56, '2022_09_18_051147_add_position_to_product_categories_table', 1),
(57, '2022_09_22_061954_alter_table_products_change_some_column_type', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ic_model_has_permissions`
--

CREATE TABLE `ic_model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_model_has_roles`
--

CREATE TABLE `ic_model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_model_has_roles`
--

INSERT INTO `ic_model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ic_password_resets`
--

CREATE TABLE `ic_password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_permissions`
--

CREATE TABLE `ic_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_permissions`
--

INSERT INTO `ic_permissions` (`id`, `parent_id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Dashboard', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(2, 1, 'Show Dashboard', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(3, NULL, 'User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(4, 3, 'Add User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(5, 3, 'Edit User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(6, 3, 'Show User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(7, 3, 'List User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(8, 3, 'Delete User', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(9, NULL, 'Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(10, 9, 'Add Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(11, 9, 'Edit Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(12, 9, 'Show Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(13, 9, 'List Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(14, 9, 'Delete Role', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(15, NULL, 'Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(16, 15, 'Add Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(17, 15, 'Edit Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(18, 15, 'Stock Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(19, 15, 'List Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(20, 15, 'Delete Product', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(21, NULL, 'Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(22, 21, 'Add Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(23, 21, 'Edit Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(24, 21, 'Show Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(25, 21, 'List Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(26, 21, 'Delete Warehouse', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(27, NULL, 'Product Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(28, 27, 'Add Product Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(29, 27, 'Edit Product Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(30, 27, 'List Product Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(31, 27, 'Delete Product Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(32, NULL, 'Brand', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(33, 32, 'Add Brand', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(34, 32, 'Edit Brand', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(35, 32, 'List Brand', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(36, 32, 'Delete Brand', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(37, NULL, 'Manufacturer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(38, 37, 'Add Manufacturer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(39, 37, 'Edit Manufacturer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(40, 37, 'List Manufacturer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(41, 37, 'Delete Manufacturer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(42, NULL, 'Weight Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(43, 42, 'Add Weight Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(44, 42, 'Edit Weight Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(45, 42, 'List Weight Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(46, 42, 'Delete Weight Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(47, NULL, 'Measurement Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(48, 47, 'Add Measurement Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(49, 47, 'Edit Measurement Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(50, 47, 'List Measurement Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(51, 47, 'Delete Measurement Unit', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(52, NULL, 'Attribute', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(53, 52, 'Add Attribute', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(54, 52, 'Edit Attribute', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(55, 52, 'List Attribute', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(56, 52, 'Delete Attribute', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(57, NULL, 'Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(58, 57, 'Add Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(59, 57, 'Edit Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(60, 57, 'Show Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(61, 57, 'List Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(62, 57, 'Cancel Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(63, 57, 'Receive Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(64, 57, 'Confirm Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(65, 57, 'Return Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(66, 57, 'Delete Purchase', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(67, 57, 'Purchase Receive List', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(68, 57, 'Purchase Return List', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(69, NULL, 'Customer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(70, 69, 'Add Customer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(71, 69, 'Edit Customer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(72, 69, 'List Customer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(73, 69, 'Delete Customer', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(74, NULL, 'Supplier', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(75, 74, 'Add Supplier', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(76, 74, 'Edit Supplier', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(77, 74, 'List Supplier', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(78, 74, 'Delete Supplier', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(79, NULL, 'Expenses Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(80, 79, 'Add Expenses Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(81, 79, 'Edit Expenses Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(82, 79, 'List Expenses Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(83, 79, 'Delete Expenses Category', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(84, NULL, 'Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(85, 84, 'Add Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(86, 84, 'Edit Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(87, 84, 'Show Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(88, 84, 'List Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(89, 84, 'Delete Expenses', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(90, NULL, 'Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(91, 90, 'List Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(92, 90, 'Add Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(93, 90, 'Edit Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(94, 90, 'Show Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(95, 90, 'Return Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(96, 90, 'View Payment Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(97, 90, 'Make Payment Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(98, 90, 'Download Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(99, 90, 'Send Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(100, 90, 'Link Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(101, 90, 'Delete Invoice', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(102, NULL, 'Sale Return', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(103, 102, 'Show Sale Return', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(104, 102, 'Return Sale Return', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(105, 102, 'Sale Return List', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(106, NULL, 'Reports', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(107, 106, 'Expenses Report', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(108, 106, 'Sales Report', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(109, 106, 'Purchases Report', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(110, 106, 'Payments Report', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(111, NULL, 'Settings', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57'),
(112, 111, 'Site Settings', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `ic_products`
--

CREATE TABLE `ic_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` decimal(13,3) DEFAULT NULL,
  `dimension_l` decimal(13,3) DEFAULT NULL,
  `dimension_w` decimal(13,3) DEFAULT NULL,
  `dimension_d` decimal(13,3) DEFAULT NULL,
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci,
  `is_variant` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manufacturer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weight_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `measurement_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tax_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'included',
  `custom_tax` decimal(13,3) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `split_sale` tinyint(1) DEFAULT NULL,
  `stock_alert_quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_product_attributes`
--

CREATE TABLE `ic_product_attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attribute_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attribute_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_product_categories`
--

CREATE TABLE `ic_product_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_product_stocks`
--

CREATE TABLE `ic_product_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attribute_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attribute_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchases`
--

CREATE TABLE `ic_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(14,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint(20) UNSIGNED DEFAULT NULL,
  `state` bigint(20) UNSIGNED DEFAULT NULL,
  `city` bigint(20) UNSIGNED DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `received` tinyint(1) DEFAULT NULL,
  `cancel_date` date DEFAULT NULL,
  `cancel_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancel_note` text COLLATE utf8mb4_unicode_ci,
  `short_address` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchase_items`
--

CREATE TABLE `ic_purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchase_item_receives`
--

CREATE TABLE `ic_purchase_item_receives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_receive_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `sub_total` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchase_receives`
--

CREATE TABLE `ic_purchase_receives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `receive_date` date NOT NULL,
  `total` decimal(14,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchase_returns`
--

CREATE TABLE `ic_purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `total` decimal(8,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_purchase_return_items`
--

CREATE TABLE `ic_purchase_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `sub_total` decimal(8,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_roles`
--

CREATE TABLE `ic_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_roles`
--

INSERT INTO `ic_roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2022-09-28 02:19:57', '2022-09-28 02:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `ic_role_has_permissions`
--

CREATE TABLE `ic_role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_role_has_permissions`
--

INSERT INTO `ic_role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ic_sale_returns`
--

CREATE TABLE `ic_sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `return_note` text COLLATE utf8mb4_unicode_ci,
  `return_total_amount` decimal(8,2) NOT NULL,
  `items_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_sale_return_items`
--

CREATE TABLE `ic_sale_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_qty` int(11) NOT NULL,
  `return_price` decimal(8,2) NOT NULL,
  `return_sub_total` decimal(8,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_suppliers`
--

CREATE TABLE `ic_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint(20) UNSIGNED DEFAULT NULL,
  `state` bigint(20) UNSIGNED DEFAULT NULL,
  `city` bigint(20) UNSIGNED DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_system_cities`
--

CREATE TABLE `ic_system_cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_system_countries`
--

CREATE TABLE `ic_system_countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shortname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phonecode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_system_settings`
--

CREATE TABLE `ic_system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `settings_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings_value` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_system_states`
--

CREATE TABLE `ic_system_states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ic_users`
--

CREATE TABLE `ic_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_users`
--

INSERT INTO `ic_users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `avatar`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'admin@app.com', NULL, '2022-09-28 02:19:57', '$2y$10$QTe1Bf8wxZJPTqyhIpcOQuOY1EMF2sdfGyOj3xdfe5w7iL2qsmW..', NULL, 'active', 'vERf2UQxbQ', '2022-09-28 02:19:57', '2022-09-28 02:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `ic_warehouses`
--

CREATE TABLE `ic_warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ic_warehouses`
--

INSERT INTO `ic_warehouses` (`id`, `name`, `email`, `phone`, `company_name`, `address_1`, `address_2`, `priority`, `is_default`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Default Warehouse', 'default@email.com', '12345678', 'Default Company', '', '', 1, 1, 'active', 1, 1, '2022-09-28 02:19:58', '2022-09-28 02:19:58');

-- --------------------------------------------------------

--
-- Table structure for table `ic_weight_units`
--

CREATE TABLE `ic_weight_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ic_attributes`
--
ALTER TABLE `ic_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_attributes_name_index` (`name`),
  ADD KEY `ic_attributes_created_by_foreign` (`created_by`),
  ADD KEY `ic_attributes_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_attribute_items`
--
ALTER TABLE `ic_attribute_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_attribute_items_attribute_id_foreign` (`attribute_id`),
  ADD KEY `ic_attribute_items_created_by_foreign` (`created_by`),
  ADD KEY `ic_attribute_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_brands`
--
ALTER TABLE `ic_brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_brands_name_index` (`name`),
  ADD KEY `ic_brands_created_by_foreign` (`created_by`),
  ADD KEY `ic_brands_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_customers`
--
ALTER TABLE `ic_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_customers_country_foreign` (`country`),
  ADD KEY `ic_customers_state_foreign` (`state`),
  ADD KEY `ic_customers_city_foreign` (`city`),
  ADD KEY `ic_customers_b_country_foreign` (`b_country`),
  ADD KEY `ic_customers_b_state_foreign` (`b_state`),
  ADD KEY `ic_customers_b_city_foreign` (`b_city`),
  ADD KEY `ic_customers_created_by_foreign` (`created_by`),
  ADD KEY `ic_customers_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_expenses`
--
ALTER TABLE `ic_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_expenses_title_index` (`title`),
  ADD KEY `ic_expenses_category_id_foreign` (`category_id`),
  ADD KEY `ic_expenses_created_by_foreign` (`created_by`),
  ADD KEY `ic_expenses_updated_by_foreign` (`updated_by`),
  ADD KEY `ic_expenses_expense_by_foreign` (`expense_by`);

--
-- Indexes for table `ic_expenses_categories`
--
ALTER TABLE `ic_expenses_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_expenses_categories_name_index` (`name`),
  ADD KEY `ic_expenses_categories_created_by_foreign` (`created_by`),
  ADD KEY `ic_expenses_categories_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_expenses_files`
--
ALTER TABLE `ic_expenses_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_expenses_files_expenses_id_foreign` (`expenses_id`);

--
-- Indexes for table `ic_expenses_items`
--
ALTER TABLE `ic_expenses_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_expenses_items_expenses_id_foreign` (`expenses_id`);

--
-- Indexes for table `ic_failed_jobs`
--
ALTER TABLE `ic_failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ic_invoices`
--
ALTER TABLE `ic_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_invoices_customer_id_foreign` (`customer_id`),
  ADD KEY `ic_invoices_created_by_foreign` (`created_by`),
  ADD KEY `ic_invoices_updated_by_foreign` (`updated_by`),
  ADD KEY `ic_invoices_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `ic_invoice_items`
--
ALTER TABLE `ic_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_invoice_items_invoice_id_index` (`invoice_id`),
  ADD KEY `ic_invoice_items_product_id_foreign` (`product_id`),
  ADD KEY `ic_invoice_items_created_by_foreign` (`created_by`),
  ADD KEY `ic_invoice_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_invoice_payments`
--
ALTER TABLE `ic_invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_invoice_payments_invoice_id_index` (`invoice_id`),
  ADD KEY `ic_invoice_payments_created_by_foreign` (`created_by`),
  ADD KEY `ic_invoice_payments_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_manufacturers`
--
ALTER TABLE `ic_manufacturers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_manufacturers_name_index` (`name`),
  ADD KEY `ic_manufacturers_created_by_foreign` (`created_by`),
  ADD KEY `ic_manufacturers_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_measurement_units`
--
ALTER TABLE `ic_measurement_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_measurement_units_name_index` (`name`),
  ADD KEY `ic_measurement_units_created_by_foreign` (`created_by`),
  ADD KEY `ic_measurement_units_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_migrations`
--
ALTER TABLE `ic_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ic_model_has_permissions`
--
ALTER TABLE `ic_model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `ic_model_has_roles`
--
ALTER TABLE `ic_model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `ic_password_resets`
--
ALTER TABLE `ic_password_resets`
  ADD KEY `ic_password_resets_email_index` (`email`);

--
-- Indexes for table `ic_permissions`
--
ALTER TABLE `ic_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ic_products`
--
ALTER TABLE `ic_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_products_name_index` (`name`),
  ADD KEY `ic_products_sku_index` (`sku`),
  ADD KEY `ic_products_barcode_index` (`barcode`),
  ADD KEY `ic_products_category_id_foreign` (`category_id`),
  ADD KEY `ic_products_brand_id_foreign` (`brand_id`),
  ADD KEY `ic_products_manufacturer_id_foreign` (`manufacturer_id`),
  ADD KEY `ic_products_weight_unit_id_foreign` (`weight_unit_id`),
  ADD KEY `ic_products_measurement_unit_id_foreign` (`measurement_unit_id`),
  ADD KEY `ic_products_created_by_foreign` (`created_by`),
  ADD KEY `ic_products_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_product_attributes`
--
ALTER TABLE `ic_product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_product_attributes_product_id_foreign` (`product_id`),
  ADD KEY `ic_product_attributes_attribute_id_foreign` (`attribute_id`),
  ADD KEY `ic_product_attributes_attribute_item_id_foreign` (`attribute_item_id`);

--
-- Indexes for table `ic_product_categories`
--
ALTER TABLE `ic_product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_product_categories_name_index` (`name`),
  ADD KEY `ic_product_categories_parent_id_foreign` (`parent_id`),
  ADD KEY `ic_product_categories_created_by_foreign` (`created_by`),
  ADD KEY `ic_product_categories_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_product_stocks`
--
ALTER TABLE `ic_product_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_product_stocks_product_id_foreign` (`product_id`),
  ADD KEY `ic_product_stocks_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `ic_product_stocks_attribute_id_foreign` (`attribute_id`),
  ADD KEY `ic_product_stocks_attribute_item_id_foreign` (`attribute_item_id`);

--
-- Indexes for table `ic_purchases`
--
ALTER TABLE `ic_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_purchases_purchase_number_unique` (`purchase_number`),
  ADD KEY `ic_purchases_supplier_id_foreign` (`supplier_id`),
  ADD KEY `ic_purchases_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `ic_purchases_country_foreign` (`country`),
  ADD KEY `ic_purchases_state_foreign` (`state`),
  ADD KEY `ic_purchases_city_foreign` (`city`),
  ADD KEY `ic_purchases_created_by_foreign` (`created_by`),
  ADD KEY `ic_purchases_updated_by_foreign` (`updated_by`),
  ADD KEY `ic_purchases_cancel_by_foreign` (`cancel_by`);

--
-- Indexes for table `ic_purchase_items`
--
ALTER TABLE `ic_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `ic_purchase_items_product_id_foreign` (`product_id`),
  ADD KEY `ic_purchase_items_created_by_foreign` (`created_by`),
  ADD KEY `ic_purchase_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_purchase_item_receives`
--
ALTER TABLE `ic_purchase_item_receives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_purchase_item_receives_purchase_receive_id_foreign` (`purchase_receive_id`),
  ADD KEY `ic_purchase_item_receives_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `ic_purchase_item_receives_product_id_foreign` (`product_id`);

--
-- Indexes for table `ic_purchase_receives`
--
ALTER TABLE `ic_purchase_receives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_purchase_receives_purchase_id_foreign` (`purchase_id`),
  ADD KEY `ic_purchase_receives_created_by_foreign` (`created_by`),
  ADD KEY `ic_purchase_receives_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_purchase_returns`
--
ALTER TABLE `ic_purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_purchase_returns_purchase_id_foreign` (`purchase_id`),
  ADD KEY `ic_purchase_returns_created_by_foreign` (`created_by`),
  ADD KEY `ic_purchase_returns_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_purchase_return_items`
--
ALTER TABLE `ic_purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `ic_purchase_return_items_purchase_item_id_foreign` (`purchase_item_id`),
  ADD KEY `ic_purchase_return_items_product_id_foreign` (`product_id`),
  ADD KEY `ic_purchase_return_items_created_by_foreign` (`created_by`),
  ADD KEY `ic_purchase_return_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_roles`
--
ALTER TABLE `ic_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ic_role_has_permissions`
--
ALTER TABLE `ic_role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `ic_role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `ic_sale_returns`
--
ALTER TABLE `ic_sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_sale_returns_invoice_id_foreign` (`invoice_id`),
  ADD KEY `ic_sale_returns_created_by_foreign` (`created_by`),
  ADD KEY `ic_sale_returns_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_sale_return_items`
--
ALTER TABLE `ic_sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `ic_sale_return_items_invoice_item_id_foreign` (`invoice_item_id`),
  ADD KEY `ic_sale_return_items_product_id_foreign` (`product_id`),
  ADD KEY `ic_sale_return_items_created_by_foreign` (`created_by`),
  ADD KEY `ic_sale_return_items_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_suppliers`
--
ALTER TABLE `ic_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_suppliers_country_foreign` (`country`),
  ADD KEY `ic_suppliers_state_foreign` (`state`),
  ADD KEY `ic_suppliers_city_foreign` (`city`),
  ADD KEY `ic_suppliers_created_by_foreign` (`created_by`),
  ADD KEY `ic_suppliers_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_system_cities`
--
ALTER TABLE `ic_system_cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_system_cities_created_by_foreign` (`created_by`),
  ADD KEY `ic_system_cities_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_system_countries`
--
ALTER TABLE `ic_system_countries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_system_countries_created_by_foreign` (`created_by`),
  ADD KEY `ic_system_countries_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_system_settings`
--
ALTER TABLE `ic_system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ic_system_states`
--
ALTER TABLE `ic_system_states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_system_states_created_by_foreign` (`created_by`),
  ADD KEY `ic_system_states_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_users`
--
ALTER TABLE `ic_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic_users_email_unique` (`email`),
  ADD KEY `ic_users_name_index` (`name`),
  ADD KEY `ic_users_email_index` (`email`);

--
-- Indexes for table `ic_warehouses`
--
ALTER TABLE `ic_warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_warehouses_name_index` (`name`),
  ADD KEY `ic_warehouses_created_by_foreign` (`created_by`),
  ADD KEY `ic_warehouses_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ic_weight_units`
--
ALTER TABLE `ic_weight_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ic_weight_units_name_index` (`name`),
  ADD KEY `ic_weight_units_created_by_foreign` (`created_by`),
  ADD KEY `ic_weight_units_updated_by_foreign` (`updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ic_attributes`
--
ALTER TABLE `ic_attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_attribute_items`
--
ALTER TABLE `ic_attribute_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_brands`
--
ALTER TABLE `ic_brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_customers`
--
ALTER TABLE `ic_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_expenses`
--
ALTER TABLE `ic_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_expenses_categories`
--
ALTER TABLE `ic_expenses_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_expenses_files`
--
ALTER TABLE `ic_expenses_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_expenses_items`
--
ALTER TABLE `ic_expenses_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_failed_jobs`
--
ALTER TABLE `ic_failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_invoices`
--
ALTER TABLE `ic_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_invoice_items`
--
ALTER TABLE `ic_invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_invoice_payments`
--
ALTER TABLE `ic_invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_manufacturers`
--
ALTER TABLE `ic_manufacturers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_measurement_units`
--
ALTER TABLE `ic_measurement_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_migrations`
--
ALTER TABLE `ic_migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `ic_permissions`
--
ALTER TABLE `ic_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `ic_products`
--
ALTER TABLE `ic_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_product_attributes`
--
ALTER TABLE `ic_product_attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_product_categories`
--
ALTER TABLE `ic_product_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_product_stocks`
--
ALTER TABLE `ic_product_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchases`
--
ALTER TABLE `ic_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchase_items`
--
ALTER TABLE `ic_purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchase_item_receives`
--
ALTER TABLE `ic_purchase_item_receives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchase_receives`
--
ALTER TABLE `ic_purchase_receives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchase_returns`
--
ALTER TABLE `ic_purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_purchase_return_items`
--
ALTER TABLE `ic_purchase_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_roles`
--
ALTER TABLE `ic_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ic_sale_returns`
--
ALTER TABLE `ic_sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_sale_return_items`
--
ALTER TABLE `ic_sale_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_suppliers`
--
ALTER TABLE `ic_suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_system_cities`
--
ALTER TABLE `ic_system_cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_system_countries`
--
ALTER TABLE `ic_system_countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_system_settings`
--
ALTER TABLE `ic_system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_system_states`
--
ALTER TABLE `ic_system_states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ic_users`
--
ALTER TABLE `ic_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ic_warehouses`
--
ALTER TABLE `ic_warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ic_weight_units`
--
ALTER TABLE `ic_weight_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ic_attributes`
--
ALTER TABLE `ic_attributes`
  ADD CONSTRAINT `ic_attributes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_attributes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_attribute_items`
--
ALTER TABLE `ic_attribute_items`
  ADD CONSTRAINT `ic_attribute_items_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ic_attribute_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_attribute_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_brands`
--
ALTER TABLE `ic_brands`
  ADD CONSTRAINT `ic_brands_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_brands_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_customers`
--
ALTER TABLE `ic_customers`
  ADD CONSTRAINT `ic_customers_b_city_foreign` FOREIGN KEY (`b_city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_b_country_foreign` FOREIGN KEY (`b_country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_b_state_foreign` FOREIGN KEY (`b_state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_customers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_expenses`
--
ALTER TABLE `ic_expenses`
  ADD CONSTRAINT `ic_expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ic_expenses_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_expenses_expense_by_foreign` FOREIGN KEY (`expense_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_expenses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_expenses_categories`
--
ALTER TABLE `ic_expenses_categories`
  ADD CONSTRAINT `ic_expenses_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_expenses_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_expenses_files`
--
ALTER TABLE `ic_expenses_files`
  ADD CONSTRAINT `ic_expenses_files_expenses_id_foreign` FOREIGN KEY (`expenses_id`) REFERENCES `ic_expenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_expenses_items`
--
ALTER TABLE `ic_expenses_items`
  ADD CONSTRAINT `ic_expenses_items_expenses_id_foreign` FOREIGN KEY (`expenses_id`) REFERENCES `ic_expenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_invoices`
--
ALTER TABLE `ic_invoices`
  ADD CONSTRAINT `ic_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `ic_customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_invoices_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`);

--
-- Constraints for table `ic_invoice_items`
--
ALTER TABLE `ic_invoice_items`
  ADD CONSTRAINT `ic_invoice_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ic_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `ic_invoice_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_invoice_payments`
--
ALTER TABLE `ic_invoice_payments`
  ADD CONSTRAINT `ic_invoice_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ic_invoice_payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_manufacturers`
--
ALTER TABLE `ic_manufacturers`
  ADD CONSTRAINT `ic_manufacturers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_manufacturers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_measurement_units`
--
ALTER TABLE `ic_measurement_units`
  ADD CONSTRAINT `ic_measurement_units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_measurement_units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_model_has_permissions`
--
ALTER TABLE `ic_model_has_permissions`
  ADD CONSTRAINT `ic_model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `ic_permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_model_has_roles`
--
ALTER TABLE `ic_model_has_roles`
  ADD CONSTRAINT `ic_model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `ic_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_products`
--
ALTER TABLE `ic_products`
  ADD CONSTRAINT `ic_products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `ic_brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ic_product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `ic_manufacturers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_measurement_unit_id_foreign` FOREIGN KEY (`measurement_unit_id`) REFERENCES `ic_measurement_units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_products_weight_unit_id_foreign` FOREIGN KEY (`weight_unit_id`) REFERENCES `ic_weight_units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_product_attributes`
--
ALTER TABLE `ic_product_attributes`
  ADD CONSTRAINT `ic_product_attributes_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_attributes_attribute_item_id_foreign` FOREIGN KEY (`attribute_item_id`) REFERENCES `ic_attribute_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_product_categories`
--
ALTER TABLE `ic_product_categories`
  ADD CONSTRAINT `ic_product_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ic_product_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_product_stocks`
--
ALTER TABLE `ic_product_stocks`
  ADD CONSTRAINT `ic_product_stocks_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `ic_attributes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_stocks_attribute_item_id_foreign` FOREIGN KEY (`attribute_item_id`) REFERENCES `ic_attribute_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ic_product_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_purchases`
--
ALTER TABLE `ic_purchases`
  ADD CONSTRAINT `ic_purchases_cancel_by_foreign` FOREIGN KEY (`cancel_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `ic_suppliers` (`id`),
  ADD CONSTRAINT `ic_purchases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchases_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `ic_warehouses` (`id`);

--
-- Constraints for table `ic_purchase_items`
--
ALTER TABLE `ic_purchase_items`
  ADD CONSTRAINT `ic_purchase_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  ADD CONSTRAINT `ic_purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  ADD CONSTRAINT `ic_purchase_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_purchase_item_receives`
--
ALTER TABLE `ic_purchase_item_receives`
  ADD CONSTRAINT `ic_purchase_item_receives_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  ADD CONSTRAINT `ic_purchase_item_receives_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `ic_purchase_items` (`id`),
  ADD CONSTRAINT `ic_purchase_item_receives_purchase_receive_id_foreign` FOREIGN KEY (`purchase_receive_id`) REFERENCES `ic_purchase_receives` (`id`);

--
-- Constraints for table `ic_purchase_receives`
--
ALTER TABLE `ic_purchase_receives`
  ADD CONSTRAINT `ic_purchase_receives_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchase_receives_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  ADD CONSTRAINT `ic_purchase_receives_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_purchase_returns`
--
ALTER TABLE `ic_purchase_returns`
  ADD CONSTRAINT `ic_purchase_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `ic_purchases` (`id`),
  ADD CONSTRAINT `ic_purchase_returns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_purchase_return_items`
--
ALTER TABLE `ic_purchase_return_items`
  ADD CONSTRAINT `ic_purchase_return_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  ADD CONSTRAINT `ic_purchase_return_items_purchase_item_id_foreign` FOREIGN KEY (`purchase_item_id`) REFERENCES `ic_purchase_items` (`id`),
  ADD CONSTRAINT `ic_purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `ic_purchase_returns` (`id`),
  ADD CONSTRAINT `ic_purchase_return_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_role_has_permissions`
--
ALTER TABLE `ic_role_has_permissions`
  ADD CONSTRAINT `ic_role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `ic_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ic_role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `ic_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ic_sale_returns`
--
ALTER TABLE `ic_sale_returns`
  ADD CONSTRAINT `ic_sale_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_sale_returns_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `ic_invoices` (`id`),
  ADD CONSTRAINT `ic_sale_returns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_sale_return_items`
--
ALTER TABLE `ic_sale_return_items`
  ADD CONSTRAINT `ic_sale_return_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_sale_return_items_invoice_item_id_foreign` FOREIGN KEY (`invoice_item_id`) REFERENCES `ic_invoice_items` (`id`),
  ADD CONSTRAINT `ic_sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `ic_products` (`id`),
  ADD CONSTRAINT `ic_sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `ic_sale_returns` (`id`),
  ADD CONSTRAINT `ic_sale_return_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_suppliers`
--
ALTER TABLE `ic_suppliers`
  ADD CONSTRAINT `ic_suppliers_city_foreign` FOREIGN KEY (`city`) REFERENCES `ic_system_cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_suppliers_country_foreign` FOREIGN KEY (`country`) REFERENCES `ic_system_countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_suppliers_state_foreign` FOREIGN KEY (`state`) REFERENCES `ic_system_states` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_suppliers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_system_cities`
--
ALTER TABLE `ic_system_cities`
  ADD CONSTRAINT `ic_system_cities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_system_cities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_system_countries`
--
ALTER TABLE `ic_system_countries`
  ADD CONSTRAINT `ic_system_countries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_system_countries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_system_states`
--
ALTER TABLE `ic_system_states`
  ADD CONSTRAINT `ic_system_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_system_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_warehouses`
--
ALTER TABLE `ic_warehouses`
  ADD CONSTRAINT `ic_warehouses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_warehouses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ic_weight_units`
--
ALTER TABLE `ic_weight_units`
  ADD CONSTRAINT `ic_weight_units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ic_weight_units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `ic_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
