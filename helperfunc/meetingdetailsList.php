<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {

	$result = array('aaData' => array());
	$responseArray = array("0" => "Pending", "1" => "Interested", "2" => "Not Interested");
	$attendanceArray = array("0" => "Absent", "1" => "Present");
	$adminTypeArray = array("0" => "-", "1" => "Society Admin", "2" => "Building Admin", "3" => "Adhoc Admin");
	$queryString = pro_db_query("select mm.*, mem.memberName, mem.adminType, bm.blockName FROM meetingMaster mm
								left join memberMaster mem on mm.memberID = mem.memberID
								left join blockMaster bm on mm.blockID = bm.blockID 
								where mm.meetingID = " . $_REQUEST['meetingID'] . "");

	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['blockID'] == 0) {
			$blockName = '<td>' . "All Blocks" . '</td>';
		}
		if ($res['memberID'] != 0) {
			if ($res['adminType'] == 0) {
				$query = pro_db_query("SELECT mcp.memberID, GROUP_CONCAT(mc.designationTitle SEPARATOR '\n') as memberInCommittee FROM designationMemberMapping mcp
								 join memberCommittee mc on mcp.committeeID = mc.committeeID where mcp.memberID = " . $res['memberID'] . "");
				$rs = pro_db_fetch_array($query);
				$name = '<td>' . ucfirst($res['memberName']) . '(' . $rs['memberInCommittee'] . ')</td>';
			} else {
				$name = '<td>' . ucfirst($res['memberName']) . '(' . $adminTypeArray[$res['adminType']] . ')</td>';
			}
		} else {
			$name = '<td>' . "Super Admin" . '</td>';
		}

		$mom = '<td>' . $res['minutesOfMeetings'] . '</td>';
		$result['aaData'][] = array("$blockName", "$name", "$mom");
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