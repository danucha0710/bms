<?php 
include('../condb.php');
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

@$member = $_POST['member'];
if ($member == "add"){
	$query = "SELECT * FROM `system` WHERE st_id = 1" or die("Error : ".mysqli_error($condb));
	$result = mysqli_query($condb, $query);
	$value = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$mem_status = mysqli_real_escape_string($condb,$_POST["mem_status"]);
	$mem_name = mysqli_real_escape_string($condb,$_POST["mem_name"]);
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);
	$mem_phone = mysqli_real_escape_string($condb,$_POST["mem_phone"]);
	$mem_password = mysqli_real_escape_string($condb,(sha1($_POST["mem_phone"])));
	$mem_address = mysqli_real_escape_string($condb,$_POST["mem_address"]);
	$common_credit = $value['st_max_amount_common'];
	$emergency_credit = $value['st_max_amount_emergency'];
	$date1 = date("Y-m-d H:i:s");

	$check = "SELECT mem_id FROM member WHERE mem_id = '$mem_id'";
    $result1 = mysqli_query($condb, $check) or die(mysqli_error($condb));
    $num = mysqli_num_rows($result1);

    if($num != 0){
		mysqli_close($condb);
	    echo "<script>";
	    echo "window.location = 'list_mem.php?mem_add_error=mem_add_error'; ";
	    echo "</script>";
    }
	else{
		$sql = "INSERT INTO member(
		mem_id,
		mem_name,
		mem_address,
		mem_phone,
		mem_status,
		mem_password,
		common_credit,
		emergency_credit,
		mem_register_date)
		VALUES(
		'$mem_id',
		'$mem_name',
		'$mem_address',
		'$mem_phone',
		 $mem_status,
		'$mem_password',
		 $common_credit,
		 $emergency_credit,
		'$date1')";
		$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");
	}
	//exit();

	if($result){
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'list_mem.php'; ";
		echo "</script>";
	}
	else{
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'member_db.php'; ";
		echo "</script>";
	}
}
elseif ($member == "edit"){
	$mem_status = mysqli_real_escape_string($condb,$_POST["mem_status"]);
	$mem_name = mysqli_real_escape_string($condb,$_POST["mem_name"]);
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);
	$mem_password = mysqli_real_escape_string($condb,$_POST["mem_password"]);
	$mem_password_new = mysqli_real_escape_string($condb,(sha1($_POST["mem_password_new"])));
	$mem_phone = mysqli_real_escape_string($condb,$_POST["mem_phone"]);
	$mem_address = mysqli_real_escape_string($condb,$_POST["mem_address"]);
	if(!empty($_POST["mem_password_new"])) {
		$mem_password = $mem_password_new;
	}

	$sql = "UPDATE member SET 
	mem_status='$mem_status',
	mem_name='$mem_name',	
	mem_password='$mem_password',
	mem_phone='$mem_phone',
	mem_address='$mem_address'
	WHERE member.mem_id=$mem_id";

	$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

	if($result){
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'list_mem.php'; ";
		echo "</script>";
	}
	else{
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'member_db.php'; ";
		echo "</script>";
	}
}elseif($member == "edit_profile"){	
	$mem_status = mysqli_real_escape_string($condb,$_POST["mem_status"]);
	$mem_name = mysqli_real_escape_string($condb,$_POST["mem_name"]);
	$mem_id = mysqli_real_escape_string($condb,$_POST["mem_id"]);
	$mem_password = mysqli_real_escape_string($condb,$_POST["mem_password"]);
	$mem_password_new = mysqli_real_escape_string($condb,(sha1($_POST["mem_password_new"])));
	$mem_phone = mysqli_real_escape_string($condb,$_POST["mem_phone"]);
	$mem_address = mysqli_real_escape_string($condb,$_POST["mem_address"]);
	if(!empty($_POST["mem_password_new"])) {
		$mem_password = $mem_password_new;
	}

	$sql = "UPDATE member SET 
	mem_name='$mem_name',	
	mem_password='$mem_password',
	mem_phone='$mem_phone',
	mem_address='$mem_address'
	WHERE member.mem_id=$mem_id";

	$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb). "<br>$sql");

	if($result){
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'index.php'; ";
		echo "</script>";
	}
	else{
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'member_db.php'; ";
		echo "</script>";
	}
}
else{
	$mem_id  = mysqli_real_escape_string($condb,$_GET["mem_id"]);
	$sql = "DELETE FROM member WHERE member.mem_id=$mem_id";
	$result = mysqli_query($condb, $sql) or die ("Error in query: $sql " . mysqli_error($condb));	
	if($result){
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'list_mem.php'; ";
		echo "</script>";
	}
	else{
		mysqli_close($condb);
		echo "<script type='text/javascript'>";
		echo "window.location = 'member_db.php'; ";
		echo "</script>";
	}
}
?>