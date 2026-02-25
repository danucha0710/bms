<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '0' && $_SESSION['mem_status'] != '1')) {
    header("Location: ../login.php");
    exit();
}

$menu = "receipt";
include('../config/condb.php');
include('../includes/header.php');

// ค้นหารายเดือน + รายบุคคล
$month_ym = isset($_GET['month_ym']) ? mysqli_real_escape_string($condb, $_GET['month_ym']) : '';
$mem_id_filter = isset($_GET['mem_id']) ? mysqli_real_escape_string($condb, $_GET['mem_id']) : '';

// ถ้าไม่เลือกเดือน ให้ใช้เดือนล่าสุดที่มีการชำระเงิน
if ($month_ym === '') {
    $q_last = "SELECT MAX(bw_date_pay) AS last_paid FROM borrowing WHERE bw_status = 1";
    $r_last = mysqli_query($condb, $q_last);
    $row_last = $r_last ? mysqli_fetch_assoc($r_last) : null;
    if ($row_last && $row_last['last_paid']) {
        $month_ym = date('Y-m', strtotime($row_last['last_paid']));
    }
}

$where = "b.bw_status = 1";
if ($month_ym !== '') {
    $where .= " AND DATE_FORMAT(b.bw_date_pay,'%Y-%m') = '$month_ym'";
}
if ($mem_id_filter !== '') {
    $where .= " AND b.mem_id = '$mem_id_filter'";
}

$query = "SELECT 
            b.mem_id,
            m.mem_name,
            DATE_FORMAT(b.bw_date_pay,'%Y-%m') AS ym,
            SUM(CASE WHEN r.br_type = 1 THEN b.bw_amount ELSE 0 END) AS total_common,
            SUM(CASE WHEN r.br_type = 2 THEN b.bw_amount ELSE 0 END) AS total_emergency,
            m.mem_stock_savings
          FROM borrowing b
          INNER JOIN member m ON b.mem_id = m.mem_id
          INNER JOIN borrow_request r ON b.br_id = r.br_id
          WHERE $where
          GROUP BY b.mem_id, m.mem_name, ym, m.mem_stock_savings
          ORDER BY ym DESC, m.mem_name ASC";
$result = mysqli_query($condb, $query);

// สมาชิกทั้งหมด (สำหรับ dropdown filter)
$q_mem = "SELECT mem_id, mem_name FROM member WHERE mem_status IN ('2','3') ORDER BY mem_name ASC";
$res_mem = mysqli_query($condb, $q_mem);
?>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0"><i class="fas fa-receipt me-2"></i> รายงานใบเสร็จรับเงิน (ผู้กู้)</h5>
            </div>
            <div class="card-body">
                <form method="get" action="receipt.php" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">ประจำเดือน</label>
                        <input type="month" class="form-control" name="month_ym" value="<?php echo htmlspecialchars($month_ym); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">สมาชิก</label>
                        <select class="form-select" name="mem_id">
                            <option value="">-- ทุกคน --</option>
                            <?php while ($m = mysqli_fetch_assoc($res_mem)): ?>
                            <option value="<?php echo htmlspecialchars($m['mem_id']); ?>" <?php echo $mem_id_filter === $m['mem_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['mem_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-1"><i class="fas fa-search"></i> ค้นหา</button>
                        <a href="receipt.php" class="btn btn-outline-secondary">ล้าง</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">เดือน</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th class="text-end">เงินกู้สามัญ (บาท)</th>
                                <th class="text-end">เงินกู้ฉุกเฉิน (บาท)</th>
                                <th class="text-end">เงินออมหุ้น/เดือน (บาท)</th>
                                <th class="text-end">ยอดรวมที่ชำระ (บาท)</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): 
                                    $sum_common = (float)$row['total_common'];
                                    $sum_emergency = (float)$row['total_emergency'];
                                    $stock_saving = isset($row['mem_stock_savings']) ? (float)$row['mem_stock_savings'] : 0;
                                    $total_all = $sum_common + $sum_emergency + $stock_saving;
                                    $ym_label = $row['ym'] ? date('m/Y', strtotime($row['ym'] . '-01')) : '-';
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo htmlspecialchars($ym_label); ?></td>
                                    <td><?php echo htmlspecialchars($row['mem_name']); ?></td>
                                    <td class="text-end"><?php echo $sum_common ? number_format($sum_common) : '-'; ?></td>
                                    <td class="text-end"><?php echo $sum_emergency ? number_format($sum_emergency) : '-'; ?></td>
                                    <td class="text-end"><?php echo $stock_saving ? number_format($stock_saving) : '-'; ?></td>
                                    <td class="text-end fw-bold text-danger"><?php echo number_format($total_all); ?></td>
                                    <td class="text-center">
                                        <a href="receipt_print.php?mem_id=<?php echo htmlspecialchars($row['mem_id']); ?>&ym=<?php echo htmlspecialchars($row['ym']); ?>" target="_blank" class="btn btn-success btn-sm">
                                            <i class="fas fa-print"></i> พิมพ์ใบเสร็จ
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">ไม่มีรายการชำระเงินตามเงื่อนไขที่เลือก</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>
