<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$responseArray = array("0" => "Pending", "1" => "Interested", "2" => "Not Interested");
	$attendanceArray = array("0" => "Absent", "1" => "Present");
	$queryString = pro_db_query("select ma.meetingAttendeesID, ma.attendeeID, mem.memberName, blk.blockName, om.officeName, bfom.officeNumber, ma.status 
								as response, ma.attendance, mm.publish, mm.dateTime
								from meetingAttendees ma 
								join meetingMaster mm on ma.meetingID = mm.meetingID
								join memberMaster mem on ma.attendeeID = mem.memberID
                                join officeMemberMapping ofm on ofm.employeeID = ma.meetingAttendeesID
								join blockFloorOfficeMapping bfom on (ofm.employeeID = bfom.memberID or ofm.parentID = bfom.memberID)
								join officeMaster om on om.officeID = bfom.officeID
								join blockMaster blk on blk.blockID = bfom.blockID
                                where mm.meetingID = " . $_REQUEST['meetingID'] . " and bfom.isPrimary = 1");

	while ($res = pro_db_fetch_array($queryString)) {

		$pk = "meetingAttendeesID:" . $res['meetingAttendeesID'];
		$query = pro_db_query("SELECT mcp.memberID, GROUP_CONCAT(mc.designationTitle SEPARATOR '\n') as memberInCommittee FROM designationMemberMapping mcp
								 join designationMaster mc on mcp.designationID = mc.designationID where mcp.memberID = " . $res['attendeeID'] . "");
		$rs = pro_db_fetch_array($query);

		$date = date('Y-m-d H:i:s');
		$officeName = '<td>' . $res['officeName'] . '</td>';
		$name = '<td>' . $res['memberName'] . '</td>';
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';
		$committeeTitle = '<td>' . $rs['memberInCommittee'] . '</td>';

		$memberResponse = $res['response'];
		$memberAttendance = $res['attendance'];

		if ($memberResponse == 1) {
			$response = '<td><i class="badge badge-success">' . $responseArray[$memberResponse] . '</i></td>';
		} else if ($memberResponse == 2) {
			$response = '<td><i class="badge badge-danger">' . $responseArray[$memberResponse] . '</i></td>';
		} else {
			$response = '<td><i class="badge badge-secondary">' . $responseArray[$memberResponse] . '</i></td>';
		}

		if ($res['publish'] == 1) {
			if ($memberAttendance == 1) {
				$attendance = '<td><i class="badge badge-success">' . $attendanceArray[$memberAttendance] . '</i></td>';
			} else {
				$attendance = '<td><i class="badge badge-danger">' . $attendanceArray[$memberAttendance] . '</i></td>';
			}
		} else {
			if ($date > $res['dateTime']) {
				if ($memberAttendance == 1) {
					$attendance = '<td><a href="#" class="eattendance" data-type="select" data-name="attendance" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Attendance status"><i class="badge badge-success">' . $attendanceArray[$memberAttendance] . '</i></a></td>';
				} else {
					$attendance = '<td><a href="#" class="eattendance" data-type="select" data-name="attendance" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Attendance status"><i class="badge badge-danger">' . $attendanceArray[$memberAttendance] . '</i></a></td>';
				}
			} else {
				$attendance = '<td>' . "-" . '</td>';
			}
		}
		$result['aaData'][] = array("$name", "$flatNumber", "$officeName", "$committeeTitle", "$response", "$attendance");
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