<?php
// ตรวจสอบว่ามี Session ทำงานอยู่หรือยัง ถ้ายังค่อยสั่ง start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(error_reporting() & ~E_NOTICE);
if(! isset($_SESSION["mem_status"])){
    Header("Location: ../login.php");
    exit();
}
include('../config/condb.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BPCC Borrow Money System</title>
  
  <link rel="icon" type="image/png" sizes="76x76" href="../assets/img/bpcc_logo.png">

  <link rel="stylesheet" href="../assets/plugins/bootstrap-5/bootstrap.min.css">
  
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  
  <link rel="stylesheet" href="../assets/plugins/datatables/css/dataTables.bootstrap5.min.css">

  <link rel="stylesheet" href="../assets/css/custom.css">

</head>
<body>

<div class="wrapper">
    <?php include(__DIR__ . '/sidebar.php'); ?>

    <div id="content">
        <?php include(__DIR__ . '/navbar.php'); ?>
        
        <div class="container-fluid py-4">