<?php
// 1. เริ่มต้น Session เพื่อให้เข้าถึงข้อมูลปัจจุบันได้
session_start();

// 2. ล้างค่าตัวแปร Session ทั้งหมดในหน่วยความจำ
$_SESSION = array();

// 3. ลบ Cookie ของ Session ที่ฝั่ง Browser (ถ้ามี)
// ขั้นตอนนี้สำคัญมาก เพื่อความปลอดภัยสูงสุด
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. ทำลาย Session ที่ฝั่ง Server
session_destroy();

// 5. Redirect กลับไปหน้า Login หรือหน้าแรก
header("Location: index.php");

// 6. จบการทำงานทันที
exit();
?>