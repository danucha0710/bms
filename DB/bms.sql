-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2025 at 04:03 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

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

--
-- Dumping data for table `borrow_log`
--

INSERT INTO `borrow_log` (`bl_id`, `mem_id`, `bl_text`, `bl_date`) VALUES
(1, '0000000000000', 'แก้ไขการตั้งค่าระบบ st_max_amount_common > 80000\n		st_max_amount_emergency > 5000\n		st_amount_cost_teacher > 2000\n		st_amount_cost_officer > 1000\n		st_max_months_common > 40\n		st_max_months_emergency > 5\n		st_interest > 9\n		st_stock_price > 200\n		st_dividend_rate > 4.5\n		st_average_return_rate > 10\n		st_dateline > 30', '2025-01-31 10:45:30');

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
  `guarantor_1` varchar(80) NOT NULL COMMENT 'ผู้ค้ำประกัน',
  `guarantor_2` varchar(80) NOT NULL COMMENT 'ผู้ค้ำประกัน',
  `br_details` text NOT NULL,
  `br_respond` text NOT NULL COMMENT 'ข้อความตอบกลับจากเจ้าหน้าที่',
  `br_status` tinyint(1) UNSIGNED NOT NULL,
  `br_approve_by` varchar(13) NOT NULL,
  `br_interest_rate` float UNSIGNED NOT NULL COMMENT 'อัตราดอกเบี้ย',
  `br_date_request` datetime NOT NULL,
  `br_date_approve` datetime NOT NULL
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
  `mem_password` varchar(50) NOT NULL,
  `secret_code` varchar(6) NOT NULL,
  `common_credit` int(10) UNSIGNED NOT NULL,
  `emergency_credit` int(10) UNSIGNED NOT NULL,
  `mem_register_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`mem_id`, `mem_name`, `mem_address`, `mem_phone`, `mem_status`, `mem_password`, `secret_code`, `common_credit`, `emergency_credit`, `mem_register_date`) VALUES
('0000000000000', 'Admin', '410 หมู่ที่ 1 ถนนบึงพระ-พิษณุโลก ต.บึงพระ อ.เมืองพิษณุโลก จ.พิษณุโลก 65000', '0000000000', 1, '8104ba1dc0409b259f487ed07db477c38f205a30', '', 0, 0, '2023-04-29 10:06:02'),
('1111111111111', '11', '1', '0111111111', 2, 'd3ae181da1361e80292d967ba4a6d80359a02aed', '', 80000, 10000, '2023-05-01 23:04:37'),
('2222222222222', '22', '2', '0222222222', 3, '14ed9824a9db7758ee0b0e111ef6848be154503b', '', 0, 0, '2025-01-28 15:22:07');

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `st_id` int(11) NOT NULL,
  `st_max_amount_common` int(10) UNSIGNED NOT NULL COMMENT 'วงเงินกู้สูงสุด (เงินกู้สามัญ)',
  `st_max_amount_emergency` int(10) UNSIGNED NOT NULL COMMENT 'วงเงินกู้สูงสุด (เงินกู้ฉุกเฉิน)',
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

INSERT INTO `system` (`st_id`, `st_max_amount_common`, `st_max_amount_emergency`, `st_amount_cost_teacher`, `st_amount_cost_officer`, `st_max_months_common`, `st_max_months_emergency`, `st_interest`, `st_stock_price`, `st_dividend_rate`, `st_average_return_rate`, `st_dateline`) VALUES
(1, 80000, 5000, 2000, 1000, 40, 5, 9, 200, 4.5, 10, 30);

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
  ADD PRIMARY KEY (`mem_id`);

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
  MODIFY `bw_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_alert`
--
ALTER TABLE `borrow_alert`
  MODIFY `ba_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrow_log`
--
ALTER TABLE `borrow_log`
  MODIFY `bl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `borrow_request`
--
ALTER TABLE `borrow_request`
  MODIFY `br_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
