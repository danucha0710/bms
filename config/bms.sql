-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 09:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bms`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `bw_id` int(10) UNSIGNED NOT NULL,
  `mem_id` varchar(13) NOT NULL,
  `br_id` int(10) UNSIGNED NOT NULL,
  `bw_amount` int(10) UNSIGNED NOT NULL,
  `bw_round` varchar(5) NOT NULL,
  `bw_status` tinyint(1) UNSIGNED NOT NULL COMMENT '0=ยังไม่จ่าย, 1=จ่ายแล้ว',
  `bw_date_pay` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing`
--

INSERT INTO `borrowing` (`bw_id`, `mem_id`, `br_id`, `bw_amount`, `bw_round`, `bw_status`, `bw_date_pay`) VALUES
(6, '1111111111111', 5, 1038, '1/5', 1, '2026-03-01'),
(7, '1111111111111', 5, 1030, '2/5', 1, '2026-02-24'),
(8, '1111111111111', 5, 1023, '3/5', 0, '2026-05-01'),
(9, '1111111111111', 5, 1015, '4/5', 0, '2026-06-01'),
(10, '1111111111111', 5, 1008, '5/5', 0, '2026-07-01'),
(11, '3333333333333', 6, 1300, '1/40', 1, '2026-02-24'),
(12, '3333333333333', 6, 1293, '2/40', 1, '2026-02-24'),
(13, '3333333333333', 6, 1285, '3/40', 1, '2026-02-24'),
(14, '3333333333333', 6, 1278, '4/40', 0, '2026-06-01'),
(15, '3333333333333', 6, 1270, '5/40', 0, '2026-07-01'),
(16, '3333333333333', 6, 1263, '6/40', 0, '2026-08-01'),
(17, '3333333333333', 6, 1255, '7/40', 0, '2026-09-01'),
(18, '3333333333333', 6, 1248, '8/40', 0, '2026-10-01'),
(19, '3333333333333', 6, 1240, '9/40', 0, '2026-11-01'),
(20, '3333333333333', 6, 1233, '10/40', 0, '2026-12-01'),
(21, '3333333333333', 6, 1225, '11/40', 0, '2027-01-01'),
(22, '3333333333333', 6, 1218, '12/40', 0, '2027-02-01'),
(23, '3333333333333', 6, 1210, '13/40', 0, '2027-03-01'),
(24, '3333333333333', 6, 1203, '14/40', 0, '2027-04-01'),
(25, '3333333333333', 6, 1195, '15/40', 0, '2027-05-01'),
(26, '3333333333333', 6, 1188, '16/40', 0, '2027-06-01'),
(27, '3333333333333', 6, 1180, '17/40', 0, '2027-07-01'),
(28, '3333333333333', 6, 1173, '18/40', 0, '2027-08-01'),
(29, '3333333333333', 6, 1165, '19/40', 0, '2027-09-01'),
(30, '3333333333333', 6, 1158, '20/40', 0, '2027-10-01'),
(31, '3333333333333', 6, 1150, '21/40', 0, '2027-11-01'),
(32, '3333333333333', 6, 1143, '22/40', 0, '2027-12-01'),
(33, '3333333333333', 6, 1135, '23/40', 0, '2028-01-01'),
(34, '3333333333333', 6, 1128, '24/40', 0, '2028-02-01'),
(35, '3333333333333', 6, 1120, '25/40', 0, '2028-03-01'),
(36, '3333333333333', 6, 1113, '26/40', 0, '2028-04-01'),
(37, '3333333333333', 6, 1105, '27/40', 0, '2028-05-01'),
(38, '3333333333333', 6, 1098, '28/40', 0, '2028-06-01'),
(39, '3333333333333', 6, 1090, '29/40', 0, '2028-07-01'),
(40, '3333333333333', 6, 1083, '30/40', 0, '2028-08-01'),
(41, '3333333333333', 6, 1075, '31/40', 0, '2028-09-01'),
(42, '3333333333333', 6, 1068, '32/40', 0, '2028-10-01'),
(43, '3333333333333', 6, 1060, '33/40', 0, '2028-11-01'),
(44, '3333333333333', 6, 1053, '34/40', 0, '2028-12-01'),
(45, '3333333333333', 6, 1045, '35/40', 0, '2029-01-01'),
(46, '3333333333333', 6, 1038, '36/40', 0, '2029-02-01'),
(47, '3333333333333', 6, 1030, '37/40', 0, '2029-03-01'),
(48, '3333333333333', 6, 1023, '38/40', 0, '2029-04-01'),
(49, '3333333333333', 6, 1015, '39/40', 0, '2029-05-01'),
(50, '3333333333333', 6, 1008, '40/40', 0, '2029-06-01'),
(51, '6666666666666', 7, 2600, '1/40', 0, '2026-03-01'),
(52, '6666666666666', 7, 2585, '2/40', 0, '2026-04-01'),
(53, '6666666666666', 7, 2570, '3/40', 0, '2026-05-01'),
(54, '6666666666666', 7, 2555, '4/40', 0, '2026-06-01'),
(55, '6666666666666', 7, 2540, '5/40', 0, '2026-07-01'),
(56, '6666666666666', 7, 2525, '6/40', 0, '2026-08-01'),
(57, '6666666666666', 7, 2510, '7/40', 0, '2026-09-01'),
(58, '6666666666666', 7, 2495, '8/40', 0, '2026-10-01'),
(59, '6666666666666', 7, 2480, '9/40', 0, '2026-11-01'),
(60, '6666666666666', 7, 2465, '10/40', 0, '2026-12-01'),
(61, '6666666666666', 7, 2450, '11/40', 0, '2027-01-01'),
(62, '6666666666666', 7, 2435, '12/40', 0, '2027-02-01'),
(63, '6666666666666', 7, 2420, '13/40', 0, '2027-03-01'),
(64, '6666666666666', 7, 2405, '14/40', 0, '2027-04-01'),
(65, '6666666666666', 7, 2390, '15/40', 0, '2027-05-01'),
(66, '6666666666666', 7, 2375, '16/40', 0, '2027-06-01'),
(67, '6666666666666', 7, 2360, '17/40', 0, '2027-07-01'),
(68, '6666666666666', 7, 2345, '18/40', 0, '2027-08-01'),
(69, '6666666666666', 7, 2330, '19/40', 0, '2027-09-01'),
(70, '6666666666666', 7, 2315, '20/40', 0, '2027-10-01'),
(71, '6666666666666', 7, 2300, '21/40', 0, '2027-11-01'),
(72, '6666666666666', 7, 2285, '22/40', 0, '2027-12-01'),
(73, '6666666666666', 7, 2270, '23/40', 0, '2028-01-01'),
(74, '6666666666666', 7, 2255, '24/40', 0, '2028-02-01'),
(75, '6666666666666', 7, 2240, '25/40', 0, '2028-03-01'),
(76, '6666666666666', 7, 2225, '26/40', 0, '2028-04-01'),
(77, '6666666666666', 7, 2210, '27/40', 0, '2028-05-01'),
(78, '6666666666666', 7, 2195, '28/40', 0, '2028-06-01'),
(79, '6666666666666', 7, 2180, '29/40', 0, '2028-07-01'),
(80, '6666666666666', 7, 2165, '30/40', 0, '2028-08-01'),
(81, '6666666666666', 7, 2150, '31/40', 0, '2028-09-01'),
(82, '6666666666666', 7, 2135, '32/40', 0, '2028-10-01'),
(83, '6666666666666', 7, 2120, '33/40', 0, '2028-11-01'),
(84, '6666666666666', 7, 2105, '34/40', 0, '2028-12-01'),
(85, '6666666666666', 7, 2090, '35/40', 0, '2029-01-01'),
(86, '6666666666666', 7, 2075, '36/40', 0, '2029-02-01'),
(87, '6666666666666', 7, 2060, '37/40', 0, '2029-03-01'),
(88, '6666666666666', 7, 2045, '38/40', 0, '2029-04-01'),
(89, '6666666666666', 7, 2030, '39/40', 0, '2029-05-01'),
(90, '6666666666666', 7, 2015, '40/40', 0, '2029-06-01');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_alert`
--

CREATE TABLE `borrow_alert` (
  `ba_id` int(10) UNSIGNED NOT NULL,
  `mem_id` varchar(13) NOT NULL,
  `ba_message` text NOT NULL,
  `ba_date` datetime NOT NULL,
  `ba_read_status` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_alert`
