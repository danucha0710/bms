<?php 
$menu = "member";
include("header.php");
$query_member = "SELECT * FROM member" or die("Error : ".mysqli_error($condb));
$rs_member = mysqli_query($condb, $query_member);
?>

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <h1>Member</h1>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="card card-gray">
        <div class="card-header">
          <h3 class="card-title">รายการสมาชิก</h3>
          <div align="right">
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-plus"></i> เพิ่มข้อมูล สมาชิก</button>
          </div>
        </div>
        <br>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <table id="tableSearch" class="table table-bordered table-hover table-striped">
                <thead>
                  <tr class="danger">
                    <th width="1%"><center>ลำดับ</center></th>
                    <th width="15%"><center>ชื่อ-นามสกุล</center></th>
                    <th width="5%"><center>เบอร์โทร</center></th>
                    <th width="5%"><center>สถานะ</center></th>
                    <th width="10%"><center>วงเงินกู้สามัญ</center></th>
                    <th width="10%"><center>วงเงินกู้ฉุกเฉิน</center></th>
                    <th width="3%"><center>แก้ไข</center></th>
                    <th width="3%"><center>ลบ</center></th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($rs_member as $row_member) { ?>
                  <tr>
                    <td align="center"><?php echo $no+=1; ?></td>
                    <td><?php echo $row_member['mem_name']; ?></td>
                    <td><?php echo $row_member['mem_phone']; ?></td>
                    <td><?php include('mem_status.php'); ?></td>
                    <td align="right"><?php echo $row_member['common_credit']; ?></td>
                    <td align="right"><?php echo $row_member['emergency_credit']; ?></td>
                    <td align="center"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal<?php echo $row_member['mem_id'];?>"><i class="fas fa-pencil-alt"></i> edit</button></td>
                    <td align="center"><a href="member_db.php?mem_id=<?php echo $row_member['mem_id']; ?>" class="btn btn-danger" onclick="return confirm_alert(this);"><i class="fas fas fa-trash"></i> del</a></td>
                  </tr>
                <?php } ?>
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
        <div class="modal-content">
          <form action="member_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="member" value="add">
            <div class="modal-header bg-gray">
              <h5 class="modal-title">เพิ่มข้อมูล สมาชิก</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <label class="col-sm-3 col-form-label">ระดับการใช้งาน</label>
                <div class="col-sm-9">
                  <?php if($mem_status == 0) { ?>
                  <select class="form-select" name="mem_status" required>
                    <option value="">-- เลือกประเภท --</option>
                    <option value="0">ผู้ดูแลระบบ</option>
                    <option value="1">พนักงานคีย์ข้อมูล</option>
                    <option value="2">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                  </select>
                  <?php } else { ?>
                  <select class="form-select" name="mem_status" required>
                    <option value="">-- เลือกประเภท --</option>
                    <option value="1">พนักงานคีย์ข้อมูล</option>
                    <option value="2">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                  </select>
                  <?php } ?>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_name" required>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">หมายเลขบัตรประชาชน</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_id" pattern="[0-9]{13}" required>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">หมายเลขโทรศัพท์</label>
                <div class="col-sm-9">
                  <input type="tel" name="mem_phone" class="form-control" pattern="[0]{1}[0-9]{9}" required>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">ที่อยู่</label>
                <div class="col-sm-9">
                  <textarea name="mem_address" class="form-control" row="3" required></textarea>
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

    <?php foreach ($rs_member as $row_member) { ?>
    <!-- The myModal Modal -->
    <div class="modal fade" id="myModal<?php echo $row_member['mem_id'];?>" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="member_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="member" value="edit">
            <div class="modal-header bg-gray">
              <h5 class="modal-title">แก้ไขข้อมูล สมาชิก</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <label class="col-sm-3 col-form-label">ระดับการใช้งาน</label>
                <div class="col-sm-9">
                  <?php if($mem_status == 0) { ?>
                  <select class="form-select" name="mem_status" required>
                    <option value="">-- เลือกประเภท --</option>
                    <option value="0">ผู้ดูแลระบบ</option>
                    <option value="1">พนักงานคีย์ข้อมูล</option>
                    <option value="2">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                  </select>
                  <?php } else { ?>
                  <select class="form-select" name="mem_status" required>
                    <option value="">-- เลือกประเภท --</option>
                    <option value="1">พนักงานคีย์ข้อมูล</option>
                    <option value="2">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                  </select>
                  <?php } ?>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_name" value="<?php echo $row_member['mem_name']; ?>">
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">หมายเลขบัตรประชาชน</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_id" value="<?php echo $row_member['mem_id']; ?>" readonly>
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">รหัสผ่าน</label>
                <div class="col-sm-9">
                  <input type="hidden" name="mem_password" value="<?php echo $row_member['mem_password']; ?>">
                  <input type="text" class="form-control" name="mem_password_new" placeholder="ไม่ต้องการแก้ไข ไม่ต้องป้อนข้อมูล">
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">หมายเลขโทรศัพท์</label>
                <div class="col-sm-9">
                  <input type="tel" name="mem_phone" class="form-control" pattern="[0]{1}[0-9]{9}" value="<?php echo $row_member['mem_phone']; ?>">
                </div>
              </div>
              <div class="row mt-3">
                <label class="col-sm-3 col-form-label">ที่อยู่</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="mem_address" row="3"><?php echo $row_member['mem_address']; ?></textarea>
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
    <?php } ?>

<?php include('footer.php'); ?>

<script type="text/javascript">
  function confirm_alert(node) {
    return confirm("ต้องการลบข้อมูลใช่ไหม ?");
  }
</script>
  
</body>
</html>

