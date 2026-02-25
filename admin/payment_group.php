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

// ดึงรายการงวดที่จะถูกมาร์กว่าชำระแล้ว (ยอดชำระ + ประเภทเงินกู้) ก่อนอัปเดต
$sel = "SELECT b.mem_id, b.bw_amount, r.br_type 
        FROM borrowing b 
        INNER JOIN borrow_request r ON b.br_id = r.br_id 
        WHERE b.bw_status = 0 
        AND b.mem_id IN ($ids_list) 
        AND DATE_FORMAT(b.bw_date_pay,'%Y-%m') = '$month_ym_safe'";
$res_sel = mysqli_query($condb, $sel);
$rows_to_credit = [];
$members_for_stock = [];
while ($r = mysqli_fetch_assoc($res_sel)) {
    $rows_to_credit[] = $r;
    $members_for_stock[$r['mem_id']] = true;
}

$sql = "UPDATE borrowing 
        SET bw_status = 1, bw_date_pay = '$pay_date' 
        WHERE bw_status = 0 
        AND mem_id IN ($ids_list) 
        AND DATE_FORMAT(bw_date_pay,'%Y-%m') = '$month_ym_safe'";

if (mysqli_query($condb, $sql)) {
    $affected = mysqli_affected_rows($condb);
    // นำยอดชำระไปเพิ่มวงเงินสมาชิก ตามประเภทเงินกู้ (สามัญ = mem_common_credit, ฉุกเฉิน = mem_emergency_credit)
    foreach ($rows_to_credit as $r) {
        $amount = (float)$r['bw_amount'];
        $mid = mysqli_real_escape_string($condb, $r['mem_id']);
        $br_type = (int)$r['br_type'];
        if ($amount <= 0 || $mid === '') continue;
        if ($br_type === 1) {
            $up = "UPDATE member SET mem_common_credit = mem_common_credit + $amount WHERE mem_id = '$mid'";
        } else {
            $up = "UPDATE member SET mem_emergency_credit = mem_emergency_credit + $amount WHERE mem_id = '$mid'";
        }
        @mysqli_query($condb, $up);
    }
    // เพิ่มเงินออมหุ้น/เดือน เข้าในยอดหุ้นสะสม ให้สมาชิกที่ถูกชำระงวดในเดือนนี้ (ครั้งละ 1 รอบต่อการเรียกใช้)
    if (!empty($members_for_stock)) {
        $ids_stock = [];
        foreach (array_keys($members_for_stock) as $mid_raw) {
            $mid_esc = mysqli_real_escape_string($condb, $mid_raw);
            if ($mid_esc !== '') {
                $ids_stock[] = "'$mid_esc'";
            }
        }
        if (!empty($ids_stock)) {
            $ids_list_stock = implode(',', $ids_stock);
            $up_stock = "UPDATE member SET mem_amount_stock = mem_amount_stock + mem_stock_savings WHERE mem_id IN ($ids_list_stock)";
            @mysqli_query($condb, $up_stock);
        }
    }
    header("Location: $redirect?save_ok=1&group=1&count=" . (int)$affected);
} else {
    header("Location: $redirect?err=1");
}
exit();
