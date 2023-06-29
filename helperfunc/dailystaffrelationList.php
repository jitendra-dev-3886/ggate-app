<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	if ($_SESSION['memberID'] == 0) {
		$whr = 'mem.memberMobile';
	} else {
		$whr = "concat('******', RIGHT(mem.memberMobile, 4)) as memberMobile";
	}
	$result = array('aaData' => array());
	$queryString = pro_db_query("select dsr.*, dsm.staffTypeID, mem.memberID, mem.memberName, mem.memberImage, om.officeName, " . $whr . "
								from dailyStaffRelation dsr
								left join dailyStaffMaster dsm on dsr.staffID = dsm.dailyStaffID 
                                join officeMemberMapping ofm on ofm.officeID = dsr.officeID
								join memberMaster mem on ofm.employeeID = mem.memberID 
                                join officeMaster om on om.officeID = ofm.officeID
								where dsr.status = 1  and ofm.status = 1 and ofm.parentID = 0 and dsr.staffID = " . $_REQUEST['dailyStaffID']);

	while ($res = pro_db_fetch_array($queryString)) {
		$memberID = $res['memberID'];

		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
		$memberName = '<td>' . ucfirst($res['memberName']) . '</td>';
		$memberMobile = '<td>' . $res['memberMobile'] . '</td>';

		$queryFlats = pro_db_query("SELECT bm.blockName, bfm.officeMappingID, bfm.floorNo, bfm.officeNumber, om.officeName from blockFloorOfficeMapping bfm
									join blockMaster bm on bfm.blockID = bm.blockID
									join officeMaster om on bfm.officeID = om.officeID 
									where bfm.status = 1 and bfm.memberID = " . $memberID . " and bfm.complexID = " . $_SESSION['complexID']);
		$arrFlats = array();
		while ($resFlat = pro_db_fetch_array($queryFlats)) {
			$arrFlats[] = '<span class="badge badge-greyGGate">' . $resFlat['blockName'] . ' - ' . $resFlat['officeNumber'] . ' : ' .  $resFlat['officeName'] . '</span>';
		}
		$flatNumber = '<td>' . implode("&nbsp;&nbsp;&nbsp;", $arrFlats) . '</td>';

		$resourceNickName = '<td>' . ucfirst($res['nickName']) . '</td>';
		if ($res['validUpto'] != null && !empty($res['validUpto']) && $res['validUpto'] != "0000-00-00 00:00:00") {
			$validUpto = '<td>' . date('d-M-Y', strtotime($res['validUpto'])) . '</td>';
		} else {
			$validUpto = '<td></td>';
		}
		if ($res['staffTypeID'] == 0) {
			$result['aaData'][] = array("$memberImage", "$memberName", "$memberMobile", "$flatNumber", "$resourceNickName", "$validUpto");
		} else {
			$result['aaData'][] = array("$memberImage", "$memberName", "$memberMobile", "$flatNumber");
		}
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