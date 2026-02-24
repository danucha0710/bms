<?php 
session_start();
ob_start(); // กันข้อความ/error แว็บก่อน redirect

// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['mem_id']) || $_SESSION['mem_status'] != '0') {
    header("Location: ../login.php");
    exit();
}

include('../config/condb.php');
 //echo "<pre>";
 //print_r($_POST);
 //echo "</pre>";
 //exit();

$borrow = isset($_POST['borrow']) ? $_POST['borrow'] : '';
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
	// ไม่ใช้ guarantor_1 / guarantor_2 ในระบบอีกต่อไป เก็บว่างไว้เพื่อให้โครงสร้างตารางเดิมทำงานได้
	$guarantor_1 = '';
	$guarantor_2 = '';
	$guarantor_1_id = isset($_POST["guarantor_1_id"]) ? mysqli_real_escape_string($condb,$_POST["guarantor_1_id"]) : '';
	$guarantor_2_id = isset($_POST["guarantor_2_id"]) ? mysqli_real_escape_string($condb,$_POST["guarantor_2_id"]) : '';
	$br_details = mysqli_real_escape_string($condb,$_POST["br_details"]);
	$date = date("Y-m-d H:i:s");
	$br_interest_rate = isset($_POST["br_interest_rate"]) && $_POST["br_interest_rate"] !== '' ? (float)$_POST["br_interest_rate"] : 0;

	// Type casting สำหรับตัวเลขเพื่อป้องกัน SQL Injection
	$br_type = (int)$br_type;
	$br_amount = (float)$br_amount;
	$br_months_pay = (int)$br_months_pay;
	$guarantee_type = (int)$guarantee_type;
	
	if($guarantee_type == 1) {
		// ใช้ผู้ค้ำประกันแบบบุคคล: ใช้ mem_id ผู้ค้ำผ่าน guarantor_1_id / guarantor_2_id และแจ้งไปยังผู้ค้ำให้ยืนยัน
		$sql = "INSERT INTO borrow_request(
		mem_id,
		br_type,
		br_amount,
		br_months_pay,
		guarantee_type,
		guarantor_1_id,
		guarantor_2_id,
		guarantor_1_approve,
		guarantor_2_approve,
		br_details,
		br_interest_rate,
		br_date_request)
		VALUES(
		'$mem_id',
		$br_type,
		$br_amount,
		$br_months_pay,
		$guarantee_type,
		" . ($guarantor_1_id ? "'$guarantor_1_id'" : "NULL") . ",
		" . ($guarantor_2_id ? "'$guarantor_2_id'" : "NULL") . ",
		0,
		0,
		'$br_details',
		$br_interest_rate,
		'$date')";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		// ถ้าบันทึกสำเร็จ ให้สร้างการแจ้งเตือนให้ผู้ค้ำทั้ง 2 คน
		if ($result) {
			$br_id_new = mysqli_insert_id($condb);
			$alert_date = date("Y-m-d H:i:s");
			$alert_msg = "คุณถูกระบุเป็นผู้ค้ำประกันคำขอกู้เลขที่ $br_id_new กรุณาเข้าสู่ระบบเพื่อยืนยันการค้ำประกัน";

			if ($guarantor_1_id) {
				$msg1 = mysqli_real_escape_string($condb, $alert_msg);
				$mem1 = mysqli_real_escape_string($condb, $guarantor_1_id);
				mysqli_query($condb, "INSERT INTO borrow_alert (mem_id, ba_message, ba_date, ba_read_status) VALUES ('$mem1', '$msg1', '$alert_date', 0)");
			}
			if ($guarantor_2_id) {
				$msg2 = mysqli_real_escape_string($condb, $alert_msg);
				$mem2 = mysqli_real_escape_string($condb, $guarantor_2_id);
				mysqli_query($condb, "INSERT INTO borrow_alert (mem_id, ba_message, ba_date, ba_read_status) VALUES ('$mem2', '$msg2', '$alert_date', 0)");
			}
		}
	}
	elseif($guarantee_type == 2) {
		$sql = "INSERT INTO borrow_request(
		mem_id,
		br_type,
		br_amount,
		br_months_pay,
		guarantee_type,
		br_details,
		br_interest_rate,
		br_date_request)
		VALUES(
		'$mem_id',
		$br_type,
		$br_amount,
		$br_months_pay,
		$guarantee_type,
		'$br_details',
		$br_interest_rate,
		'$date')";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	}

	if($result){
		mysqli_close($condb);
		ob_end_clean();
		echo "<script type='text/javascript'>";
		echo "window.location = 'borrow_request.php'; ";
		echo "</script>";
		exit;
	}
	else{
		mysqli_close($condb);
		ob_end_clean();
		echo "<script type='text/javascript'>";
		echo "window.location = 'borrow_db.php'; ";
		echo "</script>";
		exit;
	}
}
elseif ($borrow == "approve"){
	$br_id = mysqli_real_escape_string($condb,$_POST["br_id"]);
	$br_amount = mysqli_real_escape_string($condb,$_POST["br_amount"]);
	$br_months_pay = mysqli_real_escape_string($condb,$_POST["br_months_pay"]);
	$br_status = mysqli_real_escape_string($condb,$_POST["br_status"]);
	$br_approve_by = isset($_POST["br_approve_by"]) ? mysqli_real_escape_string($condb, $_POST["br_approve_by"]) : (isset($_POST["by_id"]) ? mysqli_real_escape_string($condb, $_POST["by_id"]) : '');
	$st_dateline = mysqli_real_escape_string($condb,$_POST["st_dateline"]);
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);

	if(!empty($_POST["br_interest_rate"])) {
		$br_interest_rate = mysqli_real_escape_string($condb,$_POST["br_interest_rate"]);
	}
	$br_respond = isset($_POST["br_respond"]) ? mysqli_real_escape_string($condb, $_POST["br_respond"]) : '';
	$date = date("Y-m-d H:i:s");

	if($br_status == 1) {
		// Type casting เพื่อป้องกัน SQL Injection สำหรับตัวเลข
		$br_id = (int)$br_id;
		$br_amount = (float)$br_amount;
		$br_months_pay = (int)$br_months_pay;
		$br_status = (int)$br_status;
		$br_interest_rate = isset($br_interest_rate) ? (float)$br_interest_rate : 0;
		
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

		// ตรวจสอบเงื่อนไขก่อนอนุมัติ: ต้องมีผู้ค้ำทั้ง 2 คนอนุมัติแล้ว หรือใช้หุ้นค้ำโดยหุ้นเพียงพอ
		$br_type = (int)$row['br_type'];               // 1=สามัญ, 2=ฉุกเฉิน
		$guarantee_type_row = (int)$row['guarantee_type']; // 1=บุคคล, 2=หุ้น
		$g1_approve = isset($row['guarantor_1_approve']) ? (int)$row['guarantor_1_approve'] : 0;
		$g2_approve = isset($row['guarantor_2_approve']) ? (int)$row['guarantor_2_approve'] : 0;
		$mem_amount_stock = isset($row['mem_amount_stock']) ? (int)$row['mem_amount_stock'] : 0;

		$original_br_amount = (float)$row['br_amount'];

		$hasGuarantorsOk = ($g1_approve === 1 && $g2_approve === 1);
		$hasStockEnough = ($mem_amount_stock > $original_br_amount);

		$canApprove = false;
		if ($guarantee_type_row === 1) { // ใช้บุคคลค้ำประกัน
			// ต้องมีผู้ค้ำทั้ง 2 คนกดอนุมัติก่อนเท่านั้น
			$canApprove = $hasGuarantorsOk;
		} elseif ($guarantee_type_row === 2) { // ใช้หุ้นค้ำประกัน
			// ต้องมีจำนวนหุ้นมากกว่าวงเงินที่ต้องการกู้
			$canApprove = $hasStockEnough;
		}

		if (!$canApprove) {
			mysqli_close($condb);
			ob_end_clean();
			echo "<script type='text/javascript'>";
			echo "alert('ไม่สามารถอนุมัติได้: ต้องมีผู้ค้ำทั้ง 2 คนอนุมัติ หรือจำนวนหุ้นมากกว่าวงเงินกู้ที่ขอ');";
			echo "window.location = 'approve.php?br_id=" . (int)$br_id . "';";
			echo "</script>";
			exit;
		}

		$sql2 = "SELECT * FROM system";
		$result2 = mysqli_query($condb, $sql2);
		$row_system = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		
		// คำนวณเงินที่ต้องจ่ายแต่ละเดือน (รายจ่ายแต่ละงวด) แล้วบันทึกลง borrowing
		// สูตร: ดอกเบี้ยต่องวด = ปัดขึ้น(ยอดคงค้าง × อัตราดอกเบี้ยต่อปี/12 / 100), ยอดชำระต่องวด = เงินต้นต่องวด + ดอกเบี้ย
		// เงินกู้สามัญ (br_type=1): เงินต้นต่องวดคงที่จากระบบ (ครู/เจ้าหน้าที่). เงินกู้ฉุกเฉิน (br_type=2): เงินต้นต่องวด = ยอดกู้/จำนวนงวด
		$br_amount = $original_br_amount;
		$br_months_pay = (int)$row['br_months_pay'];
		$br_interest_rate = (float)$row['br_interest_rate'];
		$mem_status = (int)$row['mem_status'];
		$st_amount_cost_teacher = (float)$row_system['st_amount_cost_teacher'];
		$st_amount_cost_officer = (float)$row_system['st_amount_cost_officer'];
		$interest_rate_monthly = $br_interest_rate / 12;

		$year_now = (int)date("Y");
		$month_now = (int)date("m");
		$st_dateline_safe = str_pad((int)$st_dateline, 2, '0', STR_PAD_LEFT);

		if ($mem_status != 2 && $mem_status != 3) {
			$result1 = true; // ไม่สร้างงวดสำหรับสถานะอื่น
		} else for($i = 1; $i <= $br_months_pay; $i++) {
			$bw_interest = (int)ceil(($br_amount * $interest_rate_monthly) / 100);
			if ($br_type == 2) {
				// เงินกู้ฉุกเฉิน: เงินต้นต่องวด = ยอดกู้ / จำนวนงวด (งวดสุดท้ายจ่ายยอดคงเหลือ)
				$principal_this = ($i < $br_months_pay) ? ($original_br_amount / $br_months_pay) : $br_amount;
				$br_per_months = $principal_this + $bw_interest;
				$br_amount -= $principal_this;
			} else {
				// เงินกู้สามัญ: เงินต้นต่องวดคงที่จากระบบ
				$principal_this = ($mem_status == 2) ? $st_amount_cost_teacher : $st_amount_cost_officer;
				$br_per_months = $principal_this + $bw_interest;
				$br_amount -= $principal_this;
			}
			$round_pay = $i . '/' . $br_months_pay;
			$month = $month_now + $i;
			$year = $year_now;
			while ($month > 12) { $month -= 12; $year++; }
			$dateline = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $st_dateline_safe;

			$br_id_safe = (int)$br_id;
			$mem_id_safe = mysqli_real_escape_string($condb, $mem_id);
			$br_per_months_safe = (float)$br_per_months;
			$round_pay_safe = mysqli_real_escape_string($condb, $round_pay);
			$dateline_safe = mysqli_real_escape_string($condb, $dateline);

			$sql2 = "INSERT INTO borrowing (bw_id, br_id, mem_id, bw_amount, bw_round, bw_date_pay, bw_status) 
			VALUES (NULL, $br_id_safe, '$mem_id_safe', $br_per_months_safe, '$round_pay_safe', '$dateline_safe', 0)";
			$result1 = mysqli_query($condb, $sql2);
		}

		// หลังอนุมัติ: หักวงเงินกู้จากเครดิตคงเหลือของสมาชิกตามประเภทที่กู้
		$update_credit_ok = true;
		if ($mem_status == 2 || $mem_status == 3) { // ครู / เจ้าหน้าที่ เท่านั้นที่มีวงเงิน
			$mem_id_credit = mysqli_real_escape_string($condb, $row['mem_id']);
			if ($br_type === 1) { // เงินกู้สามัญ
				$current_common = isset($row['mem_common_credit']) ? (int)$row['mem_common_credit'] : 0;
				$new_common = $current_common - (int)$original_br_amount;
				if ($new_common < 0) $new_common = 0;
				$sql_credit = "UPDATE member SET mem_common_credit = $new_common WHERE mem_id = '$mem_id_credit'";
			} elseif ($br_type === 2) { // เงินกู้ฉุกเฉิน
				$current_emergency = isset($row['mem_emergency_credit']) ? (int)$row['mem_emergency_credit'] : 0;
				$new_emergency = $current_emergency - (int)$original_br_amount;
				if ($new_emergency < 0) $new_emergency = 0;
				$sql_credit = "UPDATE member SET mem_emergency_credit = $new_emergency WHERE mem_id = '$mem_id_credit'";
			} else {
				$sql_credit = '';
			}

			if (!empty($sql_credit)) {
				$update_credit_ok = mysqli_query($condb, $sql_credit);
			}
		}

		if($result and $result1 and $update_credit_ok){
			mysqli_close($condb);
			ob_end_clean();
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_request.php'; ";
			echo "</script>";
			exit;
		}
		else{
			mysqli_close($condb);
			ob_end_clean();
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_db.php'; ";
			echo "</script>";
			exit;
		}
	}
	elseif($br_status == 2) {
		// Type casting เพื่อป้องกัน SQL Injection
		$br_id = (int)$br_id;
		$br_status = (int)$br_status;
		
		$sql = "UPDATE borrow_request SET
		br_status = $br_status,
		br_approve_by = '$br_approve_by',
		br_respond = '$br_respond',
		br_date_approve = '$date'
		WHERE borrow_request.br_id = $br_id";

		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

		if($result){
			mysqli_close($condb);
			ob_end_clean();
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_request.php'; ";
			echo "</script>";
			exit;
		}
		else{
			mysqli_close($condb);
			ob_end_clean();
			echo "<script type='text/javascript'>";
			echo "window.location = 'borrow_db.php'; ";
			echo "</script>";
			exit;
		}
	}
	else {
		mysqli_close($condb);
		ob_end_clean();
		echo "<script type='text/javascript'>";
		echo "alert('กรุณาเลือก สถานะคำขอ'); ";
		echo "window.location = 'borrow_request.php'; ";
		echo "</script>";
		exit;
	}
}

?>