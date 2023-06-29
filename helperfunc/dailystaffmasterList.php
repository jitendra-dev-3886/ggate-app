<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	if ($_SESSION['memberID'] == 0) {
		$whr = 'dm.staffMobileNo, dm.staffIDValue';
	} else {
		$whr = "concat('******', RIGHT(dm.staffMobileNo, 4)) as staffMobileNo, concat('******', RIGHT(dm.staffIDValue, 4)) as staffIDValue";
	}

	if (($_REQUEST['isSocietyResource'] == 1) && ($_REQUEST['staffTypeID'] == 1)) {
		$queryString = pro_db_query("SELECT dm.dailyStaffID, dm.complexID, dm.staffTypeID, dm.staffCode, dm.staffName, dm.staffProfession, dm.staffQualification, dm.staffResideAddress, dm.staffImage, dm.staffEmailAddress, dm.overallRating, dm.staffIDType, dm.staffPhotoID, dm.validUpto, dm.status, " . $whr . ", stm.sortorder as typeOrder, stm.staffTypeTitle from dailyStaffMaster dm 
									join staffTypeMaster stm on dm.staffTypeID = stm.staffTypeID 
									where stm.isComplexResource = 1 and dm.status != 2 and dm.complexID = " . $_SESSION['complexID'] . " 
									order by typeOrder");
	} else if (($_REQUEST['isSocietyResource'] == 0) && ($_REQUEST['staffTypeID'] == 1)) {
		$queryString = pro_db_query("SELECT dm.dailyStaffID, dm.complexID, dm.staffTypeID, dm.staffCode, dm.staffName, dm.staffProfession, dm.staffQualification, dm.staffResideAddress, dm.staffImage, dm.staffEmailAddress, dm.overallRating, dm.staffIDType, dm.staffPhotoID, dm.validUpto, dm.status, " . $whr . ", stm.sortorder as typeOrder, stm.staffTypeTitle from dailyStaffMaster dm 
						  			join staffTypeMaster stm on dm.staffTypeID = stm.staffTypeID 
									where stm.isComplexResource = 0 and dm.status != 2 and dm.complexID = " . $_SESSION['complexID'] . " 
									order by typeOrder");
	} else {
		$queryString = pro_db_query("SELECT dm.dailyStaffID, dm.complexID, dm.staffTypeID, dm.staffCode, dm.staffName, dm.staffProfession, dm.staffQualification, dm.staffResideAddress, dm.staffImage, dm.staffEmailAddress, dm.overallRating, dm.staffIDType, dm.staffPhotoID, dm.validUpto, dm.status, " . $whr . " from dailyStaffMaster dm where dm.status != 126 and dm.status != 2 
									and dm.complexID = " . $_SESSION['complexID'] . " order by dm.staffName");
	}
	$staffIDTypeArray = array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID", "5" => "Leaving Certificate", "10" => "Other");
	$statusArray = array("0" => "Pending", "1" => "Active", "2" => "Rejected");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "dailyStaffID:" . $res['dailyStaffID'];
		if ($res['staffImage'] == null || empty($res['staffImage'])) {
			$res['staffImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$staffImage = '<td><img src="' . $res['staffImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
		$staffName = '<td>' . $res['staffName'] . '</td>';
		$staffMobileNo = '<td>' . $res['staffMobileNo'] . '</td>';

		if (($_REQUEST['isSocietyResource'] == 0) && ($_REQUEST['staffTypeID'] == 0)) {
			$staffProfession = '<td>' . $res['staffProfession'] . '</td>';
		} else {
			$staffProfession = '<td>' . $res['staffTypeTitle'] . '</td>';
		}
		$staffIDType = '<td>' . $staffIDTypeArray[$res['staffIDType']] . '</td>';
		$staffIDValue = '<td>' . $res['staffIDValue'] . '</td>';

		if ($res['status'] == "1") {
			$staffStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else if ($res['status'] == "2") {
			$staffStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$staffStatus = '<td><a href="#" class="estatus badge badge-warning" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		if ($_REQUEST['isSocietyResource'] == 1) {
			$Action = '<td><a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=editForm&dailyStaffID=' . $res['dailyStaffID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp; 
					<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=delete&dailyStaffID=' . $res['dailyStaffID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
					</td>';
		} else if (($_REQUEST['isSocietyResource'] == 0) && ($_REQUEST['staffTypeID'] == 1)) {
			$Action = '<td><a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=editForm&dailyStaffID=' . $res['dailyStaffID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp; 
					<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=delete&dailyStaffID=' . $res['dailyStaffID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=viewRelation&personalResource=0&dailyStaffID=' . $res['dailyStaffID'] . '&staffName=' . $res['staffName'] . '" title="List of members working for"><i class="fas fa-clipboard-list text-success"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=assignToFlats&dailyStaffID=' . $res['dailyStaffID'] . '&staffName=' . $res['staffName'] . '" title="List of members working for"><i class="fas fa-user-plus text-warning"></i></a>
					</td>';
		} else {
			$Action = '<td><a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=editFormForPersonalResource&dailyStaffID=' . $res['dailyStaffID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp; 
						<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=delete&dailyStaffID=' . $res['dailyStaffID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=complexmasters&action=dailystaffmaster&subaction=viewRelation&personalResource=1&dailyStaffID=' . $res['dailyStaffID'] . '&staffName=' . $res['staffName'] . '" title="List of members working for"><i class="fas fa-clipboard-list text-success"></i></a>&nbsp;&nbsp;
						</td>';
		}
		$result['aaData'][] = array("$staffImage", "$staffName", "$staffMobileNo", "$staffProfession", "$staffIDType", "$staffIDValue", "$staffStatus", "$Action");
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
