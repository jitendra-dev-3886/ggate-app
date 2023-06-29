<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$statusArray = array("1" => "In", "3" => "Out", "0" => "Default");
	$requestData = $_REQUEST;

	/* Now Build Where Condition */
	$whr = "";
	if (isset($requestData['inDateTime']) || isset($requestData['clubID'])) {
		if (isset($requestData['inDateTime']) && isset($requestData['clubID'])) {
			$whr .= ' where cma.complexID = "' . $_SESSION['complexID'] . '" and bfm.isPrimary = 1 and cma.clubID = "' . $requestData['clubID'] . '" and date(cma.inDateTime) = "' . $requestData['inDateTime'] . '"';
		} else if (isset($requestData['currentDateTime'])) {
			$whr .= ' where cma.complexID = "' . $_SESSION['complexID'] . '" and bfm.isPrimary = 1 and date(cma.inDateTime) = "' . $requestData['inDateTime'] . '"';
		} else {
			$whr .= ' where cma.complexID = "' . $_SESSION['complexID'] . '" and bfm.isPrimary = 1 and cma.clubID = "' . $requestData['clubID'] . '" and date(cma.inDateTime) = CURRENT_DATE ';
		}
	} else {
		$whr .= ' where cma.complexID = "' . $_SESSION['complexID'] . '" and bfm.isPrimary = 1 and date(cma.inDateTime) = CURRENT_DATE';
	}

	$queryString = pro_db_query("select mm.memberName, mm.memberImage, bm.blockName, bfm.flatNumber, cm.clubTitle, cma.* from clubMemberActivity cma
								join clubMaster cm on cma.clubID = cm.clubID
								join memberMaster mm on cma.memberID = mm.memberID
								left join blockFloorFlatMapping bfm on (bfm.memberID = cma.memberID or bfm.memberID = mm.parentID)
								left join blockMaster bm on bfm.blockID = bm.blockID " . $whr);
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "gateActivityID:" . $res['gateActivityID'];

		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$memberName = '<td>' . $res['memberName'] . '</td>';

		$clubTitle = '<td>' . $res['clubTitle'] . '</td>';

		if ($res['inDateTime'] != null && !empty($res['inDateTime'])) {
			$inDateTime = '<td>' . date('H:i:s A', strtotime($res['inDateTime'])) . '</td>';
		} else {
			$inDateTime = '<td> </td>';
		}
		if ($res['outDateTime'] != null && !empty($res['outDateTime'])) {
			$outDateTime = '<td>' . date('H:i:s A', strtotime($res['outDateTime'])) . '</td>';
		} else {
			$outDateTime = '<td> </td>';
		}

		$resBlockName = $res['blockName'];
		$resFlatNumber = $res['flatNumber'];
		$flatNumber = '<td>' . $resBlockName . ' - ' . $resFlatNumber . '</td>';

		if ($res['status'] == 1) {
			$Status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
		} else if ($res['status'] == 3) {
			$Status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
		} else {
			$Status = '<td><span class="badge badge-secondary">' . $statusArray[$res['status']] . '</span></td>';
		}
		$result['aaData'][] = array("$memberImage", "$memberName", "$flatNumber", "$clubTitle", "$inDateTime", "$outDateTime", "$Status");
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
