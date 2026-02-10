<?php 
session_start();

// 1. ตรวจสอบว่ามี Session mem_id (ล็อกอินแล้วหรือยัง)
if(isset($_SESSION["mem_id"])){

    // ดึงค่าสถานะมาเก็บในตัวแปรเพื่อให้เขียนโค้ดง่ายขึ้น
    // ตรวจสอบให้แน่ใจว่าใน chk_login.php คุณใช้ชื่อตัวแปร "mem_status" นะครับ
    $mem_status = $_SESSION["mem_status"];

    // 2. แยกทางตามสถานะ (Logic ตาม chk_login.php)
    
    // สถานะ 0 = ผู้ดูแลระบบ (Admin)
    if($mem_status == "0"){
        Header("Location: admin/");
        exit();
    }
    
    // สถานะ 2 (ครู), 3 (เจ้าหน้าที่), หรือ 1 (User ทั่วไป)
    // ส่งไปที่โฟลเดอร์ user/ (หรือ member/ ตามที่คุณตั้ง)
    elseif($mem_status == "1" || $mem_status == "2" || $mem_status == "3"){ 
        Header("Location: user/"); 
        exit();
    }
    
    // กรณีสถานะแปลกปลอม (เช่น โดนแฮกค่า Session หรือ Database ผิดพลาด)
    else{
        // ล้างค่าทิ้งแล้วส่งกลับหน้า Login
        session_destroy();
        Header("Location: login.php");
        exit();
    }

}else{
    // 3. ถ้ายังไม่ล็อกอิน ให้ไปหน้า Login
    Header("Location: login.php");
    exit();
}
?>