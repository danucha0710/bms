<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '0' && $_SESSION['mem_status'] != '1')) {
    header("Location: ../login.php");
    exit();
}

$menu = "installments";
include('../includes/header.php');

// ดึงข้อมูลสัญญาที่อนุมัติแล้ว พร้อมจำนวนงวด
$sql_loans = "SELECT r.br_id, r.mem_id, r.br_type, r.br_amount, r.br_months_pay, m.mem_name
              FROM borrow_request r
              INNER JOIN member m ON r.mem_id = m.mem_id
              WHERE r.br_status = 1
              ORDER BY r.br_id DESC";
$res_loans = mysqli_query($condb, $sql_loans);

function loanTypeLabel($t) {
    if ((int)$t === 1) return 'เงินกู้สามัญ';
    if ((int)$t === 2) return 'เงินกู้ฉุกเฉิน';
    return '-';
}

// เตรียม HTML ตารางงวดแบบซ่อนต่อสัญญา
$installment_tables = [];
if ($res_loans) {
    mysqli_data_seek($res_loans, 0);
    while ($ln = mysqli_fetch_assoc($res_loans)) {
        $br_id_int = (int)$ln['br_id'];
        $q_inst = "SELECT bw_round, bw_amount, bw_date_pay, bw_status 
                   FROM borrowing 
                   WHERE br_id = $br_id_int 
                   ORDER BY bw_date_pay ASC, bw_id ASC";
        $r_inst = mysqli_query($condb, $q_inst);
        ob_start();
        ?>
        <h6 class="mb-3">คำขอกู้เลขที่ <?php echo (int)$ln['br_id']; ?> - <?php echo htmlspecialchars($ln['mem_name']); ?></h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 15%;">งวดที่</th>
                        <th class="text-end" style="width: 25%;">ยอดชำระ (บาท)</th>
                        <th class="text-center" style="width: 25%;">กำหนดชำระ</th>
                        <th class="text-center" style="width: 20%;">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($r_inst && mysqli_num_rows($r_inst) > 0): ?>
                    <?php while ($it = mysqli_fetch_assoc($r_inst)): ?>
                    <tr>
                        <td class="text-center"><?php echo htmlspecialchars($it['bw_round']); ?></td>
                        <td class="text-end"><?php echo number_format($it['bw_amount']); ?></td>
                        <td class="text-center"><?php echo date('d/m/Y', strtotime($it['bw_date_pay'])); ?></td>
                        <td class="text-center">
                            <?php
                            $bws = (int)$it['bw_status'];
                            if ($bws === 1): ?>
                                <span class="badge bg-success">ชำระแล้ว</span>
                            <?php elseif ($bws === 2): ?>
                                <span class="badge bg-secondary">ยกเลิก (รีเซ็ท)</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">ยังไม่ชำระ</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">ยังไม่มีการสร้างงวดผ่อนชำระสำหรับสัญญานี้</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        $installment_tables[$br_id_int] = ob_get_clean();
    }
    // reset pointer สำหรับใช้ loop แสดงตารางหลัก
    mysqli_data_seek($res_loans, 0);
}
?>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0"><i class="fas fa-list-ol me-2"></i> รายการงวดทั้งหมด</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="tableSearch">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10%;">เลขที่คำขอ</th>
                                <th style="width: 30%;">ชื่อผู้กู้</th>
                                <th class="text-center" style="width: 20%;">ประเภทเงินกู้</th>
                                <th class="text-end" style="width: 20%;">จำนวนเงินกู้ (บาท)</th>
                                <th class="text-center" style="width: 10%;">จำนวนงวด</th>
                                <th class="text-center" style="width: 10%;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($res_loans && mysqli_num_rows($res_loans) > 0): ?>
                            <?php while ($ln = mysqli_fetch_assoc($res_loans)): ?>
                            <tr>
                                <td class="text-center"><?php echo (int)$ln['br_id']; ?></td>
                                <td><?php echo htmlspecialchars($ln['mem_name']); ?></td>
                                <td class="text-center">
                                    <?php echo loanTypeLabel($ln['br_type']); ?>
                                </td>
                                <td class="text-end fw-bold"><?php echo number_format($ln['br_amount']); ?></td>
                                <td class="text-center"><?php echo (int)$ln['br_months_pay']; ?></td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-info btn-sm btn-view-installments"
                                            data-bs-toggle="modal"
                                            data-bs-target="#installmentModal"
                                            data-brid="<?php echo (int)$ln['br_id']; ?>"
                                            data-mem="<?php echo htmlspecialchars($ln['mem_name']); ?>">
                                        <i class="fas fa-list-ul"></i> ดูงวดทั้งหมด
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php foreach ($installment_tables as $br_id_key => $html_table): ?>
                    <div id="inst-br-<?php echo (int)$br_id_key; ?>" class="d-none">
                        <?php echo $html_table; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="installmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title">
            <i class="fas fa-list-ol me-2"></i> รายการงวดทั้งหมด
            <span id="instMemberName" class="fw-bold"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-0">เลือกสัญญาจากตารางด้านบนเพื่อดูรายละเอียดงวดผ่อนชำระ</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.btn-view-installments');
    var modal = document.getElementById('installmentModal');
    if (!modal) return;
    var modalBody = modal.querySelector('.modal-body');
    var memberSpan = document.getElementById('instMemberName');

    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var brId = this.getAttribute('data-brid');
            var memName = this.getAttribute('data-mem');
            if (memberSpan) {
                memberSpan.textContent = ' (เลขที่คำขอ ' + brId + ')';
            }
            var src = document.getElementById('inst-br-' + brId);
            if (src && modalBody) {
                modalBody.innerHTML = src.innerHTML;
            } else if (modalBody) {
                modalBody.innerHTML = '<p class="text-muted mb-0">ไม่พบข้อมูลงวดผ่อนชำระ</p>';
            }
        });
    });
});
</script>

<?php include('../includes/footer.php'); ?>

