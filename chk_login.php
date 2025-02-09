<?php 
session_start();
  if(isset($_POST['mem_id'])){
    include("condb.php");
    $mem_id = mysqli_real_escape_string($condb,$_POST['mem_id']);
    $mem_password = mysqli_real_escape_string($condb,sha1($_POST['mem_password']));
    $chk = trim($mem_id) OR trim($mem_password);
    if($chk==''){
      echo '<script>';
      echo "alert(\"กรุณากรอกชื่อผู้ใช้งาน และรหัสผ่าน\");"; 
      echo "window.history.back()";
      echo '</script>';
    }
    else{
      $sql="SELECT * FROM member WHERE mem_id='".$mem_id."' AND mem_password='".$mem_password."' ";
      $result = mysqli_query($condb,$sql);
      //echo $sql;
      //exit();
      if(mysqli_num_rows($result)==1){
        $row = mysqli_fetch_array($result);
        $_SESSION["mem_id"] = $row["mem_id"];
        $_SESSION["mem_name"] = $row["mem_name"];
        $_SESSION["mem_status"] = $row["mem_status"];
        //print_r($_SESSION);
        //var_dump($_SESSION);

        if($_SESSION["mem_status"]=="0" OR $_SESSION["mem_status"]=="1"){
          Header("Location: admin/");
        }
        elseif($_SESSION["mem_status"]=="3" OR $_SESSION["mem_status"]=="4"){  
          Header("Location: user/");
        }
        else{
          echo "<script>";
          echo "alert(\"ไม่มีสิทธิเข้าใช้งาน กรุณาติดต่อผู้ดูแลระบบ\");"; 
          echo "window.history.back()";
          echo "</script>";
        }
      }
      else{
        echo '<script>';
        echo "alert(\"ชื่อผู้ใช้งาน หรือรหัสผ่านไม่ถูกต้อง\");"; 
        echo "window.history.back()";
        echo '</script>';
        //Header("Location: login.php");
      }
	  mysqli_close($condb);
    }
  }
?>