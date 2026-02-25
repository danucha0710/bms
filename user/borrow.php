<?php
session_start();
if (!isset($_SESSION['mem_id']) || ($_SESSION['mem_status'] != '2' && $_SESSION['mem_status'] != '3')) {
    header("Location: ../login.php");
    exit();
}

$menu = "borrow";
include('../config/condb.php');
include('../includes/header.php');

$mem_id = mysqli_real_escape_string($condb, $_SESSION['mem_id']);
$sql_mem = "SELECT mem_name, mem_common_credit, mem_emergency_credit, mem_amount_stock, mem_status FROM member WHERE mem_id = '$mem_id'";
$rs_mem = mysqli_query($condb, $sql_mem);
$member = mysqli_fetch_assoc($rs_mem);
if (!$member) {
    header("Location: ../login.php");
    exit();
}

$sql_system = "SELECT * FROM system WHERE st_id = 1";
$rs_sys = mysqli_query($condb, $sql_system);
$sys = mysqli_fetch_assoc($rs_sys);

$query_mem = "SELECT mem_id, mem_name, mem_status FROM member WHERE mem_status IN (2, 3) AND mem_id != '$mem_id' ORDER BY mem_name ASC";
$rs_guar = mysqli_query($condb, $query_mem);
$guarantor_list = [];
while ($m = mysqli_fetch_assoc($rs_guar)) {
    $guarantor_list[] = $m;
}

// ตรวจสอบสัญญากู้ที่ยังค้างชำระอยู่ (br_status=1 และมีงวดที่ยังไม่จ่าย bw_status=0)
$active_loans = [];
foreach ([1, 2] as $loan_type) {
    $sql_active = "SELECT r.br_id, r.br_amount, r.br_months_pay,
                         COUNT(CASE WHEN b.bw_status = 0 THEN 1 END) AS unpaid_count,
                         COUNT(CASE WHEN b.bw_status = 1 THEN 1 END) AS paid_count
                  FROM borrow_request r
                  INNER JOIN borrowing b ON r.br_id = b.br_id
                  WHERE r.mem_id = '$mem_id' AND r.br_type = $loan_type AND r.br_status = 1
                  GROUP BY r.br_id
                  HAVING unpaid_count > 0
                  ORDER BY r.br_id DESC
                  LIMIT 1";
    $rs_active = mysqli_query($condb, $sql_active);
    $row_active = $rs_active ? mysqli_fetch_assoc($rs_active) : null;
    if ($row_active) {
        $active_loans[$loan_type] = $row_active;
    }
}
?>

