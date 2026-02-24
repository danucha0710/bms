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

// ไม่ใช้ชื่อผู้ค้ำ (guarantor_1, guarantor_2) ในระบบอีกต่อไป ค่าใน DB จะเว้นว่างไว้
$guarantor_1 = '';
$guarantor_2 = '';
$guarantor_1_id = mysqli_real_escape_string($condb, $_POST['guarantor_1_id'] ?? '');
$guarantor_2_id = mysqli_real_escape_string($condb, $_POST['guarantor_2_id'] ?? '');

$has_guarantor_approve = false;
$cr = @mysqli_query($condb, "SHOW COLUMNS FROM borrow_request LIKE 'guarantor_1_approve'");
if ($cr && mysqli_fetch_assoc($cr)) $has_guarantor_approve = true;

if ($guarantee_type == 1) {
    // ใช้ mem_id ผู้ค้ำผ่าน guarantor_1_id / guarantor_2_id เท่านั้น (ตารางไม่มี guarantor_1/guarantor_2 แล้ว)
    $sql = "INSERT INTO borrow_request (mem_id, br_type, br_amount, br_months_pay, guarantee_type, guarantor_1_id, guarantor_2_id, guarantor_1_approve, guarantor_2_approve, br_details, br_interest_rate, br_date_request) 
            VALUES ('$mem_id', $br_type, $br_amount, $br_months_pay, $guarantee_type, " . ($guarantor_1_id ? "'$guarantor_1_id'" : "NULL") . ", " . ($guarantor_2_id ? "'$guarantor_2_id'" : "NULL") . ", 0, 0, '$br_details', $br_interest_rate, '$date')";
} else {
    $sql = "INSERT INTO borrow_request (mem_id, br_type, br_amount, br_months_pay, guarantee_type, br_details, br_interest_rate, br_date_request) 
            VALUES ('$mem_id', $br_type, $br_amount, $br_months_pay, $guarantee_type, '$br_details', $br_interest_rate, '$date')";
}

if (mysqli_query($condb, $sql)) {
    ob_end_clean();
    header("Location: index.php?save_ok=1");
    exit();
}

ob_end_clean();
header("Location: borrow.php?err=1");
exit();
