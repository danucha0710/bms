<?php 
include('../config/condb.php'); // ตรวจสอบ path ไฟล์เชื่อมต่อให้ถูกต้อง

// รับค่า mode การทำงาน
$member = isset($_POST['member']) ? $_POST['member'] : (isset($_GET['member']) ? $_GET['member'] : '');

// =========================================================
// 1. เพิ่มสมาชิกใหม่ (ADD)
// =========================================================
if ($member == "add"){
    // ดึงค่าวงเงินเริ่มต้นจากระบบ (แยกครู/เจ้าหน้าที่) และช่วงเงินออมหุ้น/เดือน
    $query_sys = "SELECT st_max_amount_common_teacher, st_max_amount_common_officer, st_max_amount_emergency, st_min_stock_savings, st_max_stock_savings FROM `system` WHERE st_id = 1";
    $result_sys = mysqli_query($condb, $query_sys);
    $row_sys = mysqli_fetch_array($result_sys, MYSQLI_ASSOC);

    // รับค่าจากฟอร์ม
    $mem_status = mysqli_real_escape_string($condb, $_POST["mem_status"]);
    $mem_name   = mysqli_real_escape_string($condb, $_POST["mem_name"]);
    $mem_id     = mysqli_real_escape_string($condb, $_POST["mem_id"]); // เลขบัตร
    $mem_username = mysqli_real_escape_string($condb, $_POST["mem_username"]); // คอลัมน์ใหม่
    $mem_phone    = mysqli_real_escape_string($condb, $_POST["mem_phone"]);
    $mem_address  = mysqli_real_escape_string($condb, $_POST["mem_address"]);
    
    // การเข้ารหัสแบบใหม่ (Password Hashing)
    $password_raw = !empty($_POST["mem_password"]) ? $_POST["mem_password"] : $_POST["mem_phone"]; 
    $mem_password = password_hash($password_raw, PASSWORD_DEFAULT);

    // วงเงินกู้ตามสถานะ: Admin(0) และ พนักงานคีย์ข้อมูล(1) = 0; ครู(2)/เจ้าหน้าที่(3) = อิงจากตั้งค่าระบบ
    if ($mem_status == '0' || $mem_status == '1') {
        $mem_common_credit = 0;
        $mem_emergency_credit = 0;
    } elseif ($mem_status == '2') {
        $mem_common_credit = isset($row_sys['st_max_amount_common_teacher']) ? (int)$row_sys['st_max_amount_common_teacher'] : 0;
        $mem_emergency_credit = isset($row_sys['st_max_amount_emergency']) ? (int)$row_sys['st_max_amount_emergency'] : 0;
    } elseif ($mem_status == '3') {
        $mem_common_credit = isset($row_sys['st_max_amount_common_officer']) ? (int)$row_sys['st_max_amount_common_officer'] : 0;
        $mem_emergency_credit = isset($row_sys['st_max_amount_emergency']) ? (int)$row_sys['st_max_amount_emergency'] : 0;
    } else {
        $mem_common_credit = 0;
        $mem_emergency_credit = 0;
    }

    // เงินออมหุ้น/เดือน
    $min_stock = isset($row_sys['st_min_stock_savings']) ? (int)$row_sys['st_min_stock_savings'] : 0;
    $max_stock = isset($row_sys['st_max_stock_savings']) ? (int)$row_sys['st_max_stock_savings'] : 0;
    if ($mem_status == '0' || $mem_status == '1') {
        // ผู้ดูแลระบบ และ พนักงานคีย์ข้อมูล: เงินออมหุ้น/เดือน ต้องเป็น 0
        $mem_stock_savings = 0;
    } else {
        $mem_stock_savings = isset($_POST["mem_stock_savings"]) ? (int)$_POST["mem_stock_savings"] : 0;
        if ($mem_stock_savings < $min_stock) {
            $mem_stock_savings = $min_stock;
        }
        if ($max_stock > 0 && $mem_stock_savings > $max_stock) {
            $mem_stock_savings = $max_stock;
        }
        if ($mem_stock_savings < 0) {
            $mem_stock_savings = 0;
        }
    }

    $mem_register_date    = date("Y-m-d H:i:s");

    // ตรวจสอบข้อมูลซ้ำ (ID หรือ Username)
    $check = "SELECT mem_id FROM member WHERE mem_id = '$mem_id' OR mem_username = '$mem_username'";
    $result_check = mysqli_query($condb, $check);
    
    if(mysqli_num_rows($result_check) > 0){
        mysqli_close($condb);
        echo "<script>window.location = 'list_mem.php?error=duplicate';</script>";
        exit();
    } else {
        $sql = "INSERT INTO member (
            mem_id, mem_username, mem_name, mem_address, mem_phone, 
            mem_status, mem_password, mem_stock_savings, mem_common_credit, mem_emergency_credit, mem_register_date
        ) VALUES (
            '$mem_id', '$mem_username', '$mem_name', '$mem_address', '$mem_phone', 
            $mem_status, '$mem_password', $mem_stock_savings, $mem_common_credit, $mem_emergency_credit, '$mem_register_date'
        )";
        $result = mysqli_query($condb, $sql) or die (mysqli_error($condb));
    }

    if($result){
        Header("Location: list_mem.php?mem_add=1");
    } else {
        Header("Location: list_mem.php?error=1");
    }
}

