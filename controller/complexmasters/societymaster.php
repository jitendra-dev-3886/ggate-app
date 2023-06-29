<?php
class societymaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;

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
		$Country = generateOptions(getMasterArray('countries', 'countries_id', 'countries_name'));
		$State = generateOptions(getMasterArray('zones', 'zone_id', 'zone_name'));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Society Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Name :</label>
										<input type="text" name="societyName" class="form-control" required placeholder="Society Name">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Address :</label>
										<input type="text" name="societyAddress" class="form-control" required placeholder="Society Address">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society City :</label>
										<input type="text" name="societyCity" class="form-control" required placeholder="Society City">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Country</label>
										<select name="countries_id" class="form-control" id="countries_id" required>
											<option value="" hidden>Select Country</option>
											<?php echo $Country; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>State</label>
										<select name="zone_id" class="form-control" id="zone_id" required>
											<option value="" hidden>Select State</option>
											<?php echo $State; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>No Of Property :</label>
										<input type="text" name="noOfProperty" class="form-control" placeholder="No Of Property">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society ContactNo :</label>
										<input type="text" name="societyContactNo" required minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="Society ContactNo">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Email :</label>
										<input type="email" name="societyEmail" class="form-control" placeholder="Society Email">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Sort Order:</label>
										<input name="sortorder" class="form-control" placeholder="1">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Upload Society Logo:</label>
										<input type="file" name="societyLogo" id="societyLogo" class="form-control societyLogo">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
		$qry = pro_db_query("select * from societyMaster where societyID = " . (int)$_REQUEST['societyID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$Country = generateOptions(getMasterArray('countries', 'countries_id', 'countries_name'), $rs['countries_id']);
		$State = generateOptions(getMasterArray('zones', 'zone_id', 'zone_name'), $rs['zone_id']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Society Detail</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Name :</label>
										<input type="text" name="societyName" class="form-control" value="<?php echo $rs['societyName']; ?>" placeholder="Society Name" required>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Address :</label>
										<input type="text" name="societyAddress" class="form-control" value="<?php echo $rs['societyAddress']; ?>" placeholder="Society Address" required>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society City :</label>
										<input type="text" name="societyCity" class="form-control" value="<?php echo $rs['societyCity']; ?>" placeholder="Society City" required>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Country</label>
										<select name="countries_id" class="form-control" id="countries_id" required>
											<option value="" hidden>Select Country</option>
											<?php echo $Country; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>State</label>
										<select name="zone_id" class="form-control" id="zone_id" required>
											<option value="" hidden>Select State</option>
											<?php echo $State; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>No Of Property :</label>
										<input type="text" name="noOfProperty" class="form-control" value="<?php echo $rs['noOfProperty']; ?>" placeholder="No Of Property">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society ContactNo :</label>
										<input type="text" name="societyContactNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo $rs['societyContactNo']; ?>" placeholder="Society ContactNo" required>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Email :</label>
										<input type="email" name="societyEmail" class="form-control" value="<?php echo $rs['societyEmail']; ?>" placeholder="Society Email">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Sort Order:</label>
										<input name="sortorder" class="form-control" value="<?php echo $rs['sortorder']; ?>">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="col-xs-3">Upload image:</label>
										<div class="col-xs-7">
											<input type="file" name="societyLogo" id="societyLogo" class="form-control societyLogo">
											<input type="hidden" name="prevImage" value="<?php echo $rs['societyLogo']; ?>" id="societyLogo" class="form-control societyLogo">
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Society Logo:</label>
										<img src="<?php echo DIR_WS_BLOG_PATH . $rs['societyLogo']; ?>" class="img-responsive">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="societyID" value="<?php echo (int)$rs['societyID']; ?>">
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

		$image = explode(".", $_FILES["societyLogo"]["name"]);
		$extension = end($image);
		$upload = pro_SeoSlug(pro_db_real_escape_string($formdata['societyName'])) . "." . $extension;
		$formdata['societyLogo'] = $upload;

		if (pro_db_perform('societyMaster', $formdata)) {
			$societyID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Society Detail is saved...</p>';
		}
		// if (strlen(trim($value)) > 0) {
		// 	$insertsql = pro_db_query("INSERT INTO groupMaster set
		// 	societyName = '" . $_POST['societyName'] . "',
		// 	societyAddress = '" . $_POST['societyAddress'] . "'");
		// } else {
		// 	$msg = '<p class="bg-danger p-3">Society Detail is not saved!!!</p>';
		// }

		/* Now Upload Image */
		$target_path = DIR_FS_POPUP_PATH . $upload;
		if (!move_uploaded_file($_FILES["societyLogo"]["tmp_name"], $target_path)) {
			$msg = '<p class="bg-danger">Popup image is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "societyID=" . $_POST['societyID'];
		$formdata = $_POST;

		if ($_FILES["societyLogo"]['size'] == 0 ||  $_FILES["societyLogo"] == $_POST['prevImage']) {
			$updimg = false;
			$upload = $_POST['prevImage'];
		} else {
			$image = explode(".", $_FILES["societyLogo"]["name"]);
			$extension = end($image);
			$upload = pro_SeoSlug(pro_db_real_escape_string($formdata['societyName'])) . "." . $extension;
			@unlink(DIR_FS_BLOG_PATH . $_POST['prevImage']);
			$target_path = DIR_FS_BLOG_PATH . $upload;
			if (!move_uploaded_file($_FILES["societyLogo"]["tmp_name"], $target_path)) {
				$msg = '<p class="bg-danger">Society image is not updated!!!</p>';
			}
		}
		$formdata['societyLogo'] = $upload;
		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		unset($formdata['prevImage']);

		if (pro_db_perform('societyMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Society Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Society Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from societyMaster where societyID = '" . (int)$_REQUEST['societyID'] . "'";
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Society Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Society Detail is not deleted!!!</p>';
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
				<h4>Society Master</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Society Detail</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="societymasterList" width="100%">
								<thead>
									<tr>
										<th align="left">Society Name</th>
										<th align="left">Society Logo</th>
										<th align="left">Society Address</th>
										<th align="left">Society City</th>
										<th align="left">Countries</th>
										<th align="left">Zone</th>
										<th align="left">Society ContactNo</th>
										<th align="left">Society Email</th>
										<th align="left">Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Society Name</th>
										<th align="left">Society Logo</th>
										<th align="left">Society Address</th>
										<th align="left">Society City</th>
										<th align="left">Countries</th>
										<th align="left">Zone</th>
										<th align="left">Society ContactNo</th>
										<th align="left">Society Email</th>
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
			var listURL = 'helperfunc/societymasterList.php';
			$('#societymasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'news';
				var field_name = 'societyID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&societyID=" + primaryKey;

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
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "societyMaster"
				},
				value: 1,
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Inactive'
				}]
			});
		</script>
		<script>
			var hash = new Date().getTime();
			$('select[name="countries_id"]').on('change', function() {
				$('select[name="zone_id"]').load("ajax/states.php?hash=" + hash, {
					id: $(this).val(),
					ajax: 'true'
				});
			});
		</script>
<?php
	}
}
?>