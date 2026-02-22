<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "member";
include('../includes/header.php');

$query_member = "SELECT * FROM member ORDER BY mem_register_date DESC";
$rs_member = mysqli_query($condb, $query_member) or die("Error : ".mysqli_error($condb));

function getStatusName($status_id) {
    switch ($status_id) {
        case '0': return '<span class="badge bg-danger">ผู้ดูแลระบบ</span>';
        case '1': return '<span class="badge bg-primary">จนท.คีย์ข้อมูล</span>';
        case '2': return '<span class="badge bg-warning text-dark">ครู</span>';
        case '3': return '<span class="badge bg-info text-dark">เจ้าหน้าที่</span>';
        case '4': return '<span class="badge bg-secondary">สมาชิกทั่วไป</span>';
        default:  return '<span class="badge bg-light text-dark">ไม่ระบุ</span>';
    }
}
?>
    <section class="content mt-4">
      <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title m-0">
                    <i class="fas fa-users"></i> รายการสมาชิกทั้งหมด
                </h3>
                <button type="button" class="btn btn-light btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus"></i> เพิ่มสมาชิกใหม่
                </button>
            </div>
        
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableSearch" class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">ลำดับ</th>
                                <th width="15%" class="text-center">Username</th>
                                <th width="20%" class="text-center">ชื่อ-นามสกุล</th>
                                <th width="10%" class="text-center">เบอร์โทร</th>
                                <th width="10%" class="text-center">สถานะ</th>
                                <th width="12%" class="text-center">วงเงินสามัญ</th>
                                <th width="12%" class="text-center">วงเงินฉุกเฉิน</th>
                                <th width="10%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $i = 1; 
                        foreach ($rs_member as $row) { 
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-primary fw-bold"><?php echo htmlspecialchars($row['mem_username']); ?></td>
                                <td><?php echo htmlspecialchars($row['mem_name']); ?></td>
                                <td class="text-start"><?php echo htmlspecialchars($row['mem_phone']); ?></td>
                                <td class="text-start">
                                    <?php echo getStatusName($row['mem_status']); ?>
                                </td>
                                <td class="text-end"><?php echo number_format($row['mem_common_credit']); ?></td>
                                <td class="text-end"><?php echo number_format($row['mem_emergency_credit']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['mem_id'];?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="member_db.php?mem_id=<?php echo htmlspecialchars($row['mem_id'], ENT_QUOTES); ?>&member=del" class="btn btn-danger btn-sm" onclick="return confirm('ต้องการลบข้อมูล <?php echo htmlspecialchars($row['mem_name'], ENT_QUOTES); ?> ใช่หรือไม่?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="member_db.php" method="POST">
            <input type="hidden" name="member" value="add">
            
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title"><i class="fas fa-user-plus"></i> เพิ่มสมาชิกใหม่</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ระดับการใช้งาน</label>
                <div class="col-sm-9">
                  <select class="form-select" name="mem_status" required>
                    <option value="" selected disabled>-- กรุณาเลือก --</option>
                    <?php if($_SESSION['mem_status'] == '0'){ ?>
                        <option value="0">ผู้ดูแลระบบ (Admin)</option>
                    <?php } ?>
                    <option value="1">พนักงานคีย์ข้อมูล</option>
                    <option value="2">ครู</option>
                    <option value="3">เจ้าหน้าที่</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ชื่อผู้ใช้งาน (Username)</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_username" required placeholder="ใช้สำหรับ Login">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_name" required placeholder="นาย/นาง/นางสาว...">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">เลขบัตรประชาชน</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_id" pattern="[0-9]{13}" maxlength="13" required placeholder="เลข 13 หลัก">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">รหัสผ่านเริ่มต้น</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" name="mem_password" required placeholder="กำหนดรหัสผ่าน">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">เบอร์โทรศัพท์</label>
                <div class="col-sm-9">
                  <input type="tel" name="mem_phone" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ที่อยู่</label>
                <div class="col-sm-9">
                  <textarea name="mem_address" class="form-control" rows="2"></textarea>
                </div>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
              <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกข้อมูล</button>
            </div>
          </form>
        </div>
      </div>
    </div> 

    <?php 
    mysqli_data_seek($rs_member, 0);
    foreach ($rs_member as $row) { 
    ?>
    <div class="modal fade" id="editModal<?php echo $row['mem_id'];?>" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="member_db.php" method="POST">
            <input type="hidden" name="member" value="edit">
            <input type="hidden" name="mem_id_old" value="<?php echo htmlspecialchars($row['mem_id']); ?>">
            
            <div class="modal-header bg-warning">
              <h5 class="modal-title"><i class="fas fa-edit"></i> แก้ไขข้อมูล: <?php echo htmlspecialchars($row['mem_name']); ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
               <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ระดับการใช้งาน</label>
                <div class="col-sm-9">
                  <select class="form-select" name="mem_status" required>
                    <?php if($_SESSION['mem_status'] == '0'){ ?>
                        <option value="0" <?php echo ($row['mem_status']=='0')?'selected':''; ?>>ผู้ดูแลระบบ</option>
                    <?php } ?>
                    <option value="1" <?php echo ($row['mem_status']=='1')?'selected':''; ?>>พนักงานคีย์ข้อมูล</option>
                    <option value="2" <?php echo ($row['mem_status']=='2')?'selected':''; ?>>ครู</option>
                    <option value="3" <?php echo ($row['mem_status']=='3')?'selected':''; ?>>เจ้าหน้าที่</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_username" value="<?php echo htmlspecialchars($row['mem_username']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_name" value="<?php echo htmlspecialchars($row['mem_name']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">เลขบัตรประชาชน</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control bg-light" name="mem_id" value="<?php echo htmlspecialchars($row['mem_id']); ?>" readonly>
                    <small class="text-muted">* เลขบัตรประชาชนไม่สามารถแก้ไขได้</small>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">รหัสผ่านใหม่</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="mem_password_new" placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">เบอร์โทรศัพท์</label>
                <div class="col-sm-9">
                  <input type="tel" name="mem_phone" class="form-control" value="<?php echo htmlspecialchars($row['mem_phone']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ที่อยู่</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="mem_address" rows="2"><?php echo htmlspecialchars($row['mem_address']); ?></textarea>
                </div>
              </div>
              
              <hr>
              <h6 class="text-primary fw-bold">ตั้งค่าวงเงินกู้ส่วนบุคคล</h6>
              <div class="row mb-3">
                 <div class="col-md-6">
                    <label class="form-label small">วงเงินกู้สามัญ (บาท)</label>
                    <input type="number" class="form-control" name="common_credit" value="<?php echo $row['mem_common_credit']; ?>">
                 </div>
                 <div class="col-md-6">
                    <label class="form-label small">วงเงินกู้ฉุกเฉิน (บาท)</label>
                    <input type="number" class="form-control" name="emergency_credit" value="<?php echo $row['mem_emergency_credit']; ?>">
                 </div>
              </div>

            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
              <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> บันทึกการแก้ไข</button>
            </div>
          </form>
        </div>
      </div>
    </div> 
    <?php } ?>

<?php include('../includes/footer.php'); ?>