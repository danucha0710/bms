<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "mustpay";
include('../includes/header.php');

// =========================================================
// 1. ดึงรายการเดือนทั้งหมดที่ยังมีค้างชำระ (สำหรับ tab/dropdown เลือกเดือน)
// =========================================================
$q_months = "SELECT DISTINCT DATE_FORMAT(bw_date_pay,'%Y-%m') AS ym
             FROM borrowing
             WHERE bw_status = 0
             ORDER BY ym ASC";
$r_months = mysqli_query($condb, $q_months);
$available_months = [];
while ($rm = mysqli_fetch_assoc($r_months)) {
    $available_months[] = $rm['ym'];
}

// เดือนที่เลือก: รับจาก GET (?ym=) หรือใช้เดือนแรกสุด (เก่าที่สุด)
$selected_ym = isset($_GET['ym']) ? mysqli_real_escape_string($condb, $_GET['ym']) : '';
if ($selected_ym === '' && !empty($available_months)) {
    $selected_ym = $available_months[0];
}
// ตรวจว่า ym ที่เลือกอยู่ในรายการจริง ถ้าไม่ให้ใช้ตัวแรก
if ($selected_ym !== '' && !in_array($selected_ym, $available_months)) {
    $selected_ym = !empty($available_months) ? $available_months[0] : '';
}

$month_ym = $selected_ym;
$month_label = $month_ym ? date('m/Y', strtotime($month_ym . '-01')) : '-';

// =========================================================
// 2. ดึงรายชื่อสมาชิกของเดือนที่เลือก (สำหรับ modal กลุ่ม)
// =========================================================
$group_members = [];
if ($month_ym) {
    $q_mem = "SELECT DISTINCT borrowing.mem_id, member.mem_name 
              FROM borrowing 
              INNER JOIN member ON borrowing.mem_id = member.mem_id 
              WHERE borrowing.bw_status = 0 
              AND DATE_FORMAT(borrowing.bw_date_pay,'%Y-%m') = '$month_ym' 
              ORDER BY member.mem_name ASC";
    $r_mem = mysqli_query($condb, $q_mem);
    if ($r_mem) {
        while ($m = mysqli_fetch_assoc($r_mem)) {
            $group_members[] = $m;
        }
    }
}

// =========================================================
// 3. ดึงข้อมูลค้างชำระรวมต่อคน ของเดือนที่เลือก
// =========================================================
$result = null;
$rowcount = 0;
if ($month_ym) {
    $query = "SELECT 
                borrowing.mem_id,
                member.mem_name,
                SUM(CASE WHEN borrow_request.br_type = 1 THEN borrowing.bw_amount ELSE 0 END) AS total_common,
                SUM(CASE WHEN borrow_request.br_type = 2 THEN borrowing.bw_amount ELSE 0 END) AS total_emergency,
                member.mem_stock_savings
              FROM borrowing
              INNER JOIN member ON borrowing.mem_id = member.mem_id
              INNER JOIN borrow_request ON borrowing.br_id = borrow_request.br_id
              WHERE borrowing.bw_status = 0
                AND DATE_FORMAT(borrowing.bw_date_pay,'%Y-%m') = '$month_ym'
              GROUP BY borrowing.mem_id, member.mem_name, member.mem_stock_savings
              ORDER BY member.mem_name ASC";
    $result = mysqli_query($condb, $query) or die("Error : ".mysqli_error($condb));
    $rowcount = mysqli_num_rows($result);
}

