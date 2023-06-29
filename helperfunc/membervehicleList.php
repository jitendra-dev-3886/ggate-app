<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("0" => "Disable", "1" => "Active");
	$vehicleTypeArray = array("1" => "Two Wheeler", "2" => "Four Wheeler", "0" => "Other", "Other");
	$vehicleGuestParkingAllow = array("1" => "Allowed", "0" => "Not allowed", "Not allowed");
	$vehicleGuestParked = array("1" => "Yes", "0" => "No", "No");

	if ($_SESSION['memberID'] == 0) {
		$whr = 'mem.memberMobile, mv.vehicleNumber';
	} else {
		$whr = "concat('******', RIGHT(mem.memberMobile, 4)) as memberMobile, concat('******', RIGHT(mv.vehicleNumber, 4)) as vehicleNumber";
	}

		$queryString = pro_db_query("SELECT mem.memberID, mem.memberName, mem.memberImage, bfm.officeNumber, bm.blockName, 
									mv.vehicleID, mv.vehicleType, mv.vehicleAlias, mv.status as vehicleStatus, om.officeName, ".$whr." from memberVehicle mv
                                     join officeMemberMapping ofm on ofm.employeeID = mv.memberID
									join blockFloorOfficeMapping bfm on (ofm.employeeID = bfm.memberID or ofm.parentID = bfm.memberID)
                                    join officeMaster om on ofm.officeID = om.officeID
									join blockMaster bm on bfm.blockID = bm.blockID
									join memberMaster mem on mem.memberID = mv.memberID
									where mem.status != 126 and mv.status != 126 
								    and bfm.status = 1 and bfm.complexID = ".$_SESSION['complexID']."
									group by mem.memberName order by bm.blockName, bfm.floorNo, ofm.parentID");
	

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "vehicleID:" . $res['vehicleID'];

		$memberName = '<td>' . ucfirst($res['memberName']) . '</td>';
		$officeName = '<td>' . ucfirst($res['officeName']) . '</td>';
		$memberMobile = '<td>' . ucfirst($res['memberMobile']) . '</td>';
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';

		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$vehicleNumber = '<td>' . $res['vehicleNumber'] . '</td>';
		$vehicleAlias = '<td>' . $res['vehicleAlias'] . '</td>';

		if ($res['vehicleType'] == 1) {
			$vehicleType = '<td><i class="fas fa-motorcycle fa-lg"></i></td>';
		} else if ($res['vehicleType'] == 2) {
			$vehicleType = '<td><i class="fas fa-car-side fa-lg"></i></td>';
		} else {
			$vehicleType = '<td><span class="badge badge-secondary">' . $vehicleTypeArray[$res['vehicleType']] . '</span></td>';
		}

		if ($res['vehicleStatus'] == 1) {
			$vehicleStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['vehicleStatus']] . '</a></td>';
		} else {
			$vehicleStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['vehicleStatus']] . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=complexmasters&action=membervehiclemaster&subaction=editForm&vehicleID=' . $res['vehicleID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=complexmasters&action=membervehiclemaster&subaction=delete&vehicleID=' . $res['vehicleID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;	
				</td>';
		

		$result['aaData'][] = array("$memberName", "$officeName", "$flatNumber", "$memberImage", "$memberMobile", "$vehicleNumber", "$vehicleType", "$vehicleAlias", "$Action");
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
