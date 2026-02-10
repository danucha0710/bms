<?php 
session_start();

// 1. ตรวจสอบว่ามีการส่งข้อมูลมาครบถ้วนหรือไม่
if(isset($_POST['mem_id']) && isset($_POST['mem_password'])){
    
    // เชื่อมต่อฐานข้อมูล (ตามโครงสร้างใหม่)
    include("config/condb.php"); 

    // รับค่าและป้องกันการ Login ด้วยช่องว่าง
    $mem_id = mysqli_real_escape_string($condb, $_POST['mem_id']);
    $mem_password = mysqli_real_escape_string($condb, $_POST['mem_password']);

    // ถ้าไม่ได้กรอกข้อมูล
    if(empty($mem_id) || empty($mem_password)){
        echo "<script>alert('กรุณากรอกชื่อผู้ใช้งาน และรหัสผ่าน'); window.history.back();</script>";
        exit;
    }

    // 2. เข้ารหัสรหัสผ่าน (ใช้ SHA1 ตามเดิมของคุณ)
    // *ข้อแนะนำ: อนาคตควรเปลี่ยนไปใช้ password_hash() และ password_verify() จะปลอดภัยกว่า
    $password_hashed = sha1($mem_password);

    // 3. ใช้ PREPARED STATEMENT (สำคัญมาก! ป้องกัน SQL Injection)
    $sql = "SELECT mem_id, mem_name, mem_status 
            FROM member 
            WHERE mem_id = ? AND mem_password = ?";
    
    $stmt = mysqli_prepare($condb, $sql);
    
    // Bind Parameters: "ss" หมายถึง string 2 ตัว (user, pass)
    mysqli_stmt_bind_param($stmt, "ss", $mem_id, $password_hashed);
    
    // Execute
    mysqli_stmt_execute($stmt);
    
    // รับค่าผลลัพธ์
    $result = mysqli_stmt_get_result($stmt);

    // 4. ตรวจสอบผลลัพธ์
    if(mysqli_num_rows($result) == 1){
        
        $row = mysqli_fetch_array($result);

        // สร้าง Session
        $_SESSION["mem_id"] = $row["mem_id"];
        $_SESSION["mem_name"] = $row["mem_name"];
        $_SESSION["mem_status"] = $row["mem_status"];

        // 5. แยกเส้นทางตามสถานะ (Redirect)
        // 0 = Admin
        if($_SESSION["mem_status"] == "0"){ 
            header("Location: admin/");
            exit;
        }
        // 1, 2, 3, 4 = สมาชิกทั่วไป (เปลี่ยนไปที่โฟลเดอร์ member ตามโครงสร้างใหม่)
        // (รวมสถานะ 2=ครู, 3=จนท. ไว้ในนี้ด้วย เพื่อไม่ให้ตกหล่น)
        elseif(in_array($_SESSION["mem_status"], ["1", "2", "3", "4"])){ 
            header("Location: member/"); // ถ้าคุณใช้ชื่อโฟลเดอร์ user/ ให้เปลี่ยนตรงนี้กลับเป็น user/
            exit;
        }
        else {
            // กรณีสถานะไม่ถูกต้อง
            session_destroy(); // ล้าง session ทิ้ง
            echo "<script>alert('ไม่มีสิทธิ์เข้าใช้งาน หรือสถานะไม่ถูกต้อง'); window.history.back();</script>";
        }

    } else {
        // กรณี Login ไม่ผ่าน
        echo "<script>";
        echo "alert(\"ชื่อผู้ใช้งาน หรือรหัสผ่านไม่ถูกต้อง\");"; 
        echo "window.history.back();";
        echo "</script>";
    }

    // ปิดการเชื่อมต่อ
    mysqli_stmt_close($stmt);
    mysqli_close($condb);

} else {
    // กรณีเข้าหน้านี้โดยตรงไม่ได้ผ่าน Form
    header("Location: index.php");
    exit;
}
?>