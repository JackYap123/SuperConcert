-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 05:03 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `superconcert`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_name`, `email`, `password`, `phone_number`) VALUES
(1, 'Yap Fong Kiat', 'admin@superconcert.com', 'Admin123', 1234567890);

-- --------------------------------------------------------

--
-- Table structure for table `attendee`
--

CREATE TABLE `attendee` (
  `attendee_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `password` varchar(12) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendee`
--

INSERT INTO `attendee` (`attendee_id`, `ticket_id`, `full_name`, `password`, `email`, `phone_number`) VALUES
(1, 0, 'Yap Fong Kiat', '12345', 'yapfongkiat53@gmail.com', NULL),
(2, 0, 'Yap Fong Kiat', '123', 'fiwobo9465@jomspar.com', NULL),
(5, 0, 'Test1', '123', 'abc@gmail.com', NULL),
(6, 0, 'Yap Fong Kiat', '123', 'yap@mail.com', NULL),
(7, 0, 'test', '123', 'test12@gmail.com', NULL),
(8, 0, 'Yap Fong Kiat', '123', 'yap53@gmail.com', NULL),
(9, 0, 'ya', '123', 'j@gmail.com', NULL),
(10, 0, 'Yap Fong Kiat', '123', 'manexig556@lesotica.com', NULL),
(11, 0, 'Jack', '123', 'keweces479@naobk.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `row_label` varchar(10) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `category` enum('VIP','Regular','Economy') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','cancelled') DEFAULT 'active',
  `refund_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `attendee_id`, `event_id`, `row_label`, `seat_number`, `payment_method`, `payment_ref`, `category`, `price`, `booking_time`, `status`, `refund_time`) VALUES
