<nav id="sidebar">
    
    <div class="p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <img src="../assets/img/m.png" alt="user" class="rounded-circle border border-secondary" width="40">
            </div>
            <div class="flex-grow-1 ms-2 overflow-hidden">
                <h6 class="mb-0 text-white text-truncate"><?php echo $_SESSION['mem_name']; ?></h6>
                <small>
                    <a href="#" class="text-info text-decoration-none" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-edit"></i> แก้ไขส่วนตัว
                    </a>
                </small>
            </div>
        </div>
    </div>

    <ul class="list-unstyled components">
        
        <?php if ($_SESSION["mem_status"] == 0) { ?>
            
            <li class="px-3 pb-1 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">เมนูหลัก</li>
            
            <li class="<?php echo ($menu == 'index') ? 'active' : ''; ?>">
                <a href="index.php"><i class="fas fa-home me-2"></i> หน้าแรก</a>
            </li>
            <li class="<?php echo ($menu == 'member') ? 'active' : ''; ?>">
                <a href="list_mem.php"><i class="fas fa-users me-2"></i> สมาชิก</a>
            </li>
            <li class="<?php echo ($menu == 'borrow_request') ? 'active' : ''; ?>">
                <a href="borrow_request.php"><i class="fas fa-file-invoice-dollar me-2"></i> คำขอกู้</a>
            </li>
            <li class="<?php echo ($menu == 'mustpay') ? 'active' : ''; ?>">
                <a href="mustpay.php"><i class="fas fa-hand-holding-usd me-2"></i> รายการค้างชำระ</a>
            </li>
            <li class="<?php echo ($menu == 'system') ? 'active' : ''; ?>">
                <a href="system.php"><i class="fas fa-cogs me-2"></i> ตั้งค่าระบบ</a>
            </li>

            <li class="px-3 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">รายงานสรุป</li>
            
            <li class="<?php echo ($menu == 'report_d') ? 'active' : ''; ?>">
                <a href="report_d.php"><i class="fas fa-chart-bar me-2 text-info"></i> ยอดขายรายวัน</a>
            </li>
            <li class="<?php echo ($menu == 'report_m') ? 'active' : ''; ?>">
                <a href="report_m.php"><i class="fas fa-chart-pie me-2 text-warning"></i> ยอดขายรายเดือน</a>
            </li>
            <li class="<?php echo ($menu == 'report_y') ? 'active' : ''; ?>">
                <a href="report_y.php"><i class="fas fa-chart-line me-2 text-success"></i> ยอดขายรายปี</a>
            </li>
            <li class="<?php echo ($menu == 'report_top10') ? 'active' : ''; ?>">
                <a href="report_top10_product.php"><i class="fas fa-crown me-2 text-primary"></i> สินค้าขายดี</a>
            </li>
            <li class="<?php echo ($menu == 'report_profit') ? 'active' : ''; ?>">
                <a href="#"><i class="fab fa-btc me-2 text-secondary"></i> กำไร/ขาดทุน</a>
            </li>
            <li class="<?php echo ($menu == 'report_credit') ? 'active' : ''; ?>">
                <a href="report_credit.php"><i class="fas fa-credit-card me-2 text-light"></i> สินเชื่อ</a>
            </li>

        <?php } else { ?>
            
            <li class="px-3 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">เมนูสมาชิก</li>
            
            <li class="<?php echo ($menu == 'borrow') ? 'active' : ''; ?>">
                <a href="borrow.php"><i class="fas fa-hand-holding-usd me-2"></i> ยื่นเรื่องกู้</a>
            </li>
            <li class="<?php echo ($menu == 'history') ? 'active' : ''; ?>">
                <a href="history.php"><i class="fas fa-history me-2"></i> ประวัติการกู้</a>
            </li>

        <?php } ?>

        <li class="mt-4 border-top border-secondary pt-2">
            <a href="../logout.php" class="text-danger" onclick="return confirm('ยืนยันการออกจากระบบ?');">
                <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
            </a>
        </li>
    </ul>
</nav>

<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <?php 
        // ดึงข้อมูลสมาชิก (ควรตรวจสอบว่า $condb ถูก include มาแล้วจาก header)
        $mem_id = $_SESSION["mem_id"];
        // เพิ่มการเช็ค error เพื่อความปลอดภัย
        if(isset($condb)){
            $query_member = "SELECT * FROM member WHERE mem_id='$mem_id'";
            $rs_member = mysqli_query($condb, $query_member);
            $row_member = mysqli_fetch_array($rs_member, MYSQLI_ASSOC);
        }
      ?>
      
      <form action="member_db.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="member" value="edit_profile">
        
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-edit"></i> แก้ไขข้อมูลส่วนตัว</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body text-dark"> <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">ระดับผู้ใช้งาน</label>
            <div class="col-sm-9">
              <input type="text" class="form-control-plaintext" value="<?php echo ($row_member['mem_status']==0)?'ผู้ดูแลระบบ':'สมาชิก'; ?>" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="mem_name" value="<?php echo $row_member['mem_name']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">รหัสบัตรประชาชน</label>
            <div class="col-sm-9">
              <input type="text" class="form-control bg-light" name="mem_id" value="<?php echo $row_member['mem_id']; ?>" readonly>
              <small class="text-muted">* ไม่สามารถแก้ไขได้</small>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">รหัสผ่านใหม่</label>
            <div class="col-sm-9">
              <input type="hidden" name="mem_password" value="<?php echo $row_member['mem_password']; ?>">
              <input type="password" class="form-control" name="mem_password_new" autocomplete="off" placeholder="กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน">
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">เบอร์โทรศัพท์</label>
            <div class="col-sm-9">
              <input type="tel" name="mem_phone" class="form-control" pattern="[0-9]{10}" value="<?php echo $row_member['mem_phone']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label">ที่อยู่</label>
            <div class="col-sm-9">
              <textarea name="mem_address" class="form-control" rows="3"><?php echo $row_member['mem_address']; ?></textarea>
            </div>
          </div>

        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง</button>
        </div>
      
      </form>
    </div>
  </div>
</div>