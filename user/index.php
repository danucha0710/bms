<?php
session_start();
if (!isset($_SESSION['mem_id'])) {
    header("Location: ../login.php");
    exit();
}
if ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3') {
    header("Location: ../login.php");
    exit();
}

$menu = "index";
include('../config/condb.php');
include('../includes/header.php');

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);

// ข้อมูลสมาชิก (วงเงิน)
$sql_mem = "SELECT mem_name, mem_common_credit, mem_emergency_credit FROM member WHERE mem_id = '$mem_id'";
$rs_mem = mysqli_query($condb, $sql_mem);
$member = mysqli_fetch_assoc($rs_mem);
if (!$member) {
    header("Location: ../login.php");
    exit();
}

// รายการคำขอกู้ของฉัน (ล่าสุด 10 รายการ)
$sql_br = "SELECT * FROM borrow_request WHERE mem_id = '$mem_id' ORDER BY br_id DESC LIMIT 10";
$rs_br = mysqli_query($condb, $sql_br);

function getTypeLabel($t) {
    return $t == 1 ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
}
function getStatusBadge($s) {
    if ($s == 0) return '<span class="badge bg-warning text-dark">รอพิจารณา</span>';
    if ($s == 1) return '<span class="badge bg-success">อนุมัติ</span>';
    if ($s == 2) return '<span class="badge bg-danger">ไม่อนุมัติ</span>';
    return '-';
}
?>

<section class="content">
  <div class="container-fluid py-4">
    <?php if (!empty($_GET['save_ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i> บันทึกคำขอกู้เรียบร้อย รอการรับรองจากผู้ค้ำและพิจารณาจากแอดมิน
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if (!empty($_GET['mem_editp'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i> แก้ไขข้อมูลส่วนตัวเรียบร้อย
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <div class="row mb-4">
      <div class="col-12">
        <h4 class="mb-1"><i class="fas fa-home me-2 text-primary"></i> หน้าแรก</h4>
        <p class="text-muted mb-0">สวัสดีครับ <?php echo htmlspecialchars($member['mem_name']); ?></p>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3 me-3">
              <i class="fas fa-wallet text-primary fa-2x"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">วงเงินกู้สามัญ</p>
              <h5 class="mb-0 text-primary"><?php echo number_format((int)$member['mem_common_credit']); ?> <small class="fs-6 fw-normal">บาท</small></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-danger bg-opacity-10 rounded-3 p-3 me-3">
              <i class="fas fa-first-aid text-danger fa-2x"></i>
            </div>
            <div>
              <p class="text-muted small mb-1">วงเงินกู้ฉุกเฉิน</p>
              <h5 class="mb-0 text-danger"><?php echo number_format((int)$member['mem_emergency_credit']); ?> <small class="fs-6 fw-normal">บาท</small></h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <a href="borrow.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm h-100 border-primary border-2 hover-shadow">
            <div class="card-body d-flex align-items-center justify-content-center text-center">
              <div>
                <i class="fas fa-plus-circle text-primary fa-3x mb-2"></i>
                <p class="mb-0 fw-bold text-dark">ยื่นคำขอกู้</p>
                <small class="text-muted">เพิ่มคำขอกู้ใหม่</small>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-xl-3">
        <a href="guarantor.php" class="text-decoration-none">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-center text-center">
              <div>
                <i class="fas fa-user-check text-info fa-3x mb-2"></i>
                <p class="mb-0 fw-bold text-dark">คำขอที่ต้องรับรอง</p>
                <small class="text-muted">ในฐานะผู้ค้ำประกัน</small>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i> รายการคำขอกู้ของฉัน</h5>
        <a href="history.php" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
      </div>
      <div class="card-body p-0">
        <?php if (mysqli_num_rows($rs_br) > 0): ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">เลขที่</th>
                <th>ประเภท</th>
                <th class="text-end">ยอดกู้ (บาท)</th>
                <th class="text-center">ผ่อน (เดือน)</th>
                <th class="text-center">ผู้ค้ำ 1/2</th>
                <th class="text-center">สถานะ</th>
                <th class="text-center">วันที่ยื่น</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($rs_br)): 
                $g1 = array_key_exists('guarantor_1_approve', $row) ? (int)$row['guarantor_1_approve'] : -1;
                $g2 = array_key_exists('guarantor_2_approve', $row) ? (int)$row['guarantor_2_approve'] : -1;
                $g1txt = $g1 === 0 ? 'รอ' : ($g1 === 1 ? 'อนุมัติ' : ($g1 === 2 ? 'ไม่อนุมัติ' : '-'));
                $g2txt = $g2 === 0 ? 'รอ' : ($g2 === 1 ? 'อนุมัติ' : ($g2 === 2 ? 'ไม่อนุมัติ' : '-'));
              ?>
              <tr>
                <td class="text-center"><?php echo (int)$row['br_id']; ?></td>
                <td><?php echo getTypeLabel($row['br_type']); ?></td>
                <td class="text-end fw-bold"><?php echo number_format($row['br_amount']); ?></td>
                <td class="text-center"><?php echo (int)$row['br_months_pay']; ?></td>
                <td class="text-center small"><?php echo $g1txt; ?> / <?php echo $g2txt; ?></td>
                <td class="text-center"><?php echo getStatusBadge($row['br_status']); ?></td>
                <td class="text-center"><?php echo date('d/m/Y', strtotime($row['br_date_request'])); ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
          <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
          <p class="mb-0">ยังไม่มีคำขอกู้</p>
          <a href="borrow.php" class="btn btn-primary btn-sm mt-2">ยื่นคำขอกู้</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
