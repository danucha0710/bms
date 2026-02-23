<?php
session_start();
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] == '1') {
    header('Location: ../login.php');
    exit;
}
include('../config/condb.php');

$member = isset($_POST['member']) ? $_POST['member'] : '';

if ($member != 'edit_profile') {
    header('Location: index.php');
    exit;
}

// แก้ไขโปรไฟล์ตัวเอง (เฉพาะสมาชิกที่ล็อกอิน)
$mem_id       = mysqli_real_escape_string($condb, $_POST["mem_id"]);
if ($mem_id !== $_SESSION['mem_id']) {
    header('Location: index.php?error=1');
    exit;
}

$mem_username = mysqli_real_escape_string($condb, $_POST["mem_username"]);
$mem_name     = mysqli_real_escape_string($condb, $_POST["mem_name"]);
$mem_phone    = mysqli_real_escape_string($condb, $_POST["mem_phone"]);
$mem_address  = mysqli_real_escape_string($condb, $_POST["mem_address"]);

$sql_old = "SELECT mem_username FROM member WHERE mem_id = '$mem_id'";
$rs_old = mysqli_query($condb, $sql_old);
$row_old = mysqli_fetch_assoc($rs_old);

$is_password_changed = !empty($_POST["mem_password_new"]);
$is_username_changed = ($mem_username != $row_old['mem_username']);

$check_user = "SELECT mem_id FROM member WHERE mem_username = '$mem_username' AND mem_id != '$mem_id'";
$result_user = mysqli_query($condb, $check_user);
if (mysqli_num_rows($result_user) > 0) {
    echo "<script>alert('Username นี้มีผู้อื่นใช้แล้ว'); window.history.back();</script>";
    exit;
}

$password_update = "";
if ($is_password_changed) {
    $mem_password_new = password_hash($_POST["mem_password_new"], PASSWORD_DEFAULT);
    $password_update = ", mem_password='$mem_password_new'";
}

$sql = "UPDATE member SET 
        mem_username='$mem_username',
        mem_name='$mem_name',   
        mem_phone='$mem_phone',
        mem_address='$mem_address'
        $password_update
        WHERE mem_id='$mem_id'";

$result = mysqli_query($condb, $sql);

if ($result) {
    if ($is_password_changed || $is_username_changed) {
        session_destroy();
        echo "<script>";
        echo "alert('ข้อมูลสำคัญมีการเปลี่ยนแปลง กรุณาเข้าสู่ระบบใหม่อีกครั้ง');";
        echo "window.location = '../login.php';";
        echo "</script>";
        exit;
    }
    $_SESSION['mem_name'] = $mem_name;
    header("Location: index.php?mem_editp=1");
    exit;
}

header("Location: index.php?error=1");
mysqli_close($condb);
