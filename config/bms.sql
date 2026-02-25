-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 08:30 PM
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
  `br_date_approve` datetime NOT NULL,
  `br_is_reset` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `br_reset_br_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('3333333333333', '33', '3', '0333333333', 3, '333333', '$2y$10$Bvta3FU01.vVSJ7jokQw4.kzDSHdQbfVw99KgVTkujRJunMuCNDC2', 500, 500, 3878, 3091, '2025-02-04 12:50:32'),
('4444444444444', '444', '44444', '0444444444', 2, '444444', '$2y$10$PM.cbWUgyh6UOi/63xgbH.0fW0tCO8uzuAgYd5ZhpRQ4HtANtJ9Uu', 0, 400, 80000, 0, '2026-02-23 01:18:23'),
('5555555555555', '555', '55', '0555555555', 2, '555555', '$2y$10$PmFc.7MEYJ9KWGHywv1TLeWpau457yZD9nvt72TUZj/E/7pclkaOC', 500, 500, 37080, 5000, '2026-02-24 14:03:45'),
('6666666666666', '666', '66', '0666666666', 2, '666666', '$2y$10$AxPX5ZvqB4bDszYaAgmu/eykyZ6ELkaqH6KCcOD3fOgHx4h36gCmi', 2000, 2000, 80000, 3091, '2026-02-24 13:56:33');

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
  MODIFY `bw_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `borrow_alert`
--
ALTER TABLE `borrow_alert`
  MODIFY `ba_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `borrow_log`
--
ALTER TABLE `borrow_log`
  MODIFY `bl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `borrow_request`
--
ALTER TABLE `borrow_request`
  MODIFY `br_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
