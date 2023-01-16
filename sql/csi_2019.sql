-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2019 at 04:04 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `csi_2019`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `municipality_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `client_id`, `name`, `contact_no`, `email`, `address`, `is_main`, `municipality_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'Madeth`s Laundry shop', '0912345678', 'mls@gmail.com', 'Sapang Palay Bulacan', 0, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branch_products`
--

CREATE TABLE `branch_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `price` double DEFAULT NULL,
  `initial_stock` int(11) DEFAULT NULL,
  `minimum_stock` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_products`
--

INSERT INTO `branch_products` (`id`, `product_id`, `branch_id`, `price`, `initial_stock`, `minimum_stock`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 7.5, 10, 10, NULL, NULL, NULL),
(2, 2, 1, 7.5, 10, 10, NULL, NULL, NULL),
(3, 3, 1, 7.5, 10, 10, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branch_services`
--

CREATE TABLE `branch_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `add_super_wash` int(11) NOT NULL DEFAULT '0',
  `full_service_price` double DEFAULT NULL COMMENT 'Leave null to hide on the full service tab',
  `self_service_price` double DEFAULT NULL COMMENT 'Leave null to hide on the self service tab',
  `points` double NOT NULL DEFAULT '0' COMMENT 'Points that customer`s will receive everytime they purchase this service',
  `pulse_count` int(11) DEFAULT NULL COMMENT 'Times of tap for each purchase service| Pretty much like a token',
  `minutes_per_pulse` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_services`
--

INSERT INTO `branch_services` (`id`, `service_id`, `branch_id`, `add_super_wash`, `full_service_price`, `self_service_price`, `points`, `pulse_count`, `minutes_per_pulse`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 0, 65, 60, 0.5, 1, 40, NULL, NULL, NULL),
(2, 2, 1, 0, 65, 60, 0.5, 4, 10, NULL, NULL, NULL),
(3, 3, 1, 0, 30, 0, 0.5, 0, 0, NULL, NULL, NULL),
(4, 4, 1, 0, 15, 13, 0.2, 1, 10, NULL, NULL, NULL),
(5, 5, 1, 10, 80, 75, 1.2, 2, 40, NULL, NULL, NULL),
(6, 6, 1, 0, 0, 0, 0, 4, 10, NULL, NULL, NULL),
(7, 7, 1, 0, 0, 0, 0, 1, 40, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `city_municipalities`
--

CREATE TABLE `city_municipalities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city_municipalities`
--

INSERT INTO `city_municipalities` (`id`, `name`, `deleted_at`) VALUES
(1, 'San Jose Del Monte', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `dealer_id` int(10) UNSIGNED DEFAULT NULL,
  `initial_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_activated` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`user_id`, `dealer_id`, `initial_password`, `account_activated`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, NULL, 'admin', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `completed_product_transactions`
--

CREATE TABLE `completed_product_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_transaction_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'For void',
  `price_sum` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `job_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_paid` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `completed_service_transactions`
--

CREATE TABLE `completed_service_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `service_type_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `service_transaction_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'For void',
  `branch_service_id` int(10) UNSIGNED DEFAULT NULL,
  `available` int(11) DEFAULT NULL COMMENT 'Decrements after every use of this service',
  `machine_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_sum` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `points` double DEFAULT NULL,
  `job_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_super_wash` int(11) NOT NULL DEFAULT '0' COMMENT 'Minutes to be added to regular wash',
  `date_paid` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `completed_service_transactions`
--

INSERT INTO `completed_service_transactions` (`id`, `customer_id`, `branch_id`, `service_id`, `service_type_id`, `user_id`, `transaction_id`, `service_transaction_id`, `branch_service_id`, `available`, `machine_name`, `price_sum`, `quantity`, `points`, `job_order`, `add_super_wash`, `date_paid`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 2, 2, 3, 1, 1, 2, 0, 'Dryer 1', 6825, 105, 52.5, NULL, 0, NULL, '2019-07-15 00:00:50', '2019-07-15 08:10:31', NULL),
(2, 2, 1, 5, 1, 3, 2, 2, 5, 0, 'Washer 1', 160, 2, 2.4, NULL, 10, '2019-07-15 17:46:47', '2019-07-15 00:02:17', '2019-07-15 09:46:47', NULL),
(3, 1, 1, 1, 1, 3, 1, 3, 1, 4, 'Washer 1', 1105, 17, 8.5, NULL, 0, NULL, '2019-07-15 02:52:31', '2019-07-15 08:24:27', NULL),
(4, 1, 1, 3, 5, 3, 1, 4, 3, 1, NULL, 30, 1, 0.5, NULL, 0, NULL, '2019-07-15 07:13:18', '2019-07-15 07:13:18', NULL),
(5, 1, 1, 5, 1, 3, 1, 5, 5, 93, 'Washer 1', 8000, 100, 120, NULL, 10, NULL, '2019-07-15 07:19:40', '2019-07-15 08:24:54', NULL),
(6, 1, 1, 4, 2, 3, 1, 6, 4, 0, 'Dryer 1', 180, 12, 2.4000000000000004, NULL, 0, NULL, '2019-07-15 07:47:46', '2019-07-15 07:53:56', NULL),
(7, 2, 1, 1, 1, 3, 3, 7, 1, 1, 'Washer 1', 130, 2, 1, NULL, 0, NULL, '2019-07-15 09:50:51', '2019-07-15 09:51:16', NULL),
(8, 2, 1, 2, 2, 3, 3, 8, 2, 1, 'Dryer 1', 130, 2, 1, NULL, 0, NULL, '2019-07-15 09:50:51', '2019-07-15 09:52:24', NULL),
(9, 2, 1, 4, 2, 3, 3, 9, 4, 0, 'Dryer 1', 15, 1, 0.2, NULL, 0, NULL, '2019-07-15 09:53:19', '2019-07-15 09:53:50', NULL),
(10, 3, 1, 1, 1, 3, 4, 10, 1, 0, 'Washer 2', 65, 1, 0.5, NULL, 0, NULL, '2019-07-15 10:22:19', '2019-07-15 10:38:03', NULL),
(11, 3, 1, 2, 2, 3, 4, 11, 2, 0, 'Dryer 2', 65, 1, 0.5, NULL, 0, NULL, '2019-07-15 10:22:19', '2019-07-15 10:36:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `client_id`, `address`, `contact_number`, `email`, `rfid`, `birthday`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Tony Stark', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Peter Quill', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Pepper Pot', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dealers`
--

CREATE TABLE `dealers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `machine_type_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_activated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_add_superwash` tinyint(1) NOT NULL DEFAULT '0',
  `total_minutes` double NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `client_id`, `branch_id`, `machine_type_id`, `name`, `description`, `ip_address`, `mac_address`, `time_activated`, `is_add_superwash`, `total_minutes`, `deleted_at`, `created_at`, `updated_at`, `customer_id`) VALUES
(1, 2, 1, 1, 'Washer 1', NULL, '192.168.1.11', NULL, '2019-07-15 09:57:44', 1, 60, NULL, NULL, '2019-07-15 09:58:20', 3),
(2, 2, 1, 2, 'Dryer 1', NULL, '192.168.1.31', NULL, '2019-07-15 09:59:34', 0, 50, NULL, NULL, '2019-07-15 10:00:02', 3),
(3, 2, 1, 1, 'Washer 2', NULL, '192.168.1.11', NULL, '2019-07-15 10:38:03', 0, 40, NULL, NULL, '2019-07-15 10:38:03', 3),
(4, 2, 1, 2, 'Dryer 2', NULL, '192.168.1.11', NULL, '2019-07-15 10:36:58', 0, 40, NULL, NULL, '2019-07-15 10:36:58', 3),
(5, 2, 1, 1, 'Washer 3', NULL, '192.168.1.11', NULL, '2019-07-13 08:37:24', 0, 80, NULL, NULL, '2019-07-15 00:02:40', 2),
(6, 2, 1, 2, 'Dryer 3', NULL, '192.168.1.11', NULL, '2019-07-13 08:37:24', 0, 90, NULL, NULL, '2019-07-15 07:47:58', 1),
(7, 2, 1, 3, 'Titan Washer 1', NULL, '192.168.1.11', NULL, '2019-07-13 08:37:24', 0, 0, NULL, NULL, NULL, NULL),
(8, 2, 1, 4, 'Titan Dryer 1', NULL, '192.168.1.11', NULL, '2019-07-13 08:37:24', 0, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `machine_types`
--

CREATE TABLE `machine_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `machine_types`
--

