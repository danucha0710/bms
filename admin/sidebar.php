<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-gray elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link bg-gray">
      <img src="../bpcc_logo.png"
        alt="BPCC Logo"
        class="brand-image img-circle elevation-3"
        style="opacity: .8">
      <span class="brand-text font-weight-light">Borrow Money System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <p class="text-white"><?php echo $_SESSION['mem_name'];?> | 
            <button type="button" class="btn btn-link text-white" data-bs-toggle="modal" data-bs-target="#myModal">แก้ไขข้อมูลส่วนตัว</button>
          </p>
        </div>
      </div>

      <?php if ($_SESSION["mem_status"] == 0) {?>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <!-- nav-compact -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
            with font-awesome or any other icon font library -->
          <li class="nav-header">เมนู</li>
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php if($menu=="index"){echo "active";} ?> ">
              <i class="fas fa-home" style="font-size:20px"></i>
              <p>&nbsp;&nbsp;หน้าแรก</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="list_mem.php" class="nav-link <?php if($menu=="member"){echo "active";} ?> ">
              <i class="fas fa-user-alt" style="font-size:20px"></i>
              <p>&nbsp;&nbsp;สมาชิก</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="borrow_request.php" class="nav-link <?php if($menu=="borrow_request"){echo "active";} ?> ">
              <i class="fas fa-hand-holding-usd" style="font-size:20px"></i>
              <p>&nbsp;&nbsp;คำขอกู้</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="mustpay.php" class="nav-link <?php if($menu=="mustpay"){echo "active";} ?> ">
              <i class="fas fa-list" style="font-size:20px"></i>
              <p>&nbsp;&nbsp;ค้างชำระ</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="system.php" class="nav-link <?php if($menu=="system"){echo "active";} ?> ">
              <i class="fas fa-cog" style="font-size:20px"></i>
              <p>&nbsp;&nbsp;ตั้งค่าระบบ</p>
            </a>
          </li>
        </ul>
        <hr>

        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">รายงาน</li>
          <li class="nav-item">
            <a href="report_d.php" class="nav-link <?php if($menu=="report_d"){echo "active";} ?> ">
              <i class="nav-icon fas fa-chart-bar text-white"></i>
              <p>ยอดขายรายวัน</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report_m.php" class="nav-link <?php if($menu=="report_m"){echo "active";} ?> ">
              <i class="nav-icon fas fa-chart-bar text-warning"></i>
              <p>ยอดขายรายเดือน</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report_y.php" class="nav-link <?php if($menu=="report_y"){echo "active";} ?> ">
              <i class="nav-icon fas fa-chart-bar text-success"></i>
              <p>ยอดขายรายปี</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report_top10_product.php" class="nav-link <?php if($menu=="report_top10_product"){echo "active";} ?> ">
              <i class="nav-icon fas fa-chart-line text-primary"></i>
              <p>สินค้าขายดี</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link <?php if($menu=="report_profit_loss"){echo "active";} ?> ">
              <i class="nav-icon fab fa-btc text-secondary"></i>
              <p>กำไร/ขาดทุน</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report_credit.php" class="nav-link <?php if($menu=="report_credit"){echo "active";} ?> ">
              <i class="nav-icon fas fa-money-check text-info"></i>
              <p>สินเชื่อ</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report_purchase.php" class="nav-link <?php if($menu=="report_purchase"){echo "active";} ?> ">
              <i class="nav-icon fas fa-wallet text-white"></i>
              <p>ยอดซื้อ</p>
            </a>
          </li>
          <li class="nav-header"></li>
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-power-off"></i>
              <p>ออกจากระบบ</p>
            </a>
          </li>
        </ul>
      </nav>

      <?php }else{ ?>
      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <!-- nav-compact -->
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
            with font-awesome or any other icon font library -->
          <li class="nav-header">เมนู</li>
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-power-off"></i>
              <p>ออกจากระบบ</p>
            </a>
          </li>
        </ul>
        <hr>
      </nav>
      <!-- /.sidebar-menu -->
      <?php } ?>
    </div>
    <!-- /.sidebar -->
  </aside>

    <!-- The myModal Modal -->
    <div class="modal fade" id="myModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <?php 
            $mem_id = $_SESSION["mem_id"];
            $query_member = "SELECT * FROM member WHERE mem_id='$mem_id'" or die("Error : ".mysqli_error($condb));
            $rs_member = mysqli_query($condb, $query_member);
            $row_member = mysqli_fetch_array($rs_member, MYSQLI_ASSOC);
          ?>
          <form action="member_db.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="member" value="edit_profile">
            <div class="modal-header">
              <h5 class="modal-title">แก้ไขข้อมูลส่วนตัว</h5>
              <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <label class="col-sm-3 col-form-label">ระดับการใช้งาน</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="mem_status" value="<?php include 'mem_status.php'; ?>" readonly>
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
                  <textarea name="mem_address" class="form-control" row="3"><?php echo $row_member['mem_address']; ?></textarea>
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