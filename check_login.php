<?php
session_start();
include('config/condb.php'); // ตรวจสอบว่าไฟล์ condb.php อยู่ในโฟลเดอร์ config จริงหรือไม่

// ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
if (!isset($_POST['user_login']) || !isset($_POST['mem_password'])) {
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.location='login.php';</script>";
    exit();
}

// 1. รับค่าจากฟอร์ม
$user_login = mysqli_real_escape_string($condb, $_POST['user_login']);
$mem_password_input = $_POST['mem_password']; // รับค่าดิบมาเพื่อใช้กับ password_verify

// 2. ค้นหา User (เช็คทั้ง Username และ เลขบัตรประชาชน)
$sql = "SELECT * FROM member 
        WHERE mem_username = '$user_login' OR mem_id = '$user_login'";
$result = mysqli_query($condb, $sql);

if (!$result) {
    echo "<script>alert('เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . mysqli_error($condb) . "'); window.location='login.php';</script>";
    exit();
}

$num_rows = mysqli_num_rows($result);

if ($num_rows == 1) {
    $row = mysqli_fetch_array($result);
    $hashed_password = $row['mem_password']; 

    // 3. ตรวจสอบรหัสผ่าน
    // ถ้ารหัสผ่านในฐานข้อมูลเป็น hash (เริ่มต้นด้วย $2y$ หรือ $2a$ หรือ $2b$) ให้ใช้ password_verify
    // ถ้าไม่ใช่ (รหัสผ่านแบบเก่า) ให้เช็คตรงๆ
    $password_match = false;
    
    // ตรวจสอบว่ารหัสผ่านเป็น hash หรือไม่ (เริ่มต้นด้วย $2y$, $2a$, $2b$)
    if (preg_match('/^\$2[ayb]\$/', $hashed_password)) {
        // รหัสผ่านเป็น hash - ใช้ password_verify
        if (password_verify($mem_password_input, $hashed_password)) {
            $password_match = true;
            // ถ้า hash เก่าเกินไป ให้อัปเดตเป็น hash ใหม่
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
                $new_hash = password_hash($mem_password_input, PASSWORD_DEFAULT);
                $update_sql = "UPDATE member SET mem_password = '" . mysqli_real_escape_string($condb, $new_hash) . "' WHERE mem_id = '" . mysqli_real_escape_string($condb, $row['mem_id']) . "'";
                mysqli_query($condb, $update_sql);
            }
        }
    } else {
        // รหัสผ่านแบบเก่า (ไม่ hash) - เช็คตรงๆ แล้วอัปเดตเป็น hash ใหม่
        if ($hashed_password === $mem_password_input) {
            $password_match = true;
            $new_hash = password_hash($mem_password_input, PASSWORD_DEFAULT);
            $update_sql = "UPDATE member SET mem_password = '" . mysqli_real_escape_string($condb, $new_hash) . "' WHERE mem_id = '" . mysqli_real_escape_string($condb, $row['mem_id']) . "'";
            mysqli_query($condb, $update_sql);
        }
    }
    
    if ($password_match) {
        // --- เพิ่มความปลอดภัย: ป้องกัน Session Fixation ---
        session_regenerate_id(true);
        
        // เก็บข้อมูลลง Session
        $_SESSION["mem_id"] = $row["mem_id"];
        $_SESSION["mem_name"] = $row["mem_name"];
        $_SESSION["mem_status"] = $row["mem_status"];

        // 4. แยกเส้นทางตามสถานะ
        if ($_SESSION["mem_status"] == 0 || $_SESSION["mem_status"] == 1) {
            // Admin (0) หรือ พนักงานคีย์ข้อมูล (1) -> ไปหน้า admin
            Header("Location: admin/index.php");
            exit(); // ต้องมี exit ทุกครั้งหลัง Header Location
        } else {
            // ครู (2) หรือ เจ้าหน้าที่ (3) -> ไปหน้า user
            Header("Location: user/index.php");
            exit();
        }
    } else {
        // รหัสผ่านไม่ตรง
        echo "<script>alert('ชื่อผู้ใช้งาน หรือ รหัสผ่านไม่ถูกต้อง'); window.location='login.php';</script>";
        exit();
    }
} elseif ($num_rows == 0) {
    // ไม่พบ User
    echo "<script>alert('ไม่พบชื่อผู้ใช้งานนี้ในระบบ'); window.location='login.php';</script>";
    exit();
} else {
    // พบ User มากกว่า 1 คน (ข้อมูลซ้ำ)
    echo "<script>alert('พบข้อมูลซ้ำในระบบ กรุณาติดต่อผู้ดูแลระบบ'); window.location='login.php';</script>";
    exit();
}
?>