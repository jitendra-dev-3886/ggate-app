<?php
class blockmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	// protected $oldDashboardValues;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		// $this->oldDashboardValues = array();
	}

	public function addForm()
	{
		$societyqry = pro_db_query("select isManually from complexAccountSettings where complexID = " . $_SESSION['complexID']);
		$societyrs = pro_db_fetch_array($societyqry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$blockType = generateStaticOptions(array("0" => "Flat", "1" => "Bunglow", "2" => "Raw house", "3" => "Villa"));
		$penaltyMode = generateStaticOptions(array("0" => "Daily", "1" => "Monthly"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Block</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Type</label>
									<select name="blockType" id="blockType" class="form-control custom-select mr-sm-2" required>
										<?php echo $blockType; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Block / Area Name:</label>
									<input type="text" name="blockName" class="form-control" placeholder="Block / Area Name" required>
								</div>
								<div class="form-group col-sm-2">
									<label>No of floors:</label>
									<input type="number" min="1" name="noOfFloors" class="form-control" placeholder="No of floors" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Complex per floor:</label>
									<input type="number" min="1" name="officePerFloor" class="form-control" placeholder="Complex per floor" required>
								</div>
								<?php if ($societyrs['isManually'] == 0) { ?>
									<div class="form-group col-sm-3">
										<label>Square Feet Area:</label>
										<input type="number" min=0 step=1 name="squareFeetArea" id="squareFeetArea" onchange="sqftchange()" class="form-control" placeholder="Square Feet Area" required>
									</div>
								<?php } ?>
							</div>
							<div class="row">
								<?php if ($societyrs['isManually'] == 0) { ?>
									<h4 class="form-group col-sm-12"> Maintenance Settings</h4><br>
									<div class="form-group col-sm-12"><label>Maintenance Type:</label>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="maintenanceType" id="maintenanceType1" value="0">
											<label for="maintenanceType1">Flat Rate</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="maintenanceType" id="maintenanceType2" value="1">
											<label for="maintenanceType2">Per Square Feet Rate</label>
										</div>
									</div>
									<div class="form-group col-sm-3" id="ownerSqFtRate" style="display:None">
										<label>Owner Square Feet Rate:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="ownerSqFtRate" id="ownerSqFtRate1" onchange="owneramount()" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="ownerAmount" style="display:None">
										<label>Owner Amount:</label>
										<input type="number" min=0 name="ownerAmount" id="ownerAmount1" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="rentalSqFtRate" style="display:None">
										<label>Tenant Square Feet Rate:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="rentalSqFtRate" id="rentalSqFtRate1" onchange="rentalamount()" class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="rentalAmount" style="display:None">
										<label>Tenant Amount:</label>
										<input type="number" min=0 name="rentalAmount" id="rentalAmount1" class="form-control"><br>
									</div>
									<h4 class="form-group col-sm-12">Late Fees Settings</h4>
									<div class="form-group col-sm-12"><label>Late Fees Type:</label>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType1" value="0">
											<label for="penaltyType1">Flat Amount</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType2" value="1">
											<label for="penaltyType2">Percentage</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType3" value="2">
											<label for="penaltyType3">Fixed Amount</label>
										</div>
									</div>
									<div class="form-group col-sm-3" id="ownerPenaltyPercentage" style="display:None">
										<label>Owner Late Fee Percentage:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="ownerPenaltyPercentage" onchange="ownerpenalty()" id="ownerPenaltyPercentage1" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="ownerPenaltyAmount" style="display:None">
										<label>Owner Late Fee Amount:</label>
										<input type="number" step="0.01" min=0 name="ownerPenaltyAmount" id="ownerPenaltyAmount1" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="rentalPenaltyPercentage" style="display:None">
										<label>Tenant Late Fee Percentage:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="rentalPenaltyPercentage" id="rentalPenaltyPercentage1" onchange="rentalpenalty()" class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="rentalPenaltyAmount" style="display:None">
										<label>Tenant Late Fee Amount:</label>
										<input type="number" step="0.01" min=0 name="rentalPenaltyAmount" id="rentalPenaltyAmount1" class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="penaltyMode" style="display:None">
										<label>Late Fees Mode:</label>
										<select name="penaltyMode" class="form-control custom-select mr-sm-2">
											<?php echo $penaltyMode; ?>
										</select>
									</div>
								<?php } ?>
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="status" value="1">
									<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function owneramount() {
				$sqft = document.getElementById('squareFeetArea').value;
				$percentage = document.getElementById('ownerSqFtRate1').value;
				$total = $sqft * $percentage;
				document.getElementById('ownerAmount1').value = parseFloat($total).toFixed(2);
			}

			function rentalamount() {
				$sqft = document.getElementById('squareFeetArea').value;
				$percentage = document.getElementById('rentalSqFtRate1').value;
				$total = $sqft * $percentage;
				document.getElementById('rentalAmount1').value = parseFloat($total).toFixed(2);
			}

			function ownerpenalty() {
				$ownerAmount = document.getElementById('ownerAmount1').value;
				$percentage = document.getElementById('ownerPenaltyPercentage1').value;
				$total = $ownerAmount * $percentage / 100;
				document.getElementById('ownerPenaltyAmount1').value = parseFloat($total).toFixed(2);
			}

			function rentalpenalty() {
				$ownerAmount = document.getElementById('rentalAmount1').value;
				$percentage = document.getElementById('rentalPenaltyPercentage1').value;
				$total = $ownerAmount * $percentage / 100;
				document.getElementById('rentalPenaltyAmount1').value = parseFloat($total).toFixed(2);
			}

			function sqftchange() {
				if ($('#maintenanceType2').prop('checked')) {
					owneramount();
					rentalamount();
				}

				if ($('#penaltyType2').prop('checked')) {
					ownerpenalty();
					rentalpenalty();
				}
			}

			$('#maintenanceType2').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerSqFtRate").show();
					$("#rentalSqFtRate").show();
					$("#ownerSqFtRate1").prop('required', true);
					$("#rentalSqFtRate1").prop('required', true);
					$("#ownerAmount").show();
					$("#ownerAmount1").prop('readonly', true);
					$("#rentalAmount1").prop('readonly', true);
					$("#rentalAmount").show();
				} else {
					$("#ownerSqFtRate").hide();
					$("#rentalSqFtRate").hide();
					$("#ownerSqFtRate1").prop('required', false);
					$("#rentalSqFtRate1").prop('required', false);
					$("#ownerAmount1").prop('readonly', false);
					$("#rentalAmount1").prop('readonly', false);
					$("#ownerAmount").hide();
					$("#rentalAmount").hide();
				}
			});
			$('#maintenanceType1').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerAmount").show();
					$("#ownerAmount1").prop('readonly', false);
					$("#rentalAmount1").prop('readonly', false);
					$("#ownerAmount1").prop('required', true);
					$("#rentalAmount1").prop('required', true);
					$("#rentalAmount").show();
					$("#ownerSqFtRate").hide();
					$("#rentalSqFtRate").hide();
				} else {
					$("#ownerAmount").hide();
					$("#rentalAmount").hide();
					$("#ownerAmount1").prop('required', false);
					$("#rentalAmount1").prop('required', false);
				}
			});
			$('#penaltyType2').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyPercentage").show();
					$("#rentalPenaltyPercentage").show();
					$("#ownerPenaltyPercentage1").prop('required', true);
					$("#rentalPenaltyPercentage1").prop('required', true);
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('readonly', true);
					$("#rentalPenaltyAmount1").prop('readonly', true);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#penaltyMode").show();
				} else {
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#penaltyMode").hide();
				}
			});
			$('#penaltyType1').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('required', true);
					$("#rentalPenaltyAmount1").prop('required', true);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").show();
				} else {
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('required', false);
					$("#rentalPenaltyAmount1").prop('required', false);
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#penaltyMode").hide();
				}
			});
			$('#penaltyType3').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('required', true);
					$("#rentalPenaltyAmount1").prop('required', true);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").hide();

				} else {
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyAmount1").prop('required', false);
					$("#rentalPenaltyAmount1").prop('required', false);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").hide();
				}
			});
			$('#invoiceType').on('change', function() {
				if (this.value == '0') {
					$("#invoiceDay").show();
					$("#invoiceMonth").hide();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', false);

				} else {
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', true);
				}
			});

			$('#occupationType').on('change', function() {
				if (this.value == '2') {
					$("#InvoiceTo").show();
					$("#InvoiceTo").prop('required', true);

				} else {
					$("#InvoiceTo").hide();
					$("#InvoiceTo").prop('required', false);
				}
			});
			$('#sendInvoiceToOwner').on('change', function() {
				if (this.value == '1') {
					$("#ownerName").show();
					$("#ownerMobile").show();
					$("#ownerEmail").show();
					$("#ownerName").prop('required', true);
					$("#ownerMobile").prop('required', true);
					$("#ownerEmail").prop('required', true);

				} else {
					$("#ownerName").hide();
					$("#ownerMobile").hide();
					$("#ownerEmail").hide();
					$("#ownerName").prop('required', false);
					$("#ownerMobile").prop('required', false);
					$("#ownerEmail").prop('required', false);
				}
			});
		</script>
	<?php
	}
	public function editForm()
	{
		$qry = pro_db_query("select bm.*,sms.* from blockMaster bm
							left join complexMaintenanceSettings sms on bm.blockID = sms.blockID
							where bm.blockID = " . (int)$_REQUEST['blockID']);
		$rs = pro_db_fetch_array($qry);

		$societyqry = pro_db_query("select isManually from complexAccountSettings where complexID = " . $_SESSION['complexID']);
		$societyrs = pro_db_fetch_array($societyqry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$blockType = generateStaticOptions(array("0" => "Flat", "1" => "Bunglow", "2" => "Raw house", "3" => "Villa"), $rs['blockType']);
		$penaltyMode = generateStaticOptions(array("0" => "Daily", "1" => "Monthly"), $rs['penaltyMode']);

		//Old Dashboard Values
		// global $oldDashboardValues;
		$oldDashboardValues = array();

		// global $oldDashboardValues;
		// $this->oldDashboardValues['blockName'] = $rs['blockName'] ?? "";

		// $jsonOldDashboardValues = json_encode($oldDashboardValues);
		// echo $jsonOldDashboardValues;
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Block Master</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="form-row">
								<div class="form-group col-sm-2">
									<label>Type</label>
									<select name="blockType" id="blockType" class="form-control custom-select mr-sm-2" required>
										<?php echo $blockType; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Block / Area Name:</label>
									<input type="text" name="blockName" class="form-control" placeholder="Block / Area Name" value="<?php echo $rs['blockName']; ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>No of floors:</label>
									<input type="number" min="1" name="noOfFloors" class="form-control" placeholder="No of floors" value="<?php echo $rs['noOfFloors']; ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Office per floor:</label>
									<input type="number" min="1" name="officePerFloor" class="form-control" placeholder="Office per floor" value="<?php echo $rs['officePerFloor']; ?>" required><br>
								</div>
								<?php if ($societyrs['isManually'] == 0) { ?>
									<div class="form-group col-sm-3">
										<label>Square Feet Area:</label>
										<input type="number" min=0 step=1 name="squareFeetArea" id="squareFeetArea" class="form-control" onchange="sqftchange()" value="<?php echo $rs['squareFeetArea']; ?>" placeholder="Square Feet Area" required>
									</div>
								<?php } ?>
							</div>
							<div class="row">
								<?php
								$selectedMaintenanceType = 100;
								$selectedPenaltyType = 100;
								$sqftArea = $rs['squareFeetArea'];

								if ($sqftArea > 0) {
									$selectedMaintenanceType = $rs['maintenanceType'];
									$selectedPenaltyType = $rs['penaltyType'];
								}
								?>
								<?php if ($societyrs['isManually'] == 0) { ?>
									<h4 class="form-group col-sm-12">Maintenance Settings:</h4><br>
									<div class="form-group col-sm-12"><label>Maintenance Type: </label>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="maintenanceType" id="maintenanceType1" value="0" <?php if ($selectedMaintenanceType == 0) { ?> checked <?php } ?>>
											<label for="maintenanceType1">Flat Rate</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="maintenanceType" id="maintenanceType2" value="1" <?php if ($selectedMaintenanceType == 1) { ?> checked <?php } ?>>
											<label for="maintenanceType2">Per Square Feet Rate</label>
										</div>
									</div>
									<div class="form-group col-sm-3" id="ownerSqFtRate" <?php if ($rs['maintenanceType'] == 1) { ?> style="display:show" <?php } else { ?> style="display : None" <?php } ?>>
										<label>Owner Square Feet Rate:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="ownerSqFtRate" id="ownerSqFtRate1" onchange="owneramount()" value="<?php echo $rs['ownerSqFtRate']; ?>" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="ownerAmount" style="display:show">
										<label>Owner Amount:</label>
										<input type="number" min=0 name="ownerAmount" id="ownerAmount1" value="<?php echo $rs['ownerAmount']; ?>" <?php if ($rs['maintenanceType'] == 1) { ?> readonly <?php } ?> class="form-control">
									</div>
									<div class="form-group col-sm-3" id="rentalSqFtRate" <?php if ($rs['maintenanceType'] == 1) { ?> style="display:show" <?php } else { ?> style="display : None" <?php } ?>>
										<label>Tenant Square Feet Rate:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="rentalSqFtRate" id="rentalSqFtRate1" onchange="rentalamount()" value="<?php echo $rs['rentalSqFtRate']; ?>" class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="rentalAmount" style="display:show">
										<label>Tenant Amount:</label>
										<input type="number" min=0 name="rentalAmount" id="rentalAmount1" value="<?php echo $rs['rentalAmount']; ?>" <?php if ($rs['maintenanceType'] == 1) { ?> readonly <?php } ?> class="form-control"><br>
									</div>
									<h4 class="form-group col-sm-12">Late Fees Settings:</h4>
									<div class="form-group col-sm-12"><label>Late Fees Type: </label>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType1" value="0" <?php if ($selectedPenaltyType == 0) { ?> checked <?php } ?>>
											<label for="penaltyType1">Flat Amount</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType2" value="1" <?php if ($selectedPenaltyType == 1) { ?> checked <?php } ?>>
											<label for="penaltyType2">Percentage</label>
										</div>
										<div class="form-check form-group form-check-inline">
											<input class="form-check-input" type="radio" name="penaltyType" id="penaltyType3" value="2" <?php if ($selectedPenaltyType == 2) { ?> checked <?php } ?>>
											<label for="penaltyType3">Fixed Amount</label>
										</div>
									</div>
									<div class="form-group col-sm-3" id="ownerPenaltyPercentage" <?php if ($rs['penaltyType'] == 1) { ?> style="display:show" <?php } else { ?> style="display:None" <?php } ?>>
										<label>Owner Late Fee Percentage:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="ownerPenaltyPercentage" onchange="ownerpenalty()" id="ownerPenaltyPercentage1" value="<?php echo $rs['ownerPenaltyPercentage']; ?>" class="form-control">
									</div>
									<div class="form-group col-sm-3" id="ownerPenaltyAmount" style="display:show">
										<label>Owner Late Fee Amount:</label>
										<input type="number" step="0.01" min=0 name="ownerPenaltyAmount" id="ownerPenaltyAmount1" value="<?php echo $rs['ownerPenaltyAmount']; ?>" <?php if ($rs['penaltyType'] == 1) { ?> readonly <?php } ?> class="form-control">
									</div>
									<div class="form-group col-sm-3" id="rentalPenaltyPercentage" <?php if ($rs['penaltyType'] == 1) { ?> style="display:show" <?php } else { ?> style="display:None" <?php } ?>>
										<label>Tenant Late Fee Percentage:</label>
										<input type="number" step="0.01" inputmode="decimal" min=0 name="rentalPenaltyPercentage" id="rentalPenaltyPercentage1" onchange="rentalpenalty()" value="<?php echo $rs['rentalPenaltyPercentage']; ?>" class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="rentalPenaltyAmount" style="display:show">
										<label>Tenant Late Fee Amount:</label>
										<input type="number" step="0.01" min=0 name="rentalPenaltyAmount" id="rentalPenaltyAmount1" value="<?php echo $rs['rentalPenaltyAmount']; ?>" <?php if ($rs['penaltyType'] == 1) { ?> readonly <?php } ?> class="form-control"><br>
									</div>
									<div class="form-group col-sm-3" id="penaltyMode" <?php if (($rs['penaltyType'] == 0) || ($rs['penaltyType'] == 1)) { ?> style="display:show" <?php } else { ?> style="display:None" <?php } ?>>
										<label>Late Fees Mode:</label>
										<select name="penaltyMode" class="form-control custom-select mr-sm-2">
											<?php echo $penaltyMode; ?>
										</select>
									</div>
								<?php } ?>
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID	" value="<?php echo (int)$_SESSION['complexID	']; ?>">
									<input type="hidden" name="blockID" value="<?php echo (int)$_REQUEST['blockID']; ?>">
									<input type="hidden" name="status" value="1">
									<input type="hidden" name="oldDashboardValues" value="<?php print_r($rs); ?>">
									<input type="hidden" name="maintenanceID" value="<?php echo (int)$rs['maintenanceID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function owneramount() {
				$sqft = document.getElementById('squareFeetArea').value;
				$percentage = document.getElementById('ownerSqFtRate1').value;
				$total = $sqft * $percentage;
				document.getElementById('ownerAmount1').value = parseFloat($total).toFixed(2);
			}

			function rentalamount() {
				$sqft = document.getElementById('squareFeetArea').value;
				$percentage = document.getElementById('rentalSqFtRate1').value;
				$total = $sqft * $percentage;
				document.getElementById('rentalAmount1').value = parseFloat($total).toFixed(2);
			}

			function ownerpenalty() {
				$ownerAmount = document.getElementById('ownerAmount1').value;
				$percentage = document.getElementById('ownerPenaltyPercentage1').value;
				$total = $ownerAmount * $percentage / 100;
				document.getElementById('ownerPenaltyAmount1').value = parseFloat($total).toFixed(2);
			}

			function rentalpenalty() {
				$ownerAmount = document.getElementById('rentalAmount1').value;
				$percentage = document.getElementById('rentalPenaltyPercentage1').value;
				$total = $ownerAmount * $percentage / 100;
				document.getElementById('rentalPenaltyAmount1').value = parseFloat($total).toFixed(2);
			}

			function sqftchange() {
				if ($('#maintenanceType2').prop('checked')) {
					owneramount();
					rentalamount();
				}

				if ($('#penaltyType2').prop('checked')) {
					ownerpenalty();
					rentalpenalty();
				}
			}

			$('#maintenanceType2').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerSqFtRate").show();
					$("#rentalSqFtRate").show();
					$("#ownerSqFtRate1").prop('required', true);
					$("#rentalSqFtRate1").prop('required', true);
					$("#ownerAmount").show();
					$("#ownerAmount1").prop('readonly', true);
					$("#rentalAmount1").prop('readonly', true);
					$("#rentalAmount").show();
				} else {
					$("#ownerSqFtRate").hide();
					$("#rentalSqFtRate").hide();
					$("#ownerSqFtRate1").prop('required', false);
					$("#rentalSqFtRate1").prop('required', false);
					$("#ownerAmount1").prop('readonly', false);
					$("#rentalAmount1").prop('readonly', false);
					$("#ownerAmount").hide();
					$("#rentalAmount").hide();
				}
			});
			$('#maintenanceType1').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerAmount").show();
					$("#ownerAmount1").prop('readonly', false);
					$("#rentalAmount1").prop('readonly', false);
					$("#ownerAmount1").prop('required', true);
					$("#rentalAmount1").prop('required', true);
					$("#rentalAmount").show();
					$("#ownerSqFtRate").hide();
					$("#rentalSqFtRate").hide();
				} else {
					$("#ownerAmount").hide();
					$("#rentalAmount").hide();
					$("#ownerAmount1").prop('required', false);
					$("#rentalAmount1").prop('required', false);
				}
			});
			$('#penaltyType2').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyPercentage").show();
					$("#rentalPenaltyPercentage").show();
					$("#ownerPenaltyPercentage1").prop('required', true);
					$("#rentalPenaltyPercentage1").prop('required', true);
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('readonly', true);
					$("#rentalPenaltyAmount1").prop('readonly', true);
					$("#ownerPenaltyPercentage1").prop('readonly', true);
					$("#rentalPenaltyPercentage1").prop('readonly', true);
					$("#penaltyMode").show();
				} else {
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#penaltyMode").hide();
				}
			});
			$('#penaltyType1').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('required', true);
					$("#rentalPenaltyAmount1").prop('required', true);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").show();
				} else {
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('required', false);
					$("#rentalPenaltyAmount1").prop('required', false);
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").hide();
				}
			});
			$('#penaltyType3').on('click', function() {
				if ($(this).prop('checked')) {
					$("#ownerPenaltyAmount").show();
					$("#rentalPenaltyAmount").show();
					$("#ownerPenaltyAmount1").prop('required', true);
					$("#rentalPenaltyAmount1").prop('required', true);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").hide();

				} else {
					$("#ownerPenaltyAmount").hide();
					$("#rentalPenaltyAmount").hide();
					$("#ownerPenaltyAmount1").prop('required', false);
					$("#rentalPenaltyAmount1").prop('required', false);
					$("#ownerPenaltyPercentage").hide();
					$("#rentalPenaltyPercentage").hide();
					$("#ownerPenaltyAmount1").prop('readonly', false);
					$("#rentalPenaltyAmount1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('readonly', false);
					$("#rentalPenaltyPercentage1").prop('readonly', false);
					$("#ownerPenaltyPercentage1").prop('required', false);
					$("#rentalPenaltyPercentage1").prop('required', false);
					$("#penaltyMode").hide();
				}
			});
			$('#invoiceType').on('change', function() {
				if (this.value == '0') {
					$("#invoiceDay").show();
					$("#invoiceMonth").hide();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', false);

				} else {
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', true);
				}
			});

			$('#occupationType').on('change', function() {
				if (this.value == '2') {
					$("#InvoiceTo").show();
					$("#InvoiceTo").prop('required', true);

				} else {
					$("#InvoiceTo").hide();
					$("#InvoiceTo").prop('required', false);
				}
			});
			$('#sendInvoiceToOwner').on('change', function() {
				if (this.value == '1') {
					$("#ownerName").show();
					$("#ownerMobile").show();
					$("#ownerEmail").show();
					$("#ownerName").prop('required', true);
					$("#ownerMobile").prop('required', true);
					$("#ownerEmail").prop('required', true);

				} else {
					$("#ownerName").hide();
					$("#ownerMobile").hide();
					$("#ownerEmail").hide();
					$("#ownerName").prop('required', false);
					$("#ownerMobile").prop('required', false);
					$("#ownerEmail").prop('required', false);
				}
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$isBlockAdded = false;
		$blockdata = array();
		$blockdata['complexID'] = $_SESSION['complexID'];
		$blockdata['blockName'] = $_POST['blockName'];
		$blockdata['blockType'] = $_POST['blockType'];
		$blockdata['noOfFloors'] = $_POST['noOfFloors'];
		$blockdata['officePerFloor'] = $_POST['officePerFloor'];
		$blockdata['status'] = $_POST['status'];
		$blockdata['createdate'] = date('Y-m-d H:i:s');
		$blockdata['modifieddate'] = date('Y-m-d H:i:s');
		$blockdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('blockMaster', $blockdata)) {
			$blockID = pro_db_insert_id();

			//dashboard log for blockmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "blockmaster";
			$dashboardlogdata['subAction'] = "addblock";
			$dashboardlogdata['referenceID'] = $blockID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$isBlockAdded = true;
		}

		if (isset($_POST['squareFeetArea']) && $_POST['squareFeetArea'] != null && !empty($_POST['squareFeetArea'])) {
			$maintenancedata = array();
			$maintenancedata['blockID'] = $blockID;
			$maintenancedata['complexID'] = $_SESSION['complexID'];
			$squareFeetArea = $_POST['squareFeetArea'];
			$maintenancedata['squareFeetArea'] = $squareFeetArea;

			if (isset($_POST['maintenanceType'])) {
				$maintenanceType = $_POST['maintenanceType'];
				$maintenancedata['maintenanceType'] = $maintenanceType;
				$ownerAmount = 0;
				$rentalAmount = 0;
				$ownerSqFtRate = 0;
				$rentalSqFtRate = 0;

				if ($maintenanceType == 0) {
					//Flat Rate
					if (isset($_POST['ownerAmount']) && $_POST['ownerAmount'] != null && !empty($_POST['ownerAmount'])) {
						$ownerAmount = $_POST['ownerAmount'];
					}
					if (isset($_POST['rentalAmount']) && $_POST['rentalAmount'] != null && !empty($_POST['rentalAmount'])) {
						$rentalAmount = $_POST['rentalAmount'];
					}
				} else {
					//Per Sq. Ft. Rate
					if (isset($_POST['ownerSqFtRate']) && $_POST['ownerSqFtRate'] != null && !empty($_POST['ownerSqFtRate'])) {
						$ownerSqFtRate = $_POST['ownerSqFtRate'];
					}
					if (isset($_POST['rentalSqFtRate']) && $_POST['rentalSqFtRate'] != null && !empty($_POST['rentalSqFtRate'])) {
						$rentalSqFtRate = $_POST['rentalSqFtRate'];
					}
					$ownerAmount = $squareFeetArea * $ownerSqFtRate;
					$rentalAmount = $squareFeetArea * $rentalSqFtRate;
				}
				$maintenancedata['ownerAmount'] = $ownerAmount;
				$maintenancedata['rentalAmount'] = $rentalAmount;
				$maintenancedata['ownerSqFtRate'] = $ownerSqFtRate;
				$maintenancedata['rentalSqFtRate'] = $rentalSqFtRate;

				if (isset($_POST['penaltyType'])) {
					$penaltyType = $_POST['penaltyType'];
					$ownerPenaltyAmount = 0;
					$rentalPenaltyAmount = 0;
					$ownerPenaltyPercentage = 0;
					$rentalPenaltyPercentage = 0;

					if ($penaltyType == 1) {
						//Percentage wise Penalty
						if (isset($_POST['ownerPenaltyPercentage']) && $_POST['ownerPenaltyPercentage'] != null && !empty($_POST['ownerPenaltyPercentage'])) {
							$ownerPenaltyPercentage = $_POST['ownerPenaltyPercentage'];
						}
						if (isset($_POST['rentalPenaltyPercentage']) && $_POST['rentalPenaltyPercentage'] != null && !empty($_POST['rentalPenaltyPercentage'])) {
							$rentalPenaltyPercentage = $_POST['rentalPenaltyPercentage'];
						}
						$ownerPenaltyAmount = $ownerAmount * $ownerPenaltyPercentage / 100;
						$rentalPenaltyPercentage = $rentalAmount * $rentalPenaltyPercentage / 100;
					} else {
						//Fix Penalty
						if (isset($_POST['ownerPenaltyAmount']) && $_POST['ownerPenaltyAmount'] != null && !empty($_POST['ownerPenaltyAmount'])) {
							$ownerPenaltyAmount = $_POST['ownerPenaltyAmount'];
						}
						if (isset($_POST['rentalPenaltyAmount']) && $_POST['rentalPenaltyAmount'] != null && !empty($_POST['rentalPenaltyAmount'])) {
							$rentalPenaltyAmount = $_POST['rentalPenaltyAmount'];
						}
					}
					$maintenancedata['ownerPenaltyAmount'] = $ownerPenaltyAmount;
					$maintenancedata['rentalPenaltyAmount'] = $rentalPenaltyAmount;
					$maintenancedata['ownerPenaltyPercentage'] = $ownerPenaltyPercentage;
					$maintenancedata['rentalPenaltyPercentage'] = $rentalPenaltyPercentage;
					$maintenancedata['penaltyType'] = $penaltyType;

					//Penalty Mode
					$penaltyMode = 2;
					if (isset($_POST['penaltyMode'])) {
						$penaltyMode = $_POST['penaltyMode'];
					}
					$maintenancedata['penaltyMode'] = $penaltyMode;
				}
			}
			$maintenancedata['status'] = $_POST['status'];
			$maintenancedata['createdate'] = date('Y-m-d H:i:s');
			$maintenancedata['modifieddate'] = date('Y-m-d H:i:s');
			$maintenancedata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

			if ($isBlockAdded) {
				pro_db_perform('complexMaintenanceSettings', $maintenancedata);

				$maintenanceID = pro_db_insert_id();
				//dashboard log for societyMaintenanceSettings
				$dashboardlogdata = array();
				$dashboardlogdata['complexID'] = $_SESSION['complexID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "blockmaster";
				$dashboardlogdata['subAction'] = "addcomplexMaintenanceSettings";
				$dashboardlogdata['referenceID'] = $maintenanceID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			}
		}

		if ($isBlockAdded) {
			$msg = '<p class="bg-success p-3">Block Detail is saved...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Block Detail is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$isBlockUpdated = false;
		$whr = "";
		$whr = "blockID=" . $_POST['blockID'];

		$qry = pro_db_query("select bm.*,sms.* from blockMaster bm
							left join complexMaintenanceSettings sms on bm.blockID = sms.blockID
							where bm.blockID = " . (int)$_POST['blockID']);
		$rs = pro_db_fetch_array($qry);

		$olddata = array();
		$olddata = $_POST['oldDashboardValues'];

		$blockdata = array();
		$blockdata['blockName'] = $_POST['blockName'];
		$blockdata['blockType'] = $_POST['blockType'];
		$blockdata['noOfFloors'] = $_POST['noOfFloors'];
		$blockdata['officePerFloor'] = $_POST['officePerFloor'];
		$blockdata['createdate'] = date('Y-m-d H:i:s');
		$blockdata['modifieddate'] = date('Y-m-d H:i:s');
		$blockdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$modifieddate = date('Y-m-d H:i:s');

		// $newfinalValue = "";
		// foreach ($blockdata as $key => $val) {
		// 	$newfinalValue .= $key . '=' . $val . ',';
		// }
		// $newval = rtrim($newfinalValue, ",");

		// $oldfinalValue = "";
		// foreach ($olddata as $key => $val) {
		// 	$oldfinalValue .= $key . '=' . $val . ',';
		// }
		// $oldval = rtrim($oldfinalValue, ",");

		if (pro_db_perform('blockMaster', $blockdata, 'update', $whr)) {
			$isBlockUpdated = true;

			//dashboard log for blockmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "blockmaster";
			$dashboardlogdata['subAction'] = "editblock";
			$dashboardlogdata['referenceID'] = $_POST['blockID'];
			// $dashboardlogdata['oldValue'] = $oldval;
			// $dashboardlogdata['newValue'] = $newval;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
		}

		if (isset($_POST['squareFeetArea']) && $_POST['squareFeetArea'] != null && !empty($_POST['squareFeetArea'])) {

			if ($rs['squareFeetArea'] != $_POST['squareFeetArea']) {
				pro_db_query("update blockFloorOfficeMapping set officeArea = " . $_POST['squareFeetArea'] . ", officeMaintenanceAmt = " . $_POST['ownerAmount'] . ",modifieddate = '" . $modifieddate . "' where blockID = " . $_POST['blockID'] . " and complexID = '" . $_SESSION['complexID'] . "' and occupationType = 1");
				pro_db_query("update blockFloorOfficeMapping set officeArea = " . $_POST['squareFeetArea'] . ", officeMaintenanceAmt = " . $_POST['rentalAmount'] . ",modifieddate = '" . $modifieddate . "' where blockID = " . $_POST['blockID'] . " and complexID = '" . $_SESSION['complexID'] . "' and occupationType = 2");
			} else {
				pro_db_query("update blockFloorOfficeMapping set officeArea = " . $_POST['squareFeetArea'] . ", officeMaintenanceAmt = " . $_POST['ownerAmount'] . ",modifieddate = '" . $modifieddate . "' where blockID = " . $_POST['blockID'] . " and complexID = '" . $_SESSION['complexID'] . "' and occupationType = 1 and officeArea = '0.00'");
				pro_db_query("update blockFloorOfficeMapping set officeArea = " . $_POST['squareFeetArea'] . ", officeMaintenanceAmt = " . $_POST['rentalAmount'] . ",modifieddate = '" . $modifieddate . "' where blockID = " . $_POST['blockID'] . " and complexID = '" . $_SESSION['complexID'] . "' and occupationType = 2 and officeArea = '0.00'");
			}

			$maintenancedata = array();
			$maintenancedata['blockID'] = $_POST['blockID'];
			$maintenancedata['complexID'] = $_SESSION['complexID'];
			$squareFeetArea = $_POST['squareFeetArea'];
			$maintenancedata['squareFeetArea'] = $squareFeetArea;

			if (isset($_POST['maintenanceType'])) {
				$maintenanceType = $_POST['maintenanceType'];
				$maintenancedata['maintenanceType'] = $maintenanceType;
				$ownerAmount = 0;
				$rentalAmount = 0;
				$ownerSqFtRate = 0;
				$rentalSqFtRate = 0;

				if ($maintenanceType == 0) {
					//Flat Rate
					if (isset($_POST['ownerAmount']) && $_POST['ownerAmount'] != null && !empty($_POST['ownerAmount'])) {
						$ownerAmount = $_POST['ownerAmount'];
					}
					if (isset($_POST['rentalAmount']) && $_POST['rentalAmount'] != null && !empty($_POST['rentalAmount'])) {
						$rentalAmount = $_POST['rentalAmount'];
					}
				} else {
					//Per Sq. Ft. Rate
					if (isset($_POST['ownerSqFtRate']) && $_POST['ownerSqFtRate'] != null && !empty($_POST['ownerSqFtRate'])) {
						$ownerSqFtRate = $_POST['ownerSqFtRate'];
					}
					if (isset($_POST['rentalSqFtRate']) && $_POST['rentalSqFtRate'] != null && !empty($_POST['rentalSqFtRate'])) {
						$rentalSqFtRate = $_POST['rentalSqFtRate'];
					}
					$ownerAmount = $squareFeetArea * $ownerSqFtRate;
					$rentalAmount = $squareFeetArea * $rentalSqFtRate;
				}
				$maintenancedata['ownerAmount'] = $ownerAmount;
				$maintenancedata['rentalAmount'] = $rentalAmount;
				$maintenancedata['ownerSqFtRate'] = $ownerSqFtRate;
				$maintenancedata['rentalSqFtRate'] = $rentalSqFtRate;

				if (isset($_POST['penaltyType'])) {
					$penaltyType = $_POST['penaltyType'];
					$ownerPenaltyAmount = 0;
					$rentalPenaltyAmount = 0;
					$ownerPenaltyPercentage = 0;
					$rentalPenaltyPercentage = 0;

					if ($penaltyType == 1) {
						//Percentage wise Penalty
						if (isset($_POST['ownerPenaltyPercentage']) && $_POST['ownerPenaltyPercentage'] != null && !empty($_POST['ownerPenaltyPercentage'])) {
							$ownerPenaltyPercentage = $_POST['ownerPenaltyPercentage'];
						}
						if (isset($_POST['rentalPenaltyPercentage']) && $_POST['rentalPenaltyPercentage'] != null && !empty($_POST['rentalPenaltyPercentage'])) {
							$rentalPenaltyPercentage = $_POST['rentalPenaltyPercentage'];
						}
						$ownerPenaltyAmount = $ownerAmount * $ownerPenaltyPercentage / 100;
						$rentalPenaltyAmount = $rentalAmount * $rentalPenaltyPercentage / 100;
					} else {
						//Fix Penalty
						if (isset($_POST['ownerPenaltyAmount']) && $_POST['ownerPenaltyAmount'] != null && !empty($_POST['ownerPenaltyAmount'])) {
							$ownerPenaltyAmount = $_POST['ownerPenaltyAmount'];
						}
						if (isset($_POST['rentalPenaltyAmount']) && $_POST['rentalPenaltyAmount'] != null && !empty($_POST['rentalPenaltyAmount'])) {
							$rentalPenaltyAmount = $_POST['rentalPenaltyAmount'];
						}
					}
					$maintenancedata['ownerPenaltyAmount'] = $ownerPenaltyAmount;
					$maintenancedata['rentalPenaltyAmount'] = $rentalPenaltyAmount;
					$maintenancedata['ownerPenaltyPercentage'] = $ownerPenaltyPercentage;
					$maintenancedata['rentalPenaltyPercentage'] = $rentalPenaltyPercentage;
					$maintenancedata['penaltyType'] = $penaltyType;

					//Penalty Mode
					$penaltyMode = 2;
					if (isset($_POST['penaltyMode'])) {
						$penaltyMode = $_POST['penaltyMode'];
					}
					$maintenancedata['penaltyMode'] = $penaltyMode;
				}
			}
			$maintenancedata['status'] = $_POST['status'];
			$maintenancedata['createdate'] = date('Y-m-d H:i:s');
			$maintenancedata['modifieddate'] = date('Y-m-d H:i:s');
			$maintenancedata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

			if ($isBlockUpdated) {
				if (!empty($_POST['maintenanceID'])) {
					pro_db_perform('complexMaintenanceSettings', $maintenancedata, 'update', $whr);

					//dashboard log for societyMaintenanceSettings
					$dashboardlogdata = array();
					$dashboardlogdata['complexID'] = $_SESSION['complexID'];
					$dashboardlogdata['memberID'] = $_SESSION['memberID'];
					$dashboardlogdata['contorller'] = "complexmasters";
					$dashboardlogdata['action'] = "blockmaster";
					$dashboardlogdata['subAction'] = "updatecomplexMaintenanceSettings";
					$dashboardlogdata['referenceID'] = $_POST['maintenanceID'];
					$dashboardlogdata['status'] = 1;
					$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				} else {
					pro_db_perform('complexMaintenanceSettings', $maintenancedata);

					$maintenanceID = pro_db_insert_id();
					//dashboard log for societyMaintenanceSettings
					$dashboardlogdata = array();
					$dashboardlogdata['blockID'] = $_SESSION['blockID'];
					$dashboardlogdata['memberID'] = $_SESSION['memberID'];
					$dashboardlogdata['contorller'] = "complexmasters";
					$dashboardlogdata['action'] = "blockmaster";
					$dashboardlogdata['subAction'] = "updatecomplexMaintenanceSettings";
					$dashboardlogdata['referenceID'] = $maintenanceID;
					$dashboardlogdata['status'] = 1;
					$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
					$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					pro_db_perform('dashboardLogMaster', $dashboardlogdata);
				}
			}
		}

		if ($isBlockUpdated) {
			$msg = '<p class="bg-success p-3">Block Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Block Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Update blockMaster set status = 126 where blockID = '" . (int)$_REQUEST['blockID'] . "'";
		$delmaintenance = "Update complexMaintenanceSettings set status = 126 where blockID = '" . (int)$_REQUEST['blockID'] . "'";
		$delFlats = "Update blockFloorOfficeMapping set status = 126 where blockID = '" . (int)$_REQUEST['blockID'] . "'";

		if (pro_db_query($delsql)) {

			//dashboard log for blockmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "blockmaster";
			$dashboardlogdata['subAction'] = "deleteblock";
			$dashboardlogdata['referenceID'] = $_REQUEST['blockID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (pro_db_query($delFlats)) {
				//dashboard log for blockmaster
				$dashboardlogdata = array();
				$dashboardlogdata['complexID'] = $_SESSION['complexID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "blockmaster";
				$dashboardlogdata['subAction'] = "deleteblockFloorFlatMapping";
				$dashboardlogdata['referenceID'] = $_REQUEST['blockID'];
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			}

			if (pro_db_query($delmaintenance)) {
				//dashboard log for blockmaster
				$dashboardlogdata = array();
				$dashboardlogdata['complexID'] = $_SESSION['complexID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "blockmaster";
				$dashboardlogdata['subAction'] = "deletecomplexMaintenanceSettings";
				$dashboardlogdata['referenceID'] = $_REQUEST['blockID'];
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			}
			$msg = '<p class="bg-success p-3">Block Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Block Detail is not deleted!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2 mb-3">
				<h4>Block Details</h4>
			</div>
			<?php
			$societysql = pro_db_query("select maxBlocks, complexOffice from complexMaster where  status= 1 and complexID = " . $_SESSION['complexID']);
			$societyrs = pro_db_fetch_array($societysql);

			$blocksql = pro_db_query("select count(blockID) as totalBlocks from blockMaster where status= 1 and complexID = " . $_SESSION['complexID']);
			$blockrs = pro_db_fetch_array($blocksql);
			if ($societyrs['complexOffice'] == 0) {
				if ($blockrs['totalBlocks'] < $societyrs['maxBlocks']) {
			?>
					<div class="col-sm-3 py-3 mt-1 mb-3"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Block Detail</a></div>
				<?php
				}
			} else {
				$totalblocks = $blockrs['totalBlocks'] - 1;
				if ($totalblocks < $societyrs['maxBlocks']) {
				?>
					<div class="col-sm-3 py-3 mt-1 mb-3"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Block Detail</a></div>
			<?php
				}
			}
			?>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="blockMasterList" width="100%">
								<thead>
									<tr>
										<th align="left">Block / Area</th>
										<th align="left">No of Floors</th>
										<th align="left">Office Per Floor</th>
										<th align="left">Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Block / Area</th>
										<th align="left">No of Floors</th>
										<th align="left">Office Per Floor</th>
										<th align="left">Status</th>
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
			var listURL = 'helperfunc/blockMasterList.php';
			$('#blockMasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'blockMaster';
				var field_name = 'blockID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&blockID=" + primaryKey;

				$.ajax({
					type: 'POST',
					url: 'ajax/checkchild.php',
					dataType: 'json',
					data: {
						primary_id: primary_id,
						table_name: table_name,
						field_name: field_name
					},
					success: function(data) {
						if (data.cnt >= 1) {
							alert("This master have child entries, you can not delete this.");
							return false;
						} else {
							var agree = confirm("Are you sure you want to delete?");
							if (agree)
								window.location.href = delLnk;
							else
								return false;
						}
					},
				});
			});
			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "blockMaster"
				},
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Inactive'
				}]
			});
		</script>
<?php
	}
}
?>