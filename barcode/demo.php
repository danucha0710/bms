<?php
include "src/BarcodeGenerator.php";
include "src/BarcodeGeneratorHTML.php";

$code = "000001";//รหัส Barcode ที่ต้องการสร้าง

$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
$border = 2;//กำหนดความหน้าของเส้น Barcode
$height = 50;//กำหนดความสูงของ Barcode

echo $generator->getBarcode($code , $generator::TYPE_CODE_128,$border,$height);
echo $code ;

echo "<hr>";

?>
