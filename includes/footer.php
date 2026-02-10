<?php
mysqli_close($condb);
?>

</div>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Modify by</b> Aj.Dong
    </div>
    <strong>Copyright &copy; 2023 BPCC Borraw Money System(BMS)</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>

<!-- jQuery -->
<script src="../assets/plugins/datatables/js/jquery-3.5.1.js"></script>
<!-- Bootstrap 5 -->
<script src="../assets/plugins/bootstrap-5/bootstrap.bundle.min.js"></script>
<!-- dselect function for java script -->
<script src="../assets/plugins/dselect/dselect.js"></script>
<!-- DataTables -->
<script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables/js/dataTables.bootstrap5.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
<script src="../assets/dist/js/demo.js"></script>

<?php if(isset($_GET['save_ok'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'บันทึกคำสั่งซื้อสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['mem_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'เพิ่มข้อมูลสมาชิกสำเร็จ',
  icon: 'success',
  confirmButtonText: 'OK'
})
</script>
<?php } ?>

<?php if(isset($_GET['mem_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสมาชิกสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['mem_del'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'ลบข้อมูลสมาชิกสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['type_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'เพิ่มข้อมูลประเภทสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['type_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขมูลประเภทสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['type_del'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'ลบข้อมูลประเภทสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['brand_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'เพิ่มมูลแบรนด์สำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['brand_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลแบรนด์สำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['brand_del'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'ลบข้อมูลแบรนด์สำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['product_add'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'เพิ่มข้อมูลสินค้าสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['product_edit'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลสินค้าสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['product_del'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'ลบข้อมูลสินค้าสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<?php if(isset($_GET['mem_editp'])){ ?>
<script>
  Swal.fire({
  title: 'สำเร็จ',
  text: 'แก้ไขข้อมูลส่วนตัวสำเร็จ',
  icon: 'success',
  confirmButtonText: 'ตกลง'
})
</script>
<?php } ?>

<script>
$(document).ready(function() {
  $('#tableSearch').DataTable({
    "order": [[0, "desc"]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
  });
});
</script>
