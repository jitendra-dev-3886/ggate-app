<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {

	$memberID = (int)$_REQUEST['memberID'];
	$requestType = (int)$_REQUEST['requestTypeID'];

	if ($requestType == 11) {
		$sql = pro_db_query(
			"select book.bookingID, am.assetsTitle, book.bookingDate FROM amenityBookingTemp book
			join assetMaster am on book.assetID = am.assetID
			where book.status in (1, 2) and book.memberID = " . $memberID
		);
		if (pro_db_num_rows($sql) > 0) {
			while ($brs = pro_db_fetch_array($sql)) {
				print '<option value = "' . $brs['bookingID'] . '">' . $brs['assetsTitle'] . " - " . $brs['bookingDate'] . '</option>';
			}
		}
	} else if ($requestType == 12) {
		$sql = pro_db_query("select em.eventID, em.eventName, em.eventStartTime from eventAttendees ea
							left join eventMaster em on ea.eventID = em.eventID
							where ea.status = 1 and ea.memberID = " . $memberID);
		if (pro_db_num_rows($sql) > 0) {
			while ($brs = pro_db_fetch_array($sql)) {
				print '<option value = "' . $brs['eventID'] . '">' . $brs['eventName'] . " - " . $brs['eventStartTime'] . '</option>';
			}
		}
	} else {
		$sql = pro_db_query("select flatID, blockName, flatNumber from blockFloorFlatMapping bfm, blockMaster bm 
							where bm.blockID = bfm.blockID and bfm.status = 1 and memberID = " . $memberID . " order by isPrimary desc");
		if (pro_db_num_rows($sql) > 0) {
			while ($brs = pro_db_fetch_array($sql)) {
				print '<option value = "' . $brs['flatID'] . '">' . $brs['blockName'] . " - " . $brs['flatNumber'] . '</option>';
			}
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