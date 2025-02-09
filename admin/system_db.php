<?php 
include('../condb.php');
 //echo "<pre>";
 //print_r($_POST);
 //echo "</pre>";
 //exit();

@$system = $_POST['system'];

$st_max_amount_common = mysqli_real_escape_string($condb,$_POST["st_max_amount_common"]);
$st_max_amount_emergency = mysqli_real_escape_string($condb,$_POST["st_max_amount_emergency"]);
$st_amount_cost_teacher = mysqli_real_escape_string($condb,$_POST["st_amount_cost_teacher"]);
$st_amount_cost_officer = mysqli_real_escape_string($condb,$_POST["st_amount_cost_officer"]);
$st_max_months_common = mysqli_real_escape_string($condb,$_POST["st_max_months_common"]);
$st_max_months_emergency = mysqli_real_escape_string($condb,$_POST["st_max_months_emergency"]);
$st_interest = mysqli_real_escape_string($condb,$_POST["st_interest"]);
$st_stock_price = mysqli_real_escape_string($condb,$_POST["st_stock_price"]);
$st_dividend_rate = mysqli_real_escape_string($condb,$_POST["st_dividend_rate"]);
$st_average_return_rate = mysqli_real_escape_string($condb,$_POST["st_average_return_rate"]);
$st_dateline = mysqli_real_escape_string($condb,$_POST["st_dateline"]);

$query = "SELECT * FROM `system` WHERE st_id = 1" or die("Error : ".mysqli_error($condb));
$result = mysqli_query($condb, $query);
$value = mysqli_fetch_array($result, MYSQLI_ASSOC);

if ($system == "setting"){
	if($value['st_max_amount_common'] == $st_max_amount_common and
	$value['st_max_amount_emergency'] == $st_max_amount_emergency and
	$value['st_amount_cost_teacher'] == $st_amount_cost_teacher and
	$value['st_amount_cost_officer'] == $st_amount_cost_officer and
	$value['st_max_months_common'] == $st_max_months_common and
	$value['st_max_months_emergency'] == $st_max_months_emergency and
	$value['st_interest'] == $st_interest and
	$value['st_stock_price'] == $st_stock_price and
	$value['st_dividend_rate'] == $st_dividend_rate and
	$value['st_average_return_rate'] == $st_average_return_rate and
	$value['st_dateline'] == $st_dateline) { }
	else {
		$sql = "UPDATE `system` SET
		st_max_amount_common = $st_max_amount_common,
		st_max_amount_emergency = $st_max_amount_emergency,
		st_amount_cost_teacher = $st_amount_cost_teacher,
		st_amount_cost_officer = $st_amount_cost_officer,
		st_max_months_common = $st_max_months_common,
		st_max_months_emergency = $st_max_months_emergency,
		st_interest = $st_interest,
		st_stock_price = $st_stock_price,
		st_dividend_rate = $st_dividend_rate,
		st_average_return_rate = $st_average_return_rate,
		st_dateline = $st_dateline
		WHERE st_id = 1";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		//log
		$st_edit_by = mysqli_real_escape_string($condb,$_POST["st_edit_by"]);
		$st_date = date("Y-m-d H:i:s");
		$text = "st_max_amount_common > $st_max_amount_common
		st_max_amount_emergency > $st_max_amount_emergency
		st_amount_cost_teacher > $st_amount_cost_teacher
		st_amount_cost_officer > $st_amount_cost_officer
		st_max_months_common > $st_max_months_common
		st_max_months_emergency > $st_max_months_emergency
		st_interest > $st_interest
		st_stock_price > $st_stock_price
		st_dividend_rate > $st_dividend_rate
		st_average_return_rate > $st_average_return_rate
		st_dateline > $st_dateline";

		$sql1 = "INSERT INTO `borrow_log` (bl_id, mem_id, bl_text, bl_date) 
		VALUES (NULL, '$st_edit_by', 'แก้ไขการตั้งค่าระบบ $text', '$st_date')";
		$result1 = mysqli_query($condb, $sql1) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		if($result and $result1){
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'system.php'; ";
			echo "</script>";
		}
		else{
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'system_db.php'; ";
			echo "</script>";
		}
	}
	echo "<script type='text/javascript'>";
	echo "window.location = 'system.php'; ";
	echo "</script>";
}
else {
	mysqli_close($condb);
	echo "<script type='text/javascript'>";
	echo "alert('ไม่สามารถแก้ไขได้'); ";
	echo "window.location = 'system.php'; ";
	echo "</script>";
}
?>