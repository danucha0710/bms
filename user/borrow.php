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
$sql_mem = "SELECT mem_name, mem_common_credit, mem_emergency_credit, mem_status FROM member WHERE mem_id = '$mem_id'";
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
            <div class="alert alert-light border mb-4">
              <strong>ผู้ขอกู้:</strong> <?php echo htmlspecialchars($member['mem_name']); ?>
              &nbsp;|&nbsp; วงเงินสามัญ: <?php echo number_format($member['mem_common_credit']); ?> บาท
              &nbsp;|&nbsp; วงเงินฉุกเฉิน: <?php echo number_format($member['mem_emergency_credit']); ?> บาท
            </div>

            <form action="request_save.php" method="POST" id="formBorrow">
              <input type="hidden" name="mem_id" value="<?php echo htmlspecialchars($mem_id); ?>">

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
                    <input type="number" class="form-control" id="amt_common" name="br_amount_common" min="1" style="display:none;" placeholder="สูงสุด <?php echo number_format($member['mem_status']=='2' ? $sys['st_max_amount_common_teacher'] : $sys['st_max_amount_common_officer']); ?>">
                    <input type="number" class="form-control" id="amt_emergency" name="br_amount_emergency" min="1" max="<?php echo (int)$sys['st_max_amount_emergency']; ?>" style="display:none;" placeholder="สูงสุด <?php echo number_format($sys['st_max_amount_emergency']); ?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">ผ่อนชำระ (เดือน)</label>
                  <div class="col-sm-6">
                    <input type="number" class="form-control" id="pay_common" name="br_months_pay_common" min="1" max="<?php echo (int)$sys['st_max_months_common']; ?>" style="display:none;">
                    <input type="number" class="form-control" id="pay_emergency" name="br_months_pay_emergency" min="1" max="<?php echo (int)$sys['st_max_months_emergency']; ?>" style="display:none;">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">อัตราดอกเบี้ย (ต่อปี)</label>
                <div class="col-sm-4">
                  <input type="number" class="form-control" name="br_interest_rate" value="<?php echo htmlspecialchars(@$sys['st_interest'] ?: '0'); ?>" step="0.01" min="0" readonly>
                  <small class="text-muted">% (ตามระบบ)</small>
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
  var guarantorList = <?php echo json_encode($guarantor_list); ?>;
  var myMemStatus = '<?php echo (int)$member["mem_status"]; ?>';
  var maxCommon = myMemStatus == 2 ? <?php echo (int)@$sys['st_max_amount_common_teacher']; ?> : <?php echo (int)@$sys['st_max_amount_common_officer']; ?>;

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

  document.getElementById('br_type').onchange = function() {
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
  };

  document.getElementById('guarantee_type').onchange = function() {
    var v = this.value;
    var g = document.getElementById('guarantor_section');
    g.style.display = v == '1' ? 'block' : 'none';
  };

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
})();
</script>

<?php include('../includes/footer.php'); ?>
