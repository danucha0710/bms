<nav id="sidebar">
    <div class="p-3 border-bottom border-secondary">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                    <i class="fas fa-user-circle text-secondary" style="font-size: 35px;"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-2 overflow-hidden">
                <h6 class="mb-0 text-white text-truncate"><?php echo $_SESSION['mem_name']; ?></h6>
                <small>
                    <a href="#" class="text-info text-decoration-none" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-user-edit"></i> แก้ไขข้อมูลส่วนตัว
                    </a>
                </small>
            </div>
        </div>
    </div>

    <ul class="list-unstyled components">
        
        <?php if ($_SESSION["mem_status"] == 0 || $_SESSION["mem_status"] == 1) { ?>
            
            <li class="px-3 pb-1 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Management</li>
            
            <li class="<?php echo ($menu == 'index') ? 'active' : ''; ?>">
                <a href="index.php"><i class="fas fa-chart-line me-2"></i> แผงควบคุม</a>
            </li>
            <li class="<?php echo ($menu == 'member') ? 'active' : ''; ?>">
                <a href="list_mem.php"><i class="fas fa-users me-2"></i> จัดการสมาชิก</a>
            </li>
            <li class="<?php echo ($menu == 'borrow_request') ? 'active' : ''; ?>">
                <a href="borrow_request.php"><i class="fas fa-file-signature me-2"></i> รายการคำขอกู้</a>
            </li>
            <li class="<?php echo ($menu == 'mustpay') ? 'active' : ''; ?>">
                <a href="mustpay.php"><i class="fas fa-calendar-check me-2"></i> รายการค้างชำระ</a>
            </li>

            <?php if ($_SESSION["mem_status"] == 0) { ?>
            <li class="<?php echo ($menu == 'system') ? 'active' : ''; ?>">
                <a href="system.php"><i class="fas fa-cogs me-2 text-warning"></i> ตั้งค่าระบบ</a>
            </li>
            <?php } ?>

            <li class="px-3 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Reports</li>
            
            <li class="<?php echo ($menu == 'report_d') ? 'active' : ''; ?>">
                <a href="report_d.php"><i class="fas fa-calendar-day me-2 text-info"></i> รายงานรายวัน</a>
            </li>
            <li class="<?php echo ($menu == 'report_m') ? 'active' : ''; ?>">
                <a href="report_m.php"><i class="fas fa-calendar-alt me-2 text-warning"></i> รายงานรายเดือน</a>
            </li>
            <li class="<?php echo ($menu == 'report_y') ? 'active' : ''; ?>">
                <a href="report_y.php"><i class="fas fa-table me-2 text-success"></i> รายงานรายปี</a>
            </li>

        <?php } else { ?>
            
            <li class="px-3 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Member Menu</li>
            
            <li class="<?php echo ($menu == 'borrow') ? 'active' : ''; ?>">
                <a href="borrow.php"><i class="fas fa-hand-holding-usd me-2"></i> ยื่นเรื่องขอกู้เงิน</a>
            </li>
            <li class="<?php echo ($menu == 'history') ? 'active' : ''; ?>">
                <a href="history.php"><i class="fas fa-history me-2"></i> ประวัติการทำรายการ</a>
            </li>

        <?php } ?>

        <li class="mt-4 border-top border-secondary pt-2">
            <a href="../logout.php" class="text-danger" onclick="return confirm('คุณต้องการออกจากระบบใช่หรือไม่?');">
                <i class="fas fa-power-off me-2"></i> ออกจากระบบ
            </a>
        </li>
    </ul>
</nav>

<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      
      <?php 
        $mem_id = $_SESSION["mem_id"];
        if(isset($condb)){
            $query_member = "SELECT * FROM member WHERE mem_id='$mem_id'";
            $rs_member = mysqli_query($condb, $query_member);
            $row_member = mysqli_fetch_array($rs_member, MYSQLI_ASSOC);
        }

        // ฟังก์ชันช่วยแสดงชื่อระดับใน Modal
        function getLevelName($lv) {
            $levels = [0=>'ผู้ดูแลระบบ', 1=>'จนท.คีย์ข้อมูล', 2=>'ครู', 3=>'เจ้าหน้าที่'];
            return isset($levels[$lv]) ? $levels[$lv] : 'สมาชิก';
        }
      ?>
      
      <form action="member_db.php" method="POST">
        <input type="hidden" name="member" value="edit_profile">
        <input type="hidden" name="mem_id" value="<?php echo $row_member['mem_id']; ?>">
        
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i> ข้อมูลโปรไฟล์ส่วนตัว</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        
        <div class="modal-body p-4 text-dark">
          <div class="row mb-3 align-items-center">
            <label class="col-sm-3 col-form-label fw-bold">ระดับผู้ใช้งาน</label>
            <div class="col-sm-9">
              <span class="badge bg-info text-dark fs-6"><?php echo getLevelName($row_member['mem_status']); ?></span>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">ชื่อ-นามสกุล</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="mem_name" value="<?php echo $row_member['mem_name']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">เลขบัตรประชาชน</label>
            <div class="col-sm-9">
              <input type="text" class="form-control bg-light" value="<?php echo $row_member['mem_id']; ?>" readonly>
              <small class="text-muted">* ข้อมูลนี้ใช้เป็นรหัสล็อกอินหลัก ไม่สามารถแก้ไขได้</small>
            </div>
          </div>

          <hr class="my-4">
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">ชื่อผู้ใช้งาน (Username)</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="mem_username" value="<?php echo isset($row_member['mem_username']) ? $row_member['mem_username'] : ''; ?>" required placeholder="กำหนดชื่อผู้ใช้งานสำหรับ Login">
              <small class="text-muted text-danger">* ห้ามใช้ซ้ำกับสมาชิกท่านอื่น</small>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">รหัสผ่านใหม่</label>
            <div class="col-sm-9">
              <h6 class="text-primary mb-3"><i class="fas fa-key"></i> เปลี่ยนรหัสผ่านใหม่ (หากไม่เปลี่ยนให้เว้นว่างไว้)</h6>
              <input type="password" class="form-control" name="mem_password_new" autocomplete="new-password" placeholder="ตั้งรหัสผ่านใหม่ที่นี่">
            </div>
          </div>

          <hr class="my-4">

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">เบอร์โทรศัพท์</label>
            <div class="col-sm-9">
              <input type="tel" name="mem_phone" class="form-control" pattern="[0-9]{10}" value="<?php echo $row_member['mem_phone']; ?>" required>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-3 col-form-label fw-bold">ที่อยู่</label>
            <div class="col-sm-9">
              <textarea name="mem_address" class="form-control" rows="3"><?php echo $row_member['mem_address']; ?></textarea>
            </div>
          </div>
        </div>
        
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-1"></i> บันทึกข้อมูล</button>
        </div>
      </form>
    </div>
  </div>
</div>