<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BPCC BM System | Log in</title>
  <!-- Boostrap 5 -->
  <link rel="stylesheet" href="../pos/assets/plugins/bootstrap-5/bootstrap.min.css"> 
  <script src="../pos/assets/plugins/bootstrap-5/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../pos/assets/plugins/fontawesome-free/css/all.min.css">
</head>

<style>
  .hold-transition{
    background-image: url("bg.jpeg");
    background-repeat: no-repeat;
    background-size: 100%;
  }
</style>

<body class="hold-transition">
  <div class="container mt-5">
    <center>
    <div class="card text-center" style="width:400px">
      <div class="card-body">
        <img width="250 px" src="bpcc_logo.png"><br><br>
        <h4>กรุณาเข้าสู่ระบบ</h4>
        <form action="chk_login.php" method="post">
          <div class="row mt-3">
            <div class="col-sm-1">
              <span class="fas fa-user text-primary p-2"></span>
            </div>
            <div class="col-sm-11">
              <input type="text" class="form-control" name="mem_id" placeholder="Username">
            </div>  
          </div>
          <div class="row mt-3">
            <div class="col-1">
              <span class="fas fa-lock text-primary p-2"></span>
            </div>
            <div class="col-11">
              <input type="password" class="form-control" name="mem_password" placeholder="Password">
            </div>  
          </div>
          <div class="row mt-3">
            <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
          </div>
        </form>    
      </div>
    </div>
    </center>
  </div>
</body>
</html>