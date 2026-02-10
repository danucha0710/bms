<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>BPCC BM System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="bpcc-icon" sizes="76x76" href="../assets/img/apple-icon.png">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../assets/plugins/datatables/css/dataTables.bootstrap5.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Kanit:400" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="../assets/plugins/bootstrap-5/bootstrap.min.css"> 
  <!-- Highcharts.com -->
  <script src="../assets/plugins/chart/highcharts.js"></script>
  <script src="../assets/plugins/chart/data.js"></script>
  <!-- <script src="../assets/plugins/chart/exporting.js"></script> -->
  <script src="../assets/plugins/chart/accessibility.js"></script>
  
  <style>
    body {
      font-family: 'Kanit', sans-serif;
      font-size: 14px;
    }
  </style>

  <style type="text/css">
  @media print{
    .btn{
       display: none; 
    }
  }
</style>
</head>

<?php
error_reporting(error_reporting() & ~E_NOTICE);
session_start(); 
//print_r($_SESSION);
if(! isset($_SESSION["mem_status"])){
  Header("Location: ../login.php");
}
include('../condb.php');
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <div class="wrapper">
    <?php include ("navbar.php"); ?>
    <?php include ("sidebar.php"); ?>
    <div class="content-wrapper">