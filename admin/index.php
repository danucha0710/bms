<?php 
session_start();

// 1. เช็คสิทธิ์ Admin (ถ้าไม่ใช่ให้ดีดออก)
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "index";

// 2. เรียกไฟล์โครงสร้าง (Path แบบใหม่)
include('../config/condb.php');
include('../includes/header.php'); // ย้าย header.php ไปไว้ใน admin/includes/ หรือ ../includes/ ตามที่คุณจัด
// หมายเหตุ: ถ้าคุณยังเก็บ header.php ไว้ใน admin/ ให้ใช้ include('header.php'); เหมือนเดิม

// --- 3. ส่วนดึงข้อมูล Dashboard (SQL Queries) ---

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

// 3.4 นับจำนวนรายการที่ต้องจ่าย (ค้างชำระ)
// (สมมติว่าดูจากตาราง borrowing ที่สถานะเป็น 0)
$sql_due = "SELECT COUNT(*) as total FROM borrowing WHERE bw_status = 0";
$rs_due = mysqli_query($condb, $sql_due);
$cnt_due = mysqli_fetch_assoc($rs_due)['total'];

?>

<section class="content">
  <div class="container-fluid py-4">
    
    <div class="row">
      
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
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
        <div class="card">
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
        <div class="card">
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
            <p class="mb-0">บาท (เฉพาะที่อนุมัติแล้ว)</p>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
               <i class="fas fa-hand-holding-usd opacity-10" style="font-size: 24px; margin-top: 15px;"></i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">งวดรอชำระ/ค้าง</p>
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
    
    <div class="row mt-4">
      <div class="col-lg-6 col-md-6 mt-4 mb-4">
        <div class="card z-index-2 ">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">สถิติการขอกู้ (รายวัน)</h6>
            <hr class="dark horizontal">
            <div class="d-flex ">
              <i class="material-icons text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> ข้อมูลจำลอง (ยังไม่เชื่อม DB)</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 col-md-6 mt-4 mb-4">
        <div class="card z-index-2 ">
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
              <i class="material-icons text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> ข้อมูลจำลอง (ยังไม่เชื่อม DB)</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include('../includes/footer.php'); ?>

<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="../assets/js/plugins/chartjs.min.js"></script>

<script>
  // Script กราฟ (คงไว้เหมือนเดิมก่อนครับ เพราะ Logic ซับซ้อน)
  var ctx = document.getElementById("chart-bars").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["M", "T", "W", "T", "F", "S", "S"],
      datasets: [{
        label: "Requests",
        tension: 0.4,
        borderWidth: 0,
        borderRadius: 4,
        borderSkipped: false,
        backgroundColor: "rgba(255, 255, 255, .8)",
        data: [50, 20, 10, 22, 50, 10, 40], // ตรงนี้คือข้อมูลสมมติ
        maxBarThickness: 6
      }, ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      interaction: { intersect: false, mode: 'index' },
      scales: {
        y: {
          grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
          ticks: { suggestedMin: 0, suggestedMax: 500, beginAtZero: true, padding: 10, font: { size: 14, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 }, color: "#fff" },
        },
        x: {
          grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
          ticks: { display: true, color: '#f8f9fa', padding: 10, font: { size: 14, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 }, }
        },
      },
    },
  });

  var ctx2 = document.getElementById("chart-line").getContext("2d");
  new Chart(ctx2, {
    type: "line",
    data: {
      labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Loan Amount",
        tension: 0,
        borderWidth: 0,
        pointRadius: 5,
        pointBackgroundColor: "rgba(255, 255, 255, .8)",
        pointBorderColor: "transparent",
        borderColor: "rgba(255, 255, 255, .8)",
        borderWidth: 4,
        backgroundColor: "transparent",
        fill: true,
        data: [50, 40, 300, 320, 500, 350, 200, 230, 500], // ข้อมูลสมมติ
        maxBarThickness: 6
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      interaction: { intersect: false, mode: 'index' },
      scales: {
        y: {
          grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5], color: 'rgba(255, 255, 255, .2)' },
          ticks: { display: true, color: '#f8f9fa', padding: 10, font: { size: 14, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 }, }
        },
        x: {
          grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false, borderDash: [5, 5] },
          ticks: { display: true, color: '#f8f9fa', padding: 10, font: { size: 14, weight: 300, family: "Roboto", style: 'normal', lineHeight: 2 }, }
        },
      },
    },
  });
</script>