<?php
class bookingpricemaster
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
		$assetsID = generateOptions(getMasterList('assetMaster', 'assetID', 'assetsTitle', "societyID=" . $_SESSION['societyID']));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Booking Price</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Select Asset:</label>
									<select name="assetID" id="assetID" class="form-control" data-live-search="true" required>
										<?php echo $assetsID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Per Slot Amount:</label>
										<input type="text" name="perSlotAmount" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Half Day Amount:</label>
										<input type="text" name="halfDayAmount" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Full Day Amount:</label>
										<input type="text" name="fullDayAmount" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Deposit Amount:</label>
										<input type="text" name="depositAmount" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control">
											<?php echo $status; ?>
										</select>
									</div>
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
	<?php
	}
	public function editForm()
	{
		$qry = pro_db_query("select * from bookingPriceMaster where bookingPriceID = " . (int)$_REQUEST['bookingPriceID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$assetsID = generateOptions(getMasterList('assetMaster', 'assetID', 'assetsTitle', "societyID=" . $_SESSION['societyID']), $rs['assetID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Asset Booking Prices</h4>
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
										<option value="">Select Asset</option>
										<?php echo $assetsID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Per Slot Amount:</label>
										<input type="text" name="perSlotAmount" class="form-control" value="<?php echo $rs['perSlotAmount']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Half Day Amount:</label>
										<input type="text" name="halfDayAmount" class="form-control" value="<?php echo $rs['halfDayAmount']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Full Day Amount:</label>
										<input type="text" name="fullDayAmount" class="form-control" value="<?php echo $rs['fullDayAmount']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Deposit Amount:</label>
										<input type="text" name="depositAmount" class="form-control" value="<?php echo $rs['depositAmount']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="bookingPriceID" value="<?php echo $rs['bookingPriceID']; ?>">
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

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;

		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('bookingPriceMaster', $formdata)) {
			$bookingPriceID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Bookings Pricing Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Bookings Pricing Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "bookingPriceID=" . $_POST['bookingPriceID'];
		$formdata = $_POST;

		if (pro_db_perform('bookingPriceMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Bookings Pricing Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Bookings Pricing Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from bookingPriceMaster where bookingPriceID = " . (int)$_GET['bookingPriceID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Bookings Pricing Detail is Deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Bookings Pricing Detail is not Deleted!!!!!!</p>';
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
				<h4>Amenity Booking Price Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Booking Price</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="bookingpricemasterList" width="100%">
								<thead>
									<tr>
										<th>Assets Title</th>
										<th>Per Slot Amount</th>
										<th>Half Day Amount</th>
										<th>Full Day Amount</th>
										<th>Deposit Amount</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Assets Title</th>
										<th>Per Slot Amount</th>
										<th>Half Day Amount</th>
										<th>Full Day Amount</th>
										<th>Deposit Amount</th>
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
			var listURL = 'helperfunc/bookingpricemasterList.php';
			$('#bookingpricemasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "bookingPriceMaster"
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