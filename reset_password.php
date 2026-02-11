<?php
// 1. เชื่อมต่อฐานข้อมูล
include('config/condb.php'); 

// 2. กำหนดข้อมูลที่ต้องการรีเซ็ต
$target_username = '333333';       // ใส่ Username ของอาจารย์ที่ต้องการรีเซ็ต
$new_password_plain = '333333';    // ใส่รหัสผ่านใหม่ที่ต้องการใช้

// 3. สร้างรหัสผ่านแบบ Hash (มาตรฐานใหม่)
$hashed_password = password_hash($new_password_plain, PASSWORD_DEFAULT);

// 4. อัปเดตลงฐานข้อมูล
$sql = "UPDATE member SET 
        mem_password = '$hashed_password' 
        WHERE mem_username = '$target_username' 
        OR mem_id = '$target_username'"; // เช็คทั้ง username และ id

$result = mysqli_query($condb, $sql);

echo "<h3>ระบบรีเซ็ตรหัสผ่าน (Emergency Reset)</h3>";
if ($result && mysqli_affected_rows($condb) > 0) {
    echo "<p style='color: green;'>✅ รีเซ็ตรหัสผ่านสำหรับ User: <b>$target_username</b> สำเร็จ!</p>";
    echo "<p>รหัสผ่านใหม่ของคุณคือ: <b>$new_password_plain</b></p>";
    echo "<br><a href='login.php'>ไปที่หน้าล็อกอิน</a>";
} else {
    echo "<p style='color: red;'>❌ ไม่พบชื่อผู้ใช้งานนี้ หรือข้อมูลซ้ำกับของเดิม</p>";
    echo "สาเหตุอาจเกิดจาก: <br>";
    echo "- ไม่พบ Username: '$target_username' ในระบบ<br>";
    echo "- รหัสผ่านที่ตั้งใหม่ตรงกับรหัสเดิมในฐานข้อมูลอยู่แล้ว";
}

mysqli_close($condb);
?>