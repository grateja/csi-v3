-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2020 at 02:52 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `csi-v3-2020`
--
CREATE DATABASE IF NOT EXISTS `csi-v3-2020` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `csi-v3-2020`;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `earned_points` double DEFAULT '0',
  `first_visit` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_dries`
--

DROP TABLE IF EXISTS `customer_dries`;
CREATE TABLE `customer_dries` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_transaction_item_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dryer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'TITAN, REGULAR',
  `pulse_count` int(11) NOT NULL,
  `minutes` int(11) NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `used` datetime DEFAULT NULL,
  `staff_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'staff who activates the service',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_washes`
--

DROP TABLE IF EXISTS `customer_washes`;
CREATE TABLE `customer_washes` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_transaction_item_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `washer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'TITAN, REGULAR',
  `pulse_count` int(11) NOT NULL,
  `minutes` int(11) NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `used` datetime DEFAULT NULL,
  `staff_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'staff who activates the service',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE `discounts` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drying_services`
--

DROP TABLE IF EXISTS `drying_services`;
CREATE TABLE `drying_services` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
  `machine_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'REGULAR, TITAN',
  `minutes` int(11) NOT NULL COMMENT 'Must be divisible by 10',
  `points` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `amount` double DEFAULT NULL,
  `expense_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Free text. Can add what ever type user may want to add',
  `staff_name` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `full_services`
--

DROP TABLE IF EXISTS `full_services`;
CREATE TABLE `full_services` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_charge` double(8,2) NOT NULL DEFAULT '0.00',
  `discount` double(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `full_service_items`
--

DROP TABLE IF EXISTS `full_service_items`;
CREATE TABLE `full_service_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_service_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'washing, drying, other',
  `washing_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drying_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `points` double(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `full_service_products`
--

DROP TABLE IF EXISTS `full_service_products`;
CREATE TABLE `full_service_products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_service_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_order_formats`
--

DROP TABLE IF EXISTS `job_order_formats`;
CREATE TABLE `job_order_formats` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `char_count` int(11) NOT NULL DEFAULT '5',
  `prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '#' COMMENT 'Starts with #',
  `start_number` int(11) NOT NULL DEFAULT '1' COMMENT 'The next item will be inserted starts with 1',
  `format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#%05d' COMMENT 'Leading 5 zeros before the first digits',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

DROP TABLE IF EXISTS `loyalty_points`;
CREATE TABLE `loyalty_points` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_in_peso` double DEFAULT NULL COMMENT 'Amount in peso per points',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

DROP TABLE IF EXISTS `machines`;
CREATE TABLE `machines` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'rw = regular washer, rd = regular dryer, tw = titan washer, td = titan dryer',
  `machine_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_activated` timestamp NULL DEFAULT NULL,
  `total_minutes` int(11) NOT NULL DEFAULT '0',
  `initial_time` int(11) DEFAULT NULL COMMENT 'initial pulse',
  `additional_time` int(11) DEFAULT NULL COMMENT 'additional pulse',
  `initial_price` double DEFAULT NULL,
  `additional_price` double DEFAULT NULL,
  `initial_cycle_count` int(11) DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Used only for reference for dryer, for additional dry',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machine_remarks`
--

DROP TABLE IF EXISTS `machine_remarks`;
CREATE TABLE `machine_remarks` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remaining_time` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machine_usages`
--

