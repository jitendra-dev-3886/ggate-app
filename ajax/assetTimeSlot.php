<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {

	$assetID = (int)$_REQUEST['assetID'];
	$sql = pro_db_query("select timeSlotID, slotStartTime,slotEndTime from amenityTimeSlot where assetID =" . $assetID);
	if (pro_db_num_rows($sql) > 0) {
		print '<h5>Already Added Timeslots</h5>';
		while ($brs = pro_db_fetch_array($sql)) {
			$i = $brs['timeSlotID'];
			if ($brs['slotStartTime'] < 12) {
				$starttime = "AM";
			} else {
				$starttime = "PM";
			}

			if ($brs['slotEndTime'] < 12) {
				$endtime = "AM";
			} else {
				$endtime = "PM";
			}
			print '<type = "checkbox" value="' . $i . '" checked ><lable for="' . $i . '">' . $brs['slotStartTime'] . ' : ' . $starttime . ' - ' . $brs['slotEndTime'] . ' : ' . $endtime . '</lable><br>';
			//$dropdown .= '<option value="'.$i.'">Floor - '.$brs['timeSlotID'].'</option>';
		}
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>