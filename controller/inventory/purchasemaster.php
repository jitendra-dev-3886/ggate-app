<?php
class purchasemaster
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
		$warrantyType = generateStaticOptions(array("1" => "Year", "0" => "Month"));
		$itemWarranty = generateStaticOptions(array("1" => "No", "0" => "Yes"));
		$itemID = generateOptions(getMasterList('itemMaster', 'itemID', 'itemTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Make Purchase</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-4">
									<label>Item:</label>
									<select name="itemID" id="itemID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select item</option>
										<?php echo $itemID; ?>
									</select>
								</div>
								<div class="form-group col-sm-4">
									<label>Manufacturing Company:</label>
									<input type="text" name="companyName" id="companyName" class="form-control" placeholder="Enter Manufacturing Company Name">
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Date:</label>
										<input type="text" name="purchaseDate" class="form-control eventTodayDateTime" value="<?php echo date('Y-m-d H:i:s'); ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Quantity:</label>
										<input type="number" min=0 step=1 name="purchaseQty" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Unit:</label>
										<input type="number" min=0 step=1 type="text" name="purchaseUnit" id="purchaseUnit" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Rate:</label>
										<input type="number" min=0 step=1 name="purchaseRate" id="purchaseRate" onchange="calculatePurchaseAmount()" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Discount Percentage:</label>
										<input type="number" min=0 step="0.1" max=100 inputmode="decimal" name="discountPer" id="discountPer" onchange="calculatePurchaseAmount()" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Discount Amount:</label>
										<input type="text" name="discountAmount" id="discountAmount" class="form-control" placeholder="" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>CGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="cGSTPer" id="cGSTPer" onchange="calculatePurchaseAmount()" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>SGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="sGSTPer" id="sGSTPer" onchange="calculatePurchaseAmount()" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>IGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="iGSTPer" id="iGSTPer" class="form-control" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>CGST Amount:</label>
										<input type="text" name="cGSTAmount" id="cGSTAmount" class="form-control" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>SGST Amount:</label>
										<input type="text" name="sGSTAmount" id="sGSTAmount" class="form-control" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>IGST Amount:</label>
										<input type="text" name="iGSTAmount" id="iGSTAmount" class="form-control" placeholder="" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Amount:</label>
										<input type="text" name="purchaseAmount" id="purchaseAmount" class="form-control" placeholder="" readonly>
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Warranty Included?</label>
									<select name="itemWarranty" id="itemWarranty" class="form-control custom-select mr-sm-2">
										<?php echo $itemWarranty; ?>
									</select>
								</div>
								<div class="col-sm-2" id="warrantyType" style="display:show">
									<div class="form-group">
										<label>Warranty Type:</label>
										<select name="warrantyType" id="warrantyType" class="form-control custom-select mr-sm-2">
											<?php echo $warrantyType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2" id="warrantyExpDate" style="display:show">
									<div class="form-group">
										<label>Warranty Expiry Date:</label>
										<input type="text" id="warrantyExpDate" name="warrantyExpDate" class="form-control eventTodayDateTime" value="<?php echo date('Y-m-d H:i:s'); ?>">
									</div>
								</div>
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
			$('#itemWarranty').on('change', function() {
				if (this.value == '0') {
					$("#warrantyType").show();
					$("#warrantyType").prop('required', true);
					$("#warrantyExpDate").show();
					$("#warrantyExpDate").prop('required', true);
				} else {
					$("#warrantyType").hide();
					$("#warrantyType").prop('required', false);
					$("#warrantyExpDate").hide();
					$("#warrantyExpDate").prop('required', false);
				}
			});
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				minDate: "today",
				dateFormat: "Y-m-d"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});

			function calculatePurchaseAmount() {
				$purchaseUnit = document.getElementById('purchaseUnit').value;
				$purchaseRate = document.getElementById('purchaseRate').value;
				$amount = $purchaseUnit * $purchaseRate;

				//Discount
				$discountPer = document.getElementById('discountPer').value ?? 0.0;
				$discountAmount = $amount * $discountPer / 100;
				document.getElementById('discountAmount').value = $discountAmount;

				//Amount after Discount
				$totalAmount = $amount - $discountAmount;

				//CGST
				$cGSTPer = document.getElementById('cGSTPer').value ?? 0;
				$cGSTAmount = $totalAmount * $cGSTPer / 100;
				document.getElementById('cGSTAmount').value = $cGSTAmount;

				//SGST
				$sGSTPer = document.getElementById('sGSTPer').value ?? 0;
				$sGSTAmount = $totalAmount * $sGSTPer / 100;
				document.getElementById('sGSTAmount').value = $sGSTAmount;

				//IGST
				$iGSTPer = Number($cGSTPer) + Number($sGSTPer);
				document.getElementById('iGSTPer').value = $iGSTPer;
				$iGSTAmount = $totalAmount * $iGSTPer / 100;
				document.getElementById('iGSTAmount').value = $iGSTAmount;

				//Calulate Final Purchase Amount
				$totalTax = $cGSTAmount + $sGSTAmount;
				$purchaseAmount = $totalAmount + $totalTax;
				document.getElementById('purchaseAmount').value = $purchaseAmount;
			}
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from purchaseMaster where purchaseID = " . (int)$_REQUEST['purchaseID']);
		$rs = pro_db_fetch_array($qry);
		$warrantyType = generateStaticOptions(array("1" => "Year", "0" => "Month"), $rs['warrantyType']);
		$itemWarranty = generateStaticOptions(array("1" => "No", "0" => "Yes"), $rs['itemWarranty']);
		$itemID = generateOptions(getMasterList('itemMaster', 'itemID', 'itemTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"), $rs['itemID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Purchase</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-4">
									<label>Item:</label>
									<select name="itemID" id="itemID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select item</option>
										<?php echo $itemID; ?>
									</select>
								</div>
								<div class="form-group col-sm-4">
									<label>Manufacturing Company:</label>
									<input type="text" name="companyName" id="companyName" class="form-control" value="<?php echo $rs['companyName']; ?>" placeholder="Enter Manufacturing Company Name">
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Date:</label>
										<input type="text" name="purchaseDate" class="form-control eventTodayDateTime" value="<?php echo $rs['purchaseDate']; ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Quantity:</label>
										<input type="number" min=0 step=1 name="purchaseQty" class="form-control" value="<?php echo $rs['purchaseQty']; ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Unit:</label>
										<input type="number" min=0 step=1 name="purchaseUnit" id="purchaseUnit" class="form-control" value="<?php echo $rs['purchaseUnit']; ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Rate:</label>
										<input type="number" min=0 step=1 name="purchaseRate" id="purchaseRate" onchange="calculatePurchaseAmount()" class="form-control" value="<?php echo $rs['purchaseRate']; ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Discount Percentage:</label>
										<input type="number" type="number" min=0 step="0.1" max=100 inputmode="decimal" name="discountPer" id="discountPer" onchange="calculatePurchaseAmount()" class="form-control" value="<?php echo $rs['discountPer']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Discount Amount:</label>
										<input type="text" name="discountAmount" id="discountAmount" class="form-control" value="<?php echo $rs['discountAmount']; ?>" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>CGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="cGSTPer" id="cGSTPer" onchange="calculatePurchaseAmount()" class="form-control" value="<?php echo $rs['cGSTPer']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>SGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="sGSTPer" id="sGSTPer" onchange="calculatePurchaseAmount()" class="form-control" value="<?php echo $rs['sGSTPer']; ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>IGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="iGSTPer" id="iGSTPer" class="form-control" value="<?php echo $rs['iGSTPer']; ?>" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>CGST Amount:</label>
										<input type="text" name="cGSTAmount" id="cGSTAmount" class="form-control" value="<?php echo $rs['cGSTAmount']; ?>" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>SGST Amount:</label>
										<input type="text" name="sGSTAmount" id="sGSTAmount" class="form-control" value="<?php echo $rs['sGSTAmount']; ?>" readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>IGST Amount:</label>
										<input type="text" name="iGSTAmount" id="iGSTAmount" class="form-control" value="<?php echo $rs['iGSTAmount']; ?>" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>Purchase Amount:</label>
										<input type="text" name="purchaseAmount" id="purchaseAmount" class="form-control" value="<?php echo $rs['purchaseAmount']; ?>" readonly>
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Warranty Included?</label>
									<select name="itemWarranty" id="itemWarranty" class="form-control custom-select mr-sm-2">
										<?php echo $itemWarranty; ?>
									</select>
								</div>
								<div class="col-sm-2" id="warrantyType" <?php if ($rs['itemWarranty'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Warranty Type:</label>
										<select name="warrantyType" id="warrantyType" class="form-control custom-select mr-sm-2">
											<?php echo $warrantyType; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2" id="warrantyExpDate" <?php if ($rs['itemWarranty'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Warranty Expiry Date:</label>
										<input type="text" id="warrantyExpDate" name="warrantyExpDate" class="form-control eventTodayDateTime" value="<?php echo $rs['warrantyExpDate']; ?>">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="purchaseID" value="<?php echo $rs['purchaseID']; ?>">
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
			$('#itemWarranty').on('change', function() {
				if (this.value == '0') {
					$("#warrantyType").show();
					$("#warrantyType").prop('required', true);
					$("#warrantyExpDate").show();
					$("#warrantyExpDate").prop('required', true);
				} else {
					$("#warrantyType").hide();
					$("#warrantyType").prop('required', false);
					$("#warrantyExpDate").hide();
					$("#warrantyExpDate").prop('required', false);
				}
			});
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});

			function calculatePurchaseAmount() {
				$purchaseUnit = document.getElementById('purchaseUnit').value;
				$purchaseRate = document.getElementById('purchaseRate').value;
				$amount = $purchaseUnit * $purchaseRate;

				//Discount
				$discountPer = document.getElementById('discountPer').value ?? 0.0;
				$discountAmount = $amount * $discountPer / 100;
				document.getElementById('discountAmount').value = $discountAmount;

				//Amount after Discount
				$totalAmount = $amount - $discountAmount;

				//CGST
				$cGSTPer = document.getElementById('cGSTPer').value ?? 0;
				$cGSTAmount = $totalAmount * $cGSTPer / 100;
				document.getElementById('cGSTAmount').value = $cGSTAmount;

				//SGST
				$sGSTPer = document.getElementById('sGSTPer').value ?? 0;
				$sGSTAmount = $totalAmount * $sGSTPer / 100;
				document.getElementById('sGSTAmount').value = $sGSTAmount;

				//IGST
				$iGSTPer = Number($cGSTPer) + Number($sGSTPer);
				document.getElementById('iGSTPer').value = $iGSTPer;
				$iGSTAmount = $totalAmount * $iGSTPer / 100;
				document.getElementById('iGSTAmount').value = $iGSTAmount;

				//Calulate Final Purchase Amount
				$totalTax = $cGSTAmount + $sGSTAmount;
				$purchaseAmount = $totalAmount + $totalTax;
				document.getElementById('purchaseAmount').value = $purchaseAmount;
			}
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;

		if (!isset($_POST['discountPer']) || empty($_POST['discountPer'])) {
			unset($formdata['discountPer']);
		}
		if (!isset($_POST['discountAmount']) || empty($_POST['discountAmount'])) {
			unset($formdata['discountAmount']);
		}
		if (!isset($_POST['cGSTPer']) || empty($_POST['cGSTPer'])) {
			unset($formdata['cGSTPer']);
		}
		if (!isset($_POST['cGSTAmount']) || empty($_POST['cGSTAmount'])) {
			unset($formdata['cGSTAmount']);
		}
		if (!isset($_POST['sGSTPer']) || empty($_POST['sGSTPer'])) {
			unset($formdata['sGSTPer']);
		}
		if (!isset($_POST['sGSTAmount']) || empty($_POST['sGSTAmount'])) {
			unset($formdata['sGSTAmount']);
		}
		if (!isset($_POST['iGSTPer']) || empty($_POST['iGSTPer'])) {
			unset($formdata['iGSTPer']);
		}
		if (!isset($_POST['iGSTAmount']) || empty($_POST['iGSTAmount'])) {
			unset($formdata['iGSTAmount']);
		}

		if (pro_db_perform('purchaseMaster', $formdata)) {
			$itemTypeID = pro_db_insert_id();

			//dashboard log for purchasemaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "purchasemaster";
			$dashboardlogdata['subAction'] = "addpurchase";
			$dashboardlogdata['referenceID'] = $itemTypeID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Purchase Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Purchase Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "purchaseID=" . $_POST['purchaseID'];
		$formdata = $_POST;

		if (!isset($_POST['discountPer']) || empty($_POST['discountPer'])) {
			unset($formdata['discountPer']);
		}
		if (!isset($_POST['discountAmount']) || empty($_POST['discountAmount'])) {
			unset($formdata['discountAmount']);
		}
		if (!isset($_POST['cGSTPer']) || empty($_POST['cGSTPer'])) {
			unset($formdata['cGSTPer']);
		}
		if (!isset($_POST['cGSTAmount']) || empty($_POST['cGSTAmount'])) {
			unset($formdata['cGSTAmount']);
		}
		if (!isset($_POST['sGSTPer']) || empty($_POST['sGSTPer'])) {
			unset($formdata['sGSTPer']);
		}
		if (!isset($_POST['sGSTAmount']) || empty($_POST['sGSTAmount'])) {
			unset($formdata['sGSTAmount']);
		}
		if (!isset($_POST['iGSTPer']) || empty($_POST['iGSTPer'])) {
			unset($formdata['iGSTPer']);
		}
		if (!isset($_POST['iGSTAmount']) || empty($_POST['iGSTAmount'])) {
			unset($formdata['iGSTAmount']);
		}

		if (pro_db_perform('purchaseMaster', $formdata, 'update', $whr)) {

			//dashboard log for purchasemaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "purchasemaster";
			$dashboardlogdata['subAction'] = "editpurchase";
			$dashboardlogdata['referenceID'] = $_POST['purchaseID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Purchase Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Purchase Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from purchaseMaster where purchaseID = " . (int)$_GET['purchaseID'];
		if (pro_db_query($delsql)) {

			//dashboard log for purchasemaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "purchasemaster";
			$dashboardlogdata['subAction'] = "deletepurchase";
			$dashboardlogdata['referenceID'] = $_GET['purchaseID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Purchase Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Purchase Detail Not deleted successfully</p>';
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
				<h4>Purchase Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Purchase</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="purchasemasterList" width="100%">
								<thead>
									<tr>
										<th>Item Title</th>
										<th>Company Name</th>
										<th>Purchase Quantity</th>
										<th>Purchase Rate</th>
										<th>Total Amount</th>
										<th>Item Warranty</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Item Title</th>
										<th>Company Name</th>
										<th>Purchase Quantity</th>
										<th>Purchase Rate</th>
										<th>Total Amount</th>
										<th>Item Warranty</th>
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
			var listURL = 'helperfunc/purchasemasterList.php';
			$('#purchasemasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "purchaseMaster"
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