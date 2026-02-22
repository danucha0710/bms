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
    // Default: แสดงรายการของเดือนปัจจุบันและเดือนก่อนหน้า ที่ยังไม่จ่าย
    $previousMonth = date('Y-m', strtotime('-1 month'));
    $currentMonth = date('Y-m');
    $dateText = "AND (borrowing.bw_date_pay LIKE '$previousMonth%' OR borrowing.bw_date_pay LIKE '$currentMonth%')";
    $search_msg = "แสดงรายการค้างชำระ (เดือนปัจจุบันและเดือนก่อนหน้า)";
}

// =========================================================
// 2. ดึงข้อมูล (bw_amount = เงินที่ต้องจ่ายแต่ละงวด คำนวณตอนอนุมัติใน borrow_db.php)
// =========================================================
$query = "SELECT borrowing.*, member.mem_name, borrow_request.br_type 
          FROM borrowing
          INNER JOIN member ON borrowing.mem_id = member.mem_id
          INNER JOIN borrow_request ON borrowing.br_id = borrow_request.br_id
          WHERE borrowing.bw_status = 0 $dateText
          ORDER BY borrowing.bw_date_pay DESC, borrowing.br_id ASC";
          
$result = mysqli_query($condb, $query) or die("Error : ".mysqli_error($condb));
$rowcount = mysqli_num_rows($result);

// =========================================================
// 3. ดึงค่า Config ระบบ (สำหรับ Form จ่ายเงิน)
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
                <div>
                    <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fa fa-search"></i> ค้นหาตามช่วงวันเวลา
                    </button>
                    </div>
            </div>

            <div class="card-body">
                <div class="alert alert-light border mb-3">
                    <i class="fas fa-info-circle text-info"></i> <?php echo $search_msg; ?>
                    <?php if(!empty($_POST['date_s'])){ ?>
                        <a href="mustpay.php" class="btn btn-xs btn-danger ms-2">ล้างค่า</a>
                    <?php } ?>
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
                                    <td class="text-center"><?php echo date('d/m/Y', strtotime($value['bw_date_pay'])); ?></td>
                                    <td class="text-center">
                                        <a href="payment.php?br_id=<?php echo htmlspecialchars($value['br_id']); ?>&bw_id=<?php echo htmlspecialchars($value['bw_id']); ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-money-bill-wave"></i> แจ้งชำระ
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

<?php include('../includes/footer.php'); ?>