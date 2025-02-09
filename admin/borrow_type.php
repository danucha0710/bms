<?php 
    if($value['br_type'] == 1){
        echo '<p class="text-success">';
        echo "เงินกู้สามัญ";
        echo '</p>';
    }
    elseif($value['br_type'] == 2){
        echo '<p class="text-danger">';
        echo "เงินกู้ฉุกเฉิน";
        echo '</p>';
    }
?>