DROP TABLE IF EXISTS `machine_usages`;
CREATE TABLE `machine_usages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minutes` double NOT NULL,
  `activation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `synched` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_03_30_234702_create_roles_table', 1),
(9, '2019_03_31_073444_create_role_users_table', 1),
(10, '2019_06_05_142012_create_products_table', 1),
(11, '2019_06_06_072241_create_product_purchases_table', 1),
(12, '2019_06_09_161425_create_customers_table', 1),
(13, '2019_06_09_171133_create_machines_table', 1),
(14, '2019_06_09_181043_create_transactions_table', 1),
(15, '2019_06_30_183508_create_transaction_payments_table', 1),
(16, '2019_07_13_180812_create_rfid_cards_table', 1),
(17, '2019_07_16_080235_create_rfid_load_transactions_table', 1),
(18, '2019_07_18_204538_create_expenses_table', 1),
(19, '2019_07_21_081604_create_discounts_table', 1),
(20, '2019_07_21_213712_create_loyalty_points_table', 1),
(21, '2019_07_22_081008_create_job_order_formats_table', 1),
(22, '2019_10_22_234331_create_clients_table', 1),
(23, '2019_12_28_090837_create_drying_services_table', 1),
(24, '2019_12_28_091612_create_washing_services_table', 1),
(25, '2019_12_28_091920_create_other_services_table', 1),
(26, '2019_12_28_131352_create_full_services_table', 1),
(27, '2019_12_28_131704_create_full_service_items_table', 1),
(28, '2020_01_02_165210_create_service_transaction_items_table', 1),
(29, '2020_01_05_165654_create_customer_washes_table', 1),
(30, '2020_01_06_195618_create_customer_dries_table', 1),
(31, '2020_01_10_190241_create_full_service_products_table', 1),
(32, '2020_01_11_122725_create_product_transaction_items_table', 1),
(33, '2020_01_13_173031_create_machine_usages_table', 1),
(34, '2020_01_14_191110_create_machine_remarks_table', 1),
(35, '2020_01_19_202548_create_rfid_card_transactions_table', 1),
(36, '2020_01_20_194531_create_unregistered_cards_table', 1),
(37, '2020_02_11_194859_create_transaction_remarks_table', 1),
(38, '2020_03_31_132419_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('29438efab2858442dde6f6ceee7319f4988e131016e19bbe75713bb51be61ffb0321a73417ebe229', 'e026cf14-0093-4de3-8ab2-e13086acb7ac', 'a80845fb-bef2-47d8-83b6-568816518817', 'csi-2019', '[]', 0, '2020-04-08 00:50:49', '2020-04-08 00:50:49', '2021-04-08 08:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
('a80845fb-bef2-47d8-83b6-568816518817', NULL, 'csi-v3-2020', 'L1Bc7DKljgdsqLEV7DA4Jmx41zWRasCbg0OZJXZt', 'http://localhost', 1, 0, 0, '2020-04-08 00:50:04', '2020-04-08 00:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 'a80845fb-bef2-47d8-83b6-568816518817', '2020-04-08 00:50:04', '2020-04-08 00:50:04');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_services`
--

DROP TABLE IF EXISTS `other_services`;
CREATE TABLE `other_services` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selling_price` double DEFAULT NULL,
  `current_stock` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchases`
--

DROP TABLE IF EXISTS `product_purchases`;
CREATE TABLE `product_purchases` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_cost` double DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `staff_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_transaction_items`
--

DROP TABLE IF EXISTS `product_transaction_items`;
CREATE TABLE `product_transaction_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `product_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfid_cards`
--

DROP TABLE IF EXISTS `rfid_cards`;
CREATE TABLE `rfid_cards` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` double NOT NULL DEFAULT '0',
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'If issued to customer',
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For master card',
  `card_type` char(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'c' COMMENT 'c = customer, u = user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfid_card_transactions`
--

DROP TABLE IF EXISTS `rfid_card_transactions`;
CREATE TABLE `rfid_card_transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `card_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'c, u',
  `minutes` int(11) NOT NULL,
  `machine_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfid_card_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfid_load_transactions`
--

DROP TABLE IF EXISTS `rfid_load_transactions`;
CREATE TABLE `rfid_load_transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid_card_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double DEFAULT NULL,
  `remaining_balance` double DEFAULT NULL COMMENT 'Remaining balance before loading.',
  `current_balance` double DEFAULT NULL COMMENT 'Balance after loading.',
  `cash` double DEFAULT NULL COMMENT 'Amount paid.',
  `change` double DEFAULT NULL,
  `staff_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

