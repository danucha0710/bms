<?php 
session_start();

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

$menu = "borrow_request";
include('../includes/header.php');

// =========================================================
// 1. Logic การค้นหาตามช่วงเวลา
// =========================================================
$dateText = "1"; // Default แสดงทั้งหมด
if (!empty($_POST['date_s']) && !empty($_POST['date_e'])) {
    $date_s = mysqli_real_escape_string($condb, $_POST['date_s']);
    $date_e = mysqli_real_escape_string($condb, $_POST['date_e']);
    $dateText = "borrow_request.br_date_request BETWEEN '$date_s 00:00:00' AND '$date_e 23:59:59'";
}

// =========================================================
// 2. ดึงข้อมูลคำขอกู้
// =========================================================
$query = "SELECT borrow_request.*, member.mem_name 
          FROM borrow_request
          INNER JOIN member ON borrow_request.mem_id = member.mem_id
          WHERE $dateText
          ORDER BY br_id DESC";
$result = mysqli_query($condb, $query) or die("Query failed: " . mysqli_error($condb));

// =========================================================
// 3. ดึงค่า Config ระบบ (เพื่อเอาไปใช้ใน Form Validation)
// =========================================================
$sql_system = "SELECT * FROM `system` WHERE st_id = 1";
$result_system = mysqli_query($condb, $sql_system);
$row_system = mysqli_fetch_array($result_system, MYSQLI_ASSOC);

// =========================================================
// 3.5 ดึงข้อมูลสัญญากู้ที่ค้างชำระทั้งหมด เพื่อใช้ตอนเพิ่มคำขอ
// =========================================================
$sql_active_all = "SELECT r.mem_id, r.br_type, r.br_id, r.br_amount, r.br_months_pay,
                         COUNT(CASE WHEN b.bw_status = 0 THEN 1 END) AS unpaid_count
                  FROM borrow_request r
                  INNER JOIN borrowing b ON r.br_id = b.br_id
                  WHERE r.br_status = 1
                  GROUP BY r.br_id
                  HAVING unpaid_count > 0";
$rs_active_all = mysqli_query($condb, $sql_active_all);
$active_loans_all = [];
if ($rs_active_all) {
    while ($row_act = mysqli_fetch_assoc($rs_active_all)) {
        $active_loans_all[$row_act['mem_id']][$row_act['br_type']] = $row_act;
    }
}

// =========================================================
// 4. Helper Functions (ทำงานเร็วกว่า include ไฟล์ใน loop)
// =========================================================
function getBorrowType($type_id) {
    if ($type_id == 1) return '<span class="text-primary">เงินกู้สามัญ</span>';
    if ($type_id == 2) return '<span class="text-danger">เงินกู้ฉุกเฉิน</span>';
    return '-';
}

