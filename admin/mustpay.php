<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "mustpay";
include('../includes/header.php'); // แก้ Path

// =========================================================
// 1. Logic การค้นหาตามช่วงเวลา
// =========================================================
$dateText = "";
$search_msg = "";

if (!empty($_POST['date_s']) && !empty($_POST['date_e'])) {
    $date_s = mysqli_real_escape_string($condb, $_POST['date_s']);
    $date_e = mysqli_real_escape_string($condb, $_POST['date_e']);
    $dateText = "AND borrowing.bw_date_pay BETWEEN '$date_s' AND '$date_e'";
    $search_msg = "ผลการค้นหาช่วงวันที่: " . date('d/m/Y', strtotime($date_s)) . " ถึง " . date('d/m/Y', strtotime($date_e));
} else {
    // Default: แสดงรายการค้างชำระทั้งหมด (ยังไม่จ่าย) และเรียงตามกำหนดชำระจากน้อยไปมาก
    // เช่น ถ้ามีกำหนด 01/03/2026 ของหลายคน จะขึ้นมาก่อน ตามด้วย 02/03/2026 ฯลฯ
    $dateText = "";
    $search_msg = "แสดงรายการค้างชำระทั้งหมด เรียงตามวันที่กำหนดชำระ (วันครบกำหนดน้อยที่สุดก่อน)";
}

// =========================================================
// 2. ดึงข้อมูล (bw_amount = เงินที่ต้องจ่ายแต่ละงวด คำนวณตอนอนุมัติใน borrow_db.php)
// =========================================================
$query = "SELECT borrowing.*, member.mem_name, borrow_request.br_type 
          FROM borrowing
          INNER JOIN member ON borrowing.mem_id = member.mem_id
          INNER JOIN borrow_request ON borrowing.br_id = borrow_request.br_id
          WHERE borrowing.bw_status = 0 $dateText
          ORDER BY borrowing.bw_date_pay ASC, borrowing.br_id ASC, borrowing.bw_round ASC";
          
$result = mysqli_query($condb, $query) or die("Error : ".mysqli_error($condb));
$rowcount = mysqli_num_rows($result);

// =========================================================
// 3. ดึงค่า Config ระบบ (สำหรับ Form จ่ายเงิน)
// =========================================================
$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);

// =========================================================
// 4. ข้อมูลสำหรับ Modal แจ้งชำระแบบกลุ่ม (เดือนล่าสุด = เดือนที่ครบกำหนดเร็วที่สุดที่ยังมีค้าง)
// =========================================================
$q_first = "SELECT MIN(bw_date_pay) AS first_due FROM borrowing WHERE bw_status = 0";
$r_first = mysqli_query($condb, $q_first);
$first_row = $r_first ? mysqli_fetch_assoc($r_first) : null;
$month_ym = ($first_row && $first_row['first_due']) ? date('Y-m', strtotime($first_row['first_due'])) : '';
$group_members = [];
if ($month_ym) {
    $q_mem = "SELECT DISTINCT borrowing.mem_id, member.mem_name 
              FROM borrowing 
              INNER JOIN member ON borrowing.mem_id = member.mem_id 
              WHERE borrowing.bw_status = 0 
              AND DATE_FORMAT(borrowing.bw_date_pay,'%Y-%m') = '" . mysqli_real_escape_string($condb, $month_ym) . "' 
              ORDER BY member.mem_name ASC";
    $r_mem = mysqli_query($condb, $q_mem);
    if ($r_mem) {
        while ($m = mysqli_fetch_assoc($r_mem)) {
            $group_members[] = $m;
        }
    }
}
$month_label = $month_ym ? date('m/Y', strtotime($month_ym . '-01')) : '-';
?>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title m-0"><i class="fas fa-file-invoice-dollar"></i> รายการค้างชำระ</h4>
                <div>
                    <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fa fa-search"></i> ค้นหาตามช่วงวันเวลา
                    </button>
                    </div>
            </div>

            <div class="card-body">
                <div class="alert alert-light border mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span>
                        <i class="fas fa-info-circle text-info"></i> <?php echo $search_msg; ?>
                        <?php if(!empty($_POST['date_s'])){ ?>
                            <a href="mustpay.php" class="btn btn-xs btn-danger ms-2">ล้างค่า</a>
                        <?php } ?>
                    </span>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#groupPayModal">
                        <i class="fas fa-users"></i> แจ้งชำระเงินแบบกลุ่ม
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle" id="tableSearch">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">ชื่อ-นามสกุล</th>
                                <th width="10%" class="text-center">ประเภท</th>
                                <th width="10%" class="text-center">งวดที่</th>
                                <th width="15%" class="text-end">ยอดชำระ (บาท)</th>
                                <th width="15%" class="text-center">กำหนดชำระ</th>
                                <th width="10%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($rowcount > 0) {
                                foreach($result as $value){ 
                                    $borrowType = ($value['br_type'] == 1) ? '<span class="badge bg-primary">สามัญ</span>' : '<span class="badge bg-danger">ฉุกเฉิน</span>';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($value['mem_name']); ?></td>
                                    <td class="text-center"><?php echo $borrowType; ?></td>
                                    <td class="text-center"><?php echo $value['bw_round']; ?></td>
                                    <td class="text-end fw-bold text-danger"><?php echo number_format($value['bw_amount']); ?></td>
                                    <td class="text-center" data-order="<?php echo htmlspecialchars($value['bw_date_pay']); ?>"><?php echo date('d/m/Y', strtotime($value['bw_date_pay'])); ?></td>
                                    <td class="text-center">
                                        <a href="payment.php?br_id=<?php echo htmlspecialchars($value['br_id']); ?>&bw_id=<?php echo htmlspecialchars($value['bw_id']); ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-money-bill-wave"></i> แจ้งชำระเงิน
                                        </a>
                                    </td>
                                </tr>
                            <?php } } ?> 
                        </tbody>
                    </table> 
                </div>   
            </div>
        </div>
    </div>       
