<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '0' && $_SESSION['mem_status'] != '1')) {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');

// โหมดใหม่: ใบเสร็จรายเดือน (รายบุคคล) จาก mem_id + ym
$mem_id = isset($_GET['mem_id']) ? mysqli_real_escape_string($condb, $_GET['mem_id']) : '';
$ym = isset($_GET['ym']) ? mysqli_real_escape_string($condb, $_GET['ym']) : '';

if ($mem_id !== '' && $ym !== '' && preg_match('/^\d{4}-\d{2}$/', $ym)) {
    // ดึงข้อมูลสมาชิก + งวดที่ชำระในเดือนนั้น
    $sql_head = "SELECT mem_id, mem_name, mem_address, mem_phone, mem_stock_savings 
                 FROM member WHERE mem_id = '$mem_id'";
    $res_head = mysqli_query($condb, $sql_head);
    $member = mysqli_fetch_assoc($res_head);
    if (!$member) {
        echo '<p>ไม่พบข้อมูลสมาชิก</p>';
        exit();
    }

    $ym_safe = mysqli_real_escape_string($condb, $ym);
    $sql_items = "SELECT b.bw_amount, b.bw_round, b.bw_date_pay, 
                         r.br_id, r.br_type, r.br_months_pay, r.br_interest_rate
                  FROM borrowing b
                  INNER JOIN borrow_request r ON b.br_id = r.br_id
                  WHERE b.bw_status = 1 
                    AND b.mem_id = '$mem_id'
                    AND DATE_FORMAT(b.bw_date_pay,'%Y-%m') = '$ym_safe'
                  ORDER BY b.bw_date_pay ASC, b.bw_id ASC";
    $res_items = mysqli_query($condb, $sql_items);
    $items = [];
    $sum_common = 0;
    $sum_emergency = 0;
    $first_pay_date = null;
    while ($r = mysqli_fetch_assoc($res_items)) {
        $items[] = $r;
        if ((int)$r['br_type'] === 1) {
            $sum_common += (float)$r['bw_amount'];
        } else {
            $sum_emergency += (float)$r['bw_amount'];
        }
        if ($first_pay_date === null) {
            $first_pay_date = $r['bw_date_pay'];
        }
    }
    if (empty($items)) {
        echo '<p>ไม่พบงวดที่ชำระในเดือนนี้</p>';
        exit();
    }

    $stock_saving = isset($member['mem_stock_savings']) ? (float)$member['mem_stock_savings'] : 0;
    $total_all = $sum_common + $sum_emergency + $stock_saving;
    $receipt_no = 'M' . date('Ym', strtotime($ym . '-01')) . '-' . substr($mem_id, -4);
    $pay_date_th = $first_pay_date ? date('d/m/Y', strtotime($first_pay_date)) : date('d/m/Y');
    $month_label = date('m/Y', strtotime($ym . '-01'));
    // ใช้งวดแรกของเดือนมาระบุในวงเล็บ เช่น 02/2026 (2/40)
    $first_round = isset($items[0]['bw_round']) ? $items[0]['bw_round'] : '';
    $month_label_display = $month_label . ($first_round !== '' ? ' (' . $first_round . ')' : '');
} else {
    // โหมดเดิม: ใบเสร็จงวดเดียว (สำรองไว้เผื่อมีการเรียกใช้แบบเก่า)
    $bw_id = isset($_GET['bw_id']) ? (int)$_GET['bw_id'] : 0;
    if ($bw_id <= 0) {
        echo '<p>ไม่พบรายการ</p>';
        exit();
    }

    $sql = "SELECT b.bw_id, b.mem_id, b.br_id, b.bw_amount, b.bw_round, b.bw_date_pay,
                   m.mem_name, m.mem_address, m.mem_phone,
                   r.br_type, r.br_amount AS loan_amount, r.br_months_pay, r.br_interest_rate
            FROM borrowing b
            INNER JOIN member m ON b.mem_id = m.mem_id
            INNER JOIN borrow_request r ON b.br_id = r.br_id
            WHERE b.bw_id = $bw_id AND b.bw_status = 1";
    $result = mysqli_query($condb, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo '<p>ไม่พบข้อมูลใบเสร็จ</p>';
        exit();
    }

    $typeLabel_single = ((int)$row['br_type'] === 1) ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
    $receipt_no = 'R' . str_pad($row['bw_id'], 6, '0', STR_PAD_LEFT);
    $pay_date_th = date('d/m/Y', strtotime($row['bw_date_pay']));
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ใบเสร็จรับเงิน - <?php echo htmlspecialchars($receipt_no); ?></title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap-5/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .receipt-box { box-shadow: none; border: 1px solid #333; }
        }
        .receipt-box { max-width: 600px; margin: 0 auto; border: 2px solid #333; padding: 24px; }
        .receipt-title { font-size: 1.5rem; font-weight: bold; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .receipt-row { display: flex; margin-bottom: 8px; }
        .receipt-label { width: 180px; flex-shrink: 0; }
        .receipt-value { flex-grow: 1; }
        .amount-big { font-size: 1.4rem; font-weight: bold; color: #c00; }
        .sign-line { border-bottom: 1px solid #333; width: 200px; display: inline-block; margin-top: 40px; text-align: center; }
    </style>
</head>
<body class="bg-light py-4">
    <div class="container no-print mb-3">
        <a href="receipt.php" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> กลับ</a>
        <button type="button" class="btn btn-success" onclick="window.print();"><i class="fas fa-print"></i> พิมพ์ใบเสร็จ</button>
    </div>

    <div class="receipt-box bg-white">
        <div class="receipt-title">ใบเสร็จรับเงิน</div>

        <div class="receipt-row">
            <span class="receipt-label">เลขที่ใบเสร็จ</span>
            <span class="receipt-value"><?php echo htmlspecialchars($receipt_no); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">วันที่ออกใบเสร็จ</span>
            <span class="receipt-value"><?php echo $pay_date_th; ?></span>
        </div>

        <?php if (isset($items)): // โหมดรายเดือน รายบุคคล ?>
        <div class="receipt-row">
            <span class="receipt-label">ประจำเดือน</span>
            <span class="receipt-value"><?php echo htmlspecialchars($month_label_display); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">ได้รับเงินจาก</span>
            <span class="receipt-value"><?php echo htmlspecialchars($member['mem_name']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">ที่อยู่</span>
            <span class="receipt-value"><?php echo htmlspecialchars($member['mem_address']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">เบอร์โทร</span>
            <span class="receipt-value"><?php echo htmlspecialchars($member['mem_phone']); ?></span>
        </div>

        <hr class="my-3">
        <h6 class="mb-2">รายละเอียดรายการที่ชำระประจำเดือน</h6>
        <table class="table table-sm table-bordered mb-3">
            <thead>
                <tr>
                    <th>รายการ</th>
                    <th class="text-end">จำนวนเงิน (บาท)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ชำระเงินกู้สามัญรวมทั้งเดือน</td>
                    <td class="text-end"><?php echo number_format($sum_common); ?></td>
                </tr>
                <tr>
                    <td>ชำระเงินกู้ฉุกเฉินรวมทั้งเดือน</td>
                    <td class="text-end"><?php echo number_format($sum_emergency); ?></td>
                </tr>
                <tr>
                    <td>เงินออมหุ้นประจำเดือน</td>
                    <td class="text-end"><?php echo number_format($stock_saving); ?></td>
                </tr>
                <tr>
                    <th class="text-end">รวมทั้งสิ้น</th>
                    <th class="text-end amount-big"><?php echo number_format($total_all); ?></th>
                </tr>
            </tbody>
        </table>

        <?php else: // โหมดเดิม ใบเสร็จงวดเดียว ?>
        <div class="receipt-row">
            <span class="receipt-label">ได้รับเงินจาก</span>
            <span class="receipt-value"><?php echo htmlspecialchars($row['mem_name']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">ที่อยู่</span>
            <span class="receipt-value"><?php echo htmlspecialchars($row['mem_address']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">เบอร์โทร</span>
            <span class="receipt-value"><?php echo htmlspecialchars($row['mem_phone']); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">คำขอกู้เลขที่</span>
            <span class="receipt-value"><?php echo (int)$row['br_id']; ?> (<?php echo $typeLabel_single; ?>)</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">ชำระงวดที่</span>
            <span class="receipt-value"><?php echo htmlspecialchars($row['bw_round']); ?> จาก <?php echo (int)$row['br_months_pay']; ?> งวด</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">จำนวนเงิน (บาท)</span>
            <span class="receipt-value amount-big"><?php echo number_format($row['bw_amount']); ?></span>
        </div>
        <?php endif; ?>

        <div class="mt-5 pt-4 text-end">
            <span class="sign-line">ลงชื่อผู้รับเงิน</span>
        </div>
    </div>

    <script>
        // อัตโนมัติเปิด dialog พิมพ์เมื่อโหลด (ถ้าเปิดใน tab ใหม่สำหรับพิมพ์)
        if (window.location.search.indexOf('print=1') !== -1) {
            window.onload = function() { window.print(); };
        }
    </script>
</body>
</html>
