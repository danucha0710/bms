  </div>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Modify by</b> Aj.dong
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

<script>
$(document).ready(function() {
  $('#tableSearch').DataTable({
    "order": [[0, "desc"]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
  });
});
</script>