<section class="content">
  <div class="container-fluid py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i> ยื่นคำขอกู้เงิน</h5>
          </div>
            <div class="card-body p-4">
            <div class="alert alert-light border mb-3">
              <strong>ผู้ขอกู้:</strong> <?php echo htmlspecialchars($member['mem_name']); ?>
              &nbsp;|&nbsp; วงเงินสามัญ: <?php echo number_format($member['mem_common_credit']); ?> บาท
              &nbsp;|&nbsp; วงเงินฉุกเฉิน: <?php echo number_format($member['mem_emergency_credit']); ?> บาท
              &nbsp;|&nbsp; จำนวนเงินหุ้น: <?php echo number_format(isset($member['mem_amount_stock']) ? (int)$member['mem_amount_stock'] : 0); ?> บาท
            </div>

            <?php if (!empty($active_loans)): ?>
            <div class="alert alert-warning border-warning mb-3">
              <div class="d-flex align-items-start gap-2">
                <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                <div>
                  <strong>สัญญาเงินกู้ที่ยังค้างชำระ:</strong>
                  <?php foreach ($active_loans as $type => $loan): ?>
                  <div class="mt-1">
                    <span class="badge <?php echo $type == 1 ? 'bg-primary' : 'bg-warning text-dark'; ?>">
                      <?php echo $type == 1 ? 'สามัญ' : 'ฉุกเฉิน'; ?>
                    </span>
                    สัญญาเลขที่ <strong>#<?php echo (int)$loan['br_id']; ?></strong>
                    &nbsp;|&nbsp; วงเงิน <?php echo number_format((float)$loan['br_amount']); ?> บาท
                    &nbsp;|&nbsp; เหลืออีก <strong><?php echo (int)$loan['unpaid_count']; ?></strong>
                    / <?php echo (int)$loan['br_months_pay']; ?> งวด
                  </div>
                  <?php endforeach; ?>
                  <div class="mt-2 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    การยื่นคำขอกู้ซ้ำประเภทเดิมต้องเลือก <strong>รีเซ็ทสัญญาเดิม</strong> — งวดที่เหลือจะถูกยกเลิกเมื่อผู้ดูแลอนุมัติคำขอใหม่
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['err']) && $_GET['err'] === 'active_loan'): ?>
            <div class="alert alert-danger mb-3">
              <i class="fas fa-ban me-1"></i>
              ไม่สามารถยื่นคำขอกู้ประเภทนี้ได้ เนื่องจากยังมีสัญญาที่ค้างชำระอยู่ กรุณาเลือก <strong>รีเซ็ทสัญญา</strong> หากต้องการเริ่มสัญญาใหม่
            </div>
            <?php endif; ?>

            <form action="request_save.php" method="POST" id="formBorrow">
              <input type="hidden" name="mem_id" value="<?php echo htmlspecialchars($mem_id); ?>">
              <input type="hidden" name="br_is_reset" id="br_is_reset" value="0">
              <input type="hidden" name="br_reset_br_id" id="br_reset_br_id" value="">

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ประเภทเงินกู้</label>
                <div class="col-sm-9">
                  <select class="form-select" name="br_type" id="br_type" required>
                    <option value="" selected disabled>-- เลือก --</option>
                    <option value="1">เงินกู้สามัญ</option>
                    <option value="2">เงินกู้ฉุกเฉิน</option>
                  </select>
                </div>
              </div>

              <div id="loan_details" style="display:none;">
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">วงเงินกู้ (บาท)</label>
                  <div class="col-sm-6">
                    <input type="number" class="form-control" id="amt_common" name="br_amount_common" min="1" style="display:none;" placeholder="สูงสุด <?php echo number_format($member['mem_status']=='2' ? $sys['st_max_amount_common_teacher'] : $sys['st_max_amount_common_officer']); ?> บาท">
                    <input type="number" class="form-control" id="amt_emergency" name="br_amount_emergency" min="1" max="<?php echo (int)$sys['st_max_amount_emergency']; ?>" style="display:none;" placeholder="สูงสุด <?php echo number_format($sys['st_max_amount_emergency']); ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">งวดผ่อนชำระ (เดือน)</label>
                  <div class="col-sm-6">
                    <input
                      type="number"
                      class="form-control"
                      id="pay_common"
                      name="br_months_pay_common"
                      min="1"
                      max="<?php echo (int)$sys['st_max_months_common']; ?>"
                      style="display:none;"
                      placeholder="สูงสุด <?php echo (int)$sys['st_max_months_common']; ?> เดือน"
                    >
                    <input
                      type="number"
                      class="form-control"
                      id="pay_emergency"
                      name="br_months_pay_emergency"
                      min="1"
                      max="<?php echo (int)$sys['st_max_months_emergency']; ?>"
                      style="display:none;"
                      placeholder="สูงสุด <?php echo (int)$sys['st_max_months_emergency']; ?> เดือน"
                    >
                  </div>
                </div>
              </div>

              <div id="reset_section" style="display:none;" class="mb-3">
                <div class="alert alert-danger border-danger py-3">
                  <div class="d-flex gap-2">
                    <i class="fas fa-sync-alt text-danger mt-1 fs-5"></i>
                    <div class="w-100">
                      <strong class="text-danger">คำเตือน: มีสัญญาเงินกู้ประเภทนี้ที่ยังค้างชำระ</strong>
                      <p class="mb-2 mt-1 small" id="reset_info_text"></p>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirm_reset">
                        <label class="form-check-label fw-semibold text-danger" for="confirm_reset">
                          ฉันเข้าใจและยืนยันขอรีเซ็ทสัญญาเดิม — งวดที่เหลือทั้งหมดจะถูกยกเลิกเมื่อผู้ดูแลระบบอนุมัติ
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">อัตราดอกเบี้ย (% ต่อปี)</label>
                <div class="col-sm-4">
                  <input type="number" class="form-control" name="br_interest_rate" value="<?php echo htmlspecialchars(@$sys['st_interest'] ?: '0'); ?>" step="0.01" min="0" readonly>
                  <small class="text-muted">(ตามระบบ)</small>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">การค้ำประกัน</label>
                <div class="col-sm-9">
                  <select class="form-select" name="guarantee_type" id="guarantee_type" required>
                    <option value="" selected disabled>-- เลือก --</option>
                    <option value="1">ใช้บุคคลค้ำประกัน</option>
                    <option value="2">ใช้หุ้นค้ำประกัน</option>
                  </select>
                </div>
              </div>

              <div id="guarantor_section" style="display:none;">
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">ผู้ค้ำคนที่ 1</label>
                  <div class="col-sm-9 position-relative">
                    <input type="text" class="form-control" id="guarantor_1_search" placeholder="พิมพ์ชื่อเพื่อค้นหา..." autocomplete="off">
                    <input type="hidden" name="guarantor_1" id="guarantor_1_name">
                    <input type="hidden" name="guarantor_1_id" id="guarantor_1_id">
                    <div id="guarantor1Dropdown" class="list-group position-absolute w-100 shadow" style="display:none; max-height:200px; overflow-y:auto; z-index:10;"></div>
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">ผู้ค้ำคนที่ 2</label>
                  <div class="col-sm-9 position-relative">
                    <input type="text" class="form-control" id="guarantor_2_search" placeholder="พิมพ์ชื่อเพื่อค้นหา..." autocomplete="off">
                    <input type="hidden" name="guarantor_2" id="guarantor_2_name">
                    <input type="hidden" name="guarantor_2_id" id="guarantor_2_id">
                    <div id="guarantor2Dropdown" class="list-group position-absolute w-100 shadow" style="display:none; max-height:200px; overflow-y:auto; z-index:10;"></div>
                  </div>
                </div>
                <p class="text-muted small">เมื่อยื่นคำขอแล้ว ผู้ค้ำทั้งสองจะต้องเข้าสู่ระบบเพื่ออนุมัติ/รับรองก่อนส่งให้เจ้าหน้าที่พิจารณา</p>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">เหตุผลการกู้</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="br_details" rows="3" required placeholder="ระบุเหตุผล/รายละเอียด..."></textarea>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> ยื่นคำขอ</button>
                <a href="index.php" class="btn btn-outline-secondary">ยกเลิก</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  var guarantorList = <?php echo json_encode($guarantor_list, JSON_UNESCAPED_UNICODE); ?> || [];
  var myMemStatus = '<?php echo (int)$member["mem_status"]; ?>';
  var memberStock = <?php echo isset($member['mem_amount_stock']) ? (int)$member['mem_amount_stock'] : 0; ?>;
  var maxCommon = myMemStatus == 2 ? <?php echo (int)@$sys['st_max_amount_common_teacher']; ?> : <?php echo (int)@$sys['st_max_amount_common_officer']; ?>;
  var activeLoansData = <?php echo json_encode($active_loans, JSON_UNESCAPED_UNICODE); ?>;

  function renderGuarantor(q, dropdown, nameEl, idEl, searchEl) {
    var list = guarantorList.filter(function(m) {
      if (!q) return true;
      return (m.mem_name && m.mem_name.toLowerCase().indexOf(q.toLowerCase()) !== -1) || (m.mem_id && m.mem_id.indexOf(q) !== -1);
    });
    dropdown.innerHTML = '';
    list.forEach(function(m) {
      var a = document.createElement('a');
      a.href = '#'; a.className = 'list-group-item list-group-item-action';
      a.textContent = m.mem_name;
      a.onclick = function(e) { e.preventDefault(); nameEl.value = m.mem_name; idEl.value = m.mem_id; searchEl.value = m.mem_name; dropdown.style.display = 'none'; };
      dropdown.appendChild(a);
    });
    dropdown.style.display = list.length ? 'block' : 'none';
  }

  var brTypeEl = document.getElementById('br_type');
  if (brTypeEl) {
    brTypeEl.onchange = function() {
      var d = document.getElementById('loan_details');
      d.style.display = 'block';
      var v = this.value;
      document.getElementById('amt_common').style.display = v == '1' ? 'block' : 'none';
      document.getElementById('amt_emergency').style.display = v == '2' ? 'block' : 'none';
      document.getElementById('pay_common').style.display = v == '1' ? 'block' : 'none';
      document.getElementById('pay_emergency').style.display = v == '2' ? 'block' : 'none';
      document.getElementById('amt_common').required = v == '1';
      document.getElementById('amt_emergency').required = v == '2';
      document.getElementById('pay_common').required = v == '1';
      document.getElementById('pay_emergency').required = v == '2';
      if (v == '1') { document.getElementById('amt_common').max = maxCommon; }

      // ตรวจสอบสัญญาที่ยังค้างชำระสำหรับประเภทที่เลือก
      var activeLoan = (activeLoansData && activeLoansData[v]) ? activeLoansData[v] : null;
      var resetSection = document.getElementById('reset_section');
      var brIsReset = document.getElementById('br_is_reset');
      var brResetBrId = document.getElementById('br_reset_br_id');
      var confirmReset = document.getElementById('confirm_reset');

      if (activeLoan) {
        var typeName = v == '1' ? 'เงินกู้สามัญ' : 'เงินกู้ฉุกเฉิน';
        document.getElementById('reset_info_text').innerHTML =
          'สัญญา' + typeName + ' เลขที่ <strong>#' + activeLoan.br_id + '</strong>' +
          ' ยังเหลืออีก <strong>' + activeLoan.unpaid_count + '/' + activeLoan.br_months_pay + ' งวด</strong>' +
          ' — หากดำเนินการต่อ งวดที่เหลือทั้งหมดจะถูกยกเลิกโดยอัตโนมัติเมื่อผู้ดูแลอนุมัติคำขอใหม่นี้';
        resetSection.style.display = 'block';
        brIsReset.value = '1';
        brResetBrId.value = activeLoan.br_id;
        if (confirmReset) confirmReset.checked = false;
      } else {
        resetSection.style.display = 'none';
        brIsReset.value = '0';
        brResetBrId.value = '';
        if (confirmReset) confirmReset.checked = false;
      }
    };
  }

  var guarTypeEl = document.getElementById('guarantee_type');
  if (guarTypeEl) {
    guarTypeEl.onchange = function() {
      var v = this.value;
      var g = document.getElementById('guarantor_section');
      g.style.display = v == '1' ? 'block' : 'none';
    };
  }

  ['guarantor_1','guarantor_2'].forEach(function(prefix) {
    var search = document.getElementById(prefix + '_search');
    var drop = document.getElementById(prefix + 'Dropdown');
    var nameEl = document.getElementById(prefix + '_name');
    var idEl = document.getElementById(prefix + '_id');
    if (!search || !drop) return;
    search.onfocus = function() { renderGuarantor(search.value, drop, nameEl, idEl, search); };
    search.oninput = function() { renderGuarantor(search.value, drop, nameEl, idEl, search); };
    search.onblur = function() { setTimeout(function() { drop.style.display = 'none'; }, 200); };
  });

  var form = document.getElementById('formBorrow');
  if (form) {
    form.addEventListener('submit', function(e) {
      // ถ้ามีสัญญาที่ต้องรีเซ็ท ต้องยืนยันก่อนส่งฟอร์ม
      var brIsResetVal = document.getElementById('br_is_reset').value;
      if (brIsResetVal == '1') {
        var confirmReset = document.getElementById('confirm_reset');
        if (!confirmReset || !confirmReset.checked) {
          alert('กรุณายืนยันการรีเซ็ทสัญญาเดิมก่อนดำเนินการต่อ\n(ติ๊กเครื่องหมายยืนยันในกล่องแจ้งเตือนสีแดง)');
          e.preventDefault();
          return false;
        }
      }

      var gType = document.getElementById('guarantee_type').value;
      var bType = document.getElementById('br_type').value;
      var amount = 0;
      if (bType === '1') {
        amount = parseFloat(document.getElementById('amt_common').value || '0');
      } else if (bType === '2') {
        amount = parseFloat(document.getElementById('amt_emergency').value || '0');
      }

      if (gType === '2') {
        if (memberStock <= 0) {
          alert('ไม่สามารถใช้หุ้นค้ำประกันได้ เนื่องจากยังไม่มีจำนวนเงินหุ้นสะสม');
          e.preventDefault();
          return false;
        }
        if (amount <= 0 || isNaN(amount)) {
          alert('กรุณากรอกวงเงินกู้ให้ถูกต้องก่อน');
          e.preventDefault();
          return false;
        }
        if (amount > memberStock) {
          alert('วงเงินที่จะกู้ต้องไม่มากกว่าจำนวนเงินหุ้น (' + memberStock.toLocaleString() + ' บาท)');
          e.preventDefault();
          return false;
        }
      }
    });
  }
})();
</script>

<?php include('../includes/footer.php'); ?>
