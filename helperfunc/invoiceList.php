<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$statusArray = array("0" => "Pending", "1" => "Completed", "2" => "Failed");
	$typeArray = array("9" => "Development Fees", "10" => "Maintenance Fees", "11" => "Amenties Booking", "12" => "Event Booking", "Society Invoice");
	//Same from main controller File
	$queryString = pro_db_query("select iv.*, it.paymentDate, it.paymentMethod, it.paymentDate, it.referenceNo, it.bankName, it.amount,
								it.status as paymentStatus, mm.*, bfom.*, bm.blockName, cm.complexName, om.officeName,
								cm.complexAddress, iv.invoiceAmount as mount FROM invoice iv
								left join blockFloorOfficeMapping bfom on iv.officeMappingID = bfom.officeMappingID and bfom.status = 1
								left join blockMaster bm on bfom.blockID = bm.blockID and bm.status = 1
								left join officeMaster om on om.officeID = bfom.officeID
								left join invoiceTransaction it on iv.invoiceID = it.invoiceID and it.transactionID in 
									(SELECT max(transactionID) as request_id
									FROM invoiceTransaction GROUP BY invoiceID
									order by transactionID desc)
								join memberMaster mm on bfom.memberID = mm.memberID 
								join complexMaster cm on iv.complexID = cm.complexID 
								where iv.status != 126 and iv.complexID = " . $_SESSION['complexID']);

	while ($res = pro_db_fetch_array($queryString)) {
		//$pk = "invoiceID:".$res['invoiceID'];
		//$invoiceNumber = '<td>'.$res['invoiceNumber'].'</td>';
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';
		if ($res['sendInvoiceToOwner'] == 0) {
			$name = '<td>' . $res['memberName'] . '</td>';
		} else if ($res['sendInvoiceToOwner'] == 1) {
			$name = '<td>' . $res['ownerName'] . '</td>';
		} else {
			$name = '<td>' . $res['memberName'] . '</td>';
		}
		if ($res['invoiceAmount'] != null && !empty($res['invoiceAmount'])) {
			$invoiceAmount = '<td>₹ ' . $res['invoiceAmount'] . '</td>';
		} else {
			$invoiceAmount = '<td></td>';
		}
		// $invoiceAmount = '<td>' . $res['billAmount'] . '</td>';
		$invoiceDate = '<td>' . date('d-M-Y', strtotime($res['invoiceDate'])) . '</td>';
		$invoiceDueDate = '<td>' . date('d-M-Y', strtotime($res['invoiceDueDate'])) . '</td>';

		if (isset($res['paymentDate'])) {
			$paymentDate = '<td>' . date('d-M-Y', strtotime($res['paymentDate'])) . '</td>';
		} else {
			$paymentDate = '<td></td>';
		}
		if (isset($res['paymentStatus'])) {
			$paymentStatus = '<td>' . $statusArray[$res['paymentStatus']] . '</td>';
		} else {
			$paymentStatus = '<td>' . "Not Initiated" . '</td>';
		}

		$invoiceType = '<td>' . $typeArray[$res['itemID']] . '</td>';

		if (isset($res['particulars'])) {
			$particulars = '<td>' . $res['particulars'] . '</td>';
		} else {
			$particulars = '<td></td>';
		}

		$paymentMethod = '<td>' . $res['paymentMethod'] . '</td>';
		$officeName = '<td>' . $res['officeName'] . '</td>';

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

		if (isset($res['paymentStatus'])) {
			if ($res['paymentStatus'] == 1) {
				$Action = '<td>' . $invoiceURL . '</td>';
			} else if ($res['paymentStatus'] == 0) {
				$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $updateFeesURL . '</td>';
			} else {
				$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $addFeesURL . '</td>';
			}
		} else {
			$Action = '<td>' . $invoiceURL . '&nbsp;&nbsp;&nbsp;' . $addFeesURL . '&nbsp;&nbsp;&nbsp;' . $discountFeesURL . '</td>';
		}
		$result['aaData'][] = array("$flatNumber", "$officeName", "$name", "$invoiceDate", "$invoiceDueDate", "$invoiceType", "$particulars", "$invoiceAmount", "$paymentDate", "$paymentMethod", "$paymentStatus", "$Action");
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