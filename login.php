<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BPCC BM System | เข้าสู่ระบบ</title>
  
  <link rel="stylesheet" href="assets/plugins/bootstrap-5/bootstrap.min.css"> 
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  
  <style>
    body {
        /* พื้นหลังแบบเต็มจอและไม่เพี้ยน */
        background-image: url("assets/img/bg.jpeg"); /* ชี้ไปที่ assets/img */
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-position: center;
        height: 100vh; /* ความสูงเต็มหน้าจอ */
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Sarabun', sans-serif; /* แนะนำให้หา Font ไทยสวยๆ มาใส่ */
    }

    .login-card {
        width: 100%;
        max-width: 400px; /* ความกว้างสูงสุด */
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2); /* เงาสวยๆ */
        background-color: rgba(255, 255, 255, 0.95); /* พื้นหลังขาวโปร่งแสงนิดๆ */
        padding: 20px;
    }

    .login-logo {
        width: 150px;
        margin-bottom: 20px;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #0d6efd; /* สี Primary */
    }

    .form-control {
        border-left: none;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <div class="card-body text-center">
      <img src="assets/img/bpcc_logo.png" class="login-logo" alt="Logo">
      
      <h4 class="mb-4 fw-bold text-secondary">เข้าสู่ระบบ</h4>
      
      <form action="check_login.php" method="post">
        
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fas fa-user"></i></span>
          <input type="text" class="form-control" name="mem_id" placeholder="ชื่อผู้ใช้งาน (Username)" required autofocus>
        </div>

        <div class="input-group mb-4">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" class="form-control" name="mem_password" placeholder="รหัสผ่าน (Password)" required>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
             <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
          </button>
        </div>

      </form>
      
      <div class="mt-3 text-muted small">
         BPCC Borrowing Management System &copy; <?php echo date('Y'); ?>
      </div>
    </div>
  </div>

  <script src="assets/plugins/bootstrap-5/bootstrap.bundle.min.js"></script>

</body>
</html>