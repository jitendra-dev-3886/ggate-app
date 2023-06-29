<?php
class itemmaster
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
		$itemTypeID = generateOptions(getMasterList('itemTypeMaster', 'itemTypeID', 'itemTypeTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Item </h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Item Type:</label>
									<select name="itemTypeID" id="itemTypeID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Item Type</option>
										<?php echo $itemTypeID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="itemTitle" class="form-control" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Manufacturing Company:</label>
										<input type="text" name="itemMfgCompany" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Model:</label>
										<input type="text" name="itemModel" class="form-control" placeholder="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>HSN Code:</label>
										<input type="text" name="itemHSN" class="form-control" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Remarks:</label>
										<input type="text" name="itemRemark" class="form-control" placeholder="">
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
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select im.*, itm.itemTypeTitle from itemMaster im 
							join itemTypeMaster itm on itm.itemTypeID = im.itemTypeID
							where im.itemID = " . (int)$_REQUEST['itemID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$itemTypeID = generateOptions(getMasterList('itemTypeMaster', 'itemTypeID', 'itemTypeTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"), $rs['itemTypeID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Item Type</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Item Type:</label>
									<select name="itemTypeID" id="itemTypeID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Item Type</option>
										<?php echo $itemTypeID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="itemTitle" class="form-control" value="<?php echo $rs['itemTitle']; ?>" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Manufacturing Company:</label>
										<input type="text" name="itemMfgCompany" class="form-control" value="<?php echo $rs['itemMfgCompany']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Model:</label>
										<input type="text" name="itemModel" class="form-control" value="<?php echo $rs['itemModel']; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>HSN Code:</label>
										<input type="text" name="itemHSN" class="form-control" value="<?php echo $rs['itemHSN']; ?>">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Remarks:</label>
										<input type="text" name="itemRemark" class="form-control" value="<?php echo $rs['itemRemark']; ?>">
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
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="itemID" value="<?php echo $rs['itemID']; ?>">
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
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('itemMaster', $formdata)) {
			$itemMaster = pro_db_insert_id();

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "itemmaster";
			$dashboardlogdata['subAction'] = "additemmaster";
			$dashboardlogdata['referenceID'] = $itemMaster;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Item Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Item Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "itemID=" . $_POST['itemID'];
		$formdata = $_POST;

		if (pro_db_perform('itemMaster', $formdata, 'update', $whr)) {

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "itemmaster";
			$dashboardlogdata['subAction'] = "edititemmaster";
			$dashboardlogdata['referenceID'] = $_POST['itemID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Item Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Item Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from itemMaster where itemID = " . (int)$_GET['itemID'];
		if (pro_db_query($delsql)) {

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "itemmaster";
			$dashboardlogdata['subAction'] = "deleteitemmaster";
			$dashboardlogdata['referenceID'] = $_GET['itemID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Item Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Item Detail Not deleted successfully</p>';
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
				<h4>Item Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Item</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="itemList" width="100%">
								<thead>
									<tr>
										<th width="20%">Title</th>
										<th>Item Type</th>
										<th>Company</th>
										<th>HSN Code</th>
										<th width="10%">Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="20%">Title</th>
										<th>Item Type</th>
										<th>Company</th>
										<th>HSN Code</th>
										<th width="10%">Status</th>
										<th width="10%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/itemList.php';
			$('#itemList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "itemMaster"
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