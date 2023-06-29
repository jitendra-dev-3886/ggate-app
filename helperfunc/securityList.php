<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	if ($_SESSION['memberID'] == 0) {
		$whr = 'employeeMobileNo, employeeIDValue';
	} else {
		$whr = "concat('******', RIGHT(employeeMobileNo, 4)) as employeeMobileNo, concat('******', RIGHT(employeeIDValue, 4)) as employeeIDValue";
	}
	// print_r($_SESSION);
	$queryString = pro_db_query("select employeeID, complexID, staffTypeID, vendorID, employeeCode, employeeType, employeeName,
								employeeQualification, employeeOfficeAddress, employeeResideAddress, employeePhoneNo, employeeImage,
								employeeEmailAddress, employeeAbout, employeeIDType, employeePhotoID, qrEnrolled, isLoggedIn, status,
								" . $whr . " from complexEmployeeMaster
								where (employeeType = 1 or employeeType = 0) and status != 126 and complexID = " . $_SESSION['complexID']);
	// print_r($queryString);
	// exit();
	$statusArray = array("0" => "Inactive", "1" => "Active");
	$qrEnrolledArray = array("0" => "Not Enrolled", "1" => "Enrolled");
	$employeeIDTypeArray = array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID", "5" => "Leaving Certificate", "10" => "Other");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "employeeID:" . $res['employeeID'];

		if ($res['employeeImage'] == null || empty($res['employeeImage'])) {
			$res['employeeImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$employeeImage = '<td><img src="' . $res['employeeImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
		$employeeName = '<td>' . $res['employeeName'] . '</td>';
		$employeeMobileNo = '<td>' . $res['employeeMobileNo'] . '</td>';
		$employeeIDType = '<td>' . $employeeIDTypeArray[$res['employeeIDType']] . '</td>';
		$employeePhotoIDValue = '<td>' . $res['employeeIDValue'] . '</td>';
		$employeeCode = '<td>' . $res['employeeCode'] . '</td>';

		
		// $isLoggedIn = '<td><p class="badge">Logged Out</p></td>';
		if ($res['isLoggedIn'] == "1") {
			$isLoggedIn = '<td><a href="#" class="eisLoggedIn badge badge-danger" data-type="select" data-name="isLoggedIn" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Logout">&nbsp;Logged In&nbsp;</a></td>';
		}else {
			$isLoggedIn = '<td><p class="badge">Logged Out</p></td>';
		}
		if ($res['qrEnrolled'] == "1") {
			$qrEnrolled = '<td><a href="#" class="eqrEnrolled badge badge-info" data-type="select" data-name="qrEnrolled" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Enrollment Status">' . $qrEnrolledArray[$res['qrEnrolled']] . '</a></td>';
		} else {
			$qrEnrolled = '<td><a href="#" class="eqrEnrolled badge badge-danger" data-type="select" data-name="qrEnrolled" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Enrollment Status">' . $qrEnrolledArray[$res['qrEnrolled']] . '</a></td>';
		}
		if ($res['status'] == "1") {
			$empStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$empStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$action = '<td><a href="index.php?controller=complexmasters&action=securitymaster&subaction=editForm&employeeID=' . $res['employeeID'] . '" title="Edit"><i class="fe-edit text-warning"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						<a href="index.php?controller=complexmasters&action=securitymaster&subaction=delete&employeeID=' . $res['employeeID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$employeeImage", "$employeeName", "$employeeMobileNo", "$employeeIDType", "$employeePhotoIDValue", "$employeeCode", "$isLoggedIn", "$qrEnrolled", "$empStatus", "$action");
	}
	// End While Loop
	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>