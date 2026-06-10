-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 12:20 PM
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
  `price_updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ideal_soil` varchar(255) DEFAULT NULL,
  `ideal_ecological_zones` varchar(255) DEFAULT NULL,
  `ideal_districts` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_config`
--

INSERT INTO `crop_config` (`id`, `crop_name`, `min_rainfall`, `max_rainfall`, `min_temp`, `max_temp`, `drought_resistant`, `current_price`, `price_updated_at`, `ideal_soil`, `ideal_ecological_zones`, `ideal_districts`) VALUES
(1, 'maize', 30, 9999, '0.00', '99.00', 0, '520.00', '2026-01-09 19:13:50', 'Ferruginous,Alluvial,Sandy', 'Medium Altitude,High Altitude,Lakeshore', 'Lilongwe,Kasungu,Mzimba,Rumphi,Salima'),
(2, 'rice', 30, 9999, '0.00', '99.00', 0, '1300.00', '2026-01-09 19:13:50', 'Alluvial,Vertisols', 'Lakeshore,Shire Valley', 'Salima,Mangochi,Karonga,Nkhata Bay,Chikwawa,Nsanje'),
(3, 'sorghum', 0, 50, '25.00', '99.00', 1, '450.00', '2026-01-09 19:13:50', 'Vertisols,Sandy,Ferruginous', 'Shire Valley,Medium Altitude', 'Chikwawa,Nsanje,Machinga,Lilongwe'),
(4, 'millet', 0, 50, '25.00', '99.00', 1, '400.00', '2026-01-09 19:13:50', 'Sandy,Ferruginous', 'Medium Altitude,Shire Valley', 'Kasungu,Mzimba,Chikwawa,Nsanje'),
(5, 'cowpeas', 0, 50, '25.00', '99.00', 1, '900.00', '2026-01-09 19:13:50', 'Sandy,Ferruginous', 'Medium Altitude,Shire Valley', 'Lilongwe,Kasungu,Chikwawa,Nsanje'),
(6, 'beans', 20, 9999, '0.00', '99.00', 0, '820.00', '2026-01-09 19:13:50', 'Ferruginous,Sandy,Alluvial', 'High Altitude,Medium Altitude', 'Rumphi,Mzimba,Ntcheu,Dedza,Thyolo'),
(7, 'groundnuts', 20, 9999, '0.00', '99.00', 0, '1500.00', '2026-01-29 20:21:33', 'Sandy,Ferruginous', 'Medium Altitude,Shire Valley', 'Lilongwe,Kasungu,Mchinji,Dedza'),
(8, 'cassava', 0, 9999, '0.00', '99.00', 1, '300.00', '2026-01-09 19:13:50', 'Sandy,Ferruginous,Lithosols', 'Lakeshore,Medium Altitude,Shire Valley', 'Salima,Mangochi,Karonga,Nkhata Bay,Chikwawa'),
(10, 'Soybeans', 450, 700, '20.00', '32.00', 0, '750.00', '2026-02-21 20:31:50', 'Ferruginous,Alluvial', 'Medium Altitude,High Altitude', 'Lilongwe,Mzimba,Kasungu,Dedza'),
(12, 'Tobacco', 600, 900, '20.00', '30.00', 0, '2500.00', '2026-02-21 20:31:50', 'Sandy,Ferruginous', 'Medium Altitude', 'Kasungu,Mzimba,Lilongwe,Rumphi'),
(14, 'Pigeon Peas', 300, 500, '20.00', '40.00', 1, '600.00', '2026-02-21 20:31:50', 'Sandy,Ferruginous', 'Medium Altitude,Shire Valley', 'Lilongwe,Kasungu,Chiradzulu,Thyolo,Machinga');

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
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `farm_name` varchar(255) NOT NULL,
  `district` varchar(100) NOT NULL,
  `ecological_zone` varchar(100) NOT NULL,
  `soil_type` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `user_id`, `farm_name`, `district`, `ecological_zone`, `soil_type`, `latitude`, `longitude`, `created_at`) VALUES
(1, 1, 'Rumphi plot', 'Rumphi', 'Medium Altitude', '0', '-10.61335693', '34.10726258', '2026-03-16 21:00:28'),
(3, 14, 'Rumphi plot', 'Rumphi', 'High Altitude', '0', '-11.01670000', '33.85000000', '2026-03-16 21:59:30'),
(4, 14, 'Lilongwe plot', 'Lilongwe', 'Medium Altitude', '0', '-13.96260000', '33.77410000', '2026-03-16 22:02:43'),
(9, 36, 'Kasungu plot', 'Kasungu', 'Medium Altitude', 'Ferruginous', '-13.03330000', '33.48330000', '2026-05-08 03:11:36'),
(10, 5, 'Zomba Farm', 'Zomba', 'Medium Altitude', 'Ferruginous', '-15.38170000', '35.31880000', '2026-05-10 09:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `farm_activities`
--

CREATE TABLE `farm_activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `farm_id` int(11) DEFAULT NULL,
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

