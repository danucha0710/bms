<?php 
$menu = "system";
include("header.php");
?>

<?php 
$query = "SELECT * FROM `system` WHERE st_id = 1" or die("Error : ".mysqli_error($condb));
$result = mysqli_query($condb, $query);
$value = mysqli_fetch_array($result, MYSQLI_ASSOC);
?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <h1>System Setting</h1>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card card-gray">
        <div class="card-header ">
          <h3 class="card-title">ตั้งค่าระบบ</h3>  
        </div>
        <br>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <form action="system_db.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="system" value="setting">
                <input type="hidden" name="st_edit_by" value="<?php echo $_SESSION["mem_id"]; ?>">
                <div class="row">
                  <label class="col-sm-5 col-form-label">วงเงินกู้สูงสุด (เงินกู้สามัญ)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_max_amount_common" value="<?php echo $value['st_max_amount_common']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">วงเงินกู้สูงสุด (เงินกู้ฉุกเฉิน)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_max_amount_emergency" value="<?php echo $value['st_max_amount_emergency']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">จำนวนเงินต้น (ครู)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_amount_cost_teacher" value="<?php echo $value['st_amount_cost_teacher']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">จำนวนเงินต้น (เจ้าหน้าที่)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_amount_cost_officer" value="<?php echo $value['st_amount_cost_officer']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">จำนวนเดือนที่ผ่อนชำระสูงสุด (เงินกู้สามัญ)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_max_months_common" value="<?php echo $value['st_max_months_common']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">จำนวนเดือนที่ผ่อนชำระสูงสุด (เงินกู้ฉุกเฉิน)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_max_months_emergency" value="<?php echo $value['st_max_months_emergency']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">อัตราดอกเบี้ยต่อปี</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" max="100" step="0.01" name="st_interest" value="<?php echo $value['st_interest']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">ราคาหุ้น (บาท)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" step="1" name="st_stock_price" value="<?php echo $value['st_stock_price']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">เงินปันผล (%)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" max="100" step="0.01" name="st_dividend_rate" value="<?php echo $value['st_dividend_rate']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">เงินเฉลี่ยคืน (%)</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" max="100" step="0.01" name="st_average_return_rate" value="<?php echo $value['st_average_return_rate']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-5 col-form-label">วันที่สิ้นสุดชำระเงินรายเดือน</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" main="0" max="31" step="1" name="st_dateline" value="<?php echo $value['st_dateline']; ?>" required>
                  </div>
                </div>
                <div class="row mt-3">
                  <button type="submit" class="btn btn-info btn-block">ตั้งค่า</button>
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

<script>
$(document).ready(function(){
  $("#hide").click(function(){
    $("p").hide();
  });
  $("#show").click(function(){
    $("p").show();
  });
});
</script>

</body>
</html>