// =========================================================
// 2. แก้ไขสมาชิก (EDIT - โดย Admin)
// =========================================================
elseif ($member == "edit"){
    $mem_id_old = mysqli_real_escape_string($condb, $_POST["mem_id_old"]);
    $mem_username = mysqli_real_escape_string($condb, $_POST["mem_username"]);
    $mem_status = mysqli_real_escape_string($condb, $_POST["mem_status"]);
    $mem_name   = mysqli_real_escape_string($condb, $_POST["mem_name"]);
    $mem_phone  = mysqli_real_escape_string($condb, $_POST["mem_phone"]);
    $mem_address = mysqli_real_escape_string($condb, $_POST["mem_address"]);

    // วงเงินกู้: Admin(0) และ พนักงานคีย์ข้อมูล(1) ต้องเป็น 0; ครู(2)/เจ้าหน้าที่(3) ใช้ค่าจากฟอร์มแต่ไม่เกินที่ตั้งในระบบ
    if ($mem_status == '0' || $mem_status == '1') {
        $common_credit = 0;
        $emergency_credit = 0;
        $mem_stock_savings = 0;
        $mem_amount_stock = 0;
    } else {
        $rs_sys = mysqli_query($condb, "SELECT st_max_amount_common_teacher, st_max_amount_common_officer, st_max_amount_emergency, st_min_stock_savings, st_max_stock_savings FROM `system` WHERE st_id = 1");
        $row_sys = $rs_sys ? mysqli_fetch_assoc($rs_sys) : null;
        $max_common = 0;
        $max_emergency = isset($row_sys['st_max_amount_emergency']) ? (int)$row_sys['st_max_amount_emergency'] : 0;
        if ($mem_status == '2') {
            $max_common = isset($row_sys['st_max_amount_common_teacher']) ? (int)$row_sys['st_max_amount_common_teacher'] : 0;
        } elseif ($mem_status == '3') {
            $max_common = isset($row_sys['st_max_amount_common_officer']) ? (int)$row_sys['st_max_amount_common_officer'] : 0;
        }
        $common_credit = (int) $_POST["common_credit"];
        $emergency_credit = (int) $_POST["emergency_credit"];
        if ($common_credit > $max_common) $common_credit = $max_common;
        if ($emergency_credit > $max_emergency) $emergency_credit = $max_emergency;
        if ($common_credit < 0) $common_credit = 0;
        if ($emergency_credit < 0) $emergency_credit = 0;

        // เงินออมหุ้น/เดือน: ครู/เจ้าหน้าที่ ใช้ค่าจากฟอร์มและจำกัดตามที่ตั้งในระบบ
        $min_stock = isset($row_sys['st_min_stock_savings']) ? (int)$row_sys['st_min_stock_savings'] : 0;
        $max_stock = isset($row_sys['st_max_stock_savings']) ? (int)$row_sys['st_max_stock_savings'] : 0;
        $mem_stock_savings = isset($_POST["mem_stock_savings"]) ? (int)$_POST["mem_stock_savings"] : 0;
        if ($mem_stock_savings < $min_stock) {
            $mem_stock_savings = $min_stock;
        }
        if ($max_stock > 0 && $mem_stock_savings > $max_stock) {
            $mem_stock_savings = $max_stock;
        }
        if ($mem_stock_savings < 0) {
            $mem_stock_savings = 0;
        }

        $mem_amount_stock = isset($_POST["mem_amount_stock"]) ? (int)$_POST["mem_amount_stock"] : 0;
        if ($mem_amount_stock < 0) $mem_amount_stock = 0;
    }

    // จัดการรหัสผ่านแบบใหม่
    $password_update = "";
    if(!empty($_POST["mem_password_new"])) {
        $mem_password_new = password_hash($_POST["mem_password_new"], PASSWORD_DEFAULT);
        $password_update = ", mem_password='$mem_password_new'";
    }

    $sql = "UPDATE member SET 
            mem_username='$mem_username',
            mem_status='$mem_status',
            mem_name='$mem_name', 
            mem_phone='$mem_phone',
            mem_address='$mem_address',
            mem_stock_savings='$mem_stock_savings',
            mem_amount_stock='$mem_amount_stock',
            mem_common_credit='$common_credit',
            mem_emergency_credit='$emergency_credit'
            $password_update
            WHERE mem_id='$mem_id_old'";

    $result = mysqli_query($condb, $sql) or die (mysqli_error($condb));

    if($result){
        Header("Location: list_mem.php?mem_edit=1");
    } else {
        Header("Location: list_mem.php?error=1");
    }
}

