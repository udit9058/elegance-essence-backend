-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 30, 2025 at 03:20 PM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elegance_essence`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_token_index` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(158, 'App\\Models\\SellerUser', 10, 'seller_token', 'e0171ccd12054e1da903eb99d8edbc62d3058ab9a2dfc76a5213a52660c67962', '[\"*\"]', '2025-07-30 02:50:26', NULL, '2025-07-30 01:26:25', '2025-07-30 02:50:26'),
(161, 'App\\Models\\User', 11, 'auth_token', '020d63d4c3efc1deaba5d82fe9a8a29dd01e02a0fff013c0f9f6f1ff1d1a8cd1', '[\"*\"]', '2025-07-30 05:04:21', NULL, '2025-07-30 05:04:05', '2025-07-30 05:04:21'),
(163, 'App\\Models\\User', 6, 'auth_token', 'ab92e200b4b243a28d2bcce28a23ba4ea3fcc14fe5fcad927c5f6997b0850164', '[\"*\"]', '2025-07-30 05:17:26', NULL, '2025-07-30 05:17:01', '2025-07-30 05:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `name`, `description`, `price`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(8, 10, 'Casual Summer Dress', 'A light and breezy cotton dress with floral patterns, ideal for summer outings. Features short sleeves and a knee-length hem for a relaxed fit.', 1499.00, 20, 'product_images/Eun9B2gELdS7Y90KmxCMxDbjFnWpkxBl1LTharbW.jpg', '2025-07-29 09:32:46', '2025-07-29 09:32:46'),
(7, 10, 'Elegant Evening Gown', 'A stunning floor-length gown with intricate beadwork and a flowing chiffon skirt, perfect for formal events or galas. Features a deep V-neck and fitted bodice.', 4999.00, 10, 'product_images/o3hBdgsDru9PqeahYFlNa03ZwqIXPgAnxa5EJqhf.jpg', '2025-07-29 09:32:11', '2025-07-29 09:32:11'),
(9, 12, 'Classic Leather Jacket', 'A timeless black leather jacket with a tailored fit, featuring zippered pockets and a notched lapel collar. Perfect for adding edge to any outfit.', 7999.00, 8, 'product_images/2tuASONMF26i2xp2W5PHTcNZTsgyTWZ8ks46M1b9.jpg', '2025-07-29 09:34:53', '2025-07-29 09:34:53'),
(10, 12, 'Bohemian Maxi Skirt', 'A vibrant, flowing maxi skirt with a mix of paisley and tribal prints. Pairs beautifully with a simple top for a boho-chic look.', 2499.00, 15, 'product_images/bBxL12yNp0DJGlziQbgUxkq3Xij14dF8LJrXTXIt.jpg', '2025-07-29 09:35:13', '2025-07-29 09:35:58'),
(11, 12, 'Formal Blazer', 'A sleek, navy blue blazer with a single-breasted design, ideal for professional settings or smart-casual events. Features a slim fit and two-button closure.', 3999.00, 12, 'product_images/w1bkWGbmIOBJLsRfHrokKyA25GNftT8RptdAzolL.jpg', '2025-07-29 09:35:44', '2025-07-29 09:35:44'),
(15, 10, 'red welwet', 'Red welwet elegant dress for women', 2500.00, 12, 'product_images/XtLOlATD0WU2zL6V1FvQgcKP33VJ5JK40sNM3PxU.jpg', '2025-07-30 05:16:32', '2025-07-30 05:16:32');

-- --------------------------------------------------------

--
-- Table structure for table `seller_users`
--

DROP TABLE IF EXISTS `seller_users`;
CREATE TABLE IF NOT EXISTS `seller_users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_duration` int DEFAULT NULL,
  `plan_price` decimal(8,2) DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_users`
--

INSERT INTO `seller_users` (`id`, `name`, `email`, `password`, `contact_number`, `profile_image`, `business_name`, `business_address`, `city`, `state`, `pincode`, `plan_type`, `plan_duration`, `plan_price`, `role`, `created_at`, `updated_at`) VALUES
(10, 'Mr. UDIT SHYAM SHARMA', 'uditsharma9058@gmail.com', '$2y$12$s4ai85mqNHTYjc5xFnFlIO9Yo5EVgkumRAXcwadQsNa7/GbGTcUNy', '08780675140', NULL, 'test', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, NULL, NULL, 'seller', '2025-07-29 04:28:15', '2025-07-29 04:28:15'),
(11, 'test1', 'test1@gmail.com', '$2y$12$5BgIRxbBD38e8Vi5RemSU.7rlhztjMJVWAUdLmeK3auDP1cFSpKt6', '1234567890', NULL, 'test1', 'test1', 'test', 'test', '1', NULL, NULL, NULL, 'seller', '2025-07-29 04:30:33', '2025-07-29 04:30:33'),
(12, 'Aditya Shyam sharma', 'adityashyamsharma@gmail.com', '$2y$12$/tJHsoMGawFXwva4cRHmNed6HNnzT9X3gbzqr9sqkmskB08MQbkoS', '6353077621', NULL, 'aditi collection', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, NULL, NULL, 'seller', '2025-07-29 09:33:48', '2025-07-29 09:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `role` enum('customer','seller','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `contact_number`, `profile_image`, `age`, `gender`, `address`, `city`, `state`, `pincode`, `remember_token`, `created_at`, `updated_at`, `otp`, `otp_expires_at`, `role`) VALUES
(5, 'Aditi Sharma', 'goldisharma6122130@gmail.com', NULL, '$2y$12$2TNtu0YlS3mWSkCfrry0x.9OVace8TEfmUKNMgQD6Ef551O68OYqu', '7874999619', 'profile_images/afLtfYGbWNRa6rlHC5QlK5NC1I43a8okzcZu446G.png', 26, 'female', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, '2025-07-22 13:46:47', '2025-07-22 13:46:47', NULL, NULL, 'customer'),
(6, 'Mr. UDIT SHYAM SHARMA', 'uditsharma9058@gmail.com', NULL, '$2y$12$QrqLi1edJKb5rTraDT9J4eTFvq0DKvpEORRj5YsKUiGNVCoQh9nKC', '8780675140', 'profile_images/msDVYosMAxKgc9LbypU4oQRWaxHnW5Hm5G7tZtXy.png', 22, 'male', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, '2025-07-23 06:55:38', '2025-07-27 04:27:20', NULL, NULL, 'customer'),
(7, 'shyam', 'shyamgyani1223@gmail.com', NULL, '$2y$12$7wsPgAwnHvB1wHTzhEXLX.g/1lW2CNrlS0nlYjf9gJyFxZvwGjHla', '8866445948', 'profile_images/ZEIFVWh8E15iBh6sADyppMku9pdFul7CD32NRYmH.jpg', 60, 'male', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, '2025-07-23 07:22:25', '2025-07-23 12:46:17', '818928', '2025-07-23 12:47:17', 'customer'),
(8, 'Bhumi Dabhi', 'bhumidabhi32@gmail.com', NULL, '$2y$12$cy.KJV0cTu.XoqIVz00ju.7U4JNsYv9ESFk/SnEYIsqRoUF8HVHdS', '9313888314', 'profile_images/M7x3LMWqF9flC370gSTXyXwusiBb9A5fOQ2XeHpo.jpg', 22, 'female', '43 phase 2 Maharshi Villa , Osra road , Bharuch', 'Bharuch', 'Gujarat', '392015', NULL, '2025-07-24 15:14:35', '2025-07-24 15:15:24', NULL, NULL, 'customer'),
(9, 'Aditya sharma', 'adityashyamsharma@gmail.com', NULL, '$2y$12$DjZ2VuRTPOv5iXzZHVeB8ejvPg5IjSMjLTUiZF.7mvLrLOpL60VTW', '6353077925', 'profile_images/QZzh1DDyta3Nq40e2KO0cWNkPEtC6R1TokLvemqC.jpg', 30, 'male', '43 phase 2 Maharshi Villa , Osra road , Bharuch', 'Bharuch', 'Gujarat', '392015', NULL, '2025-07-24 15:57:35', '2025-07-24 15:58:41', NULL, NULL, 'customer'),
(10, 'nikita', 'nikita@gmail.com', NULL, '$2y$12$R8sBVwNgXrVRXC9XCXDno.nBG3vEzHexsTs.KWMQDqN6rNPSmLDZa', '08780675140', 'profile_images/acKo78tvDIDVG8A5Sij9P7hUjIbhDq92FzZk66sp.png', 25, 'female', '28 HEENA PARK MAKTAMPUR BHARUCH', 'Bharuch', 'Gujarat', '392012', NULL, '2025-07-28 14:31:33', '2025-07-28 14:31:33', NULL, NULL, 'customer'),
(11, 'Devansh', 'mohan.devanshsharma@gmail.com', NULL, '$2y$12$95J./EC0l4x4hx9eDGeW3uqWYQedLGzQBMO4yzxs8jNMM18abR4wW', '8953891935', 'profile_images/kH4AjnOnAPSrmSHbAONTkqnyRnGnQ2ziS0w4DRvj.jpg', 20, 'male', 'PM yojna lucknow', 'lucknow', 'UP', '226014', NULL, '2025-07-30 10:33:02', '2025-07-30 10:33:02', NULL, NULL, 'customer');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
