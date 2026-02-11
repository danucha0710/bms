<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BPCC Borrow Money System | เข้าสู่ระบบ</title>
  
  <link rel="stylesheet" href="assets/plugins/bootstrap-5/bootstrap.min.css"> 
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  
  <style>
    body {
        /* เปลี่ยนจากรูปภาพเป็นสีพื้นหลังปกติ */
        background-color: #f4f6f9; 
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Sarabun', sans-serif;
        margin: 0;
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        border-radius: 15px;
        /* ปรับเงาให้ดูนุ่มนวลขึ้นบนพื้นหลังสีเรียบ */
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        background-color: #ffffff; /* ใช้สีขาวทึบเพื่อความชัดเจน */
        padding: 30px;
    }

    .login-logo {
        width: 120px;
        margin-bottom: 20px;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: #0d6efd;
    }

    .form-control {
        border-left: none;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        padding: 12px;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <div class="card-body text-center">
      <img src="assets/img/bpcc_logo.png" class="login-logo" alt="Logo">
      
      <h4 class="mb-4 fw-bold text-dark">เข้าสู่ระบบ</h4>
      
      <form action="check_login.php" method="post">
        
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" name="user_login" placeholder="ชื่อผู้ใช้งาน หรือ เลขบัตรประชาชน" required autofocus>
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
      
      <div class="mt-4 text-muted small">
          BPCC Borrow Money System &copy; <?php echo date('Y'); ?>
      </div>
    </div>
  </div>

  <script src="assets/plugins/bootstrap-5/bootstrap.bundle.min.js"></script>

</body>
</html>