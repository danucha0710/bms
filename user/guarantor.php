<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3')) {
    header("Location: ../login.php");
    exit();
}

$menu = "guarantor";
include('../config/condb.php');
include('../includes/header.php');

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);

$has_col = false;
$rc = @mysqli_query($condb, "SHOW COLUMNS FROM borrow_request LIKE 'guarantor_1_id'");
if ($rc && mysqli_fetch_assoc($rc)) $has_col = true;

$list = [];
if ($has_col) {
    $sql = "SELECT br_id, mem_id, br_type, br_amount, br_months_pay, br_date_request, br_details,
                   guarantor_1, guarantor_2, guarantor_1_id, guarantor_2_id,
                   guarantor_1_approve, guarantor_2_approve,
                   (SELECT mem_name FROM member WHERE mem_id = borrow_request.mem_id) AS borrower_name
            FROM borrow_request
            WHERE br_status = 0
            AND (guarantor_1_id = '$mem_id' OR guarantor_2_id = '$mem_id')
            ORDER BY br_id DESC";
    $rs = mysqli_query($condb, $sql);
    if ($rs) {
        while ($r = mysqli_fetch_assoc($rs)) {
            $r['my_role'] = ($r['guarantor_1_id'] === $mem_id) ? 1 : 2;
            $r['my_approve'] = $r['my_role'] == 1 ? (int)($r['guarantor_1_approve'] ?? 0) : (int)($r['guarantor_2_approve'] ?? 0);
            $list[] = $r;
        }
    }
}

function typeLabel($t) {
    return $t == 1 ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
}
?>

<section class="content">
  <div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i> คำขอที่ต้องรับรอง (ในฐานะผู้ค้ำประกัน)</h5>
      </div>
      <div class="card-body p-0">
        <?php if (!$has_col): ?>
        <div class="p-4 text-muted text-center">ระบบยังไม่ได้เปิดใช้การรับรองจากผู้ค้ำ กรุณาติดต่อผู้ดูแลระบบ</div>
        <?php elseif (empty($list)): ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
          <p class="mb-0">ไม่มีคำขอที่รอการรับรองจากคุณ</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">เลขที่</th>
                <th>ผู้ขอกู้</th>
                <th>ประเภท</th>
                <th class="text-end">ยอดกู้ (บาท)</th>
                <th class="text-center">บทบาทคุณ</th>
                <th class="text-center">สถานะการรับรอง</th>
                <th class="text-center">จัดการ</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($list as $r): ?>
              <tr>
                <td class="text-center"><?php echo (int)$r['br_id']; ?></td>
                <td><?php echo htmlspecialchars($r['borrower_name']); ?></td>
                <td><?php echo typeLabel($r['br_type']); ?></td>
                <td class="text-end fw-bold"><?php echo number_format($r['br_amount']); ?></td>
                <td class="text-center">ผู้ค้ำคนที่ <?php echo $r['my_role']; ?></td>
                <td class="text-center">
                  <?php
                  if ($r['my_approve'] == 0) echo '<span class="badge bg-warning text-dark">รอรับรอง</span>';
                  elseif ($r['my_approve'] == 1) echo '<span class="badge bg-success">อนุมัติแล้ว</span>';
                  else echo '<span class="badge bg-danger">ไม่อนุมัติ</span>';
                  ?>
                </td>
                <td class="text-center">
                  <?php if ($r['my_approve'] == 0): ?>
                  <form action="guarantor_approve.php" method="POST" class="d-inline">
                    <input type="hidden" name="br_id" value="<?php echo (int)$r['br_id']; ?>">
                    <input type="hidden" name="role" value="<?php echo $r['my_role']; ?>">
                    <button type="submit" name="action" value="1" class="btn btn-success btn-sm me-1"><i class="fas fa-check"></i> อนุมัติ</button>
                    <button type="submit" name="action" value="2" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> ไม่อนุมัติ</button>
                  </form>
                  <?php else: ?>
                  -
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
