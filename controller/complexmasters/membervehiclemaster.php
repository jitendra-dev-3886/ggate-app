<?php
class membervehiclemaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $parkingallotmentformaction;
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
		$this->parkingallotmentformaction = $this->redirectUrl . "&subaction=allotparking";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', ' complexID = ' . $_SESSION['complexID']));
		$vehicleType = generateStaticOptions(array("1" => "Two Wheeler", "2" => "Four Wheeler", "0" => "Other"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Member Vehicle</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Select Member:</label>
									<select name="memberID" id="memberID" class="custom-select mr-sm-2 form-control" data-live-search="true" required>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Vehicle Type:</label>
										<select name="vehicleType" class="custom-select mr-sm-2">
											<?php echo $vehicleType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Vehicle Number:</label>
										<input type="text" oninput="this.value = this.value.toUpperCase()" name="vehicleNumber" minlength="10" maxlength="10" title="For e.g. GJ05XX0000" pattern="[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}" id="vehicleNumber" class="form-control" placeholder="Enter Vehicle Number" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Alias:</label>
										<input type="text" name="vehicleAlias" class="form-control" placeholder="Enter Vehicle Alias" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="custom-select mr-sm-2">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
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
			// Example starter JavaScript for disabling form submissions if there are invalid fields
			(function() {
				'use strict';
				window.addEventListener('load', function() {
					// Fetch all the forms we want to apply custom Bootstrap validation styles to
					var forms = document.getElementsByClassName('needs-validation');
					// Loop over them and prevent submission
					var validation = Array.prototype.filter.call(forms, function(form) {
						form.addEventListener('submit', function(event) {
							if (form.checkValidity() === false) {
								event.preventDefault();
								event.stopPropagation();
							}
							form.classList.add('was-validated');
						}, false);
					});
				}, false);
			})();
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from memberVehicle where vehicleID = " . (int)$_REQUEST['vehicleID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'complexID = ' . $_SESSION['complexID']), $rs['memberID']);
		$vehicleType = generateStaticOptions(array("1" => "Two Wheeler", "2" => "Four Wheeler", "0" => "Other"), $rs['vehicleType']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Member Vehicle</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Select Member:</label>
									<select name="memberID" id="memberID" class="custom-select mr-sm-2 form-control" data-live-search="true" required>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Vehicle Type:</label>
										<select name="vehicleType" class="custom-select mr-sm-2">
											<?php echo $vehicleType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Vehicle Number:</label>
										<input type="text" oninput="this.value = this.value.toUpperCase()" name="vehicleNumber" minlength="10" maxlength="10" title="For e.g. GJ05XX0000" pattern="[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}" id="vehicleNumber" class="form-control" value="<?php echo $rs['vehicleNumber']; ?>" placeholder="Enter Vehicle Number" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Alias:</label>
										<input type="text" name="vehicleAlias" class="form-control" value="<?php echo $rs['vehicleAlias']; ?>" placeholder="Enter Vehicle Alias" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="custom-select mr-sm-2">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="vehicleID" value="<?php echo (int)$rs['vehicleID']; ?>">
										<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function allotparkingForm()
	{
		$qry = pro_db_query("select mv.memberVehicleID, mv.memberID, mv.societyID, mv.vehicleType, mv.vehicleNumber, 
							mv.vehicleAlias, mv.status, park.parkingID, park.allocatedArea, park.guestParkingAllow, 
							mem.memberName, mem.memberImage, mem.memberMobile, bfm.flatID, bm.blockName, bfm.flatNumber 
							from memberVehicle mv
							left join parkingAllotment park on mv.memberVehicleID = park.memberVehicleID
							left join memberMaster mem on mv.memberID = mem.memberID
							left join blockFloorFlatMapping bfm on bfm.memberID = mv.memberID
							left join blockMaster bm on bfm.blockID = bm.blockID
							where mv.memberVehicleID = " . (int)$_REQUEST['memberVehicleID'] . " and bfm.isPrimary = 1 and bfm.status = 1");
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'parentID = 0 and societyID = ' . $_SESSION['societyID']), $rs['memberID']);
		$vehicleType = generateStaticOptions(array("1" => "Two Wheeler", "2" => "Four Wheeler"), $rs['vehicleType']);
		$flatID = generateOptions(getMasterList('blockFloorFlatMapping bfm, blockMaster bm', 'flatID', 'concat(blockName, " - ", flatNumber)', "bfm.status = 1 and bfm.blockID = bm.blockID and bfm.flatID=" . $rs['flatID']));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Allot Parking</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->parkingallotmentformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Resident:</label>
									<select name="memberID" id="memberID" class="custom-select mr-sm-2 form-control" data-live-search="true" disabled>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Residence Number:</label>
									<select name="flatID" id="flatID" class="custom-select mr-sm-2 form-control" disabled>
										<?php echo $flatID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Vehicle Type:</label>
										<select name="vehicleType" id="vehicleType" class="custom-select mr-sm-2" disabled>
											<?php echo $vehicleType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Vehicle Number:</label>
										<input type="text" name="vehicleNumber" minlength="10" maxlength="10" title="For e.g. GJ05XX0000" pattern="[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}" id="vehicleNumber" class="form-control" value="<?php echo $rs['vehicleNumber']; ?>" placeholder="Enter vehicle number" readonly>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Allocated Area:</label>
										<input type="text" name="allocatedArea" class="form-control" value="<?php echo $rs['allocatedArea']; ?>" placeholder="Enter Allocation Area" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="parkingID" value="<?php echo (int)$rs['parkingID']; ?>">
										<input type="hidden" name="memberVehicleID" value="<?php echo (int)$rs['memberVehicleID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo (int)$rs['memberID']; ?>">
										<input type="hidden" name="flatID" value="<?php echo (int)$rs['flatID']; ?>">
										<input type="hidden" name="vehicleType" value="<?php echo (int)$rs['vehicleType']; ?>">
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
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('memberVehicle', $formdata)) {
			$vehicleID = pro_db_insert_id();

			//dashboard log for member vehicle
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "membervehiclemaster";
			$dashboardlogdata['subAction'] = "addvehcile";
			$dashboardlogdata['referenceID'] = $vehicleID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Vehicle Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Vehicle Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function allotparking()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['status'] = 1;
		$formdata['userID'] = $_SESSION['memberID'];
		$formdata['societyID'] = $_SESSION['societyID'];
		$parkingID = $formdata['parkingID'];

		$query = pro_db_query("select * from parkingAllotment where complexID=" . $_SESSION['complexID'] . " and
								memberID != " . $formdata['memberID'] . " and allocatedArea = '" . $_POST['allocatedArea'] . "'");
		$row = pro_db_num_rows($query);
		if ($row == 0) {
			if ($parkingID == 0) {
				if (pro_db_perform('parkingAllotment', $formdata)) {
					$parkingID = pro_db_insert_id();

					//dashboard log for member vehicle
					$dashboardlogdata = array();
					$dashboardlogdata['societyID'] = $_SESSION['societyID'];
					$dashboardlogdata['memberID'] = $_SESSION['memberID'];
					$dashboardlogdata['contorller'] = "complexmasters";
					$dashboardlogdata['action'] = "membervehiclemaster";
					$dashboardlogdata['subAction'] = "addparking";
					$dashboardlogdata['referenceID'] = $parkingID;
					$dashboardlogdata['status'] = 1;
					$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);

					$msg = '<p class="bg-success p-3">Parking Detail is saved successfully...</p>';
				} else {
					$msg = '<p class="bg-danger p-3">Parking Detail is not saved!!!!!!</p>';
				}
			} else {
				$whr = "parkingID=" . $parkingID;
				if (pro_db_perform('parkingAllotment', $formdata, 'update', $whr)) {

					//dashboard log for member vehicle
					$dashboardlogdata = array();
					$dashboardlogdata['societyID'] = $_SESSION['societyID'];
					$dashboardlogdata['memberID'] = $_SESSION['memberID'];
					$dashboardlogdata['contorller'] = "complexmasters";
					$dashboardlogdata['action'] = "membervehiclemaster";
					$dashboardlogdata['subAction'] = "updateparking";
					$dashboardlogdata['referenceID'] = $whr;
					$dashboardlogdata['status'] = 1;
					$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);

					$msg = '<p class="bg-success p-3">Parking Detail is saved successfully...</p>';
				} else {
					$msg = '<p class="bg-danger p-3">Parking Detail is not saved!!!!!!</p>';
				}
			}
		} else {
			$msg = '<p class="bg-danger p-3">This area has already been allocated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "vehicleID=" . $_POST['vehicleID'];
		$formdata = $_POST;
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('memberVehicle', $formdata, 'update', $whr)) {

			//dashboard log for member vehicle
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "membervehiclemaster";
			$dashboardlogdata['subAction'] = "editvehcile";
			$dashboardlogdata['referenceID'] = $_POST['vehicleID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Vehicle Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Vehicle Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from memberVehicle where vehicleID = " . (int)$_GET['vehicleID'];
		if (pro_db_query($delsql)) {

			//dashboard log for member vehicle
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "membervehiclemaster";
			$dashboardlogdata['subAction'] = "deletevehicle";
			$dashboardlogdata['referenceID'] = $_GET['vehicleID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Vehicle Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Vehicle Detail Not deleted successfully</p>';
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
				<h4>Vehicle Directory & Parking Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i> Add New Vehicle</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<!-- table-condensed -->
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="membervehicleList" width="100%">
								<thead>
									<tr>
										<!-- <th width="9%">Residence</th> -->
										<th width="15%">Member Name</th>
										<th width="15%">Office Name</th>
										<th width="15%">Address</th>
										<th>Image</th>
										<th>Contact</th>
										<th>Vehicle Number</th>
										<th>Type</th>
										<th width="10%">Alias</th>
										<th width="8%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<!-- <th width="9%">Residence</th> -->
										<th width="15%">Member</th>
										<th width="15%">Office Name</th>
										<th width="15%">Address</th>
										<th>Image</th>
										<th>Contact</th>
										<th>Vehicle Number</th>
										<th>Type</th>
										<th width="10%">Alias</th>
										<th width="8%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/membervehicleList.php';
			$('#membervehicleList').dataTable({
				dom: 'Bfrtip',
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "memberVehicle"
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