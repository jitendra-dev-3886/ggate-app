<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {

	$assetID = (int)$_REQUEST['assetID'];
	$date = $_REQUEST['bookingDate'];

	if (isset($_REQUEST['assetID'])) {
		$sql = pro_db_query("select bm.timeSlotID, bm.slotStartTime, bm.slotEndTime, bm.slotType from bookingTimeSlot bm where bm.status = 1 and bm.assetID =" . $assetID);
		if (pro_db_num_rows($sql) > 0) {
			$timeSlotID = "";
			while ($brs = pro_db_fetch_array($sql)) {
				$i = $brs['timeSlotID'];
				if ($brs['slotType'] == 0) {
					$type = "Full Day";
				} else if ($brs['slotType'] == 1) {
					$type = "Half Day";
				} else {
					$type = "Slot Wise";
				}
				$dropdown .= '<option value="' . $i . '">' . $brs['slotStartTime'] . ' - ' . $brs['slotEndTime'] . ' - ' . $type . '</option>';
			}
		}
		print $dropdown;
	}

	if ((isset($_REQUEST['assetID']) && isset($_REQUEST['bookingDate']))) {
		$data = null;
		$sqltimeslot = pro_db_query("SELECT bookingTimeSlotID FROM amenityBookingMain WHERE status = 1 and assetID = " . $assetID . " and bookingDate = '" . $_REQUEST['bookingDate'] . "'
										UNION
										SELECT bookingTimeSlotID FROM amenityBookingTemp WHERE assetID = " . $assetID . " and bookingDate = '" . $_REQUEST['bookingDate'] . "'");
		$row = $sqltimeslot->fetch_assoc();
		$data[] = $row;

		$sql = pro_db_query("select bm.timeSlotID, bm.slotStartTime, bm.slotEndTime, bm.slotType from bookingTimeSlot bm where bm.status = 1 and bm.assetID =" . $assetID);
		if (pro_db_num_rows($sql) > 0) {
			$timeSlotID = "";
			while ($brs = pro_db_fetch_array($sql)) {
				$i = $brs['timeSlotID'];
				if ($brs['slotType'] == 0) {
					$type = "Full Day";
				} else if ($brs['slotType'] == 1) {
					$type = "Half Day";
				} else {
					$type = "Slot Wise";
				}

				//$dropdown .= '<option value="'.$i.'">'.$brs['slotStartTime'].' - '.$brs['slotEndTime']. ' - '.$type.'</option>';			
				$dropdown .= '<option value="' . $i . '">' . $_REQUEST['assetID'] . '</option>';
			}
		}
		print $dropdown;
	} else {
		$dropdown .= '<option value="' . $i . '">' . "22131" . '</option>';
		print $dropdown;
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>