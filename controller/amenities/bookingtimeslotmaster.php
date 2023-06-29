<?php
class bookingtimeslotmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $makeAdminformaction;
	protected $cloudStorage;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$slotType = generateStaticOptions(array("2" => "Slot Wise", "1" => "Half Day", "0" => "Full day"));
		$assetsID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "complexID=" . $_SESSION['complexID']));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Booking Time-Slots</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="form-group col-sm-3 ">
											<label>Select Asset:</label>
											<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" data-target-list="timeslots" data-target-url="ajax/assetTimeSlot.php" required>
												<option value="">Select Asset</option>
												<?php echo $assetsID; ?>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12" style="padding-top:15px;">
											<div class="form-group input-field table-responsive">
												<table cellpadding="1" cellspacing="2" border="2" class="table table-bordered " id="table_field" style="width :100%">
													<tr>
														<th style="width : 16%">Slot start Time</th>
														<th style="width : 16%">Slot End Time</th>
														<th style="width : 16%">Slot Type</th>
														<th style="width : 16%">Slot Price</th>
														<th style="width : 16%">Slot Discount</th>
														<th style="width : 16%">Add or Remove</th>
													</tr>
													<tr>
														<td><input type="time" class="form-control" name="slotStartTime[]"></td>
														<td><input type="time" class="form-control" name="slotEndTime[]"></td>
														<td><select class="form-control custom-select mr-sm-2" name="slotType[]"> <?php echo $slotType; ?></select></td>
														<td><input type="number" min=0 class="form-control" name="amount[]"></td>
														<td><input type="number" min=0 class="form-control" name="discount[]"></td>
														<td><input class="btn btn-warning" type="button" name="add" id="add" value="Add Slots"></td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group col-sm-3" id="timeslots">
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			var timeslothtml = '<tr><td><input type="time" class="form-control" name="slotStartTime[]" id="slotStartTime"></td><td><input type="time" class="form-control" name="slotEndTime[]" id="slotEndTime"></td><td><select class="form-control custom-select mr-sm-2" name="slotType[]">	<?php echo $slotType; ?></select></td><td><input type="number" min=0 class="form-control" name="amount[]" id="amount"></td><td><input type="number" min=0 class="form-control" name="discount[]" id="discount"></td><td><input class="btn btn-danger" type="button" name="remove" id="remove" value="Remove"></td></tr>';
			var timeslots = 1;
			var max = 25;
			$("#add").click(function() {
				if (timeslots <= max) {
					$("#table_field").append(timeslothtml);
					timeslots++;
				}
			});
			$("#table_field").on('click', '#remove', function() {
				$(this).closest('tr').remove();
				timeslots--;
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from amenityTimeSlot where timeSlotID = " . (int)$_REQUEST['timeSlotID']);
		$rs = pro_db_fetch_array($qry);

		$oldtimeslotdata = array();
		$oldtimeslotdata = $rs;

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$slotType = generateStaticOptions(array("1" => "Half Day", "0" => "Full day", "2" => "Slot Wise"), $rs['slotType']);
		$assetsID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "complexID=" . $_SESSION['complexID']), $rs['assetID']);
		$slots = generateOptions(getMasterList('amenityTimeSlot', 'timeSlotID', 'concat(slotStartTime, " - ", slotEndTime)', "slottype = 2 and assetID = '" . $rs['assetID'] . "'"));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Booking Time Slots</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Select Asset:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Asset</option>
										<?php echo $assetsID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Slot Start Time:</label>
										<input type="time" name="slotStartTime" class="form-control" value="<?php echo $rs['slotStartTime']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Slot End Time:</label>
										<input type="time" name="slotEndTime" class="form-control" value="<?php echo $rs['slotEndTime']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Slot Type:</label>
										<select name="slotType" id="slotType" class="form-control custom-select mr-sm-2">
											<?php echo $slotType; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Price:</label>
										<input type="number" min="0" step="1" name="amount" class="form-control" value="<?php echo $rs['amount']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Discount:</label>
										<input type="number" min="0" step="1" name="discount" class="form-control" value="<?php echo $rs['discount']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control custom-select mr-sm-2">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
								<div class="form-group col-sm-3" id="existingslots" <?php if ($rs['slotType'] == 1) { ?>style="display:show;" <?php } else { ?> style="display:none;" <?php } ?>>
									<label>Time Slots:</label>
									<select name="parentID[]" id="parentID" class="custom-select mr-sm-2 form-control" multiple data-live-search="true">
										<?php echo $slots; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="timeSlotID" value="<?php echo $rs['timeSlotID']; ?>">
										<input type="hidden" name="oldtimeslotdata[]" value="<?php print_r($oldtimeslotdata); ?>">
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
			$('#slotType').on('change', function() {
				if (this.value == '1') {
					$("#existingslots").show();
					$("#existingslots").prop('required', true);

				} else {
					$("#existingslots").hide();
					$("#existingslots").prop('required', false);
				}
			});
			$('select').selectpicker();
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$rUrl = $this->redirectUrl . "&subaction=listData";

		$assetID = $_POST['assetID'];
		$slotStartTime = $_POST['slotStartTime'];
		$slotType = $_POST['slotType'];
		$slotEndTime = $_POST['slotEndTime'];
		$amount = $_POST['amount'];
		$discount = $_POST['discount'];
		$createdate = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$status = 1;

		if (isset($_POST['slotStartTime']) && isset($_POST['slotEndTime'])) {
			foreach ($slotStartTime as $key => $value) {
				$whr = "";
				if ($slotType[$key] == 1) {
					$whr .= " where assetID = '" . $assetID . "' and status = 1 and slotType = 1 and ('" . $value . "' BETWEEN slotStartTime AND slotEndTime or '" . $slotEndTime[$key] . "' BETWEEN slotStartTime AND slotEndTime);";
				} else if ($slotType[$key] == 2) {
					$whr .= " where assetID = '" . $assetID . "' and status = 1 and slotType = 2 and ('" . $value . "' BETWEEN slotStartTime AND slotEndTime or '" . $slotEndTime[$key] . "' BETWEEN slotStartTime AND slotEndTime);";
				} else {
					$whr .= " where assetID = '" . $assetID . "' and status = 1 and slotType = 0";
				}

				$qur = "SELECT DISTINCT assetID FROM amenityTimeSlot" . $whr;

				$sql = pro_db_query($qur);

				if (pro_db_num_rows($sql) == 0) {
					$save = pro_db_query("INSERT INTO amenityTimeSlot(assetID,slotStartTime,slotEndTime,slotType,amount,discount,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $value . "','" . $slotEndTime[$key] . "','" . $slotType[$key] . "','" . $amount[$key] . "','" . $discount[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $status . "')");
					$timeSlotID = pro_db_insert_id();

					$dashboardlogdata = array();
					$dashboardlogdata['complexID'] = $_SESSION['complexID'];
					$dashboardlogdata['memberID'] = $_SESSION['memberID'];
					$dashboardlogdata['contorller'] = "amenities";
					$dashboardlogdata['action'] = "bookingtimeslotmaster";
					$dashboardlogdata['subAction'] = "addTimeSlots";
					$dashboardlogdata['referenceID'] = $timeSlotID;
					$dashboardlogdata['status'] = 1;
					$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);

					$msg = '<p class="bg-success p-3">Bookings Time Slot Detail is saved successfully...</p>';
				} else {
					$msg = '<p class="bg-danger p-3">This Booking time slot has been already added...</p>';
				}
			}
		} else {
			$msg = '<p class="bg-danger p-3">Bookings Time Slot Detail is not saved!!!!!!</p>';
		}
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "timeSlotID=" . $_POST['timeSlotID'];
		$formdata = $_POST;
		unset($formdata['oldtimeslotdata']);
		$wher = "";

		// foreach ($formdata as $key => $val) {
		// 	$newfinalValue .= $key . '=' . $val . ',';
		// }
		// $newval = rtrim($newfinalValue, ",");

		// foreach ($_POST['oldtimeslotdata'] as $key => $val) {
		// 	$oldfinalValue .= $key . '=' . $val . ',';
		// }
		// $oldval = rtrim($oldfinalValue, ",");

		// $oldval22 = json_encode($oldval);
		// $newval22 = json_encode($newval);

		$dashboardlogdata = array();
		$dashboardlogdata['complexID'] = $_SESSION['complexID'];
		$dashboardlogdata['memberID'] = $_SESSION['memberID'];
		$dashboardlogdata['contorller'] = "amenities";
		$dashboardlogdata['action'] = "bookingtimeslotmaster";
		$dashboardlogdata['subAction'] = "editTimeSlots";
		$dashboardlogdata['referenceID'] = $_POST['timeSlotID'];
		// $dashboardlogdata['oldValue'] = $oldval22;
		// $dashboardlogdata['newValue'] = $newval22;
		$dashboardlogdata['status'] = 1;
		$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		unset($formdata['parentID']);
		$parentID = $_POST['parentID'];

		if ($_POST['slotType'] == 1) {
			$wher .= " where assetID = '" . $_POST['assetID'] . "' and status = 1 and slotType = 1 and ('" . $_POST['slotStartTime'] . "' BETWEEN slotStartTime AND slotEndTime or '" . $_POST['slotEndTime'] . "' BETWEEN slotStartTime AND slotEndTime) and timeslotID != '" . $_POST['timeSlotID'] . "' ;";
		}
		if ($_POST['slotType'] == 2) {
			$wher .= " where assetID = '" . $_POST['assetID'] . "' and status = 1 and slotType = 2 and ('" . $_POST['slotStartTime'] . "' BETWEEN slotStartTime AND slotEndTime or '" . $_POST['slotEndTime'] . "' BETWEEN slotStartTime AND slotEndTime) and timeslotID != '" . $_POST['timeSlotID'] . "';";
		}
		if ($_POST['slotType'] == 0) {
			$wher .= " where assetID = '" . $_POST['assetID'] . "' and status = 1 and slotType = 0 and timeSlotID != '" . $_POST['timeSlotID'] . "'";
		}

		$qur = "SELECT DISTINCT assetID FROM amenityTimeSlot" . $wher;
		$sql = pro_db_query($qur);

		if (pro_db_num_rows($sql) == 0) {

			if (pro_db_perform('amenityTimeSlot', $formdata, 'update', $whr)) {
				$dashboardlogdata['referenceID'] = $_POST['timeSlotID'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				if (isset($_POST['parentID'])) {
					foreach ($parentID as $key => $value) {
						$save = pro_db_query("update amenityTimeSlot set parentID = '" . $_POST['timeSlotID'] . "' where timeSlotID = '" . $value . "'");
					}
				}
				$msg = '<p class="bg-success p-3">Bookings Time Slot is updated successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Bookings Time Slot is not updated!!!!!!</p>';
			}
		} else {
			$msg = '<p class="bg-danger p-3">This Booking time slot has been already added...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;

		$dashboardlogdata = array();
		$dashboardlogdata['complexID'] = $_SESSION['complexID'];
		$dashboardlogdata['memberID'] = $_SESSION['memberID'];
		$dashboardlogdata['contorller'] = "amenities";
		$dashboardlogdata['action'] = "bookingtimeslotmaster";
		$dashboardlogdata['subAction'] = "deleteTimeSlots";
		$dashboardlogdata['referenceID'] = $_GET['timeSlotID'];
		$dashboardlogdata['status'] = 1;
		$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$delsql = "Delete from amenityTimeSlot where timeSlotID = " . (int)$_GET['timeSlotID'];
		if (pro_db_query($delsql)) {
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			$msg = '<p class="bg-success p-3">Bookings Time Slot has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">This Booking time slot has been already added...</p>';
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
				<h4>Amenity Booking Time-Slots</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Booking Time-Slot</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="bookingtimeslotmasterList" width="100%">
								<thead>
									<tr>
										<th width="20%">Amenity Title</th>
										<th>Slot Start-Time</th>
										<th>Slot End-Time</th>
										<th>Slot Type</th>
										<th>Price</th>
										<th>Discount</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="20%">Amenity Title</th>
										<th>Slot Start-Time</th>
										<th>Slot End-Time</th>
										<th>Slot Type</th>
										<th>Price</th>
										<th>Discount</th>
										<th>Status</th>
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
			var listURL = 'helperfunc/bookingtimeslotmasterList.php';
			$('#bookingtimeslotmasterList').dataTable({
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