function getBorrowStatus($status_id) {
    switch ($status_id) {
        case 0: return '<span class="badge bg-warning text-dark">รอพิจารณา</span>';
        case 1: return '<span class="badge bg-success">อนุมัติ</span>';
        case 2: return '<span class="badge bg-danger">ไม่อนุมัติ</span>';
        default: return '-';
    }
}
?>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title m-0"><i class="fas fa-file-invoice-dollar"></i> รายการคำขอกู้</h4>
                <div>
                    <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fa fa-search"></i> ค้นหาตามช่วงวันเวลา
                    </button>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> เพิ่มคำขอ
                    </button>
                </div>
            </div>

            <div class="card-body">
                <?php if(!empty($_POST['date_s'])){ ?>
                    <div class="alert alert-info py-2">
                        <i class="fas fa-calendar-alt"></i> ผลการค้นหาช่วงวันที่: <b><?php echo date('d/m/Y', strtotime($date_s)); ?></b> ถึง <b><?php echo date('d/m/Y', strtotime($date_e)); ?></b>
                        <a href="borrow_request.php" class="btn btn-xs btn-danger ms-2">ล้างค่า</a>
                    </div>
                <?php } ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle" id="tableSearch">
                        <thead class="table-light">
                            <tr>
                                <th width="10%" class="text-center" style="text-align: center !important;">เลขที่คำขอ</th>
                                <th width="20%" class="text-center" style="text-align: center !important;">ชื่อ-นามสกุล</th>
                                <th width="15%" class="text-center" style="text-align: center !important;">ประเภท</th>
                                <th width="15%" class="text-center" style="text-align: center !important;">ยอดกู้ (บาท)</th>
                                <th width="10%" class="text-center" style="text-align: center !important;">วันที่ยื่น</th>
                                <th width="10%" class="text-center" style="text-align: center !important;">สถานะ</th>
                                <th width="10%" class="text-center" style="text-align: center !important;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result as $row){ ?>
                                <tr>
                                    <td class="text-center"><?php echo (int)$row['br_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['mem_name']); ?></td>
                                    <td class="text-center"><?php echo getBorrowType($row['br_type']); ?></td>
                                    <td class="text-end fw-bold"><?php echo number_format($row['br_amount']); ?></td>
                                    <td class="text-center"><?php echo date('d/m/Y', strtotime($row['br_date_request'])); ?></td>
                                    <td class="text-center"><?php echo getBorrowStatus($row['br_status']); ?></td>
                                    <td class="text-center">
                                        <a href="approve.php?br_id=<?php echo htmlspecialchars($row['br_id']); ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-search"></i> ตรวจสอบ
                                        </a>
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

<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="borrow_request.php" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="fas fa-search"></i> ค้นหาตามช่วงวันเวลา</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" name="date_s" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" name="date_e" required>
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                </div>
            </div>
        </form>
    </div>
</div> 

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="borrow_db.php" method="POST" id="formAddBorrow">
                <input type="hidden" name="borrow" value="add">
                <input type="hidden" name="by_id" value="<?php echo htmlspecialchars($_SESSION["mem_id"]); ?>">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> เพิ่มคำขอกู้เงิน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ชื่อสมาชิก</label>
                        <div class="col-sm-9 position-relative">
                            <input type="text" class="form-control" id="mem_search" placeholder="พิมพ์ชื่อสมาชิกเพื่อค้นหา..." autocomplete="off">
                            <input type="hidden" name="mem_id" id="mem_id" required>
                            <div id="memberDropdown" class="list-group border rounded shadow-sm bg-white position-absolute w-100" style="display:none; max-height: 220px; overflow-y: auto; z-index: 1050; top: 100%; left: 0;"></div>
                            <small class="text-muted" id="mem_selected_label"></small>
                        </div>
                    </div>
                    <script>
                    window.memberList = [
                        <?php
                        $query_mem = "SELECT mem_id, mem_name, mem_status FROM member WHERE mem_status IN (2, 3) ORDER BY mem_name ASC";
                        $rs_mem = mysqli_query($condb, $query_mem);
                        $arr = [];
                        while ($m = mysqli_fetch_assoc($rs_mem)) {
                            $arr[] = '{ "id": "' . addslashes($m['mem_id']) . '", "name": ' . json_encode($m['mem_name'], JSON_UNESCAPED_UNICODE) . ', "status": "' . (int)$m['mem_status'] . '" }';
                        }
                        echo implode(',', $arr);
                        ?>
                    ];
                    window.sysMaxCommonTeacher = <?php echo (int)@$row_system['st_max_amount_common_teacher']; ?>;
                    window.sysMaxCommonOfficer = <?php echo (int)@$row_system['st_max_amount_common_officer']; ?>;
                    window.activeLoansAll = <?php echo json_encode($active_loans_all, JSON_UNESCAPED_UNICODE); ?>;
                    </script>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">ประเภทเงินกู้</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="br_type" id="br_type" required>
                                <option value="" selected disabled>-- กรุณาเลือก --</option>
                                <option value="1">เงินกู้สามัญ (Common)</option>
                                <option value="2">เงินกู้ฉุกเฉิน (Emergency)</option>
                            </select>
                        </div>
                    </div>

                    <div id="loan_details" style="display: none;">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">วงเงินกู้</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control loan-input" id="amt_common" name="br_amount_common" min="1" data-max-teacher="<?php echo (int)@$row_system['st_max_amount_common_teacher']; ?>" data-max-officer="<?php echo (int)@$row_system['st_max_amount_common_officer']; ?>" placeholder="กรุณาเลือกสมาชิกก่อน" style="display: none;">
                                    
                                    <input type="number" class="form-control loan-input" id="amt_emergency" name="br_amount_emergency" min="1" max="<?php echo $row_system['st_max_amount_emergency']; ?>" placeholder="สูงสุด <?php echo number_format($row_system['st_max_amount_emergency']); ?>" style="display: none;">
                                    
                                    <span class="input-group-text">บาท</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">ผ่อนชำระ</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control loan-input" id="pay_common" name="br_months_pay_common" min="1" max="<?php echo $row_system['st_max_months_common']; ?>" placeholder="สูงสุด <?php echo $row_system['st_max_months_common']; ?>" style="display: none;">
                                    
                                    <input type="number" class="form-control loan-input" id="pay_emergency" name="br_months_pay_emergency" min="1" max="<?php echo $row_system['st_max_months_emergency']; ?>" placeholder="สูงสุด <?php echo $row_system['st_max_months_emergency']; ?>" style="display: none;">
                                    
                                    <span class="input-group-text">งวด (เดือน)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="br_is_reset" id="br_is_reset" value="0">
                    <input type="hidden" name="br_reset_br_id" id="br_reset_br_id" value="">

                    <div id="reset_section" style="display:none;" class="row mb-3">
                        <div class="col-sm-9 offset-sm-3">
                            <div class="alert alert-danger border-danger py-2 mb-0">
                                <div class="d-flex gap-2">
                                    <i class="fas fa-sync-alt text-danger mt-1"></i>
                                    <div class="w-100">
                                        <strong class="text-danger">คำเตือน: มีสัญญาเงินกู้ประเภทนี้ที่ยังค้างชำระ</strong>
                                        <p class="mb-1 mt-1 small" id="reset_info_text"></p>
                                        
                                        <div id="reset_calc_section" class="p-2 mb-2 bg-white rounded border border-danger small" style="display:none;">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>วงเงินกู้ใหม่:</span>
                                                <strong id="calc_new_amt">0 บาท</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1 text-danger">
                                                <span>หักยอดหนี้คงเหลือเดิม:</span>
                                                <strong id="calc_old_bal">0 บาท</strong>
                                            </div>
                                            <hr class="my-1">
                                            <div class="d-flex justify-content-between text-success">
                                                <span>ยอดเงินที่คาดว่าจะได้รับจริง:</span>
                                                <strong id="calc_receive_amt">0 บาท</strong>
                                            </div>
                                            <div id="calc_error" class="text-danger mt-1 fw-bold text-center" style="display:none;">
                                                <i class="fas fa-exclamation-circle"></i> วงเงินที่กู้ใหม่ต้องมากกว่ายอดหนี้คงเหลือเดิม
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="confirm_reset">
                                            <label class="form-check-label fw-semibold text-danger" for="confirm_reset">
                                                ยืนยันการกู้เพิ่ม (นำยอดเดิมมารวมและคำนวณงวดจ่ายใหม่ เมื่ออนุมัติ)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">อัตราดอกเบี้ย (ต่อปี)</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" class="form-control" name="br_interest_rate" id="br_interest_rate" value="<?php echo htmlspecialchars(@$row_system['st_interest'] ?? '0'); ?>" step="0.01" min="0">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">ใช้คำนวณรายจ่ายแต่ละงวดเมื่ออนุมัติคำขอ</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">การค้ำประกัน</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="guarantee_type" id="guarantee_type" required>
                                <option value="" selected disabled>-- กรุณาเลือก --</option>
                                <option value="1">ใช้บุคคลค้ำประกัน</option>
                                <option value="2">ใช้หุ้นค้ำประกัน</option>
                            </select>
                        </div>
                    </div>

                    <div id="guarantor_section" style="display: none;">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">ผู้ค้ำคนที่ 1</label>
                            <div class="col-sm-9 position-relative">
                                <input type="text" class="form-control" id="guarantor_1_search" placeholder="พิมพ์ชื่อสมาชิกเพื่อค้นหา..." autocomplete="off">
                                <input type="hidden" name="guarantor_1" id="guarantor_1_name">
                                <input type="hidden" name="guarantor_1_id" id="guarantor_1_id">
                                <div id="guarantor1Dropdown" class="list-group border rounded shadow-sm bg-white position-absolute w-100" style="display:none; max-height: 220px; overflow-y: auto; z-index: 1050; top: 100%; left: 0;"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">ผู้ค้ำคนที่ 2</label>
                            <div class="col-sm-9 position-relative">
                                <input type="text" class="form-control" id="guarantor_2_search" placeholder="พิมพ์ชื่อสมาชิกเพื่อค้นหา..." autocomplete="off">
                                <input type="hidden" name="guarantor_2" id="guarantor_2_name">
                                <input type="hidden" name="guarantor_2_id" id="guarantor_2_id">
                                <div id="guarantor2Dropdown" class="list-group border rounded shadow-sm bg-white position-absolute w-100" style="display:none; max-height: 220px; overflow-y: auto; z-index: 1050; top: 100%; left: 0;"></div>
                            </div>
                        </div>
                    </div>

                    <div id="stock_check_section" class="row mb-3" style="display: none;">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="button" id="btnCheckStock" class="btn btn-warning btn-sm shadow-sm">
                                <i class="fas fa-search-dollar"></i> ตรวจสอบมูลค่าหุ้นของสมาชิก
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">เหตุผลการกู้</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="br_details" rows="2" required placeholder="ระบุรายละเอียด..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึกคำขอ</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">ข้อมูลหุ้นและวงเงิน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="stockResultContent">กำลังโหลดข้อมูล...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Live Search ชื่อสมาชิก (แสดงชื่อ, ส่งค่า mem_id)
    var memSearch = document.getElementById('mem_search');
    var memIdHidden = document.getElementById('mem_id');
    var memberDropdown = document.getElementById('memberDropdown');
    var memList = window.memberList || [];
    var selectedMemStatus = null; // 2=ครู, 3=เจ้าหน้าที่

    function getCommonMaxByStatus(status) {
        var t = window.sysMaxCommonTeacher || 0;
        var o = window.sysMaxCommonOfficer || 0;
        if (status == '2') return t;
        if (status == '3') return o;
        return Math.max(t, o);
    }

    function updateCommonLoanLimit() {
        var amtCommon = document.getElementById('amt_common');
        if (!amtCommon) return;
        var maxVal = getCommonMaxByStatus(selectedMemStatus);
        amtCommon.max = maxVal;
        amtCommon.placeholder = maxVal ? ('สูงสุด ' + Number(maxVal).toLocaleString('th-TH')) : 'กรุณาเลือกสมาชิกก่อน';
        if (amtCommon.value && parseInt(amtCommon.value, 10) > maxVal) amtCommon.value = maxVal;
    }

    function renderMemberList(filter) {
        var q = (filter || '').trim().toLowerCase();
        var list = q
            ? memList.filter(function(m) {
                return (m.name && m.name.toLowerCase().indexOf(q) !== -1) ||
                       (m.id && m.id.indexOf(q) !== -1);
            })
            : memList;
        memberDropdown.innerHTML = '';
        if (list.length === 0) {
            memberDropdown.innerHTML = '<div class="list-group-item text-muted">ไม่พบสมาชิก</div>';
        } else {
            list.forEach(function(m) {
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = m.name;
                a.dataset.memId = m.id;
                a.dataset.memName = m.name;
                a.dataset.memStatus = (m.status !== undefined && m.status !== null) ? String(m.status) : '';
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    memIdHidden.value = this.dataset.memId;
                    memSearch.value = this.dataset.memName;
                    selectedMemStatus = this.dataset.memStatus || null;
                    memberDropdown.style.display = 'none';
                    updateCommonLoanLimit();
                    if(typeof checkActiveLoanForAdmin === 'function') checkActiveLoanForAdmin();
                });
                memberDropdown.appendChild(a);
            });
        }
        memberDropdown.style.display = 'block';
    }

    memSearch.addEventListener('focus', function() {
        renderMemberList(memSearch.value);
    });
    memSearch.addEventListener('input', function() {
        renderMemberList(memSearch.value);
        if (!memSearch.value.trim()) {
            memIdHidden.value = '';
            selectedMemStatus = null;
            updateCommonLoanLimit();
        }
    });
    memSearch.addEventListener('blur', function() {
        setTimeout(function() { memberDropdown.style.display = 'none'; }, 200);
    });

    document.getElementById('addModal').addEventListener('show.bs.modal', function() {
        memSearch.value = '';
        memIdHidden.value = '';
        selectedMemStatus = null;
        memberDropdown.style.display = 'none';
        updateCommonLoanLimit();
        var g1 = document.getElementById('guarantor_1_search');
        var g2 = document.getElementById('guarantor_2_search');
        var g1Name = document.getElementById('guarantor_1_name');
        var g1Id = document.getElementById('guarantor_1_id');
        var g2Name = document.getElementById('guarantor_2_name');
        var g2Id = document.getElementById('guarantor_2_id');
        if (g1) { g1.value = ''; }
        if (g2) { g2.value = ''; }
        if (g1Name) { g1Name.value = ''; }
        if (g1Id) { g1Id.value = ''; }
        if (g2Name) { g2Name.value = ''; }
        if (g2Id) { g2Id.value = ''; }
        if (document.getElementById('guarantor1Dropdown')) document.getElementById('guarantor1Dropdown').style.display = 'none';
        if (document.getElementById('guarantor2Dropdown')) document.getElementById('guarantor2Dropdown').style.display = 'none';
    });

    // 2. Logic สลับประเภทเงินกู้ (Common vs Emergency)
    const brType = document.getElementById('br_type');
    const loanDetails = document.getElementById('loan_details');
    
    // Inputs
    const amtCommon = document.getElementById('amt_common');
    const amtEmer = document.getElementById('amt_emergency');
    const payCommon = document.getElementById('pay_common');
    const payEmer = document.getElementById('pay_emergency');

    function checkActiveLoanForAdmin() {
        var mId = memIdHidden.value;
        var bType = brType.value;
        var resetSection = document.getElementById('reset_section');
        var brIsReset = document.getElementById('br_is_reset');
        var brResetBrId = document.getElementById('br_reset_br_id');
        var confirmReset = document.getElementById('confirm_reset');
        var resetInfo = document.getElementById('reset_info_text');
        var resetCalcSection = document.getElementById('reset_calc_section');

        if (mId && bType && window.activeLoansAll && window.activeLoansAll[mId] && window.activeLoansAll[mId][bType]) {
            var activeLoan = window.activeLoansAll[mId][bType];
            var typeName = bType == '1' ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
            var remainingPrincipal = Math.round((parseFloat(activeLoan.br_amount) / parseFloat(activeLoan.br_months_pay)) * parseFloat(activeLoan.unpaid_count));
            activeLoan.remaining_principal = remainingPrincipal; // Store for calculation

            resetInfo.innerHTML =
                'สัญญา' + typeName + ' เลขที่ <strong>#' + activeLoan.br_id + '</strong>' +
                ' ยังเหลืออีก <strong>' + activeLoan.unpaid_count + '/' + activeLoan.br_months_pay + ' งวด</strong> (ยอดหนี้คงเหลือประมาณ <strong>' + Number(remainingPrincipal).toLocaleString('th-TH') + '</strong> บาท)';
            resetSection.style.display = 'flex';
            if (resetCalcSection) resetCalcSection.style.display = 'block';
            brIsReset.value = '1';
            brResetBrId.value = activeLoan.br_id;
            if (confirmReset) confirmReset.checked = false;
            
            if (typeof updateAdminResetCalc === 'function') updateAdminResetCalc();
        } else {
            resetSection.style.display = 'none';
            if (resetCalcSection) resetCalcSection.style.display = 'none';
            brIsReset.value = '0';
            brResetBrId.value = '';
            if (confirmReset) confirmReset.checked = false;
        }
    }

    function updateAdminResetCalc() {
        var mId = memIdHidden.value;
        var bType = brType.value;
        if (!mId || !bType || !window.activeLoansAll || !window.activeLoansAll[mId] || !window.activeLoansAll[mId][bType]) return;
        
        var activeLoan = window.activeLoansAll[mId][bType];
        var oldBal = parseFloat(activeLoan.remaining_principal || 0);
        var newAmtInput = (bType == '1') ? document.getElementById('amt_common') : document.getElementById('amt_emergency');
        var newAmt = parseFloat(newAmtInput.value || 0);
        
        var calcNewAmt = document.getElementById('calc_new_amt');
        var calcOldBal = document.getElementById('calc_old_bal');
        if(calcNewAmt) calcNewAmt.innerText = newAmt.toLocaleString('th-TH') + ' บาท';
        if(calcOldBal) calcOldBal.innerText = oldBal.toLocaleString('th-TH') + ' บาท';
        
        var recvAmt = newAmt - oldBal;
        var recvEl = document.getElementById('calc_receive_amt');
        var errEl = document.getElementById('calc_error');
        var confirmReset = document.getElementById('confirm_reset');
        
        if (newAmt > 0 && newAmt <= oldBal) {
            if(recvEl) {
                recvEl.innerText = '0 บาท';
                recvEl.className = 'text-danger';
            }
            if(errEl) errEl.style.display = 'block';
            if(confirmReset) {
                confirmReset.checked = false;
                confirmReset.disabled = true;
            }
        } else {
            if(recvEl) {
                recvEl.innerText = (recvAmt > 0 ? recvAmt : 0).toLocaleString('th-TH') + ' บาท';
                recvEl.className = 'text-success';
            }
            if(errEl) errEl.style.display = 'none';
            if(confirmReset) confirmReset.disabled = false;
        }
    }

    if (amtCommon) { amtCommon.addEventListener('input', updateAdminResetCalc); }
    if (amtEmer) { amtEmer.addEventListener('input', updateAdminResetCalc); }

    brType.addEventListener('change', function() {
        loanDetails.style.display = 'block'; // โชว์กล่องรวม
        
        // Reset Required & Display
        amtCommon.style.display = 'none'; amtCommon.required = false;
        amtEmer.style.display = 'none'; amtEmer.required = false;
        payCommon.style.display = 'none'; payCommon.required = false;
        payEmer.style.display = 'none'; payEmer.required = false;

        if (this.value == '1') { // สามัญ
            amtCommon.style.display = 'block'; amtCommon.required = true;
            payCommon.style.display = 'block'; payCommon.required = true;
            updateCommonLoanLimit();
        } else if (this.value == '2') { // ฉุกเฉิน
            amtEmer.style.display = 'block'; amtEmer.required = true;
            payEmer.style.display = 'block'; payEmer.required = true;
        }
        
        checkActiveLoanForAdmin();
    });

    // 3. Logic สลับประเภทค้ำประกัน
    const guaType = document.getElementById('guarantee_type');
    const guaSection = document.getElementById('guarantor_section');
    const stockSection = document.getElementById('stock_check_section');
    var guarantor1Search = document.getElementById('guarantor_1_search');
    var guarantor2Search = document.getElementById('guarantor_2_search');
    var guarantor1Name = document.getElementById('guarantor_1_name');
    var guarantor1Id = document.getElementById('guarantor_1_id');
    var guarantor2Name = document.getElementById('guarantor_2_name');
    var guarantor2Id = document.getElementById('guarantor_2_id');

    guaType.addEventListener('change', function() {
        if(this.value == '1') { // บุคคล
            guaSection.style.display = 'block';
            stockSection.style.display = 'none';
            if (guarantor1Search) guarantor1Search.required = true;
            if (guarantor2Search) guarantor2Search.required = true;
        } else if(this.value == '2') { // หุ้น
            guaSection.style.display = 'none';
            stockSection.style.display = 'block';
            if (guarantor1Search) guarantor1Search.required = false;
            if (guarantor2Search) guarantor2Search.required = false;
        } else {
            guaSection.style.display = 'none';
            stockSection.style.display = 'none';
            if (guarantor1Search) guarantor1Search.required = false;
            if (guarantor2Search) guarantor2Search.required = false;
        }
    });

    // Live Search ผู้ค้ำคนที่ 1 และ 2 (ใช้ memberList เหมือนช่องชื่อสมาชิก, ไม่แสดงชื่อผู้ขอกู้)
    function renderGuarantorDropdown(filter, dropdownEl, searchEl, nameEl, idEl) {
        var borrowerId = (memIdHidden && memIdHidden.value) ? memIdHidden.value.trim() : '';
        var q = (filter || '').trim().toLowerCase();
        var list = q
            ? memList.filter(function(m) {
                return ((m.name && m.name.toLowerCase().indexOf(q) !== -1) ||
                       (m.id && m.id.indexOf(q) !== -1));
            })
            : memList.slice();
        list = list.filter(function(m) { return m.id !== borrowerId; });
        dropdownEl.innerHTML = '';
        if (list.length === 0) {
            dropdownEl.innerHTML = '<div class="list-group-item text-muted">ไม่พบสมาชิก</div>';
        } else {
            list.forEach(function(m) {
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = m.name;
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (searchEl) searchEl.value = m.name;
                    if (nameEl) nameEl.value = m.name;
                    if (idEl) idEl.value = m.id;
                    dropdownEl.style.display = 'none';
                });
                dropdownEl.appendChild(a);
            });
        }
        dropdownEl.style.display = 'block';
    }

    var guarantor1Drop = document.getElementById('guarantor1Dropdown');
    var guarantor2Drop = document.getElementById('guarantor2Dropdown');

    if (guarantor1Search && guarantor1Drop) {
        guarantor1Search.addEventListener('focus', function() { renderGuarantorDropdown(guarantor1Search.value, guarantor1Drop, guarantor1Search, guarantor1Name, guarantor1Id); });
        guarantor1Search.addEventListener('input', function() { renderGuarantorDropdown(guarantor1Search.value, guarantor1Drop, guarantor1Search, guarantor1Name, guarantor1Id); });
        guarantor1Drop.addEventListener('mousedown', function(e) { e.preventDefault(); });
        guarantor1Search.addEventListener('blur', function() { setTimeout(function() { guarantor1Drop.style.display = 'none'; }, 200); });
    }
    if (guarantor2Search && guarantor2Drop) {
        guarantor2Search.addEventListener('focus', function() { renderGuarantorDropdown(guarantor2Search.value, guarantor2Drop, guarantor2Search, guarantor2Name, guarantor2Id); });
        guarantor2Search.addEventListener('input', function() { renderGuarantorDropdown(guarantor2Search.value, guarantor2Drop, guarantor2Search, guarantor2Name, guarantor2Id); });
        guarantor2Drop.addEventListener('mousedown', function(e) { e.preventDefault(); });
        guarantor2Search.addEventListener('blur', function() { setTimeout(function() { guarantor2Drop.style.display = 'none'; }, 200); });
    }

    // 4. Logic ตรวจสอบหุ้น (Fetch API)
    document.getElementById('btnCheckStock').addEventListener('click', function() {
        const memId = document.getElementById('mem_id').value;
        if(!memId) {
            alert('กรุณาเลือกสมาชิกก่อนครับ');
            return;
        }

        // เปิด Modal รอ
        var stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
        stockModal.show();
        
        // เรียกข้อมูล
        fetch('fetch_member_data.php?mem_id=' + memId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('stockResultContent').innerHTML = data;
            })
            .catch(error => {
                document.getElementById('stockResultContent').innerHTML = '<div class="text-danger">เกิดข้อผิดพลาดในการดึงข้อมูล</div>';
            });
    });

    // 5. Logic Validate ฟอร์ม
    var formAddBorrow = document.getElementById('formAddBorrow');
    if (formAddBorrow) {
        formAddBorrow.addEventListener('submit', function(e) {
            var brIsResetVal = document.getElementById('br_is_reset').value;
            if (brIsResetVal == '1') {
                var confirmReset = document.getElementById('confirm_reset');
                
                var mId = memIdHidden.value;
                var bType = brType.value;
                var activeLoan = (window.activeLoansAll && window.activeLoansAll[mId] && window.activeLoansAll[mId][bType]) ? window.activeLoansAll[mId][bType] : null;
                var oldBal = activeLoan ? parseFloat(activeLoan.remaining_principal || 0) : 0;
                var amount = 0;
                if (bType === '1') {
                    amount = parseFloat(document.getElementById('amt_common').value || '0');
                } else if (bType === '2') {
                    amount = parseFloat(document.getElementById('amt_emergency').value || '0');
                }

                if (amount <= oldBal) {
                    alert('ไม่สามารถบันทึกคำขอได้ เนื่องจากวงเงินที่กู้ใหม่ต้องมากกว่ายอดหนี้คงเหลือเดิม');
                    e.preventDefault();
                    return false;
                }

                if (!confirmReset || !confirmReset.checked) {
                    alert('กรุณายืนยันการกู้เพิ่มก่อนดำเนินการต่อ\n(ติ๊กเครื่องหมายยืนยันในกล่องแจ้งเตือนสีแดง)');
                    e.preventDefault();
                    return false;
                }
            }

            var gType = document.getElementById('guarantee_type').value;
            if (gType === '1') {
                var g1Id = document.getElementById('guarantor_1_id').value;
                var g2Id = document.getElementById('guarantor_2_id').value;
                if (!g1Id || !g2Id) {
                    alert('กรุณาเลือกผู้ค้ำประกันให้ครบทั้ง 2 คนจากรายชื่อที่ค้นพบ');
                    e.preventDefault();
                    return false;
                }
                if (g1Id === g2Id) {
                    alert('ผู้ค้ำคนที่ 1 และ ผู้ค้ำคนที่ 2 ต้องไม่เป็นบุคคลเดียวกัน');
                    e.preventDefault();
                    return false;
                }
            }
        });
    }

});
</script>