INSERT INTO `machine_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'regular washer', NULL, NULL),
(2, 'regular dryer', NULL, NULL),
(3, 'titan washer', NULL, NULL),
(4, 'titan dryer', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_04_09_211011_create_barangays_table', 1),
(2, '2014_04_09_211133_create_provinces_table', 1),
(3, '2014_04_09_211206_create_city_municipalities_table', 1),
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(6, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(7, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(8, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(9, '2016_06_01_000004_create_oauth_clients_table', 1),
(10, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(11, '2019_03_23_071206_create_dealers_table', 1),
(12, '2019_03_23_080629_create_clients_table', 1),
(13, '2019_03_23_085632_create_branches_table', 1),
(14, '2019_03_30_234702_create_roles_table', 1),
(15, '2019_03_31_073444_create_role_users_table', 1),
(16, '2019_06_04_221013_create_machine_types_table', 1),
(17, '2019_06_04_221133_create_machines_table', 1),
(18, '2019_06_04_235204_add_user_client_id_foreign_key', 1),
(19, '2019_06_05_142012_create_products_table', 1),
(20, '2019_06_05_152107_create_user_branches_table', 1),
(21, '2019_06_05_193718_create_branch_products_table', 1),
(22, '2019_06_06_072241_create_product_purchases_table', 1),
(23, '2019_06_09_161425_create_customers_table', 1),
(24, '2019_06_09_181043_create_transactions_table', 1),
(25, '2019_06_09_181123_create_product_transactions_table', 1),
(26, '2019_06_21_195323_add_user_active_branch_id_foreign_key', 1),
(27, '2019_06_23_210537_create_service_types_table', 1),
(28, '2019_06_23_211028_create_services_table', 1),
(29, '2019_06_23_212708_create_branch_services_table', 1),
(30, '2019_06_23_215253_create_service_transactions_table', 1),
(31, '2019_06_29_173041_create_completed_service_transactions_table', 1),
(32, '2019_06_29_174625_create_tokens_table', 1),
(33, '2019_06_30_170302_create_completed_product_transactions_table', 1),
(34, '2019_06_30_183508_create_transaction_payments_table', 1),
(35, '2019_07_12_115719_add_foreign_key_machine_customer_id', 1),
(36, '2019_07_13_180812_create_rfid_cards_table', 1),
(37, '2019_07_13_181109_create_rfid_service_prices_table', 1),
(38, '2019_07_13_182422_create_rfid_transactions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
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
('03175d61f16e572b6bf9ee847dad7281393b5cf75798175eee6d4881318a278fd0150ac72286ecc4', 3, 1, NULL, '[]', 0, '2019-07-15 01:13:40', '2019-07-15 01:13:40', '2020-07-15 09:13:40'),
('051a66f7118c23919b760050077ad867c5f74649839086e15a9e5d1fbd72b2c51cfdf063519a4871', 3, 1, NULL, '[]', 0, '2019-07-15 09:54:46', '2019-07-15 09:54:46', '2020-07-15 17:54:46'),
('05fe4d06b7f0b9ff969ac910b0b5400edb4860c18160dbf3c68bd2f872cfa75e1b34e67187060400', 3, 1, NULL, '[]', 0, '2019-07-15 02:50:05', '2019-07-15 02:50:05', '2020-07-15 10:50:05'),
('08213e3b96cf3b8a7a5a4eea69bf233d86eed24536e33458ce4320d7885dd3c08a89b6e1ec6dbcf0', 3, 1, NULL, '[]', 0, '2019-07-15 05:02:22', '2019-07-15 05:02:22', '2020-07-15 13:02:22'),
('09c25c0ec200c9d636b108d69fdd08af60fc549e9e16efa94db552de502bfc5eee68cfa6b06d2692', 3, 1, NULL, '[]', 0, '2019-07-15 06:47:06', '2019-07-15 06:47:06', '2020-07-15 14:47:06'),
('0a2db06531e38d7ce1b324720cf2442125bbbada79637dd1e4310a1e6ed4475e9b43b12316cda68e', 3, 1, NULL, '[]', 0, '2019-07-15 03:23:34', '2019-07-15 03:23:34', '2020-07-15 11:23:34'),
('0a8d601bc4b936b4167b5ebe7b2c298bb9eca99a69599db1562d973dd9e61541d2cee9032ea71ecb', 3, 1, NULL, '[]', 0, '2019-07-15 02:01:28', '2019-07-15 02:01:28', '2020-07-15 10:01:28'),
('0d5d3b027765b502d4ea4cb041980f3fe536d7964ab4fc364ba6511ff0f43d59c9ead65354547d28', 3, 1, NULL, '[]', 0, '2019-07-15 00:27:46', '2019-07-15 00:27:46', '2020-07-15 08:27:46'),
('0dfe353b63548a6cd8ff61fe87202bd80c314d5401b1845ad7f5b99b99647bbaeaa10814e87e7b2f', 3, 1, NULL, '[]', 0, '2019-07-15 05:32:27', '2019-07-15 05:32:27', '2020-07-15 13:32:27'),
('0e8bdeae76863f68656e2bfde371a5d82506d6a98e29c653f1704031f8960b00dba9a046789655df', 3, 1, NULL, '[]', 0, '2019-07-15 02:18:31', '2019-07-15 02:18:31', '2020-07-15 10:18:31'),
('0f89822e9cb5053f244a1d6ca8de551b27cef3f7eaf9f6bf2d67bc65a31529cb6af0aeb4deadffa3', 3, 1, NULL, '[]', 0, '2019-07-15 08:14:17', '2019-07-15 08:14:17', '2020-07-15 16:14:17'),
('100325d069bfdd366ab7d1cce5161f70430dc08f8947df3c13b8c3acea994f7f8cad7d7d9a402d14', 3, 1, NULL, '[]', 0, '2019-07-15 02:18:12', '2019-07-15 02:18:12', '2020-07-15 10:18:12'),
('1128d9963f7ab277ad3eca701ce2c621552fe3bee9514937077f03922915ad89109b5e8617fd5910', 3, 1, NULL, '[]', 0, '2019-07-15 07:50:28', '2019-07-15 07:50:28', '2020-07-15 15:50:28'),
('11d9fdabccfa290dfe729007eaa3d85b82b483a5dabd760a9fdfa173c851e6883eadf0f89bdaf9b8', 3, 1, NULL, '[]', 0, '2019-07-15 01:11:04', '2019-07-15 01:11:04', '2020-07-15 09:11:04'),
('13663916583ccc91c9e65e47420d0d0f68b726fb76b974f2524e25df783dfe02c6768b4fb5d7982c', 3, 1, NULL, '[]', 0, '2019-07-15 00:30:28', '2019-07-15 00:30:28', '2020-07-15 08:30:28'),
('1417351e700fd71fa300804c4ce432f11a318bd93f42d91af74dc25285bca18a09f2c963f92206a8', 3, 1, NULL, '[]', 0, '2019-07-15 01:46:07', '2019-07-15 01:46:07', '2020-07-15 09:46:07'),
('14fae24990524c74565a66f6ac622f5d6389a1e812ff67c9020283be8e6ff9bc8bf064bdf245cef0', 3, 1, NULL, '[]', 0, '2019-07-15 07:32:20', '2019-07-15 07:32:20', '2020-07-15 15:32:20'),
('1879aa4e9393bfe7ce84d48c652abbe7783bb4435198c6ef3aeec24d19bd9ce8dd91ed9b0c4b6823', 3, 1, NULL, '[]', 0, '2019-07-15 06:51:28', '2019-07-15 06:51:28', '2020-07-15 14:51:28'),
('18a3619c81cad6b0fc49340f76d416d25145c6fb33507b7b064ee5289e66ac94d9856e24a20c400d', 3, 1, NULL, '[]', 0, '2019-07-15 00:33:14', '2019-07-15 00:33:14', '2020-07-15 08:33:14'),
('18e0d87b5252fe95ca8a31dea15b11c19fb12ea58603e17472da70cadb80f35770a36ccbcdd02bf0', 3, 1, NULL, '[]', 0, '2019-07-15 04:53:11', '2019-07-15 04:53:11', '2020-07-15 12:53:11'),
('1af87dd4bec467826f8a74393da90bcf3bb69911b868832dcfcf477ea2ad2b8ecec9f83b406273fc', 3, 1, NULL, '[]', 0, '2019-07-15 08:25:46', '2019-07-15 08:25:46', '2020-07-15 16:25:46'),
('1b12ce630031da5d88fb768e259b21e5e3f0b2460d912f23b0ef39148777b99d5e062ec44e854221', 3, 1, NULL, '[]', 0, '2019-07-15 09:00:45', '2019-07-15 09:00:45', '2020-07-15 17:00:45'),
('1c76cf34f17dd460a0c62ee00cf8490e6f8788602c2897a10f55aebe0b46fafee7620e980dca25fd', 3, 1, NULL, '[]', 0, '2019-07-15 07:25:09', '2019-07-15 07:25:09', '2020-07-15 15:25:09'),
('1c92ff3d7eabeb1c78208e6ffc6de0a35ce1abd4a87ee0d5503d5a318c8b31af5b8aa5f4b1bb5b55', 3, 1, NULL, '[]', 0, '2019-07-15 08:37:18', '2019-07-15 08:37:18', '2020-07-15 16:37:18'),
('1ea9fc2e713f949a32761ab865a371e4ad1d9232e3ebee1c6333da4caa6426d9a73708d69f2c09df', 3, 1, NULL, '[]', 0, '2019-07-15 01:16:09', '2019-07-15 01:16:09', '2020-07-15 09:16:09'),
('1ed7afcaaeed529483daa229f193560f8ef350d3094038a97b8cb2a361f2bee0b45845714c009e94', 3, 1, NULL, '[]', 0, '2019-07-15 00:36:16', '2019-07-15 00:36:16', '2020-07-15 08:36:16'),
('1f37aaa902307f370ade851ec07ab1e027e7453c1b7d13a8d8e2e603acfe04a56f58a67caaa192c4', 3, 1, NULL, '[]', 0, '2019-07-15 07:57:50', '2019-07-15 07:57:50', '2020-07-15 15:57:50'),
('1fb09f82acbd6ef90fd39dd11393fa6697690b629ae216a9e3804714b6f9d7ee38f6cf7d9617c038', 3, 1, NULL, '[]', 0, '2019-07-15 06:46:42', '2019-07-15 06:46:42', '2020-07-15 14:46:42'),
('2227258e88ad81cf5cdce7ccfe069026bde41eaa4eb5016ee14f105ef9ba6a2f27b0bd00fd4eebdf', 3, 1, NULL, '[]', 0, '2019-07-15 02:22:16', '2019-07-15 02:22:16', '2020-07-15 10:22:16'),
('2261778ef379b39c6fdecbbf1318f173870843079924c6fdf5330316ff6bb7958dc52e595ef83507', 3, 1, NULL, '[]', 0, '2019-07-15 05:30:29', '2019-07-15 05:30:29', '2020-07-15 13:30:29'),
('23b076b804286941f8e08218672d9dd9490a0d199ec86f3865ef337d64fe55e1d68c739ee1c636ed', 3, 1, NULL, '[]', 0, '2019-07-15 07:12:43', '2019-07-15 07:12:43', '2020-07-15 15:12:43'),
('23caae81f93e800ceff5ccd1f49e2d2f84ce44e891e679c300d82ff89ce9ce758317547660c55ed6', 3, 1, NULL, '[]', 0, '2019-07-15 02:23:57', '2019-07-15 02:23:57', '2020-07-15 10:23:57'),
('241a27d82648d905f0d765e0cc582a5fcb58c2342dd9d156509c54a1a2fb1d8be9eacb101f759025', 3, 1, NULL, '[]', 0, '2019-07-15 08:33:01', '2019-07-15 08:33:01', '2020-07-15 16:33:01'),
('25dd4e2835c516b1a9030edecaf2f8c8ccb85924aa79dc7aa7345afd955cc67dc4187f9d1166a34c', 3, 1, NULL, '[]', 0, '2019-07-15 04:53:49', '2019-07-15 04:53:49', '2020-07-15 12:53:49'),
('273361814d0b492b5d7206f3e7aa25b2a599603a7f4ebd90c4c9bdfb4b78d3dbe147de2574cd131d', 3, 1, NULL, '[]', 0, '2019-07-14 23:50:54', '2019-07-14 23:50:54', '2020-07-15 07:50:54'),
('280c8398a2b057b5383f4c1ea91be3e7fd0de4b6336b16db53048d24160135ddc287668d7422b300', 3, 1, NULL, '[]', 0, '2019-07-15 05:29:09', '2019-07-15 05:29:09', '2020-07-15 13:29:09'),
('28eab64ab47e550eb04d6ce29fa9466f0070939bc9160a5e7259874f854f4be57f8b6fbd9c1391b5', 3, 1, NULL, '[]', 0, '2019-07-15 03:37:43', '2019-07-15 03:37:43', '2020-07-15 11:37:43'),
('2da7cafbe67fd772fc4374bcbbea9f85f4634219c1c09602bcd5f3b27c2a277a73c5f52123a3d4f3', 3, 1, NULL, '[]', 0, '2019-07-15 04:52:09', '2019-07-15 04:52:09', '2020-07-15 12:52:09'),
('3063a79acf45cdc8fa6b6bf66105e32c393c85ef7b6b666828e894a61628c55a3855d6911839a1bf', 3, 1, NULL, '[]', 0, '2019-07-15 06:33:14', '2019-07-15 06:33:14', '2020-07-15 14:33:14'),
('35d02d72f8c4822e3667c6cf727917270be66ce334ae09e05ae6908fa116e950e5e8189e7688f3ba', 3, 1, NULL, '[]', 0, '2019-07-15 09:45:39', '2019-07-15 09:45:39', '2020-07-15 17:45:39'),
('36bfca308688ecd16019981c6915d1cd28e16db3566242c4279223b85fb769c56a89b1fff9e951f5', 3, 1, NULL, '[]', 0, '2019-07-15 07:53:46', '2019-07-15 07:53:46', '2020-07-15 15:53:46'),
('377880ec66f077fb3e2bc32c4bc8989cb2e42f2cc0eecaa3f6814adf0bc8f4f47757f207b6e0a300', 3, 1, NULL, '[]', 0, '2019-07-15 07:59:51', '2019-07-15 07:59:51', '2020-07-15 15:59:51'),
('389bbe1d13afd9456868d8783b47144ee71ca56c20e8d2274a22ebba34ba84f7e3399c6d520e9355', 3, 1, NULL, '[]', 0, '2019-07-15 03:45:45', '2019-07-15 03:45:45', '2020-07-15 11:45:45'),
('3aa7202809ff552e99e70f05f7cc5b36bb14b20890397ad57cf8f2ccbda88591b378815c839c83f3', 3, 1, NULL, '[]', 0, '2019-07-15 00:33:35', '2019-07-15 00:33:35', '2020-07-15 08:33:35'),
('3b267fc883748ab33d8a3f5bacff576ec7d9b61a288996c8658c52690915d2e4e37c22ca2677794d', 3, 1, NULL, '[]', 0, '2019-07-15 01:21:02', '2019-07-15 01:21:02', '2020-07-15 09:21:02'),
('3c0e8b5d4a8597d821c841bdec8fad6651d7fc636b613a9c2c969229d3da1149dc7bbcabfc04411a', 3, 1, NULL, '[]', 0, '2019-07-15 05:07:00', '2019-07-15 05:07:00', '2020-07-15 13:07:00'),
('3ce9713563bf8ed87c96cf226bdc962d910cb1d6724362252407b0ca9ca778ca7cce399a97bef3ff', 3, 1, NULL, '[]', 0, '2019-07-15 07:58:44', '2019-07-15 07:58:44', '2020-07-15 15:58:44'),
('3e26d44c8980e8459bfe2b3a5ffb850d0e1561d1bf4058dfbc84bfe53bb8e4667c4aa2517271d573', 3, 1, NULL, '[]', 0, '2019-07-15 00:11:29', '2019-07-15 00:11:29', '2020-07-15 08:11:29'),
('3e4c3e5150b9e2711c8acfbcc38857a2f82343347e7b30565c6d20d8aa55d58b543a1239724b2f12', 3, 1, NULL, '[]', 0, '2019-07-15 02:30:56', '2019-07-15 02:30:56', '2020-07-15 10:30:56'),
('420b2bc19ad14c64728f37825bb3ebeb4c7f8f5bff5585221f65f42b2cb94f5fc60a330268bc1596', 3, 1, NULL, '[]', 0, '2019-07-15 02:00:04', '2019-07-15 02:00:04', '2020-07-15 10:00:04'),
('44b252510bfa5951f8329ad03da85a04a2a1089c9c2b3bb86eb609d0c8b763c0f1e3f439964cd2f6', 3, 1, NULL, '[]', 0, '2019-07-15 03:28:57', '2019-07-15 03:28:57', '2020-07-15 11:28:57'),
('458129f2da04b9ca0bdd90e71efbb6e6456f37a48284f2b0a3ad2b27bd4be6d3ae402b141bac16ab', 3, 1, NULL, '[]', 0, '2019-07-15 03:36:15', '2019-07-15 03:36:15', '2020-07-15 11:36:15'),
('458ad90cbc63ce27d9bd7b18e31b71e80faf4526feb5c77069400490ebca6ec2db1d5849ff409e28', 3, 1, NULL, '[]', 0, '2019-07-15 03:01:07', '2019-07-15 03:01:07', '2020-07-15 11:01:07'),
('45990bf833f555d3350efa11a393de948f34c82259fd8ee7334901f39d8fc0e080c0dc770ffd1148', 3, 1, NULL, '[]', 0, '2019-07-15 08:12:44', '2019-07-15 08:12:44', '2020-07-15 16:12:44'),
('469a69ec566fe84f73676dfe7dbd0dea9d932887ce2bf03daf37cff5fd86ea096b58f2bbf4b70a0a', 3, 1, NULL, '[]', 0, '2019-07-15 03:27:07', '2019-07-15 03:27:07', '2020-07-15 11:27:07'),
('495f641797e3427f116b9e06c9f0d65d47068cb08b48edf212a67d21f93232fa09e2e6d637129f31', 3, 1, NULL, '[]', 0, '2019-07-15 06:32:54', '2019-07-15 06:32:54', '2020-07-15 14:32:54'),
('496d7c10d65c5002d95975068e7e83402c99c08c8d0c5f756ed9d5739e682a506ac8e8efda07b038', 3, 1, NULL, '[]', 0, '2019-07-15 02:56:13', '2019-07-15 02:56:13', '2020-07-15 10:56:13'),
('510e850ffb6da5a03e8105339b08c6b6fde81f6be1e3a71d72aef29f63188cf2864d144967bcf357', 3, 1, NULL, '[]', 0, '2019-07-15 02:31:55', '2019-07-15 02:31:55', '2020-07-15 10:31:55'),
('5275d7519b5d883651e8fe02b5951c066f2a9075b69557c7354e247fd8807220ec1f4379ae897a35', 3, 1, NULL, '[]', 0, '2019-07-15 08:15:31', '2019-07-15 08:15:31', '2020-07-15 16:15:31'),
('5280a85a712ef06000d8b7e3a5f968eda2f251cf6bdd8bc9c5a3b370c6cf05b70e08536f3ec4edd8', 3, 1, NULL, '[]', 0, '2019-07-15 01:23:32', '2019-07-15 01:23:32', '2020-07-15 09:23:32'),
('5382d1749197de1a0f8375575a1b4f2d6346429adee0574dd8a2dbe947f831de891526d8a81e4277', 3, 1, NULL, '[]', 0, '2019-07-15 02:28:28', '2019-07-15 02:28:28', '2020-07-15 10:28:28'),
('53b48d342a480628f8d3bca05702647a18c713808a3ccb172b6116a66c098ee3ee1948502c515bfb', 3, 1, NULL, '[]', 0, '2019-07-15 01:41:09', '2019-07-15 01:41:09', '2020-07-15 09:41:09'),
('545a9380d8a9880b113b976d715d75c4c08b9bfd77ccd90d1430a943670d27aecea31310de335564', 3, 1, NULL, '[]', 0, '2019-07-15 06:32:33', '2019-07-15 06:32:33', '2020-07-15 14:32:33'),
('546d6ff72d60b99adcc6abd7ea8587311d949399cfe1b5d982325e78b694ef6b0bd9c8bc6d0e2b5f', 3, 1, NULL, '[]', 0, '2019-07-15 07:48:27', '2019-07-15 07:48:27', '2020-07-15 15:48:27'),
('574627081bb91b7dee1ab5ca988a564b2a14526b9016ddeb9d2f4986c600287db0cc31b72ef00bab', 3, 1, NULL, '[]', 0, '2019-07-15 01:24:32', '2019-07-15 01:24:32', '2020-07-15 09:24:32'),
('57ecce84b88d18fc81063d167cb945082f67cc2854be48c49ddda591111e400739827e8f6f1e44eb', 3, 1, NULL, '[]', 0, '2019-07-15 01:16:57', '2019-07-15 01:16:57', '2020-07-15 09:16:57'),
('58d92dc6eb60edab39134eaa19b86028bd5ce3f30da8526e4477f3e2c76a21061355db5693e35e20', 3, 1, NULL, '[]', 0, '2019-07-15 01:24:54', '2019-07-15 01:24:54', '2020-07-15 09:24:54'),
('594874a2c8d2a51122b20e549c2a6d12b9798c337561feab12423a19e61d1ea049083ce689394531', 3, 1, NULL, '[]', 0, '2019-07-15 00:35:15', '2019-07-15 00:35:15', '2020-07-15 08:35:15'),
('59ccadfd4b494e7e06208949e62cd6fecbfd20db2630f3b3fb0ef03391af6c486df84e7907fc4547', 3, 1, NULL, '[]', 0, '2019-07-15 07:12:53', '2019-07-15 07:12:53', '2020-07-15 15:12:53'),
('5b4fc8d9dc9734660bcc7597fa83dfada0ecb5b4a7544fe0f6e7777074c86b22db941f725a595a50', 3, 1, NULL, '[]', 0, '2019-07-15 09:36:32', '2019-07-15 09:36:32', '2020-07-15 17:36:32'),
('5bd2677f42881d1d4da475f2e01bb02f128802bd745055f17fac1aebb1d86cba824d99b2f0a1cdf8', 3, 1, NULL, '[]', 0, '2019-07-15 05:07:32', '2019-07-15 05:07:32', '2020-07-15 13:07:32'),
('5bdcd6deb5f6bb79fc5818038a5a72328ffbd583ffb19b1b6a742e730a3093d53f25bba0eb680345', 3, 1, NULL, '[]', 0, '2019-07-15 07:20:59', '2019-07-15 07:20:59', '2020-07-15 15:20:59'),
('5c9cbe6eb9951cf4a1a0ce1124c39fe0c7f550a2738427d4ba64cc4412a4bb427efefa97ef6615a3', 3, 1, NULL, '[]', 0, '2019-07-15 07:17:04', '2019-07-15 07:17:04', '2020-07-15 15:17:04'),
('5ef0088cd5b508d93afdc71c64d04103829a6fc0d35250c0ff71f9179001534fe884d2927227b324', 3, 1, NULL, '[]', 0, '2019-07-15 01:16:53', '2019-07-15 01:16:53', '2020-07-15 09:16:53'),
('61e728fecf3c373f051f93758d36abaf778b7f3b404a382e67db1c7add534bc82d7dc20158ac3886', 3, 1, NULL, '[]', 0, '2019-07-15 03:36:31', '2019-07-15 03:36:31', '2020-07-15 11:36:31'),
('61f4d08b02a2851800f65ef209327f583caae91551d10d3a10e5f3dde2c515d9d17862868446476f', 3, 1, NULL, '[]', 0, '2019-07-15 03:54:57', '2019-07-15 03:54:57', '2020-07-15 11:54:57'),
('62806d976f3200516af6f8c8e44473821408f589b85a90067da1c128bfc75117f5777b9cdbe360e2', 3, 1, NULL, '[]', 0, '2019-07-15 03:26:21', '2019-07-15 03:26:21', '2020-07-15 11:26:21'),
('62b5b2c5f8f8f5dad808eb2d7782c46d5c9393fdd2b090df78d359b501a0ccff4d1f9d9f64ae538f', 3, 1, NULL, '[]', 0, '2019-07-15 09:41:40', '2019-07-15 09:41:40', '2020-07-15 17:41:40'),
('66ae9db84a19d87d3cead1f10ce95d13f4b5096c39c8f0caa0d75b3dcb0f323d087c095d52f3f1e7', 3, 1, NULL, '[]', 0, '2019-07-15 06:26:07', '2019-07-15 06:26:07', '2020-07-15 14:26:07'),
('67a2b4678359b27f47a86ae1bfa3d8e64839ea7c98f274aef5ef04d4525c0a7166118d8f8b90a39f', 3, 1, NULL, '[]', 0, '2019-07-15 00:47:08', '2019-07-15 00:47:08', '2020-07-15 08:47:08'),
('6839090184676e4cf7c5fd88d0713cc8b3f21b5639239a6c6620e9bdbb9d8a1ba9789ce6e2096112', 3, 1, NULL, '[]', 0, '2019-07-15 06:25:33', '2019-07-15 06:25:33', '2020-07-15 14:25:33'),
('6979df25ad253b960729dda5bd99c6e34d35013f3a4e90fe68bf70e93b21d530afe70d18a0e479bf', 3, 1, NULL, '[]', 0, '2019-07-15 04:47:25', '2019-07-15 04:47:25', '2020-07-15 12:47:25'),
('6982118fc4dd0cbbf8e659908597722ee8204c15bb9bfcc93d045195a8bb0a1014e0806bd5dde35a', 3, 1, NULL, '[]', 0, '2019-07-15 01:57:59', '2019-07-15 01:57:59', '2020-07-15 09:57:59'),
('6a511b2054910c451b357226cc8460ff4bac8acb3b77d263e38bc4912c30fd8de17c927031f6b021', 3, 1, NULL, '[]', 0, '2019-07-15 08:26:31', '2019-07-15 08:26:31', '2020-07-15 16:26:31'),
('6e7732e4a55764d9ca4118197ca995e3415880b416a27a8d6c944878143e525e677854d6d9a8d484', 3, 1, NULL, '[]', 0, '2019-07-15 00:48:09', '2019-07-15 00:48:09', '2020-07-15 08:48:09'),
('6eeaa53d6f551f7d78c4b00b833f37f3ab4341f5d02468d4e332fa27a5062a696bb90907d56e2c18', 3, 1, NULL, '[]', 0, '2019-07-15 02:00:50', '2019-07-15 02:00:50', '2020-07-15 10:00:50'),
('725e2a35eb17d79effd84abb805d9dd09dc1539fcd824d0d42f5a3fef5fa4cba995558773b86a83b', 3, 1, NULL, '[]', 0, '2019-07-15 07:57:40', '2019-07-15 07:57:40', '2020-07-15 15:57:40'),
('73d188f20b07d8e153446b1967a317aa3ff874f4326b91968b81bff193da36a67fd67bb8a0b294c7', 3, 1, NULL, '[]', 0, '2019-07-15 02:59:10', '2019-07-15 02:59:10', '2020-07-15 10:59:10'),
('7412baab311c45d81272ced9a9da1fccaab42581f602c365fc0588030b395786adb8fabf8e2f16a0', 3, 1, NULL, '[]', 0, '2019-07-15 08:22:11', '2019-07-15 08:22:11', '2020-07-15 16:22:11'),
('745995aa4e116e2ebc01809a49709ce114e79d8000ea45a18c29e47231683fd285a2a1969370ba4b', 3, 1, NULL, '[]', 0, '2019-07-15 03:23:16', '2019-07-15 03:23:16', '2020-07-15 11:23:16'),
('75ea55da4d2e034f818732da6f6f50c391ad251064d89b7d884a643b4769e9137f114b9f7f2bffc4', 3, 1, NULL, '[]', 0, '2019-07-15 01:01:50', '2019-07-15 01:01:50', '2020-07-15 09:01:50'),
('774b41bce60f2fc738116058abad813136e5c6bd7b6b0e41d41880ba9da40ef53ae9bea5812d3314', 3, 1, NULL, '[]', 0, '2019-07-15 02:28:42', '2019-07-15 02:28:42', '2020-07-15 10:28:42'),
('79d5681ccd4a4a0f3fdcbd1117e8d27fa7f1896f754dac0c9364c9a1259aff8ab1494f99a1f54f35', 3, 1, NULL, '[]', 0, '2019-07-15 06:46:22', '2019-07-15 06:46:22', '2020-07-15 14:46:22'),
('7b9446ffcc3194cbab5610460f3732c4762af804183d73f5fc0d408642456bb06722fb694bcb795c', 3, 1, NULL, '[]', 0, '2019-07-15 01:17:44', '2019-07-15 01:17:44', '2020-07-15 09:17:44'),
('7cb17b254e1b8be8042e79e7b4d59a4d272bbfa3782cdec8c6a34842269afbf0857e6a4f2eaced76', 3, 1, NULL, '[]', 0, '2019-07-15 03:27:37', '2019-07-15 03:27:37', '2020-07-15 11:27:37'),
('7f5c558530c534577ae84d7433e4e4f680c13e2e064008dca0237b2e785cb5d7ec09136a13bc2026', 3, 1, NULL, '[]', 0, '2019-07-15 08:17:51', '2019-07-15 08:17:51', '2020-07-15 16:17:51'),
('8401492282429055c72fef75cb2a84dc95c4069d38d4a77d0c884bfa7707b27d5b4c7c61ec77cd76', 3, 1, NULL, '[]', 0, '2019-07-15 06:36:05', '2019-07-15 06:36:05', '2020-07-15 14:36:05'),
('85dcee2e36ac771bfb17522a0203495f0e8180b0acb2fe12b3d0ac9e5e961cb297ad30bcd49a4986', 3, 1, NULL, '[]', 0, '2019-07-15 00:47:52', '2019-07-15 00:47:52', '2020-07-15 08:47:52'),
('86352614d747d7979c07e3d6e8059590c3160346f18b91fc60dca68ba2f760f7e84d1115f8a3ea5e', 3, 1, NULL, '[]', 0, '2019-07-15 07:49:08', '2019-07-15 07:49:08', '2020-07-15 15:49:08'),
('86394ba5d09f4c0685ca894eb718789010be02b57589a0a484d2091da9901800a5adf6f6b91a741f', 3, 1, NULL, '[]', 0, '2019-07-15 00:17:52', '2019-07-15 00:17:52', '2020-07-15 08:17:52'),
('8743b7a23e422aa4b11865509a2a63863c642ed7c51b3b9231be948f5a7b1ad36a6d06e3ac801318', 3, 1, NULL, '[]', 0, '2019-07-15 02:51:48', '2019-07-15 02:51:48', '2020-07-15 10:51:48'),
('8846664ceef15a5c61da4c3009fca56ef9b889c8dc91daa214085d7ea09202b66f6fa80774b0f4f2', 3, 1, NULL, '[]', 0, '2019-07-15 01:06:25', '2019-07-15 01:06:25', '2020-07-15 09:06:25'),
('891d76659f815cacf38217c14735f2b7463c4b24b51fbc6212648631bf8532d61ee3720f3f080797', 3, 1, NULL, '[]', 0, '2019-07-15 05:53:53', '2019-07-15 05:53:53', '2020-07-15 13:53:53'),
('89da69e9ec65dc3c5eb1d02124871775497917b89bc5547bd6564a342200405d0867b1649e664cfc', 3, 1, NULL, '[]', 0, '2019-07-15 01:12:26', '2019-07-15 01:12:26', '2020-07-15 09:12:26'),
('8b43a57a8949fa5a6c09d663617c164c4319ed19a5b3c810701f8c854994ef2f0cbdbb6cb0388488', 3, 1, NULL, '[]', 0, '2019-07-15 08:16:19', '2019-07-15 08:16:19', '2020-07-15 16:16:19'),
('8de9f34bcfb950c56034acfb8389c9a00bf974b66466fbacbf2e70c2a889ac72281f63cbf8ded5a8', 3, 1, NULL, '[]', 0, '2019-07-15 01:01:30', '2019-07-15 01:01:30', '2020-07-15 09:01:30'),
('90c8ad32f65add978431f8a965a7de00175e6a12d321458d710411b3a6baf985f7b0bbaac6549eb9', 3, 1, NULL, '[]', 0, '2019-07-15 00:36:00', '2019-07-15 00:36:00', '2020-07-15 08:36:00'),
('9989cc22a05e9b5dc4d59b1a9c829873dc1c53ecb6124315944227a36524a5f5eab1d19179e75f88', 3, 1, NULL, '[]', 0, '2019-07-15 01:19:23', '2019-07-15 01:19:23', '2020-07-15 09:19:23'),
('9dfebb7a4acb91aa6df5e5a97efc84b5866dcbe0442c0a9da0ebd229fcbecc51a457a32c3190020c', 3, 1, NULL, '[]', 0, '2019-07-15 06:55:10', '2019-07-15 06:55:10', '2020-07-15 14:55:10'),
('9f455c99f66c0201b518892dc2f1817e21bde5b1061232202ca108ac0f11519950d22864503bf57c', 3, 1, NULL, '[]', 0, '2019-07-15 07:46:46', '2019-07-15 07:46:46', '2020-07-15 15:46:46'),
('a0fe22bbe1e88106e44c78024e48f69e42141f917f6e8c6906e2f4ad122a146de8ce0347fd3d5bab', 3, 1, NULL, '[]', 0, '2019-07-15 00:32:11', '2019-07-15 00:32:11', '2020-07-15 08:32:11'),
('a2aec09e6b4f31825f25c3eaf006ecea891b543560664f70c529daf92fffe61e80be1dab8656cf71', 3, 1, NULL, '[]', 0, '2019-07-15 06:49:18', '2019-07-15 06:49:18', '2020-07-15 14:49:18'),
('a64719c3b54f1169ad2d2a00722a9cb607d7f4586fcb46607f2055a64b0d978613b144acde40e71f', 3, 1, NULL, '[]', 0, '2019-07-15 06:52:33', '2019-07-15 06:52:33', '2020-07-15 14:52:33'),
('a68adec17e9a241ce58f997d5cddaeceb844fad0f4b6d9c4c12fb98c7669906e364804fef8046375', 3, 1, NULL, '[]', 0, '2019-07-15 00:16:22', '2019-07-15 00:16:22', '2020-07-15 08:16:22'),
('a89d18f8cab679a7190043fd8602f3c760a9a26297e192665e639e5ef7c8323e4232cb584327625c', 3, 1, NULL, '[]', 0, '2019-07-15 01:42:00', '2019-07-15 01:42:00', '2020-07-15 09:42:00'),
('a98542e499a25e6331a31dfc6444a06b6e48fdb51469014608c1dd37401921ea963013500b7b84e2', 3, 1, NULL, '[]', 0, '2019-07-15 08:35:49', '2019-07-15 08:35:49', '2020-07-15 16:35:49'),
('a9f772ba59639ad3d039ce4867163b52be812c6e03580cc00b009ef9ebf6c04e4dc496eaf637d2c8', 3, 1, NULL, '[]', 0, '2019-07-15 01:56:31', '2019-07-15 01:56:31', '2020-07-15 09:56:31'),
('ac977179ff0377546143badb257d2a1c5f110e35929569ed9eac3bdb18407f3ab36b9046d87a4863', 3, 1, NULL, '[]', 0, '2019-07-15 02:09:36', '2019-07-15 02:09:36', '2020-07-15 10:09:36'),
('aef0c0e646bff977c2470380255ec44d399a0287d667ba48092f4829c9d31e71cddb1f7759e0e274', 3, 1, NULL, '[]', 0, '2019-07-15 00:33:55', '2019-07-15 00:33:55', '2020-07-15 08:33:55'),
('af68cb89b95290b7decc65395b34192d57d56abe35321fe7c0ab0b064fa5e454ef77473ee1c03264', 3, 1, NULL, '[]', 0, '2019-07-15 08:28:53', '2019-07-15 08:28:53', '2020-07-15 16:28:53'),
('b0b0e3ead8946468654d41ca046faf2c3552ccaf920e52ef00df15104fe4898b8ddc07d9185b4712', 3, 1, NULL, '[]', 0, '2019-07-15 07:52:56', '2019-07-15 07:52:56', '2020-07-15 15:52:56'),
('b193bc5e73be356f90ffa0b92b532565f2b5ed39f479e580d4658a0eade1e7a266140905894617d6', 3, 1, NULL, '[]', 0, '2019-07-15 13:40:54', '2019-07-15 13:40:54', '2020-07-15 21:40:54'),
('b19b06c2a02654b958f7be54f79e1dd8b11bc75fb0a77cbfbf76cf4691a03ac997b565c20acd8e23', 3, 1, NULL, '[]', 0, '2019-07-15 07:18:55', '2019-07-15 07:18:55', '2020-07-15 15:18:55'),
('b1ee2fb0dde1743ef1170d5c38cc18d099eda2d3a38dd0c7c9a083337670d1ff9030bdb04afc2207', 3, 1, NULL, '[]', 0, '2019-07-15 00:48:33', '2019-07-15 00:48:33', '2020-07-15 08:48:33'),
('b2fdbf91ed74b2937e1bc64d86746365ed765c27dc2bdac5e4f1bc71fac7b02d8497d04fbcefc3d9', 3, 1, NULL, '[]', 0, '2019-07-15 04:54:42', '2019-07-15 04:54:42', '2020-07-15 12:54:42'),
('b8b3ae241965a5055f945e47c35ed87f3845f3f9e648d839fc83cc6ece5946a515cd3cdee13288a8', 3, 1, NULL, '[]', 0, '2019-07-15 03:30:46', '2019-07-15 03:30:46', '2020-07-15 11:30:46'),
('b941da016ef05ee72bfb039700a5641e2cceb03cb1d20700c0d3c717517d22baff43b8e98d0b39a1', 3, 1, NULL, '[]', 0, '2019-07-15 02:08:36', '2019-07-15 02:08:36', '2020-07-15 10:08:36'),
('b97750d6f99de196941ed5401fe09c0670eb60c456c1050b5319131e8682cbeae4627db2e3e9e297', 3, 1, NULL, '[]', 0, '2019-07-15 01:18:48', '2019-07-15 01:18:48', '2020-07-15 09:18:48'),
('bcbde520d7f71ad739b51a705ac3fcbfad9a5c9680d12091d322bae43636a3e7c7ce15222ffd3629', 3, 1, NULL, '[]', 0, '2019-07-15 01:16:15', '2019-07-15 01:16:15', '2020-07-15 09:16:15'),
('bdf55a967b545c3a6c151e90777e0f407fc77131d930e569aa16b06e54b4218b0434b3abeee1726b', 3, 1, NULL, '[]', 0, '2019-07-15 07:40:03', '2019-07-15 07:40:03', '2020-07-15 15:40:03'),
('be2d0b58c10e5409bc7ea704deef983aaba92ee7e61064f4388be0286c13a45b40b1d02324d77064', 3, 1, NULL, '[]', 0, '2019-07-14 23:49:56', '2019-07-14 23:49:56', '2020-07-15 07:49:56'),
('bf8f11888da984f3503ca1f75718dbbd052134e4fbcece9ac8f7ea88b48aa8987c19accf57e5f512', 3, 1, NULL, '[]', 0, '2019-07-15 08:28:58', '2019-07-15 08:28:58', '2020-07-15 16:28:58'),
('c24aeb8ec024282d95f334ef6ef22f546d361a409dc17cb3b155499a60b00cb9259fbba0d42fab8f', 3, 1, NULL, '[]', 0, '2019-07-14 23:53:16', '2019-07-14 23:53:16', '2020-07-15 07:53:16'),
('c33430d0858bc62c8903087e301d6064f3450576ae0b8f83c07b3c886625d65ac2ffb2457cdfcfcf', 3, 1, NULL, '[]', 0, '2019-07-14 23:59:00', '2019-07-14 23:59:00', '2020-07-15 07:59:00'),
('c50eea19397948aadb8078dbda34a8bc799e259dd947d8da6941102fc08bb828d5a8f6566c976bb3', 3, 1, NULL, '[]', 0, '2019-07-15 09:06:28', '2019-07-15 09:06:28', '2020-07-15 17:06:28'),
('c7b780bba7bb0f29aa9ea53176daef0d6f698cff108c022287db6c381b1bc796c7b67df50ced2897', 3, 1, NULL, '[]', 0, '2019-07-15 02:01:16', '2019-07-15 02:01:16', '2020-07-15 10:01:16'),
('c900dabf46c188a957c3cc1b010af8ffd69ede3cad165d961ca56fac3cee4172859581b2394e0dac', 3, 1, NULL, '[]', 0, '2019-07-15 03:18:10', '2019-07-15 03:18:10', '2020-07-15 11:18:10'),
('c98037b6e5e4c51999c2c381abf958b874ab18f424034e82e861170b9b89c76fb041e2ba6b6f57ea', 3, 1, NULL, '[]', 0, '2019-07-15 02:08:00', '2019-07-15 02:08:00', '2020-07-15 10:08:00'),
('ccc3d8b901c6b0fbc5caab794023fe313192fa434576e50ec5f25d57257000a3c11fa0c792ed4de0', 3, 1, NULL, '[]', 0, '2019-07-15 09:33:13', '2019-07-15 09:33:13', '2020-07-15 17:33:13'),
('cdc43d3b8f051645e0994693120e6d4edf77872f9e24d7992861cc3c85250f49cd8f4705fea6d276', 3, 1, NULL, '[]', 0, '2019-07-15 06:31:08', '2019-07-15 06:31:08', '2020-07-15 14:31:08'),
('d125409b784683f5aa801b5c2da2c2e421f29ebc03b4840bd5ea179a505820466f0520eea5bac9a1', 3, 1, NULL, '[]', 0, '2019-07-15 09:03:09', '2019-07-15 09:03:09', '2020-07-15 17:03:09'),
('d30deba132d6d57330b0a3eb14d1fd83b4b34fb45b98c0f9fdb212281fe79b04a62512dea1832ea6', 3, 1, NULL, '[]', 0, '2019-07-15 01:04:06', '2019-07-15 01:04:06', '2020-07-15 09:04:06'),
('d40ae522b931d0480caeb7611d12dec5d8380f265edb9159273bb14afb0d71cfa561a3d03c93fde2', 3, 1, NULL, '[]', 0, '2019-07-14 23:41:26', '2019-07-14 23:41:26', '2020-07-15 07:41:26'),
('d719c8a8bf7e50bb9029243e98ab788a9808928815c35258c3d621ce3ff81e00e0bcf8c3acc7da12', 3, 1, NULL, '[]', 0, '2019-07-15 02:30:24', '2019-07-15 02:30:24', '2020-07-15 10:30:24'),
('d81b7d452496b2e5fd8f7ae4887c881bdb940619881f85deafc359608b68058f95e756027d48ebf0', 3, 1, NULL, '[]', 0, '2019-07-15 09:00:30', '2019-07-15 09:00:30', '2020-07-15 17:00:30'),
('d8573686fa080d5443068ec9a1cc2b39a463d0985cc5f01bf443e6ef7d32c6a1ac5bc8105231f6ee', 3, 1, NULL, '[]', 0, '2019-07-14 23:48:34', '2019-07-14 23:48:34', '2020-07-15 07:48:34'),
('dc7d7652efc1d64cc262150b49d8b8c9b0178691af3123dde2382063829ea34415123b5ff97dca14', 3, 1, NULL, '[]', 0, '2019-07-15 09:46:25', '2019-07-15 09:46:25', '2020-07-15 17:46:25'),
('dd3c68260252b3092e3334f04cbc8bafb2fccaf9fb98642c8da898618c9d672b5e6a1373bd58e7a6', 3, 1, NULL, '[]', 0, '2019-07-15 09:44:04', '2019-07-15 09:44:04', '2020-07-15 17:44:04'),
('dd7adbf2606a962ee68ef5c798779b4026df7232b5df3012b6a5b8ec3c8b66c56e310680555630a3', 3, 1, NULL, '[]', 0, '2019-07-15 06:47:57', '2019-07-15 06:47:57', '2020-07-15 14:47:57'),
('ddbdb4621656a623b3816b7473487e2b5ef029c26e9737138d581c06c0eee9611d8d5f461173f405', 3, 1, NULL, '[]', 0, '2019-07-15 07:19:11', '2019-07-15 07:19:11', '2020-07-15 15:19:11'),
('e11ce361bf101979ab949bb89c97cd88dbc288d0f8b3ef4d98832441dba33ba7d1723dc2cdef8bcf', 3, 1, NULL, '[]', 0, '2019-07-15 07:52:32', '2019-07-15 07:52:32', '2020-07-15 15:52:32'),
('e1b00fc0ef3cba75e519afff29957a998da9c62e99a1a77fb5094560bf1d8aa15ebbc12e769fb021', 3, 1, NULL, '[]', 0, '2019-07-15 09:46:52', '2019-07-15 09:46:52', '2020-07-15 17:46:52'),
('e21a28894fe5a064c255029695daa2cd34562657bc677fabc237c3fad993f71445049440d9203e4f', 3, 1, NULL, '[]', 0, '2019-07-15 00:14:28', '2019-07-15 00:14:28', '2020-07-15 08:14:28'),
('e280834e52ae892a0e39d0fc03ff6f45cc97b5f96ba729747565a089c4d5f638d79ae36d754aae70', 3, 1, NULL, '[]', 0, '2019-07-15 10:19:40', '2019-07-15 10:19:40', '2020-07-15 18:19:40'),
('e3d96f393898531c881c245fb385f3b2f86d069ce38f648783ed5b69d040e292d4e427edb955bd0c', 3, 1, NULL, '[]', 0, '2019-07-15 02:14:58', '2019-07-15 02:14:58', '2020-07-15 10:14:58'),
('e5221b578e968a14f7bb76dae4c0e40e67026601b66bd32683828e81673f5fe56d78493f42e20597', 3, 1, NULL, '[]', 0, '2019-07-15 02:16:09', '2019-07-15 02:16:09', '2020-07-15 10:16:09'),
('e6bbaa9de3b34c747e15113de9a2efb4bf771f13e284e8998fe1c41cdf8dce6aa150a90905481a68', 3, 1, NULL, '[]', 0, '2019-07-15 02:13:40', '2019-07-15 02:13:40', '2020-07-15 10:13:40'),
('e708e14080923a55eefbf767cc37a6cb5a8d560f2ed5be544f3f68f1457c0e82db46b8ecbb6088a5', 3, 1, NULL, '[]', 0, '2019-07-14 23:55:18', '2019-07-14 23:55:18', '2020-07-15 07:55:18'),
('e74d0e6e194ab397588da076e7b24486f662114cc8359a79fd526c8a32b64c2d4afd0156fee55a61', 3, 1, NULL, '[]', 0, '2019-07-15 00:17:21', '2019-07-15 00:17:21', '2020-07-15 08:17:21'),
('e94624192b0b0c6767f23541b694f3624d31be66094f2dca1dc0d136d07d0522aa699043f40f0dff', 3, 1, NULL, '[]', 0, '2019-07-15 00:13:45', '2019-07-15 00:13:45', '2020-07-15 08:13:45'),
('ec1b45afd12ebcb8ee762c999cbf9447aa6dd145b51444050fa9ff70861f6044c5d6d4d6f1143b88', 3, 1, NULL, '[]', 0, '2019-07-15 01:05:35', '2019-07-15 01:05:35', '2020-07-15 09:05:35'),
('ec2e5bc49e1348c73084e2218e697a8bf1006707dd0448e3c0f1fef0356502805c75dbccc08e7889', 3, 1, NULL, '[]', 0, '2019-07-15 03:17:46', '2019-07-15 03:17:46', '2020-07-15 11:17:46'),
('ed32a5ea16a576e130b33c76bd2c699dc5185e3b0e68739cc8f0805993ec46a274ce7fb5a0630398', 3, 1, NULL, '[]', 0, '2019-07-15 06:50:12', '2019-07-15 06:50:12', '2020-07-15 14:50:12'),
('eec5222625db731e484cbbb7d0fa0109c1203bec29416dce1d9f1a405d55054588f982d0bb301910', 3, 1, NULL, '[]', 0, '2019-07-15 02:54:32', '2019-07-15 02:54:32', '2020-07-15 10:54:32'),
('eee19e0f588f84b40a3f5fdec42c00f1b2a00b99db313727fdd0d98404ebd8bb4b442f270e37f69c', 3, 1, NULL, '[]', 0, '2019-07-15 01:58:55', '2019-07-15 01:58:55', '2020-07-15 09:58:55'),
('f22697517b5b8232b70347d91e2d397ba5771b8fd32d902016c31a45836d1f80ef006c64c45c7b57', 3, 1, NULL, '[]', 0, '2019-07-15 01:10:18', '2019-07-15 01:10:18', '2020-07-15 09:10:18'),
('f328ba92abc2ab9cafa2d63d9dab5b90561215410f16e50155ac446d13ab65f8d2f6715b4f5e510d', 3, 1, NULL, '[]', 0, '2019-07-15 05:06:37', '2019-07-15 05:06:37', '2020-07-15 13:06:37'),
('f341974823688997042a2ef510b5bd4b4e99a5b11a64d9fde644f8662ef6a632b1cca424577bda40', 3, 1, NULL, '[]', 0, '2019-07-15 01:10:39', '2019-07-15 01:10:39', '2020-07-15 09:10:39'),
('f3fe581ef39cc2521964b6a664de190bc0c1a5a007461c2ae40f272d62efeed2e5a55ea2c4cfb1c1', 3, 1, NULL, '[]', 0, '2019-07-15 09:48:30', '2019-07-15 09:48:30', '2020-07-15 17:48:30'),
('f48d7b7111b6be4dc8ef6269bc9048a6d3ce88fd82ab2ba7011b3d317245df8d553c326efc9c0855', 3, 1, NULL, '[]', 0, '2019-07-15 06:27:55', '2019-07-15 06:27:55', '2020-07-15 14:27:55'),
('f61f4ec75f1324d7a5cf5d55e653b07a38bc6ceb18adf024d988133fc83cd1e6f0308a2bd5c23c76', 3, 1, NULL, '[]', 0, '2019-07-15 00:12:44', '2019-07-15 00:12:44', '2020-07-15 08:12:44'),
('f80a90becd1dd730a17ce5b7bab8854928b5be54f0e26191efdd86779be38ef961ebe681747c9b1c', 3, 1, NULL, '[]', 0, '2019-07-15 01:12:45', '2019-07-15 01:12:45', '2020-07-15 09:12:45'),
('fbe7b963cfbe01130c5302059fcca1330ca6da73e4f4ad9daec348de3a1a612c3253f887d3662735', 3, 1, NULL, '[]', 0, '2019-07-15 01:13:35', '2019-07-15 01:13:35', '2020-07-15 09:13:35'),
('fe9b6c1f61c00dec261974033efb3fe17aae3389b2666bf25c867604034e63d057dcee1d5f4671c1', 3, 1, NULL, '[]', 0, '2019-07-14 23:55:48', '2019-07-14 23:55:48', '2020-07-15 07:55:48'),
('feb0aebcf4215a7fa5ed8beab8498ec2e63ac5f97e5a6df746c3c3bce8ce37f6b9c49a7628356fbd', 3, 1, NULL, '[]', 0, '2019-07-15 09:43:19', '2019-07-15 09:43:19', '2020-07-15 17:43:19'),
('ff6dfdf1c448f50797dab8149096a34fcf48281997696296a2b2dab8331a828b1c612020483506e8', 3, 1, NULL, '[]', 0, '2019-07-15 02:02:18', '2019-07-15 02:02:18', '2020-07-15 10:02:18');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
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
(1, NULL, 'csi', '09ptESlztAw7SekfrRPWAyu7VQqQzhj9NGMssnu7', 'http://localhost', 1, 0, 0, '2019-07-14 23:41:20', '2019-07-14 23:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2019-07-14 23:41:20', '2019-07-14 23:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

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

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `client_id`, `name`, `description`, `barcode`, `img_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Ariel', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 'Downy Passion', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 2, 'Surf Fabcon', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_purchases`
--

