<?php 
$menu = "approve";
include('../includes/header.php'); // แก้ Path ให้ถูกต้อง

// 1. รับค่าและดึงข้อมูลคำขอ
$br_id = mysqli_real_escape_string($condb, $_GET["br_id"]);
$query = "SELECT borrow_request.*, member.mem_name 
          FROM borrow_request
          INNER JOIN member ON borrow_request.mem_id = member.mem_id
          WHERE br_id = '$br_id'";
$result = mysqli_query($condb, $query) or die("Error : ".mysqli_error($condb));
$value = mysqli_fetch_array($result, MYSQLI_ASSOC);

// ถ้าไม่พบข้อมูล ให้ดีดกลับ
if(!$value){
    echo "<script>alert('ไม่พบข้อมูลคำขอนี้'); window.location='borrow_request.php';</script>";
    exit();
}

// 2. ดึงค่า Config ระบบ
$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);

// Helper Function แปลงประเภท
function getTypeText($type){
    return ($type == 1) ? "เงินกู้สามัญ" : "เงินกู้ฉุกเฉิน";
}
function getGuaranteeText($type){
    if($type == 1) return "ค้ำประกันด้วยบุคคล";
    if($type == 2) return "ค้ำประกันด้วยจำนวนหุ้น";
    return "-";
}
?>

