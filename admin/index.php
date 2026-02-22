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

// 3.4 นับจำนวนรายการที่ต้องจ่าย (ค้างชำระ)
$sql_due = "SELECT COUNT(*) as total FROM borrowing WHERE bw_status = 0";
$rs_due = mysqli_query($condb, $sql_due);
$cnt_due = mysqli_fetch_assoc($rs_due)['total'];
?>

<section class="content">
  <div class="container-fluid py-4">
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
    
    <div class="row mt-5">
      <div class="col-lg-6 col-md-6 mt-4 mb-4">
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
              <p class="mb-0 text-sm"> ข้อมูลจำลอง (ยังไม่เชื่อม DB)</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 col-md-6 mt-4 mb-4">
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
              <p class="mb-0 text-sm"> ข้อมูลจำลอง (ยังไม่เชื่อม DB)</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include('../includes/footer.php'); ?>