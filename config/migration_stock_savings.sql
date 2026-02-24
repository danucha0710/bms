-- Migration: เพิ่มเงินออมหุ้นขั้นต่ำ/สูงสุด (system) และเงินออมหุ้น (member)
-- รันครั้งเดียวกับฐานข้อมูลที่มีอยู่แล้ว (เลือกใช้ ALTER หรือข้ามถ้ามีคอลัมน์แล้ว)

-- ตาราง system: เพิ่มเงินออมหุ้นขั้นต่ำ และเงินออมหุ้นสูงสุด
ALTER TABLE `system`
  ADD COLUMN `st_min_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินออมหุ้นขั้นต่ำ (บาท)' AFTER `st_max_amount_emergency`,
  ADD COLUMN `st_max_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินออมหุ้นสูงสุด (บาท)' AFTER `st_min_stock_savings`;

-- ตาราง member: เพิ่มเงินออมหุ้น (บาท)
ALTER TABLE `member`
  ADD COLUMN `mem_stock_savings` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'เงินที่สมาชิกออมหุ้นทุกเดือน (บาท)' AFTER `mem_amount_stock`;
