-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 09:03 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `naos`
--

-- --------------------------------------------------------

--
-- Table structure for table `crop_config`
--

CREATE TABLE `crop_config` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `min_rainfall` int(11) DEFAULT 0,
  `max_rainfall` int(11) DEFAULT 9999,
  `min_temp` decimal(5,2) DEFAULT 0.00,
  `max_temp` decimal(5,2) DEFAULT 99.00,
  `drought_resistant` tinyint(1) DEFAULT 0,
  `current_price` decimal(10,2) DEFAULT NULL,
  `price_updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_config`
--

INSERT INTO `crop_config` (`id`, `crop_name`, `min_rainfall`, `max_rainfall`, `min_temp`, `max_temp`, `drought_resistant`, `current_price`, `price_updated_at`) VALUES
(1, 'maize', 30, 9999, '0.00', '99.00', 0, '520.00', '2026-01-09 19:13:50'),
(2, 'rice', 30, 9999, '0.00', '99.00', 0, '1300.00', '2026-01-09 19:13:50'),
(3, 'sorghum', 0, 50, '25.00', '99.00', 1, '450.00', '2026-01-09 19:13:50'),
(4, 'millet', 0, 50, '25.00', '99.00', 1, '400.00', '2026-01-09 19:13:50'),
(5, 'cowpeas', 0, 50, '25.00', '99.00', 1, '900.00', '2026-01-09 19:13:50'),
(6, 'beans', 20, 9999, '0.00', '99.00', 0, '820.00', '2026-01-09 19:13:50'),
(7, 'groundnuts', 20, 9999, '0.00', '99.00', 0, '1500.00', '2026-01-29 20:21:33'),
(8, 'cassava', 0, 9999, '0.00', '99.00', 1, '300.00', '2026-01-09 19:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `crop_timelines`
--

CREATE TABLE `crop_timelines` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `season_name` varchar(50) DEFAULT NULL,
  `recommended_plant_date` date DEFAULT NULL,
  `actual_plant_date` date DEFAULT NULL,
  `days_delayed` int(11) DEFAULT 0,
  `estimated_harvest_date` date DEFAULT NULL,
  `adjusted_harvest_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farm_activities`
--

CREATE TABLE `farm_activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `activity_type` enum('planted','weeded','fertilized','sprayed','harvested') NOT NULL,
  `date` date NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farm_activities`
--

INSERT INTO `farm_activities` (`id`, `user_id`, `crop_name`, `activity_type`, `date`, `quantity`, `unit`, `notes`, `created_at`) VALUES
(1, 1, 'beans', 'planted', '2026-02-09', NULL, '', '', '2026-02-19 15:01:36');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `timestamp`, `message`) VALUES
(1, NULL, '2026-01-09 12:37:30', 'Admin updated cowpeas price to 900 MWK/kg'),
(2, NULL, '2026-01-09 22:21:15', 'SMS to 0997079797: Your NAOS verification code is: 557509'),
(3, NULL, '2026-01-12 20:50:40', 'SMS to 0894970323: Your NAOS verification code is: 985403'),
(4, 4, '2026-01-29 22:21:33', 'Admin updated groundnuts price to 1500 MWK/kg'),
(5, NULL, '2026-01-29 22:24:09', 'SMS to 0991799034: Your NAOS verification code is: 784188'),
(6, NULL, '2026-02-04 07:56:29', 'SMS to 0994507855: Your NAOS verification code is: 983091'),
(7, NULL, '2026-02-06 15:44:15', 'SMS to 0894970328: Your NAOS verification code is: 257575'),
(8, NULL, '2026-02-11 23:40:38', 'SMS to 0998121260: Your NAOS verification code is: 977015'),
(9, NULL, '2026-02-13 22:33:09', 'SMS to 0997079797: Your NAOS verification code is: 855129'),
(10, NULL, '2026-02-14 10:34:29', '[Development] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 095616'),
(11, NULL, '2026-02-14 14:32:09', '[SMS8] [FAILED] SMS to 0997079797: Your NAOS verification code is: 151158 | Error: Unknown error'),
(12, NULL, '2026-02-14 14:32:17', '[SMS8] [FAILED] SMS to 0997079797: Your NAOS verification code is: 807549 | Error: Unknown error'),
(13, NULL, '2026-02-14 14:44:11', '[SMS8] [SUCCESS] SMS to 0894970323: Your NAOS verification code is: 780678'),
(14, NULL, '2026-02-14 14:48:11', '[SMS8] [SUCCESS] SMS to 0894970323: Your NAOS verification code is: 113180'),
(15, NULL, '2026-02-14 14:49:16', '[SMS8] [SUCCESS] SMS to 0894970323: Your NAOS verification code is: 232834'),
(16, NULL, '2026-02-14 14:55:56', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 212949'),
(17, NULL, '2026-02-14 14:57:21', '[SMS8] [SUCCESS] SMS to 0894970323: Your NAOS verification code is: 560771'),
(18, NULL, '2026-02-14 15:02:33', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 040536'),
(19, NULL, '2026-02-14 17:42:27', '[SMS8] [FAILED] SMS to 0993807482: Your NAOS verification code is: 607555 | Error: cURL error: Could not resolve host: app.sms8.io'),
(20, NULL, '2026-02-14 17:44:08', '[SMS8] [SUCCESS] SMS to 0993807482: Your NAOS verification code is: 441660'),
(21, NULL, '2026-02-18 14:47:09', '[SMS8] [SUCCESS] SMS to 0994308469: Your NAOS verification code is: 602617'),
(22, NULL, '2026-02-19 09:06:31', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 308680'),
(23, NULL, '2026-02-19 09:06:35', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 964555'),
(24, NULL, '2026-02-19 09:07:34', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 511143'),
(25, NULL, '2026-02-19 09:09:33', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 254439'),
(26, NULL, '2026-02-19 09:09:36', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 933615');

-- --------------------------------------------------------

--
-- Table structure for table `market_insights`
--

CREATE TABLE `market_insights` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `price_trend` enum('rising','falling','stable') NOT NULL,
  `trend_percentage` decimal(5,2) DEFAULT 0.00,
  `recommendation` text NOT NULL,
  `optimal_sell_window` varchar(50) DEFAULT NULL,
  `demand_level` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_price_history`
--

CREATE TABLE `market_price_history` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `market_price_history`
--

INSERT INTO `market_price_history` (`id`, `crop_name`, `price_per_kg`, `recorded_at`) VALUES
(1, 'cowpeas', '900.00', '2026-01-09 10:37:30'),
(2, 'groundnuts', '1500.00', '2026-01-29 20:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `message` text NOT NULL,
  `action_data` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) DEFAULT 0,
  `type` varchar(100) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'pending',
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `listing_id`, `type`, `quantity`, `price`, `status`, `payment_status`, `created_at`) VALUES
(13, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-02-11 18:35:49'),
(14, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-02-11 21:22:03'),
(15, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-02-11 21:22:54'),
(16, 2, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-02-11 22:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'MWK',
  `status` varchar(50) DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'card',
  `provider_ref` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produce_listings`
--

CREATE TABLE `produce_listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crop_id` int(11) DEFAULT NULL,
  `produce_type` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('available','sold') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produce_listings`
--

INSERT INTO `produce_listings` (`id`, `user_id`, `crop_id`, `produce_type`, `quantity`, `price`, `created_at`, `status`) VALUES
(4, 1, 1, 'Maize', '67.00', '8809.00', '2026-01-29 23:00:40', 'available'),
(5, 1, 6, 'beans', '100.00', '500.00', '2026-02-08 06:53:18', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_type` enum('farmer','buyer') DEFAULT 'farmer',
  `status` enum('active','inactive','expired') DEFAULT 'inactive',
  `plan_type` enum('monthly','yearly','trial') DEFAULT 'trial',
  `start_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `role_type`, `status`, `plan_type`, `start_date`, `expiry_date`, `created_at`, `updated_at`) VALUES
(1, 2, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-01-09 19:13:46', '2026-02-06 13:51:48'),
(2, 1, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-01-09 19:13:46', '2026-01-09 19:13:46'),
(3, 4, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-01-09 19:13:46', '2026-01-09 19:13:46'),
(5, 9, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-02-06 13:44:15', '2026-02-06 13:44:15'),
(6, 1, 'farmer', 'expired', 'monthly', '2026-02-08 06:52:57', '2025-03-08 06:52:57', '2026-02-08 04:52:57', '2026-02-11 18:42:28'),
(7, 11, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-14 13:02:29', '2026-02-14 13:02:29'),
(8, 12, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-14 15:42:27', '2026-02-14 15:42:27'),
(11, 14, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-02-17 06:05:14', '2026-02-17 06:05:14'),
(12, 14, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-17 06:05:14', '2026-02-17 06:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `updated_by`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 4, 'allow_registration', '1', '2026-01-29 20:33:23'),
(2, 4, 'default_lang', 'en', '2026-01-29 20:23:19'),
(3, 4, 'maintenance_mode', '0', '2026-02-21 19:08:21'),
(4, 4, 'system_name', 'Nyasa Agricultural Optimization System (NAOS)', '2026-02-21 11:10:27'),
(5, 4, 'subscription_amount', '50', '2026-02-21 11:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('farmer','buyer','admin') NOT NULL,
  `active_role` enum('farmer','buyer') DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `lang` varchar(10) DEFAULT 'en',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subscription_status` enum('active','inactive') DEFAULT 'inactive',
  `profile_picture` varchar(255) DEFAULT 'default_avatar.png',
  `is_phone_verified` tinyint(1) DEFAULT 0,
  `phone_verification_code` varchar(6) DEFAULT NULL,
  `is_profile_complete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `phone_number`, `password_hash`, `role`, `active_role`, `gender`, `location`, `lang`, `is_active`, `created_at`, `updated_at`, `subscription_status`, `profile_picture`, `is_phone_verified`, `phone_verification_code`, `is_profile_complete`) VALUES
(1, 'Albert', '0880870323', '$2y$10$8/IUPwBnRTtVrRrO8eofe.0S4wsgsmYg/g1r3lgatZNhcEMMzLGEa', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:53:46', 'inactive', 'profile_1_1768304847.jpg', 1, NULL, 1),
(2, 'AGM', '0880779557', '$2y$10$mJp1mk0FOmM43oEfvvF0deAcECUv/OwCvzMkCy306V4x6udbCd3Ne', 'buyer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:24', 'inactive', 'profile_2_1768248762.jpg', 1, NULL, 1),
(4, 'Davis', '0996576755', '$2y$10$G5flDc4eb3rPyp0FCF46IOq4cw1Fn19OR.XikJ7lhrp4Iuf1OYnbO', 'admin', NULL, 'male', '', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:20', 'inactive', 'profile_4_1768244657.jpg', 1, NULL, 1),
(5, 'Praise Msiska', '0997079797', '$2y$10$n7PL92pHRFPlA36EY7q3.e2YIoOXMGC3jzxiOrgWPMx8vwv3X0mQy', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2026-01-09 20:21:15', '2026-02-14 12:55:55', 'inactive', 'profile_5_1767990188.jpg', 1, '212949', 1),
(6, 'Albert Msiska', '0894970323', '$2y$10$kjZJszfvYE/.6WG9tSugUOD.65xie9F6s/TI5lKHT2e4Klt0vGvna', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-01-12 18:50:40', '2026-02-14 12:57:19', 'inactive', 'profile_6_1768244238.jpg', 1, '560771', 1),
(7, 'Monalisa Mwenitete', '0991799034', '$2y$10$aRveUxTelo.Dn0Q5bb8N5O.B/7hIx5rUAaOj6ItmiXkHjdiBIt1iO', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 0, '2026-01-29 20:24:09', '2026-02-06 13:51:48', 'inactive', 'default_avatar.png', 1, NULL, 0),
(8, 'puli', '0994507855', '$2y$10$A7NQRvHe0xrFn/Dv3xsUsOALk4DOtYVqZn9OVRxuKPOHd7lkFhoca', 'farmer', 'farmer', 'male', 'lilongwe', 'en', 1, '2026-02-04 05:56:29', '2026-02-06 13:51:48', 'inactive', 'profile_8_1770184966.jpg', 1, NULL, 1),
(9, 'Emmanuel Tembo', '0894970328', '$2y$10$HNZlhpHiB0GhkfnaHasm.ulK29RMiM53KsCQkDxjSB.fWNUDDFBay', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-02-06 13:44:15', '2026-02-06 13:51:48', 'inactive', 'profile_9_1770385558.png', 1, NULL, 1),
(10, 'Rejoice Msiska', '0998121260', '$2y$10$PXoVVL/hk/9w8c3hH.Iz5O5oU7ljBBq00G4baWigvSrGfEEN.vmNu', 'admin', NULL, NULL, 'Mzuzu', 'en', 1, '2026-02-11 21:33:03', '2026-02-11 21:42:15', 'inactive', 'profile_10_1770846135.png', 1, NULL, 1),
(11, 'Mona', '0993740077', '$2y$10$Flqg8f8NLCA5vKEDrxBxp..j8kiLotmkyWdjCAikWeZvAz39CC2HO', 'buyer', 'buyer', 'female', 'lilongwe', 'en', 1, '2026-02-14 13:02:29', '2026-02-19 07:09:31', 'inactive', 'default_avatar.png', 1, '933615', 0),
(12, 'Patience Banda', '0993807482', '$2y$10$p5hKCEVipgdhtvjEYN6f2.WYQTnnfv9s4ZZQpeI7mO2lJTyCk77cG', 'buyer', 'buyer', 'male', 'lilongwe', 'en', 1, '2026-02-14 15:42:27', '2026-02-14 15:45:40', 'inactive', 'profile_12_1771083940.jpg', 1, NULL, 1),
(14, 'Davis AGM', '0994308469', '$2y$10$LgBWWtS5Wf6pYSthb1jT3.mQbZZrs8H2FIhLEZGUB1tov4DgbeLQC', 'farmer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2026-02-17 06:05:14', '2026-02-19 15:20:20', 'inactive', 'profile_14_1771418940.jpg', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_type` enum('farmer','buyer','admin') NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_type`, `is_primary`, `created_at`) VALUES
(1, 1, 'farmer', 1, '2026-02-06 13:51:48'),
(2, 2, 'buyer', 1, '2026-02-06 13:51:48'),
(3, 5, 'farmer', 1, '2026-02-06 13:51:48'),
(4, 6, 'farmer', 1, '2026-02-06 13:51:48'),
(5, 7, 'farmer', 1, '2026-02-06 13:51:48'),
(6, 8, 'farmer', 1, '2026-02-06 13:51:48'),
(7, 9, 'farmer', 1, '2026-02-06 13:51:48'),
(8, 4, 'admin', 1, '2026-02-07 20:56:26'),
(9, 11, 'buyer', 1, '2026-02-14 13:02:29'),
(10, 12, 'buyer', 1, '2026-02-14 15:42:27'),
(13, 14, 'farmer', 1, '2026-02-17 06:05:14'),
(14, 14, 'buyer', 0, '2026-02-17 06:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `weather_alerts`
--

CREATE TABLE `weather_alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `alert_type` enum('heavy_rain','drought','frost','fertilizer_delay','harvest_ready') NOT NULL,
  `severity` enum('low','medium','high') DEFAULT 'medium',
  `message` text NOT NULL,
  `action_required` text DEFAULT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crop_config`
--
ALTER TABLE `crop_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_name` (`crop_name`);

--
-- Indexes for table `crop_timelines`
--
ALTER TABLE `crop_timelines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_crop` (`user_id`,`crop_name`),
  ADD KEY `idx_season` (`season_name`);

--
-- Indexes for table `farm_activities`
--
ALTER TABLE `farm_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_crop` (`user_id`,`crop_name`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_activity_type` (`activity_type`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `idx_listing` (`listing_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logs_ibfk_1` (`user_id`);

--
-- Indexes for table `market_insights`
--
ALTER TABLE `market_insights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crop` (`crop_name`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_trend` (`price_trend`);

--
-- Indexes for table `market_price_history`
--
ALTER TABLE `market_price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crop_date` (`crop_name`,`recorded_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_users` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `produce_listings`
--
ALTER TABLE `produce_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_produce_type` (`produce_type`),
  ADD KEY `fk_listings_crop` (`crop_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_user_role` (`user_id`,`role_type`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `system_settings_ibfk_1` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_type`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_role_type` (`role_type`);

--
-- Indexes for table `weather_alerts`
--
ALTER TABLE `weather_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_validity` (`valid_from`,`valid_until`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crop_config`
--
ALTER TABLE `crop_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `crop_timelines`
--
ALTER TABLE `crop_timelines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farm_activities`
--
ALTER TABLE `farm_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `market_insights`
--
ALTER TABLE `market_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `market_price_history`
--
ALTER TABLE `market_price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `produce_listings`
--
ALTER TABLE `produce_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `weather_alerts`
--
ALTER TABLE `weather_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crop_timelines`
--
ALTER TABLE `crop_timelines`
  ADD CONSTRAINT `crop_timelines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farm_activities`
--
ALTER TABLE `farm_activities`
  ADD CONSTRAINT `farm_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `produce_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produce_listings`
--
ALTER TABLE `produce_listings`
  ADD CONSTRAINT `fk_listings_crop` FOREIGN KEY (`crop_id`) REFERENCES `crop_config` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produce_listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weather_alerts`
--
ALTER TABLE `weather_alerts`
  ADD CONSTRAINT `weather_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
