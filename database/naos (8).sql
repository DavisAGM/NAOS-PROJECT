-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2026 at 09:54 AM
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
(1, 'maize', 30, 9999, '0.00', '99.00', 0, '520.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(2, 'rice', 30, 9999, '0.00', '99.00', 0, '1300.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(3, 'sorghum', 0, 50, '25.00', '99.00', 1, '450.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(4, 'millet', 0, 50, '25.00', '99.00', 1, '400.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(5, 'cowpeas', 0, 50, '25.00', '99.00', 1, '900.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(6, 'beans', 20, 9999, '0.00', '99.00', 0, '820.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(7, 'groundnuts', 20, 9999, '0.00', '99.00', 0, '1500.00', '2026-01-29 20:21:33', NULL, NULL, NULL),
(8, 'cassava', 0, 9999, '0.00', '99.00', 1, '300.00', '2026-01-09 19:13:50', NULL, NULL, NULL),
(10, 'Soybeans', 450, 700, '20.00', '32.00', 0, '750.00', '2026-02-21 20:31:50', NULL, NULL, NULL),
(12, 'Tobacco', 600, 900, '20.00', '30.00', 0, '2500.00', '2026-02-21 20:31:50', NULL, NULL, NULL),
(14, 'Pigeon Peas', 300, 500, '20.00', '40.00', 1, '600.00', '2026-02-21 20:31:50', NULL, NULL, NULL);

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
(4, 14, 'Lilongwe plot', 'Lilongwe', 'Medium Altitude', '0', '-13.96260000', '33.77410000', '2026-03-16 22:02:43');

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
(7, 14, 3, 'millet', 'planted', '2026-03-17', NULL, '', '', '2026-03-16 23:19:01');

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
(186, NULL, '2026-04-12 17:22:23', '[SMS8] [SUCCESS] SMS to 0995797793: Dear Tawonga Phiri, your account review was successful. You can now visit our NAOS platform to enjoy our services. Welcome!');

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
(2, 34, 'general', 'Payment of 50.00 MWK was successful. Your account has been updated.', NULL, 1, '2026-04-12 15:35:06');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `listing_id`, `type`, `quantity`, `buyer_contact`, `price`, `status`, `payment_status`, `created_at`) VALUES
(13, 1, 0, 'subscription', '1.00', NULL, '5000.00', 'pending', 'unpaid', '2026-02-11 18:35:49'),
(14, 1, 0, 'subscription', '1.00', NULL, '5000.00', 'pending', 'unpaid', '2026-02-11 21:22:03'),
(15, 1, 0, 'subscription', '1.00', NULL, '5000.00', 'pending', 'unpaid', '2026-02-11 21:22:54'),
(16, 2, 0, 'subscription', '1.00', NULL, '5000.00', 'pending', 'unpaid', '2026-02-11 22:12:43'),
(26, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-02-24 05:28:50'),
(27, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-02-24 05:29:06'),
(28, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-19 09:52:06'),
(29, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-24 05:03:41'),
(30, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-24 05:04:41'),
(31, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 14:00:37'),
(32, 1, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 14:00:51'),
(33, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 20:49:58'),
(34, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 20:55:55'),
(35, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 20:56:05'),
(36, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 20:58:12'),
(37, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 21:00:47'),
(38, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 21:08:45'),
(39, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 21:11:34'),
(40, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 21:16:26'),
(41, 5, 0, 'subscription', '1.00', NULL, '50.00', 'pending', 'unpaid', '2026-03-25 21:22:44'),
(42, 5, 0, 'subscription', '1.00', NULL, '50.00', 'processing', 'paid', '2026-03-25 21:29:10'),
(43, 5, 0, 'subscription', '1.00', NULL, '50.00', 'processing', 'paid', '2026-03-25 21:46:08'),
(44, 5, 0, 'subscription', '1.00', NULL, '50.00', 'processing', 'paid', '2026-03-25 21:52:06'),
(45, 34, 0, 'subscription', '1.00', NULL, '50.00', 'processing', 'paid', '2026-04-12 15:34:17');

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
(28, 'NAOS-28-1773913926', 28, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-19 09:52:06', '2026-03-19 09:52:06'),
(29, 'NAOS-29-1774328621', 29, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-24 05:03:41', '2026-03-24 05:03:41'),
(30, 'NAOS-30-1774328681', 30, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-24 05:04:41', '2026-03-24 05:04:41'),
(31, 'NAOS-31-1774447237', 31, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 14:00:37', '2026-03-25 14:00:37'),
(32, 'NAOS-32-1774447251', 32, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 14:00:51', '2026-03-25 14:00:51'),
(33, 'NAOS-33-1774471798', 33, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 20:49:58', '2026-03-25 20:49:58'),
(34, 'NAOS-34-1774472155', 34, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 20:55:55', '2026-03-25 20:55:55'),
(35, 'NAOS-35-1774472165', 35, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 20:56:05', '2026-03-25 20:56:05'),
(36, 'NAOS-36-1774472292', 36, '50.00', 'MWK', 'pending', 'card', NULL, '2026-03-25 20:58:12', '2026-03-25 20:58:12'),
(37, 'NAOS-37-1774472447', 37, '50.00', 'MWK', 'pending', 'airtel', 'ID2603252300494658CTPAY', '2026-03-25 21:00:47', '2026-03-25 21:00:51'),
(38, 'NAOS-38-1774472925', 38, '50.00', 'MWK', 'failed', 'airtel', 'ID2603252308470a15CTPAY', '2026-03-25 21:08:45', '2026-03-25 21:08:54'),
(39, 'NAOS-39-1774473094', 39, '50.00', 'MWK', 'failed', 'airtel', 'ID2603252311364ba5CTPAY', '2026-03-25 21:11:34', '2026-03-25 21:11:45'),
(40, 'NAOS-40-1774473386', 40, '50.00', 'MWK', 'failed', 'airtel', 'ID260325231628168dCTPAY', '2026-03-25 21:16:26', '2026-03-25 21:16:36'),
(41, 'NAOS-41-1774473764', 41, '50.00', 'MWK', 'pending', 'airtel', 'ID2603252322463451CTPAY', '2026-03-25 21:22:44', '2026-03-25 21:22:47'),
(42, 'NAOS-42-1774474150', 42, '50.00', 'MWK', 'completed', 'airtel', 'MP260325.2329.901303', '2026-03-25 21:29:10', '2026-03-25 21:29:39'),
(43, 'NAOS-43-1774475168', 43, '50.00', 'MWK', 'completed', 'airtel', 'MP260325.2346.871209', '2026-03-25 21:46:08', '2026-03-25 21:46:44'),
(44, 'NAOS-44-1774475526', 44, '50.00', 'MWK', 'completed', 'airtel', 'MP260325.2352.872806', '2026-03-25 21:52:06', '2026-03-25 21:52:42'),
(45, 'NAOS-45-1776008057', 45, '50.00', 'MWK', 'completed', 'airtel', 'BP260412.1734.544541', '2026-04-12 15:34:17', '2026-04-12 15:35:06');

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
(35, 14, 1, 'Maize', '50.00', '500000.00', '2026-02-24 07:06:14', 'available'),
(36, 5, 1, 'maize', '1.00', '56.00', '2026-03-26 00:16:40', 'available');

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
(11, 14, 'farmer', 'active', 'trial', '2026-02-28 07:42:43', NULL, '2026-02-17 06:05:14', '2026-02-24 05:43:00'),
(12, 14, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-17 06:05:14', '2026-02-17 06:05:14'),
(13, 30, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-23 18:37:21', '2026-02-23 18:37:21'),
(14, 31, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-02-24 06:36:22', '2026-02-24 06:36:22'),
(15, 32, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-02-24 08:40:59', '2026-02-24 08:40:59'),
(16, 33, 'farmer', 'inactive', 'trial', NULL, NULL, '2026-03-23 20:03:33', '2026-03-23 20:03:33'),
(17, 5, 'farmer', 'expired', 'monthly', '2026-03-25 23:29:39', '2026-04-24 22:29:39', '2026-03-25 21:29:39', '2026-03-25 21:46:44'),
(18, 5, 'farmer', 'expired', 'monthly', '2026-03-25 23:46:44', '2026-04-24 22:46:44', '2026-03-25 21:46:44', '2026-03-25 21:52:42'),
(19, 5, 'farmer', 'active', 'monthly', '2026-03-25 23:52:42', '2026-04-24 22:52:42', '2026-03-25 21:52:42', '2026-03-25 21:52:42'),
(20, 34, 'farmer', 'expired', 'trial', NULL, NULL, '2026-04-12 14:33:58', '2026-04-12 15:35:06'),
(21, 35, 'buyer', 'inactive', 'trial', NULL, NULL, '2026-04-12 15:10:46', '2026-04-12 15:10:46'),
(22, 34, 'farmer', 'active', 'monthly', '2026-04-12 17:35:06', '2026-05-12 17:35:06', '2026-04-12 15:35:06', '2026-04-12 15:35:06');

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
  `id_verification_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `phone_number`, `password_hash`, `role`, `active_role`, `gender`, `location`, `lang`, `is_active`, `created_at`, `updated_at`, `subscription_status`, `profile_picture`, `is_phone_verified`, `phone_verification_code`, `is_profile_complete`, `national_id_path`, `id_verification_status`, `id_verification_notes`) VALUES
(1, 'Albert', '0880870323', '$2y$10$8/IUPwBnRTtVrRrO8eofe.0S4wsgsmYg/g1r3lgatZNhcEMMzLGEa', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-03-19 09:52:25', 'inactive', 'profile_1_1768304847.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(2, 'AGM', '0880779557', '$2y$10$mJp1mk0FOmM43oEfvvF0deAcECUv/OwCvzMkCy306V4x6udbCd3Ne', 'buyer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:24', 'inactive', 'profile_2_1768248762.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(4, 'Davis', '0996576755', '$2y$10$G5flDc4eb3rPyp0FCF46IOq4cw1Fn19OR.XikJ7lhrp4Iuf1OYnbO', 'admin', NULL, 'male', '', 'en', 1, '2025-12-31 15:53:12', '2026-02-20 17:54:20', 'inactive', 'profile_4_1768244657.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(5, 'Praise Msiska', '0997079797', '$2y$10$mkag6pY31wM1eTOsdqASseTilX7jdbFPJpgc.SYpkebF7glTNtB4m', 'farmer', 'farmer', 'female', 'Mzuzu', 'en', 1, '2026-01-09 20:21:15', '2026-04-12 14:31:01', 'active', 'profile_5_1767990188.jpg', 1, '033858', 1, NULL, 'approved', NULL),
(6, 'Albert Msiska', '0894970323', '$2y$10$kjZJszfvYE/.6WG9tSugUOD.65xie9F6s/TI5lKHT2e4Klt0vGvna', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-01-12 18:50:40', '2026-02-14 12:57:19', 'inactive', 'profile_6_1768244238.jpg', 1, '560771', 1, NULL, 'approved', NULL),
(7, 'Monalisa Mwenitete', '0991799034', '$2y$10$aRveUxTelo.Dn0Q5bb8N5O.B/7hIx5rUAaOj6ItmiXkHjdiBIt1iO', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 0, '2026-01-29 20:24:09', '2026-02-06 13:51:48', 'inactive', 'default_avatar.png', 1, NULL, 0, NULL, 'approved', NULL),
(8, 'puli', '0994507855', '$2y$10$A7NQRvHe0xrFn/Dv3xsUsOALk4DOtYVqZn9OVRxuKPOHd7lkFhoca', 'farmer', 'farmer', 'male', 'lilongwe', 'en', 1, '2026-02-04 05:56:29', '2026-02-06 13:51:48', 'inactive', 'profile_8_1770184966.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(9, 'Emmanuel Tembo', '0894970328', '$2y$10$HNZlhpHiB0GhkfnaHasm.ulK29RMiM53KsCQkDxjSB.fWNUDDFBay', 'farmer', 'farmer', 'male', 'Mzuzu', 'en', 1, '2026-02-06 13:44:15', '2026-02-06 13:51:48', 'inactive', 'profile_9_1770385558.png', 1, NULL, 1, NULL, 'approved', NULL),
(10, 'Rejoice Msiska', '0998121260', '$2y$10$PXoVVL/hk/9w8c3hH.Iz5O5oU7ljBBq00G4baWigvSrGfEEN.vmNu', 'admin', NULL, NULL, 'Mzuzu', 'en', 1, '2026-02-11 21:33:03', '2026-02-11 21:42:15', 'inactive', 'profile_10_1770846135.png', 1, NULL, 1, NULL, 'approved', NULL),
(11, 'Mona', '0993740077', '$2y$10$Flqg8f8NLCA5vKEDrxBxp..j8kiLotmkyWdjCAikWeZvAz39CC2HO', 'buyer', 'buyer', 'female', 'lilongwe', 'en', 1, '2026-02-14 13:02:29', '2026-02-22 13:54:28', 'inactive', 'default_avatar.png', 1, '126408', 0, NULL, 'approved', NULL),
(12, 'Patience Banda', '0993807482', '$2y$10$p5hKCEVipgdhtvjEYN6f2.WYQTnnfv9s4ZZQpeI7mO2lJTyCk77cG', 'buyer', 'buyer', 'male', 'lilongwe', 'en', 1, '2026-02-14 15:42:27', '2026-02-14 15:45:40', 'inactive', 'profile_12_1771083940.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(14, 'Davis AGM', '0994308469', '$2y$10$LgBWWtS5Wf6pYSthb1jT3.mQbZZrs8H2FIhLEZGUB1tov4DgbeLQC', 'farmer', 'buyer', 'male', 'Mzuzu', 'en', 1, '2026-02-17 06:05:14', '2026-04-14 10:03:49', 'active', 'profile_14_1771418940.jpg', 1, NULL, 1, NULL, 'approved', NULL),
(30, 'Mesho Mesho', '0983767840', '$2y$10$/NA9.wjY2DuavGU9U/JdOeor6b6VELB5EV2l4ly0K5Eqg5QXdaybi', 'buyer', 'buyer', 'male', 'Karonga', 'en', 1, '2026-02-23 18:37:21', '2026-02-24 05:04:00', 'inactive', 'profile_30_1771871917.png', 1, NULL, 1, NULL, 'approved', NULL),
(31, 'Misheck Khobiri', '0995127864', '$2y$10$oyUKs2Vphhnt2tOt32JCC.p852KxqiPtLMTMn0pYDVhbdhqboRIZC', 'buyer', 'buyer', 'male', 'Chitipa', 'en', 1, '2026-02-24 06:36:22', '2026-02-24 06:37:48', 'inactive', 'profile_31_1771915068.png', 1, NULL, 1, NULL, 'approved', NULL),
(32, 'Emmanuel', '0995365664', '$2y$10$OfMv1rekCSiKQ/p46lNJOeoYzhqdcZwka8V6769XC9yPtTbo4UDWy', 'farmer', 'farmer', 'male', 'Rumphi', 'en', 1, '2026-02-24 08:40:59', '2026-03-18 14:54:17', 'inactive', 'profile_32_1771922529.png', 1, NULL, 1, NULL, 'approved', NULL),
(33, 'Vuyo', '+26599370232', '$2y$10$gV6q2lk7udVPreiIW9JSd.LlxYyUColUlEOnOEEOexjoAELFfAjqe', 'farmer', 'farmer', 'male', 'Chitipa', 'en', 1, '2026-03-23 20:03:33', '2026-04-12 14:39:37', 'inactive', 'default_avatar.png', 0, '358127', 0, 'assets/uploads/national_ids/id_33_1774296213.jpg', 'rejected', ''),
(34, 'Ethel Chirwa', '0999130104', '$2y$10$cYvgao9hDuT3LkEbxPFRfe6Or1p3WLJ0mQDcrgSq5x4P4MmgdAZxC', 'farmer', 'farmer', 'female', 'Chitipa', 'en', 1, '2026-04-12 14:33:58', '2026-04-12 15:35:06', 'active', 'profile_34_1776007675.jpg', 1, NULL, 1, 'assets/uploads/national_ids/id_34_1776004438.jpg', 'approved', NULL),
(35, 'Tawonga Phiri', '0995797793', '$2y$10$yVUEvQVPXHTnraWj2QbB1e7KCW95/CwDzoTww.bABhkrCqEBhpFY.', 'buyer', 'buyer', 'female', 'Mzuzu', 'en', 1, '2026-04-12 15:10:46', '2026-04-12 15:25:09', 'inactive', 'profile_35_1776007509.jpg', 1, NULL, 1, 'assets/uploads/national_ids/id_35_1776006646.jpg', 'approved', NULL);

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
(20, 35, 'buyer', 1, '2026-04-12 15:10:46');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `farm_activities`
--
ALTER TABLE `farm_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `produce_listings`
--
ALTER TABLE `produce_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
