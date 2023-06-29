<?php
class amenitymaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $addtimeslotformaction;
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
		$this->addtimeslotformaction = $this->redirectUrl . "&subaction=addslots";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "community";
		} else {
			$this->mediaType = "community-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$slotType = generateStaticOptions(array("2" => "Slot Wise", "1" => "Half Day", "0" => "Full day"));
		$assetType = generateStaticOptions(array("1" => "Movable Assets", "0" => "Fixed Assets"));
		$itemID = generateOptions(getMasterList('itemMaster im, itemTypeMaster itm', 'itemID', 'concat(itemTitle, " - ", itemTypeTitle)', "im.itemTypeID = itm.itemTypeID and im.complexID=" . $_SESSION['complexID'] . " and im.status = 1"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Asset</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Item Title:</label>
									<select name="itemID" id="itemID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select item</option>
										<option value="0">Not an item</option>
										<?php echo $itemID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Asset Type:</label>
										<select name="assetType" class="form-control custom-select mr-sm-2" required>
											<?php echo $assetType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Assets Title:</label>
										<input type="text" name="assetTitle" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<label>Assets Description:</label>
										<input type="text" name="assetDescription" class="form-control" placeholder="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Assets Capacity:</label>
										<input type="number" min=0 name="capacity" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Assets Quantity:</label>
										<input type="number" min=0 name="quantity" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="assetStatus" class="form-control custom-select mr-sm-2">
											<?php echo $status; ?>
										</select><br>
									</div>
								</div>
							</div>
							<div class="row">
								<h4 class="form-group col-sm-12"> Asset Images:</h4><br>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 1:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 2:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 3:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 4:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 5:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control"><br>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 6:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control"><br>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 7:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control"><br>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 8:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control"><br>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<h4 class="form-group">Features:</h4>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_feature" style="width :100%">
											<tr>
												<th style="width : 35%">Feature Name</th>
												<th style="width : 35%">Feature Value</th>
												<th style="width : 20%">Status</th>
												<th style="width : 10%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="featureName[]"></td>
												<td><input type="text" class="form-control" name="featureValue[]"></td>
												<td><select class="form-control custom-select mr-sm-2" name="featureStatus[]"><?php echo $status; ?></select></td>
												<td><input class="btn btn-warning" type="button" name="addFeatures" id="addFeatures" value="Add Feature"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<h4 class="form-group">Additional Charges:</h4>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_charge" style="width :100%">
											<tr>
												<th style="width : 35%">Charges Type</th>
												<th style="width : 35%">Amount</th>
												<th style="width : 20%">Status</th>
												<th style="width : 10%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="chargesTitle[]"></td>
												<td><input type="number" min="0" step="1" class="form-control" name="chargesValue[]"></td>
												<td><select class="form-control custom-select mr-sm-2" name="chargeStatus[]"><?php echo $status; ?></select></td>
												<td><input class="btn btn-warning" type="button" name="addCharges" id="addCharges" value="Add Charges"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12"></div>
								<h4 class="form-group col-sm-12">Time Slots:</h4><br />
								<div class="col-sm-12"><label>Add Time Slots:</label>
									<div class="form-check  form-check-inline">
										<input class="form-check-input" type="radio" name="slot" id="slotyes" value="1">
										<label for="slotyes">Yes</label>
									</div>
									<div class="form-check form-group form-check-inline">
										<input class="form-check-input" type="radio" name="slot" id="slotno" value="0">
										<label for="slotno">No</label>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<div class="form-group input-field table-responsive" id="timeslots" style="display:none;">
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
												<td><input class="btn btn-warning" type="button" name="add" id="add" value="Add"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
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
			$(document).ready(function() {
				var featureshtml = '<tr><td><input type="text" class="form-control" name="featureName[]" required></td><td><input type="text" class="form-control" name="featureValue[]" required></td><td><select class="form-control custom-select mr-sm-2" name="featureStatus[]" required><?php echo $status; ?></select></td><td><input class="btn btn-danger" type="button" name="removeFeatures" id="removeFeatures" value="Remove"></td></tr>';
				var features = 1;
				var maxFeatures = 25;
				$("#addFeatures").click(function() {
					if (features <= maxFeatures) {
						$("#table_feature").append(featureshtml);
						features++;
					}
				});
				$("#table_feature").on('click', '#removeFeatures', function() {
					$(this).closest('tr').remove();
					features--;
				});

				var chargeshtml = '<tr><td><input type="text" class="form-control" name="chargesTitle[]" required></td><td><input type="text" class="form-control" name="chargesValue[]" required></td><td><select class="form-control custom-select mr-sm-2" name="chargeStatus[]" required><?php echo $status; ?></select></td><td><input class="btn btn-danger" type="button" name="removeCharges" id="removeCharges" value="Remove"></td></tr>';
				var charges = 1;
				var maxCharges = 25;
				$("#addCharges").click(function() {
					if (charges <= maxCharges) {
						$("#table_charge").append(chargeshtml);
						charges++;
					}
				});
				$("#table_charge").on('click', '#removeCharges', function() {
					$(this).closest('tr').remove();
					charges--;
				});

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

				$('#slotno').on('click', function() {
					if ($(this).prop('checked')) {
						$("#timeslots").hide();
					} else {
						$("#timeslots").show();
					}

				});

				$('#slotyes').on('click', function() {
					if ($(this).prop('checked')) {
						$("#timeslots").show();
					} else {
						$("#timeslots").hide();
					}
				});
			});
		</script>
	<?php
	}

	public function addTimeslots()
	{
		$slotType = generateStaticOptions(array("2" => "Slot Wise", "1" => "Half Day", "0" => "Full day"));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Time Slots</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addtimeslotformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="col-lg-12" style="padding-top:15px;">
									<h4 class="form-group"> Time Slots:</h4><br>
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
												<td><select class="form-control" name="slotType[]"> <?php echo $slotType; ?></select></td>
												<td><input type="number" min=0 class="form-control" name="amount[]"></td>
												<td><input type="number" min=0 class="form-control" name="discount[]"></td>
												<td><input class="btn btn-warning" type="button" name="add" id="add" value="Add Slots"></td>
											</tr>
										</table>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group" style="margin-top:20px;">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="assetID" value="<?php echo $_REQUEST['assetID']; ?>">
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
			var timeslothtml = '<tr><td><input type="time" class="form-control" name="slotStartTime[]" id="slotStartTime" required></td><td><input type="time" class="form-control" name="slotEndTime[]" id="slotEndTime"></td><td><select class="form-control" name="slotType[]">	<?php echo $slotType; ?></select></td><td><input type="number" min=0 class="form-control" name="amount[]" id="amount"></td><td><input type="number" min=0 class="form-control" name="discount[]" id="discount"></td><td><input class="btn btn-danger" type="button" name="remove" id="remove" value="Remove"></td></tr>';
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
		$qry = pro_db_query("select am.*,af.featureName,af.featureValue,ac.chargesTitle,ac.chargesValue from amenityMaster am 
							left join amenityCharges ac on am.assetID = ac.assetID
							left join amenityFeatures af on am.assetID = af.assetID
							where am.assetID = " . (int)$_REQUEST['assetID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		// $slotType = generateStaticOptions(array("1" => "Half Day", "0" => "Full day", "2" => "Slot Wise"), $rs['slotType']);
		$assetType = generateStaticOptions(array("1" => "Movable Assets", "0" => "Fixed Assets"), $rs['assetType']);
		$itemID = generateOptions(getMasterList('itemMaster', 'itemID', 'itemTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"), $rs['itemID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Asset</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Item Title:</label>
									<select name="itemID" id="itemID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="0">Not an item</option>
										<?php echo $itemID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Asset Type:</label>
										<select name="assetType" class="form-control custom-select mr-sm-2">
											<?php echo $assetType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Assets Title:</label>
										<input type="text" name="assetTitle" class="form-control" value="<?php echo $rs['assetTitle']; ?>" required>
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<label>Assets Description:</label>
										<input type="text" name="assetDescription" class="form-control" value="<?php echo $rs['assetDescription']; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Assets Capacity:</label>
										<input type="number" min=0 name="capacity" class="form-control" value="<?php echo $rs['capacity']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Assets Quantity:</label>
										<input type="number" min=0 name="quantity" class="form-control" value="<?php echo $rs['quantity']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="assetStatus" class="form-control custom-select mr-sm-2">
											<?php echo $status; ?>
										</select><br>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<h4 class="form-group">Features:</h4>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_feature" style="width :100%">
											<tr>
												<th style="width : 35%">Feature Name</th>
												<th style="width : 35%">Feature Value</th>
												<th style="width : 20%">Status</th>
												<th style="width : 10%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="featureName[]" value="<?php echo $rs['featureName']; ?>"></td>
												<td><input type="text" class="form-control" name="featureValue[]" value="<?php echo $rs['featureValue']; ?>"></td>
												<td><select class="form-control custom-select mr-sm-2" name="featureStatus[]"><?php echo $status; ?></select></td>
												<td><input class="btn btn-warning" type="button" name="addFeatures" id="addFeatures" value="Add Feature"></td>
											</tr>
											<?php
											$query = pro_db_query("select featureName, featureValue, status from amenityFeatures where assetID = '" . (int)$_REQUEST['assetID'] . "' limit 100 offset 1");
											while ($res = pro_db_fetch_array($query)) {
												$featuresstatus = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $res['status']);
											?>
												<tr>
													<td><input type="text" class="form-control" name="featureName[]" value="<?php echo $res['featureName']; ?>"></td>
													<td><input type="text" class="form-control" name="featureValue[]" value="<?php echo $res['featureValue']; ?>"></td>
													<td><select class="form-control custom-select mr-sm-2" name="featureStatus[]"><?php echo $featuresstatus; ?></select></td>
													<td><input class="btn btn-danger" type="button" name="removeFeatures" id="removeFeatures" value="Remove"></td>
												</tr>
											<?php
											}
											?>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<h4 class="form-group">Additional Charges:</h4>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_charge" style="width :100%">
											<tr>
												<th style="width : 35%">Charges Type</th>
												<th style="width : 35%">Amount</th>
												<th style="width : 20%">Status</th>
												<th style="width : 10%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="chargesTitle[]" value="<?php echo $rs['chargesTitle']; ?>"></td>
												<td><input type="number" min="0" step="1" class="form-control" name="chargesValue[]" value="<?php echo $rs['chargesValue']; ?>"></td>
												<td><select class="form-control custom-select mr-sm-2" name="chargeStatus[]"><?php echo $status; ?></select></td>
												<td><input class="btn btn-warning" type="button" name="addCharges" id="addCharges" value="Add Charges"></td>
											</tr>
											<?php
											$query = pro_db_query("select chargesTitle, chargesValue, status from amenityCharges where assetID = '" . (int)$_REQUEST['assetID'] . "' limit 100 offset 1");
											while ($res = pro_db_fetch_array($query)) {
												$chargesstatus = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $res['status']);
											?>
												<tr>
													<td><input type="text" class="form-control" name="chargesTitle[]" value="<?php echo $res['chargesTitle']; ?>"></td>
													<td><input type="text" class="form-control" name="chargesValue[]" value="<?php echo $res['chargesValue']; ?>"></td>
													<td><select class="form-control custom-select mr-sm-2" name="chargeStatus[]"><?php echo $chargesstatus; ?></select></td>
													<td><input class="btn btn-danger" type="button" name="removeCharges" id="removeCharges" value="Remove"></td>
												</tr>
											<?php
											}
											?>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group" style="margin-top:20px;">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="assetID" value="<?php echo $rs['assetID']; ?>">
										<button type="submit" class="btn btn-success">Update</button>
										&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			var featureshtml = '<tr><td><input type="text" class="form-control" name="featureName[]" required></td><td><input type="text" class="form-control" name="featureValue[]" required></td><td><select class="form-control custom-select mr-sm-2" name="featureStatus[]" required><?php echo $status; ?></select></td><td><input class="btn btn-danger" type="button" name="removeFeatures" id="removeFeatures" value="Remove"></td></tr>';
			var features = 1;
			var maxFeatures = 25;
			$("#addFeatures").click(function() {
				if (features <= maxFeatures) {
					$("#table_feature").append(featureshtml);
					features++;
				}
			});
			$("#table_feature").on('click', '#removeFeatures', function() {
				$(this).closest('tr').remove();
				features--;
			});

			var chargeshtml = '<tr><td><input type="text" class="form-control" name="chargesTitle[]" required></td><td><input type="text" class="form-control" name="chargesValue[]" required></td><td><select class="form-control custom-select mr-sm-2" name="chargeStatus[]" required><?php echo $status; ?></select></td><td><input class="btn btn-danger" type="button" name="removeCharges" id="removeCharges" value="Remove"></td></tr>';
			var charges = 1;
			var maxCharges = 25;
			$("#addCharges").click(function() {
				if (charges <= maxCharges) {
					$("#table_charge").append(chargeshtml);
					charges++;
				}
			});
			$("#table_charge").on('click', '#removeCharges', function() {
				$(this).closest('tr').remove();
				charges--;
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$assetdata['complexID'] = $_SESSION['complexID'];
		$assetdata['itemID'] = $_POST['itemID'];
		$assetdata['assetType'] = $_POST['assetType'];
		$assetdata['assetTitle'] = $_POST['assetTitle'];
		$assetdata['assetDescription'] = $_POST['assetDescription'];
		$assetdata['capacity'] = $_POST['capacity'];
		$assetdata['quantity'] = $_POST['quantity'];
		$assetdata['status'] = $_POST['assetStatus'];
		$assetdata['username'] = $_SESSION['username'];
		$assetdata['assetCode'] = genOTP(4);
		$assetdata['createdate'] = date('Y-m-d H:i:s');
		$assetdata['modifieddate'] = date('Y-m-d H:i:s');
		$assetdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('amenityMaster', $assetdata)) {

			$assetID = pro_db_insert_id();
			$code = str_pad($assetID, 6, "0", STR_PAD_LEFT);
			$assetCode = $_SESSION['complexID'] . $code;
			$data['assetCode'] = $assetCode;

			$whr = "";
			$whr = "assetID=" . $assetID;
			pro_db_perform('amenityMaster', $data, 'update', $whr);

			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "amenities";
			$dashboardlogdata['action'] = "amenitymaster";
			$dashboardlogdata['subAction'] = "addAsset";
			$dashboardlogdata['referenceID'] = $assetID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			foreach ($_FILES['assetImage']['tmp_name'] as $key => $value) {
				$assetImagedata['assetID'] = $assetID;
				if (!empty($_FILES["assetImage"]["name"][$key])) {
					if (pro_db_perform('amenityImage', $assetImagedata)) {
						$imageID = pro_db_insert_id();
						$allowedTypes = array("gif", "jpeg", "jpg", "png");
						$assetImage = $_FILES["assetImage"]["name"][$key];
						$image = explode(".", $assetImage);
						$extension = end($image);

						if ($_FILES["assetImage"]["error"][$key] > 0) {
							$msg = $_FILES["assetImage"]["error"][$key];
							//$rawData["imageName"] = null;
						} else {
							$imageRawData = file_get_contents($_FILES['assetImage']['tmp_name'][$key]);
							$objectName = "assetImage-" . $imageID . "-" . date('YmdHis') . "." . $extension;
							$imageName = $this->mediaType . "/" . $objectName;

							if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
								$finalImageName = GCLOUD_CDN_URL . $imageName;
								//Update assets Image
								$wher = "";
								$wher = "imageID=" . $imageID;
								$imageData['assetImage'] = $finalImageName;
								pro_db_perform('amenityImage', $imageData, 'update', $wher);
								//dashboard log for Image
								$dashboardlogdata['subAction'] = "addImages";
								$dashboardlogdata['referenceID'] = $imageID;
								pro_db_perform('dashboardLogMaster', $dashboardlogdata);
							}
						}
					}
				}
			}
			$complexID = $_SESSION['complexID'];
			$createdate = date('Y-m-d H:i:s');
			$modifieddate = date('Y-m-d H:i:s');
			$remote_ip = $_SERVER['REMOTE_ADDR'];

			if (isset($_POST['featureName']) && isset($_POST['featureValue'])) {
				$featureName = $_POST['featureName'];
				$featureValue = $_POST['featureValue'];
				$featureStatus = $_POST['featureStatus'];

				foreach ($featureName as $key => $value) {
					$save = pro_db_query("INSERT INTO amenityFeatures(assetID,complexID,featureName,featureValue,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $complexID . "','" . $value . "','" . $featureValue[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $featureStatus[$key] . "')");

					$featuresID = pro_db_insert_id();
					//dashboard log for feature
					$dashboardlogdata['subAction'] = "addfeatures";
					$dashboardlogdata['referenceID'] = $featuresID;
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				}
			}

			if (isset($_POST['chargesTitle']) && isset($_POST['chargesValue'])) {
				$chargesTitle = $_POST['chargesTitle'];
				$chargesValue = $_POST['chargesValue'];
				$chargeStatus = $_POST['chargeStatus'];

				foreach ($chargesTitle as $key => $value) {
					$save = pro_db_query("INSERT INTO amenityCharges(assetID,complexID,chargesTitle,chargesValue,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $complexID . "','" . $value . "','" . $chargesValue[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $chargeStatus[$key] . "')");

					$chargesID = pro_db_insert_id();
					//dashboard log for charge
					$dashboardlogdata['subAction'] = "addcharges";
					$dashboardlogdata['referenceID'] = $chargesID;
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				}
			}

			if (isset($_POST['slot']) &&  $_POST['slot'] == 1) {
				if (isset($_POST['slotStartTime']) && isset($_POST['slotEndTime'])) {
					$slotStartTime = $_POST['slotStartTime'];
					$slotEndTime = $_POST['slotEndTime'];
					$slotType = $_POST['slotType'];
					$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
					$discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
					$status = 1;

					foreach ($slotStartTime as $key => $value) {
						$save = pro_db_query("INSERT INTO amenityTimeSlot(assetID,slotStartTime,slotEndTime,slotType,amount,discount,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $value . "','" . $slotEndTime[$key] . "','" . $slotType[$key] . "','" . $amount[$key] . "','" . $discount[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $status . "')");

						$timeSlotID = pro_db_insert_id();
						//dashboard log for charge
						$dashboardlogdata['subAction'] = "addcharges";
						$dashboardlogdata['referenceID'] = $timeSlotID;
						pro_db_perform('dashboardLogMaster', $dashboardlogdata);
					}
				}
			}

			$msg = '<p class="bg-success p-3">Assets Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function addslots()
	{
		global $frmMsgDialog;
		$assetID = $_POST['assetID'];
		$complexID = $_SESSION['complexID'];
		$slotStartTime = $_POST['slotStartTime'];
		$slotEndTime = $_POST['slotEndTime'];
		$slotType = $_POST['slotType'];
		$amount = $_POST['amount'];
		$discount = $_POST['discount'];
		$status = 1;
		$createdate = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];

		if (isset($_POST['slotStartTime']) && isset($_POST['slotEndTime'])) {
			foreach ($slotStartTime as $key => $value) {
				$save = pro_db_query("INSERT INTO amenityTimeSlot(assetID,complexID,slotStartTime,slotEndTime,slotType,amount,discount,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $complexID . "','" . $value . "','" . $slotEndTime[$key] . "','" . $slotType[$key] . "','" . $amount[$key] . "','" . $discount[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $status . "')");
			}

			$msg = '<p class="bg-success p-3">Time Slots has been added successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Time Slots is not successfully!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "assetID=" . $_POST['assetID'];

		$assetdata['complexID'] = $_SESSION['complexID'];
		$assetdata['itemID'] = $_POST['itemID'];
		$assetdata['assetType'] = $_POST['assetType'];
		$assetdata['assetTitle'] = $_POST['assetTitle'];
		$assetdata['assetDescription'] = $_POST['assetDescription'];
		$assetdata['capacity'] = $_POST['capacity'];
		$assetdata['quantity'] = $_POST['quantity'];
		$assetdata['status'] = $_POST['assetStatus'];
		$assetdata['username'] = $_SESSION['username'];
		$assetdata['modifieddate'] = date('Y-m-d H:i:s');
		$assetdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('amenityMaster', $assetdata, 'update', $whr)) {
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "amenities";
			$dashboardlogdata['action'] = "amenitymaster";
			$dashboardlogdata['subAction'] = "editAsset";
			$dashboardlogdata['referenceID'] = $_POST['assetID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$delcharges = pro_db_query("Delete from amenityCharges where assetID = " . (int)$_POST['assetID']);
			$delfeatures = pro_db_query("Delete from amenityFeatures where assetID = " . (int)$_POST['assetID']);

			$assetID = $_POST['assetID'];
			$complexID = $_SESSION['complexID'];
			$featureName = $_POST['featureName'];
			$featureValue = $_POST['featureValue'];
			$featureStatus = $_POST['featureStatus'];
			$createdate = date('Y-m-d H:i:s');
			$modifieddate = date('Y-m-d H:i:s');
			$remote_ip = $_SERVER['REMOTE_ADDR'];

			if (isset($_POST['featureName']) && isset($_POST['featureValue'])) {
				foreach ($featureName as $key => $value) {
					$save = pro_db_query("INSERT INTO amenityFeatures(assetID,complexID,featureName,featureValue,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $complexID . "','" . $value . "','" . $featureValue[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $featureStatus[$key] . "')");

					$featuresID = pro_db_insert_id();
					//dashboard log for feature
					$dashboardlogdata['subAction'] = "editfeatures";
					$dashboardlogdata['referenceID'] = $featuresID;
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				}
			}

			$chargesTitle = $_POST['chargesTitle'];
			$chargesValue = $_POST['chargesValue'];
			$chargeStatus = $_POST['chargeStatus'];

			if (isset($_POST['chargesTitle']) && isset($_POST['chargesValue'])) {
				foreach ($chargesTitle as $key => $value) {
					$save = pro_db_query("INSERT INTO amenityCharges(assetID,complexID,chargesTitle,chargesValue,createdate,modifieddate,remote_ip,status) VALUES('" . $assetID . "','" . $complexID . "','" . $value . "','" . $chargesValue[$key] . "','" . $createdate . "','" . $modifieddate . "','" . $remote_ip . "','" . $chargeStatus[$key] . "')");

					//dashboard log for charge
					$chargesID = pro_db_insert_id();
					$dashboardlogdata['subAction'] = "editcharges";
					$dashboardlogdata['referenceID'] = $chargesID;
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				}
			}

			$msg = '<p class="bg-success p-3">Assets Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from amenityMaster where assetID = " . (int)$_GET['assetID'];

		$dashboardlogdata = array();
		$dashboardlogdata['complexID'] = $_SESSION['complexID'];
		$dashboardlogdata['memberID'] = $_SESSION['memberID'];
		$dashboardlogdata['contorller'] = "amenities";
		$dashboardlogdata['action'] = "amenitymaster";
		$dashboardlogdata['subAction'] = "deleteAsset";
		$dashboardlogdata['referenceID'] = $_GET['assetID'];
		$dashboardlogdata['status'] = 1;
		$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_query("Delete from amenityCharges where assetID = " . (int)$_GET['assetID'])) {
			$assetID = pro_db_insert_id();
			//dashboard log for charges
			$dashboardlogdata['subAction'] = "deleteCharges";
			$dashboardlogdata['referenceID'] = $assetID;
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
		}
		if (pro_db_query("Delete from amenityFeatures where assetID = " . (int)$_GET['assetID'])) {
			$assetID = pro_db_insert_id();
			//dashboard log for charges
			$dashboardlogdata['subAction'] = "deleteFeatures";
			$dashboardlogdata['referenceID'] = $assetID;
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
		}

		if (pro_db_query($delsql)) {
			$dashboardlogdata['subAction'] = "deleteAsset";
			$dashboardlogdata['referenceID'] = $_GET['assetID'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			$msg = '<p class="bg-success p-3">Assets Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets Detail Not deleted successfully</p>';
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
				<h4>Amenity Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Assets</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="amenityMasterList" width="100%">
								<thead>
									<tr>
										<th width="30%">Amenity Title</th>
										<th>Item Type</th>
										<th>Amenity Type</th>
										<th>Asset Code</th>
										<th width="15%">Item Status</th>
										<th width="15%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="30%">Amenity Title</th>
										<th>Item Type</th>
										<th>Amenity Type</th>
										<th>Asset Code</th>
										<th width="15%">Item Status</th>
										<th width="15%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/amenityMasterList.php';
			$('#amenityMasterList').dataTable({
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