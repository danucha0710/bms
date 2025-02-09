<?php 
    if($value['br_status'] == 0){
        echo '<p class="text-info">';
        echo "ยื่นขอกู้เงิน";
        echo '</p>';
    }
    elseif($value['br_status'] == 1){
        echo '<p class="text-success">';
        echo "อนุมัติ";
        echo '</p>';
    }
    elseif($value['br_status'] == 2){
        echo '<p class="text-danger">';
        echo "ไม่อนุมัติ";
        echo '</p>';
    }
?>