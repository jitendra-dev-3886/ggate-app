<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$requestData = $_REQUEST;

	/* Now Build Where Condition */
	$whr = "";
	if (isset($requestData['currentDateTime']) || isset($requestData['employeeID'])) {
		if (isset($requestData['currentDateTime']) && isset($requestData['employeeID'])) {
			$whr .= ' where gta.complexID = "' . $_SESSION['complexID'] . '" and gta.employeeID = "' . $requestData['employeeID'] . '" and date(gta.currentDateTime) = "' . $requestData['currentDateTime'] . '"';
		} else if (isset($requestData['currentDateTime'])) {
			$whr .= ' where gta.complexID = "' . $_SESSION['complexID'] . '" and date(gta.currentDateTime) = "' . $requestData['currentDateTime'] . '"';
		} else {
			$whr .= ' where gta.complexID = "' . $_SESSION['complexID'] . '" and gta.employeeID = "' . $requestData['employeeID'] . '" and date(gta.currentDateTime) = CURRENT_DATE ';
		}
	} else {
		$whr .= ' where gta.complexID = "' . $_SESSION['complexID'] . '" and date(gta.currentDateTime) = CURRENT_DATE';
	}

	$queryString = pro_db_query("SELECT gta.*, em.employeeName, em.employeeImage, lm.locationName FROM guardTrackingActivity gta
									join complexEmployeeMaster em on gta.employeeID = em.employeeID
									join locationMaster lm on gta.locationID = lm.locationID " . $whr);
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "trackingID:" . $res['trackingID'];
		$trackingID = '<td>' . $res['trackingID'] . '</td>';

		if ($res['employeeImage'] == null || empty($res['employeeImage'])) {
			$res['employeeImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$employeeImage = '<td><img src="' . $res['employeeImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
		$employeeName = '<td>' . $res['employeeName'] . '</td>';
		$locationName = '<td>' . $res['locationName'] . '</td>';
		$currentDateTime = '<td>' . date('d-M-Y H:i A', strtotime($res['currentDateTime'])) . '</td>';
		$result['aaData'][] = array("$employeeImage", "$employeeName", "$locationName", "$currentDateTime");
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
