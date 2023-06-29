<?php
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
include "vendor/autoload.php";		//TODO: For - Server
// require "../vendor/autoload.php";	//TODO: For - cPanel Code

if (isset($_REQUEST['invoiceID']) && is_numeric($_REQUEST['invoiceID']) && $_REQUEST['invoiceID'] != '') {
	$societyqry = pro_db_query("select cas.isManually, iv.itemID from complexAccountSettings cas, invoice iv 
								where cas.complexID = iv.complexID and iv.invoiceID = " . $_REQUEST['invoiceID']);
	$societyrs = pro_db_fetch_array($societyqry);
	if (($societyrs['itemID'] == 11) || ($societyrs['itemID'] == 12)) {
			$qry = pro_db_query("select iv.invoiceID as InvoiceID, iv.officeMappingID, iv.complexID, iv.itemID, iv.invoiceDate, iv.invoiceType as inInvoiceType,
								iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount, iv.igstAmount, iv.taxPercentage, iv.taxAmount, iv.lateFeesAmount,
								iv.penaltyAmount, iv.discountAmount, iv.invoiceAmount as finalAmount, iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate,
								it.paymentMethod, it.referenceNo, it.bankName, it.amount, it.status paymentStatus, mm.memberID, mm.memberName, mm.memberEmail,
							    mm.memberMobile, bm.blockID, bm.blockName, bfom.floorNo, bfom.officeNumber, bfom.sendInvoiceToOwner, bfom.ownerName, bfom.ownerMobile, 
							    bfom.ownerEmail, cm.complexName, cm.complexAddress, cm.complexLogo, cas.panNumber, cas.isGSTApplicable, cas.gstNumber, cas.invoiceType,
							    cas.waiverDays, cas.cgstRate, cas.sgstRate, cas.igstRate, cas.bankName, cas.bankIFSC, cas.bankAddress, cas.accountName, cas.accountNumber,
							     cas.accountType from invoice iv 
								left join memberMaster mm on mm.memberID = iv.memberID
								left join blockFloorOfficeMapping bfom on mm.memberID = bfom.memberID 
								left join blockMaster bm on bfom.blockID = bm.blockID 
								left join invoiceTransaction it on iv.invoiceID = it.invoiceID 
								left join complexMaster cm on cm.complexID = iv.complexID 
								left join complexAccountSettings cas on cas.complexID = iv.complexID
								where bfom.isPrimary = 1 and iv.invoiceID = " . $_REQUEST['invoiceID'] . "
								order by it.transactionID desc limit 1");
	} else {
		if ($societyrs['isManually'] == 0) {
				$qry = pro_db_query("select iv.invoiceID as InvoiceID, iv.memberID, iv.officeMappingID, iv.complexID, iv.itemID, iv.invoiceDate, 
									iv.invoiceType as inInvoiceType, iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount, 
									iv.igstAmount, iv.taxPercentage, iv.taxAmount, iv.lateFeesAmount, iv.penaltyAmount, iv.discountAmount, 
									iv.invoiceAmount as finalAmount, iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate, 
									it.paymentMethod, it.referenceNo, it.bankName, it.amount, it.status as paymentStatus, 
									ivDet.memberName, ivDet.memberMobile, ivDet.memberResidence, ivDet.residenceArea, ivDet.maintenanceType, 
									ivDet.maintenanceRate, ivDet.maintenanceAmount, ivDet.penaltyType, ivDet.penaltyMode, ivDet.penaltyRate, 
									ivDet.penaltyDuration, ivDet.penaltyAmount as latePenaltyAmount, cm.complexName, cm.complexAddress, cm.complexLogo, 
									cas.panNumber, cas.isGSTApplicable, cas.gstNumber, cas.invoiceType, cas.waiverDays, cas.cgstRate, cas.sgstRate, 
									cas.igstRate, cas.bankName, cas.bankIFSC, cas.bankAddress, cas.accountName, cas.accountNumber, cas.accountType 
									from invoice iv 
									left join invoiceDetails ivDet on iv.invoiceID = ivDet.invoiceID 
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID 
									left join complexMaster cm on cm.complexID = iv.complexID 
									left join complexAccountSettings cas on sas.complexID = iv.complexID 
									where iv.invoiceID = " . $_REQUEST['invoiceID'] . " 
									order by it.transactionID desc limit 1");
		} else {
				$qry = pro_db_query("select iv.*, iv.invoiceID as InvoiceID,iv.invoiceAmount as finalAmount, it.*, 
									it.paymentDate, it.paymentMethod, it.referenceNo, it.bankName, it.amount, 
									it.status as paymentStatus,mm.*,bfm.*, bm.blockName, sm.complexName, 
									sm.complexAddress, cas.panNumber, sm.complexLogo, cas.gstNumber, fcm.type, fcm.waiverDays,
									fcm.cgstRate, fcm.sgstRate, fcm.noticeIntervalOne, fcm.noticeIntervalTwo, fcm.noticeIntervalThree,
									fcm.intervalOnePenalty, fcm.intervalTwoPenalty, fcm.intervalThreePenalty, fcm.discountAmount,
									cas.bankName, cas.accountNumber, cas.accountName, cas.bankIFSC, cas.bankAddress
									from invoice iv
									left join blockFloorOfficeMapping bfm on iv.officeMappingID = bfm.officeMappingID
									join flatInvoiceMapping fcm on iv.officeMappingID = fcm.officeMappingID 
									left join blockMaster bm on bfm.blockID = bm.blockID
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID
									left join memberMaster mm on iv.memberID = mm.memberID 
									left join complexMaster sm on sm.complexID = iv.complexID
                                    left join complexAccountSettings cas on cas.complexID = iv.complexID 
									where iv.invoiceID = " . $_REQUEST['invoiceID'] . "
									order by it.transactionID desc limit 1");
		}
	}
	$rs = pro_db_fetch_array($qry);

	$societyPaymentMode = "";
	$startDay = date("d", strtotime($rs['invoiceDate']));
	$startMonth = date("m", strtotime($rs['invoiceDate']));
	$startYear = date("Y", strtotime($rs['invoiceDate']));
	$startDate = date("d-M-Y", strtotime("01-" . $startMonth . "-" . $startYear));

	switch ($rs["invoiceType"]) {
		case "1":
			$societyPaymentMode = "Quarterly";
			$invoiceTimePeriod = "+3 months -1 day";
			$endDate = date("d-M-Y", strtotime($invoiceTimePeriod, strtotime($startDate)));
			$paymentPeriod = 3;
			break;

		case "2":
			$societyPaymentMode = "Half-Yearly";
			$invoiceTimePeriod = "+6 months -1 day";
			$endDate = date("d-M-Y", strtotime($invoiceTimePeriod, strtotime($startDate)));
			$paymentPeriod = 6;
			break;

		case "3":
			$societyPaymentMode = "Yearly";
			$invoiceTimePeriod = "+1 year -1 day";
			$endDate = date("d-M-Y", strtotime($invoiceTimePeriod, strtotime($startDate)));
			$paymentPeriod = 12;
			break;

		default:
			$societyPaymentMode = "Monthly";
			$invoiceTimePeriod = "+1 month -1 day";
			$endDate = date("d-M-Y", strtotime($invoiceTimePeriod, strtotime($startDate)));
			$paymentPeriod = 1;
			break;
	}

	$invoiceDate = $rs['invoiceDate'];
	$dueDate = $rs['invoiceDueDate'];

	$cgstRate = $rs['cgstRate'];
	$sgstRate = $rs['sgstRate'];

	//Discount
	if ($rs['discountAmount'] == null || $rs['discountAmount'] == "" || $rs['discountAmount'] <= 0.0) {
		$discountAmount = 0;
	} else {
		$discountAmount = $rs['discountAmount'];
	}
	//Late Fees
	if ($rs['latePenaltyAmount'] == null || $rs['latePenaltyAmount'] == "" || $rs['latePenaltyAmount'] <= 0.0) {
		$latePenaltyAmount = 0;
	} else {
		$latePenaltyAmount = $rs['latePenaltyAmount'];
	}
	//Penalty
	if ($rs['penaltyAmount'] == null || $rs['penaltyAmount'] == "" || $rs['penaltyAmount'] <= 0.0) {
		$penaltyAmount = 0;
	} else {
		$penaltyAmount = $rs['penaltyAmount'];
	}

	if ($rs['itemID'] == 10) {
		$flatMaintenanceAmount = $rs['maintenanceAmount'];
	}

	$billAmount = $rs['billAmount'];
	$subTotalAmount = $billAmount - $discountAmount + $latePenaltyAmount + $penaltyAmount;

	if ($rs['itemID'] == 10) {
		$invoiceType = "Building Maintenance";
		$flatSqFeetArea = round($rs['residenceArea'], 2);
		$maintenanceTypeTitle = $rs["maintenanceType"] == 0 ? "Flat Rate" : "Rate / Sq. Feet";
		$maintenancePerSqFeetRate = $rs['maintenanceRate'];

		$maintenancePenaltyAmount = $rs['penaltyRate'];
		$maintenancePenaltyDays = $rs['penaltyDuration'];

		if ($rs["penaltyMode"] == 0) {
			$maintenancePenaltyMode = " / daily";
			$maintenancePenaltyDayDetails = $maintenancePenaltyDays . ($maintenancePenaltyDays == 1 ? " day" : " days");
		} else if ($rs["penaltyMode"] == 1) {
			$maintenancePenaltyMode = " / monthly";
			$maintenancePenaltyDayDetails = $maintenancePenaltyDays . ($maintenancePenaltyDays == 1 ? " month" : " months");
		} else {
			$maintenancePenaltyMode = " - Fixed";
			$maintenancePenaltyDayDetails = "Fixed";
		}

		$txtParticular = $invoiceType . " (" . $rs['memberResidence'] . ")";
		$txtLateFees = "Late Fees: ₹" . $maintenancePenaltyAmount . $maintenancePenaltyMode;

		$maintenanceTypeRate = number_format(($rs["maintenanceType"] == 0 ? $flatMaintenanceAmount : $maintenancePerSqFeetRate), 2, '.', ',');
		$maintenanceAmountMonthly = number_format($flatMaintenanceAmount, 2, '.', ',');
		$maintenancePenaltyAmount = number_format($maintenancePenaltyAmount, 2, '.', ',');
	} else {
		if ($rs['itemID'] == 11) {
			$asesetsql = pro_db_query("select am.assetTitle from amenityMaster am 
										join amenityBookingTemp abt on am.assetID = abt.assetID
										join invoice iv on iv.invoiceID = abt.invoiceID where iv.invoiceID = " . $_REQUEST['invoiceID'] . "
										union
										select am.assetTitle from amenityMaster am
										join amenityBookingMain abm on am.assetID = abm.assetID
										join invoice iv on iv.invoiceID = abm.invoiceID where iv.invoiceID = " . $_REQUEST['invoiceID']);
			$assetsrs = pro_db_fetch_array($asesetsql);

			$invoiceType = "Amenities Booking";
			$txtParticular = $invoiceType . '<br/>' . $assetsrs['assetTitle'];
		} else if ($rs['itemID'] == 12) {
			$eventsql = pro_db_query("select em.eventName from eventMaster em
									join eventAttendees ea on em.eventID = ea.eventID
									join invoice iv on iv.invoiceID = ea.invoiceID where iv.invoiceID = " . $_REQUEST['invoiceID'] . "");
			$eventrs = pro_db_fetch_array($eventsql);

			$invoiceType = "Event Booking";
			$txtParticular = $invoiceType . '<br/>' . $eventrs['eventName'];
		} else if ($rs['itemID'] == 9) {
			$invoiceType = "Development Fees";
			$txtParticular = $invoiceType . " (" . $rs['blockName'] . " - " . $rs['officeNumber'] . ")";
		} else {
			$invoiceType = "Invalid Invoice";
			$txtParticular = $invoiceType;
		}
	}

	if ($rs['isGSTApplicable'] == 1) {
		$totalTitle = "Total Amount (A + B)";
	} else {
		$totalTitle = "Total Amount (A)";
	}

	if (!empty($rs['particulars'])) {
		$txtParticular .= '<br/>' . $rs['particulars'];
	}

	$societyLogo = "https://cdn.ggate.app/masters/society_logo_ggate.png";
	if ($rs['comlexLogo'] != null && !empty($rs['comlexLogo'])) {
		$societyLogo = $rs['comlexLogo'];
	}

	$billAmount = number_format($billAmount, 2, '.', ',');
	$discountAmount = number_format($discountAmount, 2, '.', ',');
	$totalLateFeesAmount = number_format($latePenaltyAmount, 2, '.', ',');
	$totalPenaltyAmount = number_format($penaltyAmount, 2, '.', ',');
	$subTotalAmount = number_format($subTotalAmount, 2, '.', ',');
	$cgstAmount = number_format($rs['cgstAmount'], 2, '.', ',');
	$sgstAmount = number_format($rs['sgstAmount'], 2, '.', ',');
	$totalTaxAmount = number_format($rs['taxAmount'], 2, '.', ',');
	$grossInvoiceAmount = number_format($rs['finalAmount'], 2, '.', ',');
	$paidAmount = number_format($rs['amount'], 2, '.', ',');

	//Remittance Details - Need to display
	$isDisplayRemittanceDetails = false;
	if (!empty($rs['bankName'])) {
		$isDisplayRemittanceDetails = true;
	}
	if (!empty($rs['accountName'])) {
		$isDisplayRemittanceDetails = true;
	}
	if (!empty($rs['accountNumber'])) {
		$isDisplayRemittanceDetails = true;
	}
	if (!empty($rs['bankIFSC'])) {
		$isDisplayRemittanceDetails = true;
	}
	if (!empty($rs['bankAddress'])) {
		$isDisplayRemittanceDetails = true;
	}

	$html = "";
	$html .= '
        <table cellpadding="2" cellspacing="0">
            <tr>
				<td align="left">
					<img src="' . $societyLogo . '" style="width:200px; max-width:240px;">
				</td>
				<td width="30%" align="right" style="font-size:14px;">' . $rs['complexName'] . '<br>
					' . nl2br($rs['complexAddress']) . '
				</td>
            </tr>
            <tr>
				<td colspan="2">
					<hr>
				</td>
			</tr>
			<tr>
				<td style="font-size:14px;">
					<b>To,</b><br>
					' . $rs['memberName'] . '<br>
					' . $rs['memberResidence'] . ', ' . $rs['complexName'] . '<br>
					' . nl2br($rs['complexAddress']) . '
				</td>
				<td align="right" style="font-size:14px;">';
	if ($rs['inInvoiceType'] == 1) {
		$html .= '<b>Tax Invoice # ' . $rs['invoiceNumber'] . '</b><br>';
	} else {
		$html .= '<b>Proforma Invoice # ' . $rs['InvoiceID'] . '</b><br>';
	}

	$html .= 'Date: ' . date('d-M-Y', strtotime($invoiceDate)) . '<br>
			Due Date: ' . date('d-M-Y', strtotime($dueDate)) . '<br>';
	if ($rs['itemID'] == 10) {
		$html .= 'Payment Mode: ' . $societyPaymentMode . '';
	}
	$html .= '</td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>';
	if ($rs['itemID'] == 10) {
		$html .= '<tr>
					<td colspan="5">
						<table class="items" cellspacing="0" cellpadding="4" width="100%">
							<tr style="background-color:#e5e5e5; text-align: center; font-size:14px;">
								<th width="5%">No.</th>
								<th>Particulars</th>';

		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<th width="10%">SAC Code</th>';
		}
		$html .= '<th width="15%">Sq. Feet</th>
								<th width="15%">' . $maintenanceTypeTitle . '</th>
								<th width="15%">Amount (₹)</th>
							</tr>
							<tr style="font-size:14px;">
								<td style="text-align: center; padding-bottom:95px;" valign="top" rowspan=3>1</td>
								<td style="padding-bottom:50px;" valign="top">' . $txtParticular . '</td>';

		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<td style="text-align: center; padding-bottom:95px;" valign="top" rowspan=3>9995</td>';
		}

		$html .= '<td style="padding-bottom:95px;" align="right" valign="top" rowspan=3>' . $flatSqFeetArea . '</td>
												<td style="padding-bottom:95px;" align="right" valign="top" rowspan=3>₹ ' . $maintenanceTypeRate . '</td>
												<td style="padding-bottom:40px;" align="right" valign="top">₹ ' . $maintenanceAmountMonthly . '</td>
								</tr>';

		$html .= '<tr style="font-size:14px;">
						<td valign="top">' . $societyPaymentMode . " (" . $startDate . " to " . $endDate . ")" . '</td>
						<td align="right" valign="top">x ' . $paymentPeriod . '</td>
					</tr>
					<tr style="font-size:14px;">
						<td valign="top">Total Maintenance Amount</td>
						<td align="right" valign="top"><b>₹ ' . $billAmount . '</b></td>
					</tr>';
		if ($rs['discountAmount'] > 0) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">Discount</td>';
			if ($rs['isGSTApplicable'] == 1) {
				$html .= '<td></td>';
			}

			$html .= '<td></td>
					<td></td>
					<td align="right">- ₹ ' . $discountAmount . '</td>
					</tr>';
		}
		if ($rs['lateFeesAmount'] > 0) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">' . $txtLateFees . '</td>';
			if ($rs['isGSTApplicable'] == 1) {
				$html .= '<td></td>';
			}

			$html .= '<td align="right" valign="top">₹ ' . $maintenancePenaltyAmount . '</td>
					<td align="right" valign="top">' . $maintenancePenaltyDayDetails . '</td>
					<td align="right">+ ₹ ' . $totalLateFeesAmount . '</td>
					</tr>';
		}
		if ($rs['penaltyAmount'] > 0) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">Penalty Fees</td>';
			if ($rs['isGSTApplicable'] == 1) {
				$html .= '<td></td>';
			}

			$html .= '<td></td>
					<td></td>
					<td align="right">+ ₹ ' . $totalPenaltyAmount . '</td>
					</tr>';
		}
		$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">Sub Total (A)</td>';
		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<td></td>';
		}

		$html .= '<td></td>
					<td></td>
					<td align="right"><b>₹ ' . $subTotalAmount . '</b></td>
					</tr>';

		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">CGST ' . $cgstRate . '%</td>
						<td></td>
						<td></td>
						<td align="right">₹ ' . $cgstAmount . '</td>
					</tr>
					<tr style="font-size:14px;">
						<td></td>
						<td align="right">SGST ' . $sgstRate . '%</td>
						<td></td>
						<td></td>
						<td align="right">₹ ' . $sgstAmount . '</td>
					</tr>
					<tr style="font-size:14px;">
						<td></td>
						<td align="right">Total GST Amount (B)</td>
						<td></td>
						<td></td>
						<td align="right"><b>₹ ' . $totalTaxAmount . '</b></td>
					</tr>';
		}

		$html .= '<tr style="height: 30px;">
						<td></td>
						<td></td>';
		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<td></td>';
		}
		$html .= '<td></td>
					<td></td>
					<td></td>
					</tr>';

		$html .= '<tr style="height: 30px; font-size:14px;">
						<td></td>
						<td align="right"><b>' . $totalTitle . '</b></td>';
		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<td></td>';
		}
		$html .= '<td></td>
					<td></td>
					<td align="right"><b>₹ ' . $grossInvoiceAmount . '</b></td>
					</tr>
					</table>
					</td>
					</tr>';
	} else {
		$html .= '<tr>
					<td colspan="5">
						<table class="items" cellspacing="0" cellpadding="4" width="100%">
							<tr style="background-color:#e5e5e5; text-align: center; font-size:14px;">
								<th width="10%">No.</th>
								<th>Particulars</th>
								<th width="20%">Amount (₹)</th>
							</tr>
							<tr style="font-size:14px;">
								<td style="text-align: center; padding-bottom:50px;" valign="top">1</td>
								<td style="padding-bottom:50px;" valign="top">' . $txtParticular . '</td>
								<td style="padding-bottom:50px;" align="right" valign="top">₹ ' . $billAmount . '</td>
							</tr>';
		if ($discountAmount > 0) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">Discount</td>
						<td align="right">- ₹ ' . $discountAmount . '</td>
					</tr>';
		}
		if ($totalPenaltyAmount > 0) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">Penalty Fees</td>
						<td align="right">+ ₹ ' . $totalPenaltyAmount . '</td>
					</tr>';
		}
		$html .= '<tr style="font-size:14px;">
					<td></td>
					<td align="right"><b>Sub Total (A)</b></td>
					<td align="right"><b>₹ ' . $subTotalAmount . '</b></td>
				</tr>';
		if ($rs['isGSTApplicable'] == 1) {
			$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right">CGST ' . $cgstRate . '%</td>
						<td align="right">₹ ' . $cgstAmount . '</td>
					</tr>
					<tr style="font-size:14px;">
						<td></td>
						<td align="right">SGST ' . $sgstRate . '%</td>
						<td align="right">₹ ' . $sgstAmount . '</td>
					</tr>
					<tr style="font-size:14px;">
						<td></td>
						<td align="right"><b>Total GST Amount (B)</b></td>
						<td align="right"><b>₹ ' . $totalTaxAmount . '</b></td>
					</tr>';
		}

		$html .= '<tr style="height: 30px; ">
						<td></td>
						<td></td>
						<td></td>
					</tr>';

		$html .= '<tr style="font-size:14px;">
						<td></td>
						<td align="right"><b>' . $totalTitle . '</b></td>
						<td align="right"><b>₹ ' . $grossInvoiceAmount . '</b></td>
					</tr>
					</table>
					</td>
					</tr>';
	}
	if ($rs['paymentStatus'] == 1) {
		$html .= '<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th colspan="4" align="left" style="font-size:14px;">Payment Details</th>
				</tr>
				<tr>
					<td colspan="2">
						<table class="items" cellspacing="0" cellpadding="4" width="100%">
							<tr style="background-color:#e5e5e5; font-size:13px;">
								<th>Payment Date</th>
								<th>Payment Method</th>
								<th>Transaction ID</th>
								<th align="right">Amount (₹)</th>
							</tr>
							<tr style="font-size:14px;">
								<td align="center">' . $rs['paymentDate'] . '</td>
								<td align="center">' . $rs['paymentMethod'] . '</td>
								<td align="center">' . $rs['referenceNo'] . '</td>
								<td align="right"><b>₹ ' . $paidAmount . '</b></td>
							</tr>
						</table>
					</td>
				</tr>';
	}
	$html .= '<tr>';

	$html .= '<td colspan="2">
				<table width="100%" style="border: none; font-size:15px; line-height: 20px;">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>';

	if (!empty($rs['panNumber'])) {
		$html .= '<td style="padding-left: 5px; font-size:13px;"><b>PAN: </b>' . $rs['panNumber'] . '</td>';
	}
	if ($rs['isGSTApplicable'] == 1) {
		$html .= '<td align="right" style="padding-right: 5px; font-size:13px;"><b>GST Number: </b>' . $rs['gstNumber'] . '</td>';
	}

	$html .= '</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>';

	if ($isDisplayRemittanceDetails) {
		$html .= '<tr style="font-size:14px;">
					<th colspan="2" align="left" style="padding: 5px 0px 5px 5px;">Remittance Details:</th>
				</tr>';
	}

	if (!empty($rs['bankName'])) {
		$html .= '<tr style="font-size:13px;">
					<td style="padding-left: 5px;"><b>Bank Name: </b>' . $rs['bankName'] . '</td>
					<td></td>
				</tr>';
	}

	if (!empty($rs['accountName'])) {
		$html .= '<tr style="font-size:13px;">
					<td style="padding-left: 5px;"><b>Account Title: </b>' . $rs['accountName'] . '</td>
					<td></td>
				</tr>';
	}

	if (!empty($rs['accountNumber'])) {
		$html .= '<tr style="font-size:13px;">
					<td style="padding-left: 5px;"><b>Account Number: </b>' . $rs['accountNumber'] . '</td>
					<td></td>
				</tr>';
	}

	if (!empty($rs['bankIFSC'])) {
		$html .= '<tr style="font-size:13px;">
					<td style="padding-left: 5px;"><b>IFSC Code: </b>' . $rs['bankIFSC'] . '</td>
					<td></td>
				</tr>';
	}

	$html .= '<tr style="font-size:13px;">';
	if (!empty($rs['bankAddress'])) {
		$html .= '<td width="50%" align="left" style="padding-left: 5px;"><b>Bank Address: </b>' . $rs['bankAddress'] . '</td>';
	} else {
		$html .= '<td width="50%" align="left"></td>';
	}
	$html .= '<td width="50%" align="right" style="padding-right: 5px;"><b>Issued By: </b>' . $rs['complexName'] . ' - Treasurer</td>
				</tr>
				</table>
			</td>
			</tr>
		</table>';

	$html .= '<div class="text-center">
				<p style="font-size:13px; text-align:center; padding-top: 50px;">Subjected to Surat Jurisdiction.<br />This is computerised generated invoice.</p>
				<div>
					<h5 class="text-center">This invoice is generated by GGATE system.</h5>
					<img src="https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg" style="display:block; margin-left:auto; margin-right:auto; width:100px; max-width:100px;">
				</div>
			</div>
		</body>
		</html>';

	$stylesheet1 = '
		<html>
		<head>
			<style>
				body {
					font-family: Verdana, Helvetica, sans-serif;
				}
				table {
					border-collapse: collapse;
				}
				table.items {
					border: 1px solid #cccccc;
					border-collapse: collapse;
				}
				table.items td, table.items th {
					padding: 1mm;
					border: 0.1mm solid #cccccc;
					vertical-align: middle;
				}
				.text-center {
					text-align: center;
				}
			</style>
		</head>
		<body>';

	//Generate PDF in case of Production & Staging Server
	if ($isProduction != 0) {
		$mpdf = new \Mpdf\Mpdf(['tempDir' => DIR_FS_PDF_TMP_PATH]);
		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle($_REQUEST['invoiceID']);
		$mpdf->SetAuthor($rs['societyName']);
		$mpdf->SetDisplayMode('fullpage');

		$mpdf->WriteHTML($stylesheet1);
		$mpdf->WriteHTML($html);
		// $mpdf->Output();

		$time = date("YmdHis");
		$filename = "GGATE_" . $_REQUEST['invoiceID'] . "_" . $time . ".pdf";
		$mpdf->Output($filename, 'I');
	} else {
		echo $stylesheet1 . $html;
	}


	//TODO: For - cPanel Code
	/*
	$mpdf = new \Mpdf\Mpdf;
	$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle($_REQUEST['invoiceID']);
	$mpdf->SetAuthor($rs['societyName']);
	$mpdf->SetDisplayMode('fullpage');

	$mpdf->WriteHTML($stylesheet1);
	$mpdf->WriteHTML($html);
	// $mpdf->Output();

	$time = date("YmdHis");
	$filename = "GGATE_" . $_REQUEST['invoiceID'] . "_" . $time . ".pdf";
	$mpdf->Output($filename, 'I');
	*/

	exit;
} else {
	echo "Something went wrong !!!";
	exit;
}
?>
