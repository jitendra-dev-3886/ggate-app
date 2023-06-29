<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("1" => "Finished", "0" => "In Progress");
	$typeArray = array("2" => "Committee", "1" => "Complex");
	$publishArray = array("0" => "Not Published", "1" => "Committee", "2" => "Complex");

	$queryString =  pro_db_query("SELECT mm.* from meetingMaster mm  where mm.status != 126 and mm.complexID = " . $_SESSION['complexID'] . "
																	order by mm.meetingID desc");
	
	while ($res = pro_db_fetch_array($queryString)) {

		$pk = "meetingID:" . $res['meetingID'];
		if ($res['blockID'] == 0) {
			$blockName = '<td>' . "All Blocks" . '</td>';
		} 
		$title = '<td>' . $res['title'] . '</td>';
		$agenda = '<td "style=white-space: pre";>' . $res['agenda'] . '</td>';
		$dateTime = '<td>' . date('d M Y H:i A', strtotime($res['dateTime'])) . '</td>';
		$place = '<td>' . $res['place'] . '</td>';
		$type = '<td>' . $typeArray[$res['meetingType']] . '</td>';
		$MeetingStatus = '<td>' . $statusArray[$res['status']] . '</td>';
		if ((($res['publish'] == 1) || ($res['publish'] == 2)) && ($res['meetingType'] == 2)) {
			$publish = "Published";
			if ($res['publish'] == 1) {
				$publishStatus = '<td>' . $publishArray[$res['publish']] . '</td>';
				$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
						<a href ="index.php?controller=community&action=meetings&subaction=meetingAttendees&meetingID=' . $res['meetingID'] . '&publish=' . $publish . '&title=' . $res['title'] . '" title="Meeting Attendees"><i class="fas fas fa-clipboard text-warning"></i></a>&nbsp;&nbsp;
						<a href ="index.php?controller=community&action=meetings&subaction=meetingDetails&meetingID=' . $res['meetingID'] . '&title=' . $res['title'] . '" title="Meeting Details"><i class="fas fa-info-circle text-success"></i></a></td>';
			} else {
				$publishStatus = '<td>' . $publishArray[$res['publish']] . '</td>';
				$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
						<a href ="index.php?controller=community&action=meetings&subaction=meetingDetails&meetingID=' . $res['meetingID'] . '&title=' . $res['title'] . '" title="Meeting Details"><i class="fas fa-info-circle text-success"></i></a></td>';
			}
		} else if (($res['publish'] == 0) && (($res['meetingType'] == 2) || ($res['meetingType'] == 1))) {
			if ($res['meetingType'] == 2) {
				$publish = "Not Published";
				$publishStatus = '<td><a href="index.php?controller=community&action=meetings&subaction=publishMeeting&meetingID=' . $res['meetingID'] . '" title="Publish Meeting" ><i class="fas fa-upload text-info"></i></a></td>';
				$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
						<a href ="index.php?controller=community&action=meetings&subaction=meetingAttendees&meetingID=' . $res['meetingID'] . '&publish=' . $publish . '&title=' . $res['title'] . '" title="Meeting Attendees"><i class="fas fas fa-clipboard text-warning"></i></a></td>';
			} else {
				$publish = "Not Published";
				$publishStatus = '<td><a href="index.php?controller=community&action=meetings&subaction=publishMeeting&meetingID=' . $res['meetingID'] . '" title="Publish Meeting" ><i class="fas fa-upload text-info"></i></a></td>';
				$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a></td>';
			}
		} else if ((($res['publish'] == 1) || ($res['publish'] == 2)) && ($res['meetingType'] == 1)) {
			if ($res['publish'] == 1) {
				$publishStatus = '<td>' . $publishArray[$res['publish']] . '</td>';
				$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;
						<a href ="index.php?controller=community&action=meetings&subaction=meetingDetails&meetingID=' . $res['meetingID'] . '&title=' . $res['title'] . '" title="Meeting Details"><i class="fas fa-info-circle text-success"></i></a></td>';
			} else {
				$publishStatus = '<td>' . $publishArray[$res['publish']] . '</td>';
				$Action = '
				<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
						<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a></td>';
			}
		} else {
			$publishStatus = '<td>' . $publishArray[$res['publish']] . '</td>';
			$Action = '<td><a href="index.php?controller=community&action=meetings&subaction=editForm&meetingID=' . $res['meetingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=community&action=meetings&subaction=delete&meetingID=' . $res['meetingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a></td>';
		}
		$result['aaData'][] = array("$blockName", "$title", "$agenda", "$dateTime", "$place", "$type", "$publishStatus",  "$Action");
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
