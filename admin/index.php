<?php 
session_start();

// 1. เช็คสิทธิ์ Admin หรือพนักงานคีย์ข้อมูล
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '0' && $_SESSION['mem_status'] != '1')) {
    header("Location: ../login.php");
    exit();
}

$menu = "index";

// 2. เรียกไฟล์โครงสร้าง
include('../config/condb.php');
include('../includes/header.php'); 

// --- 3. ส่วนดึงข้อมูล Dashboard ---

// 3.1 นับจำนวนคำขอกู้ทั้งหมด (รอพิจารณา)
$sql_wait = "SELECT COUNT(*) as total FROM borrow_request WHERE br_status = 0";
$rs_wait = mysqli_query($condb, $sql_wait);
$cnt_wait = mysqli_fetch_assoc($rs_wait)['total'];

// 3.2 นับจำนวนสมาชิกทั้งหมด
$sql_mem = "SELECT COUNT(*) as total FROM member";
$rs_mem = mysqli_query($condb, $sql_mem);
$cnt_mem = mysqli_fetch_assoc($rs_mem)['total'];

// 3.3 ยอดเงินกู้รวมทั้งหมด (เฉพาะที่อนุมัติแล้ว)
$sql_money = "SELECT SUM(br_amount) as total FROM borrow_request WHERE br_status = 1";
$rs_money = mysqli_query($condb, $sql_money);
$sum_money = mysqli_fetch_assoc($rs_money)['total'];

// 3.4 นับจำนวนงวดรอชำระ/ค้าง เฉพาะ \"เดือนล่าสุด\" (เดือนที่ครบกำหนดเร็วที่สุดที่ยังมีค้าง)
$sql_first_due = "SELECT MIN(bw_date_pay) AS first_due FROM borrowing WHERE bw_status = 0";
$rs_first_due = mysqli_query($condb, $sql_first_due);
$row_first = $rs_first_due ? mysqli_fetch_assoc($rs_first_due) : null;
$cnt_due = 0;
if ($row_first && $row_first['first_due']) {
    $ym = date('Y-m', strtotime($row_first['first_due']));
    $sql_due = "SELECT COUNT(*) AS total FROM borrowing WHERE bw_status = 0 AND DATE_FORMAT(bw_date_pay,'%Y-%m') = '$ym'";
    $rs_due = mysqli_query($condb, $sql_due);
    $cnt_due = $rs_due ? mysqli_fetch_assoc($rs_due)['total'] : 0;
}

// 3.5 ข้อมูลสำหรับกราฟสถิติการขอกู้ (7 วันย้อนหลัง)
$chart7_labels = [];
$chart7_values = [];
$date_map = [];
$sql_last7 = "SELECT DATE(br_date_request) AS d, COUNT(*) AS c 
              FROM borrow_request 
              WHERE br_date_request >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
              GROUP BY DATE(br_date_request)";
$rs_last7 = mysqli_query($condb, $sql_last7);
if ($rs_last7) {
    while ($r = mysqli_fetch_assoc($rs_last7)) {
        $date_map[$r['d']] = (int)$r['c'];
    }
}
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chart7_labels[] = date('d/m', strtotime($d));
    $chart7_values[] = isset($date_map[$d]) ? $date_map[$d] : 0;
}

// 3.6 ข้อมูลสำหรับกราฟแนวโน้มยอดเงินกู้ (รายเดือน) – 6 เดือนย้อนหลัง
$chartM_labels = [];
$chartM_values = [];
$month_map = [];
$startMonth = date('Y-m-01', strtotime('-5 months'));
$sql_month = "SELECT DATE_FORMAT(br_date_approve,'%Y-%m') AS ym, SUM(br_amount) AS total
              FROM borrow_request
              WHERE br_status = 1 AND br_date_approve >= '$startMonth'
              GROUP BY DATE_FORMAT(br_date_approve,'%Y-%m')
              ORDER BY ym";
$rs_month = mysqli_query($condb, $sql_month);
if ($rs_month) {
    while ($r = mysqli_fetch_assoc($rs_month)) {
        $month_map[$r['ym']] = (float)$r['total'];
    }
}
for ($i = 5; $i >= 0; $i--) {
    $ym = date('Y-m', strtotime("-$i months"));
    $chartM_labels[] = date('m/Y', strtotime($ym . '-01'));
    $chartM_values[] = isset($month_map[$ym]) ? $month_map[$ym] : 0;
}
?>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-dark text-center border-radius-xl mt-n4 position-absolute">
              <i class="fas fa-file-invoice-dollar opacity-10" style="font-size: 24px; margin-top: 15px;"></i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">รอพิจารณาอนุมัติ</p>
              <h4 class="mb-0 text-warning"><?php echo number_format($cnt_wait); ?> รายการ</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">Action:</span> รีบดำเนินการตรวจสอบ</p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
              <i class="fas fa-users opacity-10" style="font-size: 24px; margin-top: 15px;"></i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">สมาชิกทั้งหมด</p>
              <h4 class="mb-0"><?php echo number_format($cnt_mem); ?> คน</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
             <p class="mb-0">ข้อมูลล่าสุด ณ วันนี้</p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
              <i class="fas fa-coins opacity-10" style="font-size: 24px; margin-top: 15px;"></i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">ยอดปล่อยกู้รวม</p>
              <h4 class="mb-0"><?php echo number_format($sum_money); ?></h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <p class="mb-0 text-sm">บาท (เฉพาะที่อนุมัติแล้ว)</p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
               <i class="fas fa-hand-holding-usd opacity-10" style="font-size: 24px; margin-top: 15px;"></i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">งวดรอชำระเดือนนี้</p>
              <h4 class="mb-0"><?php echo number_format($cnt_due); ?> รายการ</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
             <p class="mb-0">ติดตามการชำระเงิน</p>
          </div>
        </div>
      </div>

    </div>
    
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-4">
        <div class="card z-index-2 shadow-sm">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">สถิติการขอกู้ (7 วันย้อนหลัง)</h6>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="fas fa-history text-sm my-auto me-1"></i>
              <p class="mb-0 text-sm"> จำนวนคำขอกู้ต่อวัน 7 วันที่ผ่านมา</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 col-md-6 mb-4">
        <div class="card z-index-2 shadow-sm">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">แนวโน้มยอดเงินกู้ (รายเดือน)</h6>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="fas fa-chart-line text-sm my-auto me-1"></i>
              <p class="mb-0 text-sm"> ยอดปล่อยกู้รวมรายเดือน (6 เดือนย้อนหลัง)</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var ctxBar = document.getElementById('chart-bars').getContext('2d');
  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($chart7_labels); ?>,
      datasets: [{
        label: 'จำนวนคำขอกู้',
        data: <?php echo json_encode($chart7_values, JSON_NUMERIC_CHECK); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0 }
        }
      }
    }
  });

  var ctxLine = document.getElementById('chart-line').getContext('2d');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($chartM_labels); ?>,
      datasets: [{
        label: 'ยอดปล่อยกู้ (บาท)',
        data: <?php echo json_encode($chartM_values, JSON_NUMERIC_CHECK); ?>,
        borderColor: 'rgba(40, 167, 69, 1)',
        backgroundColor: 'rgba(40, 167, 69, 0.2)',
        borderWidth: 2,
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
</script>

<?php include('../includes/footer.php'); ?>