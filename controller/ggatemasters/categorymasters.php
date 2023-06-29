<?php
class categorymasters
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
			$this->mediaType = "icons";
		} else {
			$this->mediaType = "icons-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Category Master</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-4">
									<label>Category Name:</label>
									<input type="text" name="categoryTitle" class="form-control" placeholder="Enter Category Name" required>
								</div>
								<div class="form-group col-sm-4">
									<label>Category Image:</label>
									<input type="file" name="categoryImage" accept="image/*" id="categoryImage" class="form-control categoryImage">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
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
		$qry = pro_db_query("select * from categoryMaster where categoryID = " . (int)$_REQUEST['categoryID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Category Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="form-row">
								<div class="form-group col-sm-4">
									<label>Category Name:</label>
									<input type="text" name="categoryTitle" class="form-control" placeholder="Enter Category Name" value="<?php echo $rs['categoryTitle']; ?>" required>
								</div>
								<div class="form-group col-sm-4">
									<label>Category Image:</label>
									<input type="file" name="categoryImage" accept="image/*" id="categoryImage" class="form-control categoryImage">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<input type="hidden" name="categoryID" value="<?php echo (int)$rs['categoryID']; ?>">
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
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['username'] = $_SESSION['username'];
		$categoryTitle = $_POST["categoryTitle"];
		$imgCategoryTitle = strtolower($categoryTitle);
		$imgCategoryTitle = str_replace(' ', '_', $imgCategoryTitle);

		if (pro_db_perform('categoryMaster', $formdata)) {
			$categoryID = pro_db_insert_id();

			//Upload Vendor Image
			if (!empty($_FILES["categoryImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$categoryImage = $_FILES["categoryImage"]["name"];
				$image = explode(".", $categoryImage);
				$extension = end($image);

				if ($_FILES["categoryImage"]["error"] > 0) {
					$msg = $_FILES["categoryImage"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['categoryImage']['tmp_name']);
					$objectName = "ico_ven_" . $imgCategoryTitle . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "categoryID = " . $categoryID;
						$imageData['categoryImage'] = $finalImageName;
						pro_db_perform('categoryMaster', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success text-white p-3">New Category is created...</p>';
		} else {
			$msg = '<p class="bg-success text-white p-3">Category is not created...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "categoryID = " . $_POST['categoryID'];

		$formdata = $_POST;
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$categoryID = $_POST['categoryID'];
		$categoryTitle = $_POST["categoryTitle"];
		$imgCategoryTitle = strtolower($categoryTitle);
		$imgCategoryTitle = str_replace(' ', '_', $imgCategoryTitle);

		if (pro_db_perform('categoryMaster', $formdata, 'update', $whr)) {
			//Upload Vendor Image
			if (!empty($_FILES["categoryImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$categoryImage = $_FILES["categoryImage"]["name"];
				$image = explode(".", $categoryImage);
				$extension = end($image);

				if ($_FILES["categoryImage"]["error"] > 0) {
					$msg = $_FILES["categoryImage"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['categoryImage']['tmp_name']);
					$objectName = "ico_ven_" . $imgCategoryTitle . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "categoryID = " . $categoryID;
						$imageData['categoryImage'] = $finalImageName;
						pro_db_perform('categoryMaster', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success p-3">Category Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Category Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from categoryMaster where categoryID = " . (int)$_REQUEST['categoryID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Category Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Category Detail is not deleted!!!</p>';
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
				<h4>GGATE Vendor Categories</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Category</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="categoryMasterList" width="100%">
								<thead>
									<tr>
										<th width="10%">Image</th>
										<th>Category Name</th>
										<th width="10%">Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="10%">Image</th>
										<th>Category Name</th>
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
				var listURL = 'helperfunc/categoryMasterList.php';
				$('#categoryMasterList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25,
					"order": []
				});
			});
			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "categoryMaster"
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