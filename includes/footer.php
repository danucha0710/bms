</div> </div> </div> <footer class="bg-white text-center text-lg-start border-top mt-auto py-3">
  <div class="text-center text-secondary">
    Copyright &copy; <?php echo date('Y'); ?> 
    <span class="text-primary fw-bold">BPCC Borrow Money System</span>. All rights reserved.
  </div>
</footer>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล (ถ้าเปิดอยู่)
if(isset($condb)){ mysqli_close($condb); }
?>

<!-- Toast แจ้งเตือนมุมขวาบน -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080; pointer-events: none;">
  <div id="statusToast" class="toast text-bg-success border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true" style="pointer-events: auto;">
    <div class="d-flex align-items-center">
      <div class="toast-body d-flex align-items-center gap-2" id="statusToastMessage">
        <!-- ข้อความแจ้งเตือนจะถูกเติมด้วย JS -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="../assets/plugins/datatables/js/jquery-3.5.1.js"></script>

<script src="../assets/plugins/bootstrap-5/bootstrap.bundle.min.js"></script>

<script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables/js/dataTables.bootstrap5.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(event) {
    
    // 1. Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }
    
    // 2. DataTable Setup (หน้า mustpay ใช้ order ตามกำหนดชำระ)
    if ($.fn.DataTable) {
        var dtOrder = (typeof window.MUSTPAY_ORDER !== 'undefined') ? window.MUSTPAY_ORDER : [[0, "desc"]];
        $('#tableSearch').DataTable({
            "order": dtOrder, 
            "pageLength": 25, 
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            "language": {
                "search": "ค้นหา:",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                "paginate": { 
                    "first": "หน้าแรก", 
                    "last": "หน้าสุดท้าย", 
                    "next": "ถัดไป", 
                    "previous": "ก่อนหน้า" 
                },
                "zeroRecords": "ไม่พบข้อมูล",
                "infoEmpty": "ไม่มีข้อมูลแสดง",
                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
            }
        });
    }
});
</script>

<?php 
// รายการข้อความแจ้งเตือนทั้งหมด
$alerts = [
    'save_ok' => 'บันทึกข้อมูลสำเร็จ',
    'mem_add' => 'เพิ่มข้อมูลสมาชิกสำเร็จ',
    'mem_edit' => 'แก้ไขข้อมูลสมาชิกสำเร็จ',
    'mem_editp' => 'แก้ไขข้อมูลส่วนตัวสำเร็จ',
    'mem_del' => 'ลบข้อมูลสมาชิกสำเร็จ',
    'type_add' => 'เพิ่มข้อมูลประเภทสำเร็จ',
    'type_edit' => 'แก้ไขข้อมูลประเภทสำเร็จ',
    'type_del' => 'ลบข้อมูลประเภทสำเร็จ',
    'product_add' => 'เพิ่มข้อมูลสินค้าสำเร็จ',
    'product_edit' => 'แก้ไขข้อมูลสินค้าสำเร็จ',
    'product_del' => 'ลบข้อมูลสินค้าสำเร็จ',
    'error' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง'
];

// ตรวจสอบ $_GET เพื่อสั่งแสดง Toast
foreach ($alerts as $key => $message) {
    if(isset($_GET[$key])){
        // กำหนดสีและไอคอน (ถ้าเป็น error ให้เป็นสีแดง)
        $bgClass = ($key == 'error') ? 'text-bg-danger' : 'text-bg-success';
        $iconClass = ($key == 'error') ? 'fa-times-circle' : 'fa-check-circle';
?>
    <script>
        // แสดง Toast แจ้งเตือนมุมขวาบน ด้วย Bootstrap 5
        document.addEventListener("DOMContentLoaded", function() {
            var toastEl = document.getElementById('statusToast');
            var bodyEl = document.getElementById('statusToastMessage');
            if (!toastEl || !bodyEl || typeof bootstrap === 'undefined') return;

            // ปรับสีพื้นหลังตามสถานะ (สำเร็จ / ผิดพลาด)
            toastEl.classList.remove('text-bg-success', 'text-bg-danger');
            toastEl.classList.add('<?php echo $bgClass; ?>');

            // ใส่ข้อความ + ไอคอน
            bodyEl.innerHTML = '<i class="fas <?php echo $iconClass; ?>"></i> <?php echo $message; ?>';

            // แสดง Toast (หายไปเองใน ~3 วินาที)
            var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        });
    </script>
<?php 
        break; // หยุดลูปเมื่อเจอข้อความแรก
    }
}
?>

</body>
</html>