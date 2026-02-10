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
        $stock_count = $row_member['mem_amount_stock'];
        $stock_value = $stock_count * $stock_price;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row_member['mem_name']) . "</td>";
        echo "<td class='text-center'>" . $row_member['mem_phone'] . "</td>";
        
        // แสดงมูลค่าหุ้น (บาท) และจำนวนหน่วยในวงเล็บ
        echo "<td class='text-end'>
                <span class='fw-bold text-success'>" . number_format($stock_value, 2) . "</span> 
                <small class='text-muted'>(" . number_format($stock_count) . " หุ้น)</small>
              </td>";
        
        // แสดงวงเงินกู้สามัญ
        echo "<td class='text-end text-primary'>" . number_format($row_member['mem_common_credit']) . "</td>";
        
        // แสดงวงเงินกู้ฉุกเฉิน
        echo "<td class='text-end text-danger'>" . number_format($row_member['mem_emergency_credit']) . "</td>";
        echo "</tr>";
        
    } else {
        echo "<tr><td colspan='5' class='text-center text-danger'>ไม่พบข้อมูลสมาชิกรหัสนี้</td></tr>";
    }

    mysqli_close($condb);
}
?>