INSERT INTO `farm_activities` (`id`, `user_id`, `farm_id`, `crop_name`, `activity_type`, `date`, `quantity`, `unit`, `notes`, `created_at`) VALUES
(1, 1, NULL, 'beans', 'planted', '2026-02-09', NULL, '', '', '2026-02-19 15:01:36'),
(2, 1, NULL, 'maize', 'planted', '2026-02-22', NULL, '', '', '2026-02-22 12:58:57'),
(3, 14, 3, 'beans', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 22:25:50'),
(4, 14, 3, 'maize', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 23:07:15'),
(5, 14, 4, 'groundnuts', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 23:08:24'),
(6, 14, 4, 'Soybeans', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 23:18:33'),
(7, 14, 3, 'millet', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 23:19:01'),
(8, 36, NULL, 'cowpeas', 'planted', '2026-04-18', NULL, '', '', '2026-04-18 05:40:24'),
(9, 36, NULL, 'rice', 'weeded', '2024-02-07', NULL, '', '1234567', '2026-05-07 17:38:13'),
(10, 36, NULL, 'cassava', 'planted', '2030-06-07', NULL, '', '', '2026-05-07 17:38:48'),
(11, 5, 10, 'cassava', 'planted', '2026-05-10', NULL, '', '', '2026-05-10 09:48:13');

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
(26, NULL, '2026-02-19 09:09:36', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 933615'),
(124, 1, '2026-01-23 22:32:11', 'Viewed market trends'),
(125, 1, '2026-01-01 22:32:11', 'Updated system configurations'),
(126, 1, '2025-12-31 22:32:11', 'Marked notification as read'),
(127, 1, '2026-01-21 22:32:11', 'Updated profile settings'),
(128, 1, '2026-02-15 22:32:11', 'Added new produce listing'),
(129, NULL, '2026-02-22 15:00:31', '[SMS8] [SUCCESS] SMS to 0993740077: Your NAOS verification code is: 967704'),
(130, NULL, '2026-02-22 15:04:04', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 552292'),
(131, NULL, '2026-02-22 15:05:30', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 633885'),
(132, NULL, '2026-02-22 15:18:26', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 673248'),
(133, NULL, '2026-02-22 15:33:22', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 999087'),
(134, NULL, '2026-02-22 15:33:38', '[SMS8] [SUCCESS] SMS to 0997079797: Your NAOS verification code is: 315881'),
(135, NULL, '2026-02-22 15:39:27', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 186667. Do not share your with anyone.'),
(136, NULL, '2026-02-22 15:41:20', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 639701. Do not share this code with anyone.'),
(137, NULL, '2026-02-22 15:43:59', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 169894. Do not share it with anyone.'),
(138, NULL, '2026-02-22 15:54:30', '[SMS8] [SUCCESS] SMS to 0993740077: Your Nyasa Agricultural Optimization System verification code is: 126408. Do not share it with anyone.'),
(139, NULL, '2026-02-23 19:41:36', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 020900. Do not share it with anyone. | Error: You have insufficient credits to complete this operation. Please get more credits to continue. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"You have insufficient credits to complete this operation. Please get more credits to continue.\"}}'),
(140, NULL, '2026-02-23 19:41:56', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 527040. Do not share it with anyone. | Error: You have insufficient credits to complete this operation. Please get more credits to continue. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"You have insufficient credits to complete this operation. Please get more credits to continue.\"}}'),
(141, NULL, '2026-02-23 19:43:32', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 026495. Do not share it with anyone. | Error: You have insufficient credits to complete this operation. Please get more credits to continue. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"You have insufficient credits to complete this operation. Please get more credits to continue.\"}}'),
(142, NULL, '2026-02-23 19:53:12', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 032423. Do not share it with anyone. | Error: You have insufficient credits to complete this operation. Please get more credits to continue. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"You have insufficient credits to complete this operation. Please get more credits to continue.\"}}'),
(143, NULL, '2026-02-23 20:15:35', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 280342. Do not share it with anyone.'),
(144, NULL, '2026-02-23 20:19:03', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 009465. Do not share it with anyone.'),
(145, NULL, '2026-02-23 20:30:16', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 964643. Do not share it with anyone.'),
(146, NULL, '2026-02-23 20:37:23', '[SMS8] [SUCCESS] SMS to 0983767840: Your Nyasa Agricultural Optimization System verification code is: 447271. Do not share it with anyone.'),
(147, NULL, '2026-02-24 08:36:23', '[SMS8] [SUCCESS] SMS to 0995127864: Your Nyasa Agricultural Optimization System verification code is: 732635. Do not share it with anyone.'),
(148, NULL, '2026-02-24 08:38:31', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 186342. Do not share it with anyone.'),
(149, NULL, '2026-02-24 10:41:01', '[SMS8] [SUCCESS] SMS to 0995365664: Your Nyasa Agricultural Optimization System verification code is: 957730. Do not share it with anyone.'),
(150, 1, '2026-03-16 23:00:28', 'Added a new farm: Rumphi plot'),
(151, 14, '2026-03-16 23:01:51', 'Added a new farm: Rumphi plot'),
(152, 14, '2026-03-16 23:57:57', 'Deleted farm ID: 2'),
(153, 14, '2026-03-16 23:59:30', 'Added a new farm: Rumphi plot'),
(154, 14, '2026-03-17 00:02:43', 'Added a new farm: Lilongwe plot'),
(155, 1, '2026-03-19 11:52:25', 'Updated profile details'),
(156, NULL, '2026-03-23 22:03:35', '[SMS8] [FAILED] SMS to +26599370232: Your Nyasa Agricultural Optimization System verification code is: 358127. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(157, 1, '2026-03-25 16:00:28', 'Updated profile details'),
(158, NULL, '2026-04-10 04:52:31', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 437998. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(159, NULL, '2026-04-12 15:00:29', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 641139. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(160, NULL, '2026-04-12 15:33:49', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 191691. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(161, NULL, '2026-04-12 15:34:01', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 966312. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(162, NULL, '2026-04-12 15:35:13', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 335166. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(163, NULL, '2026-04-12 15:42:55', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 309178. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(164, NULL, '2026-04-12 15:44:34', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 205113. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(165, NULL, '2026-04-12 15:54:37', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 173456. Do not share it with anyone. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(166, NULL, '2026-04-12 16:02:43', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 194346. Do not share it with anyone.'),
(167, NULL, '2026-04-12 16:03:54', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 167414. Do not share it with anyone.'),
(168, NULL, '2026-04-12 16:07:00', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 338551. Do not share it with anyone.'),
(169, NULL, '2026-04-12 16:11:56', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 666835. Do not share it with anyone. | Error: Invalid request format. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Invalid request format.\"}}'),
(170, NULL, '2026-04-12 16:13:28', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 152999. Do not share it with anyone. | Error: There is no SIM card present on slot index 2. Please restart the SMS Gateway app on your Android device if you recently inserted the SIM. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"There is no SIM card present on slot index 2. Please restart the SMS Gateway app on your Android device if you recently inserted the SIM.\"}}'),
(171, NULL, '2026-04-12 16:16:36', '[SMS8] [FAILED] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 865439. Do not share it with anyone. | Error: Invalid request format. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Invalid request format.\"}}'),
(172, NULL, '2026-04-12 16:18:03', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 092930. Do not share it with anyone.'),
(173, NULL, '2026-04-12 16:20:05', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 938858. Do not share it with anyone.'),
(174, NULL, '2026-04-12 16:26:43', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 162864. Do not share it with anyone.'),
(175, NULL, '2026-04-12 16:30:09', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 470277. Do not share it with anyone.'),
(176, NULL, '2026-04-12 16:31:05', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 033858. Do not share it with anyone.'),
(177, NULL, '2026-04-12 16:34:01', '[SMS8] [SUCCESS] SMS to 0999130104: Your Nyasa Agricultural Optimization System verification code is: 920765. Do not share it with anyone.'),
(178, 4, '2026-04-12 16:37:42', 'Admin approved user verification for: Ethel Chirwa (ID: 34)'),
(179, NULL, '2026-04-12 16:37:46', '[SMS8] [FAILED] SMS to 0999130104: Dear Ethel Chirwa, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome! | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(180, 4, '2026-04-12 16:39:37', 'Admin rejected user verification for: Vuyo (ID: 33). Notes: '),
(181, NULL, '2026-04-12 16:39:40', '[SMS8] [FAILED] SMS to +26599370232: Dear Vuyo, your NAOS account verification was unsuccessful. Please contact support for assistance. | Error: Your subscription has expired. Renew your subscription to keep using this application. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":500,\"message\":\"Your subscription has expired. Renew your subscription to keep using this application.\"}}'),
(182, NULL, '2026-04-12 17:10:46', '[SMS8] [FAILED] SMS to 0995797793: Your Nyasa Agricultural Optimization System verification code is: 527369. Do not share it with anyone. | Error: cURL error: Could not resolve host: app.sms8.io'),
(183, NULL, '2026-04-12 17:11:57', '[SMS8] [FAILED] SMS to 0995797793: Your Nyasa Agricultural Optimization System verification code is: 618917. Do not share it with anyone. | Error: cURL error: Could not resolve host: app.sms8.io'),
(184, NULL, '2026-04-12 17:16:09', '[SMS8] [SUCCESS] SMS to 0995797793: Your Nyasa Agricultural Optimization System verification code is: 010729. Do not share it with anyone.'),
(185, 4, '2026-04-12 17:22:21', 'Admin approved user verification for: Tawonga Phiri (ID: 35)'),
(186, NULL, '2026-04-12 17:22:23', '[SMS8] [SUCCESS] SMS to 0995797793: Dear Tawonga Phiri, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(187, NULL, '2026-04-17 12:10:10', '[SMS8] [SUCCESS] SMS to 0997079797: Your Nyasa Agricultural Optimization System verification code is: 405572. Do not share it with anyone.'),
(188, NULL, '2026-04-17 14:39:05', '[SMS8] [SUCCESS] SMS to 0992119027: Your Nyasa Agricultural Optimization System verification code is: 481213. Do not share it with anyone.'),
(189, 4, '2026-04-17 14:41:37', 'Admin approved user verification for: Tawonga Mkandawire (ID: 36)'),
(190, NULL, '2026-04-17 14:41:38', '[SMS8] [SUCCESS] SMS to 0992119027: Dear Tawonga Mkandawire, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(191, 36, '2026-04-17 14:49:10', 'Added a new farm: Kasungu Plot'),
(192, 36, '2026-04-18 07:27:33', 'Deleted farm ID: 5'),
(193, 36, '2026-04-18 07:28:32', 'Added a new farm: Thyolo plot 1'),
(194, 36, '2026-04-18 19:43:53', 'Added a new farm: Mzuzu plot 1'),
(195, 36, '2026-04-18 20:05:40', 'Deleted farm ID: 6'),
(196, 36, '2026-04-18 20:06:49', 'Added a new farm: Blantyre Plot'),
(197, NULL, '2026-04-27 00:30:50', '[SMS8] [SUCCESS] SMS to 0989877698: Your Nyasa Agricultural Optimization System verification code is: 085021. Do not share it with anyone.'),
(198, NULL, '2026-04-27 00:43:14', '[SMS8] [SUCCESS] SMS to 0989877697: Your Nyasa Agricultural Optimization System verification code is: 379297. Do not share it with anyone.'),
(199, NULL, '2026-05-02 15:07:21', '[SMS8] [SUCCESS] SMS to 0888972300: Your Nyasa Agricultural Optimization System verification code is: 262329. Do not share it with anyone.'),
(200, 4, '2026-05-02 17:00:08', 'Admin approved user verification for: User Oyeselera (ID: 39)'),
(201, NULL, '2026-05-02 17:00:11', '[SMS8] [SUCCESS] SMS to 0888972300: Dear User Oyeselera, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(202, NULL, '2026-05-04 08:39:53', '[SMS8] [SUCCESS] SMS to 0884542495: Your Nyasa Agricultural Optimization System verification code is: 396987. Do not share it with anyone.'),
(203, 4, '2026-05-04 08:43:29', 'Admin approved user verification for: Wanda Lusizi (ID: 40)'),
(204, NULL, '2026-05-04 08:43:30', '[SMS8] [SUCCESS] SMS to 0884542495: Dear Wanda Lusizi, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(205, NULL, '2026-05-05 06:34:17', '[SMS8] [SUCCESS] SMS to 0886578527: Your Nyasa Agricultural Optimization System verification code is: 847685. Do not share it with anyone.'),
(206, 4, '2026-05-05 06:38:33', 'Admin approved user verification for: Nelyce Suman (ID: 41)'),
(207, NULL, '2026-05-05 06:38:33', '[SMS8] [SUCCESS] SMS to 0886578527: Dear Nelyce Suman, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(208, NULL, '2026-05-05 10:15:08', '[SMS8] [SUCCESS] SMS to 0882827566: Your Nyasa Agricultural Optimization System verification code is: 085476. Do not share it with anyone.'),
(209, 4, '2026-05-05 10:16:15', 'Admin approved user verification for: Another User (ID: 42)'),
(210, NULL, '2026-05-05 10:16:16', '[SMS8] [SUCCESS] SMS to 0882827566: Dear Another User, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(211, 36, '2026-05-07 19:31:58', 'Updated profile details'),
(212, 36, '2026-05-08 05:10:58', 'Deleted farm ID: 7'),
(213, 36, '2026-05-08 05:11:02', 'Deleted farm ID: 8'),
(214, 36, '2026-05-08 05:11:36', 'Added a new farm: Kasungu plot'),
(215, NULL, '2026-05-08 22:04:19', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 325554. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(216, NULL, '2026-05-08 22:06:26', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 862656. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(217, NULL, '2026-05-08 22:06:51', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 783192. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(218, NULL, '2026-05-08 22:23:44', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 494440. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(219, NULL, '2026-05-09 04:08:32', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 063179. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(220, NULL, '2026-05-09 04:09:22', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 297967. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(221, NULL, '2026-05-09 04:09:50', '[SMS8] [FAILED] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 537621. Do not share it with anyone. | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(222, NULL, '2026-05-09 04:13:13', '[SMS8] [SUCCESS] SMS to 0889603794: Your Nyasa Agricultural Optimization System verification code is: 794715. Do not share it with anyone.'),
(223, NULL, '2026-05-09 04:19:38', '[SMS8] [SUCCESS] SMS to 0899153294: Your Nyasa Agricultural Optimization System verification code is: 976355. Do not share it with anyone.'),
(224, 4, '2026-05-09 04:22:07', 'Admin approved user verification for: Ireen Chionera (ID: 44)'),
(225, NULL, '2026-05-09 04:22:07', '[SMS8] [FAILED] SMS to 0899153294: Dear Ireen Chionera, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome! | Error: This API key is invalid. | Raw Response: {\"success\":false,\"data\":null,\"error\":{\"code\":401,\"message\":\"This API key is invalid.\"}}'),
(226, NULL, '2026-05-09 04:29:38', '[SMS8] [SUCCESS] SMS to 0893035327: Your Nyasa Agricultural Optimization System verification code is: 408336. Do not share it with anyone.'),
(227, 4, '2026-05-09 04:32:16', 'Admin rejected user verification for: Obale Wathu (ID: 45). Notes: Your ID is not valid. Retry registering'),
(228, NULL, '2026-05-09 04:32:16', '[SMS8] [SUCCESS] SMS to 0893035327: Dear Obale Wathu, your NAOS account verification was unsuccessful. Reason: Your ID is not valid. Retry registering. Please contact support for assistance.'),
(229, NULL, '2026-05-09 04:39:10', '[SMS8] [SUCCESS] SMS to 0893035327: Your Nyasa Agricultural Optimization System verification code is: 278603. Do not share it with anyone.'),
(230, 4, '2026-05-09 04:40:22', 'Admin approved user verification for: Obale Wathu (ID: 46)'),
(231, NULL, '2026-05-09 04:40:23', '[SMS8] [SUCCESS] SMS to 0893035327: Dear Obale Wathu, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!'),
(232, 36, '2026-05-09 05:20:14', 'Updated profile details'),
(233, 36, '2026-05-09 05:20:21', 'Updated profile details'),
(234, 36, '2026-05-09 05:23:40', 'Updated profile details'),
(235, 36, '2026-05-09 05:23:48', 'Updated profile details'),
(236, 36, '2026-05-09 05:27:27', 'Updated profile details'),
(237, 36, '2026-05-09 05:27:35', 'Updated profile details'),
(238, NULL, '2026-05-09 06:41:47', '[SMS8] [SUCCESS] SMS to 0893576695: Your Nyasa Agricultural Optimization System verification code is: 082250. Do not share it with anyone.'),
(239, NULL, '2026-05-09 07:31:25', '[SMS8] [SUCCESS] SMS to 0888471251: Your Nyasa Agricultural Optimization System verification code is: 074377. Do not share it with anyone.'),
(240, NULL, '2026-05-09 07:31:26', '[SMS8] [SUCCESS] SMS to 0888471251: NAOS: Farmer identity verification failed. Please check your Farmer ID and Full Name and try again.'),
(241, NULL, '2026-05-09 07:58:10', '[SMS8] [SUCCESS] SMS to 0880330431: Your Nyasa Agricultural Optimization System verification code is: 076183. Do not share it with anyone.'),
(242, NULL, '2026-05-09 07:59:00', '[SMS8] [SUCCESS] SMS to 0880330431: Your Nyasa Agricultural Optimization System verification code is: 017189. Do not share it with anyone.'),
(243, NULL, '2026-05-09 07:59:23', '[SMS8] [SUCCESS] SMS to 0880330431: NAOS: Your farmer identity has been successfully verified against Ministry records. Welcome!'),
(244, 5, '2026-05-10 11:39:47', 'Added a new farm: Zomba Farm');

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

--
-- Dumping data for table `market_insights`
--

INSERT INTO `market_insights` (`id`, `crop_name`, `current_price`, `price_trend`, `trend_percentage`, `recommendation`, `optimal_sell_window`, `demand_level`, `created_at`) VALUES
(1, 'Maize', '450.00', 'stable', '0.14', 'Based on current stable trends, it is recommended to sell soon.', 'Next 2-3 Weeks', 'medium', '2026-02-21 20:32:11'),
(2, 'Soybeans', '750.00', 'stable', '0.03', 'Based on current stable trends, it is recommended to sell soon.', 'Next 2-3 Weeks', 'low', '2026-02-21 20:32:11'),
(3, 'Groundnuts', '900.00', 'rising', '0.06', 'Based on current rising trends, it is recommended to hold inventory.', 'Next 2-3 Weeks', 'low', '2026-02-21 20:32:11'),
(4, 'Tobacco', '2500.00', 'stable', '0.09', 'Based on current stable trends, it is recommended to sell soon.', 'Next 2-3 Weeks', 'high', '2026-02-21 20:32:11'),
(5, 'Rice', '1100.00', 'rising', '0.03', 'Based on current rising trends, it is recommended to hold inventory.', 'Next 2-3 Weeks', 'high', '2026-02-21 20:32:11'),
(6, 'Pigeon Peas', '600.00', 'falling', '0.08', 'Based on current falling trends, it is recommended to sell soon.', 'Next 2-3 Weeks', 'medium', '2026-02-21 20:32:11');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `action_data`, `is_read`, `created_at`) VALUES
(1, 5, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-03-25 21:52:42'),
(2, 34, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-04-12 15:35:06'),
(3, 5, 'general', 'New order received from AGM for maize. Please check your incoming orders.', NULL, 1, '2026-04-17 10:24:44'),
(4, 2, 'general', 'Farmer Praise Msiska has viewed your order and may contact you soon.', NULL, 1, '2026-04-17 10:25:34'),
(5, 36, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-04-17 12:53:07'),
(6, 5, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-04 06:53:23'),
(7, 5, 'general', 'New order received from Another User for maize. Please check your incoming orders.', NULL, 1, '2026-05-08 04:09:30'),
(8, 11, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 100 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":39,\"section\":\"listings\"}', 0, '2026-05-08 19:56:44'),
(9, 12, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 100 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":39,\"section\":\"listings\"}', 0, '2026-05-08 19:56:44'),
(10, 40, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 100 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":39,\"section\":\"listings\"}', 0, '2026-05-08 19:56:44'),
(11, 36, 'general', 'New order received from Davis AGM for maize. Please check your incoming orders.', NULL, 1, '2026-05-08 19:57:56'),
(12, 14, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-08 19:58:27'),
(13, 36, 'general', 'New order received from AGM for maize. Please check your incoming orders.', NULL, 1, '2026-05-09 07:10:23'),
(14, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 07:12:16'),
(15, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 10:46:13'),
(16, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 12:17:34'),
(17, 14, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 12:17:36'),
(18, 5, 'general', 'Order #46 status updated to cancelled by Buyer.', NULL, 1, '2026-05-09 12:20:36'),
(19, 36, 'general', 'New order received from AGM for maize. Please check your incoming orders.', NULL, 1, '2026-05-09 13:08:18'),
(20, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:11:31'),
(21, 2, 'general', 'Order #52 status updated to confirmed by Farmer.', NULL, 1, '2026-05-09 13:11:36'),
(22, 2, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-09 13:28:11'),
(23, 2, 'general', 'Order #52 status updated to delivered by Farmer.', NULL, 1, '2026-05-09 13:28:45'),
(24, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:28:54'),
(25, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:38:53'),
(26, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:39:17'),
(27, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:39:23'),
(28, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:41:16'),
(29, 2, 'general', 'Farmer Tawonga Mkandawire has viewed your order and may contact you soon.', NULL, 1, '2026-05-09 13:41:22'),
(30, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 13:56:04'),
(31, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 13:56:32'),
(32, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 13:56:45'),
(33, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 14:09:34'),
(34, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 1, '2026-05-09 14:09:47'),
(35, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 14:17:05'),
(36, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 1, '2026-05-09 14:17:13'),
(37, 2, 'general', 'Order #51 status updated to confirmed by Farmer.', NULL, 1, '2026-05-09 14:17:21'),
(38, 2, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-09 14:18:31'),
(39, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 1, '2026-05-09 14:19:12'),
(40, 2, 'general', 'Your order #51 has been shipped via CTS. Tracking: CTS001', NULL, 1, '2026-05-09 14:19:42'),
(41, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 1, '2026-05-09 14:19:47'),
(42, 36, 'general', 'Buyer has confirmed receipt of order #51. Funds have been released to your account.', NULL, 1, '2026-05-09 14:21:01'),
(43, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 1, '2026-05-09 14:22:31'),
(44, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 14:22:36'),
(45, 14, 'general', 'A farmer is currently reviewing your order #50.', NULL, 1, '2026-05-09 15:58:48'),
(46, 2, 'general', 'A farmer is currently reviewing your order #52.', NULL, 1, '2026-05-09 19:19:22'),
(47, 42, 'general', 'A farmer is currently reviewing your order #49.', NULL, 1, '2026-05-10 04:43:19'),
(48, 11, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 50 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":40,\"section\":\"listings\"}', 0, '2026-05-10 05:14:15'),
(49, 12, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 50 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":40,\"section\":\"listings\"}', 0, '2026-05-10 05:14:15'),
(50, 40, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 50 kg of Maize at MWK 50/kg in Lilongwe.', '{\"listing_id\":40,\"section\":\"listings\"}', 0, '2026-05-10 05:14:15'),
(51, 11, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 40 kg of Cowpeas at MWK 49/kg in Lilongwe.', '{\"listing_id\":41,\"section\":\"listings\"}', 0, '2026-05-10 08:32:27'),
(52, 12, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 40 kg of Cowpeas at MWK 49/kg in Lilongwe.', '{\"listing_id\":41,\"section\":\"listings\"}', 0, '2026-05-10 08:32:27'),
(53, 40, 'new_listing', '🌾 New produce near you: Tawonga Mkandawire is selling 40 kg of Cowpeas at MWK 49/kg in Lilongwe.', '{\"listing_id\":41,\"section\":\"listings\"}', 0, '2026-05-10 08:32:27'),
(54, 36, 'general', 'New order received from Another User for cowpeas. Please check your incoming orders.', NULL, 1, '2026-05-10 08:54:05'),
(55, 36, 'general', 'New order received from Another User for maize. Please check your incoming orders.', NULL, 1, '2026-05-10 08:54:05'),
(56, 42, 'general', 'A farmer is currently reviewing your order #54.', NULL, 1, '2026-05-10 08:54:40'),
(57, 42, 'general', 'Order #54 status updated to confirmed by Farmer.', NULL, 1, '2026-05-10 08:54:44'),
(58, 42, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-10 08:56:03'),
(59, 42, 'general', 'A farmer is currently reviewing your order #54.', NULL, 1, '2026-05-10 08:57:06'),
(60, 42, 'general', 'Your order #54 has been shipped via CTS Courier. Tracking: CTS-119147-MW', NULL, 1, '2026-05-10 08:57:46'),
(61, 42, 'general', 'Your order #54 has been shipped via CTS Courier. Tracking: CTS-916131-MW', NULL, 1, '2026-05-10 08:58:01'),
(62, 42, 'general', 'A farmer is currently reviewing your order #54.', NULL, 1, '2026-05-10 09:28:22'),
(63, 42, 'general', 'A farmer is currently reviewing your order #53.', NULL, 1, '2026-05-10 09:28:34'),
(64, 42, 'general', 'Order #53 status updated to cancelled by Farmer.', NULL, 1, '2026-05-10 09:28:40'),
(65, 42, 'general', 'A farmer is currently reviewing your order #53.', NULL, 1, '2026-05-10 09:28:46'),
(66, 42, 'general', 'A farmer is currently reviewing your order #54.', NULL, 1, '2026-05-10 09:31:45'),
(67, 2, 'general', 'A farmer is currently reviewing your order #51.', NULL, 0, '2026-05-10 09:32:45'),
(68, 36, 'general', 'Buyer has confirmed receipt of order #54. Funds have been released to your account.', NULL, 1, '2026-05-10 09:33:07'),
(69, 42, 'general', 'A farmer is currently reviewing your order #49.', NULL, 1, '2026-05-10 09:48:40'),
(70, 42, 'general', 'Order #49 status updated to confirmed by Farmer.', NULL, 1, '2026-05-10 09:48:46'),
(71, 42, 'general', 'Payment of 56.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-10 10:19:36'),
(72, 42, 'general', 'A farmer is currently reviewing your order #49.', NULL, 1, '2026-05-10 10:21:27'),
(73, 42, 'general', 'Your order #49 has been shipped via CTS Courier. Tracking: CTS-794725-MW', NULL, 1, '2026-05-10 10:21:43'),
(74, 42, 'general', 'Your order #49 has been shipped via CTS Courier. Tracking: CTS-140827-MW', NULL, 1, '2026-05-10 10:22:39'),
(75, 42, 'general', 'A farmer is currently reviewing your order #49.', NULL, 1, '2026-05-10 10:37:34'),
(76, 2, 'general', 'A farmer is currently reviewing your order #46.', NULL, 0, '2026-05-10 10:37:38'),
(77, 2, 'general', 'A farmer is currently reviewing your order #46.', NULL, 0, '2026-05-10 10:37:46'),
(78, 2, 'new_listing', '🌾 New produce near you: Praise Msiska is selling 76 kg of Cowpeas at MWK 83/kg in Mzuzu.', '{\"listing_id\":42,\"section\":\"listings\"}', 0, '2026-05-10 10:43:00'),
(79, 35, 'new_listing', '🌾 New produce near you: Praise Msiska is selling 76 kg of Cowpeas at MWK 83/kg in Mzuzu.', '{\"listing_id\":42,\"section\":\"listings\"}', 0, '2026-05-10 10:43:00'),
(80, 43, 'new_listing', '🌾 New produce near you: Praise Msiska is selling 76 kg of Cowpeas at MWK 83/kg in Mzuzu.', '{\"listing_id\":42,\"section\":\"listings\"}', 0, '2026-05-10 10:43:00'),
(81, 5, 'general', 'New order received from Another User for cowpeas. Please check your incoming orders.', NULL, 1, '2026-05-10 10:43:23'),
(82, 42, 'general', 'A farmer is currently reviewing your order #56.', NULL, 1, '2026-05-10 10:43:47'),
(83, 42, 'general', 'Order #56 status updated to confirmed by Farmer.', NULL, 1, '2026-05-10 10:43:50'),
(84, 5, 'general', 'Buyer has confirmed receipt of order #49. Funds have been released to your account.', NULL, 1, '2026-05-10 10:44:14'),
(85, 42, 'general', 'Payment of 83.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-05-10 10:45:18'),
(86, 42, 'general', 'A farmer is currently reviewing your order #56.', NULL, 1, '2026-05-10 10:45:48'),
(87, 42, 'general', 'Your order #56 has been shipped via CTS Courier. Tracking: CTS-784608-MW', NULL, 1, '2026-05-10 10:45:53'),
(88, 42, 'general', 'A farmer is currently reviewing your order #56.', NULL, 1, '2026-05-10 10:46:03');

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
  `buyer_contact` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'pending',
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `escrow_status` enum('pending','paid','released','cancelled') DEFAULT 'pending',
  `courier_name` varchar(50) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `listing_id`, `type`, `quantity`, `buyer_contact`, `price`, `status`, `payment_status`, `created_at`, `escrow_status`, `courier_name`, `tracking_number`, `shipment_date`) VALUES
(46, 2, 36, 'maize', '1.00', '0997079797', '56.00', 'cancelled', 'unpaid', '2026-04-17 10:24:44', 'pending', NULL, NULL, NULL),
(49, 42, 36, 'maize', '1.00', '0997079797', '56.00', 'delivered', 'paid', '2026-05-08 04:09:30', 'released', 'CTS Courier', 'CTS-140827-MW', '2026-05-10 12:22:39'),
(50, 14, 39, 'maize', '1.00', '0997079797', '50.00', 'pending', 'unpaid', '2026-05-08 19:57:56', 'pending', NULL, NULL, NULL),
(51, 2, 39, 'maize', '1.00', '0997079797', '50.00', 'delivered', 'paid', '2026-05-09 07:10:23', 'released', 'CTS', 'CTS001', '2026-05-09 16:19:42'),
(52, 2, 39, 'maize', '1.00', '0997079797', '50.00', 'delivered', 'paid', '2026-05-09 13:08:18', 'paid', NULL, NULL, NULL),
(53, 42, 41, 'cowpeas', '1.00', '0997079797', '49.00', 'cancelled', 'unpaid', '2026-05-10 08:54:05', 'pending', NULL, NULL, NULL),
(54, 42, 40, 'maize', '1.00', '0997079797', '50.00', 'delivered', 'paid', '2026-05-10 08:54:05', 'released', 'CTS Courier', 'CTS-916131-MW', '2026-05-10 10:58:01'),
(55, 42, 0, 'subscription', '1.00', NULL, '50.00', 'cancelled', 'unpaid', '2026-05-10 10:20:28', 'pending', NULL, NULL, NULL),
(56, 42, 42, 'cowpeas', '1.00', '0997079797', '83.00', 'shipped', 'paid', '2026-05-10 10:43:23', 'paid', 'CTS Courier', 'CTS-784608-MW', '2026-05-10 12:45:53');

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `transaction_id`, `order_id`, `amount`, `currency`, `status`, `payment_method`, `provider_ref`, `created_at`, `updated_at`) VALUES
(49, 'NAOS-52-1778333114', 52, '50.00', 'MWK', 'pending', 'card', NULL, '2026-05-09 13:25:14', '2026-05-09 13:25:14'),
(50, 'NAOS-52-1778333259', 52, '50.00', 'MWK', 'completed', 'airtel', 'BP260509.1527.731836', '2026-05-09 13:27:39', '2026-05-09 13:28:11'),
(51, 'NAOS-51-1778336278', 51, '50.00', 'MWK', 'completed', 'airtel', 'BP260509.1618.454693', '2026-05-09 14:17:58', '2026-05-09 14:18:31'),
(52, 'NAOS-54-1778403330', 54, '50.00', 'MWK', 'completed', 'airtel', 'BP260510.1055.485824', '2026-05-10 08:55:30', '2026-05-10 08:56:03'),
(53, 'NAOS-49-1778406587', 49, '56.00', 'MWK', 'pending', 'airtel', 'ID2605101149490cd5CTPAY', '2026-05-10 09:49:47', '2026-05-10 09:49:50'),
(54, 'NAOS-49-1778407037', 49, '56.00', 'MWK', 'pending', 'airtel', 'ID2605101157194104CTPAY', '2026-05-10 09:57:17', '2026-05-10 09:57:20'),
(55, 'NAOS-49-1778407322', 49, '56.00', 'MWK', 'pending', 'airtel', 'ID2605101202040d49CTPAY', '2026-05-10 10:02:02', '2026-05-10 10:02:05'),
(56, 'NAOS-49-1778407570', 49, '56.00', 'MWK', 'pending', 'airtel', 'ID260510120612360eCTPAY', '2026-05-10 10:06:10', '2026-05-10 10:06:13'),
(57, 'NAOS-49-1778407770', 49, '56.00', 'MWK', 'pending', 'airtel', 'ID260510120932d37bCTPAY', '2026-05-10 10:09:30', '2026-05-10 10:09:33'),
(58, 'NAOS-49-1778408217', 49, '56.00', 'MWK', 'failed', 'airtel', 'ID260510121659518dCTPAY', '2026-05-10 10:16:57', '2026-05-10 10:17:30'),
(59, 'NAOS-49-1778408344', 49, '56.00', 'MWK', 'completed', 'airtel', 'BP260510.1219.395926', '2026-05-10 10:19:04', '2026-05-10 10:19:36'),
(60, 'NAOS-55-1778408428', 55, '50.00', 'MWK', 'pending', 'card', NULL, '2026-05-10 10:20:28', '2026-05-10 10:20:28'),
(61, 'NAOS-56-1778409886', 56, '83.00', 'MWK', 'completed', 'airtel', 'BP260510.1244.674555', '2026-05-10 10:44:46', '2026-05-10 10:45:18');

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
  `status` enum('available','sold') DEFAULT 'available',
  `produce_image` varchar(255) DEFAULT 'default_produce.png',
  `description` text DEFAULT NULL,
  `unit` varchar(20) NOT NULL DEFAULT 'kg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produce_listings`
--

INSERT INTO `produce_listings` (`id`, `user_id`, `crop_id`, `produce_type`, `quantity`, `price`, `created_at`, `status`, `produce_image`, `description`, `unit`) VALUES
(35, 14, 1, 'Maize', '50.00', '500000.00', '2026-02-24 07:06:14', 'available', 'default_produce.png', NULL, 'kg'),
(36, 5, 1, 'maize', '1.00', '56.00', '2026-03-26 00:16:40', 'available', 'default_produce.png', NULL, 'kg'),
(37, 36, 8, 'cassava', '1.00', '50.00', '2026-04-18 20:07:33', 'available', 'default_produce.png', NULL, 'kg'),
(38, 14, 5, 'cowpeas', '1.00', '50.00', '2026-04-22 10:29:18', 'available', 'default_produce.png', NULL, 'kg'),
(39, 36, 1, 'maize', '100.00', '50.00', '2026-05-08 21:56:44', 'available', 'assets/images/produce/produce_36_1778270204_c937bdac.jfif', 'YELLOW MAIZE AVAILABLE', 'kg'),
(40, 36, 1, 'maize', '50.00', '50.00', '2026-05-10 07:14:15', 'available', 'assets/images/produce/produce_36_1778390055_c1661424.jfif', 'yellow maize', 'kg'),
(41, 36, 5, 'cowpeas', '40.00', '49.00', '2026-05-10 10:32:27', 'available', 'assets/images/produce/produce_36_1778401947_3154fb35.jfif', 'Fresh cowpeas', 'kg'),
(42, 5, 5, 'cowpeas', '76.00', '83.00', '2026-05-10 12:43:00', 'available', 'assets/images/produce/produce_5_1778409780_50d4d6c9.jfif', '', 'kg');

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
(5, 9, 'farmer', 'active', 'trial', NULL, NULL, '2026-02-06 13:44:15', '2026-05-04 06:54:22'),
(6, 1, 'farmer', 'expired', 'monthly', '2026-02-08 06:52:57', '2025-03-08 06:52:57', '2026-02-08 04:52:57', '2026-02-11 18:42:28'),
(7, 11, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-14 13:02:29', '2026-02-14 13:02:29'),
(8, 12, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-14 15:42:27', '2026-02-14 15:42:27'),
(11, 14, 'farmer', 'active', 'trial', '2026-02-28 07:42:43', NULL, '2026-02-17 06:05:14', '2026-02-24 05:43:00'),
(12, 14, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-17 06:05:14', '2026-02-17 06:05:14'),
(13, 30, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-23 18:37:21', '2026-02-23 18:37:21'),
(14, 31, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-24 06:36:22', '2026-02-24 06:36:22'),
(15, 32, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-02-24 08:40:59', '2026-02-24 08:40:59'),
(16, 33, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-03-23 20:03:33', '2026-03-23 20:03:33'),
(17, 5, 'farmer', 'expired', 'monthly', '2026-03-25 23:29:39', '2026-04-24 22:29:39', '2026-03-25 21:29:39', '2026-03-25 21:46:44'),
(18, 5, 'farmer', 'expired', 'monthly', '2026-03-25 23:46:44', '2026-04-24 22:46:44', '2026-03-25 21:46:44', '2026-03-25 21:52:42'),
(19, 5, 'farmer', 'expired', 'monthly', '2026-03-25 23:52:42', '2026-04-24 22:52:42', '2026-03-25 21:52:42', '2026-05-04 06:53:23'),
(20, 34, 'farmer', 'expired', 'trial', NULL, NULL, '2026-04-12 14:33:58', '2026-04-12 15:35:06'),
(21, 35, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-04-12 15:10:46', '2026-04-12 15:10:46'),
(22, 34, 'farmer', 'active', 'monthly', '2026-04-12 17:35:06', '2026-05-12 17:35:06', '2026-04-12 15:35:06', '2026-04-12 15:35:06'),
(23, 36, 'farmer', 'expired', 'trial', NULL, NULL, '2026-04-17 12:39:03', '2026-04-17 12:53:07'),
(24, 36, 'farmer', 'active', 'monthly', '2026-04-17 14:53:07', '2026-05-17 14:53:07', '2026-04-17 12:53:07', '2026-04-17 12:53:07'),
(25, 39, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-02 13:07:20', '2026-05-02 13:07:20'),
(26, 40, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-05-04 06:39:52', '2026-05-04 06:39:52'),
(27, 5, 'farmer', 'active', 'monthly', '2026-05-04 08:53:23', '2026-06-03 08:53:23', '2026-05-04 06:53:23', '2026-05-04 06:53:23'),
(28, 41, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-05 04:34:16', '2026-05-05 04:34:16'),
(29, 42, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-05 08:15:07', '2026-05-05 08:15:07'),
(30, 42, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-05-05 08:15:07', '2026-05-05 08:15:07'),
(31, 43, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-05-08 20:04:18', '2026-05-08 20:04:18'),
(32, 44, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-09 02:19:37', '2026-05-09 02:19:37'),
(35, 46, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-09 02:39:09', '2026-05-09 02:39:09'),
(36, 46, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-05-09 02:39:09', '2026-05-09 02:39:09'),
(37, 47, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-09 04:41:46', '2026-05-09 04:41:46'),
(38, 48, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-09 05:31:24', '2026-05-09 05:31:24'),
(39, 49, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-05-09 05:58:09', '2026-05-09 05:58:09');

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
(4, 4, 'system_name', 'Nyasa Agricultural Optimization System', '2026-03-18 14:39:26'),
(5, 4, 'subscription_amount', '50', '2026-02-21 11:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `farmer_id` varchar(50) DEFAULT NULL,
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
  `is_profile_complete` tinyint(1) DEFAULT 0,
  `national_id_path` varchar(255) DEFAULT NULL,
  `id_verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `id_verification_notes` text DEFAULT NULL,
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `farmer_id`, `username`, `phone_number`, `password_hash`, `role`, `active_role`, `gender`, `location`, `lang`, `is_active`, `created_at`, `updated_at`, `subscription_status`, `profile_picture`, `is_phone_verified`, `phone_verification_code`, `is_profile_complete`, `national_id_path`, `id_verification_status`, `id_verification_notes`, `notification_preferences`) VALUES
(1, NULL, 'Albert', '0880870323', '$2y$10$QKPl6iAoIPZ5JMjZY1xvK.Yd6OYY3HEVaOsysca5F9SaSgxxvPPM.', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-05-10 12:38:45', 'inactive', 'profile_1_1768304847.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(2, NULL, 'AGM', '0880779557', '$2y$10$mJp1mk0FOmM43oEfvvF0deAcECUv/OwCvzMkCy306V4x6udbCd3Ne', 'buyer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:24', 'inactive', 'profile_2_1768248762.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(4, NULL, 'Davis', '0996576755', '$2y$10$G5flDc4eb3rPyp0FCF46IOq4cw1Fn19OR.XikJ7lhrp4Iuf1OYnbO', 'admin', NULL, 'male', '', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:20', 'inactive', 'profile_4_1768244657.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(5, NULL, 'Praise Msiska', '0997079797', '$2y$10$mkag6pY31wM1eTOsdqASseTilX7jdbFPJpgc.SYpkebF7glTNtB4m', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2026-01-09 20:21:15', '2026-05-11 10:10:16', 'active', 'profile_5_1767990188.jpg', 1, '405572', 1, NULL, 'approved', NULL, NULL),
(6, NULL, 'Albert Msiska', '0894970323', '$2y$10$kjZJszfvYE/.6WG9tSugUOD.65xie9F6s/TI5lKHT2e4Klt0vGvna', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-01-12 18:50:40', '2026-02-14 12:57:19', 'inactive', 'profile_6_1768244238.jpg', 1, '560771', 1, NULL, 'approved', NULL, NULL),
(7, NULL, 'Monalisa Mwenitete', '0991799034', '$2y$10$aRveUxTelo.Dn0Q5bb8N5O.B/7hIx5rUAaOj6ItmiXkHjdiBIt1iO', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 0, '2026-01-29 20:24:09', '2026-02-06 13:51:48', 'inactive', 'default_avatar.png', 1, NULL, 0, NULL, 'approved', NULL, NULL),
(8, NULL, 'puli', '0994507855', '$2y$10$A7NQRvHe0xrFn/Dv3xsUsOALk4DOtYVqZn9OVRxuKPOHd7lkFhoca', 'farmer', 'farmer', 'male', 'lilongwe', 'en', 1, '2026-02-04 05:56:29', '2026-02-06 13:51:48', 'inactive', 'profile_8_1770184966.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(9, NULL, 'Emmanuel Tembo', '0894970328', '$2y$10$HNZlhpHiB0GhkfnaHasm.ulK29RMiM53KsCQkDxjSB.fWNUDDFBay', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-02-06 13:44:15', '2026-02-06 13:51:48', 'inactive', 'profile_9_1770385558.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(10, NULL, 'Rejoice Msiska', '0998121260', '$2y$10$PXoVVL/hk/9w8c3hH.Iz5O5oU7ljBBq00G4baWigvSrGfEEN.vmNu', 'farmer', NULL, NULL, 'Mzuzu', 'en', 1, '2026-02-11 21:33:03', '2026-04-18 05:15:24', 'inactive', 'profile_10_1770846135.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(11, NULL, 'Mona', '0993740077', '$2y$10$Flqg8f8NLCA5vKEDrxBxp..j8kiLotmkyWdjCAikWeZvAz39CC2HO', 'buyer', 'buyer', 'female', 'lilongwe', 'en', 1, '2026-02-14 13:02:29', '2026-02-22 13:54:28', 'inactive', 'default_avatar.png', 1, '126408', 0, NULL, 'approved', NULL, NULL),
(12, NULL, 'Patience Banda', '0993807482', '$2y$10$p5hKCEVipgdhtvjEYN6f2.WYQTnnfv9s4ZZQpeI7mO2lJTyCk77cG', 'buyer', 'buyer', 'male', 'lilongwe', 'en', 1, '2026-02-14 15:42:27', '2026-02-14 15:45:40', 'inactive', 'profile_12_1771083940.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(14, NULL, 'Davis AGM', '0994308469', '$2y$10$LgBWWtS5Wf6pYSthb1jT3.mQbZZrs8H2FIhLEZGUB1tov4DgbeLQC', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-02-17 06:05:14', '2026-05-10 13:01:09', 'active', 'profile_14_1771418940.jpg', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(30, NULL, 'Mesho Mesho', '0983767840', '$2y$10$/NA9.wjY2DuavGU9U/JdOeor6b6VELB5EV2l4ly0K5Eqg5QXdaybi', 'buyer', 'buyer', 'male', 'Karonga', 'en', 1, '2026-02-23 18:37:21', '2026-02-24 05:04:00', 'inactive', 'profile_30_1771871917.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(31, NULL, 'Misheck Khobiri', '0995127864', '$2y$10$oyUKs2Vphhnt2tOt32JCC.p852KxqiPtLMTMn0pYDVhbdhqboRIZC', 'buyer', 'buyer', 'male', 'Chitipa', 'en', 1, '2026-02-24 06:36:22', '2026-02-24 06:37:48', 'inactive', 'profile_31_1771915068.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(32, NULL, 'Emmanuel', '0995365664', '$2y$10$OfMv1rekCSiKQ/p46lNJOeoYzhqdcZwka8V6769XC9yPtTbo4UDWy', 'farmer', 'farmer', 'male', 'Rumphi', 'en', 1, '2026-02-24 08:40:59', '2026-03-18 14:54:17', 'inactive', 'profile_32_1771922529.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(33, NULL, 'Vuyo', '+26599370232', '$2y$10$gV6q2lk7udVPreiIW9JSd.LlxYyUColUlEOnOEEOexjoAELFfAjqe', 'farmer', 'farmer', 'male', 'Chitipa', 'en', 1, '2026-03-23 20:03:33', '2026-04-12 14:39:37', 'inactive', 'default_avatar.png', 0, '358127', 0, 'assets/uploads/national_ids/id_33_1774296213.jpg', 'rejected', '', NULL),
(34, NULL, 'Ethel Chirwa', '0999130104', '$2y$10$cYvgao9hDuT3LkEbxPFRfe6Or1p3WLJ0mQDcrgSq5x4P4MmgdAZxC', 'farmer', 'farmer', 'female', 'Chitipa', 'en', 1, '2026-04-12 14:33:58', '2026-04-12 15:35:06', 'active', 'profile_34_1776007675.jpg', 1, NULL, 1, 'assets/uploads/national_ids/id_34_1776004438.jpg', 'approved', NULL, NULL),
(35, NULL, 'Tawonga Phiri', '0995797793', '$2y$10$yVUEvQVPXHTnraWj2QbB1e7KCW95/CwDzoTww.bABhkrCqEBhpFY.', 'buyer', 'buyer', 'female', 'Mzuzu', 'en', 1, '2026-04-12 15:10:46', '2026-04-12 15:25:09', 'inactive', 'profile_35_1776007509.jpg', 1, NULL, 1, 'assets/uploads/national_ids/id_35_1776006646.jpg', 'approved', NULL, NULL),
(36, NULL, 'Tawonga Mkandawire', '0992119027', '$2y$10$.4WUBhqB9J4v3Fk3JN0yI..HwDaZfE2FnRIOnfTlfWeROGP4.Mzxe', 'farmer', 'farmer', 'female', 'Lilongwe', 'en', 1, '2026-04-17 12:39:03', '2026-05-09 03:27:35', 'active', 'profile_36_1776429741.jpg', 1, NULL, 1, 'assets/uploads/national_ids/id_36_1776429543.png', 'approved', NULL, NULL),
(37, NULL, 'Maliko Phiri', '0989877697', '$2y$10$Lidy7R.F7yrUv.LWGdGbZuap80jTR63AoDkjeL2CLnqTyafyQ0wLK', 'farmer', NULL, NULL, 'Chitipa', 'en', 1, '2026-04-26 20:28:51', '2026-04-26 22:44:53', 'inactive', 'default_avatar.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(38, NULL, 'Zebedu zebedu', '0989877698', '$2y$10$17W2WBKRcatk7vGQNGqGJuJwey6ndc9HQo1sy5vk6NxOzMHKJf7IS', 'farmer', NULL, NULL, 'Chitipa', 'en', 1, '2026-04-26 21:29:07', '2026-04-26 22:32:07', 'inactive', 'default_avatar.png', 1, NULL, 1, NULL, 'approved', NULL, NULL),
(39, NULL, 'User Oyeselera', '0888972300', '$2y$10$Efz0WoZs/Vsg7qWHblINp.y806MwBHy/n.jIln9kdbUlW/n34sKDm', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-05-02 13:07:20', '2026-05-02 17:13:22', 'inactive', 'default_avatar.png', 1, NULL, 1, 'assets/uploads/national_ids/id_39_1777727240.png', 'approved', NULL, NULL),
(40, NULL, 'Wanda Lusizi', '0884542495', '$2y$10$Zi3EbSzXKf7GlQO3tA4KrOl7dOld9wvSotpeno/OnLwrke65Z.bO6', 'buyer', 'buyer', 'male', 'lilongwe', 'en', 1, '2026-05-04 06:39:52', '2026-05-04 06:45:20', 'inactive', 'default_avatar.png', 1, NULL, 1, 'assets/uploads/national_ids/id_40_1777876792.jpg', 'approved', NULL, NULL),
(41, NULL, 'Nelyce Suman', '0886578527', '$2y$10$pDP22Glj0JDjevtI2NJoWeYQYkCa1VD2iZEeIr0J0xQqJpEFnssaa', 'farmer', 'farmer', 'female', 'lilongwe', 'en', 1, '2026-05-05 04:34:16', '2026-05-05 04:38:33', 'inactive', 'default_avatar.png', 1, NULL, 0, 'assets/uploads/national_ids/id_41_1777955656.jpg', 'approved', NULL, NULL),
(42, NULL, 'Another User', '0882827566', '$2y$10$q7JNslZZhSYFbG/nsv/YqeqY9YbeMYH55H17.EM185m3w..HvBmd2', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2026-05-05 08:15:07', '2026-05-10 12:21:41', 'inactive', 'default_avatar.png', 1, NULL, 1, 'assets/uploads/national_ids/id_42_1777968907.jpg', 'approved', NULL, NULL),
(43, NULL, 'Praise Chirwa', '0889603794', '$2y$10$5kEb9d68ZBY7EL0Rmx5bmODbWQzhitVv/2K6OtF9OIULMDW1H2RFS', 'buyer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2026-05-08 20:04:18', '2026-05-09 02:15:13', 'inactive', 'default_avatar.png', 1, NULL, 1, '', 'approved', NULL, NULL),
(44, NULL, 'Ireen Chionera', '0899153294', '$2y$10$gEFDqCJkoZKf.MGZuw7/HukUjAWQt75cH0nn9w6bl.OUTJlQIxvDS', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2026-05-09 02:19:37', '2026-05-09 02:22:07', 'inactive', 'default_avatar.png', 1, NULL, 0, 'assets/uploads/national_ids/id_44_1778293177.jpg', 'approved', NULL, NULL),
(46, NULL, 'Obale Wathu', '0893035327', '$2y$10$OS1sU2XnGR78WVSwzM0RzeqWLx/Fm54RVa6xbiyt2SZ.nSwlnqz0O', 'farmer', 'farmer', 'female', 'lilongwe', 'en', 1, '2026-05-09 02:39:09', '2026-05-09 02:40:22', 'inactive', 'default_avatar.png', 1, NULL, 0, 'assets/uploads/national_ids/id_46_1778294349.jpg', 'approved', NULL, NULL),
(47, 'MW-SF-00001', 'Chisomo Banda', '0893576695', '$2y$10$49JqaTsmeOt4/ur00jROkugYUQeZAFkaD4xidsR5iHtBkoJ0ASyOi', 'farmer', 'farmer', 'male', 'lilongwe', 'en', 1, '2026-05-09 04:41:46', '2026-05-09 04:41:46', 'inactive', 'default_avatar.png', 0, '082250', 0, 'assets/uploads/national_ids/id_47_1778301706.jpg', 'approved', 'Automatically verified against Ministry records.', NULL),
(48, 'MW-SF-00001', 'Kondwani Phiri', '0888471251', '$2y$10$4msYz03JPVT0o2X8Yd6Ddu6mMAkObHW1l/tjt8aUEHEdTXvQeSMCK', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-05-09 05:31:24', '2026-05-09 05:34:05', 'inactive', 'default_avatar.png', 1, NULL, 0, 'assets/uploads/national_ids/id_48_1778304684.jpg', 'rejected', 'Verification failed: Farmer ID and Full Name do not match Ministry records.', NULL),
(49, 'MW-SF-00003', 'Tadala Mwale', '0880330431', '$2y$10$FH04Mi7LWW3HA/dpcWB2yOy9zBNwhxEMd2zt18y/0Ya8lhdhl.RQu', 'farmer', 'farmer', 'female', 'lilongwe', 'en', 1, '2026-05-09 05:58:09', '2026-05-09 06:00:39', 'inactive', 'default_avatar.png', 1, NULL, 1, 'assets/uploads/national_ids/id_49_1778306289.jpg', 'approved', 'Automatically verified against Ministry records.', NULL);

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
(14, 14, 'buyer', 0, '2026-02-17 06:05:14'),
(15, 30, 'buyer', 1, '2026-02-23 18:37:21'),
(16, 31, 'buyer', 1, '2026-02-24 06:36:22'),
(17, 32, 'farmer', 1, '2026-02-24 08:40:59'),
(18, 33, 'farmer', 1, '2026-03-23 20:03:33'),
(19, 34, 'farmer', 1, '2026-04-12 14:33:58'),
(20, 35, 'buyer', 1, '2026-04-12 15:10:46'),
(21, 36, 'farmer', 1, '2026-04-17 12:39:03'),
(22, 39, 'farmer', 1, '2026-05-02 13:07:20'),
(23, 40, 'buyer', 1, '2026-05-04 06:39:52'),
(24, 41, 'farmer', 1, '2026-05-05 04:34:16'),
(25, 42, 'farmer', 1, '2026-05-05 08:15:07'),
(26, 42, 'buyer', 0, '2026-05-05 08:15:07'),
(27, 43, 'buyer', 1, '2026-05-08 20:04:18'),
(28, 44, 'farmer', 1, '2026-05-09 02:19:37'),
(31, 46, 'farmer', 1, '2026-05-09 02:39:09'),
(32, 46, 'buyer', 0, '2026-05-09 02:39:09'),
(33, 47, 'farmer', 1, '2026-05-09 04:41:46'),
(34, 48, 'farmer', 1, '2026-05-09 05:31:24'),
(35, 49, 'farmer', 1, '2026-05-09 05:58:09');

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

-- --------------------------------------------------------

--
-- Table structure for table `wfp_sync_log`
--

CREATE TABLE `wfp_sync_log` (
  `id` int(11) NOT NULL,
  `synced_at` datetime NOT NULL,
  `source` varchar(100) DEFAULT 'WFP/HDX',
  `records_found` int(11) DEFAULT 0,
  `crops_updated` int(11) DEFAULT 0,
  `status` enum('success','partial','failed') DEFAULT 'success',
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wfp_sync_log`
--

INSERT INTO `wfp_sync_log` (`id`, `synced_at`, `source`, `records_found`, `crops_updated`, `status`, `message`) VALUES
(1, '2026-05-08 12:18:42', 'WFP/HDX', 0, 0, 'failed', 'Both WFP/HDX and WFP VAM Dataviz APIs are currently unavailable.'),
(2, '2026-05-08 12:18:52', 'WFP/HDX', 0, 0, 'failed', 'Both WFP/HDX and WFP VAM Dataviz APIs are currently unavailable.'),
(3, '2026-05-08 12:19:08', 'WFP/HDX', 0, 0, 'failed', 'Both WFP/HDX and WFP VAM Dataviz APIs are currently unavailable.'),
(4, '2026-05-08 15:01:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(5, '2026-05-08 15:04:12', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(6, '2026-05-08 15:04:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(7, '2026-05-08 15:06:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(8, '2026-05-08 15:14:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(9, '2026-05-08 15:14:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(10, '2026-05-08 15:14:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(11, '2026-05-08 15:24:25', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(12, '2026-05-08 16:53:49', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(13, '2026-05-08 17:20:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(14, '2026-05-08 21:50:45', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(15, '2026-05-08 21:53:33', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(16, '2026-05-08 21:53:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(17, '2026-05-08 21:53:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(18, '2026-05-08 21:57:03', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(19, '2026-05-08 21:57:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(20, '2026-05-08 22:00:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(21, '2026-05-08 22:00:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(22, '2026-05-09 04:15:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(23, '2026-05-09 04:15:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(24, '2026-05-09 05:14:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(25, '2026-05-09 05:20:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(26, '2026-05-09 05:23:28', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(27, '2026-05-09 05:27:16', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(28, '2026-05-09 05:28:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(29, '2026-05-09 05:45:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(30, '2026-05-09 08:00:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(31, '2026-05-09 08:28:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(32, '2026-05-09 08:28:48', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(33, '2026-05-09 08:29:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(34, '2026-05-09 08:29:46', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(35, '2026-05-09 08:43:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(36, '2026-05-09 08:44:46', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(37, '2026-05-09 08:44:48', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(38, '2026-05-09 08:46:12', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(39, '2026-05-09 08:46:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(40, '2026-05-09 08:46:16', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(41, '2026-05-09 08:46:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(42, '2026-05-09 08:46:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(43, '2026-05-09 08:46:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(44, '2026-05-09 08:51:51', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(45, '2026-05-09 08:51:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(46, '2026-05-09 08:53:12', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(47, '2026-05-09 08:53:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(48, '2026-05-09 08:54:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(49, '2026-05-09 08:54:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(50, '2026-05-09 09:08:49', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(51, '2026-05-09 09:09:16', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(52, '2026-05-09 09:09:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(53, '2026-05-09 09:11:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(54, '2026-05-09 09:11:41', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(55, '2026-05-09 09:33:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(56, '2026-05-09 09:34:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(57, '2026-05-09 09:34:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(58, '2026-05-09 12:15:29', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(59, '2026-05-09 12:45:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(60, '2026-05-09 14:16:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(61, '2026-05-09 14:18:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(62, '2026-05-09 14:18:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(63, '2026-05-09 14:20:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(64, '2026-05-09 14:20:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(65, '2026-05-09 14:27:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(66, '2026-05-09 14:27:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(67, '2026-05-09 15:11:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(68, '2026-05-09 15:11:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(69, '2026-05-09 15:12:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(70, '2026-05-09 15:12:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(71, '2026-05-09 15:23:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(72, '2026-05-09 15:25:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(73, '2026-05-09 15:25:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(74, '2026-05-09 15:27:28', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(75, '2026-05-09 15:27:30', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(76, '2026-05-09 15:28:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(77, '2026-05-09 15:28:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(78, '2026-05-09 15:28:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(79, '2026-05-09 15:38:44', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(80, '2026-05-09 15:50:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(81, '2026-05-09 15:50:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(82, '2026-05-09 15:50:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(83, '2026-05-09 15:56:00', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(84, '2026-05-09 16:09:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(85, '2026-05-09 16:17:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(86, '2026-05-09 16:17:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(87, '2026-05-09 16:17:44', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(88, '2026-05-09 16:17:46', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(89, '2026-05-09 16:18:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(90, '2026-05-09 16:18:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(91, '2026-05-09 16:19:03', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(92, '2026-05-09 16:20:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(93, '2026-05-09 16:20:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(94, '2026-05-09 16:22:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(95, '2026-05-09 16:35:33', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(96, '2026-05-09 16:35:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(97, '2026-05-09 16:41:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(98, '2026-05-09 16:42:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(99, '2026-05-09 16:42:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(100, '2026-05-09 16:42:26', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(101, '2026-05-09 17:10:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(102, '2026-05-09 17:10:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(103, '2026-05-09 17:10:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(104, '2026-05-09 17:11:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(105, '2026-05-09 17:11:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(106, '2026-05-09 17:11:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(107, '2026-05-09 17:11:20', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(108, '2026-05-09 17:22:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(109, '2026-05-09 17:22:16', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(110, '2026-05-09 17:22:30', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(111, '2026-05-09 17:22:32', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(112, '2026-05-09 17:22:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(113, '2026-05-09 17:22:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(114, '2026-05-09 17:22:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(115, '2026-05-09 17:37:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(116, '2026-05-09 17:37:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(117, '2026-05-09 17:37:25', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(118, '2026-05-09 17:38:25', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(119, '2026-05-09 17:38:27', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(120, '2026-05-09 20:35:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(121, '2026-05-09 20:35:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(122, '2026-05-09 20:36:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(123, '2026-05-09 20:36:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(124, '2026-05-09 20:36:41', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(125, '2026-05-09 20:39:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(126, '2026-05-09 20:39:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(127, '2026-05-09 20:46:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(128, '2026-05-09 20:46:30', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(129, '2026-05-09 20:46:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(130, '2026-05-09 20:59:25', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(131, '2026-05-09 20:59:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(132, '2026-05-09 20:59:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(133, '2026-05-09 20:59:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(134, '2026-05-09 21:04:20', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(135, '2026-05-09 21:04:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(136, '2026-05-09 21:04:26', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(137, '2026-05-09 21:05:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(138, '2026-05-09 21:05:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(139, '2026-05-09 21:05:29', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(140, '2026-05-09 21:05:32', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(141, '2026-05-09 21:05:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(142, '2026-05-09 21:05:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(143, '2026-05-09 21:06:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(144, '2026-05-09 21:06:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(145, '2026-05-09 21:06:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(146, '2026-05-09 21:06:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(147, '2026-05-09 21:06:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(148, '2026-05-09 21:07:00', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(149, '2026-05-09 21:07:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(150, '2026-05-09 21:07:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(151, '2026-05-09 21:12:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(152, '2026-05-09 21:12:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(153, '2026-05-09 21:12:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(154, '2026-05-09 21:19:49', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(155, '2026-05-09 21:28:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(156, '2026-05-10 06:26:33', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(157, '2026-05-10 06:26:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(158, '2026-05-10 06:28:10', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(159, '2026-05-10 06:32:41', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(160, '2026-05-10 06:32:43', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(161, '2026-05-10 06:44:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(162, '2026-05-10 06:44:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(163, '2026-05-10 06:44:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(164, '2026-05-10 07:08:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(165, '2026-05-10 07:08:41', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(166, '2026-05-10 07:14:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(167, '2026-05-10 07:14:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(168, '2026-05-10 07:14:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(169, '2026-05-10 10:30:10', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(170, '2026-05-10 10:30:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(171, '2026-05-10 10:35:48', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(172, '2026-05-10 10:35:51', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(173, '2026-05-10 10:36:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(174, '2026-05-10 10:36:20', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(175, '2026-05-10 10:44:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(176, '2026-05-10 10:44:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(177, '2026-05-10 10:45:27', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(178, '2026-05-10 10:45:29', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(179, '2026-05-10 10:46:28', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(180, '2026-05-10 10:46:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(181, '2026-05-10 10:47:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(182, '2026-05-10 10:47:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(183, '2026-05-10 10:51:50', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(184, '2026-05-10 10:51:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(185, '2026-05-10 10:52:28', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(186, '2026-05-10 10:52:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(187, '2026-05-10 10:53:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(188, '2026-05-10 10:53:03', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(189, '2026-05-10 10:53:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(190, '2026-05-10 10:54:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(191, '2026-05-10 10:54:25', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(192, '2026-05-10 10:55:16', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(193, '2026-05-10 10:55:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(194, '2026-05-10 10:55:20', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(195, '2026-05-10 10:56:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(196, '2026-05-10 10:56:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(197, '2026-05-10 10:56:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(198, '2026-05-10 10:56:32', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(199, '2026-05-10 10:56:35', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(200, '2026-05-10 11:28:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(201, '2026-05-10 11:28:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(202, '2026-05-10 11:32:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(203, '2026-05-10 11:32:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(204, '2026-05-10 11:33:00', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(205, '2026-05-10 11:34:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(206, '2026-05-10 11:34:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(207, '2026-05-10 11:39:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(208, '2026-05-10 11:39:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(209, '2026-05-10 11:47:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(210, '2026-05-10 11:47:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(211, '2026-05-10 11:49:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(212, '2026-05-10 11:49:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(213, '2026-05-10 11:49:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(214, '2026-05-10 11:56:48', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(215, '2026-05-10 11:56:50', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(216, '2026-05-10 11:56:52', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(217, '2026-05-10 11:58:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(218, '2026-05-10 11:58:03', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(219, '2026-05-10 11:58:05', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(220, '2026-05-10 12:05:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(221, '2026-05-10 12:05:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(222, '2026-05-10 12:06:00', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(223, '2026-05-10 12:06:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(224, '2026-05-10 12:06:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(225, '2026-05-10 12:06:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(226, '2026-05-10 12:16:46', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(227, '2026-05-10 12:16:48', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(228, '2026-05-10 12:16:50', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(229, '2026-05-10 12:16:52', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(230, '2026-05-10 12:16:54', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(231, '2026-05-10 12:16:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(232, '2026-05-10 12:17:32', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(233, '2026-05-10 12:17:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(234, '2026-05-10 12:17:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(235, '2026-05-10 12:19:39', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(236, '2026-05-10 12:19:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(237, '2026-05-10 12:19:43', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(238, '2026-05-10 12:20:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(239, '2026-05-10 12:20:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(240, '2026-05-10 12:20:58', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(241, '2026-05-10 12:20:59', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(242, '2026-05-10 12:21:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(243, '2026-05-10 12:21:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(244, '2026-05-10 12:21:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(245, '2026-05-10 12:37:28', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(246, '2026-05-10 12:37:29', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(247, '2026-05-10 12:41:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(248, '2026-05-10 12:41:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(249, '2026-05-10 12:41:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(250, '2026-05-10 12:43:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(251, '2026-05-10 12:43:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(252, '2026-05-10 12:43:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(253, '2026-05-10 12:45:20', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(254, '2026-05-10 12:45:22', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(255, '2026-05-10 12:45:24', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(256, '2026-05-10 12:56:01', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(257, '2026-05-10 12:56:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(258, '2026-05-10 12:56:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(259, '2026-05-10 12:56:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(260, '2026-05-10 12:56:41', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(261, '2026-05-10 13:42:12', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(262, '2026-05-10 13:42:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(263, '2026-05-10 13:55:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(264, '2026-05-10 13:55:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(265, '2026-05-10 14:01:51', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(266, '2026-05-10 14:01:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(267, '2026-05-10 14:02:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(268, '2026-05-10 14:02:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(269, '2026-05-10 14:02:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(270, '2026-05-10 14:02:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(271, '2026-05-10 14:06:54', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(272, '2026-05-10 14:06:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(273, '2026-05-10 14:07:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(274, '2026-05-10 14:07:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(275, '2026-05-10 14:07:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(276, '2026-05-10 14:07:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(277, '2026-05-10 14:07:26', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(278, '2026-05-10 14:07:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(279, '2026-05-10 14:07:46', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(280, '2026-05-10 14:07:50', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(281, '2026-05-10 14:10:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(282, '2026-05-10 14:10:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(283, '2026-05-10 14:10:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(284, '2026-05-10 14:10:45', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(285, '2026-05-10 14:10:51', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(286, '2026-05-10 14:10:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(287, '2026-05-10 14:13:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(288, '2026-05-10 14:13:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(289, '2026-05-10 14:14:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(290, '2026-05-10 14:14:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(291, '2026-05-10 14:14:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(292, '2026-05-10 14:14:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(293, '2026-05-10 14:14:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(294, '2026-05-10 14:14:24', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(295, '2026-05-10 14:16:33', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(296, '2026-05-10 14:16:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(297, '2026-05-10 14:16:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(298, '2026-05-10 14:16:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(299, '2026-05-10 14:16:44', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(300, '2026-05-10 14:17:33', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(301, '2026-05-10 14:17:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(302, '2026-05-10 14:17:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(303, '2026-05-10 14:17:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(304, '2026-05-10 14:17:55', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(305, '2026-05-10 14:17:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(306, '2026-05-10 14:21:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(307, '2026-05-10 14:21:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(308, '2026-05-10 14:21:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(309, '2026-05-10 14:21:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(310, '2026-05-10 14:21:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(311, '2026-05-10 14:21:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(312, '2026-05-10 14:24:43', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(313, '2026-05-10 14:24:45', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(314, '2026-05-10 14:24:49', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(315, '2026-05-10 14:24:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(316, '2026-05-10 14:25:23', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(317, '2026-05-10 14:25:26', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(318, '2026-05-10 14:25:31', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(319, '2026-05-10 14:25:34', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(320, '2026-05-10 14:26:13', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(321, '2026-05-10 14:26:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(322, '2026-05-10 14:26:19', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(323, '2026-05-10 14:26:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(324, '2026-05-10 14:53:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(325, '2026-05-10 14:53:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(326, '2026-05-10 14:54:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(327, '2026-05-10 14:54:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(328, '2026-05-10 15:00:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(329, '2026-05-10 15:01:02', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(330, '2026-05-10 15:01:07', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(331, '2026-05-10 15:01:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(332, '2026-05-10 15:01:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(333, '2026-05-10 15:01:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(334, '2026-05-10 15:01:18', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(335, '2026-05-10 15:03:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(336, '2026-05-10 15:03:10', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(337, '2026-05-11 10:41:54', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(338, '2026-05-11 10:41:54', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(339, '2026-05-11 10:44:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(340, '2026-05-11 10:44:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(341, '2026-05-11 10:55:47', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(342, '2026-05-11 10:55:49', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(343, '2026-05-11 11:27:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(344, '2026-05-11 11:27:15', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(345, '2026-05-11 11:27:38', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(346, '2026-05-11 11:27:42', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(347, '2026-05-11 11:28:37', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(348, '2026-05-11 11:28:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(349, '2026-05-11 11:29:06', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(350, '2026-05-11 11:29:11', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(351, '2026-05-11 11:29:36', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(352, '2026-05-11 11:29:40', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(353, '2026-05-11 11:29:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(354, '2026-05-11 11:30:09', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(355, '2026-05-11 11:46:00', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(356, '2026-05-11 11:46:04', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(357, '2026-05-11 11:46:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(358, '2026-05-11 11:46:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(359, '2026-05-11 11:47:14', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.');
INSERT INTO `wfp_sync_log` (`id`, `synced_at`, `source`, `records_found`, `crops_updated`, `status`, `message`) VALUES
(360, '2026-05-11 11:47:17', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(361, '2026-05-11 11:47:53', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(362, '2026-05-11 11:47:57', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(363, '2026-05-11 12:09:21', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(364, '2026-05-11 12:09:56', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.'),
(365, '2026-05-11 12:10:08', 'WFP/HDX', 0, 0, 'failed', 'Upstream APIs (WFP/HDX) are currently unavailable. Prices have not been updated.');

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
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `farm_activities`
--
ALTER TABLE `farm_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_crop` (`user_id`,`crop_name`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `fk_farm_activities_farm` (`farm_id`);

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
-- Indexes for table `wfp_sync_log`
--
ALTER TABLE `wfp_sync_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crop_config`
--
ALTER TABLE `crop_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `crop_timelines`
--
ALTER TABLE `crop_timelines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `farm_activities`
--
ALTER TABLE `farm_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `market_insights`
--
ALTER TABLE `market_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `market_price_history`
--
ALTER TABLE `market_price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `produce_listings`
--
ALTER TABLE `produce_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `weather_alerts`
--
ALTER TABLE `weather_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wfp_sync_log`
--
ALTER TABLE `wfp_sync_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crop_timelines`
--
ALTER TABLE `crop_timelines`
  ADD CONSTRAINT `crop_timelines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `farms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farm_activities`
--
ALTER TABLE `farm_activities`
  ADD CONSTRAINT `farm_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_farm_activities_farm` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL;

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
