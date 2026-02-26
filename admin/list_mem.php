<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "member";
include('../includes/header.php');

// ดึงค่าตั้งค่าวงเงินจากระบบ (สำหรับฟอร์มเพิ่มสมาชิก)
$query_system = "SELECT st_max_amount_common_teacher, st_max_amount_common_officer, st_max_amount_emergency, st_min_stock_savings, st_max_stock_savings FROM `system` WHERE st_id = 1";
$rs_system = mysqli_query($condb, $query_system) or die("Error : ".mysqli_error($condb));
$row_system = mysqli_fetch_assoc($rs_system);
$sys_common_teacher = (int)($row_system['st_max_amount_common_teacher'] ?? 0);
$sys_common_officer  = (int)($row_system['st_max_amount_common_officer'] ?? 0);
$sys_emergency      = (int)($row_system['st_max_amount_emergency'] ?? 0);
$sys_min_stock_savings = (int)($row_system['st_min_stock_savings'] ?? 0);
$sys_max_stock_savings = (int)($row_system['st_max_stock_savings'] ?? 0);

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
                                <th width="20%" class="text-center">ชื่อ-นามสกุล</th>
                                <th width="10%" class="text-center">เบอร์โทร</th>
                                <th width="10%" class="text-center">สถานะ</th>
                                <th width="12%" class="text-center">จำนวนเงินหุ้น</th>
                                <th width="12%" class="text-center">เงินออมหุ้น/เดือน</th>
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
                                <td><?php echo htmlspecialchars($row['mem_name']); ?></td>
                                <td class="text-start"><?php echo htmlspecialchars($row['mem_phone']); ?></td>
                                <td class="text-start">
                                    <?php echo getStatusName($row['mem_status']); ?>
                                </td>
                                <td class="text-end"><?php echo number_format(isset($row['mem_amount_stock']) ? (int)$row['mem_amount_stock'] : 0); ?></td>
                                <td class="text-end"><?php echo number_format(isset($row['mem_stock_savings']) ? (int)$row['mem_stock_savings'] : 0); ?></td>
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
              <hr>
              <h6 class="text-primary fw-bold">วงเงินกู้ / เงินออมหุ้น (อิงตามการตั้งค่าใน ตั้งค่าระบบ)</h6>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label small">
                    เงินออมหุ้น/เดือน (บาท)
                    <?php if ($sys_min_stock_savings || $sys_max_stock_savings) { ?>
                      <span class="text-muted small">
                        (ขั้นต่ำ <?php echo number_format($sys_min_stock_savings); ?> บาท
                        <?php if ($sys_max_stock_savings) { ?>, สูงสุด <?php echo number_format($sys_max_stock_savings); ?> บาท<?php } ?>)
                      </span>
                    <?php } ?>
                  </label>
                  <input
                    type="number"
                    class="form-control"
                    id="add_stock_savings"
                    name="mem_stock_savings"
                    min="<?php echo $sys_min_stock_savings; ?>"
                    <?php if ($sys_max_stock_savings > 0) { ?>
                      max="<?php echo $sys_max_stock_savings; ?>"
                    <?php } ?>
                    step="1"
                    value="<?php echo $sys_min_stock_savings > 0 ? $sys_min_stock_savings : 0; ?>"
                  >
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label small">วงเงินกู้สามัญ (บาท)</label>
                  <input type="text" class="form-control bg-light" id="add_common_credit" readonly value="— เลือกระดับการใช้งานก่อน">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">วงเงินกู้ฉุกเฉิน (บาท)</label>
                  <input type="text" class="form-control bg-light" id="add_emergency_credit" readonly value="— เลือกระดับการใช้งานก่อน">
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
    <script>
    (function() {
      var sysCommonTeacher = <?php echo $sys_common_teacher; ?>;
      var sysCommonOfficer = <?php echo $sys_common_officer; ?>;
      var sysEmergency = <?php echo $sys_emergency; ?>;
      var sysMinStock = <?php echo $sys_min_stock_savings; ?>;
      var sysMaxStock = <?php echo $sys_max_stock_savings; ?>;

      var sel = document.querySelector('#addModal select[name="mem_status"]');
      var commonEl = document.getElementById('add_common_credit');
      var emergencyEl = document.getElementById('add_emergency_credit');
      var stockEl = document.getElementById('add_stock_savings');

      function fmt(n) { return n.toLocaleString('th-TH'); }

      function updateAddCreditsAndStock() {
        var v = sel ? sel.value : '';

        // วงเงินกู้
        if (v === '') {
          commonEl.value = '— เลือกระดับการใช้งานก่อน';
          emergencyEl.value = '— เลือกระดับการใช้งานก่อน';
        } else if (v === '0' || v === '1') {
          commonEl.value = '0 (ไม่ใช้วงเงินกู้)';
          emergencyEl.value = '0 (ไม่ใช้วงเงินกู้)';
        } else if (v === '2') {
          commonEl.value = fmt(sysCommonTeacher);
          emergencyEl.value = fmt(sysEmergency);
        } else if (v === '3') {
          commonEl.value = fmt(sysCommonOfficer);
          emergencyEl.value = fmt(sysEmergency);
        } else {
          commonEl.value = '0';
          emergencyEl.value = '0';
        }

        // เงินออมหุ้น/เดือน
        if (!stockEl) return;
        if (v === '0' || v === '1') {
          // ผู้ดูแลระบบ และ พนักงานคีย์ข้อมูล: เงินออมหุ้น/เดือนไม่ให้พิมพ์ และเป็น 0
          stockEl.value = 0;
          stockEl.readOnly = true;
          stockEl.classList.add('bg-light');
          stockEl.removeAttribute('min');
          stockEl.removeAttribute('max');
        } else {
          // สถานะอื่น: ใช้ min/max จากระบบ
          stockEl.readOnly = false;
          stockEl.classList.remove('bg-light');
          if (sysMinStock >= 0) stockEl.min = sysMinStock;
          else stockEl.removeAttribute('min');
          if (sysMaxStock > 0) stockEl.max = sysMaxStock;
          else stockEl.removeAttribute('max');
          if (!stockEl.value || parseInt(stockEl.value, 10) < sysMinStock) {
            stockEl.value = sysMinStock > 0 ? sysMinStock : 0;
          }
        }
      }

      if (sel) sel.addEventListener('change', updateAddCreditsAndStock);
      document.getElementById('addModal').addEventListener('show.bs.modal', updateAddCreditsAndStock);
    })();
    </script> 

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
              <h5 class="modal-title"><i class="fas fa-edit"></i> แก้ไขข้อมูล</h5>
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
              <h6 class="text-primary fw-bold">วงเงินกู้ / เงินออมหุ้น (อิงตามการตั้งค่าใน ตั้งค่าระบบ)</h6>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label small">
                      เงินออมหุ้น/เดือน (บาท)
                      <?php if ($sys_min_stock_savings || $sys_max_stock_savings) { ?>
                        <span class="text-muted small">
                          (ขั้นต่ำ <?php echo number_format($sys_min_stock_savings); ?> บาท
                          <?php if ($sys_max_stock_savings) { ?>, สูงสุด <?php echo number_format($sys_max_stock_savings); ?> บาท<?php } ?>)
                        </span>
                      <?php } ?>
                    </label>
                    <input
                      type="number"
                      class="form-control edit-stock-savings"
                      name="mem_stock_savings"
                      min="<?php echo $sys_min_stock_savings; ?>"
                      <?php if ($sys_max_stock_savings > 0) { ?>
                        max="<?php echo $sys_max_stock_savings; ?>"
                      <?php } ?>
                      step="1"
                      value="<?php echo isset($row['mem_stock_savings']) ? (int)$row['mem_stock_savings'] : 0; ?>"
                    >
                 </div>
                 <div class="col-md-6">
                   <label class="form-label small">จำนวนเงินหุ้น (บาท)</label>
                   <input
                     type="number"
                     class="form-control edit-amount-stock"
                     name="mem_amount_stock"
                     min="0"
                     step="1"
                     value="<?php echo isset($row['mem_amount_stock']) ? (int)$row['mem_amount_stock'] : 0; ?>"
                   >
                 </div>
              </div>
              <div class="row mb-3">
                 <div class="col-md-6">
                    <label class="form-label small">วงเงินกู้สามัญ (บาท) <span class="edit-max-common-hint text-muted small"></span></label>
                    <input type="number" class="form-control edit-common-credit" name="common_credit" min="0" value="<?php echo (int)$row['mem_common_credit']; ?>">
                 </div>
                 <div class="col-md-6">
                    <label class="form-label small">วงเงินกู้ฉุกเฉิน (บาท) <span class="edit-max-emergency-hint text-muted small"></span></label>
                    <input type="number" class="form-control edit-emergency-credit" name="emergency_credit" min="0" value="<?php echo (int)$row['mem_emergency_credit']; ?>">
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
    <script>
    (function() {
      var sysCommonTeacher = <?php echo $sys_common_teacher; ?>;
      var sysCommonOfficer = <?php echo $sys_common_officer; ?>;
      var sysEmergency = <?php echo $sys_emergency; ?>;
      var sysMinStock = <?php echo $sys_min_stock_savings; ?>;
      var sysMaxStock = <?php echo $sys_max_stock_savings; ?>;

      document.querySelectorAll('.modal').forEach(function(modal) {
        var sel = modal.querySelector('select[name="mem_status"]');
        var commonInp = modal.querySelector('.edit-common-credit');
        var emergencyInp = modal.querySelector('.edit-emergency-credit');
        var stockInp = modal.querySelector('.edit-stock-savings');
        var amountStockInp = modal.querySelector('.edit-amount-stock');
        var commonHint = modal.querySelector('.edit-max-common-hint');
        var emergencyHint = modal.querySelector('.edit-max-emergency-hint');
        if (!sel || !commonInp || !emergencyInp) return;
        function setMaxAndHint(maxCommon, maxEmergency) {
          commonInp.max = maxCommon;
          emergencyInp.max = maxEmergency;
          if (commonHint) commonHint.textContent = maxCommon > 0 ? '(สูงสุด ' + Number(maxCommon).toLocaleString('th-TH') + ' บาท)' : '';
          if (emergencyHint) emergencyHint.textContent = maxEmergency > 0 ? '(สูงสุด ' + Number(maxEmergency).toLocaleString('th-TH') + ' บาท)' : '';
          var cv = parseInt(commonInp.value, 10) || 0;
          var ev = parseInt(emergencyInp.value, 10) || 0;
          if (maxCommon >= 0 && cv > maxCommon) commonInp.value = maxCommon;
          if (maxEmergency >= 0 && ev > maxEmergency) emergencyInp.value = maxEmergency;
        }
        function toggleEditCredits() {
          var v = sel.value;
          if (v === '0' || v === '1') {
            commonInp.value = '0';
            emergencyInp.value = '0';
            commonInp.readOnly = true;
            emergencyInp.readOnly = true;
            commonInp.removeAttribute('max');
            emergencyInp.removeAttribute('max');
            commonInp.classList.add('bg-light');
            emergencyInp.classList.add('bg-light');
            if (commonHint) commonHint.textContent = '';
            if (emergencyHint) emergencyHint.textContent = '';
            // เงินออมหุ้น/เดือน: Admin/พนักงานคีย์ข้อมูล = 0 และแก้ไขไม่ได้
            if (stockInp) {
              stockInp.value = 0;
              stockInp.readOnly = true;
              stockInp.classList.add('bg-light');
              stockInp.removeAttribute('min');
              stockInp.removeAttribute('max');
            }
            if (amountStockInp) {
              amountStockInp.value = 0;
              amountStockInp.readOnly = true;
              amountStockInp.classList.add('bg-light');
            }
          } else {
            commonInp.readOnly = false;
            emergencyInp.readOnly = false;
            commonInp.classList.remove('bg-light');
            emergencyInp.classList.remove('bg-light');
            if (v === '2') setMaxAndHint(sysCommonTeacher, sysEmergency);
            else if (v === '3') setMaxAndHint(sysCommonOfficer, sysEmergency);
            else setMaxAndHint(0, 0);

            // เงินออมหุ้น/เดือน: ครู/เจ้าหน้าที่ ใช้ min/max จากระบบ
            if (stockInp) {
              stockInp.readOnly = false;
              stockInp.classList.remove('bg-light');
              if (sysMinStock >= 0) stockInp.min = sysMinStock;
              else stockInp.removeAttribute('min');
              if (sysMaxStock > 0) stockInp.max = sysMaxStock;
              else stockInp.removeAttribute('max');
              var sv = parseInt(stockInp.value, 10);
              if (isNaN(sv) || sv < sysMinStock) {
                stockInp.value = sysMinStock > 0 ? sysMinStock : 0;
              } else if (sysMaxStock > 0 && sv > sysMaxStock) {
                stockInp.value = sysMaxStock;
              }
            }
            if (amountStockInp) {
              amountStockInp.readOnly = false;
              amountStockInp.classList.remove('bg-light');
            }
          }
        }
        sel.addEventListener('change', toggleEditCredits);
        modal.addEventListener('show.bs.modal', toggleEditCredits);
      });
    })();
    </script>

<?php include('../includes/footer.php'); ?>