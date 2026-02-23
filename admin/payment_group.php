<?php
session_start();

if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');

$redirect = 'mustpay.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['group_pay']) || !isset($_POST['month_ym']) || !isset($_POST['pay_date'])) {
    header("Location: $redirect");
    exit();
}

$month_ym = mysqli_real_escape_string($condb, $_POST['month_ym']);
$pay_date = mysqli_real_escape_string($condb, $_POST['pay_date']);
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $pay_date)) {
    $pay_date = date('Y-m-d');
}

$mem_ids = isset($_POST['mem_ids']) && is_array($_POST['mem_ids']) ? $_POST['mem_ids'] : [];
if (empty($mem_ids)) {
    header("Location: $redirect?err=no_select");
    exit();
}

$safe_ids = [];
foreach ($mem_ids as $id) {
    $id = mysqli_real_escape_string($condb, $id);
    if ($id !== '') {
        $safe_ids[] = "'$id'";
    }
}
if (empty($safe_ids)) {
    header("Location: $redirect?err=no_select");
    exit();
}

$ids_list = implode(',', $safe_ids);
$month_ym_safe = mysqli_real_escape_string($condb, $month_ym);

$sql = "UPDATE borrowing 
        SET bw_status = 1, bw_date_pay = '$pay_date' 
        WHERE bw_status = 0 
        AND mem_id IN ($ids_list) 
        AND DATE_FORMAT(bw_date_pay,'%Y-%m') = '$month_ym_safe'";

if (mysqli_query($condb, $sql)) {
    $affected = mysqli_affected_rows($condb);
    header("Location: $redirect?save_ok=1&group=1&count=" . (int)$affected);
} else {
    header("Location: $redirect?err=1");
}
exit();
