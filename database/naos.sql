-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 09:03 PM
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
  `drought_resistant` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_config`
--

INSERT INTO `crop_config` (`id`, `crop_name`, `min_rainfall`, `max_rainfall`, `min_temp`, `max_temp`, `drought_resistant`) VALUES
(1, 'maize', 30, 9999, '0.00', '99.00', 0),
(2, 'rice', 30, 9999, '0.00', '99.00', 0),
(3, 'sorghum', 0, 50, '25.00', '99.00', 1),
(4, 'millet', 0, 50, '25.00', '99.00', 1),
(5, 'cowpeas', 0, 50, '25.00', '99.00', 1),
(6, 'beans', 20, 9999, '0.00', '99.00', 0),
(7, 'groundnuts', 20, 9999, '0.00', '99.00', 0),
(8, 'cassava', 0, 9999, '0.00', '99.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `farm_records`
--

CREATE TABLE `farm_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `activity` text DEFAULT NULL,
  `input_usage` text DEFAULT NULL,
  `yield` decimal(10,2) DEFAULT 0.00,
  `income` decimal(10,2) DEFAULT 0.00,
  `expense` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farm_records`
--

INSERT INTO `farm_records` (`id`, `user_id`, `date`, `activity`, `input_usage`, `yield`, `income`, `expense`) VALUES
(1, 1, '0000-00-00', '', '', '0.00', '0.00', '0.00');

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
  `timestamp` datetime DEFAULT current_timestamp(),
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_prices`
--

CREATE TABLE `market_prices` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `market_prices`
--

INSERT INTO `market_prices` (`id`, `crop_name`, `price_per_kg`, `last_updated`) VALUES
(1, 'maize', '520.00', '2025-12-26 17:37:59'),
(2, 'rice', '1300.00', '2025-12-26 17:37:59'),
(3, 'groundnuts', '950.00', '2025-12-26 17:37:59'),
(4, 'beans', '820.00', '2025-12-26 17:37:59'),
(5, 'soybeans', '1100.00', '2025-12-26 17:37:59'),
(6, 'sorghum', '450.00', '2025-12-26 17:37:59'),
(7, 'millet', '400.00', '2025-12-26 17:38:00'),
(8, 'cowpeas', '850.00', '2025-12-26 17:38:00'),
(9, 'cassava', '300.00', '2025-12-26 17:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `market_price_history`
--

CREATE TABLE `market_price_history` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
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
(1, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 18:53:07'),
(2, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:12:24'),
(3, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:14:58'),
(4, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:14:59'),
(5, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:18:11'),
(6, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:30:40'),
(7, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:31:03'),
(8, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:40:50'),
(9, 1, 0, 'subscription', '1.00', '5000.00', 'pending', 'unpaid', '2026-01-02 19:59:35');

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
  `provider_ref` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `transaction_id`, `order_id`, `amount`, `currency`, `status`, `provider_ref`, `created_at`, `updated_at`) VALUES
(1, 'NAOS-1-1767379987', 1, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 18:53:07', '2026-01-02 18:53:07'),
(2, 'NAOS-2-1767381144', 2, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:12:24', '2026-01-02 19:12:24'),
(3, 'NAOS-3-1767381298', 3, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:14:58', '2026-01-02 19:14:58'),
(4, 'NAOS-4-1767381299', 4, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:14:59', '2026-01-02 19:14:59'),
(5, 'NAOS-5-1767381491', 5, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:18:11', '2026-01-02 19:18:11'),
(6, 'NAOS-6-1767382241', 6, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:30:41', '2026-01-02 19:30:41'),
(7, 'NAOS-7-1767382263', 7, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:31:03', '2026-01-02 19:31:03'),
(8, 'NAOS-8-1767382851', 8, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:40:51', '2026-01-02 19:40:51'),
(9, 'NAOS-9-1767383975', 9, '1000.00', 'MWK', 'pending', NULL, '2026-01-02 19:59:35', '2026-01-02 19:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `produce_listings`
--

CREATE TABLE `produce_listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produce_type` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('available','sold') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'inactive',
  `plan_type` enum('monthly','yearly','trial') DEFAULT 'trial',
  `start_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('allow_registration', '1', '2025-12-31 16:12:06'),
('default_lang', 'en', '2025-12-31 16:12:06'),
('maintenance_mode', '0', '2025-12-31 16:12:05'),
('system_name', 'NAOS - Nyasa Agricultural Optimization System', '2025-12-31 16:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('farmer','buyer','admin') NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `lang` varchar(10) DEFAULT 'en',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subscription_status` enum('active','inactive') DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `location`, `lang`, `is_active`, `created_at`, `updated_at`, `subscription_status`) VALUES
(1, 'Albert', '$2y$10$8/IUPwBnRTtVrRrO8eofe.0S4wsgsmYg/g1r3lgatZNhcEMMzLGEa', 'farmer', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-01-02 19:55:32', 'inactive'),
(2, 'AGM', '$2y$10$mJp1mk0FOmM43oEfvvF0deAcECUv/OwCvzMkCy306V4x6udbCd3Ne', 'buyer', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2025-12-31 15:53:13', 'inactive'),
(3, 'testfarmer', '\\/93Lge8KoWyIGsIkMWzesmfHTt2c8sSSIVCs7rtVeksliu92IrK', 'farmer', '-13.2543,34.3015', 'en', 1, '2025-12-31 15:53:12', '2026-01-02 19:54:22', 'inactive'),
(4, 'Davis', '$2y$10$G5flDc4eb3rPyp0FCF46IOq4cw1Fn19OR.XikJ7lhrp4Iuf1OYnbO', 'admin', '', 'en', 1, '2025-12-31 15:53:12', '2025-12-31 15:53:13', 'inactive');

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
-- Indexes for table `farm_records`
--
ALTER TABLE `farm_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`date`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market_prices`
--
ALTER TABLE `market_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_name` (`crop_name`);

--
-- Indexes for table `market_price_history`
--
ALTER TABLE `market_price_history`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `idx_produce_type` (`produce_type`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crop_config`
--
ALTER TABLE `crop_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `farm_records`
--
ALTER TABLE `farm_records`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `market_prices`
--
ALTER TABLE `market_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `market_price_history`
--
ALTER TABLE `market_price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produce_listings`
--
ALTER TABLE `produce_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `farm_records`
--
ALTER TABLE `farm_records`
  ADD CONSTRAINT `farm_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `produce_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produce_listings`
--
ALTER TABLE `produce_listings`
  ADD CONSTRAINT `produce_listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
