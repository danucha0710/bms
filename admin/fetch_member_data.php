<?php
// 1. แก้ไข Path ให้ตรงกับโครงสร้างใหม่
include('../config/condb.php'); 

if (isset($_GET['mem_id'])) {
    
    // 2. ป้องกัน SQL Injection
    $mem_id = mysqli_real_escape_string($condb, $_GET['mem_id']);

    // 3. ดึงค่าราคาหุ้น (Stock Price) จากตาราง system มาก่อน เพื่อเอามาคำนวณมูลค่า
    $query_system = "SELECT st_stock_price FROM system WHERE st_id = 1";
    $rs_system = mysqli_query($condb, $query_system);
    $row_system = mysqli_fetch_assoc($rs_system);
    $stock_price = $row_system['st_stock_price']; // ราคาต่อหุ้น

    // 4. ดึงข้อมูลสมาชิก
    $query_member = "SELECT * FROM member WHERE mem_id = '$mem_id'";
    $rs_member = mysqli_query($condb, $query_member);
    $row_member = mysqli_fetch_assoc($rs_member);

    if ($row_member) {
        // คำนวณมูลค่าหุ้น (จำนวนหุ้น * ราคาต่อหุ้น)
        $stock_count = (int) $row_member['mem_amount_stock'];
        $stock_value = $stock_count * $stock_price;
        $common_credit = (int) $row_member['mem_common_credit'];
        $emergency_credit = (int) $row_member['mem_emergency_credit'];

        // รูปแบบแสดงใน Modal ใช้หุ้นค้ำประกัน: 3 บรรทัด
        echo "<p class='mb-2'><strong>มูลค่าหุ้น " . number_format($stock_value, 2) . " บาท</strong> (" . number_format($stock_count) . " หุ้น)</p>";
        echo "<p class='mb-2'>วงเงินกู้สามัญ " . number_format($common_credit) . " บาท</p>";
        echo "<p class='mb-0'>วงเงินกู้ฉุกเฉิน " . number_format($emergency_credit) . " บาท</p>";
    } else {
        echo "<p class='text-danger mb-0'>ไม่พบข้อมูลสมาชิกรหัสนี้</p>";
    }

    mysqli_close($condb);
}
?>