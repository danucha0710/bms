<?php 
session_start();
    if(isset($_SESSION["mem_id"])){
        if($_SESSION["ref_l_id"]=="1"){
            Header("Location: admin/");
        }
        elseif($_SESSION["ref_l_id"]=="2"){  
            Header("Location: admin/");
        }
        elseif($_SESSION["ref_l_id"]=="3"){  
            Header("Location: user/");
        }
        else{
            Header("Location: login.php");
        }
    }
    else{
        Header("Location: login.php");
    }
?>