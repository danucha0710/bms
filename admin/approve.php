<?php 
$menu = "approve";
include("header.php");
?>

<?php 
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit();
$br_id = $_GET["br_id"];
$query = "SELECT * FROM borrow_request
INNER JOIN member ON borrow_request.mem_id = member.mem_id
WHERE br_id = $br_id" or die("Error : ".mysqli_error($condb));
$result = mysqli_query($condb, $query);
$rowcount = mysqli_num_rows($result);
if($rowcount > 0) {
  $value = mysqli_fetch_array($result, MYSQLI_ASSOC);
}

$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);

$st_max_amount_common = $row_system['st_max_amount_common'];
$st_max_amount_emergency = $row_system['st_max_amount_emergency'];
$st_max_months_common = $row_system['st_max_months_common'];
$st_max_months_emergency = $row_system['st_max_months_emergency'];
$st_interest = $row_system['st_interest'];
$st_dateline = $row_system['st_dateline'];
?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <h1>Approve Order</h1>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card card-gray">
        <div class="card-header ">
          <h3 class="card-title">พิจารณา คำขอกู้</h3>  
        </div>
        <br>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <form action="borrow_db.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="borrow" value="approve">
                <input type="hidden" name="by_id" value="<?php echo $_SESSION["mem_id"]; ?>">
                <input type="hidden" name="st_dateline" value="<?php echo $st_dateline; ?>">
                <input type="hidden" name="mem_id" value="<?php echo $value['mem_id']; ?>">
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">ชื่อ-นามสกุล ผู้ขอกู้</label>
                  <input type="hidden" name="br_id" value="<?php echo $value['br_id']; ?>">
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="mem_name" value="<?php echo $value['mem_name']; ?>" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">ประเภทเงินกู้</label>
                  <input type="hidden" name="br_type" value="<?php echo $value['br_type']; ?>">
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="br_type_text" value="<?php 
                      if($value['br_type'] == 1){
                        echo "เงินกู้สามัญ";
                      }
                      elseif($value['br_type'] == 2){
                        echo "เงินกู้ฉุกเฉิน";
                      }
                      ?>" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">จำนวนเงินที่ต้องการกู้</label>
                  <div class="col-sm-8">
                    <?php 
                      if($value['br_type'] == 1){
                    ?>
                        <input type="number" class="form-control" name="br_amount" min="1" max="<?php echo $st_max_amount_common; ?>" value="<?php echo $value['br_amount']; ?>" required>
                    <?php }
                      elseif($value['br_type'] == 2){
                    ?>
                        <input type="number" class="form-control" name="br_amount" min="1" max="<?php echo $st_max_amount_emergency; ?>" value="<?php echo $value['br_amount']; ?>" required>
                    <?php } ?>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">จำนวนเดือนที่ต้องการผ่อนชำระ</label>
                  <div class="col-sm-8">
                  <?php 
                      if($value['br_type'] == 1){
                    ?>
                        <input type="number" class="form-control" name="br_months_pay" min="1" max="<?php echo $st_max_months_common; ?>" value="<?php echo $value['br_months_pay']; ?>" required>
                    <?php }
                      elseif($value['br_type'] == 2){
                    ?>
                        <input type="number" class="form-control" name="br_months_pay" min="1" max="<?php echo $st_max_months_emergency; ?>" value="<?php echo $value['br_months_pay']; ?>" required>
                    <?php } ?>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">ประเภทการค้ำประกัน</label>
                  <input type="hidden" name="guarantee_type" value="<?php echo $value['guarantee_type']; ?>">
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="guarantee_type_text" value="<?php include 'guarantee_type.php'; ?>" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">ชื่อผู้ค้ำประกัน 1</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="guarantor_1" value="<?php echo $value['guarantor_1']; ?>" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">ชื่อผู้ค้ำประกัน 2</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="guarantor_2" value="<?php echo $value['guarantor_2']; ?>" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">รายละเอียดคำขอกู้</label>
                  <div class="col-sm-8">
                    <textarea class="form-control" name="br_details" row="3" readonly><?php echo $value['br_details']; ?></textarea>
                  </div>
                </div>
                <hr style="height:5px;border-width:0;color:red;background-color:red">
                <?php if($value['br_status'] == 0) { ?>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">สถานะคำขอ</label>
                  <div class="col-sm-8">
                    <select class="form-select" name="br_status" onchange="show1(this.value)" required>
                      <option value="">-- กรุณาเลือก --</option>
                      <option value="1">อนุมัติ</option>
                      <option value="2">ไม่อนุมัติ</option>
                    </select>
                  </div>
                </div>
                <div class="row mt-3">
                  <input type="hidden" name="br_approve_by" value="<?php echo $_SESSION["mem_id"]; ?>">
                  <label style="display: none" class="col-sm-4 col-form-label" id="br_interest_rate_label">อัตราดอกเบี้ย</label>
                  <div class="col-sm-8">
                    <input style="display: none" type="number" min="0" max="15" step="0.01" class="form-control" name="br_interest_rate" id="br_interest_rate" value="<?php echo $st_interest; ?>">
                  </div>
                </div>
                <div class="row mt-3">
                  <label style="display: none" class="col-sm-4 col-form-label" id="br_respond_label">เหตุผลที่ไม่อนุมัติ</label>
                  <div class="col-sm-8">
                    <textarea style="display: none" class="form-control" name="br_respond" id="br_respond" row="3"></textarea>
                  </div>
                </div>
                <?php } else { 
                  if($value['br_status'] == 1) { 
                ?>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">สถานะคำขอ</label>
                  <div class="col-sm-8">
                    <select class="form-select" name="br_status" disabled>
                      <option value="1" selected>อนุมัติ</option>
                      <option value="2">ไม่อนุมัติ</option>
                    </select>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-4 col-form-label">อัตราดอกเบี้ย</label>
                  <input type="hidden" name="br_approve_by" value="<?php echo $_SESSION["mem_id"]; ?>">
                  <div class="col-sm-8">
                    <input type="number" min="0" step="0.01" class="form-control" name="br_interest_rate" value="<?php echo $value['br_interest_rate']; ?>" readonly>
                  </div>
                </div>
                <?php } 
                  elseif($value['br_status'] == 2) { 
                ?>
                  <div class="row mt-3">
                    <label class="col-sm-4 col-form-label">สถานะคำขอ</label>
                    <div class="col-sm-8">
                      <select class="form-select" name="br_status" disabled>
                        <option value="1">อนุมัติ</option>
                        <option value="2" selected>ไม่อนุมัติ</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <label class="col-sm-4 col-form-label">เหตุผลที่ไม่อนุมัติ</label>
                    <input type="hidden" name="br_approve_by" value="<?php echo $_SESSION["mem_id"]; ?>">
                    <div class="col-sm-8">
                      <textarea class="form-control" name="br_respond" row="3" readonly><?php echo $value['br_respond']; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } } ?>
                <div class="row mt-3">
                  <?php 
                    if($value['br_status'] == 0) {
                  ?>
                  <button type="submit" class="btn btn-danger btn-block">ยืนยัน</button>
                  <?php } ?>
                </div>
              </form>   
            </div> 
          </div>
        </div>   
      </div>
    </section>
    <!-- /.content -->

<?php 
  mysqli_close($condb);
  include('footer2.php'); 
?>

<script>
  function show1(text) {
    if(text == 1) {
      document.getElementById("br_interest_rate_label").style.display = "inline";
      document.getElementById("br_interest_rate").style.display = "inline";
      document.getElementById("br_interest_rate").required = true; 
      document.getElementById("br_respond_label").style.display = "none";
      document.getElementById("br_respond").style.display = "none";
      document.getElementById("br_respond").required = false; 
    }
    else if(text == 2) {
      document.getElementById("br_interest_rate_label").style.display = "none";
      document.getElementById("br_interest_rate").style.display = "none";
      document.getElementById("br_interest_rate").required = false; 
      document.getElementById("br_respond_label").style.display = "inline";
      document.getElementById("br_respond").style.display = "inline";
      document.getElementById("br_respond").required = true; 
    }
  }
</script>

</body>
</html>