--

INSERT INTO `borrow_alert` (`ba_id`, `mem_id`, `ba_message`, `ba_date`, `ba_read_status`) VALUES
(1, '4444444444444', 'คุณถูกระบุเป็นผู้ค้ำประกันคำขอกู้เลขที่ 7 กรุณาเข้าสู่ระบบเพื่อยืนยันการค้ำประกัน', '2026-02-24 15:37:04', 0),
(2, '5555555555555', 'คุณถูกระบุเป็นผู้ค้ำประกันคำขอกู้เลขที่ 7 กรุณาเข้าสู่ระบบเพื่อยืนยันการค้ำประกัน', '2026-02-24 15:37:04', 0),
(3, '4444444444444', 'คุณถูกระบุเป็นผู้ค้ำประกันคำขอกู้เลขที่ 8 กรุณาเข้าสู่ระบบเพื่อยืนยันการค้ำประกัน', '2026-02-24 15:38:33', 0),
(4, '6666666666666', 'คุณถูกระบุเป็นผู้ค้ำประกันคำขอกู้เลขที่ 8 กรุณาเข้าสู่ระบบเพื่อยืนยันการค้ำประกัน', '2026-02-24 15:38:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_log`
--

CREATE TABLE `borrow_log` (
  `bl_id` int(10) UNSIGNED NOT NULL,
  `mem_id` varchar(13) NOT NULL,
  `bl_text` text NOT NULL,
  `bl_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_log`
--

INSERT INTO `borrow_log` (`bl_id`, `mem_id`, `bl_text`, `bl_date`) VALUES
(1, '0000000000000', 'แก้ไขการตั้งค่าระบบ st_max_amount_common > 80000\n		st_max_amount_emergency > 5000\n		st_amount_cost_teacher > 2000\n		st_amount_cost_officer > 1000\n		st_max_months_common > 40\n		st_max_months_emergency > 5\n		st_interest > 9\n		st_stock_price > 200\n		st_dividend_rate > 4.5\n		st_average_return_rate > 10\n		st_dateline > 30', '2025-01-31 10:45:30'),
(2, '0000000000000', 'แก้ไขการตั้งค่าระบบ st_max_amount_common > 80000\n		st_max_amount_emergency > 5000\n		st_amount_cost_teacher > 2000\n		st_amount_cost_officer > 1000\n		st_max_months_common > 40\n		st_max_months_emergency > 5\n		st_interest > 9\n		st_stock_price > 200\n		st_dividend_rate > 4.5\n		st_average_return_rate > 10\n		st_dateline > 1', '2025-02-04 11:32:16'),
(3, '0000000000000', 'ปรับปรุงการตั้งค่าระบบ: \r\n        [กู้สามัญ: 80000, กู้ฉุกเฉิน: 5000]\r\n        [ดอกเบี้ย: 9%, หุ้น: 200, ปันผล: 4.9%, เฉลี่ยคืน: 10%]\r\n        [ตัดรอบวันที่: 1]', '2026-02-17 14:23:47'),
(4, '0000000000000', 'ปรับปรุงการตั้งค่าระบบ: \r\n        [กู้สามัญ: 80000, กู้ฉุกเฉิน: 5000]\r\n        [ดอกเบี้ย: 9%, หุ้น: 200, ปันผล: 4.5%, เฉลี่ยคืน: 10%]\r\n        [ตัดรอบวันที่: 1]', '2026-02-17 14:23:56'),
(5, '0000000000000', 'ปรับปรุงการตั้งค่าระบบ: \r\n        [กู้สามัญครู: 80000, กู้สามัญเจ้าหน้าที่: 40000, กู้ฉุกเฉิน: 5000]\r\n        [ดอกเบี้ย: 9%, หุ้น: 200, ปันผล: 4.5%, เฉลี่ยคืน: 10%]\r\n        [ตัดรอบวันที่: 1]', '2026-02-23 01:01:54'),
(6, '0000000000000', 'ปรับปรุงการตั้งค่าระบบ: \r\n        [กู้สามัญครู: 80000, กู้สามัญเจ้าหน้าที่: 40000, กู้ฉุกเฉิน: 5000]\r\n        [เงินออมหุ้นขั้นต่ำ: 200, เงินออมหุ้นสูงสุด: 2000]\r\n        [ดอกเบี้ย: 9%, หุ้น: 200, ปันผล: 4.5%, เฉลี่ยคืน: 10%]\r\n        [ตัดรอบวันที่: 1]', '2026-02-24 11:40:55'),
(7, '0000000000000', 'ปรับปรุงการตั้งค่าระบบ: \r\n        [กู้สามัญครู: 80000, กู้สามัญเจ้าหน้าที่: 40000, กู้ฉุกเฉิน: 5000]\r\n        [เงินออมหุ้นขั้นต่ำ: 400, เงินออมหุ้นสูงสุด: 2000]\r\n        [ดอกเบี้ย: 9%, หุ้น: 200, ปันผล: 4.5%, เฉลี่ยคืน: 10%]\r\n        [ตัดรอบวันที่: 1]', '2026-02-24 13:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_request`
--

CREATE TABLE `borrow_request` (
  `br_id` int(10) UNSIGNED NOT NULL,
  `mem_id` varchar(13) NOT NULL,
  `br_type` varchar(50) NOT NULL COMMENT 'ชนิดการกู้เงิน',
  `br_amount` int(10) UNSIGNED NOT NULL,
  `br_months_pay` int(10) UNSIGNED NOT NULL,
  `guarantee_type` varchar(50) NOT NULL COMMENT 'ชนิดการค้ำประกัน',
  `guarantor_1_id` varchar(13) DEFAULT NULL COMMENT 'mem_id ผู้ค้ำคนที่ 1',
  `guarantor_2_id` varchar(13) DEFAULT NULL COMMENT 'mem_id ผู้ค้ำคนที่ 2',
  `guarantor_1_approve` tinyint(1) UNSIGNED DEFAULT 0 COMMENT '0=รอ 1=อนุมัติ 2=ไม่อนุมัติ',
  `guarantor_2_approve` tinyint(1) UNSIGNED DEFAULT 0 COMMENT '0=รอ 1=อนุมัติ 2=ไม่อนุมัติ',
  `guarantor_1_approve_date` datetime DEFAULT NULL,
  `guarantor_2_approve_date` datetime DEFAULT NULL,
  `br_details` text NOT NULL,
  `br_respond` text NOT NULL COMMENT 'ข้อความตอบกลับจากเจ้าหน้าที่',
  `br_status` tinyint(1) UNSIGNED NOT NULL COMMENT '0=ไม่อนุมัติ, 1=อนุมัติ',
  `br_approve_by` varchar(13) NOT NULL,
  `br_interest_rate` float UNSIGNED NOT NULL COMMENT 'อัตราดอกเบี้ย',
  `br_date_request` datetime NOT NULL,
  `br_date_approve` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_request`
--

INSERT INTO `borrow_request` (`br_id`, `mem_id`, `br_type`, `br_amount`, `br_months_pay`, `guarantee_type`, `guarantor_1_id`, `guarantor_2_id`, `guarantor_1_approve`, `guarantor_2_approve`, `guarantor_1_approve_date`, `guarantor_2_approve_date`, `br_details`, `br_respond`, `br_status`, `br_approve_by`, `br_interest_rate`, `br_date_request`, `br_date_approve`) VALUES
(5, '1111111111111', '2', 5000, 5, '1', NULL, NULL, 1, 1, NULL, NULL, '...', '', 1, '0000000000000', 9, '2026-02-23 23:30:21', '2026-02-23 23:30:47'),
(6, '3333333333333', '1', 40000, 40, '1', NULL, NULL, 1, 1, NULL, NULL, '...', '', 1, '0000000000000', 9, '2026-02-23 23:37:38', '2026-02-23 23:37:47'),
(7, '6666666666666', '1', 80000, 40, '1', '4444444444444', '5555555555555', 0, 0, NULL, NULL, '...', '', 1, '0000000000000', 9, '2026-02-24 15:37:04', '2026-02-24 15:37:57'),
(8, '5555555555555', '2', 4000, 5, '1', '4444444444444', '6666666666666', 0, 0, NULL, NULL, '...', '', 1, '0000000000000', 9, '2026-02-24 15:38:33', '2026-02-24 15:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `mem_id` varchar(13) NOT NULL,
  `mem_name` varchar(80) NOT NULL,
  `mem_address` text NOT NULL,
  `mem_phone` varchar(10) NOT NULL,
  `mem_status` int(1) UNSIGNED NOT NULL COMMENT '0=แอดมิน, 1=พนักงานคีย์ข้อมูล, 2=ครู, 3=เจ้าหน้าที่',
  `mem_username` varchar(20) NOT NULL,
  `mem_password` varchar(255) NOT NULL,
  `mem_amount_stock` int(10) UNSIGNED NOT NULL COMMENT 'จำนวนเงินหุ้น',
  `mem_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินออมหุ้น (บาท/เดือน)',
  `mem_common_credit` int(10) UNSIGNED NOT NULL,
  `mem_emergency_credit` int(10) UNSIGNED NOT NULL,
  `mem_register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`mem_id`, `mem_name`, `mem_address`, `mem_phone`, `mem_status`, `mem_username`, `mem_password`, `mem_amount_stock`, `mem_stock_savings`, `mem_common_credit`, `mem_emergency_credit`, `mem_register_date`) VALUES
('0000000000000', 'Administrator', '410 หมู่ที่ 1 ถนนบึงพระ-พิษณุโลก ต.บึงพระ อ.เมืองพิษณุโลก จ.พิษณุโลก 65000', '0000000000', 0, 'admin', '$2y$10$mUvdneOy2QPZT9NW/QclvO99afC21X7AcHqWWBI/FgfIp8FVMrFPG', 0, 0, 0, 0, '2023-04-29 10:06:02'),
('1111111111111', '1111', '1', '0111111111', 2, '111111', '$2y$10$z7iF7VxEaa4xESH8k9MoZegtcogxn7Z//nXciQ207ojttRecjK8yG', 0, 1000, 80000, 5000, '2026-02-17 14:22:46'),
('2222222222222', '22', '2', '0222222222', 1, '222222', '$2y$10$WGFvNEis/EOtrGGwihwg7eeZ0A4ChfdEYgh39/2ImFewG9VcoVf3i', 0, 0, 0, 0, '2025-02-04 12:47:36'),
('3333333333333', '33', '3', '0333333333', 3, '333333', '$2y$10$Bvta3FU01.vVSJ7jokQw4.kzDSHdQbfVw99KgVTkujRJunMuCNDC2', 0, 500, 40000, 5000, '2025-02-04 12:50:32'),
('4444444444444', '444', '44444', '0444444444', 2, '444444', '$2y$10$PM.cbWUgyh6UOi/63xgbH.0fW0tCO8uzuAgYd5ZhpRQ4HtANtJ9Uu', 0, 400, 80000, 5000, '2026-02-23 01:18:23'),
('5555555555555', '555', '55', '0555555555', 2, '555555', '$2y$10$PmFc.7MEYJ9KWGHywv1TLeWpau457yZD9nvt72TUZj/E/7pclkaOC', 0, 500, 80000, 5000, '2026-02-24 14:03:45'),
('6666666666666', '666', '66', '0666666666', 2, '666666', '$2y$10$AxPX5ZvqB4bDszYaAgmu/eykyZ6ELkaqH6KCcOD3fOgHx4h36gCmi', 0, 2000, 0, 5000, '2026-02-24 13:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `st_id` int(11) NOT NULL,
  `st_max_amount_common_teacher` int(10) UNSIGNED NOT NULL COMMENT 'วงเงินกู้สูงสุด (เงินกู้สามัญ ครู)',
  `st_max_amount_common_officer` int(10) UNSIGNED NOT NULL COMMENT 'วงเงินกู้สูงสุด (เงินกู้สามัญ เจ้าหน้าที่)',
  `st_max_amount_emergency` int(10) UNSIGNED NOT NULL COMMENT 'วงเงินกู้สูงสุด (เงินกู้ฉุกเฉิน)',
  `st_min_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินออมหุ้นขั้นต่ำ (บาท)',
  `st_max_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินออมหุ้นสูงสุด (บาท)',
  `st_amount_cost_teacher` int(10) UNSIGNED NOT NULL COMMENT 'จำนวนเงินต้น (ครู)',
  `st_amount_cost_officer` int(10) UNSIGNED NOT NULL COMMENT 'จำนวนเงินต้น (เจ้าหน้าที่)',
  `st_max_months_common` int(10) UNSIGNED NOT NULL COMMENT 'จำนวนเดือนที่ผ่อนชำระสูงสุด (เงินกู้สามัญ)',
  `st_max_months_emergency` int(10) UNSIGNED NOT NULL COMMENT 'จำนวนเดือนที่ผ่อนชำระสูงสุด (เงินกู้ฉุกเฉิน)',
  `st_interest` float UNSIGNED NOT NULL COMMENT 'อัตราดอกเบี้ยต่อปี',
  `st_stock_price` float UNSIGNED NOT NULL COMMENT 'ราคาหุ้น (บาท)',
  `st_dividend_rate` float UNSIGNED NOT NULL COMMENT 'เงินปันผล (%)',
  `st_average_return_rate` float UNSIGNED NOT NULL COMMENT 'เงินเฉลี่ยคืน (%)',
  `st_dateline` int(10) UNSIGNED NOT NULL COMMENT 'วันที่สิ้นสุดชำระเงินรายเดือน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`st_id`, `st_max_amount_common_teacher`, `st_max_amount_common_officer`, `st_max_amount_emergency`, `st_min_stock_savings`, `st_max_stock_savings`, `st_amount_cost_teacher`, `st_amount_cost_officer`, `st_max_months_common`, `st_max_months_emergency`, `st_interest`, `st_stock_price`, `st_dividend_rate`, `st_average_return_rate`, `st_dateline`) VALUES
(1, 80000, 40000, 5000, 400, 2000, 2000, 1000, 40, 5, 9, 200, 4.5, 10, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`bw_id`);

--
-- Indexes for table `borrow_alert`
--
ALTER TABLE `borrow_alert`
  ADD PRIMARY KEY (`ba_id`);

--
-- Indexes for table `borrow_log`
--
ALTER TABLE `borrow_log`
  ADD PRIMARY KEY (`bl_id`);

--
-- Indexes for table `borrow_request`
--
ALTER TABLE `borrow_request`
  ADD PRIMARY KEY (`br_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`mem_id`),
  ADD UNIQUE KEY `mem_username` (`mem_username`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`st_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowing`
--
ALTER TABLE `borrowing`
  MODIFY `bw_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `borrow_alert`
--
ALTER TABLE `borrow_alert`
  MODIFY `ba_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `borrow_log`
--
ALTER TABLE `borrow_log`
  MODIFY `bl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `borrow_request`
--
ALTER TABLE `borrow_request`
  MODIFY `br_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
