<?php
class officemaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	// protected $oldDashboardValues;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		// $this->oldDashboardValues = array();
	}

	public function addForm()
	{
		
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Block</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="officeName" class="form-control" placeholder="Office Name" required>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Image:</label>
										<input type="file" name="officeLogo" accept="image/*" id="officeLogo" class="form-control memberImage">
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Email:</label>
									<input type="email" min="1" name="officeEmail" class="form-control" placeholder="Email" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Contact No:</label>
									<input type="number" min="1" name="officeContactNo" class="form-control" placeholder="Contact No" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="status" value="1">
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
		$qry = pro_db_query("select * from officeMaster where officeID = " . (int)$_REQUEST['officeID'] . "");
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Block Master</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="officeName" class="form-control" value="<?php echo $rs['officeName']; ?>" required>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Image:</label>
										<input type="file" name="officeLogo" accept="image/*" class="form-control memberImage">
										<input type="hidden" name="prevImage" value="<?php echo $rs['officeLogo']; ?>" id="officeLogo" class="form-control memberImage">
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Email:</label>
									<input type="email" min="1" name="officeEmail" class="form-control"  value="<?php echo $rs['officeEmail']; ?>"  required>
								</div>
								<div class="form-group col-sm-3">
									<label>Contact No:</label>
									<input type="number" min="1" name="officeContactNo" class="form-control" value="<?php echo $rs['officeContactNo']; ?>"  required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="officeID" value="<?php echo $rs['officeID']; ?>">
									<input type="hidden" name="status" value="1">
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

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('officeMaster', $formdata)) {
			$officeID = pro_db_insert_id();

			//dashboard log for primarymember
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "office";
			$dashboardlogdata['subAction'] = "createoffice";
			$dashboardlogdata['referenceID'] = $officeID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (!empty($_FILES["officeLogo"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$officeLogo = $_FILES["officeLogo"]["name"];
				$image = explode(".", $officeLogo);
				$extension = end($image);

				if ($_FILES["officeLogo"]["error"] > 0) {
					$msg = $_FILES["officeLogo"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['officeLogo']['tmp_name']);
					$objectName = "officeLogo-" . $officeID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "officeID=" . $officeID;
						$imageData['officeLogo'] = $finalImageName;
						if (pro_db_perform('officeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$msg = '<p class="bg-success p-3">Member Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Member Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$officeID = $_POST['officeID'];
		$whr = "";
		$whr = "officeID=" . $officeID;
		$formdata = $_POST;

		if (!empty($_FILES["officeLogo"]["name"])) {
			$allowedTypes = array("gif", "jpeg", "jpg", "png");
			$officeLogo = $_FILES["officeLogo"]["name"];
			$image = explode(".", $officeLogo);
			$extension = end($image);

			if ($_FILES["officeLogo"]["error"] > 0) {
				$msg = $_FILES["officeLogo"]["error"];
				//$rawData["imageName"] = null;
			} else {
				$imageRawData = file_get_contents($_FILES['officeLogo']['tmp_name']);
				$objectName = "officeLogo-" . $officeID . "-" . date('YmdHis') . "." . $extension;
				$imageName = $this->mediaType . "/" . $objectName;
				$imagebaseUrl = GCLOUD_CDN_URL . $this->mediaType . "/";

				//Upload a file to the bucket.
				if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
					$finalImageName = GCLOUD_CDN_URL . $imageName;

					//Update into dailyStaffMaster
					$wher = "";
					$wher = "officeID=" . $officeID;
					$imageData['officeLogo'] = $finalImageName;

					$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevImage']);
					//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);

					if (pro_db_perform('officeMaster', $imageData, 'update', $wher)) {
					}
				}
			}
		}
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');

		unset($formdata['prevImage']);

		if (pro_db_perform('officeMaster', $formdata, 'update', $whr)) {

			//dashboard log for familymember
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmaster";
			$dashboardlogdata['action'] = "officemaster";
			$dashboardlogdata['subAction'] = "editoffice";
			$dashboardlogdata['referenceID'] = $_POST['officeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			$msg = '<p class="bg-success p-3">Member Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Member Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Update officeMaster set status = 126 where officeID = '" . (int)$_REQUEST['officeID'] . "'";

		if (pro_db_query($delsql)) {

			//dashboard log for blockmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "officemaster";
			$dashboardlogdata['subAction'] = "deleteoffice";
			$dashboardlogdata['referenceID'] = $_REQUEST['officeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Block Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Block Detail is not deleted!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-2 py-1 mt-2 mb-3">
				<h4>OFfice Management</h4>
			</div>
			<div class="col-sm-10 py-1 mt-1 mb-3">
				<a href="<?php echo $formaction; ?>" class="btn btn-info float-right ml-2"><i class="fe-plus"></i> Add New Office</a>
			</div>
		</div>	
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="officeMasterList" width="100%">
								<thead>
									<tr>
										<th align="left">Name</th>
										<th align="left">Email</th>
										<th align="left">Mobile</th>
										<th align="left">Logo</th>
										<th align="left">Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Name</th>
										<th align="left">Email</th>
										<th align="left">Mobile</th>
										<th align="left">Logo</th>
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
			var listURL = 'helperfunc/officeMasterList.php';
			$('#officeMasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'officeMaster';
				var field_name = 'officeID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&officeID=" + primaryKey;

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
					"tblName": "officeMaster"
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
