<?php
class amenitiesbookingmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $makeAdminformaction;
	protected $addfeesformaction;
	protected $cloudStorage;
	protected $updatefeesformaction;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->addfeesformaction = $this->redirectUrl . "&subaction=addFees";
		$this->updatefeesformaction = $this->redirectUrl . "&subaction=updateFees";
	}

	public function addForm()
	{
		$assetID = generateOptions(getMasterList('assetMaster', 'assetID', 'assetsTitle', "societyID=" . $_SESSION['societyID']));
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'parentID = 0 and societyID = ' . $_SESSION['societyID']));
		$sql = pro_db_query("select bm.timeSlotID, bm.slotStartTime, bm.slotEndTime, bm.slotType from bookingTimeSlot bm
							join assetMaster am on bm.assetID = am.assetID
							where am.societyID =" . $_SESSION['societyID']);
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
				$timeSlotID .= '<option value="' . $i . '">' . $brs['slotStartTime'] . ' - ' . $brs['slotEndTime'] . ' - ' . $type . '</option>';
			}
		}

		$datesql = pro_db_query("SELECT bookingDate  FROM amenityBookingMain WHERE status = 1 and assetID = 4
								UNION
								SELECT bookingDate  FROM amenityBookingTemp WHERE assetID = 4");
		$row = $datesql->fetch_assoc();
		$date[] = $row;
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Assets Booking</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Asset Name:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" data-target-list="bookingTimeSlotID" data-target-url="ajax/timeslot.php" required>
										<option value="">Select Asset</option>
										<?php echo $assetID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3 ">
									<label>Member :</label>
									<select name="memberID" id="memberID" class="form-control" data-live-search="true" required>
										<option value="">Select Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Booking Date :</label>
										<input type="date" name="bookingDate" id="date" class="form-control" placeholder="" value="">
									</div>
								</div>
								<div class="form-group col-sm-3 ">
									<label>Time Slot :</label>
									<select name="bookingTimeSlotID" id="bookingTimeSlotID" class="form-control" data-live-search="true" required>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Quantity</label>
										<input type="text" name="quantity" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		require("lib/calendar.php");
		$bookcal = new Calendar();
		$hallID = 4;
		echo $bookcal->show($hallID);
		?>
		<div id="hallBookingDiv"></div>
		<script>
			// For Datetime Calendar
			//$('.eventTodayDateTime').flatpickr({
			//enableTime: false,
			//dateFormat: "Y-m-d"
			//});
			//// For Datetime Calendar
			//$('.eventEndDateTime').flatpickr({
			//enableTime: true,
			//dateFormat: "Y-m-d H:i",
			//minDate: new Date().fp_incr(2)
			//});	

			//var dates = ["20/01/2018", "21/01/2018", "22/01/2018", "23/01/2018"];		
			//function DisableDates(date) {
			//var string = jQuery.datepicker.formatDate('dd/mm/yy', date);
			//return [dates.indexOf(string) == -1];
			//}

			//$(function() {
			//$("#date").datepicker({
			//beforeShowDay: DisableDates
			//});
			//});

			// ar disableDates = ["9-11-2019", "14-11-2019", "15-11-2019","27-12-2019"];

			$('.datepicker').datepicker({
				format: 'yyyy/mm/dd',
				beforeShowDay: function(date) {
					dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
					if (disableDates.indexOf(dmy) != -1) {
						return false;
					} else {
						return true;
					}
				}
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from amenityBookingTemp where bookingID = " . (int)$_REQUEST['bookingID']);
		$rs = pro_db_fetch_array($qry);

		$assetID = generateOptions(getMasterList('assetMaster', 'assetID', 'assetsTitle', "societyID=" . $_SESSION['societyID']), $rs['assetID']);
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'parentID = 0 and societyID = ' . $_SESSION['societyID']), $rs['memberID']);

		$sql = pro_db_query("select bm.timeSlotID, count(bm.timeSlotID) as totalSlots, bm.slotStartTime, bm.slotEndTime, bm.slotType from bookingTimeSlot bm
							join assetMaster am on bm.assetID = am.assetID
							where am.societyID =" . $_SESSION['societyID']);
		if (pro_db_num_rows($sql) > 0) {
			$timeSlotID = "";
			$brs = pro_db_fetch_array($sql);
			for ($i = 1; $i <= $brs['totalSlots']; $i++) {
				if ($brs['slotType'] == 0) {
					$type = "Full Day";
				} else if ($brs['slotType'] == 1) {
					$type = "Half Day";
				} else {
					$type = "Slot Wise";
				}

				if ($i == $rs['bookingTimeSlotID']) $timeSlotID .= '<option value="' . $i . '" selected>' . $brs['slotStartTime'] . ' - ' . $brs['slotEndTime'] . ' - ' . $type . '</option>';
				else $timeSlotID .= '<option value="' . $i . '">' . $brs['slotStartTime'] . ' - ' . $brs['slotEndTime'] . ' - ' . $type . '</option>';
			}
		}
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Amenities Booking</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Select Asset:</label>
									<select name="assetID" id="assetID" class="form-control" data-live-search="true" required>
										<?php echo $assetID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3 ">
									<label>Member :</label>
									<select name="memberID" id="memberID" class="form-control" data-live-search="true" required>
										<option value="">Select Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Booking Date :</label>
										<input type="text" name="bookingDate" class="form-control eventTodayDateTime" placeholder="Select Date" value="<?php echo $rs['bookingDate']; ?>">
									</div>
								</div>
								<div class="form-group col-sm-3 ">
									<label>Time Slot :</label>
									<select name="bookingTimeSlotID" id="bookingTimeSlotID" class="form-control" data-live-search="true" required>
										<?php echo $timeSlotID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Quantity</label>
										<input type="text" name="quantity" class="form-control" value="<?php echo $rs['quantity']; ?>">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="bookingID" value="<?php echo $rs['bookingID']; ?>">
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				minDate: new Date().fp_incr(2)
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['status'] = 0;

		if (pro_db_perform('amenityBookingTemp', $formdata)) {

			$bookingID = pro_db_insert_id();

			$slottypesql = pro_db_query("select slotType from bookingTimeSlot where timeSlotID =" . $_POST['bookingTimeSlotID']);
			$slottypers = pro_db_fetch_array($slottypesql);

			if ($slottypers['slotType'] == 0) {
				$bookingpricesql = pro_db_query("select fullDayAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['fullDayAmount'];
			} else if ($slottypers['slotType'] == 1) {
				$bookingpricesql = pro_db_query("select halfDayAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['halfDayAmount'];
			} else {
				$bookingpricesql = pro_db_query("select perSlotAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['perSlotAmount'];
			}

			$taxsql = pro_db_query("SELECT gstNumber from societyMaster where societyID = " . $_SESSION['societyID']);
			$taxrs = pro_db_fetch_array($taxsql);

			if (($taxrs['gstNumber'] != "NA") || ($taxrs['gstNumber'] != "")) {
				$taxPercentage = 18;
				$csgAmount = ($billAmount * 9) / 100;
				$sgstAmount = ($billAmount * 9) / 100;
				$taxAmount = $csgAmount + $sgstAmount;
			} else {
				$taxPercentage = 0;
				$csgAmount = 0;
				$sgstAmount = 0;
				$taxAmount = 0;
			}

			$invoiceAmount = $billAmount + $taxAmount;
			$invoiceDueDate = date('Y-m-d', strtotime($_POST['bookingDate'] . ' + 2 days'));

			$flatsql = pro_db_query("SELECT flatID from blockFloorFlatMapping where memberID = " . $_POST['memberID'] . " and isPrimary = 1");
			$flatrs = pro_db_fetch_array($flatsql);

			$invoiceData['societyID'] = $_SESSION['societyID'];
			$invoiceData['flatID'] = $flatrs['flatID'];
			$invoiceData['invoiceDate'] = $_POST['bookingDate'];
			$invoiceData['invoiceType'] = 0;
			$invoiceData['itemID '] = 11;
			$invoiceData['billAmount'] = $billAmount;
			$invoiceData['taxPercentage'] = $taxPercentage;
			$invoiceData['cgstAmount'] = $csgAmount;
			$invoiceData['sgstAmount'] = $sgstAmount;
			$invoiceData['taxAmount'] = $taxAmount;
			$invoiceData['invoiceAmount'] = $invoiceAmount;
			$invoiceData['invoiceDueDate'] = $invoiceDueDate;
			$invoiceData['createdate'] = date('Y-m-d H:i:s');
			$invoiceData['modifieddate'] = date('Y-m-d H:i:s');
			$invoiceData['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$invoiceData['status'] = 0;

			pro_db_perform('invoice', $invoiceData);
			$invoiceID = pro_db_insert_id();

			$whr = "bookingID=" . $bookingID;
			$data['invoiceID'] = $invoiceID;

			pro_db_perform('amenityBookingTemp', $data, 'update', $whr);

			$msg = '<p class="bg-success p-3">Amenities Bookings Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Amenities Bookings Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "bookingID=" . $_POST['bookingID'];
		$formdata = $_POST;

		if (pro_db_perform('amenityBookingTemp', $formdata, 'update', $whr)) {

			$slottypesql = pro_db_query("select slotType from bookingTimeSlot where timeSlotID =" . $_POST['bookingTimeSlotID']);
			$slottypers = pro_db_fetch_array($slottypesql);

			if ($slottypers['slotType'] == 0) {
				$bookingpricesql = pro_db_query("select fullDayAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['fullDayAmount'];
			} else if ($slottypers['slotType'] == 1) {
				$bookingpricesql = pro_db_query("select halfDayAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['halfDayAmount'];
			} else {
				$bookingpricesql = pro_db_query("select perSlotAmount from bookingPriceMaster where assetId =" . $_POST['assetID']);
				$bookingpricers = pro_db_fetch_array($bookingpricesql);
				$billAmount = $bookingpricers['perSlotAmount'];
			}

			$taxsql = pro_db_query("SELECT gstNumber from societyMaster where societyID = " . $_SESSION['societyID']);
			$taxrs = pro_db_fetch_array($taxsql);

			if (($taxrs['gstNumber'] != "NA") || ($taxrs['gstNumber'] != "")) {
				$taxPercentage = 18;
				$csgAmount = ($billAmount * 9) / 100;
				$sgstAmount = ($billAmount * 9) / 100;
				$taxAmount = $csgAmount + $sgstAmount;
			} else {
				$taxPercentage = 0;
				$csgAmount = 0;
				$sgstAmount = 0;
				$taxAmount = 0;
			}

			$invoiceAmount = $billAmount + $taxAmount;
			$invoiceDueDate = date('Y-m-d', strtotime($_POST['bookingDate'] . ' + 2 days'));

			$flatsql = pro_db_query("SELECT flatID from blockFloorFlatMapping where memberID = " . $_POST['memberID'] . " and isPrimary = 1");
			$flatrs = pro_db_fetch_array($flatsql);

			$invoiceData['societyID'] = $_SESSION['societyID'];
			$invoiceData['flatID'] = $flatrs['flatID'];
			$invoiceData['invoiceDate'] = $_POST['bookingDate'];
			$invoiceData['invoiceType'] = 0;
			$invoiceData['itemID '] = 11;
			$invoiceData['billAmount'] = $billAmount;
			$invoiceData['taxPercentage'] = $taxPercentage;
			$invoiceData['cgstAmount'] = $csgAmount;
			$invoiceData['sgstAmount'] = $sgstAmount;
			$invoiceData['taxAmount'] = $taxAmount;
			$invoiceData['invoiceAmount'] = $invoiceAmount;
			$invoiceData['invoiceDueDate'] = $invoiceDueDate;
			$invoiceData['createdate'] = date('Y-m-d H:i:s');
			$invoiceData['modifieddate'] = date('Y-m-d H:i:s');
			$invoiceData['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$invoiceData['status'] = 0;

			pro_db_perform('invoice', $invoiceData);
			$invoiceID = pro_db_insert_id();

			$whr = "bookingID=" . $_POST['bookingID'];
			$data['invoiceID'] = $invoiceID;

			pro_db_perform('amenityBookingTemp', $data, 'update', $whr);

			$msg = '<p class="bg-success p-3">Amenities Bookings Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Amenities Bookings Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from amenityBookingTemp where bookingID = " . (int)$_GET['bookingID'];
		$delmainsql = "Delete from amenityBookingMain where bookingID = " . (int)$_GET['bookingID'];
		// $delinvoice = pro_db_query("Delete from invoice where invoiceID = " . (int)$_GET['invoiceID']);
		// $deltra = pro_db_query("Delete from invoiceTransaction where invoiceID = " . (int)$_GET['invoiceID']);
		if ((pro_db_query($delmainsql)) || (pro_db_query($delsql))) {
			$msg = '<p class="bg-success p-3">Amenities Bookings Detail is Deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Amenities Bookings Detail is not dDelete!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}
	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Amenity Booking Management</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="amenitiesbookingmasterList" width="100%">
								<thead>
									<tr>
										<th>Amenity</th>
										<th>Office Admin</th>
										<th>Booking Date</th>
										<th>Booking Slot</th>
										<th>Booking Status</th>
										<th>Invoice Amount</th>
										<th>Payment Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Amenity</th>
										<th>Office Admin</th>
										<th>Booking Date</th>
										<th>Booking Slot</th>
										<th>Booking Status</th>
										<th>Invoice Amount</th>
										<th>Payment Status</th>
										<th>Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/amenitiesbookingmasterList.php';
			$('#amenitiesbookingmasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "amenityMaster"
				},
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Disable'
				}]
			});
		</script>
<?php
	}
}
?>