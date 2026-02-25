<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container-fluid">

    <button type="button" id="sidebarCollapse" class="btn btn-light border text-secondary me-3">
      <i class="fas fa-bars"></i>
    </button>

    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link <?php if ($menu == "index"){ echo "active fw-bold text-primary"; } ?>" href="index.php">
          <i class="fas fa-home"></i> หน้าแรก
        </a>
      </li>
      </ul>

    <ul class="navbar-nav ms-auto">
      
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link text-secondary">
            ยินดีต้อนรับ: <?php echo htmlspecialchars($_SESSION['mem_name']); ?>
        </span>
      </li>image.png
    </ul>

  </div>
</nav>