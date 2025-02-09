<?php 
include('../condb.php');
 //echo "<pre>";
 //print_r($_POST);
 //echo "</pre>";
 //exit();

@$borrow = $_POST['borrow'];
if ($borrow == "add"){
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);
	$br_type = mysqli_real_escape_string($condb,$_POST["br_type"]);
	if(!empty($_POST["br_amount_common"])) {
		$br_amount = mysqli_real_escape_string($condb,$_POST["br_amount_common"]);
	}
	if(!empty($_POST["br_amount_emergency"])) {
		$br_amount= mysqli_real_escape_string($condb,$_POST["br_amount_emergency"]);
	}
	if(!empty($_POST["br_months_pay_common"])) {
		$br_months_pay = mysqli_real_escape_string($condb,$_POST["br_months_pay_common"]);
	}
	if(!empty($_POST["br_months_pay_emergency"])) {
		$br_months_pay = mysqli_real_escape_string($condb,$_POST["br_months_pay_emergency"]);
	}
	$guarantee_type = mysqli_real_escape_string($condb,$_POST["guarantee_type"]);
	$guarantor_1 = mysqli_real_escape_string($condb,$_POST["guarantor_1"]);
	$guarantor_2 = mysqli_real_escape_string($condb,$_POST["guarantor_2"]);
	$br_details = mysqli_real_escape_string($condb,$_POST["br_details"]);
	$date = date("Y-m-d H:i:s");

	if($guarantee_type == 1) {
		$sql = "INSERT INTO borrow_request(
		mem_id,
		br_type,
		br_amount,
		br_months_pay,
		guarantee_type,
		guarantor_1,
		guarantor_2,
		br_details,
		br_date_request)
		VALUES(
		'$mem_id',
		'$br_type',
		'$br_amount',
		'$br_months_pay',
		'$guarantee_type',
		'$guarantor_1',
		'$guarantor_2',
		'$br_details',
		'$date')";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	}
	elseif($guarantee_type == 2) {
		$sql = "INSERT INTO borrow_request(
		mem_id,
		br_type,
		br_amount,
		br_months_pay,
		guarantee_type,
		br_details,
		br_date_request)
		VALUES(
		'$mem_id',
		'$br_type',
		'$br_amount',
		'$br_months_pay',
		'$guarantee_type',
		'$br_details',
		'$date')";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	}

	if($result){
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'borrow_request.php'; ";
		echo "</script>";
	}
	else{
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'borrow_db.php'; ";
		echo "</script>";
	}
}
elseif ($borrow == "approve"){
	$br_id = mysqli_real_escape_string($condb,$_POST["br_id"]);
	$br_amount = mysqli_real_escape_string($condb,$_POST["br_amount"]);
	$br_months_pay = mysqli_real_escape_string($condb,$_POST["br_months_pay"]);
	$br_status = mysqli_real_escape_string($condb,$_POST["br_status"]);
	$br_approve_by = mysqli_real_escape_string($condb,$_POST["br_approve_by"]);
	$st_dateline = mysqli_real_escape_string($condb,$_POST["st_dateline"]);
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);

	if(!empty($_POST["br_interest_rate"])) {
		$br_interest_rate = mysqli_real_escape_string($condb,$_POST["br_interest_rate"]);
	}
	if(!empty($_POST["br_respond"])) {
		$br_respond = mysqli_real_escape_string($condb,$_POST["br_respond"]);
	}
	$date = date("Y-m-d H:i:s");

	if($br_status == 1) {
		$sql = "UPDATE borrow_request SET 
		br_amount = $br_amount,
		br_months_pay = $br_months_pay,
		br_status = $br_status,
		br_approve_by = '$br_approve_by',
		br_interest_rate = $br_interest_rate,
		br_date_approve = '$date'
		WHERE borrow_request.br_id = $br_id";

		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		$sql1 = "SELECT * FROM borrow_request
		INNER JOIN member ON borrow_request.mem_id=member.mem_id
		WHERE borrow_request.br_id = $br_id";
		$result1 = mysqli_query($condb, $sql1);
		$row = mysqli_fetch_array($result1, MYSQLI_ASSOC);

		$sql2 = "SELECT * FROM system";
		$result2 = mysqli_query($condb, $sql2);
		$row_system = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		
		// Borrow Month Lists
		$br_amount = $row['br_amount'];
		$br_months_pay = $row['br_months_pay'];
		$br_interest_rate = $row['br_interest_rate'];
		$mem_status = $row['mem_status'];
		$st_amount_cost_teacher = $row_system['st_amount_cost_teacher'];
		$st_amount_cost_officer = $row_system['st_amount_cost_officer'];

		if($mem_status == 2){
			$year_now = date("Y");
			$month_now = date("m");
			for($i=1; $i<=$br_months_pay; $i++) {
				$interest_rate = $br_interest_rate/12;
				$bw_interest = ceil(($br_amount*$interest_rate)/100);	//ปัดเศษขึ้นทั้งหมด
				$br_per_months = $st_amount_cost_teacher+$bw_interest;
				$br_amount = $br_amount-$st_amount_cost_teacher;
				$round_pay = "$i"."/"."$br_months_pay";
				$a = floor($i/12);
				$b = $i%12;
				$year = $year_now+$a;
				$month = $month_now+$b;
				if($month > 12) {
					$month = $month-12;
					$year = $year+1;
				}
				$dateline = "$year"."-"."$month"."-"."$st_dateline";
				//echo $br_id .'/'. $mem_id.'/'.$br_per_months.'/'.$bw_interest.'/'.$round_pay.'/'.$dateline.'<br>';
				$sql2 = "INSERT INTO borrowing (bw_id, br_id, mem_id, bw_amount, bw_interest, bw_round, bw_date_pay, bw_status, bw_date) 
				VALUES (NULL, $br_id, $mem_id, $br_per_months, $bw_interest, '$round_pay','$dateline' , 0, '')";
				$result1 = mysqli_query($condb, $sql2);
			}
		}
		if($mem_status == 3){
			$year_now = date("Y");
			$month_now = date("m");
			for($i=1; $i<=$br_months_pay; $i++) {
				$interest_rate = $br_interest_rate/12;
				$bw_interest = ceil(($br_amount*$interest_rate)/100);	//ปัดเศษขึ้นทั้งหมด
				$br_per_months = $st_amount_cost_officer+$bw_interest;
				$br_amount = $br_amount-$st_amount_cost_officer;
				$round_pay = "$i"."/"."$br_months_pay";
				$a = floor($i/12);
				$b = $i%12;
				$year = $year_now+$a;
				$month = $month_now+$b;
				if($month > 12) {
					$month = $month-12;
					$year = $year+1;
				}
				$dateline = "$year"."-"."$month"."-"."$st_dateline";
				//echo $br_id .'/'. $mem_id.'/'.$br_per_months.'/'.$bw_interest.'/'.$round_pay.'/'.$dateline.'<br>';
				$sql2 = "INSERT INTO borrowing (bw_id, br_id, mem_id, bw_amount, bw_interest, bw_round, bw_date_pay, bw_status, bw_date) 
				VALUES (NULL, $br_id, $mem_id, $br_per_months, $bw_interest, '$round_pay','$dateline' , 0, '')";
				$result1 = mysqli_query($condb, $sql2);
			}
		}

		if($result and $result1){
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_request.php'; ";
			echo "</script>";
		}
		else{
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_db.php'; ";
			echo "</script>";
		}
	}
	elseif($br_status == 2) {
		$sql = "UPDATE borrow_request SET
		br_status = $br_status,
		br_approve_by = '$br_approve_by',
		br_respond = '$br_respond',
		br_date_approve = '$date'
		WHERE borrow_request.br_id = $br_id";

		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		if($result){
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_request.php'; ";
			echo "</script>";
		}
		else{
			mysqli_close($condb);
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_db.php'; ";
			echo "</script>";
		}
	}
	else {
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "alert('กรุณาเลือก สถานะคำขอ'); ";
		echo "window.location = 'borrow_request.php'; ";
		echo "</script>";
	}
}

?>