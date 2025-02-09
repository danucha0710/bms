<?php 
$menu = "borrow_request";
include("header.php");
?>

<?php 
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit();

if (!empty($_POST['date_s']) AND !empty($_POST['date_e'])) {
  $date_s = mysqli_real_escape_string($condb, $_POST['date_s']);
  $date_e = mysqli_real_escape_string($condb, $_POST['date_e']);
  $dateText = "borrow_request.br_date_request BETWEEN '$date_s' AND '$date_e'";
}
else {
  $dateText = "1";
}

$query = "SELECT * FROM borrow_request
INNER JOIN member ON borrow_request.mem_id = member.mem_id
WHERE $dateText
ORDER By br_id DESC";
$result = mysqli_query($condb, $query);
if (!$result) {
  die("Query failed: " . mysqli_error($condb));
}
$rowcount = mysqli_num_rows($result);

$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);

$st_max_amount_common = $row_system['st_max_amount_common'];
$st_max_amount_emergency = $row_system['st_max_amount_emergency'];
$st_amount_cost_teacher = $row_system['st_amount_cost_teacher'];
$st_amount_cost_officer = $row_system['st_amount_cost_officer'];
$st_max_months_common = $row_system['st_max_months_common'];
$st_max_months_emergency = $row_system['st_max_months_emergency'];
$st_interest = $row_system['st_interest'];
$st_stock_price = $row_system['st_stock_price'];
$st_dividend_rate = $row_system['st_dividend_rate'];
$st_average_return_rate = $row_system['st_average_return_rate'];
$st_dateline = $row_system['st_dateline'];
?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <h1>Request Borrow</h1>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card card-gray">
        <div class="card-header ">
          <h3 class="card-title">รายการคำขอกู้</h3>
          <div align="right">
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-search"></i> ค้นหา ตามช่วงเวลา</button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div align="right">
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-edit"></i> เพิ่มคำขอกู้</button>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <table class="table table-bordered table-hover table-striped" id="tableSearch">
                <thead>
                  <tr>
                    <th width="8%">หมายเลขคำขอกู้</th>
                    <th width="15%">ชื่อ-นามสกุล</th>
                    <th width="10%">ประเภทเงินกู้</th>
                    <th width="10%">จำนวนเงินกู้</th>
                    <th width="10%">สถานะคำขอกู้</th>
                    <th width="8%">ตรวจสอบข้อมูล</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    if($rowcount > 0) {
                      foreach($result as $value){ ?>
                        <tr>
                          <td><?php echo $value['br_id']; ?></td>
                          <td><?php echo $value['mem_name']; ?></td>
                          <td><?php include('borrow_type.php'); ?></td>
                          <td><?php echo $value['br_amount']; ?></td>
                          <td><?php include('borrow_status.php'); ?></td>
                          <td align="center"><a href="approve.php?br_id=<?php echo $value['br_id']; ?>" class="btn btn-warning"><i class="fas fa-eye"></i> details</a></td>
                        </tr>
                  <?php } } ?> 
                </tbody>
              </table> 
            </div>   
          </div>
        </div>
      </div>       
    </section>
    <!-- /.content -->

    <!-- The exampleModal Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <form action="borrow_request.php" method="POST" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-header bg-gray">
              <h5 class="modal-title" id="exampleModalLabel">ค้นหาตามช่วงเวลา</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <h6>ค้นหาแบบช่วงวัน</h6>
              </div>  
              <div class="row mt-3">
                <div class="col-md-6">
                  <label for="date_s">วันที่เริ่มต้น</label>
                  <input type="date" class="form-control" id="date_s" name="date_s">
                </div>
                <div class="col-md-6">
                  <label for="date_e">วันที่สิ้นสุด</label>
                  <input type="date" class="form-control" id="date_e" name="date_e">
                </div>
              </div> 
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ยืนยัน</button>
            </div>
          </div>
        </form>
      </div>
    </div> 

    <!-- The addModal Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="borrow_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="borrow" value="add">
            <input type="hidden" name="by_id" value="<?php echo $_SESSION["mem_id"]; ?>">
            <div class="modal-header bg-gray">
              <h5 class="modal-title">เพิ่มคำขอกู้เงิน</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">ชื่อ-นามสกุล ผู้ขอกู้</label>
                <div class="col-sm-8">
                  <select class="form-select" name="mem_id" id="mem_id" required>
                    <option value="">-- ค้นหาสมาชิก --</option>
                    <?php
                      $query_mem = "SELECT mem_id, mem_name FROM member";
                      $rs_mem = mysqli_query($condb, $query_mem); 
                      foreach ($rs_mem as $row_mem) { ?>
                      <option value="<?php echo $row_mem['mem_id'];?>"><?php echo $row_mem['mem_name'];?></option>
                      <?php } ?> 
                  </select>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">ประเภทเงินกู้</label>
                <div class="col-sm-8">
                  <select class="form-select" name="br_type" id="br_type" required>
                    <option value="">-- กรุณาเลือก --</option>
                    <option value="1">เงินกู้สามัญ</option>
                    <option value="2">เงินกู้ฉุกเฉิน</option>
                  </select>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">จำนวนเงินที่ต้องการกู้</label>
                <div class="col-sm-8">
                  <input style="display: none" type="number" class="form-control" name="br_amount_common" id="br_amount_common" min="1" max="<?php echo $st_max_amount_common; ?>" placeholder="สูงสุด <?php echo $st_max_amount_common; ?> บาท">
                  <input style="display: none" type="number" class="form-control" name="br_amount_emergency" id="br_amount_emergency" min="1" max="<?php echo $st_max_amount_emergency; ?>" placeholder="สูงสุด <?php echo $st_max_amount_emergency; ?> บาท">
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">จำนวนเดือนที่ต้องการผ่อนชำระ</label>
                <div class="col-sm-8">
                  <input style="display: none" type="number" class="form-control" name="br_months_pay_common" id="br_months_pay_common" min="1" max="<?php echo $st_max_months_common; ?>" placeholder="สูงสุด <?php echo $st_max_months_common; ?> เดือน">
                  <input style="display: none" type="number" class="form-control" name="br_months_pay_emergency" id="br_months_pay_emergency" min="1" max="<?php echo $st_max_months_emergency; ?>" placeholder="สูงสุด <?php echo $st_max_months_emergency; ?> เดือน">
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">ประเภทการค้ำประกัน</label>
                <div class="col-sm-8">
                  <select class="form-select" name="guarantee_type" id="guarantee_type" required>
                    <option value="">-- กรุณาเลือก --</option>
                    <option value="1">ค้ำประกันด้วยบุคคล</option>
                    <option value="2">ค้ำประกันด้วยจำนวนหุ้น</option>
                  </select>
                </div>
              </div>
              <div class="row mt-3" id="guarantor_fields" style="display: none;">
                <label class="col-sm-4 col-form-label" id="guarantor_1_label">ชื่อผู้ค้ำประกัน 1</label>
                <div class="col-sm-8">
                  <select class="form-select" name="guarantor_1" id="guarantor_1">
                    <option value="">-- ค้นหาสมาชิก --</option>
                    <?php
                      $query_mem = "SELECT mem_id, mem_name FROM member";
                      $rs_mem = mysqli_query($condb, $query_mem); 
                      foreach ($rs_mem as $row_mem) { ?>
                    <option value="<?php echo $row_mem['mem_name'];?>"><?php echo $row_mem['mem_name'];?></option>
                    <?php } ?> 
                  </select>
                </div>
                <label class="col-sm-4 col-form-label" id="guarantor_2_label">ชื่อผู้ค้ำประกัน 2</label>
                <div class="col-sm-8">
                  <select class="form-select" name="guarantor_2" id="guarantor_2">
                    <option value="">-- ค้นหาสมาชิก --</option>
                    <?php
                      $query_mem = "SELECT mem_id, mem_name FROM member";
                      $rs_mem = mysqli_query($condb, $query_mem); 
                      foreach ($rs_mem as $row_mem) { ?>
                    <option value="<?php echo $row_mem['mem_name'];?>"><?php echo $row_mem['mem_name'];?></option>
                    <?php } ?> 
                  </select>
                </div>
              </div>
              <div class="row mt-3 align-items-center" id="check_stock" style="display: none;">
                <label class="col-sm-4 col-form-label">&nbsp;</label>
                <div class="col-sm-8">
                  <button type="button" id="check" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkModal"><i class="fa fa-search"></i> ตรวจสอบจำนวนหุ้น</button>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-4 col-form-label">รายละเอียดคำขอกู้</label>
                <div class="col-sm-8">
                  <textarea class="form-control" name="br_details" row="3" required></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ยืนยัน</button>
            </div>
          </form>
        </div>
      </div>
    </div> 

    <!-- The checkModal Modal -->
    <div class="modal fade" id="checkModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-gray">
            <h5 class="modal-title">ตรวจสอบจำนวนหุ้น</h5>
            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <table class="table table-hover table-striped" id="tableSearch">
                  <thead>
                    <tr>
                      <th width="10%">ชื่อ-นามสกุล</th>
                      <th width="10%">หมายเลขโทรศัพท์</th>
                      <th width="18%">จำนวนหุ้น (บาท)</th>
                      <th width="15%">จำนวนเงินกู้สามัญ (บาท)</th>
                      <th width="15%">จำนวนเงินกู้ฉุกเฉิน (บาท)</th>
                    </tr>
                  </thead>
                  <tbody id="memberStockData">
                    <tr><td colspan="5" class="text-center">กรุณาเลือกสมาชิกและกดปุ่มตรวจสอบ</td></tr>
                  </tbody>
                </table> 
              </div>   
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
          </div>
          </form>
        </div>
      </div>
    </div> 

