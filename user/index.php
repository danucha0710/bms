<?php 
session_start();

// ตรวจสอบว่ามี Session (ล็อกอินแล้วหรือยัง)
if (!isset($_SESSION['mem_id'])) {
    header("Location: ../login.php");
    exit();
}

// ตรวจสอบว่าเป็น mem_status = 2 หรือ 3 เท่านั้น
if ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3') {
    header("Location: ../login.php");
    exit();
}

$menu = "index";
include('../includes/header.php');
?>

<section class="content">
  <div class="container-fluid py-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center py-5">
            <div class="mb-4">
              <i class="fas fa-tools fa-5x text-muted mb-4"></i>
            </div>
            <h2 class="text-muted mb-3">Coming Soon...</h2>
            <p class="text-muted mb-4">ระบบกำลังอยู่ในระหว่างการพัฒนา</p>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> 
              ระบบสำหรับสมาชิก (ครู/เจ้าหน้าที่) กำลังอยู่ในระหว่างการพัฒนา
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
