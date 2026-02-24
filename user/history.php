<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3')) {
    header("Location: ../login.php");
    exit();
}

$menu = "history";
include('../config/condb.php');
include('../includes/header.php');

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);
$sql = "SELECT * FROM borrow_request WHERE mem_id = '$mem_id' ORDER BY br_id DESC";
$result = mysqli_query($condb, $sql);

// ดึงค่าจากแถวตามชื่อคอลัมน์ (รองรับทั้งตัวพิมพ์เล็ก-ใหญ่จาก MySQL)
function rowVal($row, $key) {
    if (isset($row[$key])) return $row[$key];
    foreach ($row as $k => $v) {
        if (strcasecmp($k, $key) === 0) return $v;
    }
    return null;
}

function typeLabel($t) {
    return $t == 1 ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
}
function statusBadge($s) {
    if ($s == 0) return '<span class="badge bg-warning text-dark">รอพิจารณา</span>';
    if ($s == 1) return '<span class="badge bg-success">อนุมัติ</span>';
    if ($s == 2) return '<span class="badge bg-danger">ไม่อนุมัติ</span>';
    return '-';
}
function guarantorStatus($g) {
    if ($g === null || $g === '' || !is_numeric($g)) return '-';
    $g = (int)$g;
    if ($g === 1) return '<span class="badge bg-success">อนุมัติ</span>';
    if ($g === 2) return '<span class="badge bg-danger">ไม่อนุมัติ</span>';
    return '<span class="badge bg-secondary">รอยืนยัน</span>';
}
?>

<section class="content">
  <div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i> ประวัติคำขอกู้ของฉัน</h5>
        <a href="borrow.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> ยื่นคำขอใหม่</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">เลขที่</th>
                <th>ประเภท</th>
                <th class="text-end">ยอดกู้ (บาท)</th>
                <th class="text-center">ผ่อน (เดือน)</th>
                <th class="text-center">ผู้ค้ำ 1</th>
                <th class="text-center">ผู้ค้ำ 2</th>
                <th class="text-center">สถานะคำขอ</th>
                <th class="text-center">วันที่ยื่น</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td class="text-center"><?php echo (int)$row['br_id']; ?></td>
                <td><?php echo typeLabel($row['br_type']); ?></td>
                <td class="text-end fw-bold"><?php echo number_format($row['br_amount']); ?></td>
                <td class="text-center"><?php echo (int)$row['br_months_pay']; ?></td>
                <td class="text-center"><?php echo (int)rowVal($row, 'guarantee_type') === 1 ? guarantorStatus(rowVal($row, 'guarantor_1_approve')) : '-'; ?></td>
                <td class="text-center"><?php echo (int)rowVal($row, 'guarantee_type') === 1 ? guarantorStatus(rowVal($row, 'guarantor_2_approve')) : '-'; ?></td>
                <td class="text-center"><?php echo statusBadge($row['br_status']); ?></td>
                <td class="text-center"><?php echo date('d/m/Y', strtotime($row['br_date_request'])); ?></td>
              </tr>
              <?php if ($row['br_status'] == 2 && !empty($row['br_respond'])): ?>
              <tr class="table-danger">
                <td colspan="8" class="small">เหตุผลที่ไม่อนุมัติ: <?php echo htmlspecialchars($row['br_respond']); ?></td>
              </tr>
              <?php endif; ?>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
          <p class="mb-0">ยังไม่มีประวัติคำขอกู้</p>
          <a href="borrow.php" class="btn btn-primary btn-sm mt-2">ยื่นคำขอกู้</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