</section>

<div class="modal fade" id="groupPayModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-users"></i> แจ้งชำระเงินแบบกลุ่ม (เดือน <?php echo htmlspecialchars($month_label); ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <?php if (count($group_members) > 0): ?>
            <form action="payment_group.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="month_ym" value="<?php echo htmlspecialchars($month_ym); ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">วันเวลาที่ชำระเงิน</label>
                        <input type="date" class="form-control" name="pay_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <p class="text-muted small">รายชื่อผู้มีงวดค้างชำระในเดือนนี้ (เรียงตามชื่อ) — เลือก All หรือติ๊กรายบุคคล</p>
                    <div class="border rounded p-2 bg-light" style="max-height: 320px; overflow-y: auto;">
                        <div class="form-check mb-2 border-bottom pb-2">
                            <input class="form-check-input" type="checkbox" id="groupPayAll">
                            <label class="form-check-label fw-bold" for="groupPayAll">เลือกทั้งหมด (All)</label>
                        </div>
                        <?php foreach ($group_members as $gm): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input group-pay-cb" type="checkbox" name="mem_ids[]" value="<?php echo htmlspecialchars($gm['mem_id']); ?>" id="mem_<?php echo htmlspecialchars($gm['mem_id']); ?>">
                            <label class="form-check-label" for="mem_<?php echo htmlspecialchars($gm['mem_id']); ?>"><?php echo htmlspecialchars($gm['mem_name']); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success" name="group_pay" value="1"><i class="fas fa-check"></i> ยืนยันแจ้งชำระแบบกลุ่ม</button>
                </div>
            </form>
            <?php else: ?>
            <div class="modal-body">
                <p class="text-muted mb-0">ไม่มีรายการค้างชำระในเดือนล่าสุด</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="mustpay.php" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fas fa-search"></i> ค้นหาตามช่วงวันเวลา</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" name="date_s" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" name="date_e" required>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                </div>
            </div>
        </form>
    </div>
</div> 

<script>
// หน้า mustpay: ให้ DataTable เรียงตามคอลัมน์ "กำหนดชำระ" (คอลัมน์ที่ 5, index 4) จากน้อยไปมาก
window.MUSTPAY_ORDER = [[4, "asc"]];

document.addEventListener('DOMContentLoaded', function() {
    var groupPayAll = document.getElementById('groupPayAll');
    var groupCbs = document.querySelectorAll('.group-pay-cb');
    if (groupPayAll && groupCbs.length) {
        groupPayAll.addEventListener('change', function() {
            groupCbs.forEach(function(cb) { cb.checked = groupPayAll.checked; });
        });
        groupCbs.forEach(function(cb) {
            cb.addEventListener('change', function() {
                var checked = document.querySelectorAll('.group-pay-cb:checked').length;
                groupPayAll.checked = (checked === groupCbs.length);
            });
        });
    }
});
</script>
<?php include('../includes/footer.php'); ?>