// =========================================================
// 4. ดึงค่า Config ระบบ
// =========================================================
$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);
?>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title m-0"><i class="fas fa-file-invoice-dollar"></i> รายการค้างชำระ</h4>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#groupPayModal">
                    <i class="fas fa-users"></i> แจ้งชำระเงินแบบกลุ่ม
                </button>
            </div>

            <div class="card-body">

                <div class="alert alert-light border mb-3 d-flex flex-wrap align-items-center gap-2">
                    <?php if (!empty($available_months)): ?>
                        <!-- Dropdown เลือกเดือน -->
                        <label class="form-label mb-0 fw-bold text-nowrap">
                            <i class="fas fa-calendar-alt me-1 text-secondary"></i> เลือกเดือน:
                        </label>
                        <select class="form-select w-auto" id="monthSelector" onchange="window.location='mustpay.php?ym='+this.value;">
                            <?php foreach ($available_months as $ym_opt): 
                                $lbl = date('m/Y', strtotime($ym_opt . '-01'));
                            ?>
                            <option value="<?php echo htmlspecialchars($ym_opt); ?>"
                                <?php echo ($ym_opt === $month_ym) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lbl); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    
                    <i class="fas fa-info-circle text-info"></i>
                    <?php if ($month_ym): ?>
                        แสดงรายการค้างชำระรวมต่อคน สำหรับงวดที่ถึงกำหนดในเดือน <strong><?php echo htmlspecialchars($month_label); ?></strong>
                        (พบ <?php echo $rowcount; ?> ราย)
                    <?php else: ?>
                        ไม่มีรายการค้างชำระ
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle" id="tableSearch">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">ชื่อ-นามสกุล</th>
                                <th width="15%" class="text-end">เงินกู้สามัญค้างชำระ (บาท)</th>
                                <th width="15%" class="text-end">เงินกู้ฉุกเฉินค้างชำระ (บาท)</th>
                                <th width="15%" class="text-end">เงินออมหุ้น/เดือน (บาท)</th>
                                <th width="15%" class="text-end">ยอดรวมที่ต้องชำระ (บาท)</th>
                                <th width="10%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($rowcount > 0 && $result): ?>
                                <?php foreach($result as $value): 
                                    $sum_common   = (float)$value['total_common'];
                                    $sum_emergency= (float)$value['total_emergency'];
                                    $stock_saving = isset($value['mem_stock_savings']) ? (float)$value['mem_stock_savings'] : 0;
                                    $total_all    = $sum_common + $sum_emergency + $stock_saving;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($value['mem_name']); ?></td>
                                    <td class="text-end"><?php echo $sum_common   ? number_format($sum_common)   : '-'; ?></td>
                                    <td class="text-end"><?php echo $sum_emergency? number_format($sum_emergency): '-'; ?></td>
                                    <td class="text-end"><?php echo $stock_saving ? number_format($stock_saving) : '-'; ?></td>
                                    <td class="text-end fw-bold text-danger"><?php echo number_format($total_all); ?></td>
                                    <td class="text-center">
                                        <?php if ($total_all > 0 && $month_ym): ?>
                                        <form action="payment_group.php" method="POST" class="d-inline"
                                              onsubmit="return confirm('ยืนยันการชำระเงินสำหรับสมาชิกคนนี้หรือไม่?');">
                                            <input type="hidden" name="group_pay" value="1">
                                            <input type="hidden" name="month_ym" value="<?php echo htmlspecialchars($month_ym); ?>">
                                            <input type="hidden" name="pay_date" value="<?php echo date('Y-m-d'); ?>">
                                            <input type="hidden" name="mem_ids[]" value="<?php echo htmlspecialchars($value['mem_id']); ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-money-bill-wave"></i> แจ้งชำระเงิน
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table> 
                </div>   
            </div>
        </div>
    </div>       
</section>

<!-- Modal แจ้งชำระแบบกลุ่ม -->
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
                    <p class="text-muted small">รายชื่อผู้มีงวดค้างชำระในเดือน <?php echo htmlspecialchars($month_label); ?> — เลือก All หรือติ๊กรายบุคคล</p>
                    <div class="border rounded p-2 bg-light" style="max-height: 320px; overflow-y: auto;">
                        <div class="form-check mb-2 border-bottom pb-2">
                            <input class="form-check-input" type="checkbox" id="groupPayAll">
                            <label class="form-check-label fw-bold" for="groupPayAll">เลือกทั้งหมด (All)</label>
                        </div>
                        <?php foreach ($group_members as $gm): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input group-pay-cb" type="checkbox"
                                   name="mem_ids[]"
                                   value="<?php echo htmlspecialchars($gm['mem_id']); ?>"
                                   id="mem_<?php echo htmlspecialchars($gm['mem_id']); ?>">
                            <label class="form-check-label" for="mem_<?php echo htmlspecialchars($gm['mem_id']); ?>">
                                <?php echo htmlspecialchars($gm['mem_name']); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success" name="group_pay" value="1">
                        <i class="fas fa-check"></i> ยืนยันแจ้งชำระแบบกลุ่ม
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="modal-body">
                <p class="text-muted mb-0">ไม่มีรายการค้างชำระในเดือนนี้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
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
