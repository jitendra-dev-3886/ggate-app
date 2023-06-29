<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("select ab.bookingID, ab.assetID, ab.memberID, ab.complexID, ab.bookingReason, ab.bookingName, ab.bookingAddress, ab.bookingEmail, ab.bookingMobile, 
								ab.bookingCompany, ab.bookingDate, ab.bookingTimeSlotID, ab.guestCount, ab.foodType, ab.quantity, ab.invoiceID, ab.paidAmount, ab.transactionNumber, ab.paymentDate, ab.referenceID, 
								ab.username, ab.createdate, ab.modifieddate, ab.remote_ip, ab.status as bookingStatus,
								am.assetTitle, mm.memberName, bts.slotStartTime, bts.slotEndTime, bts.slotType, bts.amount, 
								iv.billAmount, it.status as paymentStatus 
								from amenityBookingMain ab
								join amenityMaster am on ab.assetID = am.assetID 
								join amenityTimeSlot bts on ab.bookingTimeSlotID = bts.timeSlotID
								join memberMaster mm on ab.memberID = mm.memberID
								left join invoice iv on ab.invoiceID = iv.invoiceID
								left join invoiceTransaction it on ab.invoiceID = it.invoiceID
								where ab.status in (4,5,6) and am.complexID = " . $_SESSION['complexID'] . "
								UNION 
								select ab.bookingID, ab.assetID, ab.memberID, ab.complexID, ab.bookingReason, ab.bookingName, ab.bookingAddress, ab.bookingEmail, ab.bookingMobile, 
								ab.bookingCompany, ab.bookingDate, ab.bookingTimeSlotID, ab.guestCount, ab.foodType, ab.quantity, ab.invoiceID, ab.paidAmount, ab.transactionNumber, ab.paymentDate, '0' as referenceID, 
								ab.username, ab.createdate, ab.modifieddate, ab.remote_ip, ab.status as bookingStatus,
								am.assetTitle, mm.memberName, bts.slotStartTime, bts.slotEndTime, bts.slotType, bts.amount, 
								iv.billAmount, it.status as paymentStatus 
								from amenityBookingTemp ab
								join amenityMaster am on ab.assetID = am.assetID
								join amenityTimeSlot bts on ab.bookingTimeSlotID = bts.timeSlotID
								join memberMaster mm on ab.memberID = mm.memberID
								left join invoice iv on ab.invoiceID = iv.invoiceID
								left join invoiceTransaction it on ab.invoiceID = it.invoiceID
								where ab.status in (1,2,125) and am.complexID = " . $_SESSION['complexID']);
	$slotTypeArray = array("1" => "Half Day", "0" => "Full day", "2" => "Slot Wise");
	$statusArray = array("0" => "Pending", "1" => "Paid", "2" => "Failed");
	$bookingStatusArray = array("1" => "Blocked", "2" => "Booked", "125" => "Auo-Cancelled", "4" => "Confirmed", "5" => "Requested Refund", "6" => "Cancelled");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "bookingID:" . $res['bookingID'];

		$assetTitle = '<td>' . $res['assetTitle'] . '</td>';
		$memberName = '<td>' . $res['memberName'] . '</td>';
		if ($res['billAmount'] != null && !empty($res['billAmount'])) {
			$billAmount = '<td>₹ ' . $res['billAmount'] . '</td>';
		} else {
			$billAmount = '<td></td>';
		}

		$bookingDate = '<td>' . date('d M Y', strtotime($res['bookingDate'])) . '</td>';
		$timeslot = '<td>' . $slotTypeArray[$res['slotType']] . ': ' . date('h:i A', strtotime($res['slotStartTime'])) . ' to ' . date('h:i A', strtotime($res['slotEndTime'])) . '</td>';

		if (isset($res['bookingStatus'])) {
			$bookingStatus = '<td>' . $bookingStatusArray[$res['bookingStatus']] . '</td>';
		} else {
			$bookingStatus = '<td>' . "Pending" . '</td>';
		}

		if (isset($res['paymentStatus'])) {
			$paymentStatus = '<td>' . $statusArray[$res['paymentStatus']] . '</td>';
		} else {
			$paymentStatus = '<td>' . "Not Initiated" . '</td>';
		}

		$invoiceURL = "";
		if ($res['invoiceID'] != null && !empty($res['invoiceID'])) {
			$invoiceURL = '<a href="index.php?controller=complexmasters&action=invoice&subaction=invoiceForm&invoiceID=' . $res['invoiceID'] . '" title="View" ><i class="fas fa-file-invoice-dollar text-success"></i></a>';
		}
		$addFeesURL = "";
		if ($res['invoiceID'] != null && !empty($res['invoiceID'])) {
			$addFeesURL = '<a href="index.php?controller=complexmasters&action=invoice&subaction=feesform&invoiceID=' . $res['invoiceID'] . '" title="Add Payment" ><i class="fas fa-rupee-sign text-danger"></i></a>';
		}
		$updateFeesURL = "";
		if ($res['invoiceID'] != null && !empty($res['invoiceID'])) {
			$updateFeesURL = '<a href="index.php?controller=complexmasters&action=invoice&subaction=editForm&invoiceID=' . $res['invoiceID'] . '" title="Change Payment Status" ><i class="fe-edit text-info text-success"></i></a>';
		}
		$discountFeesURL = "";
		if ($res['invoiceID'] != null && !empty($res['invoiceID'])) {
			$discountFeesURL = '<a href="index.php?controller=complexmasters&action=invoice&subaction=discountForm&invoiceID=' . $res['invoiceID'] . '" title="Discount Amount" ><i class="fas fa-percentage text-warning"></i></a>';
		}
		$deleteURL = '<a href="index.php?controller=amenities&action=amenitiesbookingmaster&subaction=delete&bookingID=' . $res['bookingID'] . '&invoiceID=' . $res['invoiceID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>';

		if (isset($res['paymentStatus'])) {
			if ($res['paymentStatus'] == 1) {
				$Action = '<td>' . $invoiceURL . '</td>';
			} else if ($res['paymentStatus'] == 0) {
				$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $updateFeesURL . '</td>';
			} else {
				$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $addFeesURL . '</td>';
			}
		} else {
			$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $addFeesURL . '</td>';
		}
		$result['aaData'][] = array("$assetTitle", "$memberName", "$bookingDate", "$timeslot", "$bookingStatus", "$billAmount", "$paymentStatus", "$Action");
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