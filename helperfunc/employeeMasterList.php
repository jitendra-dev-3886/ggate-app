<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("0" => "Disable", "1" => "Active");
	$isAppUserArray = array("0" => "No", "1" => "Yes");
	$adminTypeArray = array("0" => "-", "1" => "Complex Admin", "2" => "Block Admin", "3" => "Adhoc Admin", "4" => "Office Admin");
	$memberStatusArray = array("0" => "Registered", "1" => "Logged In", "2" => "Requested OTP", "3" => "Verified", "4" => "Active", "126" => "Logged Out");
	// $queryString = pro_db_query("select mem.memberID, mem.memberName, mem.memberImage, mem.adminType, mem.memberEmail, mem.memberMobile, mem.bloodGroup,
	// 									bfom.blockID, blk.blockName, bfom.floorNo, bfom.officeNumber, bfom.occupationType, bfom.officeArea,
	// 									om.officeName, om.officeLogo, om.officeEmail, om.officeContactNo, odm.designationTitle, bfom.isPrimary, bfom.status, bfom.officeMappingID, omm.mappingID, ma.status as memberStatus, pm.professionTitle, omm.isAppUser
	// 									from memberMaster bfom
	// 									join memberMaster mem on bfom.memberID = mem.memberID and mem.status = 1
	// 									join blockMaster blk on bfom.blockID = blk.blockID and blk.status = 1
	// 									join officeMemberMapping omm on bfom.officeMappingID = omm.officeMappingID and omm.status = 1 and omm.parentID = 0
	// 									left join officeDesignationMaster odm on omm.officeDesignationID = odm.officeDesignationID
	// 									left join memberActivity ma on mem.memberID = ma.memberID and ma.status != 127
	// 									left join professionMaster pm on mem.professionID = pm.professionID
	// 									join officeMaster om on omm.officeID = om.officeID
	// 									where bfom.status = 1 and bfom.complexID = " . $_SESSION['complexID'] . "
	// 									order by blk.blockName, bfom.floorNo, cast(bfom.officeNumber as unsigned),
	// 									bfom.occupationType, mem.memberName");

	$queryString = pro_db_query("select mem.memberID, mem.memberName, mem.memberImage, mem.adminType, mem.memberEmail, mem.memberMobile, mem.bloodGroup,
										bfom.floorNo, bfom.officeNumber, bfom.occupationType, bfom.officeArea, bfom.isPrimary, bfom.status, bfom.officeMappingID, bm.blockName, om.officeName, omm.officeID, cem.employeeName
										from memberMaster mem
										left join blockFloorOfficeMapping bfom on mem.memberID=bfom.memberID
										left join blockMaster bm on bfom.blockID=bm.blockID
										left join officeMaster om on bfom.officeID=om.officeID
										left join officeMemberMapping omm on bfom.officeID=omm.officeID
										left join complexEmployeeMaster cem on omm.employeeID=cem.employeeID
										where mem.complexID = " . $_SESSION['complexID'] . "
										group by mem.memberID");

	while ($res = pro_db_fetch_array($queryString)) {

		$pk = "memberID:" . $res['memberID'];

		$isAdmin = false;
		if ($res['adminType'] > 0) {
			$queryAdmin = pro_db_query("SELECT * FROM loginMaster where complexID = " . $_SESSION['complexID'] . " and memberID = " . $res['memberID']);
			$resAdmin = pro_db_fetch_array($queryAdmin);
			$adminStatus = $resAdmin["status"];
			if ($adminStatus == "1" || $adminStatus == "E") {
				$isAdmin = true;
			}
		}
		if ($isAdmin) {
			$memberName = '<td>' . ucfirst($res['memberName']) . ' (' . $adminTypeArray[$res['adminType']] . ')</td>';
		} else {
			$memberName = '<td><a href="index.php?controller=complexmasters&action=employeemaster&subaction=makeAdminForm&memberID=' . $res['memberID'] . '" title="Make Admin"><i class="far fa-user-circle icon-sm text-info"></i></a>' . '&nbsp&nbsp' . ucfirst($res['memberName']) . '</td>';
		}

		if ($res['isAppUser'] == 1) {
			$isAppUser = '<td><i class="text-info">' . $isAppUserArray[$res['isAppUser']] . '</i></td>';
		} else {
			$isAppUser = '<td><i class="text-warning">' . $isAppUserArray[$res['isAppUser']] . '</i></td>';
		}

		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$memberMobile = '<td>' . $res['memberMobile'] . '</td>';
		$professionTitle = '<td>' . $res['professionTitle'] . '</td>';
		$bloodGroup = '<td>' . $res['bloodGroup'] . '</td>';

		if (isset($res['memberStatus'])) {
			if ($res['memberStatus'] == 4) {
				$memberStatus = '<td><span class="badge badge-info">' . $memberStatusArray[$res['memberStatus']] . '</span></td>';
			} else if ($res['memberStatus'] == 126) {
				$memberStatus = '<td><span class="badge badge-danger">' . $memberStatusArray[$res['memberStatus']] . '</span></td>';
			} else {
				$memberStatus = '<td><span class="badge badge-warning">' . $memberStatusArray[$res['memberStatus']] . '</span></td>';
			}
		} else {
			$memberStatus = '<td><span class="badge badge-secondary">' . "Not Available" . '</td>';
		}

		$resBlockName = $res['blockName'];
		$resFlatNumber = $res['officeNumber'];

		if ($resBlockName != null && !empty($resBlockName)) {
			if ($resFlatNumber != null && !empty($resFlatNumber)) {
				$flatNumber = '<td>' . $resBlockName . ' - ' . $resFlatNumber . '</td>';
			} else {
				$flatNumber = '<td>' . $resBlockName . '</td>';
			}
		} else {
			if ($resFlatNumber != null && !empty($resFlatNumber)) {
				$flatNumber = '<td>' . $resFlatNumber . '</td>';
			} else {
				$flatNumber = '<td>Unassigned</td>';
			}
		}

		if (($_SESSION['complexID'] == 1001) || ($_SESSION['complexID'] == 1002)) {
			$Action = '<td><a href="index.php?controller=complexmasters&action=employeemaster&subaction=editForm&memberID=' . $res['memberID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
							<a href="index.php?controller=complexmasters&action=employeemaster&subaction=delete&memberID=' . $res['memberID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
						</td>';
		} else {
			$Action = '<td><a href="index.php?controller=complexmasters&action=employeemaster&subaction=editForm&memberID=' . $res['memberID'] . '" title="Edit"><i class="fe-edit text-info"></i></a></td>';
		}

		$office = '<td>' . $res['officeName'] . "&nbsp; ". $flatNumber . '</td>';
		$result['aaData'][] = array("$office", "$memberImage", "$memberName", "$memberMobile", "$professionTitle", "$bloodGroup", "$isAppUser", "$memberStatus", "$Action");
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
