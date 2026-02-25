<?php
session_start();

if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');

$br_id = isset($_GET['br_id']) ? (int)$_GET['br_id'] : 0;
$bw_id = isset($_GET['bw_id']) ? (int)$_GET['bw_id'] : 0;

// บันทึกการชำระเงิน (POST) — ต้องทำก่อนส่ง HTML ใดๆ เพื่อให้ redirect ได้
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_pay']) && $bw_id > 0) {
    $bw_id_safe = (int)$bw_id;
    // ดึงข้อมูลงวด + ประเภทเงินกู้ + mem_id ก่อนอัปเดต
    $qPay = "SELECT b.bw_amount, b.mem_id, b.bw_status, r.br_type
             FROM borrowing b
             INNER JOIN borrow_request r ON b.br_id = r.br_id
             WHERE b.bw_id = $bw_id_safe";
    $resPay = mysqli_query($condb, $qPay);
    $payRow = mysqli_fetch_assoc($resPay);

    if (!$payRow || (int)$payRow['bw_status'] === 1) {
        header("Location: mustpay.php?error=already");
        exit();
    }

    $paid_input = isset($_POST['bw_date_paid']) && trim($_POST['bw_date_paid']) !== ''
        ? trim($_POST['bw_date_paid'])
        : date('Y-m-d\TH:i');
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $paid_input, $m)) {
        $bw_date_pay_value = mysqli_real_escape_string($condb, $m[1]);
    } else {
        $bw_date_pay_value = date('Y-m-d');
    }

    $sql = "UPDATE borrowing SET bw_status = 1, bw_date_pay = '$bw_date_pay_value' WHERE bw_id = $bw_id_safe";
    if (!mysqli_query($condb, $sql)) {
        $payment_error = mysqli_error($condb);
    } else {
        // นำยอดชำระไปเพิ่มวงเงินสมาชิก ตามประเภทเงินกู้
        $pay_amount = (float)$payRow['bw_amount'];
        $mem_id_credit = mysqli_real_escape_string($condb, $payRow['mem_id']);
        $br_type = (int)$payRow['br_type'];

        if ($pay_amount > 0 && $mem_id_credit !== '') {
            if ($br_type === 1) {
                $sql_credit = "UPDATE member SET mem_common_credit = mem_common_credit + $pay_amount WHERE mem_id = '$mem_id_credit'";
            } else {
                $sql_credit = "UPDATE member SET mem_emergency_credit = mem_emergency_credit + $pay_amount WHERE mem_id = '$mem_id_credit'";
            }
            @mysqli_query($condb, $sql_credit);

            // เพิ่มเงินออมหุ้น/เดือน เข้าในยอดหุ้นสะสม
            $sql_stock = "UPDATE member SET mem_amount_stock = mem_amount_stock + mem_stock_savings WHERE mem_id = '$mem_id_credit'";
            @mysqli_query($condb, $sql_stock);
        }
        header("Location: mustpay.php?save_ok=1");
        exit();
    }
}

$menu = "mustpay";
include('../includes/header.php');

if (!$br_id || !$bw_id) {
    echo "<script>alert('พารามิเตอร์ไม่ครบ'); window.location='mustpay.php';</script>";
    exit();
}

// ดึงข้อมูลงวดที่ต้องชำระ
$query = "SELECT borrowing.*, member.mem_name, borrow_request.br_type
          FROM borrowing
          INNER JOIN member ON borrowing.mem_id = member.mem_id
          INNER JOIN borrow_request ON borrowing.br_id = borrow_request.br_id
          WHERE borrowing.bw_id = $bw_id AND borrowing.br_id = $br_id";
$result = mysqli_query($condb, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$row) {
    echo "<script>alert('ไม่พบรายการนี้'); window.location='mustpay.php';</script>";
    exit();
}

if ((int)$row['bw_status'] === 1) {
    echo "<script>alert('งวดนี้ชำระเงินแล้ว'); window.location='mustpay.php';</script>";
    exit();
}

$error = isset($payment_error) ? $payment_error : '';

$borrowType = ((int)$row['br_type'] === 1) ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
$default_datetime = date('Y-m-d\TH:i');
?>

<section class="content mt-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0"><i class="fas fa-money-bill-wave"></i> แจ้งชำระเงิน</h5>
                        <a href="mustpay.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">ชื่อ-นามสกุล</th>
                                    <td><?php echo htmlspecialchars($row['mem_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>ประเภทเงินกู้</th>
                                    <td><?php echo $borrowType; ?></td>
                                </tr>
                                <tr>
                                    <th>งวดที่</th>
                                    <td><?php echo htmlspecialchars($row['bw_round']); ?></td>
                                </tr>
                                <tr>
                                    <th>ยอดชำระ (บาท)</th>
                                    <td class="fw-bold text-danger"><?php echo number_format($row['bw_amount']); ?></td>
                                </tr>
                                <tr>
                                    <th>กำหนดชำระ</th>
                                    <td><?php echo date('d/m/Y', strtotime($row['bw_date_pay'])); ?></td>
                                </tr>
                            </table>
                        </div>

                        <form method="post" action="">
                            <input type="hidden" name="confirm_pay" value="1">
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label fw-bold">วันเวลาที่ชำระเงิน</label>
                                <div class="col-sm-6">
                                    <input type="datetime-local" class="form-control" name="bw_date_paid" value="<?php echo htmlspecialchars($default_datetime); ?>" required>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> ยืนยันชำระเงิน
                                </button>
                                <a href="mustpay.php" class="btn btn-secondary">ยกเลิก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('../includes/footer.php'); ?>
