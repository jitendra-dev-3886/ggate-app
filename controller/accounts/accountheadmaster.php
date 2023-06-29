<?php
class accountheadmaster
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
		$headType = generateStaticOptions(array("0" => "Balance Sheet", "1" => "P & L", "2" => "None"));
		$headSide = generateStaticOptions(array("0" => "Dr", "1" => "Cr"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Account Head</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Head Name:</label>
									<input type="text" name="headName" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Head Type:</label>
									<select name="headType" class="form-control custom-select mr-sm-2">
										<?php echo $headType; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<label>Head Side:</label>
									<select name="headSide" class="form-control custom-select mr-sm-2">
										<?php echo $headSide; ?>
									</select>
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
		$qry = pro_db_query("select * from accountHeadMaster where accountHeadID = " . (int)$_REQUEST['accountHeadID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$headType = generateStaticOptions(array("0" => "Balance Sheet", "1" => "P & L", "2" => "None"), $rs['headType']);
		$headSide = generateStaticOptions(array("0" => "Dr", "1" => "Cr"), $rs['headSide']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Account Head</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Head Name:</label>
									<input type="text" name="headName" class="form-control" value="<?php echo $rs['headName']; ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Head Type:</label>
									<select name="headType" class="form-control custom-select mr-sm-2">
										<?php echo $headType; ?>
									</select>
								</div>
								<div class="form-group col-sm-2">
									<label>Head Side:</label>
									<select name="headSide" class="form-control custom-select mr-sm-2">
										<?php echo $headSide; ?>
									</select>
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
									<input type="hidden" name="accountHeadID" value="<?php echo $rs['accountHeadID']; ?>">
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
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('accountHeadMaster', $formdata)) {
			$accountHeadID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Account Head Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Account Head Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "accountHeadID = " . $_POST['accountHeadID'];
		$formdata = $_POST;

		if (pro_db_perform('accountHeadMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Account Head Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Head Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update accountHeadMaster set status = 126 where accountHeadID = " . (int)$_GET['accountHeadID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Account Head Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Head Detail Not deleted successfully</p>';
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
				<h4>Account Head Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Account Head</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="accountHeadList" width="100%">
								<thead>
									<tr>
										<th>Head</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Head</th>
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
			var listURL = 'helperfunc/accountHeadList.php';
			$('#accountHeadList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "accountHeadMaster"
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