<section class="content mt-4">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-10">
        
        <div class="card shadow-sm border-0">
          <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title m-0">
                <i class="fas fa-clipboard-check"></i> พิจารณาคำขอกู้ (ID: <?php echo $value['br_id']; ?>)
            </h4>
            <a href="borrow_request.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
          </div>
          
          <div class="card-body">
            <form action="borrow_db.php" method="POST">
              <input type="hidden" name="borrow" value="approve">
              <input type="hidden" name="by_id" value="<?php echo $_SESSION["mem_id"]; ?>">
              <input type="hidden" name="st_dateline" value="<?php echo $row_system['st_dateline']; ?>">
              <input type="hidden" name="mem_id" value="<?php echo $value['mem_id']; ?>">
              <input type="hidden" name="br_id" value="<?php echo $value['br_id']; ?>">
              <input type="hidden" name="br_type" value="<?php echo $value['br_type']; ?>">
              <input type="hidden" name="guarantee_type" value="<?php echo $value['guarantee_type']; ?>">

              <h6 class="text-primary border-bottom pb-2 mb-3">ข้อมูลผู้ขอกู้</h6>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label fw-bold">ชื่อ-นามสกุล</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control-plaintext" value="<?php echo $value['mem_name']; ?>" readonly>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label fw-bold">ประเภทเงินกู้</label>
                <div class="col-sm-9">
                   <span class="badge <?php echo ($value['br_type']==1)?'bg-primary':'bg-danger'; ?> fs-6">
                       <?php echo getTypeText($value['br_type']); ?>
                   </span>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">จำนวนเงินที่ขอกู้</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="br_amount" 
                            min="1" 
                            max="<?php echo ($value['br_type']==1) ? $row_system['st_max_amount_common'] : $row_system['st_max_amount_emergency']; ?>" 
                            value="<?php echo $value['br_amount']; ?>" 
                            <?php echo ($value['br_status']!=0)?'readonly':'required'; ?>>
                        <span class="input-group-text">บาท</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ระยะเวลาผ่อนชำระ</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="br_months_pay" 
                            min="1" 
                            max="<?php echo ($value['br_type']==1) ? $row_system['st_max_months_common'] : $row_system['st_max_months_emergency']; ?>" 
                            value="<?php echo $value['br_months_pay']; ?>" 
                            <?php echo ($value['br_status']!=0)?'readonly':'required'; ?>>
                        <span class="input-group-text">เดือน</span>
                    </div>
                </div>
              </div>

              <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">ข้อมูลหลักประกัน</h6>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label fw-bold">รูปแบบการค้ำประกัน</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control-plaintext" value="<?php echo getGuaranteeText($value['guarantee_type']); ?>" readonly>
                </div>
              </div>

              <?php if($value['guarantee_type'] == 1){ ?>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label fw-bold">ผู้ค้ำประกัน</label>
                <div class="col-sm-9">
                  <ul class="list-group">
                    <li class="list-group-item">1. <?php echo $value['guarantor_1']; ?></li>
                    <li class="list-group-item">2. <?php echo $value['guarantor_2']; ?></li>
                  </ul>
                </div>
              </div>
              <?php } ?>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label fw-bold">เหตุผลการกู้</label>
                <div class="col-sm-9">
                  <textarea class="form-control bg-light" rows="3" readonly><?php echo $value['br_details']; ?></textarea>
                </div>
              </div>

              <?php if($value['br_status'] == 0) { ?>
                  <div class="alert alert-warning mt-4">
                    <h5 class="alert-heading"><i class="fas fa-gavel"></i> ส่วนการพิจารณาอนุมัติ</h5>
                    <hr>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">ผลการพิจารณา</label>
                        <div class="col-sm-9">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="br_status" id="status_approve" value="1" onchange="toggleReason(1)" required>
                                <label class="btn btn-outline-success" for="status_approve">
                                    <i class="fas fa-check-circle"></i> อนุมัติ
                                </label>

                                <input type="radio" class="btn-check" name="br_status" id="status_reject" value="2" onchange="toggleReason(2)">
                                <label class="btn btn-outline-danger" for="status_reject">
                                    <i class="fas fa-times-circle"></i> ไม่อนุมัติ
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="approve_options" style="display:none;">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">อัตราดอกเบี้ย</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="br_interest_rate" id="interest_rate" value="<?php echo $row_system['st_interest']; ?>" step="0.01" min="0">
                                    <span class="input-group-text">% ต่อปี</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="reject_options" style="display:none;">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold text-danger">เหตุผลที่ไม่อนุมัติ</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="br_respond" id="reject_reason" rows="2" placeholder="ระบุเหตุผล..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-5" onclick="return confirm('ยืนยันผลการพิจารณา?');">
                            <i class="fas fa-save"></i> บันทึกผลการพิจารณา
                        </button>
                    </div>
                  </div>

              <?php } else { ?>
                  <div class="alert <?php echo ($value['br_status']==1)?'alert-success':'alert-danger'; ?> mt-4">
                    <h5 class="alert-heading">
                        <i class="fas <?php echo ($value['br_status']==1)?'fa-check-circle':'fa-times-circle'; ?>"></i> 
                        ผลการพิจารณา: <?php echo ($value['br_status']==1)?'อนุมัติ':'ไม่อนุมัติ'; ?>
                    </h5>
                    <hr>
                    <?php if($value['br_status'] == 1){ ?>
                        <p class="mb-0"><strong>อัตราดอกเบี้ย:</strong> <?php echo $value['br_interest_rate']; ?>% ต่อปี</p>
                    <?php } else { ?>
                        <p class="mb-0"><strong>เหตุผล:</strong> <?php echo $value['br_respond']; ?></p>
                    <?php } ?>
                  </div>
              <?php } ?>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>

<script>
function toggleReason(status) {
    const approveDiv = document.getElementById('approve_options');
    const rejectDiv = document.getElementById('reject_options');
    const interestInput = document.getElementById('interest_rate');
    const reasonInput = document.getElementById('reject_reason');

    if (status == 1) { // อนุมัติ
        approveDiv.style.display = 'block';
        rejectDiv.style.display = 'none';
        
        interestInput.required = true;
        reasonInput.required = false;
        reasonInput.value = ''; // ล้างค่าเหตุผล
    } else { // ไม่อนุมัติ
        approveDiv.style.display = 'none';
        rejectDiv.style.display = 'block';
        
        interestInput.required = false;
        reasonInput.required = true;
    }
}
</script>