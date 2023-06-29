<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("SELECT ats.*, am.assetTitle from amenityTimeSlot ats
								join amenityMaster am on ats.assetID = am.assetID 
								where am.complexID =" . $_SESSION['complexID']);

	$statusArray = array("0" => "Inactive", "1" => "Active");
	$slotTypeArray = array("0" => "Full Day", "1" => "Half Day", "2" => "Slot Wise");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "timeSlotID:" . $res['timeSlotID'];
		$assetTitle = '<td>' . $res['assetTitle'] . '</td>';
		$slotStartTime = '<td>' . date('h:i A', strtotime($res['slotStartTime'])) . '</td>';
		$slotEndTime = '<td>' . date('h:i A', strtotime($res['slotEndTime'])) . '</td>';
		$slotType = '<td>' . $slotTypeArray[$res['slotType']] . '</td>';
		$amount = '<td>' . $res['amount'] . '</td>';
		$discount = '<td>' . $res['discount'] . '</td>';
		$bookingtimeslotstatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$Action = '<td><a href="index.php?controller=amenities&action=bookingtimeslotmaster&subaction=editForm&timeSlotID=' . $res['timeSlotID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> | 
					<a href="index.php?controller=amenities&action=bookingtimeslotmaster&subaction=delete&timeSlotID=' . $res['timeSlotID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$assetTitle", "$slotStartTime", "$slotEndTime", "$slotType", "$amount", "$discount", "$bookingtimeslotstatus", "$Action");
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