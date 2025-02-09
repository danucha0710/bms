<?php 
    if($value['guarantee_type'] == 1){
        echo "ค้ำประกันด้วยบุคคล";
    }
    elseif($value['guarantee_type'] == 2){
        echo "ค้ำประกันด้วยจำนวนหุ้น";
    }
?>