(60, 10, 31, 'DD', 'DD109', NULL, NULL, 'VIP', 200.00, '2025-04-03 16:04:59', 'active', NULL),
(64, 10, 31, 'CC', 'CC82', NULL, NULL, 'VIP', 200.00, '2025-04-03 16:09:12', 'active', NULL),
(68, 9, 31, 'BB', 'BB53', NULL, NULL, 'VIP', 200.00, '2025-04-05 01:41:25', 'cancelled', '2025-04-05 04:03:40'),
(72, 9, 31, 'CC', 'CC79', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:04:00', 'cancelled', '2025-04-05 04:04:22'),
(74, 9, 31, 'CC', 'CC77', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:07:57', 'cancelled', '2025-04-05 04:08:15'),
(76, 9, 31, 'DD', 'DD110', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:12:37', 'cancelled', '2025-04-05 04:12:46'),
(82, 10, 31, 'DD', 'DD113', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:32:23', 'active', NULL),
(83, 10, 31, 'BB', 'BB51', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:37:04', 'active', NULL),
(85, 10, 31, 'DD', 'DD114', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:41:12', 'active', NULL),
(86, 10, 31, 'BB', 'BB49', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:46:36', 'active', NULL),
(87, 10, 31, 'CC', 'CC80', NULL, NULL, 'VIP', 200.00, '2025-04-05 02:52:00', 'active', NULL),
(90, 10, 31, 'CC', 'CC83', NULL, NULL, 'VIP', 200.00, '2025-04-05 03:25:10', 'active', NULL),
(94, 10, 35, 'A', 'A11', NULL, NULL, 'Regular', 150.00, '2025-04-05 08:10:39', 'active', NULL),
(95, 10, 35, 'A', 'A12', NULL, NULL, 'Regular', 150.00, '2025-04-05 08:10:39', 'active', NULL),
(96, 10, 35, 'A', 'A13', NULL, NULL, 'Regular', 150.00, '2025-04-05 08:10:39', 'active', NULL),
(97, 10, 35, 'A', 'A14', NULL, NULL, 'Regular', 150.00, '2025-04-05 08:10:39', 'active', NULL),
(98, 10, 35, 'A', 'A15', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(99, 10, 35, 'A', 'A16', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(100, 10, 35, 'A', 'A17', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(101, 10, 35, 'A', 'A18', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(102, 10, 35, 'A', 'A19', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(103, 10, 35, 'A', 'A20', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(104, 10, 35, 'A', 'A21', NULL, NULL, 'VIP', 200.00, '2025-04-05 08:10:39', 'active', NULL),
(133, 8, 36, 'E', 'E144', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:31:43', 'active', NULL),
(134, 8, 36, 'E', 'E145', NULL, NULL, 'VIP', 400.00, '2025-04-07 05:31:43', 'active', NULL),
(135, 8, 36, 'E', 'E146', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:31:43', 'active', NULL),
(136, 8, 36, 'E', 'E147', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:31:43', 'active', NULL),
(137, 8, 36, 'G', 'G206', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:31:43', 'active', NULL),
(138, 8, 36, 'G', 'G207', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:31:43', 'active', NULL),
(139, 8, 36, 'G', 'G208', NULL, NULL, 'VIP', 400.00, '2025-04-07 05:31:43', 'active', NULL),
(140, 8, 36, 'G', 'G209', NULL, NULL, 'VIP', 400.00, '2025-04-07 05:31:43', 'active', NULL),
(141, 8, 36, 'G', 'G210', NULL, NULL, 'VIP', 400.00, '2025-04-07 05:31:43', 'active', NULL),
(155, 8, 36, 'E', 'E142', NULL, NULL, 'Economy', 100.00, '2025-04-07 05:37:32', 'active', NULL),
(160, 1, 35, 'H', 'H243', NULL, NULL, 'VIP', 200.00, '2025-04-08 11:53:08', 'active', NULL),
(164, 1, 35, 'A', 'A22', NULL, NULL, 'VIP', 200.00, '2025-04-11 03:54:57', 'active', NULL),
(165, 1, 36, 'E', 'E143', NULL, NULL, 'Regular', 200.00, '2025-04-11 04:04:15', 'active', NULL),
(166, 1, 36, 'G', 'G211', NULL, NULL, 'VIP', 400.00, '2025-04-11 04:14:38', 'active', NULL),
(168, 1, 35, 'CC', 'CC80', NULL, NULL, 'VIP', 200.00, '2025-04-11 06:18:56', 'active', NULL),
(169, 1, 35, 'CC', 'CC81', NULL, NULL, 'VIP', 200.00, '2025-04-15 02:07:18', 'active', NULL),
(170, 1, 35, 'CC', 'CC82', NULL, NULL, 'VIP', 200.00, '2025-04-15 02:07:18', 'active', NULL),
(171, 1, 42, 'FF', 'FF171', NULL, NULL, 'VIP', 200.00, '2025-04-15 02:23:47', 'active', NULL),
(172, 1, 42, 'FF', 'FF172', NULL, NULL, 'Regular', 150.00, '2025-04-15 02:23:47', 'active', NULL),
(173, 1, 42, 'FF', 'FF173', NULL, NULL, 'Economy', 100.00, '2025-04-15 02:23:47', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `event_description` varchar(100) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `event_duration` int(11) NOT NULL,
  `vip_price` decimal(10,2) DEFAULT NULL,
  `regular_price` decimal(10,2) DEFAULT NULL,
  `economy_price` decimal(10,2) DEFAULT NULL,
  `promo_code` varchar(100) DEFAULT NULL,
  `promo_discount` decimal(5,2) DEFAULT NULL,
  `promo_limit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `organizer_id`, `event_name`, `event_date`, `event_time`, `event_description`, `file_name`, `event_duration`, `vip_price`, `regular_price`, `economy_price`, `promo_code`, `promo_discount`, `promo_limit`) VALUES
(7, 29, 'Test Event', '2025-03-05', '23:07:00', 'test', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 1, 'Test', '2025-03-10', '18:18:00', 'Hello', '1741601928_Resgister Account doesn\'t exsits.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 1, 'Test Event123', '2025-03-10', '20:11:00', '123', '1741608675_Attendee Login Error Page(1).png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 1, 'TestEvent1', '2025-03-10', '20:28:00', '123', '1741609731_Resgister Account doesn\'t exsits.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 1, 'Test Event1232', '2025-03-10', '20:38:00', '123', '1741610306_Resgister Account doesn\'t exsits.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 39, 'Test Event', '2025-03-10', '20:45:00', 'Hello', '1741610723_Resgister Account doesn\'t exsits.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 34, '123', '2025-03-12', '00:33:00', 'test', '1741659222_Attendee Register Account(2).png', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 36, '1233', '2025-03-03', '00:31:00', '1', '1741659711_Screenshot 2024-08-26 232312.png', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 36, '1234', '2025-03-11', '12:31:00', '1', '1741659728_Screenshot 2024-09-03 180155.png', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 36, '124234', '2025-03-03', '00:31:00', '123', '1741659745_Screenshot 2024-08-24 175949.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 41, 'Testing css and bs', '2025-03-12', '13:35:00', 'Hello World', '1741620756_1380532.png', 3, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 41, 'Testing 2', '2025-03-21', '14:30:00', 'Hello World 2', '1741620806_29145.jpg', 4, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 41, 'Tōgeya', '2025-03-11', '11:00:00', 'Featuring Cars', '1741620875_car_bg.jpg', 5, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 45, 'Test Eventhklakds', '2025-04-08', '15:56:00', 'Good event', '1743667006_register Account.png', 2, 200.00, 150.00, 100.00, 'Haha', 15.00, 200),
(32, 45, 'Test Event', '2025-04-03', '18:04:00', 'Hello Is me', '1743674704_register Account.png', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 44, 'Test Event', '2025-04-03', '19:56:00', 'This is a good event\r\n', '1743681418_login_BG.png', 2, 200.00, 120.00, 150.00, 'EarlyBIrd', 15.00, 100),
(35, 22, 'Jack', '2025-04-15', '04:09:00', '123', '1743840572_istockphoto-1093142896-612x612.jpg', 3, 200.00, 150.00, 120.00, 'Jack', 10.00, 10),
(36, 22, 'Hi Ha', '2025-04-10', '14:30:00', 'Hello World\r\n', '1744003687_Picture1.png', 2, 400.00, 200.00, 100.00, 'GGCOM', 20.00, 20),
(41, 48, 'Test Event', '2025-04-08', '09:11:00', '22', '1744114292_istockphoto-1093142896-612x612-removebg-preview.png', 2, 200.00, 150.00, 100.00, 'Hello', 15.00, 10),
(42, 22, 'HelloWorld', '2025-04-08', '17:00:00', 'Hello World Event', '1744271982_Brand Architecture Whiteboard (1).png', 5, 200.00, 150.00, 100.00, 'Test', 10.00, 10),
(45, 22, 'w', '2025-04-04', '02:07:00', 'ww', '1744344338_Brand Architecture Whiteboard (1).png', 2, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_seats`
--

CREATE TABLE `event_seats` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `row_label` varchar(10) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `category` enum('VIP','Regular','Economy') NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_seats`
--

INSERT INTO `event_seats` (`id`, `event_id`, `row_label`, `seat_number`, `category`, `price`) VALUES
(225, 31, 'BB', 'BB43', 'VIP', 200.00),
(226, 31, 'BB', 'BB44', 'VIP', 200.00),
(227, 31, 'BB', 'BB45', 'VIP', 200.00),
(228, 31, 'BB', 'BB46', 'VIP', 200.00),
(229, 31, 'BB', 'BB47', 'VIP', 200.00),
(230, 31, 'BB', 'BB48', 'VIP', 200.00),
(231, 31, 'BB', 'BB49', 'VIP', 200.00),
(232, 31, 'BB', 'BB51', 'VIP', 200.00),
(234, 31, 'BB', 'BB52', 'VIP', 200.00),
(235, 31, 'BB', 'BB53', 'VIP', 200.00),
(236, 31, 'BB', 'BB54', 'VIP', 200.00),
(237, 31, 'CC', 'CC86', 'VIP', 200.00),
(238, 31, 'CC', 'CC85', 'VIP', 200.00),
(239, 31, 'CC', 'CC84', 'VIP', 200.00),
(240, 31, 'CC', 'CC83', 'VIP', 200.00),
(241, 31, 'CC', 'CC82', 'VIP', 200.00),
(242, 31, 'CC', 'CC81', 'VIP', 200.00),
(243, 31, 'CC', 'CC80', 'VIP', 200.00),
(244, 31, 'CC', 'CC79', 'VIP', 200.00),
(245, 31, 'CC', 'CC77', 'VIP', 200.00),
(246, 31, 'CC', 'CC76', 'VIP', 200.00),
(247, 31, 'CC', 'CC75', 'VIP', 200.00),
(248, 31, 'CC', 'CC78', 'VIP', 200.00),
(249, 31, 'DD', 'DD107', 'VIP', 200.00),
(250, 31, 'DD', 'DD108', 'VIP', 200.00),
(251, 31, 'DD', 'DD109', 'VIP', 200.00),
(252, 31, 'DD', 'DD111', 'VIP', 200.00),
(253, 31, 'DD', 'DD110', 'VIP', 200.00),
(254, 31, 'DD', 'DD113', 'VIP', 200.00),
(255, 31, 'DD', 'DD112', 'VIP', 200.00),
(256, 31, 'DD', 'DD115', 'VIP', 200.00),
(257, 31, 'DD', 'DD114', 'VIP', 200.00),
(258, 31, 'DD', 'DD116', 'VIP', 200.00),
(259, 31, 'DD', 'DD117', 'VIP', 200.00),
(260, 31, 'DD', 'DD118', 'VIP', 200.00),
(262, 31, 'G', 'G218', 'VIP', 200.00),
(263, 31, 'EE', 'EE154', 'VIP', 200.00),
(264, 33, 'BB', 'BB49', 'Regular', 120.00),
(265, 33, 'CC', 'CC81', 'Economy', 150.00),
(266, 33, 'DD', 'DD113', 'VIP', 200.00),
(267, 33, 'DD', 'DD115', 'Regular', 120.00),
(268, 33, 'EE', 'EE148', 'VIP', 200.00),
(269, 33, 'FF', 'FF180', 'VIP', 200.00),
(273, 35, 'A', 'A12', 'Regular', 150.00),
(274, 35, 'A', 'A13', 'Regular', 150.00),
(275, 35, 'A', 'A14', 'Regular', 150.00),
(276, 35, 'A', 'A15', 'VIP', 200.00),
(277, 35, 'A', 'A16', 'VIP', 200.00),
(278, 35, 'A', 'A17', 'VIP', 200.00),
(279, 35, 'A', 'A18', 'VIP', 200.00),
(280, 35, 'A', 'A19', 'VIP', 200.00),
(281, 35, 'A', 'A20', 'VIP', 200.00),
(282, 35, 'A', 'A21', 'VIP', 200.00),
(283, 35, 'A', 'A22', 'VIP', 200.00),
(284, 36, 'E', 'E143', 'Regular', 200.00),
(285, 36, 'E', 'E144', 'Economy', 100.00),
(286, 36, 'E', 'E145', 'VIP', 400.00),
(287, 36, 'E', 'E146', 'Economy', 100.00),
(288, 36, 'E', 'E147', 'Economy', 100.00),
(289, 36, 'E', 'E142', 'Economy', 100.00),
(290, 36, 'G', 'G206', 'Economy', 100.00),
(291, 36, 'G', 'G207', 'Economy', 100.00),
(292, 36, 'G', 'G208', 'VIP', 400.00),
(293, 36, 'G', 'G209', 'VIP', 400.00),
(294, 36, 'G', 'G210', 'VIP', 400.00),
(295, 36, 'G', 'G211', 'VIP', 400.00),
(305, 35, 'CC', 'CC80', 'VIP', 200.00),
(306, 35, 'CC', 'CC81', 'VIP', 200.00),
(307, 35, 'CC', 'CC82', 'VIP', 200.00),
(308, 35, 'H', 'H242', 'VIP', 200.00),
(309, 35, 'H', 'H244', 'VIP', 200.00),
(310, 35, 'H', 'H243', 'VIP', 200.00),
(321, 36, 'DD', 'DD112', 'Regular', 200.00),
(322, 36, 'DD', 'DD113', 'VIP', 400.00),
(323, 36, 'FF', 'FF178', 'VIP', 400.00),
(335, 41, 'AA', 'AA15', 'VIP', 200.00),
(336, 41, 'AA', 'AA16', 'Economy', 100.00),
(337, 41, 'AA', 'AA17', 'Regular', 150.00),
(338, 42, 'FF', 'FF171', 'VIP', 200.00),
(339, 42, 'FF', 'FF172', 'Regular', 150.00),
(340, 42, 'FF', 'FF173', 'Economy', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `event_transaction`
--

CREATE TABLE `event_transaction` (
  `transaction_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `organiser_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `seat_id` int(11) DEFAULT NULL,
  `status` enum('purchased','pending','cancelled') DEFAULT 'purchased'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organisers`
--

CREATE TABLE `organisers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `organization_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_first_login` tinyint(1) DEFAULT 1,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organisers`
--

INSERT INTO `organisers` (`id`, `name`, `email`, `phone_number`, `organization_name`, `password`, `is_first_login`, `is_admin`) VALUES
(22, 'Yap Fong Kiat', 'pijey75182@eluxeer.com', '0127903684', '', '1', 0, 0),
(23, 'Yap Fong Kiat', 'jadire3963@eluxeer.com', '0127903684', '', '8240897b', 1, 0),
(24, 'Yap Fong Kiat', 'hobapo8649@bmixr.com', '0127903684', '', '123', 0, 0),
(25, 'Yap Fong Kiat', 'potaco4208@bmixr.com', '0127903684', '', '123', 0, 0),
(26, 'Yap Fong Kiat', 'legici9371@jarars.com', '0127903684', '', '', 1, 0),
(27, 'Yap Fong Kiat', 'daxeti5049@lxheir.com', '0127903684', '', '123', 0, 0),
(28, 'Yap Fong Kiat', 'yapfongkiat53@gmail.com', '0127903684', '', '83946a60', 1, 0),
(30, 'Yap Fong Kiat', 'yonep23300@jomspar.com', '0127903684', 'JJ Lim Sdn Berhard', '6a3235cf', 1, 0),
(31, 'Yap Fong Kiat', 'sasir67720@hartaria.com', '0127903684', 'JJ Lim Sdn Berhard', '123', 0, 0),
(32, 'Jowhihe', 'jowihe7453@jomspar.com', '78912032901', 'Jow Company Sdn', 'd0ec75eb', 1, 0),
(33, 'Yap Fong Kiat', 'mawodo9397@hartaria.com', '0127903684', '123', '4baccbda', 1, 0),
(34, 'Yap Fong Kiat', 'dimewi2648@egvoo.com', '0127903684', '', '123', 0, 0),
(35, 'Yap Fong Kiat', 'necici5520@jomspar.com', '0127903684', '', '6a8b04ef', 1, 0),
(36, 'Yap Fong Kiat', 'fiwobo9465@jomspar.com', '0127903684', '', '123', 0, 0),
(40, 'Test', 'seropa1168@oziere.com', '0127903684', '', '123', 0, 0),
(42, 'Yap Fong Kiat', 'pikivib412@payposs.com', '0127903684', '', '123', 0, 0),
(43, 'Testing2', 'bawawe4395@oziere.com', '0123456789', '', '123', 0, 0),
(44, 'Test12', 'rinatil364@dmener.com', '0127903684', 'Test Organiser', '123', 0, 0),
(45, 'Test', 'honofiy614@hartaria.com', '0123456789', 'Test Organiser', '123', 0, 0),
(46, 'Yap Fong Kiat', 'pamik61472@movfull.com', '0127903684', 'Hello', '21591b1d', 1, 0),
(47, 'Yap Fong Kiat', 'renoy91753@lesotica.com', '0127903684', '', 'e95332d0', 1, 0),
(48, 'Yap Fong Kiat', 'pepiger332@movfull.com', '0127903684', '', '123', 0, 0),
(49, 'hihi', 'hihi@gmail.com', '012-3456-7890', 'JackNization', '391a9053', 1, 0),
(50, 'Dida', 'didamib158@naobk.com', '012-3456-7890', 'DD', '6d84b1d9', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `payment_met` varchar(50) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `transaction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL,
  `discount_perc` int(11) NOT NULL,
  `expire_date` date NOT NULL,
  `promo_condition` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat`
--

CREATE TABLE `seat` (
  `seat_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `seat_row` varchar(10) DEFAULT NULL,
  `seat_number` varchar(10) NOT NULL,
  `seat_status` enum('available','booked') NOT NULL DEFAULT 'available',
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat_bookings`
--

CREATE TABLE `seat_bookings` (
  `booking_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` enum('booked','cancelled') DEFAULT 'booked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `promotion_id` int(11) DEFAULT NULL,
  `categories` varchar(50) NOT NULL,
  `prices` decimal(8,2) NOT NULL,
  `ticket_quantity` int(11) NOT NULL,
  `seat_id` varchar(50) NOT NULL,
  `sale_date` date NOT NULL DEFAULT curdate(),
  `quantity_sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `event_id`, `promotion_id`, `categories`, `prices`, `ticket_quantity`, `seat_id`, `sale_date`, `quantity_sold`) VALUES
(1, 1, NULL, 'VIP', 100.00, 50, 'A1', '2025-03-01', 10),
(2, 1, NULL, 'General', 200.00, 100, 'A2', '2025-03-02', 30),
(3, 1, NULL, 'VIP', 100.00, 50, 'A1', '2025-03-01', 10),
(4, 1, NULL, 'General', 50.00, 100, 'A2', '2025-03-02', 30);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_sales`
--

CREATE TABLE `ticket_sales` (
  `sale_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `organiser_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waiting_list`
--

CREATE TABLE `waiting_list` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `status` enum('waiting','notified','expired') DEFAULT 'waiting',
  `join_time` datetime DEFAULT current_timestamp(),
  `notified_time` datetime DEFAULT NULL,
  `request_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waiting_list`
--

INSERT INTO `waiting_list` (`id`, `event_id`, `attendee_id`, `status`, `join_time`, `notified_time`, `request_time`) VALUES
(7, 9, 10, '', '2025-04-05 16:03:12', NULL, '2025-04-05 16:03:12'),
(8, 7, 10, '', '2025-04-05 16:03:36', NULL, '2025-04-05 16:03:36'),
(11, 32, 10, '', '2025-04-06 09:58:46', NULL, '2025-04-06 09:58:46'),
(12, 32, 1, '', '2025-04-07 11:14:39', NULL, '2025-04-07 11:14:39'),
(15, 7, 1, '', '2025-04-08 19:39:24', NULL, '2025-04-08 19:39:24'),
(16, 42, 1, '', '2025-04-11 12:18:47', NULL, '2025-04-11 12:18:47'),
(17, 19, 1, '', '2025-04-15 10:25:10', NULL, '2025-04-15 10:25:10'),
(18, 27, 1, '', '2025-04-15 10:34:00', NULL, '2025-04-15 10:34:00'),
(19, 26, 1, '', '2025-04-15 10:34:41', NULL, '2025-04-15 10:34:41');

-- --------------------------------------------------------

--
-- Table structure for table `waitlist`
--

CREATE TABLE `waitlist` (
  `waitlist_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendee`
--
ALTER TABLE `attendee`
  ADD PRIMARY KEY (`attendee_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`seat_number`),
  ADD KEY `attendee_id` (`attendee_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `event_seats`
--
ALTER TABLE `event_seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`row_label`,`seat_number`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `event_id_2` (`event_id`,`seat_number`);

--
-- Indexes for table `event_transaction`
--
ALTER TABLE `event_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `organiser_id` (`organiser_id`),
  ADD KEY `attendee_id` (`attendee_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `organisers`
--
ALTER TABLE `organisers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `attendee_id` (`attendee_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `attendee_id` (`attendee_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `promotion_id` (`promotion_id`);

--
-- Indexes for table `ticket_sales`
--
ALTER TABLE `ticket_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `organiser_id` (`organiser_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `waiting_list`
--
ALTER TABLE `waiting_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `attendee_id` (`attendee_id`);

--
-- Indexes for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD PRIMARY KEY (`waitlist_id`),
  ADD KEY `attendee_id` (`attendee_id`),
  ADD KEY `event_id` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendee`
--
ALTER TABLE `attendee`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `event_seats`
--
ALTER TABLE `event_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341;

--
-- AUTO_INCREMENT for table `event_transaction`
--
ALTER TABLE `event_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `organisers`
--
ALTER TABLE `organisers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seat`
--
ALTER TABLE `seat`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_sales`
--
ALTER TABLE `ticket_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `waiting_list`
--
ALTER TABLE `waiting_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `waitlist`
--
ALTER TABLE `waitlist`
  MODIFY `waitlist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`attendee_id`) REFERENCES `attendee` (`attendee_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`);

--
-- Constraints for table `event_seats`
--
ALTER TABLE `event_seats`
  ADD CONSTRAINT `event_seats_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_transaction`
--
ALTER TABLE `event_transaction`
  ADD CONSTRAINT `event_transaction_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_transaction_ibfk_2` FOREIGN KEY (`organiser_id`) REFERENCES `organisers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_transaction_ibfk_3` FOREIGN KEY (`attendee_id`) REFERENCES `attendee` (`attendee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_transaction_ibfk_4` FOREIGN KEY (`seat_id`) REFERENCES `event_seats` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `seat`
--
ALTER TABLE `seat`
  ADD CONSTRAINT `seat_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  ADD CONSTRAINT `seat_bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seat_bookings_ibfk_2` FOREIGN KEY (`attendee_id`) REFERENCES `attendee` (`attendee_id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_sales`
--
ALTER TABLE `ticket_sales`
  ADD CONSTRAINT `ticket_sales_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_sales_ibfk_2` FOREIGN KEY (`organiser_id`) REFERENCES `organisers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_sales_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `attendee` (`attendee_id`) ON DELETE CASCADE;

--
-- Constraints for table `waiting_list`
--
ALTER TABLE `waiting_list`
  ADD CONSTRAINT `waiting_list_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `waiting_list_ibfk_2` FOREIGN KEY (`attendee_id`) REFERENCES `attendee` (`attendee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
