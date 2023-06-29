<?php
class popup
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

	public function editForm()
	{
		$sql = "select * from popupMaster where popupID = " . (int)$_REQUEST['popupID'];
		$qry = pro_db_query($sql);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Category</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>PopUp Title:</label>
										<input type="text" name="popupTitle" class="form-control" placeholder="PopUp Title" value="<?php echo $rs['popupTitle']; ?>" required>
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
										<label class="col-xs-3">Upload PopUp image:</label>
										<div class="col-xs-7">
											<input type="file" name="popupImage" id="popupImage" class="form-control popupImage" required>
											<input type="hidden" name="prevImage" value="<?php echo $rs['popupImage']; ?>" id="popupImage" class="form-control popupImage">
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>PopUp Image:</label>
										<img src="<?php echo DIR_WS_BLOG_PATH . $rs['popupImage']; ?>" class="img-responsive">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="popupID" value="<?php echo (int)$rs['popupID']; ?>">
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
		$image = explode(".", $_FILES["popupImage"]["name"]);
		$extension = end($image);
		$upload = pro_SeoSlug(pro_db_real_escape_string($formdata['popupTitle'])) . "." . $extension;

		$formdata['popupImage'] = $upload;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['sortorder'] = get_sortorder('popupMaster', 'popupID');

		if (pro_db_perform('popupMaster', $formdata)) {
			$popupID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">PopUp is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">PopUp is not saved!!!</p>';
		}
		/* Now Upload Image */
		$target_path = DIR_FS_BLOG_PATH . $upload;
		if (!move_uploaded_file($_FILES["popupImage"]["tmp_name"], $target_path)) {
			$msg = '<p class="bg-danger">PopUp image is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "popupID=" . $_POST['popupID'];
		$formdata = $_POST;
		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['popupTitle']));

		if ($_FILES["popupImage"]['size'] == 0 ||  $_FILES["popupImage"] == $_POST['prevImage']) {
			$updimg = false;
			$upload = $_POST['prevImage'];
		} else {
			$image = explode(".", $_FILES["popupImage"]["name"]);
			$extension = end($image);
			$upload = pro_SeoSlug(pro_db_real_escape_string($formdata['popupTitle'])) . "." . $extension;
			@unlink(DIR_FS_BLOG_PATH . $_POST['prevImage']);
			$target_path = DIR_FS_BLOG_PATH . $upload;
			if (!move_uploaded_file($_FILES["popupImage"]["tmp_name"], $target_path)) {
				$msg = '<p class="bg-danger">PopUp image is not updated!!!</p>';
			}
		}

		$formdata['popupImage'] = $upload;
		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		unset($formdata['prevImage']);

		if (pro_db_perform('popupMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">PopUp Type is saved successfully...</p>';
			updSeoLnk($_POST['popupID'], "popupMaster", $slug);
		} else {
			$msg = '<p class="bg-danger p-3">PopUp Type is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from popupMaster where popupID = '" . (int)$_REQUEST['popupID'] . "'";
		if (pro_db_query($delsql)) {
			delSeoLnk((int)$_REQUEST['popupID'], 'blog');
			$msg = '<p class="bg-success p-3">PopUp is deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">PopUp is not deleted!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	function listData()
	{
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>PopUp Master</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><button class="btn btn-info float-right addButton" data-target-form="addCategory"><i class="fe-plus"></i>&nbsp;&nbsp;PopUp Add</a></div>
		</div>
		<!-- Add Form Template Start -->
		<div class="row quickForm hide" id="addCategory">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>PopUp Title :</label>
										<input type="text" name="popupTitle" class="form-control" placeholder="SOS Type Title" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Upload PopUp image:</label>
										<input type="file" name="popupImage" id="popupImage" class="form-control popupImage" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Sort Order:</label>
										<input name="sortorder" class="form-control" placeholder="1">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;
										<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- Add Form Template End -->
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="popupList" width="100%">
							<thead>
								<tr>
									<th align="left">ID</th>
									<th align="left">PopUp Title</th>
									<th align="left">PopUp Image</th>
									<th align="left">Status</th>
									<th align="left">Sort Order</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th align="left">ID</th>
									<th align="left">PopUp Title</th>
									<th align="left">PopUp Image</th>
									<th align="left">Status</th>
									<th align="left">Sort Order</th>
									<th>Action</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/popupList.php';
			$('#popupList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 25
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'popupMaster';
				var field_name = 'popupID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&popupID=" + primaryKey;

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
					"tblName": "popupMaster"
				},
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Disabled'
				}]
			});
		</script>
<?php
	}
}
?>