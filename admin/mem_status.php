<?php 
    if($row_member['mem_status'] == 0){
        echo "ผู้ดูแลระบบ";
    }
    elseif($row_member['mem_status'] == 1){
        echo "พนักงานคีย์ข้อมูล";
    }
    elseif($row_member['mem_status'] == 2){
        echo "ครู";
    }
    elseif($row_member['mem_status'] == 3){
        echo "เจ้าหน้าที่";
    } 
?>