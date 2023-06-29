<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("0" => "Pending", "1" => "Accept", "2" => "Reject", "126" => "Deleted");
	if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
		$queryString = pro_db_query("select appr.requestID, appr.complexID, appr.memberID, appr.status, 
			appr.employeeID, 
									appr.approverID, mm.memberName, mm.memberMobile, mm.memberEmail, 
									bfomp.officeID, bfomp.blockID, bfomp.floorNo,  bfomp.officeNumber, om.officeName, bm.blockName
									FROM memberApproverRequest appr
									left join memberMaster mm on appr.memberID=mm.memberID 
									left join blockFloorOfficeMapping bfomp on appr.memberID=bfomp.memberID 
									left join officeMaster om on bfomp.officeID=om.officeID 
									left join blockMaster bm on bfomp.blockID=bm.blockID 
									WHERE appr.status = 0 and appr.complexID = " . $_SESSION['complexID']. " ");
	} else {
		$queryString = pro_db_query("select appr.requestID, appr.complexID, appr.memberID, appr.status, 
			appr.employeeID, 
									appr.approverID, mm.memberName, mm.memberMobile, mm.memberEmail, 
									bfomp.officeID, bfomp.blockID, bfomp.floorNo,  bfomp.officeNumber, om.officeName, bm.blockName
									FROM memberApproverRequest appr
									left join memberMaster mm on appr.memberID=mm.memberID 
									left join blockFloorOfficeMapping bfomp on appr.memberID=bfomp.memberID 
									left join officeMaster om on bfomp.officeID=om.officeID 
									left join blockMaster bm on bfomp.blockID=bm.blockID 
									WHERE appr.status = 0 and appr.complexID = " . $_SESSION['complexID']. "  group by appr.memberID ");
	}
	while ($res = pro_db_fetch_array($queryString)) {
		// print_r($_SESSION);
		// exit();
		$pk = "requestID:" . $res['requestID'];
		// $officeName = '<td>' . $res['blockName'] . ' - ' . $res['officeName'] . '</td>';
		$officeName = '<td>' . $res['officeName'] . '</td>';
		$memberName = '<td>' . ucfirst($res['memberName']) . '</td>';
		$memberMobile = '<td>' . ucfirst($res['memberMobile']) . '</td>';
		$memberEmail = '<td>' . ucfirst($res['memberEmail']) . '</td>';
		$Action = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		$result['aaData'][] = array("$officeName", "$memberName", "$memberMobile", "$memberMobile", "$memberEmail", "$Action");
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