DROP TABLE IF EXISTS `role_users`;
CREATE TABLE `role_users` (
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_transaction_items`
--

DROP TABLE IF EXISTS `service_transaction_items`;
CREATE TABLE `service_transaction_items` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'washing, drying, other, full',
  `earning_points` double(8,2) NOT NULL DEFAULT '0.00',
  `washing_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drying_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_service_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'user who saved the transaction',
  `staff_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'static name of user who saved the transaction',
  `job_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` double(8,2) NOT NULL DEFAULT '0.00',
  `date_paid` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_payments`
--

DROP TABLE IF EXISTS `transaction_payments`;
CREATE TABLE `transaction_payments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cash` double DEFAULT '0',
  `points` double DEFAULT '0' COMMENT 'Customer loyalty points used',
  `points_in_peso` double DEFAULT '0' COMMENT 'Points in peso used during payment',
  `card_load_used` double DEFAULT '0' COMMENT 'Amount of card load used',
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Card used',
  `discount` double DEFAULT '0' COMMENT 'Percentage',
  `total_amount` double DEFAULT '0',
  `balance` double DEFAULT '0',
  `change` double DEFAULT '0',
  `total_cash` double DEFAULT '0',
  `paid_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Static name of bantay',
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bantay',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_remarks`
--

DROP TABLE IF EXISTS `transaction_remarks`;
CREATE TABLE `transaction_remarks` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `synched` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unregistered_cards`
--

DROP TABLE IF EXISTS `unregistered_cards`;
CREATE TABLE `unregistered_cards` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `machine_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `washing_services`
--

DROP TABLE IF EXISTS `washing_services`;
CREATE TABLE `washing_services` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
  `machine_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'REGULAR, TITAN',
  `regular_minutes` int(11) NOT NULL,
  `additional_minutes` int(11) NOT NULL DEFAULT '0',
  `points` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD KEY `clients_user_id_foreign` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_dries`
--
ALTER TABLE `customer_dries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_dries_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_dries_service_transaction_item_id_foreign` (`service_transaction_item_id`);

--
-- Indexes for table `customer_washes`
--
ALTER TABLE `customer_washes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_washes_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_washes_service_transaction_item_id_foreign` (`service_transaction_item_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drying_services`
--
ALTER TABLE `drying_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `full_services`
--
ALTER TABLE `full_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `full_service_items`
--
ALTER TABLE `full_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `full_service_items_full_service_id_foreign` (`full_service_id`),
  ADD KEY `full_service_items_washing_service_id_foreign` (`washing_service_id`),
  ADD KEY `full_service_items_drying_service_id_foreign` (`drying_service_id`),
  ADD KEY `full_service_items_other_service_id_foreign` (`other_service_id`);

--
-- Indexes for table `full_service_products`
--
ALTER TABLE `full_service_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `full_service_products_full_service_id_foreign` (`full_service_id`),
  ADD KEY `full_service_products_product_id_foreign` (`product_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_order_formats`
--
ALTER TABLE `job_order_formats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machines_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `machine_remarks`
--
ALTER TABLE `machine_remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machine_remarks_user_id_foreign` (`user_id`),
  ADD KEY `machine_remarks_machine_id_foreign` (`machine_id`);

--
-- Indexes for table `machine_usages`
--
ALTER TABLE `machine_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machine_usages_machine_id_foreign` (`machine_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `other_services`
--
ALTER TABLE `other_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_purchases`
--
ALTER TABLE `product_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_purchases_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_transaction_items`
--
ALTER TABLE `product_transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_transaction_items_transaction_id_foreign` (`transaction_id`),
  ADD KEY `product_transaction_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `rfid_cards`
--
ALTER TABLE `rfid_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfid_cards_rfid_unique` (`rfid`),
  ADD KEY `rfid_cards_customer_id_foreign` (`customer_id`),
  ADD KEY `rfid_cards_user_id_foreign` (`user_id`);

--
-- Indexes for table `rfid_card_transactions`
--
ALTER TABLE `rfid_card_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfid_card_transactions_machine_id_foreign` (`machine_id`),
  ADD KEY `rfid_card_transactions_rfid_card_id_foreign` (`rfid_card_id`);

--
-- Indexes for table `rfid_load_transactions`
--
ALTER TABLE `rfid_load_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfid_load_transactions_rfid_card_id_foreign` (`rfid_card_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD KEY `role_users_user_id_foreign` (`user_id`),
  ADD KEY `role_users_role_id_foreign` (`role_id`);

--
-- Indexes for table `service_transaction_items`
--
ALTER TABLE `service_transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_transaction_items_transaction_id_foreign` (`transaction_id`),
  ADD KEY `service_transaction_items_washing_service_id_foreign` (`washing_service_id`),
  ADD KEY `service_transaction_items_drying_service_id_foreign` (`drying_service_id`),
  ADD KEY `service_transaction_items_other_service_id_foreign` (`other_service_id`),
  ADD KEY `service_transaction_items_full_service_id_foreign` (`full_service_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_payments_customer_id_foreign` (`customer_id`),
  ADD KEY `transaction_payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `transaction_remarks`
--
ALTER TABLE `transaction_remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_remarks_transaction_id_foreign` (`transaction_id`);

--
-- Indexes for table `unregistered_cards`
--
ALTER TABLE `unregistered_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `washing_services`
--
ALTER TABLE `washing_services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_dries`
--
ALTER TABLE `customer_dries`
  ADD CONSTRAINT `customer_dries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_dries_service_transaction_item_id_foreign` FOREIGN KEY (`service_transaction_item_id`) REFERENCES `service_transaction_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_washes`
--
ALTER TABLE `customer_washes`
  ADD CONSTRAINT `customer_washes_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_washes_service_transaction_item_id_foreign` FOREIGN KEY (`service_transaction_item_id`) REFERENCES `service_transaction_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `full_service_items`
--
ALTER TABLE `full_service_items`
  ADD CONSTRAINT `full_service_items_drying_service_id_foreign` FOREIGN KEY (`drying_service_id`) REFERENCES `drying_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `full_service_items_full_service_id_foreign` FOREIGN KEY (`full_service_id`) REFERENCES `full_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `full_service_items_other_service_id_foreign` FOREIGN KEY (`other_service_id`) REFERENCES `other_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `full_service_items_washing_service_id_foreign` FOREIGN KEY (`washing_service_id`) REFERENCES `washing_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `full_service_products`
--
ALTER TABLE `full_service_products`
  ADD CONSTRAINT `full_service_products_full_service_id_foreign` FOREIGN KEY (`full_service_id`) REFERENCES `full_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `full_service_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `machines`
--
ALTER TABLE `machines`
  ADD CONSTRAINT `machines_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `machine_remarks`
--
ALTER TABLE `machine_remarks`
  ADD CONSTRAINT `machine_remarks_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `machine_remarks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `machine_usages`
--
ALTER TABLE `machine_usages`
  ADD CONSTRAINT `machine_usages_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_purchases`
--
ALTER TABLE `product_purchases`
  ADD CONSTRAINT `product_purchases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_transaction_items`
--
ALTER TABLE `product_transaction_items`
  ADD CONSTRAINT `product_transaction_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rfid_cards`
--
ALTER TABLE `rfid_cards`
  ADD CONSTRAINT `rfid_cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rfid_card_transactions`
--
ALTER TABLE `rfid_card_transactions`
  ADD CONSTRAINT `rfid_card_transactions_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `rfid_card_transactions_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `rfid_load_transactions`
--
ALTER TABLE `rfid_load_transactions`
  ADD CONSTRAINT `rfid_load_transactions_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_transaction_items`
--
ALTER TABLE `service_transaction_items`
  ADD CONSTRAINT `service_transaction_items_drying_service_id_foreign` FOREIGN KEY (`drying_service_id`) REFERENCES `drying_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_transaction_items_full_service_id_foreign` FOREIGN KEY (`full_service_id`) REFERENCES `full_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_transaction_items_other_service_id_foreign` FOREIGN KEY (`other_service_id`) REFERENCES `other_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_transaction_items_washing_service_id_foreign` FOREIGN KEY (`washing_service_id`) REFERENCES `washing_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD CONSTRAINT `transaction_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaction_payments_id_foreign` FOREIGN KEY (`id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaction_remarks`
--
ALTER TABLE `transaction_remarks`
  ADD CONSTRAINT `transaction_remarks_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;

-- START TRANSACTION;
-- roles
DELETE FROM `csi-v3-2020`.`roles`;
INSERT INTO `csi-v3-2020`.`roles` (`id`, `name`)
SELECT `id`, `name` FROM `csi_2019`.`roles`;

-- users
DELETE FROM `csi-v3-2020`.`users`;
INSERT INTO `csi-v3-2020`.`users` (`id`, `name`, `email`, `password`, `contact_number`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    concat(`firstname`, ' ', `lastname`),
    `email`,
    `password`,
    `contact_number`,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`users`;

-- roles_users
DELETE FROM `csi-v3-2020`.`role_users`;
INSERT INTO `csi-v3-2020`.`role_users` (`user_id`, `role_id`)
SELECT `user_id`, `role_id` FROM `csi_2019`.`role_users`;

-- clients
DELETE FROM `csi-v3-2020`.`clients`;
INSERT INTO `csi-v3-2020`.`clients` (`user_id`, `shop_name`, `shop_email`, `shop_number`, `address`, `created_at`, `updated_at`)
SELECT `client_id` AS user_id,
    `name` AS shop_name,
    `email` AS shop_email,
    `contact_no` AS shop_number,
    `address`,
    `created_at`,
    `updated_at`
FROM `csi_2019`.`branches`;

-- machines
DELETE FROM `csi-v3-2020`.`machines`;
INSERT INTO `csi-v3-2020`.`machines` (`id`, `machine_type`, `ip_address`, `machine_name`, `initial_price`, `additional_price`, `initial_time`, `additional_time`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    (CASE `machine_type_id`
        WHEN 1 THEN 'rw'
        WHEN 2 THEN 'rd'
        WHEN 3 THEN 'tw'
        WHEN 4 THEN 'td'
    END) AS `machine_type`,
    `ip_address`,
    `name` AS machine_name,
    (CASE `machine_type_id`
        WHEN 1 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Regular wash')
        WHEN 2 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = '10 mins dry')
        WHEN 3 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan wash')
        WHEN 4 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan dry')
    END) as initial_price,
    (CASE `machine_type_id`
        WHEN 1 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Add super wash')
        WHEN 2 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = '10 mins dry')
        WHEN 3 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Add super wash')
        WHEN 4 THEN (SELECT `price` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan dry')
    END) as additional_price,
    (CASE `machine_type_id`
        WHEN 1 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Regular wash')
        WHEN 2 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = '10 mins dry')
        WHEN 3 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan wash')
        WHEN 4 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan dry')
    END) as initial_time,
    (CASE `machine_type_id`
        WHEN 1 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Add super wash')
        WHEN 2 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = '10 mins dry')
        WHEN 3 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Add super wash')
        WHEN 4 THEN (SELECT `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `name` = 'Titan dry')
    END) as additional_time,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`machines`;

-- customers
DELETE FROM `csi-v3-2020`.`customers`;
INSERT INTO `csi-v3-2020`.`customers` (`id`, `name`, `address`, `contact_number`, `email`, `earned_points`, `first_visit`, `deleted_at`, `created_at`, `updated_at`)

SELECT `id`,
    `name`,
    `address`,
    `contact_number`,
    `email`,
    `earned_points`,
    `birthday` AS first_visit,
    `deleted_at`,
    `created_at`,
    `updated_at`
FROM `csi_2019`.`customers`;

-- discounts
DELETE FROM `csi-v3-2020`.`discounts`;
INSERT INTO `csi-v3-2020`.`discounts` (`id`, `name`, `percentage`, `created_at`, `deleted_at`, `updated_at`)
SELECT `id`,
    `name`,
    `percentage`,
    `created_at`,
    `deleted_at`,
    `updated_at`
FROM `csi_2019`.`discounts`;

-- loyalty_points
DELETE FROM `csi-v3-2020`.`loyalty_points`;
INSERT INTO `csi-v3-2020`.`loyalty_points` (`id`, `amount_in_peso`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `amount_in_peso`,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`loyalty_points`;

-- job_order_formats
DELETE FROM `csi-v3-2020`.`job_order_formats`;
INSERT INTO `csi-v3-2020`.`job_order_formats` (`id`, `char_count`, `prefix`, `start_number`, `format`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `char_count`,
    `prefix`,
    `start_number`,
    `format`,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`job_order_formats`;

-- expenses
DELETE FROM `csi-v3-2020`.`expenses`;
INSERT INTO `csi-v3-2020`.`expenses` (`id`, `date`, `remarks`, `amount`, `expense_type`, `staff_name`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `date`,
    `remarks`,
    `amount`,
    `expense_type`,
    (SELECT `name` FROM `csi-v3-2020`.`users` WHERE `csi-v3-2020`.`users`.`id` = `csi_2019`.`expenses`.`user_id`) AS staff_name,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`expenses`;

-- products
DELETE FROM `csi-v3-2020`.`products`;
INSERT INTO `csi-v3-2020`.`products` (`id`, `name`, `selling_price`, `current_stock`, `created_at`, `updated_at`, `deleted_at`)
SELECT `branch_products`.`id`,
    `name`,
    `price` AS selling_price,
    (`initial_stock` + (SELECT sum(`quantity`) FROM `csi_2019`.`product_purchases` WHERE `branch_products`.`id` = `product_purchases`.`branch_product_id` and `product_purchases`.`deleted_at` is null) - (SELECT sum(`quantity`) FROM `csi_2019`.`product_transactions` WHERE `branch_products`.`id` = `product_transactions`.`branch_product_id` and `product_transactions`.`deleted_at` is null)) AS current_stock,
    `branch_products`.`created_at`,
    `branch_products`.`updated_at`,
    `branch_products`.`deleted_at`
    FROM `csi_2019`.`branch_products`
    JOIN `csi_2019`.`products` ON `products`.`id` = `branch_products`.`product_id`;

-- product_purchases
DELETE FROM `csi-v3-2020`.`product_purchases`;
INSERT INTO `csi-v3-2020`.`product_purchases` (`id`, `date`, `product_id`, `product_name`, `receipt`, `quantity`, `unit_cost`, `remarks`, `staff_name`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `date`,
    `branch_product_id` AS product_id,
    (SELECT (SELECT `name` FROM `csi_2019`.`products` WHERE `products`.`id` = `branch_products`.`product_id`) FROM `csi_2019`.`branch_products` WHERE `branch_products`.`id` = `branch_product_id`) AS product_name,
    `receipt`,
    `quantity`,
    `unit_cost`,
    `remarks`,
    (SELECT concat(`firstname`, ' ', `lastname`) FROM `csi_2019`.`users` WHERE `users`.`id` = `user_id`) AS staff_name,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`product_purchases`;

-- washing_services
DELETE FROM `csi-v3-2020`.`washing_services`;
INSERT INTO `csi-v3-2020`.`washing_services` (`id`, `name`, `price`, `machine_type`, `regular_minutes`, `created_at`, `updated_at`, `deleted_at`)
SELECT `branch_services`.`id` AS id,
    `services`.`name` AS name,
    `full_service_price` AS price,
    IF(`services`.`service_type_id` = 1, 'REGULAR', 'TITAN') AS machine_type,
    `minutes_per_pulse` AS regular_minutes,
    `branch_services`.`created_at`,
    `branch_services`.`updated_at`,
    `branch_services`.`deleted_at`
FROM `csi_2019`.`branch_services`
    JOIN `csi_2019`.`services` ON `services`.`id` = `service_id`
    WHERE `services`.`service_type_id` = 1 or `services`.`service_type_id` = 3;

-- drying_services
DELETE FROM `csi-v3-2020`.`drying_services`;
INSERT INTO `csi-v3-2020`.`drying_services` (`id`, `name`, `price`, `machine_type`, `minutes`, `created_at`, `updated_at`, `deleted_at`)
SELECT `branch_services`.`id` AS id,
    `services`.`name` AS name,
    `full_service_price` AS price,
    IF(`services`.`service_type_id` = 1, 'REGULAR', 'TITAN') AS machine_type,
    `minutes_per_pulse` * `pulse_count` AS minutes,
    `branch_services`.`created_at`,
    `branch_services`.`updated_at`,
    `branch_services`.`deleted_at`
FROM `csi_2019`.`branch_services`
    JOIN `csi_2019`.`services` ON `services`.`id` = `service_id`
    WHERE `services`.`service_type_id` = 2 or `services`.`service_type_id` = 4;

-- other_services
DELETE FROM `csi-v3-2020`.`other_services`;
INSERT INTO `csi-v3-2020`.`other_services` (`id`, `name`, `price`, `created_at`, `updated_at`, `deleted_at`)
SELECT `branch_services`.`id` AS id,
    `services`.`name` AS name,
    `full_service_price` AS price,
    `branch_services`.`created_at`,
    `branch_services`.`updated_at`,
    `branch_services`.`deleted_at`
FROM `csi_2019`.`branch_services`
    JOIN `csi_2019`.`services` ON `services`.`id` = `service_id`
    WHERE `services`.`service_type_id` = 5;

-- rfid_cards
DELETE FROM `csi-v3-2020`.`rfid_cards`;
INSERT INTO `csi-v3-2020`.`rfid_cards` (`id`, `rfid`, `balance`, `customer_id`, `user_id`, `card_type`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `rfid`,
    `balance`,
    `customer_id`,
    `user_id`,
    `card_type`,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`rfid_cards`;

-- rfid_load_transactions
DELETE FROM `csi-v3-2020`.`rfid_load_transactions`;
INSERT INTO `csi-v3-2020`.`rfid_load_transactions` (`id`, `rfid_card_id`, `customer_name`, `rfid`, `amount`, `remaining_balance`, `current_balance`, `cash`, `change`, `staff_name`, `remarks`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `rfid_card_id`,
    (SELECT (SELECT `name` FROM `csi_2019`.`customers` WHERE `customers`.`id` = `rfid_cards`.`customer_id`) FROM `csi_2019`.`rfid_cards` WHERE `rfid_card_id` = `rfid_cards`.`id`) AS customer_name,
    (SELECT `rfid` FROM `csi_2019`.`rfid_cards` WHERE `rfid_card_id` = `rfid_cards`.`id`) AS rfid,
    `amount`,
    `remaining_balance`,
    `current_balance`,
    `cash`,
    `change`,
    (SELECT concat(`firstname`, ' ', `lastname`) FROM `csi_2019`.`users` WHERE `users`.`id` = `user_id`) AS staff_name,
    `remarks`,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`rfid_load_transactions`;

-- rfid_card_transactions
DELETE FROM `csi-v3-2020`.`rfid_card_transactions`;
INSERT INTO `csi-v3-2020`.`rfid_card_transactions` (`id`, `rfid`, `machine_name`, `owner_name`, `price`, `card_type`, `minutes`, `machine_id`, `rfid_card_id`, `created_at`, `updated_at`, `deleted_at`)
SELECT `rfid_transactions`.`id`,
    `rfid_cards`.`rfid` AS rfid,
    (SELECT `name` FROM `csi_2019`.`machines` WHERE `machines`.`id` = `machine_id`) AS machine_name,
    (SELECT (COALESCE((SELECT `name` FROM `csi_2019`.`customers` WHERE `customers`.`id` = `rfid_cards`.`customer_id`), (SELECT `firstname` FROM `csi_2019`.`users` WHERE `users`.`id` = `rfid_cards`.`user_id`))) WHERE `csi_2019`.`rfid_cards`.`id` = `rfid_card_id`) AS owner_name,
    `price`,
    `rfid_cards`.`card_type` AS card_type,
    (SELECT `minutes_per_pulse` FROM `csi_2019`.`rfid_service_prices` WHERE `rfid_service_prices`.`id` = `rfid_service_price_id`) AS minutes,
    `machine_id`,
    `rfid_card_id`,
    `rfid_transactions`.`created_at`,
    `rfid_transactions`.`updated_at`,
    `rfid_transactions`.`deleted_at`
FROM `csi_2019`.`rfid_transactions`
JOIN `csi_2019`.`rfid_cards` ON `rfid_cards`.`id` = `rfid_transactions`.`rfid_card_id`;

-- transactions
DELETE FROM `csi-v3-2020`.`transactions`;
INSERT INTO `csi-v3-2020`.`transactions` (`id`, `date`, `customer_id`, `user_id`, `staff_name`, `job_order`, `saved`, `customer_name`, `total_price`, `date_paid`, `created_at`, `updated_at`, `deleted_at`)
SELECT `id`,
    `date`,
    `customer_id`,
    `user_id`,
    (SELECT CONCAT(`firstname`, ' ', `lastname`) FROM `csi_2019`.`users` WHERE `users`.`id` = `user_id`) AS staff_name,
    `job_order`,
    (IF(`date_saved` is null, 0, 1)) AS saved,
    (SELECT `name` FROM `csi_2019`.`customers` WHERE `customers`.`id` = `customer_id`) AS customer_name,
    ((COALESCE((SELECT sum(`unit_price` * `quantity`) FROM `csi_2019`.`service_transactions` WHERE `transaction_id` = `transactions`.`id` and `deleted_at` is null), 0) + COALESCE((SELECT sum(`price` * `quantity`) FROM `csi_2019`.`product_transactions` WHERE `transaction_id` = `transactions`.`id` and `deleted_at` is null), 0))) AS total_price,
    (SELECT `date` FROM `csi_2019`.`transaction_payments` WHERE `transaction_payments`.`transaction_id` = `transactions`.`id`) AS date_paid,
    `created_at`,
    `updated_at`,
    `deleted_at`
FROM `csi_2019`.`transactions`;

-- transaction_payments
DELETE FROM `csi-v3-2020`.`transaction_payments`;
INSERT INTO `csi-v3-2020`.`transaction_payments` (`id`, `customer_id`, `date`, `cash`, `points`, `points_in_peso`, `discount`, `total_amount`, `balance`, `change`, `total_cash`, `paid_to`, `user_id`, `created_at`, `deleted_at`)
SELECT `transaction_id` AS id,
    `customer_id`,
    `date`,
    `cash`,
    `points`,
    `points_in_peso`,
    `discount`,
    `total_amount`,
    `balance`,
    `change`,
    `total_cash`,
    (SELECT concat(`firstname`, ' ', `lastname`) FROM `csi_2019`.`users` WHERE `user_id` = `users`.`id`) AS paid_to,
    `user_id`,
    `date` AS created_at,
    `deleted_at`
FROM `csi_2019`.`transaction_payments`;

-- service_transaction_items
DELETE FROM `csi-v3-2020`.`service_transaction_items`;
INSERT INTO `csi-v3-2020`.`service_transaction_items` (`id`, `transaction_id`, `name`, `price`, `category`, `saved`, `washing_service_id`, `drying_service_id`, `other_service_id`, `created_at`, `updated_at`, `deleted_at`)
SELECT UUID() AS id,
    `transaction_id`,
    (SELECT (SELECT `name` FROM `csi_2019`.`services` WHERE `services`.`id` = `branch_services`.`service_id`) FROM `csi_2019`.`branch_services` WHERE `branch_services`.`id` = `branch_service_id`) AS name,
    `unit_price` AS price,
    (SELECT (SELECT (SELECT lower(`name`) FROM `csi_2019`.`service_types` WHERE `service_types`.`id` = `service_type_id`) FROM `csi_2019`.`services` WHERE `services`.`id` = `branch_services`.`service_id`) FROM `csi_2019`.`branch_services` WHERE `branch_service_id` = `branch_services`.`id`) AS category,
        `saved`,

    if((SELECT (SELECT if(`service_type_id` = 1 or `service_type_id` = 3, 1, 0) FROM `csi_2019`.`services` WHERE `branch_services`.`service_id` = `services`.`id`) FROM `csi_2019`.`branch_services` WHERE `branch_service_id` = `branch_services`.`id`) = 1,`branch_service_id`, null) AS washing_service_id,

    if((SELECT (SELECT if(`service_type_id` = 2 or `service_type_id` = 4, 2, 0)  FROM `csi_2019`.`services` WHERE `branch_services`.`service_id` = `services`.`id`) FROM `csi_2019`.`branch_services` WHERE `branch_service_id` = `branch_services`.`id`) = 2,`branch_service_id`, null) AS drying_service_id,

    if((SELECT (SELECT `service_type_id` FROM `csi_2019`.`services` WHERE `branch_services`.`service_id` = `services`.`id`) FROM `csi_2019`.`branch_services` WHERE `branch_service_id` = `branch_services`.`id`) = 5,`branch_service_id`, null) AS other_service_id,

    `created_at`,
    `updated_at`,
    `deleted_at`
    FROM `csi_2019`.`service_transactions` o
    JOIN
        (SELECT (@rn := @rn + 1) AS n
            FROM `csi_2019`.`service_transactions` o cross JOIN (SELECT @rn := 0) vars
         ) n
         ON n.n <= o.quantity order by id;


-- product_transaction_items
DELETE FROM `csi-v3-2020`.`product_transaction_items`;
INSERT INTO `csi-v3-2020`.`product_transaction_items` (`id`, `transaction_id`, `name`, `price`, `product_id`, `saved`, `created_at`, `updated_at`, `deleted_at`)
SELECT UUID() AS id,
    `transaction_id`,
    (SELECT (SELECT `name` FROM `csi_2019`.`products` WHERE `products`.`id` = `product_id`) FROM `csi_2019`.`branch_products` WHERE `branch_products`.`id` = `branch_product_id`) AS name,
    `price`,
    `branch_product_id` AS product_id,
    `saved`,
    `created_at`,
    `updated_at`,
    `deleted_at`
    FROM `csi_2019`.`product_transactions` o
    JOIN
        (SELECT (@rn := @rn + 1) AS n
            FROM `csi_2019`.`product_transactions` o cross JOIN (SELECT @rn := 0) vars
         ) n
         ON n.n <= o.quantity order by id;

-- machine_usages from completed_service_transaction
DELETE FROM `csi-v3-2020`.`machine_usages`;
INSERT INTO `csi-v3-2020`.`machine_usages` (`id`, `machine_id`, `customer_name`, `minutes`, `activation_type`, `price`, `created_at`, `updated_at`)
SELECT uuid() AS id,
    `machine_id`,
    (SELECT `name` FROM `csi_2019`.`customers` WHERE `customers`.`id` = `customer_id`) AS customer_name,
    (SELECT (`minutes_per_pulse` * `pulse_count`) FROM `csi_2019`.`branch_services` WHERE `branch_service_id` = `branch_services`.`id`) AS minutes,
    'remote' AS activation_type,
    (SELECT `unit_price` FROM `csi_2019`.`service_transactions` WHERE `service_transaction_id` = `service_transactions`.`id`) AS price,
    `created_at`,
    `updated_at`
FROM `csi_2019`.`completed_service_transactions` WHERE machine_id IS NOT NULL;

-- machine_usages from rfid_transactions
INSERT INTO `csi-v3-2020`.`machine_usages` (`id`, `machine_id`, `customer_name`, `minutes`, `activation_type`, `price`, `created_at`, `updated_at`)
SELECT uuid() as id,
    `machine_id`,
    (SELECT (COALESCE((SELECT `name` FROM `csi_2019`.`customers` WHERE `customers`.`id` = `rfid_cards`.`customer_id`), (SELECT `firstname` FROM `csi_2019`.`users` WHERE `users`.`id` = `rfid_cards`.`user_id`))) WHERE `csi_2019`.`rfid_cards`.`id` = `rfid_card_id`) AS customer_name,
    (select `minutes_per_pulse` from `csi_2019`.`rfid_service_prices` where `rfid_service_prices`.`id` = `rfid_service_price_id`) as minutes,
    'card' as activation_type,
    (select `price` from `csi_2019`.`rfid_service_prices` where `rfid_service_prices`.`id` = `rfid_service_price_id`) as price,
    `rfid_transactions`.`created_at`,
    `rfid_transactions`.`updated_at`
FROM  `csi_2019`.`rfid_transactions`
JOIN `csi_2019`.`rfid_cards` ON `rfid_cards`.`id` = `rfid_card_id`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
