<?php
class flatmapping
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $generateinvoiceformaction;
	protected $transferflatformaction;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->generateinvoiceformaction = $this->redirectUrl . "&subaction=generateInvoice";
		$this->transferflatformaction = $this->redirectUrl . "&subaction=transferProperty";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Active", "0" => "Pending", "2" => "Reject"));

		$occupationType = generateStaticOptions(array("1" => "Owner", "2" => "Tenant", "3" => "Looking for Tenant"));

		$sendInvoiceToOwner = generateStaticOptions(array("1" => "Yes", "0" => "No"));

		$invoiceType = generateStaticOptions(array("0" => "Monthly", "1" => "Quaterly", "2" => "Half yearly", "3" => "Yearly"));

		$blockID = generateOptions(getMasterList('blockMaster', 'blockID', 'blockName', "complexID=" . $_SESSION['complexID'] . " and status = 1"));

		$officeID = generateOptions(getMasterList('officeMaster', 'officeID', 'officeName'));
		// print_r($officeID);

		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'status = 1 and complexID = ' . $_SESSION['complexID']));
		
		$sqlInvoice = pro_db_query("select isManually from complexAccountSettings where complexID = " . $_SESSION['complexID']);
		$rsInvoice = pro_db_fetch_array($sqlInvoice);
		if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
			$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $_SESSION['blockID']);
			if (pro_db_num_rows($sql) > 0) {
				$floorNo = "";
				$brs = pro_db_fetch_array($sql);
				for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
					$floorNo .= '<option value="' . $i . '">Floor - ' . $i . '</option>';
				}
			}
		}

		//Fetch Block Details
		$resBlocks = array();
		$sqlBlockDetails = pro_db_query("select blk.blockID, blk.blockName, blk.noOfFloors, cms.maintenanceType, cms.squareFeetArea, 
										cms.ownerSqFtRate, cms.ownerAmount, cms.rentalSqFtRate, cms.rentalAmount from blockMaster blk
										left join complexMaintenanceSettings cms on blk.blockID = cms.blockID 
										where blk.complexID = " . $_SESSION['complexID']);
		if (pro_db_num_rows($sqlBlockDetails) > 0) {
			while ($res = pro_db_fetch_array($sqlBlockDetails)) {
				$objBlock = array();
				$objBlock['blockID'] = $res['blockID'];
				$objBlock['blockName'] = $res['blockName'];
				$objBlock['noOfFloors'] = $res['noOfFloors'];
				$objBlock['maintenanceType'] = $res['maintenanceType'];
				$objBlock['squareFeetArea'] = $res['squareFeetArea'];
				$objBlock['ownerSqFtRate'] = $res['ownerSqFtRate'];
				$objBlock['ownerAmount'] = $res['ownerAmount'];
				$objBlock['rentalSqFtRate'] = $res['rentalSqFtRate'];
				$objBlock['rentalAmount'] = $res['rentalAmount'];
				$resBlocks[$res['blockID']] = $objBlock;
			}
		}
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Assign Office</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<?php
								if ($_SESSION['groupID'] < 6) {
								?>
									<div class="form-group col-sm-2">
										<label>Block / Area:</label>
										<select name="blockID" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="blockID" onchange="calculateFlatMaintenance();" data-target-list="floorNo" data-target-url="ajax/blocktofloor.php" data-target-title="Select Floor">
											<option value="">Select Block</option>
											<?php echo $blockID; ?>
										</select>
									</div>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<select name="floorNo" id="floorNo" class="custom-select mr-sm-2 form-control" required></select>
									</div>
								<?php } else {
								?>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<select name="floorNo" id="floorNo" class="form-control custom-select mr-sm-2" required>
											<?php echo $floorNo; ?>
										</select>
									</div>
								<?php } ?>
								<div class="form-group col-sm-2">
									<label>Office Number:</label>
									<input type="text" name="officeNumber" class="form-control" placeholder="Office Number" required>
								</div>
								 <div class="form-group col-sm-2">
					                    <label>Office Name:</label>
					                    <select class="form-control" id="officeID" name="officeID">
				                        <option value="">Select Office</option>
			                        			<?php echo $officeID; ?>
					                    </select>
					                </div>
								<div class="form-group col-sm-3">
									<label>Member Name:</label>
									<select name="memberID" id="memberID" class="custom-select mr-sm-2 form-control" data-live-search="true" required>
										<option value="">Select Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Office Holder:</label>
									<select name="occupationType" id="occupationType" class="custom-select mr-sm-3 form-control" onchange="calculateFlatMaintenance();" required>
										<option class="scrollable-menu" role="menu" value="">Select Occupancy</option>
										<?php echo $occupationType; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<label>Square Feet Area:</label>
									<input type="number" min=0 step=1 id="flatArea" name="officeArea" class="form-control" placeholder="Square Feet Area" onchange="calculateFlatMaintenance();" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Maintenance Type:</label>
									<input type="text" id="maintenanceType" class="form-control" placeholder="Fix Maintenance" readonly>
								</div>
								<div class="form-group col-sm-2" id="perSqFtDiv" style="display: none;">
									<label>Per Square Feet Rate:</label>
									<input type="text" id="perSqFtRate" class="form-control" placeholder="Per Square Feet Rate" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Maintenance Amount:</label>
									<input type="number" step="1" id="flatMaintenanceAmt" name="officeMaintenanceAmt" class="form-control" placeholder="Maintenance Amount" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3" id="InvoiceTo" style="display:none">
									<label>Send Invoice To Owner:</label>
									<select name="sendInvoiceToOwner" id="sendInvoiceToOwner" class="form-control custom-select mr-sm-2">
										<option class="scrollable-menu" role="menu" value="">Do you want to send Invoice to owner:</option>
										<?php echo $sendInvoiceToOwner; ?>
									</select>
								</div>
								<div class="form-group col-sm-3" id="ownerName" style="display:none">
									<label>Owner Name:</label>
									<input type="text" name="ownerName" class="form-control" placeholder="Owner Name">
								</div>
								<div class="form-group col-sm-2" id="ownerMobile" style="display:none">
									<label>Owner Mobile:</label>
									<input type="text" name="ownerMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="Owner Mobile">
								</div>
								<div class="form-group col-sm-4" id="ownerEmail" style="display:none">
									<label>Owner Email:</label>
									<input type="text" name="ownerEmail" class="form-control" placeholder="Owner Email">
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
								<input type="hidden" name="status" value="1">
								<input type="hidden" name="isPrimary" value="1"><?php
																				if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
																				?>
									<input type="hidden" name="blockID" value="<?php echo $_SESSION['blockID']; ?>">
								<?php
																				}
								?>
								<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function calculateFlatMaintenance() {
				var listBlocks = <?php echo json_encode($resBlocks); ?>;

				var blockID = document.getElementById('blockID').value;
				var objBlock = listBlocks[blockID];
				var blockName = objBlock['blockName'];
				var noOfFloors = objBlock['noOfFloors'];
				var maintenanceType = objBlock['maintenanceType'];
				var squareFeetArea = objBlock['squareFeetArea'];
				var ownerSqFtRate = objBlock['ownerSqFtRate'];
				var ownerAmount = objBlock['ownerAmount'];
				var rentalSqFtRate = objBlock['rentalSqFtRate'];
				var rentalAmount = objBlock['rentalAmount'];

				var occupationType = document.getElementById('occupationType').value;
				if (occupationType == null || occupationType == "") {
					occupationType = 1;
				}

				var enteredSquareFeetArea = document.getElementById('flatArea').value;
				if (enteredSquareFeetArea == null || enteredSquareFeetArea == "") {
					enteredSquareFeetArea = squareFeetArea;
				}

				//Calculate Maintenance Amount
				if (maintenanceType == 1) {
					if (occupationType == 2) {
						rentalAmount = enteredSquareFeetArea * rentalSqFtRate;
					} else {
						ownerAmount = enteredSquareFeetArea * ownerSqFtRate;
					}
					document.getElementById('maintenanceType').value = "Per Square Feet";
					document.getElementById("perSqFtDiv").style.display = "block";
				} else {
					document.getElementById('maintenanceType').value = "Fix Maintenance";
					document.getElementById("perSqFtDiv").style.display = "none";
				}

				if (occupationType == 2) {
					document.getElementById('flatMaintenanceAmt').value = rentalAmount;
					document.getElementById('perSqFtRate').value = rentalSqFtRate;
				} else {
					document.getElementById('flatMaintenanceAmt').value = ownerAmount;
					document.getElementById('perSqFtRate').value = ownerSqFtRate;
				}
				document.getElementById('flatArea').value = enteredSquareFeetArea;
			}

		</script>
		<script>
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
			 // $('#officeName').on('change', function() {
		  //       var officeName = $(this).val();
		  //       if (officeName==1) {
		  //           $("#addNewOffice").css("display","block");
			 //    }else{
			 //    	 $("#addNewOffice").css("display","none");
			 //    }
	   //  	});

		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select bfm.*, fim.type, fim.invoiceDay, fim.invoiceMonth, fim.invoiceDueDays, fim.noticeIntervalOne, fim.noticeIntervalTwo, fim.noticeIntervalThree, fim.intervalOnePenalty, fim.intervalTwoPenalty, fim.intervalThreePenalty, sms.maintenanceType, sms.squareFeetArea, sms.ownerSqFtRate, sms.ownerAmount, sms.rentalSqFtRate, sms.rentalAmount from blockFloorOfficeMapping bfm 
											left join flatInvoiceMapping fim on bfm.officeMappingID = fim.officeMappingID
											left join complexMaintenanceSettings sms on bfm.blockID = sms.blockID where bfm.officeMappingID = " . (int)$_REQUEST['officeMappingID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Active", "0" => "Pending", "2" => "Reject"), $rs['status']);
		$occupationType = generateStaticOptions(array("1" => "Owner", "2" => "Tenant", "3" => "Looking for Tenant"), $rs['occupationType']);
		$maintenanceType = array("1" => "Per Square Feet", "0" => "Fix Maintenance", "Fix Maintenance");
		$invoiceType = generateStaticOptions(array("0" => "Monthly", "1" => "Quaterly", "2" => "Half yearly", "3" => "Yearly"), $rs['type']);
		$officeID = generateOptions(getMasterList('officeMaster', 'officeID', 'officeName'));
		$blockID = generateOptions(getMasterList('blockMaster', 'blockID', 'blockName', "complexID=" . $_SESSION['complexID'] . " and status = 1"), $rs['blockID']);
		$sendInvoiceToOwner = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['sendInvoiceToOwner']);
		$sqlInvoice = pro_db_query("select isManually from complexAccountSettings where complexID =" . $_SESSION['complexID']);
		$rsInvoice = pro_db_fetch_array($sqlInvoice);

		$squareFeetArea = $rs['officeArea'];
		if ($squareFeetArea == null || empty($squareFeetArea) || $squareFeetArea == 0.0) {
			$squareFeetArea = $rs['squareFeetArea'];
		}

		$perSqFeetRate = $rs['ownerSqFtRate'];
		if ($rs['occupationType'] == 2) {
			$perSqFeetRate = $rs['rentalSqFtRate'];
		}

		$maintenanceAmount = $rs['officeMaintenanceAmt'];
		if ($maintenanceAmount == null || empty($maintenanceAmount) || $maintenanceAmount == 0.0) {
			if ($rs['occupationType'] == 2) {
				$maintenanceAmount = $rs['rentalAmount'];
			} else {
				$maintenanceAmount = $rs['ownerAmount'];
			}
		}

		if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
			$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $rs['blockID']);
			if (pro_db_num_rows($sql) > 0) {
				$floorNo = "";
				$brs = pro_db_fetch_array($sql);
				for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
					if ($i == $rs['floorNo']) $floorNo .= '<option value="' . $i . '" selected>Floor - ' . $i . '</option>';
					else $floorNo .= '<option value="' . $i . '" >Floor - ' . $i . '</option>';
				}
			}
		} else {
			$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $rs['blockID']);
			if (pro_db_num_rows($sql) > 0) {
				$floorNo = "";
				$brs = pro_db_fetch_array($sql);
				for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
					if ($i == $rs['floorNo']) $floorNo .= '<option value="' . $i . '" selected>Floor - ' . $i . '</option>';
					else $floorNo .= '<option value="' . $i . '">Floor - ' . $i . '</option>';
				}
			}
		}
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'status = 1 and complexID = ' . $_SESSION['complexID']), $rs['memberID']);
		//Fetch Block Details
		$resBlocks = array();
		$sqlBlockDetails = pro_db_query("select blk.blockID, blk.blockName, blk.noOfFloors, cms.maintenanceType, cms.squareFeetArea, 
										cms.ownerSqFtRate, cms.ownerAmount, cms.rentalSqFtRate, cms.rentalAmount from blockMaster blk
										left join complexMaintenanceSettings cms on blk.blockID = cms.blockID 
										where blk.complexID = " . $_SESSION['complexID']);
		if (pro_db_num_rows($sqlBlockDetails) > 0) {
			while ($res = pro_db_fetch_array($sqlBlockDetails)) {
				$objBlock = array();
				$objBlock['blockID'] = $res['blockID'];
				$objBlock['blockName'] = $res['blockName'];
				$objBlock['noOfFloors'] = $res['noOfFloors'];
				$objBlock['maintenanceType'] = $res['maintenanceType'];
				$objBlock['squareFeetArea'] = $res['squareFeetArea'];
				$objBlock['ownerSqFtRate'] = $res['ownerSqFtRate'];
				$objBlock['ownerAmount'] = $res['ownerAmount'];
				$objBlock['rentalSqFtRate'] = $res['rentalSqFtRate'];
				$objBlock['rentalAmount'] = $res['rentalAmount'];
				$resBlocks[$res['blockID']] = $objBlock;
			}
		}
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Office Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<?php
								if ($_SESSION['groupID'] < 6) {
								?>
									<div class="col-sm-2">
										<div class="form-group">
											<label>Block / Area:</label>
											<select name="blockID" class="form-control custom-select mr-sm-2 bindbox" id="blockID" disabled data-target-title="Select Floor">
												<option value="">Select Block</option>
												<?php echo $blockID; ?>
											</select>
										</div>
									</div>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<select name="floorNo" id="floorNo" class="form-control custom-select mr-sm-2" disabled required>
											<?php echo $floorNo; ?>
										</select>
									</div>
								<?php } else {
								?>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<select name="floorNo" id="floorNo" class="form-control custom-select mr-sm-2" disabled required>
											<?php echo $floorNo; ?>
										</select>
									</div>
								<?php
								}
								?>
							 	<div class="form-group col-sm-2">
				                    <label>Office Name:</label>
				                    <select class="form-control" id="officeID" name="officeID">
			                        <option value="">Select Office</option>
		                        			<?php echo $officeID; ?>
				                    </select>
					             </div>
								<div class="form-group col-sm-2">
									<label>Office Number:</label>
									<input type="text" name="officeNumber" class="form-control" value="<?php echo $rs['officeNumber']; ?>" readonly>
								</div>
								<div class="form-group col-sm-4">
									<label>Member Name:</label>
									<select id="memberID" class="form-control custom-select mr-sm-2" disabled required>
										<option value="">Select Resident</option>
										<?php echo $memberID; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Office Holder:</label>
									<select name="occupationType" id="occupationType" class="form-control custom-select mr-sm-2" onchange="calculateFlatMaintenance();" required>
										<option class="scrollable-menu" role="menu" value="">Select Occupancy</option>
										<?php echo $occupationType; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<label>Square Feet Area:</label>
									<input type="number" min=0 step=1 id="flatArea" name="officeArea" class="form-control" value="<?php echo $squareFeetArea; ?>" placeholder="Square Feet Area" onchange="calculateFlatMaintenance();" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Maintenance Type:</label>
									<input type="text" id="maintenanceType" class="form-control" value="<?php echo $maintenanceType[$rs['maintenanceType']]; ?>" placeholder="Fix Maintenance" readonly>
								</div>
								<div class="form-group col-sm-2" <?php if ($rs['maintenanceType'] == 0) { ?> style="display: none;" <?php } else { ?> style="display: block;" <?php } ?>>
									<label>Per Square Feet Rate:</label>
									<input type="text" id="perSqFtMaintenance" class="form-control" value="<?php echo $perSqFeetRate; ?>" placeholder="Per Square Feet Rate" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Maintenance Amount:</label>
									<input type="number" min=0 step="1" id="flatMaintenanceAmt" name="officeMaintenanceAmt" class="form-control" value="<?php echo $maintenanceAmount; ?>" placeholder="Maintenance Amount" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2" id="InvoiceTo" <?php if ($rs['occupationType'] == 2) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Send Invoice To Owner:</label>
									<select name="sendInvoiceToOwner" id="sendInvoiceToOwner" class="form-control custom-select mr-sm-2">
										<option class="scrollable-menu" role="menu" value="">Do you want to send Invoice to owner:</option>
										<?php echo $sendInvoiceToOwner; ?>
									</select>
								</div>
								<div class="form-group col-sm-2" id="ownerName" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Name:</label>
									<input type="text" name="ownerName" class="form-control" value="<?php echo $rs['ownerName']; ?>">
								</div>
								<div class="form-group col-sm-2" id="ownerMobile" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Mobile:</label>
									<input type="text" name="ownerMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo $rs['ownerMobile']; ?>" placeholder="Owner Mobile">
								</div>
								<div class="form-group col-sm-4" id="ownerEmail" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Email:</label>
									<input type="text" name="ownerEmail" class="form-control" value="<?php echo $rs['ownerEmail']; ?>">
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
								<input type="hidden" name="officeMappingID" value="<?php echo $rs['officeMappingID']; ?>">
								<input type="hidden" name="oldOfficeID" value="<?php echo $rs['officeID']; ?>">
								<input type="hidden" name="memberID" value="<?php echo $rs['memberID']; ?>">
								<?php
								if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
								?>
									<input type="hidden" name="blockID" value="<?php echo $_SESSION['blockID']; ?>">
								<?php
								}
								?>
								<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function calculateFlatMaintenance() {
				var listBlocks = <?php echo json_encode($resBlocks); ?>;

				var blockID = document.getElementById('blockID').value;
				var objBlock = listBlocks[blockID];
				var blockName = objBlock['blockName'];
				var noOfFloors = objBlock['noOfFloors'];
				var maintenanceType = objBlock['maintenanceType'];
				var squareFeetArea = objBlock['squareFeetArea'];
				var ownerSqFtRate = objBlock['ownerSqFtRate'];
				var ownerAmount = objBlock['ownerAmount'];
				var rentalSqFtRate = objBlock['rentalSqFtRate'];
				var rentalAmount = objBlock['rentalAmount'];

				var occupationType = document.getElementById('occupationType').value;
				if (occupationType == null || occupationType == "") {
					occupationType = 1;
				}

				var enteredSquareFeetArea = document.getElementById('flatArea').value;
				if (enteredSquareFeetArea == null || enteredSquareFeetArea == "") {
					enteredSquareFeetArea = squareFeetArea;
				}

				//Calculate Maintenance Amount
				if (maintenanceType == 1) {
					if (occupationType == 2) {
						rentalAmount = enteredSquareFeetArea * rentalSqFtRate;
					} else {
						ownerAmount = enteredSquareFeetArea * ownerSqFtRate;
					}
				}

				if (occupationType == 2) {
					document.getElementById('flatMaintenanceAmt').value = rentalAmount;
					document.getElementById('perSqFtMaintenance').value = rentalSqFtRate;
				} else {
					document.getElementById('flatMaintenanceAmt').value = ownerAmount;
					document.getElementById('perSqFtMaintenance').value = ownerSqFtRate;
				}
				document.getElementById('flatArea').value = enteredSquareFeetArea;
			}
		</script>
		<script>
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

	public function generateInvoiceForm()
	{
		$qry = pro_db_query("select bfm.*, fim.type, fim.invoiceDay, fim.invoiceMonth, fim.invoiceDueDays, 
							fim.noticeIntervalOne, fim.noticeIntervalTwo, fim.noticeIntervalThree, fim.intervalOnePenalty, 
							fim.intervalTwoPenalty, fim.waiverDays, fim.discountAmount, fim.intervalThreePenalty from blockFloorFlatMapping bfm
							left join flatInvoiceMapping fim on bfm.flatID = fim.flatID
							where bfm.flatID = " . (int)$_REQUEST['flatID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Active", "0" => "Pending", "2" => "Reject"), $rs['status']);
		$blockID = generateOptions(getMasterList('blockMaster', 'blockID', 'blockName', "blockID=" . $rs['blockID'] . " and status = 1"), $rs['blockID']);
		$invoiceType = generateStaticOptions(array("0" => "Monthly", "1" => "Quaterly", "2" => "Half yearly", "3" => "Yearly"), $rs['type']);

		$sqlInvoice = pro_db_query("select isManually from societyMaster where societyID =" . $_SESSION['societyID']);
		$rsInvoice = pro_db_fetch_array($sqlInvoice);

		if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
			$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $rs['blockID']);
			if (pro_db_num_rows($sql) > 0) {
				$floorNo = "";
				$brs = pro_db_fetch_array($sql);
				for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
					if ($i == $rs['floorNo']) $floorNo .= '<option value="' . $i . '" selected>Floor - ' . $i . '</option>';
					else $floorNo .= '<option value="' . $i . '">Floor - ' . $i . '</option>';
				}
			}
		} else {
			$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $rs['blockID']);
			if (pro_db_num_rows($sql) > 0) {
				$floorNo = "";
				$brs = pro_db_fetch_array($sql);
				for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
					if ($i == $rs['floorNo']) $floorNo .= '<option value="' . $i . '" selected>Floor - ' . $i . '</option>';
					else $floorNo .= '<option value="' . $i . '">Floor - ' . $i . '</option>';
				}
			}
		}
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'memberName', 'parentID = 0 and memberID = ' . $rs['memberID']), $rs['memberID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Property</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->generateinvoiceformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<?php
								if ($_SESSION['groupID'] < 6) {
								?>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Block:</label>
											<select name="blockID" id="blockID" class="form-control" role="menu" readonly>
												<?php echo $blockID; ?>
											</select>
										</div>
									</div>
									<div class="form-group col-sm-3">
										<label>Floor Number:</label>
										<input type="text" name="floorNo" class="form-control custom-select mr-sm-2" value="<?php echo "Floor-" . $rs['floorNo']; ?>" readonly>
									</div>
								<?php } else {
								?>
									<div class="form-group col-sm-3">
										<label>Floor Number:</label>
										<input type="text" name="floorNo" class="form-control custom-select mr-sm-2" value="<?php echo "Floor-" . $rs['floorNo']; ?>" readonly>
									</div>
								<?php } ?>
								<div class="form-group col-sm-3">
									<label>Resident Name:</label>
									<select name="memberID" id="memberID" class="form-control custom-select mr-sm-2" data-live-search="true" readonly>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Office Holder:</label>
									<input type="text" name="occupationType" class="form-control custom-select mr-sm-2" value="<?php if ($rs['occupationType'] == 1) {
										echo "Owner";
									} else if ($rs['occupationType'] == 2) {
										echo "Tenant";
									} else {
										echo "Looking for Tenant";
									} ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Residence Number:</label>
									<input type="text" name="flatNumber" class="form-control" value="<?php echo $rs['flatNumber']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Area (in sq.ft):</label>
									<input type="text" name="flatArea" class="form-control" value="<?php echo $rs['flatArea']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3" id="invoiceGeneration">
									<label>Invoice:</label>
									<select name="type" id="invoiceType" class="form-control" required>
										<?php echo $invoiceType; ?>
									</select>
								</div>
								<div class="col-sm-3" id="invoiceDay" style="display:show">
									<div class="form-group">
										<label>Invoice Generation Day:</label>
										<input type="text" name="invoiceDay" class="form-control" value="<?php echo $rs['invoiceDay']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="invoiceMonth" <?php if ($rs['invoiceMonth'] > 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Invoice Generation Month:</label>
										<input type="text" name="invoiceMonth" class="form-control" value="<?php echo $rs['invoiceMonth']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-3" id="invoiceDueDays" style="display:show">
									<div class="form-group">
										<label>Due Days:</label>
										<input type="text" name="invoiceDueDays" class="form-control" value="<?php echo $rs['invoiceDueDays']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="waiverDays" style="display:show">
									<div class="form-group">
										<label>Waiver Days:</label>
										<input type="text" name="waiverDays" class="form-control" value="<?php echo $rs['waiverDays']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="discountAmount" style="display:show">
									<div class="form-group">
										<label>Discount Amount:</label>
										<input type="text" name="discountAmount" class="form-control" value="<?php echo $rs['discountAmount']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="noticeIntervalOne" style="display:show">
									<div class="form-group">
										<label>First Notice Period Days:</label>
										<input type="text" name="noticeIntervalOne" class="form-control" value="<?php echo $rs['noticeIntervalOne']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="intervalOnePenalty" style="display:show">
									<div class="form-group">
										<label>First Notice Period Penalty:</label>
										<input type="text" name="intervalOnePenalty" class="form-control" value="<?php echo $rs['intervalOnePenalty']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="noticeIntervalTwo" style="display:show">
									<div class="form-group">
										<label>Second Notice Period Days:</label>
										<input type="text" name="noticeIntervalTwo" class="form-control" value="<?php echo $rs['noticeIntervalTwo']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="intervalTwoPenalty" style="display:show">
									<div class="form-group">
										<label>Second Notice Period Penalty:</label>
										<input type="text" name="intervalTwoPenalty" class="form-control" value="<?php echo $rs['intervalTwoPenalty']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3" id="noticeIntervalThree" style="display:show">
									<div class="form-group">
										<label>Third Notice Period Days:</label>
										<input type="text" name="noticeIntervalThree" class="form-control" value="<?php echo $rs['noticeIntervalThree']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group" id="intervalThreePenalty" style="display:show">
										<label>Third Notice Period Penalty:</label>
										<input type="text" name="intervalThreePenalty" class="form-control" value="<?php echo $rs['intervalThreePenalty']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Maintenance Amount:</label>
									<input type="number" step="1" name="flatMaintenanceAmt" class="form-control" value="<?php echo $rs['flatMaintenanceAmt']; ?>" readonly>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
								<input type="hidden" name="flatID" value="<?php echo $rs['flatID']; ?>">
								<?php
								if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
								?>
									<input type="hidden" name="blockID" value="<?php echo $_SESSION['blockID']; ?>">
								<?php
								}
								?>
								<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
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
		</script>
	<?php
	}

	public function transferFlatForm()
	{
		$qry = pro_db_query("select bfm.*, mem.memberID, mem.memberName, mem.memberMobile, blk.blockName, 
							fim.type, fim.invoiceDay, fim.invoiceMonth, fim.invoiceDueDays, 
							fim.noticeIntervalOne, fim.noticeIntervalTwo, fim.noticeIntervalThree, fim.intervalOnePenalty, 
							fim.intervalTwoPenalty, fim.intervalThreePenalty, 
							sms.maintenanceType, sms.squareFeetArea, sms.ownerSqFtRate, sms.ownerAmount, sms.rentalSqFtRate, sms.rentalAmount
							from blockFloorOfficeMapping bfm
							left join blockMaster blk on bfm.blockID = blk.blockID
							left join complexMaintenanceSettings sms on bfm.blockID = sms.blockID
							left join memberMaster mem on bfm.memberID = mem.memberID
							left join flatInvoiceMapping fim on bfm.officeMappingID = fim.officeMappingID
							where bfm.officeMappingID = " . (int)$_REQUEST['officeMappingID']);
		$rs = pro_db_fetch_array($qry);
		$assignBackToOwner = generateStaticOptions(array("1" => "Yes", "2" => "No"));
		$arrOccupations = array("1" => "Owner", "2" => "Tenant", "3" => "Looking for Tenant");
		$occupationType = generateStaticOptions($arrOccupations);

		$existingSendInvoiceToOwner = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['sendInvoiceToOwner']);
		$sendInvoiceToOwner = generateStaticOptions(array("1" => "Yes", "0" => "No"));
		$existingMemberName = $rs['memberName'] . ' - ' . $rs['memberMobile'];

		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'memberID != ' . $rs['memberID'] . ' AND status = 1 and complexID = ' . $_SESSION['complexID']));
		$squareFeetArea = $rs['officeArea'];
		if ($squareFeetArea == null || empty($squareFeetArea) || $squareFeetArea == 0.0) {
			$squareFeetArea = $rs['squareFeetArea'];
		}

		$maintenanceAmount = $rs['officeMaintenanceAmt'];
		if ($maintenanceAmount == null || empty($maintenanceAmount) || $maintenanceAmount == 0.0) {
			if ($rs['occupationType'] == 2) {
				$maintenanceAmount = $rs['rentalAmount'];
			} else {
				$maintenanceAmount = $rs['ownerAmount'];
			}
		}

		$existingownersql = pro_db_query("select bfm.officeMappingID, concat(mm.membername,' - ',mm.memberMobile) as ownerDetails from blockFloorOfficeMapping bfm
									left join memberMaster mm on bfm.memberID = mm.memberID
									where bfm.blockID = " . $rs['blockID'] . " and bfm.floorNo = " . $rs['floorNo'] . " and bfm.officeNumber = '" . $rs['officeNumber'] . "' and bfm.status = 4 and bfm.complexID = " . $_SESSION['complexID'] . " and mm.status = 1");
		$existingownerrs = pro_db_fetch_array($existingownersql);
		$existingownerrows = pro_db_num_rows($existingownersql);
	?>

		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Transfer Office to New Member</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->transferflatformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<h4 class="form-group col-sm-12">Existing Office:</h4><br>
								<?php
								if ($_SESSION['groupID'] < 6) {
								?>
									<div class="col-sm-2">
										<div class="form-group">
											<label>Block / Area:</label>
											<input type="text" id="blockID" class="form-control" value="<?php echo $rs['blockName']; ?>" readonly>
										</div>
									</div>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<input type="text" id="floorNo" class="form-control" value="<?php echo $rs['floorNo']; ?>" readonly>
									</div>
								<?php } else {
								?>
									<div class="form-group col-sm-2">
										<label>Floor Number:</label>
										<input type="text" id="floorNo" class="form-control" value="<?php echo $rs['floorNo']; ?>" readonly>
									</div>
								<?php
								}
								?>
								<div class="form-group col-sm-2">
									<label>Office Number:</label>
									<input type="text" id="flatNumber" name="officeNumber" class="form-control" value="<?php echo $rs['officeNumber']; ?>" readonly>
								</div>
								<div class="form-group col-sm-4">
									<label>Existing Member:</label>
									<input type="text" id="existingMember" class="form-control" value="<?php echo $existingMemberName; ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Office Holder:</label>
									<input type="text" id="existingOccupationType" class="form-control" value="<?php echo $arrOccupations[$rs['occupationType']]; ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2" id="OldInvoiceTo" <?php if ($rs['occupationType'] == 2) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Send Invoice To Owner:</label>
									<select id="oldSendInvoiceToOwner" class="form-control custom-select mr-sm-2">
										<option class="scrollable-menu" role="menu" value="">Do you want to send Invoice to owner:</option>
										<?php echo $existingSendInvoiceToOwner; ?>
									</select>
								</div>
								<div class="form-group col-sm-2" id="oldownerName" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Name:</label>
									<input type="text" class="form-control" value="<?php echo $rs['ownerName']; ?>" readonly>
								</div>
								<div class="form-group col-sm-2" id="oldownerMobile" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Mobile:</label>
									<input type="text" class="form-control" value="<?php echo $rs['ownerMobile']; ?>" readonly>
								</div>
								<div class="form-group col-sm-4" id="oldownerEmail" <?php if ($rs['sendInvoiceToOwner'] == 1) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Owner Email:</label>
									<input type="text" class="form-control" value="<?php echo $rs['ownerEmail']; ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3" id="assignbacktoOwner" <?php if ($rs['occupationType'] == 2) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Assign Property Back To Actual Owner:</label>
									<select name="backToOwner" id="backToOwner" class="form-control custom-select mr-sm-2">
										<option value="">Select From Below</option>
										<?php if ($existingownerrows > 0) {
											echo $assignBackToOwner;
										} else { ?>
											<option value="2">No</option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group col-sm-3" id="existingowner" <?php if ($rs['occupationType'] == 2) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Actual Owner:</label>
									<input type="text" class="form-control" <?php if ($existingownerrows > 0) { ?>value="<?php echo $existingownerrs['ownerDetails']; ?>" <?php } else { ?> value="Kindly add new owner details." <?php } ?>readonly>
								</div>
							</div>
							<div class="form-group col-sm-12"></div>
							<div class="row">
								<h4 class="form-group col-sm-12" id="newMemberData" <?php if ($rs['occupationType'] == 2) { ?>style="display:none" <?php } else { ?> style="display:show" <?php } ?>>New Member Data:</h4><br>
								<div class="form-group col-sm-4" id="newMemberID" <?php if ($rs['occupationType'] == 2) { ?>style="display:none" <?php } else { ?> style="display:show" <?php } ?>>
									<label>Member:</label>
									<select name="memberID" id="memberID" class="custom-select mr-sm-2 form-control" data-live-search="true" searchable="Search here..">
										<option value="">Select Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="form-group col-sm-2" id="newOccupationType" <?php if ($rs['occupationType'] == 2) { ?>style="display:none" <?php } else { ?> style="display:show" <?php } ?>>
									<label>Office Holder:</label>
									<select name="occupationType" id="occupationType" class="custom-select mr-sm-2 form-control">
										<option value="">Select Occupancy</option>
										<?php echo $occupationType; ?>
									</select>
								</div>
							</div>
							<div class="row" id="ownerdetails">
								<div class="form-group col-sm-2" id="InvoiceTo" style="display:none">
									<label>Send Invoice To Owner:</label>
									<select name="sendInvoiceToOwner" id="sendInvoiceToOwner" class="form-control custom-select mr-sm-2">
										<option class="scrollable-menu" role="menu" value="">Do you want to send Invoice to owner:</option>
										<?php echo $sendInvoiceToOwner; ?>
									</select>
								</div>
								<div class="form-group col-sm-2" id="ownerName" style="display:none">
									<label>Owner Name:</label>
									<input type="text" name="ownerName" class="form-control" placeholder="Owner Name">
								</div>
								<div class="form-group col-sm-2" id="ownerMobile" style="display:none">
									<label>Owner Mobile:</label>
									<input type="text" name="ownerMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="Owner Mobile">
								</div>
								<div class="form-group col-sm-4" id="ownerEmail" style="display:none">
									<label>Owner Email:</label>
									<input type="text" name="ownerEmail" class="form-control" placeholder="Owner Email">
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
								<input type="hidden" name="floorNo" value="<?php echo $rs['floorNo']; ?>">
								<input type="hidden" name="officeID" value="<?php echo $rs['officeID']; ?>">
								<input type="hidden" name="oldOccupationType" value="<?php echo $rs['occupationType']; ?>">
								<input type="hidden" name="blockID" value="<?php echo $rs['blockID']; ?>">
								<input type="hidden" name="officeMappingID" value="<?php echo $rs['officeMappingID']; ?>">
								<input type="hidden" name="officeArea" value="<?php echo $squareFeetArea; ?>">
								<input type="hidden" name="officeMaintenanceAmt" value="<?php echo $maintenanceAmount; ?>">
								<?php
								if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
								?>
									<input type="hidden" name="blockID" value="<?php echo $_SESSION['blockID']; ?>">
								<?php
								}
								?>
								<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$("#blockID").prop("disabled", true);
			$("#floorNo").prop("disabled", true);
			$("#existingMember").prop("disabled", true);
			$("#oldOccupationType").prop("disabled", true);
			$("#oldSendInvoiceToOwner").prop("disabled", true);

			$('#backToOwner').on('change', function() {
				if (this.value == '1') {
					$("#existingowner").show();
					$("#newMemberData").hide();
					$("#newMemberID").hide();
					$("#newOccupationType").hide();
					$("#ownerdetails").hide();
					$("#newMemberData").prop('required', false);
					$("#newMemberID").prop('required', false);
					$("#newOccupationType").prop('required', false);

				} else {
					$("#existingowner").show();
					$("#newMemberData").show();
					$("#newMemberID").show();
					$("#newOccupationType").show();
					$("#ownerdetails").show();
					$("#newMemberData").prop('required', true);
					$("#newMemberID").prop('required', true);
					$("#newOccupationType").prop('required', true);
				}
			});

			$('#occupationType').on('change', function() {
				if (this.value == '2') {
					$("#InvoiceTo").show();
					$("#InvoiceTo").prop('required', true);

				} else {
					$("#InvoiceTo").hide();
					$("#InvoiceTo").prop('required', false);
					$("#ownerName").hide();
					$("#ownerMobile").hide();
					$("#ownerEmail").hide();
					$("#ownerName").prop('required', false);
					$("#ownerMobile").prop('required', false);
					$("#ownerEmail").prop('required', false);
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
		$formdata['memberID'] = $_POST['memberID'];
		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['officeID'] = $_POST['officeID'];
		$formdata['blockID'] = $_POST['blockID'];
		$formdata['floorNo'] = $_POST['floorNo'];
		$formdata['officeNumber'] = $_POST['officeNumber'];
		$formdata['occupationType'] = $_POST['occupationType'];
		$formdata['officeArea'] = $_POST['officeArea'];
		$formdata['officeMaintenanceAmt'] = $_POST['officeMaintenanceAmt'];

		if (isset($_POST['sendInvoiceToOwner'])) {
			$formdata['sendInvoiceToOwner'] = $_POST['sendInvoiceToOwner'];
		}
		$formdata['ownerName'] = $_POST['ownerName'];
		$formdata['ownerMobile'] = $_POST['ownerMobile'];
		$formdata['ownerEmail'] = $_POST['ownerEmail'];
		$formdata['userID'] = $_SESSION['memberID'];
		$formdata['isPrimary'] = 0;
		$formdata['status'] = 1;
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		// $sql = pro_db_query("select * from blockFloorOfficeMapping where status < 2 and blockID = " . $_POST['blockID'] . " and floorNo = " . $_POST['floorNo'] . " and officeNumber = '" . $_POST['officeNumber'] . "'");
		if (pro_db_num_rows($sql) > 0) {
			$msg = '<p class="bg-danger p-3">This property is already registred to another user!!</p>';
		} else {
			if (pro_db_perform('blockFloorOfficeMapping', $formdata)) {
				$officeMappingID = pro_db_insert_id();

				//dashboard log for flatmapping
				$dashboardlogdata = array();
				$dashboardlogdata['complexID'] = $_SESSION['complexID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "officemapping";
				$dashboardlogdata['subAction'] = "addofficemapping";
				$dashboardlogdata['referenceID'] = $officeMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$officeMemberMappingdata = array();
				$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
				$officeMemberMappingdata['officeID'] = $_POST['officeID'];
				$officeMemberMappingdata['officeMappingID'] = $officeMappingID;
				$officeMemberMappingdata['employeeID'] = $_POST['memberID'];
				$officeMemberMappingdata['parentID'] = 0;
				// $officeMemberMappingdata['adminType'] = 0;
				$officeMemberMappingdata['allowLogin'] = 1;
				$officeMemberMappingdata['isAppUser'] = 0;
				$officeMemberMappingdata['officeDesignationID'] = 0;
				$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
				$officeMemberMappingdata['status'] = 1;
				$officeMemberMappingdata['createdate'] = date('Y-m-d H:i:s');
				$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
				$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

				pro_db_perform('officeMemberMapping', $officeMemberMappingdata);

				//blockFloorFlatMapping table chanegs
				$sql = pro_db_query("select memberID from blockFloorOfficeMapping where memberID = " . $_POST['memberID'] . " and isPrimary = 1");
				if (pro_db_num_rows($sql) == 0) {
					$updateflat = "update blockFloorOfficeMapping bfm, memberMaster mm set bfm.isPrimary = 1, mm.complexID = " . $_SESSION['complexID'] . " where bfm.memberID = mm.memberID and bfm.officeMappingID = '" . $officeMappingID . "'";
					pro_db_query($updateflat);
				}
				$msg = '<p class="bg-success p-3">Property is assign successfully..</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Property is not assign !!!</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "officeMappingID=" . $_POST['officeMappingID'];

		$formdata['officeID'] = $_POST['officeID'];
		$formdata['occupationType'] = $_POST['occupationType'];
		$formdata['officeArea'] = $_POST['officeArea'];
		$formdata['officeMaintenanceAmt'] = $_POST['officeMaintenanceAmt'];

		if (isset($_POST['sendInvoiceToOwner'])) {
			$formdata['sendInvoiceToOwner'] = $_POST['sendInvoiceToOwner'];
		}
		$formdata['ownerName'] = $_POST['ownerName'];
		$formdata['ownerMobile'] = $_POST['ownerMobile'];
		$formdata['ownerEmail'] = $_POST['ownerEmail'];
		$formdata['userID'] = $_SESSION['memberID'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('blockFloorOfficeMapping', $formdata, 'update', $whr)) {

			//dashboard log for flatmapping
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "officemapping";
			$dashboardlogdata['subAction'] = "editofficemapping";
			$dashboardlogdata['referenceID'] = $_POST['officeMappingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$officemappingIDquery = pro_db_query("select mappingID from officeMemberMapping where officeID = " . $_POST['oldOfficeID'] . " and officeMappingID = " . $_POST['officeMappingID'] . " and employeeID = " . $_POST['memberID'] . "");
			$officemappingIDres = pro_db_fetch_array($officemappingIDquery);

			$where = "";
			$where = "mappingID=" . $officemappingIDres['mappingID'];

			$officeMemberMappingdata = array();
			$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
			$officeMemberMappingdata['officeID'] = $_POST['officeID'];
			$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
			$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
			$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

			pro_db_perform('officeMemberMapping', $officeMemberMappingdata, 'update', $where);

			$msg = '<p class="bg-success p-3">Rroperty Details are updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Rroperty Details are not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function transferProperty()
	{
		global $frmMsgDialog;
		$formdata['memberID'] = $_POST['memberID'];
		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['blockID'] = $_POST['blockID'];
		$formdata['officeID'] = $_POST['officeID'];
		$formdata['floorNo'] = $_POST['floorNo'];
		$formdata['officeNumber'] = $_POST['officeNumber'];
		$formdata['occupationType'] = $_POST['occupationType'];
		$formdata['officeArea'] = $_POST['officeArea'];
		$formdata['officeMaintenanceAmt'] = $_POST['officeMaintenanceAmt'];

		if (isset($_POST['sendInvoiceToOwner'])) {
			$formdata['sendInvoiceToOwner'] = $_POST['sendInvoiceToOwner'];
			$formdata['ownerName'] = $_POST['ownerName'];
			$formdata['ownerMobile'] = $_POST['ownerMobile'];
			$formdata['ownerEmail'] = $_POST['ownerEmail'];
		}

		$formdata['userID'] = $_SESSION['memberID'];

		$sql = pro_db_query("select officeMappingID from blockFloorOfficeMapping where memberID = " . $_POST['memberID'] . " and isPrimary = 1");
		$rows = pro_db_num_rows($sql);
		if ($rows > 0) {
			$isPrimary = 0;
		} else {
			$isPrimary = 1;
		}

		$formdata['isPrimary'] = $isPrimary;
		$formdata['status'] = 1;
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if ($_POST['backToOwner'] == 1) {
			if ($_POST['oldOccupationType'] == 2) {
				//Rental to Existing Owner
				//Existing = 5
				if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 5 where officeMappingID = " . $_POST['officeMappingID'] . "")) {
					pro_db_query("update officeMemberMapping set status = 5 where officeMappingID = " . $_POST['officeMappingID'] . "");
					//Fetch Existing Owner = 4
					$query = pro_db_query("select officeMappingID from blockFloorOfficeMapping where blockID = " . $_POST['blockID'] . " and floorNo = " . $_POST['floorNo'] . " and officeNumber = '" . $_POST['officeNumber'] . "' and status = 4");
					$res = pro_db_fetch_array($query);
					//Existing Owner = 1 (From 4 to 1)
					if (pro_db_query("update blockFloorOfficeMapping set isPrimary = " . $isPrimary . ", status = 1 where officeMappingID = " . $res['officeMappingID'] . "")) {
						$officeMappingID = $res['officeMappingID'];
						pro_db_query("update officeMemberMapping set status = 1 where officeMappingID = " . $res['officeMappingID'] . "");
						$msg = '<p class="bg-success p-3">Property has been transferred successfully...</p>';
					} else {
						$msg = '<p class="bg-success p-3">Property transfer has been failed...</p>';
					}
				} else {
					$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
				}
			}
		} else {
			if ($_POST['oldOccupationType'] == 1) {
				//Owner to New Resident
				if ($_POST['occupationType'] == 1) {
					//Sell property to new Owner
					//Existing = 3
					if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 3 where officeMappingID = " . $_POST['officeMappingID'] . "")) {
						pro_db_query("update officeMemberMapping set status = 3 where officeMappingID = " . $_POST['officeMappingID'] . "");
						//New = 1 (Insert)
						if (pro_db_perform('blockFloorOfficeMapping', $formdata)) {

							$officeMappingID = pro_db_insert_id();

							$officeMemberMappingdata = array();
							$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
							$officeMemberMappingdata['officeID'] = $_POST['officeID'];
							$officeMemberMappingdata['officeMappingID'] = $officeMappingID;
							$officeMemberMappingdata['employeeID'] = $_POST['memberID'];
							$officeMemberMappingdata['parentID'] = 0;
							$officeMemberMappingdata['adminType'] = 0;
							$officeMemberMappingdata['allowLogin'] = 1;
							$officeMemberMappingdata['isAppUser'] = 0;
							$officeMemberMappingdata['officeDesignationID'] = 0;
							$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
							$officeMemberMappingdata['status'] = 1;
							$officeMemberMappingdata['createdate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
							pro_db_perform('officeMemberMapping', $officeMemberMappingdata);

							$msg = '<p class="bg-success p-3">Property has been transferred successfully...</p>';
						} else {
							$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
						}
					} else {
						$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
					}
				} else {
					//Rent property to new Rental
					//Existing = 4
					if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 4 where officeMappingID = " . $_POST['officeMappingID'] . "")) {
						pro_db_query("update officeMemberMapping set status = 4 where officeMappingID = " . $_POST['officeMappingID'] . "");
						//New = 1 (Insert)
						if (pro_db_perform('blockFloorOfficeMapping', $formdata)) {
							$officeMappingID = pro_db_insert_id();

							$officeMemberMappingdata = array();
							$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
							$officeMemberMappingdata['officeID'] = $_POST['officeID'];
							$officeMemberMappingdata['officeMappingID'] = $officeMappingID;
							$officeMemberMappingdata['employeeID'] = $_POST['memberID'];
							$officeMemberMappingdata['parentID'] = 0;
							$officeMemberMappingdata['adminType'] = 0;
							$officeMemberMappingdata['allowLogin'] = 1;
							$officeMemberMappingdata['isAppUser'] = 0;
							$officeMemberMappingdata['officeDesignationID'] = 0;
							$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
							$officeMemberMappingdata['status'] = 1;
							$officeMemberMappingdata['createdate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
							pro_db_perform('officeMemberMapping', $officeMemberMappingdata);

							$msg = '<p class="bg-success p-3">Property has been transferred successfully...</p>';
						} else {
							$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
						}
					} else {
						$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
					}
				}
			} else {
				//Rental to New Resident
				if ($_POST['occupationType'] == 1) {
					//Sell property to new Owner
					//Existing = 5
					if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 5 where officeMappingID = " . $_POST['officeMappingID'] . "")) {
						pro_db_query("update officeMemberMapping set status = 5 where officeMappingID = " . $_POST['officeMappingID'] . "");
						//Fetch Existing Owner = 4
						$query = pro_db_query("select officeMappingID from blockFloorOfficeMapping where blockID = " . $_POST['blockID'] . " and floorNo = " . $_POST['floorNo'] . " and officeNumber = '" . $_POST['officeNumber'] . "' and status = 4");
						$res = pro_db_fetch_array($query);
						//Existing Owner = 3 (From 4 to 3)
						if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 3 where officeMappingID = " . $res['officeMappingID'] . "")) {
							pro_db_query("update officeMemberMapping set status = 3 where officeMappingID = " . $res['officeMappingID'] . "");
							//New = 1 (Insert)
							if (pro_db_perform('blockFloorOfficeMapping', $formdata)) {
								$officeMappingID = pro_db_insert_id();

								$officeMemberMappingdata = array();
								$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
								$officeMemberMappingdata['officeID'] = $_POST['officeID'];
								$officeMemberMappingdata['officeMappingID'] = $officeMappingID;
								$officeMemberMappingdata['employeeID'] = $_POST['memberID'];
								$officeMemberMappingdata['parentID'] = 0;
								$officeMemberMappingdata['adminType'] = 0;
								$officeMemberMappingdata['allowLogin'] = 1;
								$officeMemberMappingdata['isAppUser'] = 0;
								$officeMemberMappingdata['officeDesignationID'] = 0;
								$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
								$officeMemberMappingdata['status'] = 1;
								$officeMemberMappingdata['createdate'] = date('Y-m-d H:i:s');
								$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
								$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
								pro_db_perform('officeMemberMapping', $officeMemberMappingdata);

								$msg = '<p class="bg-success p-3">Property has been transferred successfully...</p>';
							} else {
								$msg = '<p class="bg-success p-3">Property transfer has been failed...</p>';
							}
						} else {
							$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
						}
					} else {
						$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
					}
				} else {
					//Rent property to new Rental
					//Existing = 5
					if (pro_db_query("update blockFloorOfficeMapping set isPrimary = 0, status = 5 where officeMappingID = " . $_POST['officeMappingID'] . "")) {
						pro_db_query("update officeMemberMapping set status = 3 where officeMappingID = " . $_POST['officeMappingID'] . "");
						//Existing Owner = 4 (Keep it 4)
						//New = 1 (Insert)
						if (pro_db_perform('blockFloorOfficeMapping', $formdata)) {
							$officeMappingID = pro_db_insert_id();

							$officeMemberMappingdata = array();
							$officeMemberMappingdata['complexID'] = $_SESSION['complexID'];
							$officeMemberMappingdata['officeID'] = $_POST['officeID'];
							$officeMemberMappingdata['officeMappingID'] = $officeMappingID;
							$officeMemberMappingdata['employeeID'] = $_POST['memberID'];
							$officeMemberMappingdata['parentID'] = 0;
							$officeMemberMappingdata['adminType'] = 0;
							$officeMemberMappingdata['allowLogin'] = 1;
							$officeMemberMappingdata['isAppUser'] = 0;
							$officeMemberMappingdata['officeDesignationID'] = 0;
							$officeMemberMappingdata['userID'] = $_SESSION['memberID'];
							$officeMemberMappingdata['status'] = 1;
							$officeMemberMappingdata['createdate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['modifieddate'] = date('Y-m-d H:i:s');
							$officeMemberMappingdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
							pro_db_perform('officeMemberMapping', $officeMemberMappingdata);

							$msg = '<p class="bg-success p-3">Property has been transferred successfully...</p>';
						} else {
							$msg = '<p class="bg-success p-3">Property transfer has been failed...</p>';
						}
					} else {
						$msg = '<p class="bg-danger p-3">Property transfer has been failed...</p>';
					}
				}
			}
		}
		$sql = pro_db_query("select officeMappingID from blockFloorOfficeMapping where memberID = " . $_POST['memberID'] . " and isPrimary = 1");
		$rows = pro_db_num_rows($sql);
		if ($rows == 0) {
			$updateflat = "update blockFloorOfficeMapping bfm, memberMaster mm set  mm.complexID = " . $_SESSION['complexID'] . " where bfm.memberID = mm.memberID and bfm.officeMappingID = '" . $officeMappingID . "'";
			pro_db_query($updateflat);
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from blockFloorOfficeMapping where officeMappingID = '" . (int)$_REQUEST['officeMappingID'] . "'";
		if (pro_db_query($delsql)) {

			//dashboard log for flatmapping
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "flatmapping";
			$dashboardlogdata['subAction'] = "deleteflatmapping";
			$dashboardlogdata['referenceID'] = $_REQUEST['officeMappingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Property is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Property is not deleted!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function generateInvoice()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "officeMappingID=" . $_POST['officeMappingID'];

		$dashboardDatetime = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_POST['invoiceMonth'])) {
			if ($_POST['type'] == 0) {
				$invoiceMonth = 0;
			} else {
				$invoiceMonth = $_POST['invoiceMonth'];
			}
		} else {
			$invoiceMonth = 0;
		}

		$sql = "insert into flatInvoiceMapping (invMappingID, flatID, societyID, type, invoiceDay, invoiceMonth,
				invoiceDueDays, dashboardDatetime, noticeIntervalOne, noticeIntervalTwo, noticeIntervalThree,
				intervalOnePenalty, intervalTwoPenalty, intervalThreePenalty, status, userName, createdate, modifieddate, remote_ip)
				values(null, " . $_POST['flatID'] . ", " . $_SESSION['societyID'] . ", " . $_POST['type'] . ", " . $_POST['invoiceDay'] . ",
				" . $invoiceMonth . ", " . $_POST['invoiceDueDays'] . ", '" . $dashboardDatetime . "',
				" . $_POST['noticeIntervalOne'] . ", " . $_POST['noticeIntervalTwo'] . ", " . $_POST['noticeIntervalThree'] . ",
				" . $_POST['intervalOnePenalty'] . ", " . $_POST['intervalTwoPenalty'] . ", " . $_POST['intervalThreePenalty'] . ",
				1,'" . $_SESSION['username'] . "', '" . $modifieddate . "', '" . $modifieddate . "', '" . $remote_ip . "')
				ON DUPLICATE KEY UPDATE
				type = " . $_POST['type'] . ", invoiceDay = " . $_POST['invoiceDay'] . ", invoiceMonth = " . $invoiceMonth . ",
				invoiceDueDays = " . $_POST['invoiceDueDays'] . ", dashboardDatetime = '" . $dashboardDatetime . "',
				noticeIntervalOne = " . $_POST['noticeIntervalOne'] . ", noticeIntervalTwo = " . $_POST['noticeIntervalTwo'] . ",
				noticeIntervalThree = " . $_POST['noticeIntervalThree'] . ", intervalOnePenalty = " . $_POST['intervalOnePenalty'] . ", intervalTwoPenalty = " . $_POST['intervalTwoPenalty'] . ",
				intervalThreePenalty = " . $_POST['intervalThreePenalty'] . ", userName = '" . $_SESSION['username'] . "',
				modifieddate = '" . $modifieddate . "'";
		if (pro_db_query($sql)) {
			//-------------- No need to change anything here --------------//
			//Header
			$notificationPayload = (int) round(microtime(true) * 1000);
			$notificationHashKey = "2X9xHfKfOYCBZ6FnvoePwsWpty0" . "com.ripl.ggate";
			$notificationHashValue = hash('sha256', ($notificationPayload . $notificationHashKey));
			$headers = [
				'Content-Type: application/json', 'AUTHORIZATION: ' . $notificationHashValue,
				'PAYLOAD: ' . $notificationPayload
			];

			//Notification Params
			$requestParams = [
				'societyID' => $_SESSION['societyID'],
				'flatID' => $_POST['flatID']
			];

			$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "generateSocietyFlatInvoices";

			$ch = curl_init();
			curl_setopt(
				$ch,
				CURLOPT_URL,
				// GGATE_SOCIETY_FLAT_INVOICE_GENERATE_URL
				$CURL_REQUEST_URL
			);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt(
				$ch,
				CURLOPT_POSTFIELDS,
				json_encode($requestParams)
			);

			$result = curl_exec($ch);

			if ($result === FALSE) {
				die('Problem occurred: ' . curl_error($ch));
			}
			curl_close($ch);

			$msg = '<p class="bg-success p-3">Property is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Property is not updated!!!</p>';
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
				<h4>Office Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Assign Office</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="flatMapList" width="100%">
								<thead>
									<tr>
										<th align="left">Block / Area</th>
										<th align="left">Floor</th>
										<th align="left">Office Number</th>
										<th align="left">Office Name</th>
										<th align="left">Office Admin Name</th>
										<th align="left">Area (Sq.ft.)</th>
										<th align="left">Maintenance</th>
										<th align="left">Office Holder</th>
										<th align="left">Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Block / Area</th>
										<th align="left">Floor</th>
										<th align="left">Office Number</th>
										<th align="left">Office Name</th>
										<th align="left">Office Admin Name</th>
										<th align="left">Area (Sq.ft.)</th>
										<th align="left">Maintenance</th>
										<th align="left">Office Holder</th>
										<th align="left">Status</th>
										<th>Action</th>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/flatMapList.php';
			$('#flatMapList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25,
				"order": []
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'blockFloorFlatMapping';
				var field_name = 'flatID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&flatID=" + primaryKey;

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
					"tblName": "blockFloorOfficeMapping"
				},
				source: [{
					value: '1',
					text: 'Accept'
				}, {
					value: '2',
					text: 'Reject'
				}]
			});
		</script>
<?php

	}
}
?>
