<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$statusTypeArray = array("0" => "Pending", "1" => "In-Progress", "2" => "Resolved", "Undefined");
	$requestData = $_REQUEST;

	/* Now Build Where Condition */
	$whr = "";
	if (isset($requestData['complaintDate'])) {
		$whr .= ' where c.complexID = "' . $_SESSION['complexID'] . '" and date(c.complaintDate) = "' . $requestData['complaintDate'] . '"';
	} else {
		$whr .= ' where c.complexID = "' . $_SESSION['complexID'] . '" and date(c.complaintDate) = CURRENT_DATE';
	}

	$queryString = pro_db_query("select c.*, mm.memberName, cm.complaintType, bm.blockName, bfom.officeNumber, om.officeName
									from complaints c 
									left join complaintMaster cm on c.complaintTypeID = cm.complaintTypeID
									left join memberMaster mm on c.memberID = mm.memberID
									left join blockFloorOfficeMapping bfom on bfom.memberID = mm.memberID
									left join officeMaster om on om.officeID = bfom.officeID
									left join blockMaster bm on bfom.blockID = bm.blockID" . $whr . " group by complaintID");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "complaintID:" . $res['complaintID'];

		if (isset($_SESSION['blockName'])) {
			$flatNumber = '<td>' . $res['officeNumber'] . '</td>';
		} else {
			$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';
		}

		$officeName = '<td>' . $res['officeName'] . '</td>';
		$complainant = '<td>' . $res['memberName'] . '</td>';
		$complaintType = '<td>' . $res['complaintType'] . '</td>';
		$complaintRemark = '<td>' . $res['complaintRemark'] . '</td>';
		$complaintDate = '<td>' . date('d M Y H:i A', strtotime($res['complaintDate'])) . '</td>';

		if ($res['approxResolveDate'] != null && !empty($res['approxResolveDate'])) {
			$approxResolveDate = '<td>' . date('d M Y', strtotime($res['approxResolveDate'])) . '</td>';
		} else {
			$approxResolveDate = '<td></td>';
		}

		if ($res['resolveDate'] != null && !empty($res['resolveDate'])) {
			$resolveDate = '<td>' . date('d M Y H:i A', strtotime($res['resolveDate'])) . '</td>';
		} else {
			$resolveDate = '<td></td>';
		}
		$currentStatus = $res['status'];
		if ($currentStatus == 0) {
			$status = '<td><i class="badge badge-danger">' . $statusTypeArray[$currentStatus] . '</i></td>';
		} else if ($currentStatus == 1) {
			$status = '<td><i class="badge badge-warning">' . $statusTypeArray[$currentStatus] . '</i></td>';
		} else if ($currentStatus == 2) {
			$status = '<td><i class="badge badge-success">' . $statusTypeArray[$currentStatus] . '</i></td>';
		} else {
			$status = '<td><i class="badge badge-secondary">' . $statusTypeArray[$currentStatus] . '</i></td>';
		}
		$result['aaData'][] = array("$flatNumber", "$officeName", "$complainant", "$complaintType", "$complaintRemark", "$complaintDate", "$approxResolveDate", "$resolveDate", "$status");
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