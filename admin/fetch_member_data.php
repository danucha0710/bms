<?php
include('../condb.php'); // ไฟล์เชื่อมต่อฐานข้อมูล

if (isset($_GET['mem_id'])) {
    $mem_id = $_GET['mem_id'];

    $query_member = "SELECT * FROM member WHERE mem_id = '$mem_id'";
    $rs_member = mysqli_query($condb, $query_member);
    $row_member = mysqli_fetch_assoc($rs_member);

    if ($row_member) {
        echo "<tr>
                <td>{$row_member['mem_name']}</td>
                <td>{$row_member['mem_phone']}</td>
                <td>{$row_member['mem_amount_stock']}</td>
                <td>{$row_member['mem_common_credit']}</td>
                <td>{$row_member['mem_emergency_credit']}</td>
              </tr>";
    } else {
        echo "<tr><td colspan='5' class='text-center'>ไม่พบข้อมูล</td></tr>";
    }

    mysqli_close($condb);
}
?>