// =========================================================
// 3. แก้ไขโปรไฟล์ตัวเอง (EDIT PROFILE) - แบบบังคับ Login ใหม่เมื่อข้อมูลสำคัญเปลี่ยน
// =========================================================
elseif($member == "edit_profile"){ 
    $mem_id       = mysqli_real_escape_string($condb, $_POST["mem_id"]);
    $mem_username = mysqli_real_escape_string($condb, $_POST["mem_username"]);
    $mem_name     = mysqli_real_escape_string($condb, $_POST["mem_name"]);
    $mem_phone    = mysqli_real_escape_string($condb, $_POST["mem_phone"]);
    $mem_address  = mysqli_real_escape_string($condb, $_POST["mem_address"]);

    // ดึงข้อมูลเดิมมาเช็คก่อนว่ามีการเปลี่ยน Username หรือไม่
    $sql_old = "SELECT mem_username FROM member WHERE mem_id = '$mem_id'";
    $rs_old = mysqli_query($condb, $sql_old);
    $row_old = mysqli_fetch_assoc($rs_old);

    // เช็คว่ามีการกรอกรหัสผ่านใหม่มาไหม
    $is_password_changed = !empty($_POST["mem_password_new"]);
    // เช็คว่า Username เปลี่ยนจากเดิมไหม
    $is_username_changed = ($mem_username != $row_old['mem_username']);

    // --- ตรวจสอบ Username ซ้ำ ---
    $check_user = "SELECT mem_id FROM member WHERE mem_username = '$mem_username' AND mem_id != '$mem_id'";
    $result_user = mysqli_query($condb, $check_user);
    if(mysqli_num_rows($result_user) > 0){
        echo "<script>alert('Username นี้มีผู้อื่นใช้แล้ว'); window.history.back();</script>";
        exit();
    }

    $password_update = "";
    if($is_password_changed) {
        $mem_password_new = password_hash($_POST["mem_password_new"], PASSWORD_DEFAULT);
        $password_update = ", mem_password='$mem_password_new'";
    }

    $sql = "UPDATE member SET 
            mem_username='$mem_username',
            mem_name='$mem_name',   
            mem_phone='$mem_phone',
            mem_address='$mem_address'
            $password_update
            WHERE mem_id='$mem_id'";

    $result = mysqli_query($condb, $sql);

    if($result){
        // --- กรณีที่ต้องให้ Login ใหม่ ---
        if($is_password_changed || $is_username_changed) {
            session_destroy(); // ทำลาย Session ทั้งหมด
            echo "<script>";
            echo "alert('ข้อมูลสำคัญมีการเปลี่ยนแปลง กรุณาเข้าสู่ระบบใหม่อีกครั้ง');";
            echo "window.location = '../login.php';"; // ดีดกลับหน้า Login
            echo "</script>";
            exit();
        } else {
            // กรณีเปลี่ยนแค่ข้อมูลทั่วไป
            $_SESSION['mem_name'] = $mem_name;
            Header("Location: index.php?mem_editp=1");
            exit();
        }
    }
}

// =========================================================
// 4. ลบสมาชิก (DELETE)
// =========================================================
elseif($member == "del"){
    $mem_id = mysqli_real_escape_string($condb, $_GET["mem_id"]);
    
    $sql = "DELETE FROM member WHERE mem_id='$mem_id'";
    $result = mysqli_query($condb, $sql) or die (mysqli_error($condb));   
    
    if($result){
        Header("Location: list_mem.php?mem_del=1");
    } else {
        Header("Location: list_mem.php?error=1");
    }
}

mysqli_close($condb);
?>