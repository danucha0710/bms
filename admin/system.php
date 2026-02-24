<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "system";
include('../includes/header.php'); // แก้ Path ให้ถูกต้อง

// ดึงข้อมูลการตั้งค่าปัจจุบัน
$query = "SELECT * FROM `system` WHERE st_id = 1";
$result = mysqli_query($condb, $query) or die("Error : ".mysqli_error($condb));
$value = mysqli_fetch_array($result, MYSQLI_ASSOC);
?>

    <section class="content">
      <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="card-title m-0"><i class="fas fa-cogs"></i> ตั้งค่าระบบ</h4>  
                    </div>
                    
                    <div class="card-body">
                        <form action="system_db.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="system" value="setting">
                            <input type="hidden" name="st_edit_by" value="<?php echo htmlspecialchars($_SESSION["mem_id"]); ?>">
                            
                            <h6 class="text-primary border-bottom pb-2 mb-3">1. กำหนดวงเงินกู้สูงสุด</h6>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินกู้สามัญ (ครู)</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_amount_common_teacher" value="<?php echo isset($value['st_max_amount_common_teacher']) ? $value['st_max_amount_common_teacher'] : ''; ?>" required>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินกู้สามัญ (เจ้าหน้าที่)</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_amount_common_officer" value="<?php echo isset($value['st_max_amount_common_officer']) ? $value['st_max_amount_common_officer'] : ''; ?>" required>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินกู้ฉุกเฉิน</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_amount_emergency" value="<?php echo $value['st_max_amount_emergency']; ?>" required>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินออมหุ้นขั้นต่ำ</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_min_stock_savings" value="<?php echo isset($value['st_min_stock_savings']) ? (int)$value['st_min_stock_savings'] : 0; ?>">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินออมหุ้นสูงสุด</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_stock_savings" value="<?php echo isset($value['st_max_stock_savings']) ? (int)$value['st_max_stock_savings'] : 0; ?>">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">2. จำนวนเงินต้น</h6>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">สำหรับครู</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_amount_cost_teacher" value="<?php echo $value['st_amount_cost_teacher']; ?>" required>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">สำหรับเจ้าหน้าที่</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_amount_cost_officer" value="<?php echo $value['st_amount_cost_officer']; ?>" required>
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">3. ระยะเวลาผ่อนชำระสูงสุด</h6>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินกู้สามัญ</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_months_common" value="<?php echo $value['st_max_months_common']; ?>" required>
                                        <span class="input-group-text">งวด (เดือน)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินกู้ฉุกเฉิน</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="1" name="st_max_months_emergency" value="<?php echo $value['st_max_months_emergency']; ?>" required>
                                        <span class="input-group-text">งวด (เดือน)</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">4. อัตราดอกเบี้ยและหุ้น</h6>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">อัตราดอกเบี้ยต่อปี</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" max="100" step="0.01" name="st_interest" value="<?php echo $value['st_interest']; ?>" required>
                                        <span class="input-group-text bg-warning text-dark">% ต่อปี</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">ราคาหุ้น</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" step="0.01" name="st_stock_price" value="<?php echo $value['st_stock_price']; ?>" required>
                                        <span class="input-group-text">บาท/หุ้น</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินปันผล</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" max="100" step="0.01" name="st_dividend_rate" value="<?php echo $value['st_dividend_rate']; ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">เงินเฉลี่ยคืน</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="0" max="100" step="0.01" name="st_average_return_rate" value="<?php echo $value['st_average_return_rate']; ?>" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">5. การตั้งค่าอื่นๆ</h6>
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">ตัดรอบชำระเงินทุกวันที่</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" min="1" max="31" step="1" name="st_dateline" value="<?php echo $value['st_dateline']; ?>" required>
                                        <span class="input-group-text">ของเดือน</span>
                                    </div>
                                    <small class="text-muted">* หากระบุ 31 ระบบจะปัดเป็นวันสุดท้ายของเดือนนั้นๆ</small>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success px-5" onclick="return confirm('ยืนยันการบันทึกการตั้งค่า?');">
                                        <i class="fas fa-save"></i> บันทึกการตั้งค่า
                                    </button>
                                </div>
                            </div>
                        </form>   
                    </div> 
                </div>

            </div>   
        </div>
      </div>
    </section>
    <?php include('../includes/footer.php'); ?>