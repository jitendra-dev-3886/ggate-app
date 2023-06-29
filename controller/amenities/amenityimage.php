<?php
class amenityimage
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

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "community";
		} else {
			$this->mediaType = "community-dev";
		}
	}

	public function addForm()
	{
		$assetsID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Amenity Image</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Amenity:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Amenity</option>
										<?php echo $assetsID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 1:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 2:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 3:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 4:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control ">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Image 5:</label>
										<input type="file" name="assetImage[]" accept="image/*" class="form-control">
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
		$qry = pro_db_query("select * from amenityImage where imageID = " . (int)$_REQUEST['imageID']);
		$rs = pro_db_fetch_array($qry);
		$assetsID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "complexID=" . $_SESSION['complexID'] . " and status = 1"), $rs['assetID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Assets Image</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Assets Title:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2" data-live-search="true" disabled required>
										<option value="">Select Assets</option>
										<?php echo $assetsID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Upload Asset image:</label>
										<input type="file" accept="image/*" name="assetImage" id="assetImage" class="form-control">
										<input type="hidden" name="prevImage" value="<?php echo $rs['assetImage']; ?>" id="assetImage" class="form-control">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="assetID" value="<?php echo $rs['assetID']; ?>">
										<input type="hidden" name="imageID" value="<?php echo $rs['imageID']; ?>">
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

	public function add()
	{
		global $frmMsgDialog;
		foreach ($_FILES['assetImage']['tmp_name'] as $key => $value) {
			$formdata['assetID'] = $_POST['assetID'];
			if (!empty($_FILES["assetImage"]["name"][$key])) {
				if (pro_db_perform('amenityImage', $formdata)) {
					$imageID = pro_db_insert_id();
					$allowedTypes = array("gif", "jpeg", "jpg", "png");
					$assetImage = $_FILES["assetImage"]["name"][$key];
					$image = explode(".", $assetImage);
					$extension = end($image);

					if ($_FILES["assetImage"]["error"][$key] > 0) {
						$msg = $_FILES["assetImage"]["error"][$key];
						//$rawData["imageName"] = null;
					} else {
						$imageRawData = file_get_contents($_FILES['assetImage']['tmp_name'][$key]);
						$objectName = "assetImage-" . $imageID . "-" . date('YmdHis') . "." . $extension;
						$imageName = $this->mediaType . "/" . $objectName;

						//Upload a file to the bucket.
						if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
							$finalImageName = GCLOUD_CDN_URL . $imageName;
							//Update assets Image		
							$wher = "";
							$wher = "imageID=" . $imageID;
							$imageData['assetImage'] = $finalImageName;
							pro_db_perform('amenityImage', $imageData, 'update', $wher);

							$dashboardlogdata = array();
							$dashboardlogdata['complexID'] = $_SESSION['complexID'];
							$dashboardlogdata['memberID'] = $_SESSION['memberID'];
							$dashboardlogdata['contorller'] = "amenities";
							$dashboardlogdata['action'] = "amenityimage";
							$dashboardlogdata['subAction'] = "add";
							$dashboardlogdata['referenceID'] = $imageID;
							$dashboardlogdata['status'] = 1;
							$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
							$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
							$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

							pro_db_perform('dashboardLogMaster', $dashboardlogdata);
						}
					}
					$msg = '<p class="bg-success p-3">Assets Image is saved Successfully!!!!!!</p>';
				} else {
					$msg = '<p class="bg-danger p-3">Assets Image is not saved!!!!!!</p>';
				}
			}
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$imageID = $_POST['imageID'];
		$whr = "";
		$whr = "imageID=" . $imageID;
		$formdata = $_POST;

		if (!empty($_FILES["assetImage"]["name"])) {
			$allowedTypes = array("gif", "jpeg", "jpg", "png");
			$assetImage = $_FILES["assetImage"]["name"];
			$image = explode(".", $assetImage);
			$extension = end($image);

			if ($_FILES["assetImage"]["error"] > 0) {
				$msg = $_FILES["assetImage"]["error"];
				//$rawData["imageName"] = null;
			} else {
				$imageRawData = file_get_contents($_FILES['assetImage']['tmp_name']);
				$objectName = "assetImage-" . $imageID . "-" . date('YmdHis') . "." . $extension;
				$imageName = $this->mediaType . "/" . $objectName;
				$imagebaseUrl = GCLOUD_CDN_URL . $this->mediaType . "/";

				//Upload a file to the bucket.
				if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
					$finalImageName = GCLOUD_CDN_URL . $imageName;

					//Update into dailyStaffMaster
					$wher = "";
					$wher = "imageID=" . $imageID;
					$imageData['assetImage'] = $finalImageName;
					$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevImage']);
					//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);
					if (pro_db_perform('amenityImage', $imageData, 'update', $wher)) {
					}
				}
			}
		}

		unset($formdata['prevImage']);
		$formdata['assetID'] = $_POST['assetID'];

		if (pro_db_perform('amenityImage', $formdata, 'update', $whr)) {

			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "amenities";
			$dashboardlogdata['action'] = "amenityimage";
			$dashboardlogdata['subAction'] = "edit";
			$dashboardlogdata['referenceID'] = $_POST['imageID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Asset Image is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Asset Image is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from amenityImage where imageID = " . (int)$_GET['imageID'];
		if (pro_db_query($delsql)) {
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "amenities";
			$dashboardlogdata['action'] = "amenityimage";
			$dashboardlogdata['subAction'] = "delete";
			$dashboardlogdata['referenceID'] = $_GET['imageID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Assets Image has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets Image Not deleted successfully</p>';
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
				<h4>Asset Images</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Amenity Images</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="amenityImageList" width="100%">
								<thead>
									<tr>
										<th width="15%">Asset</th>
										<th>Asset Images</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="15%">Asset</th>
										<th>Asset Images</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/amenityImageList.php';
			$('#amenityImageList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "assetsImage"
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