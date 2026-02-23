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

<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="statusModalLabel">
            <i class="fas fa-check-circle"></i> สำเร็จ
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4 fs-5" id="modalMessage">
        </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ตกลง</button>
      </div>
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

// ตรวจสอบ $_GET เพื่อสั่งเปิด Modal
foreach ($alerts as $key => $message) {
    if(isset($_GET[$key])){
        // กำหนดสีและไอคอน (ถ้าเป็น error ให้เป็นสีแดง)
        $headerClass = ($key == 'error') ? 'bg-danger' : 'bg-success';
        $iconClass = ($key == 'error') ? 'fa-times-circle' : 'fa-check-circle';
        $titleText = ($key == 'error') ? 'ผิดพลาด' : 'สำเร็จ';
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. ดึง Element Modal มา
            var myModalEl = document.getElementById('statusModal');
            
            // ตรวจสอบว่ามี element จริงไหมก่อนเรียกใช้ Bootstrap Modal
            if(myModalEl) {
                var myModal = new bootstrap.Modal(myModalEl);
                
                // 2. ใส่ข้อความ
                var msgEl = document.getElementById('modalMessage');
                if(msgEl) msgEl.innerHTML = '<?php echo $message; ?>';
                
                // ปรับสี Header และ Title ตามสถานะ
                var modalHeader = myModalEl.querySelector('.modal-header');
                var modalTitle = myModalEl.querySelector('.modal-title');
                
                if(modalHeader) modalHeader.className = 'modal-header <?php echo $headerClass; ?> text-white';
                if(modalTitle) modalTitle.innerHTML = '<i class="fas <?php echo $iconClass; ?>"></i> <?php echo $titleText; ?>';

                // 3. แสดงผล Modal
                myModal.show();
            }
        });
    </script>
<?php 
        break; // หยุดลูปเมื่อเจอข้อความแรก
    }
}
?>

</body>
</html>