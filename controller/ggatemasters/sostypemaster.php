<?php
class sostypemaster
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
		$sendSMS = generateStaticOptions(array("1" => "Yes", "0" => "No"));
		$sendOBD = generateStaticOptions(array("1" => "Yes", "0" => "No"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add SOS Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>SOS Type:</label>
									<input type="text" name="sosTypeTitle" class="form-control" placeholder="Enter SOS Type" required>
								</div>
								<div class="form-group col-sm-3">
									<label>SOS Image:</label>
									<input type="file" name="sosImage" accept="image/*" id="sosImage" class="form-control memberImage">
								</div>
								<div class="form-group col-sm-3">
									<label>SOS Selected Image:</label>
									<input type="file" name="sosSelectedImage" accept="image/*" id="sosSelectedImage" class="form-control memberImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Send SMS:</label>
									<select name="sendSMS" class="form-control custom-select mr-sm-2">
										<?php echo $sendSMS; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Send OBD:</label>
									<select name="sendOBD" class="form-control custom-select mr-sm-2">
										<?php echo $sendOBD; ?>
									</select>
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
		$qry = pro_db_query("select * from sosTypeMaster where sosTypeID = " . (int)$_REQUEST['sosTypeID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$sendSMS = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['sendSMS']);
		$sendOBD = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['sendOBD']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit SOS Type Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>SOS Type:</label>
									<input type="text" name="sosTypeTitle" class="form-control" placeholder="Enter SOS Type" value="<?php echo $rs['sosTypeTitle']; ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>SOS Image:</label>
									<input type="file" name="sosImage" accept="image/*" id="sosImage" class="form-control memberImage">
								</div>
								<div class="form-group col-sm-3">
									<label>SOS Selected Image:</label>
									<input type="file" name="sosSelectedImage" accept="image/*" id="sosSelectedImage" class="form-control memberImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Send SMS:</label>
									<select name="sendSMS" class="form-control custom-select mr-sm-2">
										<?php echo $sendSMS; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Send OBD:</label>
									<select name="sendOBD" class="form-control custom-select mr-sm-2">
										<?php echo $sendOBD; ?>
									</select>
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
									<input type="hidden" name="sosTypeID" value="<?php echo (int)$rs['sosTypeID']; ?>">
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
		$formdata['sosTypeTitle'] = $_POST['sosTypeTitle'];
		$formdata['sendSMS'] = $_POST['sendSMS'];
		$formdata['sendOBD'] = $_POST['sendOBD'];
		$formdata['status'] = 1;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('sosTypeMaster', $formdata)) {
			$sosTypeID = pro_db_insert_id();

			if (!empty($_FILES["sosImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$sosImage = $_FILES["sosImage"]["name"];
				$image = explode(".", $sosImage);
				$extension = end($image);

				if ($_FILES["sosImage"]["error"] > 0) {
					$msg = $_FILES["sosImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['sosImage']['tmp_name']);
					$objectName = "sosImage-" . $sosTypeID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "sosTypeID = " . $sosTypeID;
						$imageData['sosImage'] = $finalImageName;
						if (pro_db_perform('sosTypeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["sosSelectedImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$sosSelectedImage = $_FILES["sosSelectedImage"]["name"];
				$image = explode(".", $sosSelectedImage);
				$extension = end($image);

				if ($_FILES["sosSelectedImage"]["error"] > 0) {
					$msg = $_FILES["sosSelectedImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['sosSelectedImage']['tmp_name']);
					$objectName = "sosSelectedImage-" . $sosTypeID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "sosTypeID = " . $sosTypeID;
						$imageData['sosSelectedImage'] = $finalImageName;
						if (pro_db_perform('sosTypeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$msg = '<p class="bg-success p-3">SOS Type Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> SOS Type is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "sosTypeID = " . $_POST['sosTypeID'];

		$formdata = array();
		$formdata['sosTypeTitle'] = $_POST['sosTypeTitle'];
		$formdata['sendSMS'] = $_POST['sendSMS'];
		$formdata['sendOBD'] = $_POST['sendOBD'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('sosTypeMaster', $formdata, 'update', $whr)) {
			$sosTypeID = $_POST['sosTypeID'];

			if (!empty($_FILES["sosImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$sosImage = $_FILES["sosImage"]["name"];
				$image = explode(".", $sosImage);
				$extension = end($image);

				if ($_FILES["sosImage"]["error"] > 0) {
					$msg = $_FILES["sosImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['sosImage']['tmp_name']);
					$objectName = "sosImage-" . $sosTypeID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "sosTypeID = " . $sosTypeID;
						$imageData['sosImage'] = $finalImageName;
						if (pro_db_perform('sosTypeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["sosSelectedImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$sosSelectedImage = $_FILES["sosSelectedImage"]["name"];
				$image = explode(".", $sosSelectedImage);
				$extension = end($image);

				if ($_FILES["sosSelectedImage"]["error"] > 0) {
					$msg = $_FILES["sosSelectedImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['sosSelectedImage']['tmp_name']);
					$objectName = "sosSelectedImage-" . $sosTypeID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "sosTypeID = " . $sosTypeID;
						$imageData['sosSelectedImage'] = $finalImageName;
						if (pro_db_perform('sosTypeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$msg = '<p class="bg-success p-3">SOS Type Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> SOS Type is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update sosTypeMaster set status = 126 where sosTypeID = " . (int)$_GET['sosTypeID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">SOS Type Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">SOS Type Detail Not deleted successfully</p>';
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
				<h4>SOS Type List</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Type</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="sosTypeMasterList" width="100%">
								<thead>
									<tr>
										<th width="20%">Title</th>
										<th>SOS Image</th>
										<th>SOS Selected Image</th>
										<th>Send SMS</th>
										<th>Send OBD</th>
										<th width="10%">Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="20%">Title</th>
										<th>SOS Image</th>
										<th>SOS Selected Image</th>
										<th>Send SMS</th>
										<th>Send OBD</th>
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
				var listURL = 'helperfunc/sosTypeMasterList.php';
				$('#sosTypeMasterList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25
				});
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "sosTypeMaster"
				},
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Inactive'
				}]
			});
			$('.table').editable({
				selector: 'a.esendOBD',
				params: {
					"tblName": "sosTypeMaster"
				},
				source: [{
					value: '1',
					text: 'Yes'
				}, {
					value: '0',
					text: 'No'
				}]
			});
			$('.table').editable({
				selector: 'a.esendSMS',
				params: {
					"tblName": "sosTypeMaster"
				},
				source: [{
					value: '1',
					text: 'Yes'
				}, {
					value: '0',
					text: 'No'
				}]
			});
		</script>
<?php
	}
}
?>