<?php 
  mysqli_free_result($result);
  mysqli_close($condb);
  include('footer2.php'); 
?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var select = document.querySelector('#mem_id');
    if (select) {
        dselect(select, { search: true });
    }
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var brType = document.getElementById("br_type");
    var amountCommon = document.getElementById("br_amount_common");
    var amountEmergency = document.getElementById("br_amount_emergency");
    var payCommon = document.getElementById("br_months_pay_common");
    var payEmergency = document.getElementById("br_months_pay_emergency");

    brType.addEventListener("change", function() {
      if (this.value == "1") {
        amountCommon.style.display = "block";     // แสดงฟิลด์เงินกู้สามัญ
        amountEmergency.style.display = "none";   // ซ่อนฟิลด์เงินกู้ฉุกเฉิน
        payCommon.style.display = "block";        // แสดงฟิลด์เดือนกู้สามัญ
        payEmergency.style.display = "none";      // ซ่อนฟิลด์เดือนกู้ฉุกเฉิน
      } 
      else if (this.value == "2") {
        amountCommon.style.display = "none";      // ซ่อนฟิลด์เงินกู้สามัญ
        amountEmergency.style.display = "block";  // แสดงฟิลด์เงินกู้ฉุกเฉิน
        payCommon.style.display = "none";         // ซ่อนฟิลด์เดือนกู้สามัญ
        payEmergency.style.display = "block";     // แสดงฟิลด์เดือนกู้ฉุกเฉิน
      }
      else {
        amountCommon.style.display = "none";      
        amountEmergency.style.display = "none";  
        payCommon.style.display = "none";        
        payEmergency.style.display = "none";    
      }
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var guaranteeType = document.getElementById("guarantee_type");
    var guarantorFields = document.getElementById("guarantor_fields");
    var checkStock = document.getElementById("check_stock");

    guaranteeType.addEventListener("change", function() {
      if (this.value == "1") {
        guarantorFields.style.display = "block";  // แสดงฟิลด์ผู้ค้ำประกัน
        checkStock.style.display = "none";        // ซ่อนฟิลด์ค้นหาจำนวนหุ้น
      } 
      else if (this.value == "2") {
        guarantorFields.style.display = "none";   // ซ่อนฟิลด์ผู้ค้ำประกัน
        checkStock.style.display = "block";       // แสดงฟิลด์ค้นหาจำนวนหุ้น
      }
      else {
        guarantorFields.style.display = "none";   
        checkStock.style.display = "none"; 
        //document.getElementById("guarantor_1").value = ""; // ล้างค่า
        //document.getElementById("guarantor_2").value = ""; // ล้างค่า
      }
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("check").addEventListener("click", function() {
      var selectedMemberId = document.getElementById("mem_id").value;
        if (!selectedMemberId) {
          alert("กรุณาเลือกสมาชิกก่อนตรวจสอบจำนวนหุ้น");
          return;
        }

        fetch("fetch_member_data.php?mem_id=" + selectedMemberId)
          .then(response => response.text())
          .then(data => {
            document.getElementById("memberStockData").innerHTML = data;
          })
          .catch(error => console.error('Error:', error));
    });
  });
</script>

</body>
</html>