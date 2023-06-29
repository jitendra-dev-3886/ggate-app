<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {

	$result = array('aaData' => array());
	$statusArray = array("0" => "Pending", "1" => "Active", "2" => "Rejected");
	$staffIDTypeArray = array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID");
	$isComplexResource = array("1" => "Daily", "2" => "Complex", "3" => "Vendor");

	if ($_SESSION['memberID'] == 0) {
		$whr = 'dm.staffMobileNo, dsm.staffIDValue';
	} else {
		$whr = "concat('******', RIGHT(dsm.staffMobileNo, 4)) as staffMobileNo, concat('******', RIGHT(dsm.staffIDValue, 4)) as staffIDValue";
	}

	$queryString = pro_db_query("select dsm.dailyStaffID, dsm.staffName, dsm.staffMobileNo, dsm.staffIDValue,
		dsm.status, dsm.staffIDType,
				                    dsr.validUpto, om.officeName, blk.blockName, stm.staffTypeTitle, stm.isComplexResource
									from dailyStaffMaster dsm 
									left join dailyStaffRelation dsr on dsm.dailyStaffID = dsr.dailyStaffID
                                    left join officeMaster om on dsr.officeID = om.officeID
                                    left join blockMaster blk on dsr.complexID = blk.complexID 
                                    left join staffTypeMaster stm on dsm.staffTypeID = stm.staffTypeID 
									where dsm.status = 0 and dsm.complexID = ".$_SESSION['complexID']." ");
							
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "dailyStaffID:" . $res['dailyStaffID'];
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';
		$staffName = '<td>' . ucfirst($res['staffName']) . '</td>';
		if (!empty($res['officeName'])) {
			$officeName = '<td>' . ucfirst($res['officeName']) . '</td>';
		} else {
			$officeName = '<td>NA</td>';
		}
		if ($res['officeName'] == 1) {

			$resourceType = '<td>' . $isComplexResource[$res['isComplexResource']] . '</a></td>';
		} else if ($res['officeName'] == 2) {

			$resourceType = '<td>' . $isComplexResource[$res['isComplexResource']] . '</a></td>';
		} else {
			
			$resourceType = '<td>' . $isComplexResource[$res['isComplexResource']] . '</a></td>';
		}

		$staffProfession = '<td>' . ucfirst($res['staffProfession']) . '</td>';
		$staffType = '<td>' . ucfirst($res['staffTypeTitle']) . '</td>';
		$validUpto = '<td>' . date('d M Y', strtotime($res['validUpto'])) . '</td>';
		$staffIDType = '<td>' . $staffIDTypeArray[$res['staffIDType']] . '</td>';
		$staffIDValue = '<td>' . $res['staffIDValue'] . '</td>';

		$Action = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		$result['aaData'][] = array("$officeName", "$staffName", "$resourceType", "$staffType", "$validUpto", "$staffIDType", "$staffIDValue", "$Action");
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
