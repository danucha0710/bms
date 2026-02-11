<?php
session_start();
include('config/condb.php'); // ตรวจสอบว่าไฟล์ condb.php อยู่ในโฟลเดอร์ config จริงหรือไม่

// 1. รับค่าจากฟอร์ม
$user_login = mysqli_real_escape_string($condb, $_POST['user_login']);
$mem_password_input = $_POST['mem_password']; // รับค่าดิบมาเพื่อใช้กับ password_verify

// 2. ค้นหา User (เช็คทั้ง Username และ เลขบัตรประชาชน)
$sql = "SELECT * FROM member 
        WHERE mem_username = '$user_login' OR mem_id = '$user_login'";
$result = mysqli_query($condb, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $hashed_password = $row['mem_password']; 

    // 3. ตรวจสอบรหัสผ่าน
    if (password_verify($mem_password_input, $hashed_password)) {
        
        // --- เพิ่มความปลอดภัย: ป้องกัน Session Fixation ---
        session_regenerate_id(true);
        
        // เก็บข้อมูลลง Session
        $_SESSION["mem_id"] = $row["mem_id"];
        $_SESSION["mem_name"] = $row["mem_name"];
        $_SESSION["mem_status"] = $row["mem_status"];

        // 4. แยกเส้นทางตามสถานะ
        if ($_SESSION["mem_status"] == 0 || $_SESSION["mem_status"] == 1) {
            Header("Location: admin/index.php");
            exit(); // ต้องมี exit ทุกครั้งหลัง Header Location
        } else {
            Header("Location: user/index.php");
            exit();
        }
    } else {
        // รหัสผ่านไม่ตรง
        echo "<script>alert('ชื่อผู้ใช้งาน หรือ รหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
        exit();
    }
} else {
    // ไม่พบ User
    echo "<script>alert('ชื่อผู้ใช้งาน หรือ รหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
    exit();
}
?>