<?php
class faqmaster
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
		$faqTypeID = generateOptions(getMasterList('faqMaster', 'faqTypeID', 'faqType', "status = 1"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add FAQs Type</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>FAQ Type:</label>
									<select name="faqTypeID" class="form-control custom-select mr-sm-2">
										<?php echo $faqTypeID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" name="faqTitle" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Link:</label>
									<input type="text" name="link" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Sort Order:</label>
									<input name="sortorder" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label>Description:</label>
									<textarea name="faqDescription" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"></textarea>
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
		$sql = "select * from faq where faqID = " . (int)$_REQUEST['faqID'];
		$qry = pro_db_query($sql);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$faqTypeID = generateOptions(getMasterList('faqMaster', 'faqTypeID', 'faqType', "status = 1"), $rs['faqTypeID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit FAQs Type</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>FAQ Type:</label>
									<select name="faqTypeID" class="form-control custom-select mr-sm-2">
										<?php echo $faqTypeID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" name="faqTitle" class="form-control" value="<?PHP echo $rs['faqTitle']; ?>" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Link:</label>
									<input type="text" name="link" class="form-control" value="<?PHP echo $rs['link']; ?>" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Sort Order:</label>
									<input name="sortorder" class="form-control" value="<?php echo $rs['sortorder']; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label>Description:</label>
									<textarea name="faqDescription" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"><?php echo $rs['faqDescription']; ?></textarea>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="faqID" value="<?php echo (int)$rs['faqID']; ?>">
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
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('faq', $formdata)) {
			$faqID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">FAQs Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">FAQ Detail is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "faqID = " . $_POST['faqID'];
		
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('faq', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">FAQ Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">FAQ Detail is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from faq where faqID = " . (int)$_REQUEST['faqID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">FAQ Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">FAQ Detail is not deleted!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>FAQ List</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New FAQ</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="faqList" width="100%">
								<thead>
									<tr>
										<th align="left" width=10%>Type</th>
										<th align="left" width=20%>Title</th>
										<th align="left">Description</th>
										<th align="left" width=20%>Link</th>
										<th align="left" width=5%>Status</th>
										<th width=5%>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left" width=10%>Type</th>
										<th align="left" width=20%>Title</th>
										<th align="left">Description</th>
										<th align="left" width=20%>Link</th>
										<th align="left" width=5%>Status</th>
										<th width=5%>Action</th>
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
				var listURL = 'helperfunc/faqList.php';
				$('#faqList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25,
					"order": []
				});
			});
			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "faq"
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