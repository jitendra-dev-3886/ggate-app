<?php
class invoice
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $addfeesformaction;
	protected $updatefeesformaction;
	protected $discountformaction;
	protected $createinvoiceformaction;
	protected $editformaction;
	protected $listformaction;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->addfeesformaction = $this->redirectUrl . "&subaction=addFees";
		$this->updatefeesformaction = $this->redirectUrl . "&subaction=updateFees";
		$this->discountformaction = $this->redirectUrl . "&subaction=discount";
		$this->createinvoiceformaction = $this->redirectUrl . "&subaction=createInvoice";
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=createInvoiceForm";
?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Complex Invoices</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right ml-2"><i class="fe-plus"></i>&nbsp;&nbsp;Generate Invoice</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="invoiceList" width="100%">
								<thead>
									<tr>
										<th width="5%" align="left">Office</th>
										<th align="left">Office Admin</th>
										<th width="8%" align="left">Invoice Date</th>
										<th width="8%" align="left">Due Date</th>
										<th width="10%" align="left">Invoice Type</th>
										<th align="left">Particulars</th>
										<th width="8%" align="left">Invoice Amount</th>
										<th width="8%" align="left">Payment Date</th>
										<th width="5%" align="left">Payment Method</th>
										<th width="10%" align="left">Payment Status</th>
										<th width="8%" align="left">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="5%" align="left">Office</th>
										<th align="left">Office Admin</th>
										<th width="8%" align="left">Invoice Date</th>
										<th width="8%" align="left">Due Date</th>
										<th width="10%" align="left">Invoice Type</th>
										<th align="left">Particulars</th>
										<th width="8%" align="left">Invoice Amount</th>
										<th width="8%" align="left">Payment Date</th>
										<th width="5%" align="left">Payment Method</th>
										<th width="10%" align="left">Payment Status</th>
										<th width="8%" align="left">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/invoiceList.php';
			$('#invoiceList').dataTable({
				dom: 'Bfrtip',
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});
		</script>
	<?php
	}

	public function invoiceForm()
	{
		$societyqry = pro_db_query("select isManually from societyMaster where societyID = " . $_SESSION['societyID']);
		$societyrs = pro_db_fetch_array($societyqry);

		$invoicetypeqry = pro_db_query("select itemID from invoice where invoiceID = " . $_REQUEST['invoiceID']);
		$invoicetypers = pro_db_fetch_array($invoicetypeqry);

		
		if (($invoicetypers['itemID'] != 9) && ($invoicetypers['itemID'] != 10)) {
			$qry = pro_db_query("select iv.invoiceID as InvoiceID, iv.flatID, iv.societyID, iv.itemID, iv.invoiceDate, iv.invoiceType as inInvoiceType,
								iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount, iv.igstAmount, iv.taxPercentage,
								iv.taxAmount, iv.lateFeesAmount as latePenaltyAmount, iv.penaltyAmount, iv.discountAmount, iv.invoiceAmount as finalAmount,
								iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate, it.paymentMethod, it.referenceNo,
								it.bankName, it.amount, it.status paymentStatus, mm.memberID, mm.memberName, mm.memberEmail, mm.memberMobile,
								bm.blockID, bm.blockName, bfm.floorNo, bfm.flatNumber, bfm.sendInvoiceToOwner, bfm.ownerName, bfm.ownerMobile, bfm.ownerEmail,
								sm.societyName, sm.societyAddress, sm.societyLogo,
								sas.panNumber, sas.isGSTApplicable, sas.gstNumber, sas.invoiceType, sas.waiverDays, sas.cgstRate, sas.sgstRate, sas.igstRate,
								sas.bankName, sas.bankIFSC, sas.bankAddress, sas.accountName, sas.accountNumber, sas.accountType
								from invoice iv
								left join memberMaster mm on mm.memberID = iv.memberID
								left join blockFloorFlatMapping bfm on mm.memberID = bfm.memberID
								left join blockMaster bm on bfm.blockID = bm.blockID
								left join invoiceTransaction it on iv.invoiceID = it.invoiceID
								left join societyMaster sm on sm.societyID = iv.societyID
								left join societyAccountSettings sas on sas.societyID = iv.societyID
								where bfm.isPrimary = 1 and iv.invoiceID = " . $_REQUEST['invoiceID'] . "
								order by it.transactionID desc limit 1");
		} else {
			if ($societyrs['isManually'] == 0) {
				$qry = pro_db_query("select iv.invoiceID as InvoiceID, iv.memberID, iv.flatID, iv.societyID, iv.itemID, iv.invoiceDate, 
									iv.invoiceType as inInvoiceType, iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount, 
									iv.igstAmount, iv.taxPercentage, iv.taxAmount, iv.lateFeesAmount, iv.penaltyAmount, iv.discountAmount, 
									iv.invoiceAmount as finalAmount, iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate, 
									it.paymentMethod, it.referenceNo, it.bankName, it.amount, it.status as paymentStatus, 
									ivDet.memberName, ivDet.memberMobile, ivDet.memberResidence, ivDet.residenceArea, ivDet.maintenanceType, 
									ivDet.maintenanceRate, ivDet.maintenanceAmount, ivDet.penaltyType, ivDet.penaltyMode, ivDet.penaltyRate, 
									ivDet.penaltyDuration, ivDet.penaltyAmount as latePenaltyAmount, sm.societyName, sm.societyAddress, sm.societyLogo, 
									sas.panNumber, sas.isGSTApplicable, sas.gstNumber, sas.invoiceType, sas.waiverDays, sas.cgstRate, sas.sgstRate, 
									sas.igstRate, sas.bankName, sas.bankIFSC, sas.bankAddress, sas.accountName, sas.accountNumber, sas.accountType 
									from invoice iv 
									left join invoiceDetails ivDet on iv.invoiceID = ivDet.invoiceID 
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID 
									left join societyMaster sm on sm.societyID = iv.societyID 
									left join societyAccountSettings sas on sas.societyID = iv.societyID 
									where iv.invoiceID = " . $_REQUEST['invoiceID'] . " 
									order by it.transactionID desc limit 1");
			} else {
				$qry = pro_db_query("select iv.*, iv.invoiceID as InvoiceID, iv.invoiceAmount as finalAmount, it.*, 
									it.paymentDate, it.paymentMethod, it.referenceNo, it.bankName, it.amount, 
									it.status as paymentStatus, mm.*, bfm.*, bm.blockName, sm.societyName, 
									sm.societyAddress, sm.panNumber, sm.societyLogo, sm.gstNumber, fcm.type, fcm.waiverDays,
									fcm.cgstRate, fcm.sgstRate, fcm.noticeIntervalOne, fcm.noticeIntervalTwo, fcm.noticeIntervalThree,
									fcm.intervalOnePenalty, fcm.intervalTwoPenalty, fcm.intervalThreePenalty, fcm.discountAmount,
									sm.bankName, sm.accountTitle, sm.accountNumber, sm.IFSCCode, sm.bankAddress
									from invoice iv
									left join blockFloorFlatMapping bfm on iv.flatID = bfm.flatID
									join flatInvoiceMapping fcm on iv.flatID = fcm.flatID 
									left join blockMaster bm on bfm.blockID = bm.blockID
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID
									left join memberMaster mm on iv.memberID = mm.memberID 
									left join societyMaster sm on sm.societyID = iv.societyID 
									where iv.invoiceID = " . $_REQUEST['invoiceID'] . "
									order by it.transactionID desc limit 1");
			}
		}
		

		$qry = pro_db_query("select iv.invoiceID as InvoiceID, iv.memberID, iv.flatID, iv.societyID, iv.itemID, iv.invoiceDate,
							iv.invoiceType as inInvoiceType, iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount,
							iv.igstAmount, iv.taxPercentage, iv.taxAmount, iv.lateFeesAmount, iv.penaltyAmount, iv.discountAmount,
							iv.invoiceAmount as finalAmount, iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate,
							it.paymentMethod, it.referenceNo, it.bankName, it.amount, it.status as paymentStatus,
							ivDet.memberName, ivDet.memberMobile, ivDet.memberResidence, ivDet.particularTitle, ivDet.particularSubtitle,
							ivDet.residenceArea, ivDet.maintenanceType, ivDet.maintenanceRate, ivDet.maintenanceAmount, ivDet.penaltyType,
							ivDet.penaltyMode, ivDet.penaltyRate, ivDet.penaltyDuration, ivDet.penaltyAmount as latePenaltyAmount,
							sm.societyName, sm.societyAddress, sm.societyLogo, sas.panNumber, sas.isGSTApplicable, sas.gstNumber,
							sas.invoiceType, sas.waiverDays, sas.cgstRate, sas.sgstRate, sas.igstRate, sas.bankName, sas.bankIFSC,
							sas.bankAddress, sas.accountName, sas.accountNumber, sas.accountType
							from invoice iv
							left join invoiceDetails ivDet on iv.invoiceID = ivDet.invoiceID
							left join invoiceTransaction it on iv.invoiceID = it.invoiceID
							left join societyMaster sm on sm.societyID = iv.societyID
							left join societyAccountSettings sas on sas.societyID = iv.societyID
							where iv.invoiceID = " . $_REQUEST['invoiceID'] . "
							order by it.transactionID desc limit 1");
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
			} else if ($rs['itemID'] == 13 || $rs['itemID'] == 14) {
				$txtParticular = $rs['itemID'] == 13 ? "Electricity Bill" : "Water Bill";
				$strParticularTitle = $rs["particularTitle"];
				if (empty($strParticularTitle)) {
					$strParticularTitle = $rs['itemID'] == 13 ? "Electricity Bill" : "Water Bill";
				}
				$strParticularSubtitle = $rs["particularSubtitle"];

				$strBillType = $rs["maintenanceType"];
				$strBillRate = $rs["maintenanceRate"];
				$strBillAmount = $rs["maintenanceAmount"];

				$maintenanceTypeTitle = ($strBillType == 2) ? "Total Units" : "Unit Amount";
				$maintenancePerSqFeetRate = ($strBillType == 2) ? "Rate / Unit" : "";
			} else if ($rs['itemID'] == 9) {
				$invoiceType = "Development Fees";
				$txtParticular = $invoiceType . " (" . $rs['memberResidence'] . ")";
			} else {
				$invoiceType = "Invalid Invoice";
				$txtParticular = $invoiceType;
			}
		}

		if (!empty($rs['particulars'])) {
			$txtParticular .= '<br/>' . $rs['particulars'];
		}

		$societyLogo = "https://cdn.ggate.app/masters/society_logo_ggate.png";
		if ($rs['societyLogo'] != null && !empty($rs['societyLogo'])) {
			$societyLogo = $rs['societyLogo'];
		}
	?>
		<style>
			table {
				font-family: sans-serif;
				border-collapse: collapse;
			}

			table.items {
				font-family: sans-serif;
				border: 1px solid #cccccc;
				border-collapse: collapse;
			}

			table.items td,
			table.items th {
				padding: 1mm;
				border: 0.1mm solid #cccccc;
				vertical-align: middle;
			}
		</style>
		<div class="row" style="justify-content:end">
			<div class="col-sm-9" style="justify-content:start">

			</div>
			<div class="col-sm-3" style="justify-content:end">
				<a href="<?php echo WS_ADMIN_ROOT . "invoicePDF.php?invoiceID=" . $rs['InvoiceID'] ?>" class="btn btn-success back" style="margin-bottom: 2rem;" target="_blank">Download PDF</a>
			</div>
		</div>
		<div class="row" style="justify-content:center">
			<div class="col-sm-10">
				<div class="card">
					<div class="card-body">
						<table cellpadding="2" cellspacing="0">
							<tr>
								<td>
									<img src=" <?php echo $societyLogo; ?>" style="width:230px; max-width:230px;" class="img-fluid">
								</td>
								<td width="30%" style="font-size:15px;">
									<?php echo $rs['societyName']; ?> <br>
									<?php echo nl2br($rs['societyAddress']); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr style="font-size:15px;">
								<td>
									<b>To,</b><br>
									<?php echo $rs['memberName']; ?><br>
									<?php echo $rs['memberResidence'] . ", " . $rs['societyName']; ?><br>
									<?php echo nl2br($rs['societyAddress']); ?>
								</td>
								<td align="right">
									<?php if ($rs['inInvoiceType'] == 1) {
										echo "<b>Tax Invoice # " . $rs['invoiceNumber'] . "</b><br>";
									} else {
										echo "<b>Proforma Invoice # " . $rs['InvoiceID'] . "</b><br>";
									} ?>
									Date: <?php echo date('d-M-Y', strtotime($invoiceDate)); ?><br>
									Due Date: <?php echo date('d-M-Y', strtotime($dueDate)); ?><br>
									<?php if ($rs['itemID'] == 10) { ?>
										Payment: <?php echo $societyPaymentMode; ?>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="5">&nbsp;</td>
							</tr>
							<?php if ($rs['itemID'] == 10) { ?>
								<tr>
									<td colspan="5">
										<table class="items" cellspacing="0" cellpadding="4" width="100%" style="font-size:15px;">
											<tr style="background-color:#e5e5e5; text-align: center;">
												<th width="5%">Sr. No.</th>
												<th>Particulars</th>
												<?php if ($rs['isGSTApplicable'] == 1) { ?>
													<th width="10%">SAC Code</th>
												<?php } ?>
												<th width="15%">Sq. Feet</th>
												<th width="15%"><?php echo $maintenanceTypeTitle; ?></th>
												<th width="15%">Amount (₹)</th>
											</tr>
											<tr>
												<td style="text-align: center; padding-bottom:95px;" valign="top" rowspan=3>1</td>
												<td style="padding-bottom:40px;" valign="top"><?php echo $txtParticular; ?></td>
												<?php if ($rs['isGSTApplicable'] == 1) { ?>
													<td style="text-align: center; padding-bottom:95px;" valign="top" rowspan=3>9995</td>
												<?php } ?>
												<td style="padding-bottom:95px;" align="right" valign="top" rowspan=3><?php echo $flatSqFeetArea; ?></td>
												<td style="padding-bottom:95px;" align="right" valign="top" rowspan=3>₹ <?php echo number_format(($rs["maintenanceType"] == 0 ? $flatMaintenanceAmount : $maintenancePerSqFeetRate), 2, '.', ','); ?></td>
												<td style="padding-bottom:40px;" align="right" valign="top">₹ <?php echo number_format($flatMaintenanceAmount, 2, '.', ','); ?></td>
											</tr>
											<tr>
												<td valign="top"><?php echo "" . $societyPaymentMode . " (" . $startDate . " to " . $endDate . ")"; ?></td>
												<td align="right" valign="top">x <?php echo $paymentPeriod; ?></td>
											</tr>
											<tr>
												<td valign="top">Total Maintenance Amount</td>
												<td align="right" valign="top"><b>₹ <?php echo number_format($rs['billAmount'], 2, '.', ','); ?></b></td>
											</tr>
											<?php if ($rs['discountAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Discount</td>
													<?php if ($rs['isGSTApplicable'] == 1) { ?>
														<td></td>
													<?php } ?>
													<td></td>
													<td></td>
													<td align="right">- ₹ <?php echo number_format($rs['discountAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php
											}
											if ($rs['lateFeesAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right"><?php echo $txtLateFees; ?></td>
													<?php if ($rs['isGSTApplicable'] == 1) { ?>
														<td></td>
													<?php } ?>
													<td align="right" valign="top">₹ <?php echo number_format($maintenancePenaltyAmount, 2, '.', ','); ?></td>
													<td align="right" valign="top"><?php echo $maintenancePenaltyDayDetails; ?></td>
													<td align="right">+ ₹ <?php echo number_format($rs['lateFeesAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php
											}
											if ($rs['penaltyAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Penalty Fees</td>
													<?php if ($rs['isGSTApplicable'] == 1) { ?>
														<td></td>
													<?php } ?>
													<td></td>
													<td></td>
													<td align="right">+ ₹ <?php echo number_format($rs['penaltyAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td></td>
												<td align="right"><b>Sub Total (A)</b></td>
												<?php if ($rs['isGSTApplicable'] == 1) { ?>
													<td></td>
												<?php } ?>
												<td></td>
												<td></td>
												<td align="right"><b>₹ <?php echo number_format($subTotalAmount, 2, '.', ','); ?></b></td>
											</tr>
											<?php
											if ($rs['isGSTApplicable'] == 1) { ?>
												<tr>
													<td></td>
													<td align="right">CGST <?php echo number_format($cgstRate); ?>%</td>
													<td></td>
													<td></td>
													<td></td>
													<td align="right">₹ <?php echo number_format($rs['cgstAmount'], 2, '.', ','); ?></td>
												</tr>
												<tr>
													<td></td>
													<td align="right">SGST <?php echo number_format($sgstRate); ?>%</td>
													<td></td>
													<td></td>
													<td></td>
													<td align="right">₹ <?php echo number_format($rs['sgstAmount'], 2, '.', ','); ?></td>
												</tr>
												<tr>
													<td></td>
													<td align="right"><b>GST Amount (B)</b></td>
													<td></td>
													<td></td>
													<td></td>
													<td align="right"><b>₹ <?php echo number_format($rs['taxAmount'], 2, '.', ','); ?></b></td>
												</tr>
											<?php } ?>
											<tr style="height: 30px;">
												<td></td>
												<td></td>
												<?php if ($rs['isGSTApplicable'] == 1) { ?>
													<td></td>
												<?php } ?>
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<?php
											if ($rs['isGSTApplicable'] == 1) {
												$totalTitle = "Total Amount (A + B)";
											} else {
												$totalTitle = "Total Amount (A)";
											}
											?>
											<tr>
												<td></td>
												<td align="right"><b><?php echo $totalTitle; ?></b></td>
												<?php if ($rs['isGSTApplicable'] == 1) { ?>
													<td></td>
												<?php } ?>
												<td></td>
												<td></td>
												<td align="right"><b>₹ <?php echo number_format($rs['finalAmount'], 2, '.', ','); ?></b></td>
											</tr>
										</table>
									</td>
								</tr>
							<?php
							} else if ($rs['itemID'] == 13 || $rs['itemID'] == 14) {
							?>
								<tr>
									<td colspan="5">
										<table class="items" cellspacing="0" cellpadding="4" width="100%" style="font-size:15px;">
											<tr style="background-color:#e5e5e5; text-align: center;">
												<th width="5%">Sr. No.</th>
												<th>Particulars</th>
												<th width="15%"><?php echo $maintenanceTypeTitle; ?></th>
												<?php if ($strBillType == 2) { ?>
													<th width="15%"><?php echo $maintenancePerSqFeetRate; ?></th>
												<?php } ?>
												<th width="15%">Amount (₹)</th>
											</tr>
											<tr>
												<td style="text-align: center; padding-bottom:80px;" valign="top">1</td>
												<td style="padding-bottom:65px;" valign="top"><?php echo $strParticularTitle . '<br/>' . $strParticularSubtitle; ?></td>
												<?php if ($strBillType == 2) {
													$totalUnits = $strBillAmount / $strBillRate;
												?>
													<td style="padding-bottom:80px;" align="right" valign="top"><?php echo $totalUnits; ?></td>
													<td style="padding-bottom:80px;" align="right" valign="top">₹ <?php echo number_format($strBillRate, 2, '.', ','); ?></td>
												<?php } else {
												?>
													<td style="padding-bottom:80px;" align="right" valign="top">₹ <?php echo number_format($strBillRate, 2, '.', ','); ?></td>
												<?php
												} ?>
												<td style="padding-bottom:80px;" align="right" valign="top">₹ <?php echo number_format($strBillAmount, 2, '.', ','); ?></td>
											</tr>
											<?php if ($rs['discountAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Discount</td>
													<?php if ($strBillType == 2) {
													?>
														<td></td>
													<?php
													} ?>
													<td></td>
													<td align="right">- ₹ <?php echo number_format($rs['discountAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php
											}
											if ($rs['lateFeesAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right"><?php echo "Late Fees"; ?></td>
													<?php if ($strBillType == 2) {
													?>
														<td></td>
													<?php
													} ?>
													<td></td>
													<td align="right">+ ₹ <?php echo number_format($rs['lateFeesAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php
											}
											if ($rs['penaltyAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Penalty Fees</td>
													<?php if ($strBillType == 2) {
													?>
														<td></td>
													<?php
													} ?>
													<td></td>
													<td align="right">+ ₹ <?php echo number_format($rs['penaltyAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td></td>
												<td align="right"><b>Sub Total (A)</b></td>
												<?php if ($strBillType == 2) {
												?>
													<td></td>
												<?php
												} ?>
												<td></td>
												<td align="right"><b>₹ <?php echo number_format($subTotalAmount, 2, '.', ','); ?></b></td>
											</tr>
											<tr style="height: 30px;">
												<td></td>
												<td></td>
												<?php if ($strBillType == 2) {
												?>
													<td></td>
												<?php
												} ?>
												<td></td>
												<td></td>
											</tr>
											<?php
											$totalTitle = "Total Amount (A)";
											?>
											<tr>
												<td></td>
												<td align="right"><b><?php echo $totalTitle; ?></b></td>
												<?php if ($strBillType == 2) {
												?>
													<td></td>
												<?php
												} ?>
												<td></td>
												<td align="right"><b>₹ <?php echo number_format($rs['finalAmount'], 2, '.', ','); ?></b></td>
											</tr>
										</table>
									</td>
								</tr>
							<?php
							} else { ?>
								<tr>
									<td colspan="5">
										<table class="items" cellspacing="0" cellpadding="4" width="100%" style="font-size:15px;">
											<tr style="background-color:#e5e5e5; text-align: center;">
												<th width="10%">Sr. No.</th>
												<th>Particulars</th>
												<th width="20%">Amount (₹)</th>
											</tr>
											<tr>
												<td style="text-align : center; padding-bottom:100px;" valign="top">1</td>
												<td style="padding-bottom:100px;" valign="top"><?php echo $txtParticular; ?></td>
												<td style="padding-bottom:100px;" align="right" valign="top">₹ <?php echo number_format($billAmount, 2, '.', ','); ?></td>
											</tr>
											<?php if ($rs['discountAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Discount</td>
													<td align="right">- ₹ <?php echo number_format($rs['discountAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php
											}
											if ($rs['penaltyAmount'] > 0) { ?>
												<tr>
													<td></td>
													<td align="right">Penalty Fees</td>
													<td align="right">+ ₹ <?php echo number_format($rs['penaltyAmount'], 2, '.', ','); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td></td>
												<td align="right"><b>Sub Total (A)</b></td>
												<td align="right"><b>₹ <?php echo number_format($subTotalAmount, 2, '.', ','); ?></b></td>
											</tr>
											<?php if ($rs['isGSTApplicable'] == 1) { ?>
												<tr>
													<td></td>
													<td align="right">CGST <?php echo number_format($cgstRate); ?>%</td>
													<td align="right">₹ <?php echo number_format($rs['cgstAmount'], 2, '.', ','); ?></td>
												</tr>
												<tr>
													<td></td>
													<td align="right">SGST <?php echo number_format($sgstRate); ?>%</td>
													<td align="right">₹ <?php echo number_format($rs['sgstAmount'], 2, '.', ','); ?></td>
												</tr>
												<tr>
													<td></td>
													<td align="right"><b>GST Amount (B)</b></td>
													<td align="right"><b>₹ <?php echo number_format($rs['taxAmount'], 2, '.', ','); ?></b></td>
												</tr>
											<?php } ?>
											<tr style="height: 30px;">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<?php
											if ($rs['isGSTApplicable'] == 1) {
												$totalTitle = "Total Amount (A + B)";
											} else {
												$totalTitle = "Total Amount (A)";
											}
											?>
											<tr>
												<td></td>
												<td align="right"><b><?php echo $totalTitle; ?></b></td>
												<td align="right"><b>₹ <?php echo number_format($rs['finalAmount'], 2, '.', ','); ?></b></td>
											</tr>
										</table>
									</td>
								</tr>
							<?php }
							if ($rs['paymentStatus'] == 1) {
							?>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr style="font-size:15px;">
									<th colspan="4" align="left">Payment Details</th>
								</tr>
								<tr>
									<td colspan="2">
										<table class="items" cellspacing="0" cellpadding="4" width="100%">
											<tr style="background-color:#e5e5e5; font-size:14px;">
												<th style="text-align: center;">Payment Date</th>
												<th style="text-align: center;">Payment Method</th>
												<th style="text-align: center;">Transaction ID</th>
												<th style="text-align: right;">Amount (₹)</th>
											</tr>
											<tr style="font-size:14px;">
												<td style="text-align: center;"><?php echo $rs['paymentDate']; ?></td>
												<td style="text-align: center;"><?php echo $rs['paymentMethod']; ?></td>
												<td style="text-align: center;"><?php echo $rs['referenceNo']; ?></td>
												<td style="text-align: right;"><b>₹<?php echo number_format($rs['amount'], 2, '.', ','); ?></b></td>
											</tr>
										</table>
									</td>
								</tr>
							<?php
							}
							?>
							<tr>
								<td colspan="2">
									<table width="100%" style="border: none; font-size:15px; line-height: 25px;">
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<?php if (!empty($rs['panNumber'])) { ?>
												<td style="padding-left: 5px;"><b>PAN: </b><?php echo $rs['panNumber'] ?></td>
											<?php } ?>
											<?php if ($rs['isGSTApplicable'] == 1) { ?>
												<td align="right" style="padding-right: 5px;"><b>GST Number: </b><?php echo $rs['gstNumber'] ?></td>
											<?php } ?>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr style="background-color:#e5e5e5;">
											<th colspan="2" align="left" style="padding: 5px 0px 5px 5px;">Remittance Details</th>
										</tr>

										<?php if (!empty($rs['bankName'])) { ?>
											<tr>
												<td style="padding-left: 5px;"><b>Bank Name: </b><?php echo $rs['bankName'] ?></td>
												<td></td>
											</tr>
										<?php } ?>

										<?php if (!empty($rs['accountName'])) { ?>
											<tr>
												<td style="padding-left: 5px;"><b>Account Title: </b><?php echo $rs['accountName'] ?></td>
												<td></td>
											</tr>
										<?php } ?>

										<?php if (!empty($rs['accountNumber'])) { ?>
											<tr>
												<td style="padding-left: 5px;"><b>Account Number: </b><?php echo $rs['accountNumber'] ?></td>
												<td></td>
											</tr>
										<?php } ?>

										<?php if (!empty($rs['bankIFSC'])) { ?>
											<tr>
												<td style="padding-left: 5px;"><b>IFSC Code: </b><?php echo $rs['bankIFSC'] ?></td>
												<td></td>
											</tr>
										<?php } ?>

										<tr>
											<?php if (!empty($rs['bankAddress'])) { ?>
												<td style="padding-left: 5px;"><b>Bank Address: </b><?php echo $rs['bankAddress'] ?></td>
											<?php } ?>
											<td align="right" style="padding-right: 5px;"><b>Issued By: </b><?php echo $rs['societyName'] . " - Treasurer"; ?> </td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
									</table>

									<p style="font-size:13px; text-align:center;">Subjected to Surat Jurisdiction.<br />This is computerised generated invoice.</p>

									<div class="card text-center">
										<h6 class="text-center">This invoice is generated by GGATE system.</h6>
										<img src="https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg" style="display:block; margin-left:auto; margin-right:auto; width:100px; max-width:100px;" class="img-fluid">
										<!-- https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg -->
										</p>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function feesForm()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
		$invoiceID = (int)$_REQUEST['invoiceID'];
		$invoiceSql = pro_db_query("select iv.invoiceID as InvoiceID, iv.flatID, iv.societyID, iv.itemID, iv.invoiceDate, iv.invoiceType as inInvoiceType,
									iv.particulars, iv.invoiceNumber, iv.billAmount, iv.cgstAmount, iv.sgstAmount, iv.igstAmount, iv.taxPercentage,
									iv.taxAmount, iv.lateFeesAmount, iv.penaltyAmount, iv.discountAmount, iv.invoiceAmount as finalAmount,
									iv.invoiceDueDate, iv.status, it.transactionID, it.paymentDate, it.paymentMethod, it.referenceNo,
									it.bankName, it.amount, it.status paymentStatus, mm.memberID, mm.memberName, mm.memberEmail, mm.memberMobile,
									bm.blockID, bm.blockName, bfm.floorNo, bfm.flatNumber, bfm.occupationType, bfm.flatMaintenanceType,
									case 
									when (bfm.occupationType = 1 and bfm.flatMaintenanceAmt = 0.00) then sms.ownerAmount
									when (bfm.occupationType = 2 and bfm.flatMaintenanceAmt = 0.00) then sms.rentalAmount
									else bfm.flatMaintenanceAmt end as flatMaintenanceAmount, bfm.flatArea,
									bfm.sendInvoiceToOwner, bfm.ownerName, bfm.ownerMobile, bfm.ownerEmail,
									sm.societyName, sm.societyAddress, sm.societyLogo,
									sms.maintenanceType, sms.ownerSqFtRate, sms.rentalSqFtRate, sms.ownerAmount, sms.rentalAmount,
									sms.penaltyType, sms.penaltyMode, sms.ownerPenaltyAmount, sms.rentalPenaltyAmount,
									sas.panNumber, sas.isGSTApplicable, sas.gstNumber, sas.invoiceType, sas.waiverDays, sas.cgstRate, sas.sgstRate, sas.igstRate,
									sas.bankName, sas.bankIFSC, sas.bankAddress, sas.accountName, sas.accountNumber, sas.accountType
									from invoice iv
									left join blockFloorFlatMapping bfm on iv.flatID = bfm.flatID
									left join societyMaintenanceSettings sms on bfm.blockID = sms.blockID and bfm.societyID = sms.societyID
									left join blockMaster bm on bfm.blockID = bm.blockID
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID
									left join memberMaster mm on bfm.memberID = mm.memberID
									left join societyMaster sm on sm.societyID = iv.societyID
									left join societyAccountSettings sas on sas.societyID = iv.societyID
									where iv.invoiceID = " . $_REQUEST['invoiceID'] . "
									order by it.transactionID desc limit 1");
		$row = pro_db_fetch_array($invoiceSql);
		$paymentStatus = generateStaticOptions(array("0" => "Pending", "1" => "Completed"));
		$paymentMethod = generateStaticOptions(array("Cash" => "Cash", "Cheque" => "Cheque", "NEFT" => "NEFT", "IMPS" => "IMPS", "UPI" => "UPI", "Scholarship" => "Scholarship"));

		//Discount
		if ($row['discountAmount'] == null || $row['discountAmount'] == "" || $row['discountAmount'] <= 0.0) {
			$discountAmount = 0;
		} else {
			$discountAmount = $row['discountAmount'];
		}
		//Late Fees
		if ($row['lateFeesAmount'] == null || $row['lateFeesAmount'] == "" || $row['lateFeesAmount'] <= 0.0) {
			$lateFeesAmount = 0;
		} else {
			$lateFeesAmount = $row['lateFeesAmount'];
		}
		//Penalty
		if ($row['penaltyAmount'] == null || $row['penaltyAmount'] == "" || $row['penaltyAmount'] <= 0.0) {
			$penaltyAmount = 0;
		} else {
			$penaltyAmount = $row['penaltyAmount'];
		}

		$itemID = $row['itemID'];
		$invoiceWaiverDays = $row['waiverDays'];
		$billAmount = $row['billAmount'];
		$finalAmount = $row['finalAmount'];

		if ($itemID == 10) {
			$maintenancePenaltyAmount = $row['occupationType'] == 1 ? $row["ownerPenaltyAmount"] : $row["rentalPenaltyAmount"];
			$maintenancePenaltyMode = $row["penaltyMode"];
		}
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Add Payment Details</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Invoice List</a></div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->addfeesformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Residence:</label>
									<input type="text" class="form-control" value="<?php echo $row['blockName'] . "-" . $row['flatNumber']; ?>" readonly>
								</div>
								<div class="form-group col-sm-4">
									<label>Resident Name:</label>
									<input type="text" class="form-control" value="<?php echo $row['memberName']; ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Invoice Date:</label>
									<input type="text" class="form-control" id="invoiceDate" value="<?php echo date('d-M-Y', strtotime($row['invoiceDate'])); ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Due Date:</label>
									<input type="text" class="form-control" id="invoiceDueDate" value="<?php echo date('d-M-Y', strtotime($row['invoiceDueDate'])); ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Bill Amount:</label>
									<input type="text" class="form-control" id="billAmount" name="billAmount" placeholder="₹ <?php echo number_format($billAmount, 2, '.', ','); ?>" value="<?php $billAmount; ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Discount Amount:</label>
									<input type="text" class="form-control" name="discountAmount" value="₹ <?php echo number_format($discountAmount, 2, '.', ','); ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Penalty Amount:</label>
									<input type="text" class="form-control" name="penaltyAmount" value="₹ <?php echo number_format($penaltyAmount, 2, '.', ','); ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Late Fees:</label>
									<input type="text" class="form-control" id="lateFeesAmountDisplay" value="₹ <?php echo number_format($lateFeesAmount, 2, '.', ','); ?>" readonly>
									<input type="hidden" name="lateFeesAmount" id="lateFeesAmount" value="<?php echo $lateFeesAmount; ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Total Payable Amount:</label>
									<input type="text" class="form-control" id="invoiceAmountDisplay" placeholder="₹ <?php echo number_format($finalAmount, 2, '.', ','); ?>" readonly>
									<input type="hidden" name="invoiceAmount" id="invoiceAmount" value="<?php echo $finalAmount; ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Payment Date:</label>
									<input type="text" id="paymentDate" name="paymentDate" class="form-control todayDateTime flatpickr-input" value="<?php echo date('Y-m-d H:i:s'); ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Payment Method</label>
									<select name="paymentMethod" id="paymentMethod" class="custom-select mr-sm-2" required>
										<option value="">Select Method</option>
										<?php echo $paymentMethod; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<label>Payment Status</label>
									<select id="paymentStatus" name="paymentStatus" class="custom-select mr-sm-2">
										<?php echo $paymentStatus; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<div class="form-group" id="bankName" style="display:none">
										<label>Bank Name</label>
										<input class="form-control" id="bankName1" type="text" name="bankName">
									</div>
								</div>
								<div class="form-group col-sm-2">
									<div class="form-group" id="referenceNo" style="display:none">
										<label>Reference No</label>
										<input type="text" name="referenceNo" id="referenceNo1" class="form-control" placeholder="Reference Number">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<input type="hidden" name="invoiceID" value="<?php echo $_REQUEST['invoiceID']; ?>">
									<input type="hidden" name="penaltyDuration" id="penaltyDuration" value="0">
									<input type="hidden" name="status" value="0">
									<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			var minPaymentDate = new Date(document.getElementById('invoiceDate').value).setHours(0, 0, 0, 0);
			$('.todayDateTime').flatpickr({
				dateFormat: "Y-m-d",
				minDate: minPaymentDate
			});
			$('#paymentMethod').on('change', function() {
				if (this.value == 'Cheque' || this.value == 'NEFT' || this.value == 'UPI' || this.value == 'IMPS') {
					$("#referenceNo").show();
					$("#bankName").show();
					$("#stbankName").show();
					$("#referenceNo").prop('required', true);
					$("#bankName").prop('required', true);
					$("#referenceNo1").prop('required', true);
					$("#bankName1").prop('required', true);
				} else {
					$("#referenceNo").hide();
					$("#bankName").hide();
					$("#stbankName").hide();
					$("#referenceNo").prop('required', false);
					$("#bankName").prop('required', true);
					$("#referenceNo1").prop('required', false);
					$("#bankName1").prop('required', false);
				}
			});
			$('#paymentDate').on('change', function() {
				calculatePenaltyAmount();
			});

			$('#paymentStatus').on('change', function() {
				calculatePenaltyAmount();
			});

			function calculatePenaltyAmount() {
				var itemID = <?php echo $itemID; ?>;
				var selectedStatus = document.getElementById('paymentStatus').value;

				var billAmount = <?php echo $billAmount; ?>;
				var lateFees = 0;
				var diffDays = 0;
				if (itemID == 10 && selectedStatus == 1) {
					var selectedDate = new Date(document.getElementById('paymentDate').value).setHours(0, 0, 0, 0);

					var invoiceStartDate = new Date(document.getElementById('invoiceDate').value).setHours(0, 0, 0, 0);
					var invoiceDueDate = new Date(document.getElementById('invoiceDueDate').value).setHours(0, 0, 0, 0);
					var invoiceWaiverDays = <?php echo $invoiceWaiverDays; ?>;

					var newDateAfterAddingWaiverDays = new Date(invoiceStartDate);
					newDateAfterAddingWaiverDays.setDate(newDateAfterAddingWaiverDays.getDate() + invoiceWaiverDays);
					var invoiceWaiverDate = new Date(newDateAfterAddingWaiverDays).setHours(0, 0, 0, 0);

					if (selectedDate > invoiceDueDate && selectedDate > invoiceWaiverDate) {
						var diffTime = Math.abs(selectedDate - invoiceWaiverDate);
						var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
						// diffDays = diffDays - invoiceWaiverDays;

						var penaltyAmount = <?php echo $maintenancePenaltyAmount; ?>;
						var penaltyMode = <?php echo $maintenancePenaltyMode; ?>;
						if (penaltyMode == 0) {
							lateFees = penaltyAmount * diffDays;
						} else if (penaltyMode == 1) {
							var diffMonths = Math.round(diffDays / (30.4375));
							lateFees = penaltyAmount * diffMonths;
						} else {
							lateFees = penaltyAmount;
						}
					} else {
						lateFees = 0;
						diffDays = 0;
					}
				} else {
					lateFees = <?php echo $lateFeesAmount; ?>;
				}
				//Late Fees
				document.getElementById('lateFeesAmount').value = lateFees;
				document.getElementById('penaltyDuration').value = diffDays;

				//Late Fees - Display
				var lateFeesDisplay = parseFloat(lateFees).toFixed(2);
				lateFeesDisplay = lateFeesDisplay.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				document.getElementById('lateFeesAmountDisplay').value = "₹ " + lateFeesDisplay;

				//Total Payable Amount
				var totalPayableAmount = billAmount + lateFees;
				document.getElementById('invoiceAmount').value = totalPayableAmount;
				//Total Payable Amount - Display
				var totalPayableAmountDisplay = parseFloat(totalPayableAmount).toFixed(2);
				totalPayableAmountDisplay = totalPayableAmountDisplay.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
				document.getElementById('invoiceAmountDisplay').value = "₹ " + totalPayableAmountDisplay;
			}
		</script>
	<?php
	}

	public function discount()
	{
		global $frmMsgDialog;
		$whr = "invoiceID = " . $_POST['invoiceID'];
		$formdata['discountAmount'] = $_POST['discountAmount'];
		$formdata['penaltyAmount'] = $_POST['penaltyAmount'];
		$formdata['cgstAmount'] = $_POST['cgstAmount'];
		$formdata['sgstAmount'] = $_POST['sgstAmount'];
		$formdata['taxAmount'] = $_POST['taxAmount'];
		$formdata['invoiceAmount'] = $_POST['invoiceAmount'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('invoice', $formdata, 'update', $whr)) {

			//dashboard log for invoice
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "societymasters";
			$dashboardlogdata['action'] = "invoice";
			$dashboardlogdata['subAction'] = "discount";
			$dashboardlogdata['referenceID'] = $whr;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Discount Amount has been added successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Discount Amount details is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function addFees()
	{
		global $frmMsgDialog;
		$formdata['invoiceID'] = $_POST['invoiceID'];
		$formdata['paymentDate'] = $_POST['paymentDate'];
		$formdata['paymentMethod'] = $_POST['paymentMethod'];
		if (!empty($_POST['referenceNo'])) {
			$formdata['referenceNo'] = $_POST['referenceNo'];
		}
		if (!empty($_POST['bankName'])) {
			$formdata['bankName'] = $_POST['bankName'];
		}
		$formdata['amount'] = $_POST['invoiceAmount'];
		$formdata['invoiceAmount'] = $_POST['invoiceAmount'];
		$formdata['status'] = $_POST['paymentStatus'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('invoiceTransaction', $formdata)) {
			//Update Invoice Details
			pro_db_query("update invoiceDetails set penaltyDuration = " . $_POST['penaltyDuration'] . ", penaltyAmount = " . $_POST['lateFeesAmount'] . " where invoiceID = " . $_POST['invoiceID']);
			//Update Invoice Amount
			pro_db_query("update invoice set invoiceAmount = " . $_POST['invoiceAmount'] . ", lateFeesAmount = " . $_POST['lateFeesAmount'] . " where invoiceID = " . $_POST['invoiceID']);

			$transactionID = pro_db_insert_id();
			//dashboard log for invoice
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "societymasters";
			$dashboardlogdata['action'] = "invoice";
			$dashboardlogdata['subAction'] = "makepayment";
			$dashboardlogdata['referenceID'] = $_POST['invoiceID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if ($_POST['paymentStatus'] == 1) {
				$this->generateVoucherEntryForAccounting();
			}
			//Response
			$msg = '<p class="bg-success p-3">Member payemnt details is saved...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Member payemnt details	 is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function editForm()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
		$invoiceID = (int)$_REQUEST['invoiceID'];
		$invoiceSql = pro_db_query("select iv.*, it.paymentDate, it.paymentMethod, it.referenceNo, it.bankName, it.amount, mm.*, bfm.*,
									bm.blockName, sm.societyName, sm.societyAddress, iv.invoiceAmount as mount from invoice iv
									left join blockFloorFlatMapping bfm on iv.flatID = bfm.flatID
									left join blockMaster bm on bfm.blockID = bm.blockID
									left join invoiceTransaction it on iv.invoiceID = it.invoiceID
									left join memberMaster mm on bfm.memberID = mm.memberID
									join societyMaster sm on iv.societyID = sm.societyID and iv.invoiceID = " . $_REQUEST['invoiceID']);
		$row = pro_db_fetch_array($invoiceSql);
		$paymentStatus = generateStaticOptions(array("2" => "Failure", "1" => "Completed"));
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Update Payment Status</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Invoice List</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->updatefeesformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="form-group col-sm-3">
											<label>Resident Name</label>
											<input type="text" name="memberName" class="form-control" value="<?php echo $row['memberName']; ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-3">
											<label>Residence</label>
											<input type="text" name="Flat" class="form-control" value="<?php echo $row['blockName'] . "-" . $row['flatNumber']; ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-3">
											<label>Invoice Date</label>
											<input type="email" name="invoiceDate" class="form-control" value="<?php echo date('d-M-Y', strtotime($row['invoiceDate'])); ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-3">
											<label>Payment status:</label>
											<select name="paymentStatus" class="form-control custom-select mr-sm-2" required>
												<?php echo $paymentStatus; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo (int)$row['memberID']; ?>">
									<input type="hidden" name="invoiceID" value="<?php echo (int)$_REQUEST['invoiceID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function discountForm()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
		$invoiceID = (int)$_REQUEST['invoiceID'];
		$invoiceSql = pro_db_query("select iv.*, it.paymentDate, it.paymentMethod, it.referenceNo, it.bankName, it.amount,
									mm.memberID, mm.memberName, mm.memberImage, mm.memberMobile, mm.memberEmail,
									bfm.flatID, bfm.floorNo, bfm.flatNumber, bm.blockName, sm.societyName,
									sas.cgstRate, sas.sgstRate, sm.societyAddress, iv.invoiceAmount as mount
									from invoice iv
									left join blockFloorFlatMapping bfm on iv.flatID = bfm.flatID
									left join blockMaster bm on bfm.blockID = bm.blockID
									left join invoiceTransaction it on iv.invoiceID =  it.invoiceID
									left join memberMaster mm on bfm.memberID = mm.memberID
									left join societyMaster sm on iv.societyID = sm.societyID
									left join societyAccountSettings sas on sas.societyID = iv.societyID
									where iv.invoiceID = " . $_REQUEST['invoiceID']);
		$row = pro_db_fetch_array($invoiceSql);
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Manage Discount / Penalty</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->discountformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="form-group col-sm-2">
											<label>Residence</label>
											<input type="text" name="Flat" class="form-control" value="<?php echo $row['blockName'] . "-" . $row['flatNumber']; ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Resident Name</label>
											<input type="text" name="memberName" class="form-control" value="<?php echo $row['memberName']; ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Invoice Date</label>
											<input type="text" name="invoiceDate" class="form-control" value="<?php echo date('d-M-Y', strtotime($row['invoiceDate'])); ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Due Date</label>
											<input type="text" name="invoiceDate" class="form-control" value="<?php echo date('d-M-Y', strtotime($row['invoiceDueDate'])); ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Maintenance Amount</label>
											<input type="number" name="billAmount" id="billAmount" class="form-control" value="<?php echo $row['billAmount']; ?>" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Late Fee</label>
											<input type="text" name="lateFeesAmount" id="lateFeesAmount" class="form-control" value="<?php echo $row['lateFeesAmount']; ?>" readonly>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-2">
											<label>Discount Amount</label>
											<input type="number" min=0 name="discountAmount" id="discountAmount" value="<?php echo round($row['discountAmount']); ?>" onchange="calculateFinalAmount();" class="form-control" placeholder="0">
										</div>
										<div class="form-group col-sm-2">
											<label>Penalty Amount</label>
											<input type="number" min=0 name="penaltyAmount" id="penaltyAmount" value="<?php echo $row['penaltyAmount']; ?>" onchange="calculateFinalAmount();" class="form-control" placeholder="0">
										</div>
										<div class="form-group col-sm-2">
											<label>Total Amount</label>
											<input type="number" min=0 id="totalAmount" value="<?php echo $row['billAmount']; ?>" class="form-control" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>CGST Rate</label>
											<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="cgstAmount" id="cgstAmount" value="<?php echo $row['cgstAmount']; ?>" class="form-control" placeholder="0" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>SGST Rate</label>
											<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="sgstAmount" id="sgstAmount" value="<?php echo $row['sgstAmount']; ?>" class="form-control" placeholder="0" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Total GST Amount</label>
											<input type="number" min=0 name="taxAmount" id="taxAmount" value="<?php echo $row['taxAmount']; ?>" class="form-control" placeholder="0" readonly>
										</div>
										<div class="form-group col-sm-2">
											<label>Final Payable Amount</label>
											<input type="text" name="invoiceAmount" id="invoiceAmount" value="<?php echo $row['invoiceAmount']; ?>" class="form-control" placeholder="0" readonly>
										</div>
									</div>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo (int)$row['memberID']; ?>">
									<input type="hidden" id="cgstRate" value="<?php echo (int)$row['cgstRate']; ?>">
									<input type="hidden" id="sgstRate" value="<?php echo (int)$row['sgstRate']; ?>">
									<input type="hidden" name="invoiceID" value="<?php echo (int)$_REQUEST['invoiceID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function calculateFinalAmount() {
				$billAmount = document.getElementById('billAmount').value;
				$lateFeesAmount = document.getElementById('lateFeesAmount').value;
				$discountAmount = document.getElementById('discountAmount').value;
				$penaltyAmount = document.getElementById('penaltyAmount').value;
				$cgstRate = document.getElementById('cgstRate').value;
				$sgstRate = document.getElementById('sgstRate').value;

				if (<?php echo $row['billAmount']; ?> < $discountAmount) {
					alert("You are not allowed to enter discount amount greater than actual amount.");
					document.getElementById('discountAmount').value = 0;
					$total = $billAmount + parseInt($penaltyAmount) + parseInt($lateFeesAmount);
					document.getElementById('totalAmount').value = $total;
					$cgstAmount = ($total * $cgstRate) / 100;
					$sgstAmount = ($total * $sgstRate) / 100;
					$invoiceAmount = $total + $cgstAmount + $sgstAmount;
					$invoiceAmount = $invoiceAmount.toFixed(2);
					document.getElementById('cgstAmount').value = $cgstAmount;
					document.getElementById('sgstAmount').value = $sgstAmount;
					document.getElementById('taxAmount').value = $cgstAmount + $sgstAmount;
					document.getElementById('invoiceAmount').value = $invoiceAmount;
				} else {
					$total = $billAmount - $discountAmount + parseInt($penaltyAmount) + parseInt($lateFeesAmount);
					document.getElementById('totalAmount').value = $total;
					$cgstAmount = ($total * $cgstRate) / 100;
					$sgstAmount = ($total * $sgstRate) / 100;
					$invoiceAmount = $total + $cgstAmount + $sgstAmount;
					$invoiceAmount = $invoiceAmount.toFixed(2);
					document.getElementById('cgstAmount').value = $cgstAmount;
					document.getElementById('sgstAmount').value = $sgstAmount;
					document.getElementById('taxAmount').value = $cgstAmount + $sgstAmount;
					document.getElementById('invoiceAmount').value = $invoiceAmount;
				}
			}
		</script>
	<?php
	}

	public function createInvoiceForm()
	{
		$arrMembers = array();
		$arrSql = pro_db_query("select mem.memberID, concat(mem.memberName, ' - ', mem.memberMobile) as memberName
				from memberMaster mem, blockFloorFlatMapping bffm
				where mem.parentID = 0 and mem.status = 1 and mem.memberID = bffm.memberID and bffm.isPrimary = 1
				and bffm.status = 1 and bffm.societyID = " . $_SESSION['societyID'] . "
				order by memberName");
		if (pro_db_num_rows($arrSql) > 0) {
			while ($rs = pro_db_fetch_array($arrSql)) {
				$arrMembers[$rs["memberID"]] = $rs["memberName"];
			}
		} else {
			$arrMembers[] = "No Members Available..!!";
		}

		$memberID = null;
		foreach ($arrMembers as $key => $value) {
			$memberID .= '<option value = "' . $key . '">' . $value . '</option>';
		}
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Generate Invoice</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->createinvoiceformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="form-group col-sm-3">
											<label>Member Name:</label>
											<select name="memberID" id="memberID" class="custom-select mr-sm-2 bindbox" data-target-list="residenceData" data-target-url="ajax/memberResidences.php" data-target-title="Select Residence">
												<option value="0">Select All Members</option>
												<?php echo $memberID; ?>
											</select>
										</div>
										<div class="form-group col-sm-3">
											<label>Select Purpose:</label>
											<select name="selectOptionForAll" id="selectOptionForAll" class="custom-select mr-sm-2" style="display:show;">
												<option value="10">Maintenance</option>
												<option value="9">Developement Fees</option>
												<option value="13">Electricity Bill</option>
												<option value="14">Water Bill</option>
												<option value="20">Other</option>
											</select>
											<select name="selectOptionForIndividual" id="selectOptionForIndividual" class="custom-select mr-sm-2" style="display:none;">
												<option value="10">Maintenance</option>
												<option value="9">Developement Fees</option>
												<!-- <option value="11">Amenities Booking</option>
												<option value="12">Event Booking</option> -->
												<option value="13">Electricity Bill</option>
												<option value="14">Water Bill</option>
												<option value="20">Other</option>
											</select>
										</div>
										<div class="form-group col-sm-3" id="divRequestType" style="display:none;">
											<label id="requestTypeLabel"></label>
											<select name="requestTypeData" id="requestTypeData" class="custom-select mr-sm-2 form-control"></select>
										</div>
										<div class="form-group col-sm-3" id="divResidence">
											<label id="residenceLabel">Residences:</label>
											<select name="residenceData" id="residenceData" class="custom-select mr-sm-2 form-control"></select>
										</div>
										<div class="form-group col-sm-3">
											<label>Payable Amount (₹)</label>
											<input type="text" name="invoiceAmount" id="invoiceAmount" class="form-control" placeholder="0" required>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-sm-5">
											<label>Particulars:</label>
											<input type="text" name="particulars" id="particulars" class="form-control" placeholder="Enter Invoice Particulars">
										</div>
										<div class="form-group col-sm-2">
											<label>Due Date:</label>
											<input type="text" name="invoiceDueDate" id="invoiceDueDate" class="form-control todayDateTime" placeholder="" required>
										</div>
										<div class="form-group col-sm-2" id="startDateDiv">
											<label>Starting Date:</label>
											<input type="text" name="startDate" id="startDate" class="form-control eventTodayDateTime">
										</div>
										<div class="form-group col-sm-2" id="endDateDiv">
											<label>Ending Date:</label>
											<input type="text" name="endDate" id="endDate" class="form-control eventTodayDateTime">
										</div>
										<div class="form-group col-sm-12"><label>Electricity Bill:</label>
											<div class="form-check form-group form-check-inline">
												<input class="form-check-input" type="radio" name="electricityBill" id="electricityBillYes" value="1">
												<label for="electricityBillYes">Yes</label>
											</div>
											<div class="form-check form-group form-check-inline">
												<input class="form-check-input" type="radio" name="electricityBill" id="electricityBillNo" value="0">
												<label for="electricityBillNo">No</label>
											</div>
										</div>
										<div class="form-group col-sm-3" id="billUnits" style="display:none;">
											<label>Units:</label>
											<input type="number" id="units" class="form-control" placeholder="0" onchange="calculateBillAmount()">
										</div>
										<div class="form-group col-sm-3" id="perUnitDiv" style="display:none;">
											<label>Units Rate:</label>
											<input type="number" id="unitRate" class="form-control" placeholder="0" readonly>
										</div>
										<div class="form-group col-sm-3" id="electricityBillDiv" style="display:none;">
											<label>Electricity Bill Amount (₹)</label>
											<input type="text" name="electricityBillAmount" id="electricityBillAmount" class="form-control" placeholder="0" required>
										</div>
									</div>
									<div class="form-group col-sm-12">
										<label></label>
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$('#selectOptionForAll').on('change', function() {
				//Generate Options
				generateInvoiceOptions(0);
			});
			$('#selectOptionForIndividual').on('change', function() {
				//Generate Options
				generateInvoiceOptions(0);
			});

			$('#electricityBillYes').on('click', function() {
				if ($(this).prop('checked')) {
					$("#billUnits").show();
					$("#perUnitDiv").show();
					$("#electricityBillDiv").show();
				}
			});

			$('#requestTypeData').on('change', function() {
				//Generate Options
				var residentID = document.getElementById('requestTypeData').value;
				generateInvoiceOptions(residentID);
			});

			$('#memberID').on('change', function() {
				if (this.value == '0') {
					$("#selectOptionForAll").show();
					$("#selectOptionForIndividual").hide();
					$("#selectOptionForAll").prop('required', true);
					$("#selectOptionForIndividual").prop('required', false);
					$("#divRequestType").hide();
				} else {
					$("#selectOptionForAll").hide();
					$("#selectOptionForIndividual").show();
					$("#selectOptionForAll").prop('required', false);
					$("#selectOptionForIndividual").prop('required', true);
					$("#divRequestType").show();
				}
				//Generate Options
				generateInvoiceOptions(0);
			});

			//Generate Options
			function generateInvoiceOptions(residentID) {
				var memberID = document.getElementById('memberID').value;
				var rt = 13;
				if (memberID == 0) {
					var requestTypeID = document.getElementById('selectOptionForAll').value;
				} else {
					var requestTypeID = document.getElementById('selectOptionForIndividual').value;
				}

				//Titles
				if (requestTypeID == 11) {
					$("#divRequestType").show();
					$("#requestTypeLabel").html("Booked Amenity:");
				} else if (requestTypeID == 12) {
					$("#divRequestType").show();
					$("#requestTypeLabel").html("Booked Event:");
				} else {
					$("#divRequestType").hide();
					$("#requestTypeLabel").html("Residences:");
				}

				//Fetch Residence, Amenity & Event Details
				if (residentID == 0) {
					$.ajax({
						type: "POST",
						url: "ajax/memberInvoiceType.php",
						data: 'memberID=' + memberID + '&requestTypeID=' + requestTypeID,
						success: function(data) {
							$("#requestTypeData").html(data);
						}
					});
				}

				//Default Values
				document.getElementById('units').value = "";
				document.getElementById('invoiceAmount').value = "";
				document.getElementById('invoiceAmount').readOnly = false;
				$("#startDateDiv").hide();
				$("#endDateDiv").hide();

				//Fetch Maintenance
				if (requestTypeID == 10) {
					$.ajax({
						type: "POST",
						url: "ajax/memberInvoiceCalculation.php",
						data: 'memberID=' + memberID + '&residentID=' + residentID,
						success: function(data) {
							document.getElementById('invoiceAmount').value = data;
						}
					});
					document.getElementById('invoiceAmount').readOnly = true;
				}
				if (memberID > 0) {
					$.ajax({
						type: "POST",
						url: "ajax/memberBillCalculation.php",
						data: 'invoiceBillType=' + rt + '&memberID=' + memberID + '&residentID=' + residentID,
						success: function(data) {
							let arrData = data.split("__GGATE__");
							const unitValue = arrData[0];
							const rateValue = arrData[1];
							if (unitValue == 2) {
								document.getElementById('unitRate').value = rateValue;
								document.getElementById('electricityBillAmount').value = "";
								document.getElementById('electricityBillAmount').readOnly = true;
							} else if (unitValue == 1) {
								document.getElementById('units').value = "";
								document.getElementById('electricityBillAmount').value = rateValue;
								document.getElementById('electricityBillAmount').readOnly = true;
							}
						}
					});
				}
				$("#startDateDiv").show();
				$("#endDateDiv").show();
			}

			function calculateBillAmount() {
				$billUnits = document.getElementById('units').value;
				$unitRate = document.getElementById('unitRate').value;
				$finalRate = $billUnits * $unitRate;
				document.getElementById('electricityBillAmount').value = $finalRate;
			}

			$('.todayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
			$('.billDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d"
			});
		</script>
<?php
	}

	public function updateFees()
	{
		global $frmMsgDialog;
		$whr = "invoiceID = " . $_POST['invoiceID'];
		$formdata['status'] = $_POST['paymentStatus'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('invoiceTransaction', $formdata, 'update', $whr)) {
			//dashboard log for invoice
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "societymasters";
			$dashboardlogdata['action'] = "invoice";
			$dashboardlogdata['subAction'] = "changepaymentstatus";
			$dashboardlogdata['referenceID'] = $whr;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if ($_POST['paymentStatus'] == 1) {
				$this->generateVoucherEntryForAccounting();
			}
			//Response
			$msg = '<p class="bg-success p-3">Member payment status is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Member payment status is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function createInvoice()
	{
		global $frmMsgDialog;
		$formdata['societyID'] = $_POST['societyID'];
		$selectedItemID = 20;

		if ($_POST['memberID'] == 0) {
			$selectedItemID = $_POST['selectOptionForAll'] ?? 20;
		} else {
			$selectedItemID = $_POST['selectOptionForIndividual'] ?? 20;
		}

		if (!empty($_POST['startDate'])) {
			$formdata['startDate'] = $_POST['startDate'];
		}

		if (!empty($_POST['endDate'])) {
			$formdata['endDate'] = $_POST['endDate'];
		}

		$formdata['itemID'] = $selectedItemID;
		$formdata['invoiceDueDate'] = $_POST['invoiceDueDate'];
		$formdata['invoiceType'] = 0;
		$formdata['status'] = 1;
		$formdata['invoiceDate'] = date('Y-m-d H:i:s');
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['billAmount'] = $_POST['invoiceAmount'];
		$formdata['particulars'] = $_POST['particulars'];
		$formdata['userName'] = $_SESSION['username'];
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$query = pro_db_query("select * from societyAccountSettings where societyID = " . $_SESSION['societyID']);
		while ($rs = pro_db_fetch_array($query)) {
			if ($rs['isGSTApplicable'] == 1) {
				$r = pro_db_query("select * from invoiceTransactionMaster itm
								join invoiceTransactionSubMaster itsm on itm.trasnactionID = itsm.trasnactionID
								join invoiceTransactionDetails itds on itsm.subTransactionID = itds.subTransactionID
								where itm.transactionID = " . $_POST['transactionID'] . "");
				$cgstAmount = $_POST['invoiceAmount'] * $rs['cgstRate'] / 100;
				$sgstAmount = $_POST['invoiceAmount'] * $rs['sgstRate'] / 100;
				$taxAmount = $cgstAmount + $sgstAmount;
				$formdata['cgstAmount'] = $cgstAmount;
				$formdata['sgstAmount'] = $sgstAmount;
				$formdata['taxAmount'] = $taxAmount;
				$invoiceamount = $_POST['invoiceAmount'] + $taxAmount;
				$formdata['invoiceAmount'] = $invoiceamount;
			} else {
				$formdata['cgstAmount'] = 0;
				$formdata['sgstAmount'] = 0;
				$formdata['taxAmount'] = 0;
				$formdata['invoiceAmount'] = $_POST['invoiceAmount'];
			}
		}

		if ($_POST['memberID'] != 0) {
			$requestTypeDataID = $_POST['requestTypeData'];
			$formdata['memberID'] = $_POST['memberID'];
			if (($selectedItemID == 11) || ($selectedItemID == 12)) {
				$flatquery = pro_db_query("select flatID from blockFloorFlatMapping where isPrimary = 1 and status = 1 and memberID = " . $_POST['memberID']);
				while ($flatrs = pro_db_fetch_array($flatquery)) {
					$formdata['flatID'] = $flatrs['flatID'];
				}
			} else {
				$formdata['flatID'] = $requestTypeDataID;
			}

			if (pro_db_perform('invoice', $formdata)) {
				$invoiceID = pro_db_insert_id();
				//Dashboard Log for Invoice
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "societymasters";
				$dashboardlogdata['action'] = "invoice";
				$dashboardlogdata['subAction'] = "createInvoice";
				$dashboardlogdata['referenceID'] = $invoiceID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				//Update Event Invoice
				if ($selectedItemID == 11) {
					pro_db_query("update eventAttendees set invoiceID = " . $invoiceID . " where memberID = " . $_POST['memberID'] . "
								and eventAttendeesID = " . $requestTypeDataID);
				}
				//Update Amenity Invoice
				if ($selectedItemID == 12) {
					pro_db_query("update amenityBookingTemp set invoiceID = " . $invoiceID . " where memberID = " . $_POST['memberID'] . "
								and bookingID = " . $requestTypeDataID);
				}
				$msg = '<p class="bg-success p-3">Invoice has been added successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Invoice has not been saved...</p>';
			}
		} else {
			$members = pro_db_query("select mm.memberID, bfm.flatID from memberMaster mm
									join blockFloorFlatMapping bfm on mm.memberID = bfm.memberID
									where bfm.societyID = " . $_SESSION['societyID'] . " and bfm.status = 1");
			while ($result = pro_db_fetch_array($members)) {
				$formdata['memberID'] = $result['memberID'];
				$formdata['flatID'] = $result['flatID'];
				pro_db_perform('invoice', $formdata);
				$i = 1;
			}
			if ($i == 1) {
				$msg = '<p class="bg-success p-3">Invoice has been added successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Invoice has not been saved...</p>';
			}
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	//TODO: MS - Need to change
	public function generateVoucherEntryForAccounting()
	{
		//Financial Year
		$queryYear = pro_db_query("select financialYearID from accountFinancialYear where currentYear = 1 ");
		$resYear = pro_db_fetch_array($queryYear);
		$financialYearID = $resYear['financialYearID'];

		//Invoice Details
		$queryInvoice = pro_db_query("select inv.invoiceID, inv.societyID, inv.memberID, inv.flatID, blk.blockName, bfom.floorNo,
									bfom.flatNumber, inv.invoiceNumber, inv.itemID, inv.billAmount, inv.particulars, inv.taxAmount,
									inv.lateFeesAmount, inv.penaltyAmount, inv.discountAmount, inv.invoiceAmount
									from invoice inv
									join blockFloorFlatMapping bfom on inv.flatID = bfom.flatID and bfom.status = 1
									join blockMaster blk on bfom.blockID = blk.blockID and blk.status = 1
									where inv.invoiceID = " . $_POST['invoiceID']);
		$resInvoice = pro_db_fetch_array($queryInvoice);

		//Fetch Debit Account Group - Sundry Debtors
		$queryDebitAccountGroup = pro_db_query("select accountGroupID from accountGroupMaster where groupName = 'Sundry Debtors'");
		$resDebitAccountGroup = pro_db_fetch_array($queryDebitAccountGroup);
		$debitAccountGroupID = $resDebitAccountGroup['accountGroupID'] ?? 0;
		//Fetch Debit Account - Maintenance
		$accountName = $resInvoice['blockName'] . "-" . $resInvoice['flatNumber'];
		$queryDebitAccount = pro_db_query("select accountID from accountMaster where accountName = '" . $accountName . "'");
		$resDebitAccount = pro_db_fetch_array($queryDebitAccount);
		$debitAccountID = $resDebitAccount['accountID'] ?? 0;
		if ($debitAccountID == 0) {
			$debitAccountData['societyID'] = $_POST['societyID'];
			$debitAccountData['accountGroupID '] = $debitAccountGroupID;
			$debitAccountData['accountName'] = $accountName;
			$debitAccountData['accountDescription'] = "Account is managed for " . $accountName;
			$debitAccountData['username'] = $_SESSION['username'];
			$debitAccountData['createdate'] = date('Y-m-d H:i:s');
			$debitAccountData['modifieddate'] = date('Y-m-d H:i:s');
			$debitAccountData['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$debitAccountData['status'] = 1;
			pro_db_perform('accountMaster', $debitAccountData);
			$debitAccountID = pro_db_insert_id();
		}

		//Transaction Master
		$requestInvoiceID = $_POST['invoiceID'];
		$transactiondata['transactionType '] = 1;
		$transactiondata['referenceID'] = $requestInvoiceID;
		$transactiondata['societyID'] = $_POST['societyID'];
		$transactiondata['financialYearID'] = $financialYearID;
		$transactiondata['transactionDate'] = date('Y-m-d H:i:s');
		$transactiondata['amount'] = $resInvoice['invoiceAmount'];
		$transactiondata['notes'] = "Invoice#" . $requestInvoiceID;
		$transactiondata['username'] = $_SESSION['username'];
		$transactiondata['createdate'] = date('Y-m-d H:i:s');
		$transactiondata['modifieddate'] = date('Y-m-d H:i:s');
		$transactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$transactiondata['status'] = 1;
		pro_db_perform('accountTransactionMaster', $transactiondata);
		$transactionID = pro_db_insert_id();

		//Transaction Sub master entry
		$subTransactiondata = array();
		$subTransactiondata['transactionID'] = $transactionID;
		$subTransactiondata['societyID'] = $_POST['societyID'];
		$subTransactiondata['debitAccountID'] = $debitAccountID;
		$subTransactiondata['debitAccountGroupID'] = $debitAccountGroupID;
		$subTransactiondata['username'] = $_SESSION['username'];
		$subTransactiondata['createdate'] = date('Y-m-d H:i:s');
		$subTransactiondata['modifieddate'] = date('Y-m-d H:i:s');
		$subTransactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$subTransactiondata['status'] = 1;

		//Fetch Credit Account - Maintenance
		$queryCreditAccount = pro_db_query("select accountID, accountGroupID from accountMaster where accountName = 'Maintenance Account'");
		$resCreditAccount = pro_db_fetch_array($queryCreditAccount);
		$creditAccountID = $resCreditAccount['accountID'] ?? 0;
		$creditAccountGroupID = $resCreditAccount['accountGroupID'] ?? 0;
		//Bill Amount
		$subTransactiondata['creditAccountID'] = $creditAccountID;
		$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
		$subTransactiondata['amountCredited'] = $resInvoice['billAmount'];
		$subTransactiondata['amountDebited'] = $resInvoice['billAmount'];
		$subTransactiondata['amount'] = $resInvoice['billAmount'];
		pro_db_perform('accountTransactionSubMaster', $subTransactiondata);

		//Tax Amount
		if ($resInvoice['taxAmount'] > 0) {
			//Fetch Credit Account - Tax Amount
			$queryCreditAccount = pro_db_query("select accountID, accountGroupID from accountMaster where accountName = 'Tax Account'");
			$resCreditAccount = pro_db_fetch_array($queryCreditAccount);
			$creditAccountID = $resCreditAccount['accountID'] ?? 0;
			$creditAccountGroupID = $resCreditAccount['accountGroupID'] ?? 0;
			//Tax Amount
			$subTransactiondata['creditAccountID'] = $creditAccountID;
			$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountCredited'] = $resInvoice['taxAmount'];
			$subTransactiondata['amountDebited'] = $resInvoice['taxAmount'];
			$subTransactiondata['amount'] = $resInvoice['taxAmount'];
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata);
		}

		//Penalty Amount
		if ($resInvoice['penaltyAmount'] > 0) {
			//Fetch Credit Account - Penalty Amount
			$queryCreditAccount = pro_db_query("select accountID, accountGroupID from accountMaster where accountName = 'Penalty Account'");
			$resCreditAccount = pro_db_fetch_array($queryCreditAccount);
			$creditAccountID = $resCreditAccount['accountID'] ?? 0;
			$creditAccountGroupID = $resCreditAccount['accountGroupID'] ?? 0;
			//Penalty Amount
			$subTransactiondata['creditAccountID'] = $creditAccountID;
			$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountCredited'] = $resInvoice['penaltyAmount'];
			$subTransactiondata['amountDebited'] = $resInvoice['penaltyAmount'];
			$subTransactiondata['amount'] = $resInvoice['penaltyAmount'];
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata);
		}

		//Late Fees Amount
		if ($resInvoice['lateFeesAmount'] > 0) {
			//Fetch Credit Account - Penalty Amount
			$queryCreditAccount = pro_db_query("select accountID, accountGroupID from accountMaster where accountName = 'Penalty Account'");
			$resCreditAccount = pro_db_fetch_array($queryCreditAccount);
			$creditAccountID = $resCreditAccount['accountID'] ?? 0;
			$creditAccountGroupID = $resCreditAccount['accountGroupID'] ?? 0;
			//Late Fees Amount
			$subTransactiondata['creditAccountID'] = $creditAccountID;
			$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountCredited'] = $resInvoice['lateFeesAmount'];
			$subTransactiondata['amountDebited'] = $resInvoice['lateFeesAmount'];
			$subTransactiondata['amount'] = $resInvoice['lateFeesAmount'];
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata);
		}

		//Discount Amount
		if ($resInvoice['discountAmount'] > 0) {
			//Fetch Credit Account - Discount Amount
			$queryCreditAccount = pro_db_query("select accountID, accountGroupID from accountMaster where accountName = 'Discount Account'");
			$resCreditAccount = pro_db_fetch_array($queryCreditAccount);
			$creditAccountID = $resCreditAccount['accountID'] ?? 0;
			$creditAccountGroupID = $resCreditAccount['accountGroupID'] ?? 0;
			//Discount Amount - Reverse Entry
			$subTransactiondata['creditAccountID'] = $debitAccountID;
			$subTransactiondata['creditAccountGroupID'] = $debitAccountGroupID;
			$subTransactiondata['amountCredited'] = $resInvoice['discountAmount'];
			$subTransactiondata['debitAccountID'] = $creditAccountID;
			$subTransactiondata['debitAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountDebited'] = $resInvoice['discountAmount'];
			$subTransactiondata['amount'] = $resInvoice['discountAmount'];
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata);
		}
	}
}
?>