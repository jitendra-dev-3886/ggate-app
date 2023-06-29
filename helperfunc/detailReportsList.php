<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$visitorTypeArray = array("1" => "Daily Staff", "2" => "Guest", "3" => "Delivery Boy", "4" => "Cab");
	$requestData = $_REQUEST;

	/* Now Build Where Condition */
	$whr = "";
	if (isset($requestData['visitorType']) && $requestData['inDateTime']) {
		if ($requestData['visitorType'] > 0) {
			if ($requestData['visitorType'] == 1) {
				$whr .= ' where dga.visitorType = "' . $requestData['visitorType'] . '" and dga.complexID = "' . $_SESSION['complexID'] . '" and date(dga.inDateTime) = "' . $requestData['inDateTime'] . '"';
			} else {
				$whr .= ' where dga.visitorType = "' . $requestData['visitorType'] . '" and dga.complexID = "' . $_SESSION['complexID'] . '" and bfm.isPrimary = 1 and (date(dga.inDateTime) = "' . $requestData['inDateTime'] . '" or date(dga.entryDate) = "' . $requestData['inDateTime'] . '")';
			}
		} else {
			$whr .= ' where dga.complexID = "' . $_SESSION['complexID'] . '" and (dga.visitorType = 1 or bfm.isPrimary = 1) and (date(dga.inDateTime) = "' . $requestData['inDateTime'] . '" or date(dga.entryDate) = "' . $requestData['inDateTime'] . '")';
		}
	} else {
		$whr .= ' where dga.complexID = "' . $_SESSION['complexID'] . '" and (dga.visitorType = 1 or bfm.isPrimary = 1) and (date(dga.inDateTime) = CURRENT_DATE or date(dga.entryDate) = CURRENT_DATE)';
	}

	$queryString = pro_db_query("select dsm.staffName, dga.visitorCode, dga.entryDate, dsm.staffImage, dga.gateActivityID, dga.visitorID,
						dga.visitorImage, dga.visitorName, dga.inDateTime, dga.outDateTime, dga.visitorType, bm.blockName, 
						bfm.officeNumber, mm.memberName, em.employeeName, om.officeName from dailyGateActivity dga
						left join memberMaster mm on dga.memberID = mm.memberID
						left join blockFloorOfficeMapping bfm on bfm.officeMappingID = dga.officeID
						left join officeMaster om on om.officeID = bfm.officeID
						left join blockMaster bm on bfm.blockID = bm.blockID
						left join dailyStaffMaster dsm on dga.visitorID = dsm.dailyStaffID
						left join complexEmployeeMaster em on em.complexEmployeeID = dga.userID " . $whr);
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "gateActivityID:" . $res['gateActivityID'];

		$visitorType = '<td>' . $visitorTypeArray[$res['visitorType']] . '</td>';

		if ($res['visitorID'] == 0) {
			$visitorImage = '<td><img src="https://cdn.ggate.app/icons/ico_visitor.png" style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
			$visitorName = '<td>' . ucfirst($res['visitorName']) . '</td>';
			$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';
			$memberName = '<td>' . $res['memberName'] . '</td>';
		} else {
			if ($res['staffImage'] == null || empty($res['staffImage'])) {
				$res['staffImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
			}
			$visitorImage = '<td><img src="' . $res['staffImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
			$visitorName = '<td>' . ucfirst($res['staffName']) . '</td>';
			$officeName = '<td>' . ucfirst($res['officeName']) . '</td>';
			$flatNumber = '<td></td>';
			$memberName = '<td></td>';
		}

		if (((empty($res['visitorCode']) || $res['visitorCode'] == 0) || !empty($res['visitorCode'])) && $res['visitorType'] != 1) {
			if (isset($res['entryDate'])) {
				$entryDate = '<td>' . date('d-M-Y', strtotime($res['entryDate'])) . '</td>';
			} else {
				if (!empty($res['inDateTime'])) {
					$entryDate = '<td>' . date('d-M-Y', strtotime($res['inDateTime'])) . '</td>';
				} else {
					$entryDate = '<td> </td>';
				}
			}
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
		} else {
			if ($res['inDateTime'] != null && !empty($res['inDateTime'])) {
				$entryDate = '<td>' . date('d-M-Y', strtotime($res['inDateTime'])) . '</td>';
				$inDateTime = '<td>' . date('H:i:s A', strtotime($res['inDateTime'])) . '</td>';
			} else {
				$entryDate = '<td> </td>';
				$inDateTime = '<td> </td>';
			}
			if ($res['outDateTime'] != null && !empty($res['outDateTime'])) {
				$outDateTime = '<td>' . date('H:i:s A', strtotime($res['outDateTime'])) . '</td>';
			} else {
				$outDateTime = '<td> </td>';
			}
		}
		$employeeName = '<td>' . $res['employeeName'] . '</td>';
		$result['aaData'][] = array("$visitorImage", "$visitorType", "$visitorName", "$flatNumber", "$officeName", "$memberName", "$entryDate", "$inDateTime", "$outDateTime", "$employeeName");
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
