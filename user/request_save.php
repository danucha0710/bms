<?php
session_start();
ob_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3')) {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: borrow.php");
    exit();
}

$br_type = (int)($_POST['br_type'] ?? 0);
$guarantee_type = (int)($_POST['guarantee_type'] ?? 0);
$br_details = mysqli_real_escape_string($condb, $_POST['br_details'] ?? '');
$br_interest_rate = isset($_POST['br_interest_rate']) ? (float)$_POST['br_interest_rate'] : 0;
$date = date('Y-m-d H:i:s');

if ($br_type == 1) {
    $br_amount = (float)($_POST['br_amount_common'] ?? 0);
    $br_months_pay = (int)($_POST['br_months_pay_common'] ?? 0);
} else {
    $br_amount = (float)($_POST['br_amount_emergency'] ?? 0);
    $br_months_pay = (int)($_POST['br_months_pay_emergency'] ?? 0);
}

if ($br_type < 1 || $br_type > 2 || $br_amount <= 0 || $br_months_pay <= 0) {
    header("Location: borrow.php?err=1");
    exit();
}

// ตรวจสอบและจัดการสัญญาที่ค้างชำระ
$br_is_reset  = (int)($_POST['br_is_reset']   ?? 0);
$br_reset_br_id = (int)($_POST['br_reset_br_id'] ?? 0);

// ตรวจสอบว่าคอลัมน์ reset มีอยู่ในตารางหรือไม่
$has_reset_col = false;
$cr_rst = @mysqli_query($condb, "SHOW COLUMNS FROM borrow_request LIKE 'br_is_reset'");
if ($cr_rst && mysqli_fetch_assoc($cr_rst)) $has_reset_col = true;

if ($br_is_reset == 0) {
    // ถ้าไม่ใช่การรีเซ็ท ตรวจสอบว่ามีสัญญาค้างชำระประเภทเดียวกันอยู่หรือไม่
    $sql_chk = "SELECT r.br_id
                FROM borrow_request r
                INNER JOIN borrowing b ON r.br_id = b.br_id
                WHERE r.mem_id = '$mem_id' AND r.br_type = $br_type
                  AND r.br_status = 1 AND b.bw_status = 0
                LIMIT 1";
    $rs_chk = mysqli_query($condb, $sql_chk);
    if ($rs_chk && mysqli_fetch_assoc($rs_chk)) {
        header("Location: borrow.php?err=active_loan");
        exit();
    }
} else {
    // ถ้าเป็นการรีเซ็ท ตรวจสอบว่า br_reset_br_id เป็นของสมาชิกนี้จริงและยังมีงวดค้างชำระ
    if ($br_reset_br_id <= 0) {
        header("Location: borrow.php?err=invalid_reset");
        exit();
    }
    $sql_vfy = "SELECT r.br_id
                FROM borrow_request r
                INNER JOIN borrowing b ON r.br_id = b.br_id
                WHERE r.br_id = $br_reset_br_id AND r.mem_id = '$mem_id'
                  AND r.br_status = 1 AND b.bw_status = 0
                LIMIT 1";
    $rs_vfy = mysqli_query($condb, $sql_vfy);
    if (!$rs_vfy || !mysqli_fetch_assoc($rs_vfy)) {
        header("Location: borrow.php?err=invalid_reset");
        exit();
    }
}

// สร้าง SQL fragment สำหรับฟิลด์ reset (ถ้าคอลัมน์มีอยู่)
$reset_cols = '';
$reset_vals = '';
if ($has_reset_col && $br_is_reset == 1 && $br_reset_br_id > 0) {
    $reset_cols = ', br_is_reset, br_reset_br_id';
    $reset_vals = ", 1, $br_reset_br_id";
}

// ไม่ใช้ชื่อผู้ค้ำ (guarantor_1, guarantor_2) ในระบบอีกต่อไป ค่าใน DB จะเว้นว่างไว้
$guarantor_1 = '';
$guarantor_2 = '';
$guarantor_1_id = mysqli_real_escape_string($condb, $_POST['guarantor_1_id'] ?? '');
$guarantor_2_id = mysqli_real_escape_string($condb, $_POST['guarantor_2_id'] ?? '');

$has_guarantor_approve = false;
$cr = @mysqli_query($condb, "SHOW COLUMNS FROM borrow_request LIKE 'guarantor_1_approve'");
if ($cr && mysqli_fetch_assoc($cr)) $has_guarantor_approve = true;

if ($guarantee_type == 1) {
    $sql = "INSERT INTO borrow_request (mem_id, br_type, br_amount, br_months_pay, guarantee_type, guarantor_1_id, guarantor_2_id, guarantor_1_approve, guarantor_2_approve, br_details, br_interest_rate, br_date_request$reset_cols) 
            VALUES ('$mem_id', $br_type, $br_amount, $br_months_pay, $guarantee_type, " . ($guarantor_1_id ? "'$guarantor_1_id'" : "NULL") . ", " . ($guarantor_2_id ? "'$guarantor_2_id'" : "NULL") . ", 0, 0, '$br_details', $br_interest_rate, '$date'$reset_vals)";
} else {
    $sql = "INSERT INTO borrow_request (mem_id, br_type, br_amount, br_months_pay, guarantee_type, br_details, br_interest_rate, br_date_request$reset_cols) 
            VALUES ('$mem_id', $br_type, $br_amount, $br_months_pay, $guarantee_type, '$br_details', $br_interest_rate, '$date'$reset_vals)";
}

if (mysqli_query($condb, $sql)) {
    ob_end_clean();
    header("Location: index.php?save_ok=1");
    exit();
}

ob_end_clean();
header("Location: borrow.php?err=1");
exit();
