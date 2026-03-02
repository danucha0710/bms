<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

// 1. เชื่อมต่อฐานข้อมูล
include('../config/condb.php');

// ตรวจสอบว่ามีการส่งค่ามาจริงหรือไม่
if (isset($_POST['system']) && $_POST['system'] == "setting") {

    // 2. รับค่าจากฟอร์ม และ Escape String ป้องกัน SQL Injection
    $st_max_amount_common_teacher = mysqli_real_escape_string($condb, $_POST["st_max_amount_common_teacher"]);
    $st_max_amount_common_officer = mysqli_real_escape_string($condb, $_POST["st_max_amount_common_officer"]);
    $st_max_amount_emergency      = mysqli_real_escape_string($condb, $_POST["st_max_amount_emergency"]);
    $st_min_stock_savings         = (int) ($_POST["st_min_stock_savings"] ?? 0);
    $st_max_stock_savings         = (int) ($_POST["st_max_stock_savings"] ?? 0);
    $st_amount_cost_teacher  = mysqli_real_escape_string($condb, $_POST["st_amount_cost_teacher"]);
    $st_amount_cost_officer  = mysqli_real_escape_string($condb, $_POST["st_amount_cost_officer"]);
    $st_max_months_common    = mysqli_real_escape_string($condb, $_POST["st_max_months_common"]);
    $st_max_months_emergency = mysqli_real_escape_string($condb, $_POST["st_max_months_emergency"]);
    $st_interest             = mysqli_real_escape_string($condb, $_POST["st_interest"]);
    $st_stock_price          = mysqli_real_escape_string($condb, $_POST["st_stock_price"]);
    $st_dividend_rate        = mysqli_real_escape_string($condb, $_POST["st_dividend_rate"]);
    $st_average_return_rate  = mysqli_real_escape_string($condb, $_POST["st_average_return_rate"]);
    $st_dateline             = mysqli_real_escape_string($condb, $_POST["st_dateline"]);
    $st_guarantor_active     = isset($_POST["st_guarantor_active"]) ? 1 : 0;
    $st_org_name             = mysqli_real_escape_string($condb, $_POST["st_org_name"] ?? '');
    $st_president_name       = mysqli_real_escape_string($condb, $_POST["st_president_name"] ?? '');
    $st_finance_name         = mysqli_real_escape_string($condb, $_POST["st_finance_name"] ?? '');
    
    // รับค่าคนแก้ไข (เอาไว้ลง Log)
    $st_edit_by = mysqli_real_escape_string($condb, $_POST["st_edit_by"]);

    // การอัพโหลดรูปภาพโลโก้
    $st_logo_query = "";
    if (isset($_FILES['st_logo']) && $_FILES['st_logo']['error'] == 0) {
        $ext = pathinfo(basename($_FILES['st_logo']['name']), PATHINFO_EXTENSION);
        $new_image_name = 'logo_' . uniqid() . "." . $ext;
        $image_path = "../assets/images/";
        if (!file_exists($image_path)) {
            mkdir($image_path, 0777, true);
        }
        $upload_path = $image_path . $new_image_name;
        if (move_uploaded_file($_FILES['st_logo']['tmp_name'], $upload_path)) {
            $st_logo_query = ", st_logo = '$new_image_name'";
        }
    }

    // 3. คำสั่ง SQL Update
    $sql = "UPDATE `system` SET
            st_max_amount_common_teacher = '$st_max_amount_common_teacher',
            st_max_amount_common_officer = '$st_max_amount_common_officer',
            st_max_amount_emergency      = '$st_max_amount_emergency',
            st_min_stock_savings         = " . $st_min_stock_savings . ",
            st_max_stock_savings         = " . $st_max_stock_savings . ",
            st_amount_cost_teacher  = '$st_amount_cost_teacher',
            st_amount_cost_officer  = '$st_amount_cost_officer',
            st_max_months_common    = '$st_max_months_common',
            st_max_months_emergency = '$st_max_months_emergency',
            st_interest             = '$st_interest',
            st_stock_price          = '$st_stock_price',
            st_dividend_rate        = '$st_dividend_rate',
            st_average_return_rate  = '$st_average_return_rate',
            st_dateline             = '$st_dateline',
            st_guarantor_active     = '$st_guarantor_active',
            st_org_name             = '$st_org_name',
            st_president_name       = '$st_president_name',
            st_finance_name         = '$st_finance_name'
            $st_logo_query
            WHERE st_id = 1";

    $result = mysqli_query($condb, $sql) or die ("Error Update: " . mysqli_error($condb));

    // 4. บันทึก Log การใช้งาน (Borrow Log)
    if($result){
        $st_date = date("Y-m-d H:i:s");
        // จัดรูปแบบข้อความ Log ให้อ่านง่าย
        $log_text = "ปรับปรุงการตั้งค่าระบบ: 
        [กู้สามัญครู: $st_max_amount_common_teacher, กู้สามัญเจ้าหน้าที่: $st_max_amount_common_officer, กู้ฉุกเฉิน: $st_max_amount_emergency]
        [เงินออมหุ้นขั้นต่ำ: $st_min_stock_savings, เงินออมหุ้นสูงสุด: $st_max_stock_savings]
        [ดอกเบี้ย: $st_interest%, หุ้น: $st_stock_price, ปันผล: $st_dividend_rate%, เฉลี่ยคืน: $st_average_return_rate%]
        [ตัดรอบวันที่: $st_dateline]";
        
        // Escape ข้อความ Log อีกครั้งเพื่อความชัวร์
        $log_text = mysqli_real_escape_string($condb, $log_text);

        $sql_log = "INSERT INTO `borrow_log` (mem_id, bl_text, bl_date) 
                    VALUES ('$st_edit_by', '$log_text', '$st_date')";
        mysqli_query($condb, $sql_log) or die ("Error Log: " . mysqli_error($condb));
        
        // ปิดการเชื่อมต่อ
        mysqli_close($condb);

        // 5. ส่งกลับไปหน้าเดิม พร้อมสถานะ success (save_ok=1)
        Header("Location: system.php?save_ok=1");
        exit();

    } else {
        // กรณี Error
        mysqli_close($condb);
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล'); window.history.back();</script>";
    }

} else {
    // กรณีเข้าไฟล์นี้มาโดยตรงไม่ได้กด submit
    Header("Location: system.php");
    exit();
}
?>