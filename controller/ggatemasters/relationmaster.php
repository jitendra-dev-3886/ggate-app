<?php
class relationmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $cloudStorage;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "sosicons";
		} else {
			$this->mediaType = "sosicons-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Relation</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Relation Title:</label>
									<input type="text" name="relationTitle" class="form-control" placeholder="Enter Relation Title" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Sort order:</label>
									<input type="text" name="sortorder" class="form-control" placeholder="Sort Order" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
		$qry = pro_db_query("select * from relationMaster where relationID = " . (int)$_REQUEST['relationID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Relation Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Relation Title:</label>
									<input type="text" name="relationTitle" class="form-control" value="<?php echo $rs['relationTitle']; ?>" placeholder="Enter Relation Title" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Sort order:</label>
									<input type="text" name="sortorder" class="form-control" value="<?php echo $rs['sortorder']; ?>" placeholder="Sort Order" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="relationID" value="<?php echo (int)$rs['relationID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
		$formdata = array();
		$formdata['relationTitle'] = $_POST['relationTitle'];
		$formdata['sortorder'] = $_POST['sortorder'];
		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('relationMaster', $formdata)) {
			$relationID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Relation Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Relation is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "relationID = " . $_POST['relationID'];

		$formdata = array();
		$formdata['relationTitle'] = $_POST['relationTitle'];
		$formdata['sortorder'] = $_POST['sortorder'];
		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('relationMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Relation Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Relation Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update relationMaster set status = 126 where relationID = " . (int)$_GET['relationID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Relation Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Relation Detail Not deleted successfully</p>';
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
				<h4>Relation List</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Relation</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="relationMasterList" width="100%">
								<thead>
									<tr>
										<th width="20%">Title</th>
										<th width="10%">Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="20%">Title</th>
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
			$(document).ready(function() {
				var listURL = 'helperfunc/relationMasterList.php';
				$('#relationMasterList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25
				});
			});
			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "relationMaster"
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