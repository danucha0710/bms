<?php
session_start();
ob_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3')) {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['br_id']) || !isset($_POST['role']) || !isset($_POST['action'])) {
    header("Location: guarantor.php");
    exit();
}

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);
$br_id = (int)$_POST['br_id'];
$role = (int)$_POST['role'];
$action = (int)$_POST['action']; // 1=อนุมัติ 2=ไม่อนุมัติ
if ($role !== 1 && $role !== 2 || $action !== 1 && $action !== 2) {
    header("Location: guarantor.php");
    exit();
}

$col = $role == 1 ? 'guarantor_1_id' : 'guarantor_2_id';
$approve_col = $role == 1 ? 'guarantor_1_approve' : 'guarantor_2_approve';
$date_col = $role == 1 ? 'guarantor_1_approve_date' : 'guarantor_2_approve_date';
$date = date('Y-m-d H:i:s');

$sql = "UPDATE borrow_request 
        SET $approve_col = $action, $date_col = '$date' 
        WHERE br_id = $br_id AND $col = '$mem_id'";
if (mysqli_query($condb, $sql) && mysqli_affected_rows($condb) > 0) {
    ob_end_clean();
    header("Location: guarantor.php?done=1");
    exit();
}

ob_end_clean();
header("Location: guarantor.php?err=1");
exit();
