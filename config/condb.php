<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "bms";

// 1. สร้างการเชื่อมต่อ
$condb = mysqli_connect($servername, $username, $password, $dbname);

// 2. ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if (mysqli_connect_errno()) {
    echo "ไม่สามารถเชื่อมต่อฐานข้อมูล MySQL ได้: " . mysqli_connect_error();
    exit();
}

// 3. ตั้งค่าภาษาให้รองรับภาษาไทยและ Emoji (utf8mb4)
mysqli_set_charset($condb, "utf8mb4");

// 4. ตั้งค่าโซนเวลาประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// ปิดการแจ้งเตือน Error เล็กน้อยๆ (Notice) เพื่อไม่ให้รบกวนหน้าเว็บ
error_reporting(error_reporting() & ~E_NOTICE);
?>