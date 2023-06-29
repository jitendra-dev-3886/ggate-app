<?php
class accountgroupmaster
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
		$accountHeadID = generateOptions(getMasterList('accountHeadMaster', 'accountHeadID', 'headName', "status = 1 and parentID = 0"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Account Group</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Account Head:</label>
									<select name="accountHeadID" id="accountHeadID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Account Head</option>
										<?php echo $accountHeadID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Group Name:</label>
									<input type="text" name="groupName" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
		$qry = pro_db_query("select * from accountGroupMaster where accountGroupID = " . (int)$_REQUEST['accountGroupID']);
		$rs = pro_db_fetch_array($qry);
		$accountHeadID = generateOptions(getMasterList('accountHeadMaster', 'accountHeadID', 'headName', "status = 1 and parentID != 0"), $rs['accountHeadID']);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Account Group</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Account Head:</label>
									<select name="accountHeadID" id="accountHeadID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Account Head</option>
										<?php echo $accountHeadID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Group Name:</label>
									<input type="text" name="groupName" class="form-control" value="<?php echo $rs['groupName']; ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="accountGroupID" value="<?php echo $rs['accountGroupID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function preventBack() {
				window.history.forward();
			}
			window.onunload = function() {
				null;
			};
			setTimeout("preventBack()", 0);
		</script>
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

		if (pro_db_perform('accountGroupMaster', $formdata)) {
			$accountGroupID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Account Type Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Account Type Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "accountGroupID = " . $_POST['accountGroupID'];
		$formdata = $_POST;

		if (pro_db_perform('accountGroupMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Account Type Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Type Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update accountGroupMaster set status = 126 where accountGroupID = " . (int)$_GET['accountGroupID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Account Type Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Type Detail Not deleted successfully</p>';
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
				<h4>Account Group Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Account Group</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="accountGroupList" width="100%">
								<thead>
									<tr>
										<th>Group</th>
										<th>Account Head</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Group</th>
										<th>Account Head</th>
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
			var listURL = 'helperfunc/accountGroupList.php';
			$('#accountGroupList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "accountGroupMaster"
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