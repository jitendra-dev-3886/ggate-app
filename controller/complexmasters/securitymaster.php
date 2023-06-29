<?php
class securitymaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $cloudStorage;
	protected $mediaType;
	protected $staffMediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "staff";
			$this->staffMediaType = "staffIdentity";
		} else {
			$this->mediaType = "staff-dev";
			$this->staffMediaType = "staffIdentity-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$employeeType = generateStaticOptions(array("0" => "Security on Gate", "1" => "Security at Club"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Security Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Name :</label>
									<input type="text" name="employeeName" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Qualification :</label>
									<input type="text" name="employeeQualification" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Office Address :</label>
									<input type="text" name="employeeOfficeAddress" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Residency Address</label>
									<input type="text" name="employeeResideAddress" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Mobile No</label>
									<input type="text" name="employeeMobileNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Phone No</label>
									<input type="text" name="employeePhoneNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Email Address</label>
									<input type="email" name="employeeEmailAddress" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>About</label>
									<input type="text" name="employeeAbout" class="form-control" placeholder="About Employee">
								</div>
								<div class="form-group col-sm-3">
									<label>ID Type:</label>
									<select name="employeeIDType" class="form-control custom-select mr-sm-2">
										<option value="1">Adhar Card</option>
										<option value="2">Driving License</option>
										<option value="3">PAN Card</option>
										<option value="4">Voter ID</option>
										<option value="5">Leaving Certificate</option>
										<option value="10">Other</option>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Value</label>
									<input type="text" name="employeeIDValue" maxlength="14" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Security For:</label>
									<select name="employeeType" class="form-control custom-select mr-sm-2">
										<?php echo $employeeType; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<option value="1">Enable</option>
										<option value="0">Disable</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Image:</label>
									<input type="file" accept="image/*" name="employeeImage" id="employeeImage" class="form-control employeeImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Photo ID:</label>
									<input type="file" accept="image/*" name="employeePhotoID" id="employeePhotoID" class="form-control employeePhotoID" required>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="staffTypeID" value="7">
										<input type="hidden" name="vendorID" value="0">
										<input type="hidden" name="employeeCode" value="<?php echo genOTP(4); ?>">
										<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// Example starter JavaScript for disabling form submissions if there are invalid fields
			(function() {
				'use strict';
				window.addEventListener('load', function() {
					// Fetch all the forms we want to apply custom Bootstrap validation styles to
					var forms = document.getElementsByClassName('needs-validation');
					// Loop over them and prevent submission
					var validation = Array.prototype.filter.call(forms, function(form) {
						form.addEventListener('submit', function(event) {
							if (form.checkValidity() === false) {
								event.preventDefault();
								event.stopPropagation();
							}
							form.classList.add('was-validated');
						}, false);
					});
				}, false);
			})();
		</script>
	<?php
	}

	public function editForm()
	{
		$sql = "select * from complexEmployeeMaster where employeeID = " . (int)$_REQUEST['employeeID'];
		$qry = pro_db_query($sql);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$employeeIDType = generateStaticOptions(array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID", "5" => "Leaving Certificate", "10" => "Other"), $rs['employeeIDType']);
		$employeeType = generateStaticOptions(array("0" => "Security on Gate", "1" => "Security at Club"));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Security</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="employeeName" class="form-control" value="<?php echo stripslashes($rs['employeeName']); ?>" placeholder="Employee Name" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Qualification:</label>
									<input type="text" name="employeeQualification" class="form-control" value="<?php echo stripslashes($rs['employeeQualification']); ?>" placeholder="Employee Qulification">
								</div>
								<div class="form-group col-sm-3">
									<label>Office Address:</label>
									<input type="text" name="employeeOfficeAddress" class="form-control" value="<?php echo stripslashes($rs['employeeOfficeAddress']); ?>" placeholder="employeeOfficeAddress">
								</div>
								<div class="form-group col-sm-3">
									<label>Residency Address:</label>
									<input type="text" name="employeeResideAddress" class="form-control" value="<?php echo stripslashes($rs['employeeResideAddress']); ?>" placeholder="employee Residency Address">
								</div>
								<div class="form-group col-sm-3">
									<label>Mobile No:</label>
									<input type="text" name="employeeMobileNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['employeeMobileNo']); ?>" placeholder="Employee Mobile No" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Phone No:</label>
									<input type="text" name="employeePhoneNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['employeePhoneNo']); ?>" placeholder="Employee PhoneNo">
								</div>
								<div class="form-group col-sm-3">
									<label>Email Address:</label>
									<input type="email" name="employeeEmailAddress" class="form-control" value="<?php echo stripslashes($rs['employeeEmailAddress']); ?>" placeholder="Employee Email Address">
								</div>
								<div class="form-group col-sm-3">
									<label>About:</label>
									<input type="text" name="employeeAbout" class="form-control" value="<?php echo stripslashes($rs['employeeAbout']); ?>" placeholder="About Employee">
								</div>
								<div class="form-group col-sm-3">
									<label>ID Type:</label>
									<select name="employeeIDType" class="form-control custom-select mr-sm-2">
										<?php echo $employeeIDType; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Value:</label>
									<input type="text" name="employeeIDValue" maxlength="14" class="form-control" value="<?php echo stripslashes($rs['employeeIDValue']); ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Security For:</label>
									<select name="employeeType" class="form-control custom-select mr-sm-2">
										<?php echo $employeeType; ?>
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
								<div class="form-group col-sm-3">
									<label>Image:</label>
									<input type="file" accept="image/*" name="employeeImage" id="employeeImage" class="form-control employeeImage">
									<input type="hidden" name="prevImage" value="<?php echo $rs['employeeImage']; ?>" id="employeeImage" class="form-control employeeImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Photo ID:</label>
									<input type="file" accept="image/*" name="employeePhotoID" id="employeePhotoID" class="form-control employeePhotoID">
									<input type="hidden" name="prevIDImage" value="<?php echo $rs['employeePhotoID']; ?>" id="employeePhotoID" class="form-control employeePhotoID">
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="employeeID" value="<?php echo (int)$rs['employeeID']; ?>">
										<input type="hidden" name="staffTypeID" value="7">
										<input type="hidden" name="vendorID" value="0">
										<input type="hidden" name="employeeCode" value="<?php echo $rs['employeeCode']; ?>">
										<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(".gst").change(function() {
				var inputvalues = $(this).val();
				var regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
				if (!regex.test(inputvalues)) {
					$(".gst").val("");
					alert("invalid Aadhar no");
					return regex.test(inputvalues);
				}
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['employeeType'] = $_POST['employeeType'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');

		if (pro_db_perform('complexEmployeeMaster', $formdata)) {
			$employeeID = pro_db_insert_id();

			//dashboard log for security
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "employeemaster";
			$dashboardlogdata['subAction'] = "addsecurity";
			$dashboardlogdata['referenceID'] = $employeeID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (!empty($_FILES["employeeImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$employeeImage = $_FILES["employeeImage"]["name"];
				$image = explode(".", $employeeImage);
				$extension = end($image);

				if ($_FILES["employeeImage"]["error"] > 0) {
					$msg = $_FILES["employeeImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['employeeImage']['tmp_name']);
					$objectName = "employeeImage-" . $employeeID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "employeeID=" . $employeeID;
						$imageData['employeeImage'] = $finalImageName;
						if (pro_db_perform('complexEmployeeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["employeePhotoID"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$employeePhotoID = $_FILES["employeePhotoID"]["name"];
				$image = explode(".", $employeePhotoID);
				$extension = end($image);

				if ($_FILES["employeePhotoID"]["error"] > 0) {
					$msg = $_FILES["employeePhotoID"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['employeePhotoID']['tmp_name']);
					$objectName = "employeePhotoID-" . $employeeID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->staffMediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "employeeID=" . $employeeID;
						$imageData['employeePhotoID'] = $finalImageName;
						if (pro_db_perform('complexEmployeeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$msg = '<p class="bg-success p-3">Employee Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Employee Detail is not saved!!!!!!</p>';
		}
		$msg = '<p class="bg-success text-white p-3">Society Employee is created...<br><strong>Name:</strong> ' . $formdata['employeeName'] . '<br><strong>Employee Code:</strong> ' . $formdata['employeeCode'] . '<br></p>';
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$employeeID = $_POST['employeeID'];
		$whr = "employeeID=" . $employeeID;
		$formdata = $_POST;
		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['employeeName']));

		$formdata['username'] = $_SESSION['username'];
		$formdata['employeeType'] = $_POST['employeeType'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');

		unset($formdata['prevImage']);
		unset($formdata['prevIDImage']);

		if (pro_db_perform('complexEmployeeMaster', $formdata, 'update', $whr)) {

			//dashboard log for security
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "employeemaster";
			$dashboardlogdata['subAction'] = "editsecurity";
			$dashboardlogdata['referenceID'] = $_POST['employeeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (!empty($_FILES["employeeImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$employeeImage = $_FILES["employeeImage"]["name"];
				$image = explode(".", $employeeImage);
				$extension = end($image);

				if ($_FILES["employeeImage"]["error"] > 0) {
					$msg = $_FILES["employeeImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['employeeImage']['tmp_name']);
					$objectName = "employeeImage-" . $employeeID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;
					$imagebaseUrl = GCLOUD_CDN_URL . $this->mediaType . "/";

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "employeeID=" . $employeeID;
						$imageData['employeeImage'] = $finalImageName;

						$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevImage']);

						//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);
						if (pro_db_perform('complexEmployeeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["employeePhotoID"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$employeePhotoID = $_FILES["employeePhotoID"]["name"];
				$image = explode(".", $employeePhotoID);
				$extension = end($image);

				if ($_FILES["employeePhotoID"]["error"] > 0) {
					$msg = $_FILES["employeePhotoID"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['employeePhotoID']['tmp_name']);
					$objectName = "employeePhotoID-" . $employeeID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->staffMediaType . "/" . $objectName;
					$imagebaseUrl = GCLOUD_CDN_URL . $this->staffMediaType . "/";
					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "employeeID=" . $employeeID;
						$imageData['employeePhotoID'] = $finalImageName;
						$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevIDImage']);

						//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);
						if (pro_db_perform('complexEmployeeMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			$msg = '<p class="bg-success p-3">Security Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Security Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$employeeImage = getfldValue("complexEmployeeMaster", "employeeID", (int)$_GET['employeeID'], "employeeImage");
		$delsql = "Delete from complexEmployeeMaster where employeeID = " . (int)$_GET['employeeID'];
		if (pro_db_query($delsql)) {

			//dashboard log for security
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "employeemaster";
			$dashboardlogdata['subAction'] = "deletesecurity";
			$dashboardlogdata['referenceID'] = $_GET['employeeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			@unlink(DIR_FS_OURTEAM_PATH . $employeeImage);
			$msg = '<p class="bg-success p-3">Security Detail deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Security Detail Not deleted successfully</p>';
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
				<h4>Security Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Security</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="employeeList" width="100%">
								<thead>
									<tr>
										<th>Image</th>
										<th width="20%">Name</th>
										<th>Mobile Number</th>
										<th>ID Proof Type</th>
										<th>ID Proof Number</th>
										<th>Security Code</th>
										<th>Login Status</th>
										<th>QR Enrolled</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Image</th>
										<th width="20%">Name</th>
										<th>Mobile Number</th>
										<th>ID Proof Type</th>
										<th>ID Proof Number</th>
										<th>Security Code</th>
										<th>Login Status</th>
										<th>QR Enrolled</th>
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
			var listURL = 'helperfunc/securityList.php';
			$('#employeeList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 25
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "complexEmployeeMaster"
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
				selector: 'a.eqrEnrolled',
				params: {
					"tblName": "complexEmployeeMaster"
				},
				source: [{
					value: '1',
					text: 'Enroll'
				}, {
					value: '0',
					text: 'Not Enroll'
				}]
			});
			$('.table').editable({
				selector: 'a.eisLoggedIn',
				params: {
					"tblName": "complexEmployeeMaster"
				},
				source: [{
					value: '0',
					text: 'Logout'
				}]
			});
		</script>
<?php
	}
}
?>