CREATE TABLE `product_purchases` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` datetime DEFAULT NULL,
  `branch_product_id` int(10) UNSIGNED DEFAULT NULL,
  `receipt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_cost` double DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_transactions`
--

CREATE TABLE `product_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `added_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'user id who added the transaction',
  `branch_product_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `price` double DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfid_cards`
--

CREATE TABLE `rfid_cards` (
  `id` int(10) UNSIGNED NOT NULL,
  `rfid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'If issued to customer',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'For master card',
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `card_type` char(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'c' COMMENT 'c = customer, u = user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfid_cards`
--

INSERT INTO `rfid_cards` (`id`, `rfid`, `balance`, `customer_id`, `user_id`, `client_id`, `branch_id`, `card_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'b5923d94', 100, 3, NULL, 2, 1, 'c', NULL, NULL, NULL),
(3, '89b2ca24', 1000, NULL, 2, 2, 1, 'u', NULL, NULL, NULL),
(4, '123456', NULL, NULL, NULL, 2, 1, 'u', '2019-07-15 02:18:05', '2019-07-15 02:18:05', NULL),
(5, '098766', NULL, NULL, NULL, 2, 1, 'u', '2019-07-15 02:19:51', '2019-07-15 02:19:51', NULL),
(6, '765', NULL, NULL, NULL, 2, 1, 'u', '2019-07-15 02:22:27', '2019-07-15 02:22:27', NULL),
(7, '65', 100, NULL, 2, 2, 1, 'u', '2019-07-15 02:28:38', '2019-07-15 02:28:38', NULL),
(8, '4832482', 400, NULL, 4, 2, 1, 'u', '2019-07-15 02:30:36', '2019-07-15 02:30:36', NULL),
(9, '654', 500, NULL, 3, 2, 1, 'u', '2019-07-15 02:32:06', '2019-07-15 02:32:06', NULL),
(10, '9876', 100, 2, NULL, 2, 1, 'all', '2019-07-15 05:00:11', '2019-07-15 05:00:11', NULL),
(11, '1', 1, 1, NULL, 2, 1, 'c', '2019-07-15 05:06:50', '2019-07-15 05:06:50', NULL),
(12, 'y', 100, 1, NULL, 2, 1, 'c', '2019-07-15 05:07:26', '2019-07-15 05:07:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rfid_service_prices`
--

CREATE TABLE `rfid_service_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `machine_type_id` int(10) UNSIGNED DEFAULT NULL,
  `price` double DEFAULT NULL COMMENT 'Price to be deducted after successful tap card',
  `minutes_per_pulse` int(11) DEFAULT NULL,
  `add_super_wash` int(11) NOT NULL DEFAULT '0',
  `pulse_count` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfid_service_prices`
--

INSERT INTO `rfid_service_prices` (`id`, `client_id`, `branch_id`, `name`, `machine_type_id`, `price`, `minutes_per_pulse`, `add_super_wash`, `pulse_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'Regular wash', 1, 60, 40, 0, 1, NULL, NULL, NULL),
(2, 2, 1, 'Regular dry', 2, 60, 10, 0, 4, NULL, NULL, NULL),
(3, 2, 1, 'Titan wash', 3, 80, 40, 0, 1, NULL, NULL, NULL),
(4, 2, 1, 'Titan dry', 4, 80, 10, 0, 4, NULL, NULL, NULL),
(5, 2, 1, 'Add super wash', 1, 20, 20, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rfid_transactions`
--

CREATE TABLE `rfid_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `machine_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `rfid_card_id` int(10) UNSIGNED DEFAULT NULL,
  `rfid_service_price_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfid_transactions`
--

INSERT INTO `rfid_transactions` (`id`, `client_id`, `branch_id`, `machine_id`, `user_id`, `rfid_card_id`, `rfid_service_price_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 3, NULL, 1, 1, NULL, '2019-07-14 23:55:10', '2019-07-14 23:55:10'),
(2, 2, 1, 4, NULL, 1, 2, NULL, '2019-07-14 23:58:54', '2019-07-14 23:58:54'),
(3, 2, 1, 4, NULL, 1, 2, NULL, '2019-07-15 00:33:49', '2019-07-15 00:33:49'),
(4, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 00:35:02', '2019-07-15 00:35:02'),
(5, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 00:35:11', '2019-07-15 00:35:11'),
(6, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 02:48:30', '2019-07-15 02:48:30'),
(7, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 02:54:39', '2019-07-15 02:54:39'),
(8, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 02:54:52', '2019-07-15 02:54:52'),
(9, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 02:56:26', '2019-07-15 02:56:26'),
(10, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 02:56:50', '2019-07-15 02:56:50'),
(11, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 02:57:24', '2019-07-15 02:57:24'),
(12, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:00:36', '2019-07-15 03:00:36'),
(13, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:02:14', '2019-07-15 03:02:14'),
(14, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:04:48', '2019-07-15 03:04:48'),
(15, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:04:56', '2019-07-15 03:04:56'),
(16, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:05:02', '2019-07-15 03:05:02'),
(17, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:05:09', '2019-07-15 03:05:09'),
(18, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:05:39', '2019-07-15 03:05:39'),
(19, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 03:06:32', '2019-07-15 03:06:32'),
(20, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:18:17', '2019-07-15 03:18:17'),
(21, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 03:18:31', '2019-07-15 03:18:31'),
(22, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:23:50', '2019-07-15 03:23:50'),
(23, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 03:24:09', '2019-07-15 03:24:09'),
(24, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:26:28', '2019-07-15 03:26:28'),
(25, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:29:05', '2019-07-15 03:29:05'),
(26, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 03:29:24', '2019-07-15 03:29:24'),
(27, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:36:56', '2019-07-15 03:36:56'),
(28, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 03:38:05', '2019-07-15 03:38:05'),
(29, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 05:28:25', '2019-07-15 05:28:25'),
(30, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 05:29:49', '2019-07-15 05:29:49'),
(31, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 05:37:34', '2019-07-15 05:37:34'),
(32, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:50:40', '2019-07-15 05:50:40'),
(33, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:52:48', '2019-07-15 05:52:48'),
(34, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:53:05', '2019-07-15 05:53:05'),
(35, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:53:29', '2019-07-15 05:53:29'),
(36, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:54:00', '2019-07-15 05:54:00'),
(37, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:54:27', '2019-07-15 05:54:27'),
(38, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:54:30', '2019-07-15 05:54:30'),
(39, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:54:35', '2019-07-15 05:54:35'),
(40, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 05:54:48', '2019-07-15 05:54:48'),
(41, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 05:55:31', '2019-07-15 05:55:31'),
(42, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:57:42', '2019-07-15 05:57:42'),
(43, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:58:16', '2019-07-15 05:58:16'),
(44, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:58:30', '2019-07-15 05:58:30'),
(45, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 05:58:38', '2019-07-15 05:58:38'),
(46, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:36:09', '2019-07-15 06:36:09'),
(47, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 06:36:45', '2019-07-15 06:36:45'),
(48, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:47:28', '2019-07-15 06:47:28'),
(49, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:48:05', '2019-07-15 06:48:05'),
(50, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:49:26', '2019-07-15 06:49:26'),
(51, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:50:48', '2019-07-15 06:50:48'),
(52, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:52:10', '2019-07-15 06:52:10'),
(53, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:54:05', '2019-07-15 06:54:05'),
(54, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 06:54:17', '2019-07-15 06:54:17'),
(55, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 06:57:38', '2019-07-15 06:57:38'),
(56, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 07:32:45', '2019-07-15 07:32:45'),
(57, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 07:32:51', '2019-07-15 07:32:51'),
(58, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:52:05', '2019-07-15 07:52:05'),
(59, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:52:11', '2019-07-15 07:52:11'),
(60, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:52:14', '2019-07-15 07:52:14'),
(61, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:52:16', '2019-07-15 07:52:16'),
(62, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 07:58:15', '2019-07-15 07:58:15'),
(63, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 07:58:24', '2019-07-15 07:58:24'),
(64, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:58:49', '2019-07-15 07:58:49'),
(65, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:58:57', '2019-07-15 07:58:57'),
(66, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:59:01', '2019-07-15 07:59:01'),
(67, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 07:59:04', '2019-07-15 07:59:04'),
(68, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:16:55', '2019-07-15 08:16:55'),
(69, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 08:17:16', '2019-07-15 08:17:16'),
(70, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:25:59', '2019-07-15 08:25:59'),
(71, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 08:26:05', '2019-07-15 08:26:05'),
(72, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:26:52', '2019-07-15 08:26:52'),
(73, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 08:27:01', '2019-07-15 08:27:01'),
(74, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:28:23', '2019-07-15 08:28:23'),
(75, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:29:04', '2019-07-15 08:29:04'),
(76, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:31:15', '2019-07-15 08:31:15'),
(77, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:31:53', '2019-07-15 08:31:53'),
(78, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:32:23', '2019-07-15 08:32:23'),
(79, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:32:41', '2019-07-15 08:32:41'),
(80, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:32:53', '2019-07-15 08:32:53'),
(81, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:33:21', '2019-07-15 08:33:21'),
(82, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:35:52', '2019-07-15 08:35:52'),
(83, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 08:36:00', '2019-07-15 08:36:00'),
(84, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:23', '2019-07-15 08:36:23'),
(85, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:29', '2019-07-15 08:36:29'),
(86, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:31', '2019-07-15 08:36:31'),
(87, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:32', '2019-07-15 08:36:32'),
(88, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:34', '2019-07-15 08:36:34'),
(89, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:37', '2019-07-15 08:36:37'),
(90, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:40', '2019-07-15 08:36:40'),
(91, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:47', '2019-07-15 08:36:47'),
(92, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 08:36:48', '2019-07-15 08:36:48'),
(93, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 08:37:24', '2019-07-15 08:37:24'),
(94, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 08:37:30', '2019-07-15 08:37:30'),
(95, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 09:00:57', '2019-07-15 09:00:57'),
(96, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:01:01', '2019-07-15 09:01:01'),
(97, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 09:01:51', '2019-07-15 09:01:51'),
(98, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:02:05', '2019-07-15 09:02:05'),
(99, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:05:15', '2019-07-15 09:05:15'),
(100, 2, 1, 1, NULL, 1, 1, NULL, '2019-07-15 09:57:45', '2019-07-15 09:57:45'),
(101, 2, 1, 1, NULL, 1, 5, NULL, '2019-07-15 09:58:20', '2019-07-15 09:58:20'),
(102, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:59:34', '2019-07-15 09:59:34'),
(103, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:59:55', '2019-07-15 09:59:55'),
(104, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 09:59:58', '2019-07-15 09:59:58'),
(105, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 10:00:00', '2019-07-15 10:00:00'),
(106, 2, 1, 2, NULL, 1, 2, NULL, '2019-07-15 10:00:02', '2019-07-15 10:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `level`, `deleted_at`) VALUES
(1, 'developer', 'Mostly for backoffice', 'developer', NULL),
(2, 'admin', 'This is awesome', 'client', NULL),
(3, 'oic', 'This is still awesome', 'user', NULL),
(4, 'staff', 'Not so awesome', 'user', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `service_type_id` int(10) UNSIGNED DEFAULT NULL,
  `add_super_wash` int(11) NOT NULL DEFAULT '0',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `client_id`, `service_type_id`, `add_super_wash`, `name`, `description`, `barcode`, `img_path`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 0, 'Regular Wash', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 2, 0, 'Regular Dry', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 2, 5, 0, 'Fold', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 2, 0, 'Extra dry', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 1, 0, 'Add super wash', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 2, 2, 0, 'Free dry', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 2, 1, 0, 'Free wash', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_transactions`
--

CREATE TABLE `service_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `added_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'user id who added the transaction',
  `branch_service_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `service_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'full | self',
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `unit_price` double DEFAULT NULL COMMENT 'Fill only when saving the transaction',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_transactions`
--

INSERT INTO `service_transactions` (`id`, `transaction_id`, `added_by`, `branch_service_id`, `quantity`, `service_group`, `saved`, `unit_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 2, 105, 'full', 1, 65, NULL, '2019-07-15 00:00:48', '2019-07-15 08:02:19'),
(2, 2, 3, 5, 2, 'full', 1, 80, NULL, '2019-07-15 00:02:15', '2019-07-15 07:16:52'),
(3, 1, 3, 1, 17, 'full', 1, 65, NULL, '2019-07-15 02:52:25', '2019-07-15 07:13:18'),
(4, 1, 3, 3, 1, 'full', 1, 30, NULL, '2019-07-15 06:44:50', '2019-07-15 07:13:18'),
(5, 1, 3, 5, 100, 'full', 1, 80, NULL, '2019-07-15 07:19:37', '2019-07-15 07:19:40'),
(6, 1, 3, 4, 12, 'full', 1, 15, NULL, '2019-07-15 07:47:44', '2019-07-15 07:47:46'),
(7, 3, 3, 1, 2, 'full', 1, 65, NULL, '2019-07-15 09:50:42', '2019-07-15 09:50:51'),
(8, 3, 3, 2, 2, 'full', 1, 65, NULL, '2019-07-15 09:50:47', '2019-07-15 09:50:51'),
(9, 3, 3, 4, 1, 'full', 1, 15, NULL, '2019-07-15 09:53:16', '2019-07-15 09:53:19'),
(10, 4, 3, 1, 1, 'full', 1, 65, NULL, '2019-07-15 10:22:13', '2019-07-15 10:22:19'),
(11, 4, 3, 2, 1, 'full', 1, 65, NULL, '2019-07-15 10:22:16', '2019-07-15 10:22:19');

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE `service_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_types`
--

INSERT INTO `service_types` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Washer', 'Regular LG washer', NULL, NULL),
(2, 'Dryer', 'Regular LG dryer', NULL, NULL),
(3, 'Titan Washer', 'Titan LG washer', NULL, NULL),
(4, 'Titan Dryer', 'Titan LG dryer', NULL, NULL),
(5, 'Other', 'Includes folding, ironing and any unrelated to washers and dryers', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Branch where it was purchased',
  `service_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `completed_service_transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `pulse_count` int(11) DEFAULT NULL,
  `add_super_wash` int(11) NOT NULL DEFAULT '0' COMMENT 'Minutes to be added to regular wash',
  `minutes_per_pulse` double DEFAULT NULL,
  `time_activated` timestamp NULL DEFAULT NULL,
  `expires_in` timestamp NULL DEFAULT NULL,
  `machine_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Machine used',
  `service_type_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Washer, Dryer, Titan Washer, Titan Dryer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` datetime DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `paid_to` int(10) UNSIGNED DEFAULT NULL COMMENT 'user who received the payment',
  `job_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_paid` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `date`, `customer_id`, `branch_id`, `paid_to`, `job_order`, `date_paid`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '2019-07-15 08:00:48', 1, 1, NULL, NULL, NULL, NULL, '2019-07-15 00:00:48', '2019-07-15 00:00:48'),
(2, '2019-07-15 08:02:15', 2, 1, 3, NULL, '2019-07-15 17:46:47', NULL, '2019-07-15 00:02:15', '2019-07-15 09:46:47'),
(3, '2019-07-15 17:50:42', 2, 1, NULL, NULL, NULL, NULL, '2019-07-15 09:50:42', '2019-07-15 09:50:42'),
(4, '2019-07-15 18:22:13', 3, 1, NULL, NULL, NULL, NULL, '2019-07-15 10:22:13', '2019-07-15 10:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_payments`
--

CREATE TABLE `transaction_payments` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cash` double DEFAULT NULL,
  `points` double DEFAULT NULL COMMENT 'Customer loyalty points used',
  `discount` double DEFAULT NULL COMMENT 'Percentage',
  `total_amount` double DEFAULT NULL,
  `change` double DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Bantay',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_payments`
--

INSERT INTO `transaction_payments` (`transaction_id`, `customer_id`, `date`, `cash`, `points`, `discount`, `total_amount`, `change`, `user_id`, `deleted_at`) VALUES
(2, 2, '2019-07-15 09:46:47', 0, NULL, NULL, 160, -160, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middlename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barangay_id` int(10) UNSIGNED DEFAULT NULL,
  `city_municipality_id` int(10) UNSIGNED DEFAULT NULL,
  `province_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `active_branch_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lastname`, `firstname`, `middlename`, `contact_number`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `address`, `barangay_id`, `city_municipality_id`, `province_id`, `deleted_at`, `client_id`, `active_branch_id`) VALUES
(1, 'Golez', 'Princes Grace', NULL, NULL, 'developer@gmail.com', NULL, '$2y$10$WSxo4CUK2TSvwb/tvo.tvOxOTg9kQ7CD99loIqShe.OwHmeBNmNkC', NULL, NULL, '2019-07-14 14:06:50', NULL, NULL, NULL, NULL, NULL, 2, NULL),
(2, 'Luciano', 'Marideth', NULL, NULL, 'admin@gmail.com', NULL, '$2y$10$Uie2ZhCLzNXbHHj7Gr49GuDHgoSe.Jm/Nu2QwU.knBEAXKTBaxjr.', NULL, NULL, '2019-07-14 14:06:50', NULL, NULL, NULL, NULL, NULL, 2, NULL),
(3, 'Ballad', 'Jesseca', NULL, NULL, 'oic@gmail.com', NULL, '$2y$10$BYnAHkjtiBLQq.ma/edUrOBz13u9M0iIgUBkcvHRuzIJFJdR0ZIgu', NULL, NULL, '2019-07-14 23:40:35', NULL, NULL, NULL, NULL, NULL, 2, 1),
(4, 'Manalo', 'Jasmin Joy', NULL, NULL, 'staff@gmail.com', NULL, '$2y$10$TRQKTbB6fUmrAvfr9WMj5eFMCMJ9Oo9x7vLyduBl62skenYmR/qAW', NULL, NULL, '2019-07-14 14:06:50', NULL, NULL, NULL, NULL, NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_branches`
--

CREATE TABLE `user_branches` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_branches`
--

INSERT INTO `user_branches` (`user_id`, `branch_id`) VALUES
(2, 1),
(3, 1),
(4, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branches_client_id_foreign` (`client_id`),
  ADD KEY `branches_municipality_id_foreign` (`municipality_id`);

--
-- Indexes for table `branch_products`
--
ALTER TABLE `branch_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_products_product_id_foreign` (`product_id`),
  ADD KEY `branch_products_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `branch_services`
--
ALTER TABLE `branch_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_services_service_id_foreign` (`service_id`),
  ADD KEY `branch_services_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `city_municipalities`
--
ALTER TABLE `city_municipalities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD KEY `clients_user_id_foreign` (`user_id`);

--
-- Indexes for table `completed_product_transactions`
--
ALTER TABLE `completed_product_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `completed_product_transactions_customer_id_foreign` (`customer_id`),
  ADD KEY `completed_product_transactions_branch_id_foreign` (`branch_id`),
  ADD KEY `completed_product_transactions_product_id_foreign` (`product_id`),
  ADD KEY `completed_product_transactions_user_id_foreign` (`user_id`),
  ADD KEY `completed_product_transactions_branch_product_id_foreign` (`branch_product_id`),
  ADD KEY `completed_product_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `completed_product_transactions_product_transaction_id_foreign` (`product_transaction_id`);

--
-- Indexes for table `completed_service_transactions`
--
ALTER TABLE `completed_service_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `completed_service_transactions_branch_service_id_foreign` (`branch_service_id`),
  ADD KEY `completed_service_transactions_customer_id_foreign` (`customer_id`),
  ADD KEY `completed_service_transactions_branch_id_foreign` (`branch_id`),
  ADD KEY `completed_service_transactions_service_id_foreign` (`service_id`),
  ADD KEY `completed_service_transactions_service_type_id_foreign` (`service_type_id`),
  ADD KEY `completed_service_transactions_user_id_foreign` (`user_id`),
  ADD KEY `completed_service_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `completed_service_transactions_service_transaction_id_foreign` (`service_transaction_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_client_id_foreign` (`client_id`);

--
-- Indexes for table `dealers`
--
ALTER TABLE `dealers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machines_client_id_foreign` (`client_id`),
  ADD KEY `machines_branch_id_foreign` (`branch_id`),
  ADD KEY `machines_machine_type_id_foreign` (`machine_type_id`),
  ADD KEY `machines_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `machine_types`
--
ALTER TABLE `machine_types`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_client_id_foreign` (`client_id`);

--
-- Indexes for table `product_purchases`
--
ALTER TABLE `product_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_purchases_branch_product_id_foreign` (`branch_product_id`);

--
-- Indexes for table `product_transactions`
--
ALTER TABLE `product_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_transactions_added_by_foreign` (`added_by`),
  ADD KEY `product_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `product_transactions_branch_product_id_foreign` (`branch_product_id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rfid_cards`
--
ALTER TABLE `rfid_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfid_cards_customer_id_foreign` (`customer_id`),
  ADD KEY `rfid_cards_user_id_foreign` (`user_id`),
  ADD KEY `rfid_cards_client_id_foreign` (`client_id`),
  ADD KEY `rfid_cards_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `rfid_service_prices`
--
ALTER TABLE `rfid_service_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfid_service_prices_machine_type_id_foreign` (`machine_type_id`),
  ADD KEY `rfid_service_prices_client_id_foreign` (`client_id`),
  ADD KEY `rfid_service_prices_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `rfid_transactions`
--
ALTER TABLE `rfid_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rfid_transactions_rfid_service_price_id_foreign` (`rfid_service_price_id`),
  ADD KEY `rfid_transactions_rfid_card_id_foreign` (`rfid_card_id`),
  ADD KEY `rfid_transactions_user_id_foreign` (`user_id`),
  ADD KEY `rfid_transactions_client_id_foreign` (`client_id`),
  ADD KEY `rfid_transactions_branch_id_foreign` (`branch_id`);

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
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_service_type_id_foreign` (`service_type_id`),
  ADD KEY `services_client_id_foreign` (`client_id`);

--
-- Indexes for table `service_transactions`
--
ALTER TABLE `service_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_transactions_added_by_foreign` (`added_by`),
  ADD KEY `service_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `service_transactions_branch_service_id_foreign` (`branch_service_id`);

--
-- Indexes for table `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tokens_machine_id_foreign` (`machine_id`),
  ADD KEY `tokens_customer_id_foreign` (`customer_id`),
  ADD KEY `tokens_branch_id_foreign` (`branch_id`),
  ADD KEY `tokens_service_transaction_id_foreign` (`service_transaction_id`),
  ADD KEY `tokens_completed_service_transaction_id_foreign` (`completed_service_transaction_id`),
  ADD KEY `tokens_service_type_id_foreign` (`service_type_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_paid_to_foreign` (`paid_to`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`),
  ADD KEY `transactions_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD KEY `transaction_payments_customer_id_foreign` (`customer_id`),
  ADD KEY `transaction_payments_user_id_foreign` (`user_id`),
  ADD KEY `transaction_payments_transaction_id_index` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_barangay_id_foreign` (`barangay_id`),
  ADD KEY `users_city_municipality_id_foreign` (`city_municipality_id`),
  ADD KEY `users_province_id_foreign` (`province_id`),
  ADD KEY `users_client_id_foreign` (`client_id`),
  ADD KEY `users_active_branch_id_foreign` (`active_branch_id`);

--
-- Indexes for table `user_branches`
--
ALTER TABLE `user_branches`
  ADD KEY `user_branches_user_id_foreign` (`user_id`),
  ADD KEY `user_branches_branch_id_foreign` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `branch_products`
--
ALTER TABLE `branch_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `branch_services`
--
ALTER TABLE `branch_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `city_municipalities`
--
ALTER TABLE `city_municipalities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `completed_product_transactions`
--
ALTER TABLE `completed_product_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `completed_service_transactions`
--
ALTER TABLE `completed_service_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `dealers`
--
ALTER TABLE `dealers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `machine_types`
--
ALTER TABLE `machine_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `product_purchases`
--
ALTER TABLE `product_purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_transactions`
--
ALTER TABLE `product_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rfid_cards`
--
ALTER TABLE `rfid_cards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `rfid_service_prices`
--
ALTER TABLE `rfid_service_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `rfid_transactions`
--
ALTER TABLE `rfid_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `service_transactions`
--
ALTER TABLE `service_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `city_municipalities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branch_products`
--
ALTER TABLE `branch_products`
  ADD CONSTRAINT `branch_products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_services`
--
ALTER TABLE `branch_services`
  ADD CONSTRAINT `branch_services_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `completed_product_transactions`
--
ALTER TABLE `completed_product_transactions`
  ADD CONSTRAINT `completed_product_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_product_transactions_branch_product_id_foreign` FOREIGN KEY (`branch_product_id`) REFERENCES `branch_products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_product_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_product_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_product_transactions_product_transaction_id_foreign` FOREIGN KEY (`product_transaction_id`) REFERENCES `product_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_product_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_product_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `completed_service_transactions`
--
ALTER TABLE `completed_service_transactions`
  ADD CONSTRAINT `completed_service_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_service_transactions_branch_service_id_foreign` FOREIGN KEY (`branch_service_id`) REFERENCES `branch_services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_service_transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_service_transactions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_service_transactions_service_transaction_id_foreign` FOREIGN KEY (`service_transaction_id`) REFERENCES `service_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_service_transactions_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `completed_service_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `completed_service_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `machines`
--
ALTER TABLE `machines`
  ADD CONSTRAINT `machines_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `machines_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `machines_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `machines_machine_type_id_foreign` FOREIGN KEY (`machine_type_id`) REFERENCES `machine_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `product_purchases`
--
ALTER TABLE `product_purchases`
  ADD CONSTRAINT `product_purchases_branch_product_id_foreign` FOREIGN KEY (`branch_product_id`) REFERENCES `branch_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_transactions`
--
ALTER TABLE `product_transactions`
  ADD CONSTRAINT `product_transactions_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_transactions_branch_product_id_foreign` FOREIGN KEY (`branch_product_id`) REFERENCES `branch_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rfid_cards`
--
ALTER TABLE `rfid_cards`
  ADD CONSTRAINT `rfid_cards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_cards_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_cards_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rfid_service_prices`
--
ALTER TABLE `rfid_service_prices`
  ADD CONSTRAINT `rfid_service_prices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_service_prices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_service_prices_machine_type_id_foreign` FOREIGN KEY (`machine_type_id`) REFERENCES `machine_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rfid_transactions`
--
ALTER TABLE `rfid_transactions`
  ADD CONSTRAINT `rfid_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_transactions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rfid_transactions_rfid_card_id_foreign` FOREIGN KEY (`rfid_card_id`) REFERENCES `rfid_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `rfid_transactions_rfid_service_price_id_foreign` FOREIGN KEY (`rfid_service_price_id`) REFERENCES `rfid_service_prices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `rfid_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `services_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_transactions`
--
ALTER TABLE `service_transactions`
  ADD CONSTRAINT `service_transactions_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_transactions_branch_service_id_foreign` FOREIGN KEY (`branch_service_id`) REFERENCES `branch_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tokens_completed_service_transaction_id_foreign` FOREIGN KEY (`completed_service_transaction_id`) REFERENCES `completed_service_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tokens_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tokens_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tokens_service_transaction_id_foreign` FOREIGN KEY (`service_transaction_id`) REFERENCES `service_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tokens_service_type_id_foreign` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_paid_to_foreign` FOREIGN KEY (`paid_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD CONSTRAINT `transaction_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaction_payments_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_active_branch_id_foreign` FOREIGN KEY (`active_branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_barangay_id_foreign` FOREIGN KEY (`barangay_id`) REFERENCES `barangays` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_city_municipality_id_foreign` FOREIGN KEY (`city_municipality_id`) REFERENCES `city_municipalities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_branches`
--
ALTER TABLE `user_branches`
  ADD CONSTRAINT `user_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_branches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
