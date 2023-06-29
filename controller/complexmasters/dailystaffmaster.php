<?php
class dailystaffmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $addflatformaction;
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
		$this->editFormPersonalResourceAction = $this->redirectUrl . "&subaction=editFormPersonalResourceAction";
		$this->addflatformaction = $this->redirectUrl . "&subaction=addflats";

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
		$status = generateStaticOptions(array("1" => "Active", "0" => "Pending"));
		$isComplexResource = generateStaticOptions(array("1" => "Daily Resource", "2" => "Complex Resource", "3" => "Complex Vendor"));
		$staffTypeID = generateOptions(getMasterArray('staffTypeMaster', 'staffTypeID', 'staffTypeTitle'));
		$sql = pro_db_query("select staffTypeID, staffTypeTitle from staffTypeMaster where isComplexResource = 0");
		if (pro_db_num_rows($sql) > 0) {
			$staffType = "";
			$brs = pro_db_fetch_arrays($sql);
			for ($i = 0; $i < count($brs); $i++) {
				$staffType .= '<option value="' . $brs[$i]['staffTypeID'] . '">' . $brs[$i]['staffTypeTitle'] . '</option>';
			}
		}
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Resource Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Complex Resource:</label>
										<select class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="isComplexResource" data-target-list="staffTypeID" data-target-url="ajax/fetchSocietyResources.php" data-target-title="Select Staff Type">
											<?php echo $isComplexResource; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Staff Type:</label>
										<select name="staffTypeID" id="staffTypeID" class="form-control custom-select mr-sm-2" required>
											<?php echo $staffType; ?>
										</select>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="staffName" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Address:</label>
									<input type="text" name="staffResideAddress" class="form-control" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Mobile No:</label>
									<input type="text" name="staffMobileNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Phone No:</label>
									<input type="text" name="staffPhoneNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Email Address:</label>
									<input type="email" name="staffEmailAddress" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Qualification:</label>
									<input type="text" name="staffQualification" class="form-control" placeholder="">
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Resource Image:</label>
										<input type="file" accept="image/*" name="staffImage" id="staffImage" class="form-control staffImage">
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Type:</label>
									<select name="staffIDType" class="form-control custom-select mr-sm-2">
										<option value="1">Adhar Card</option>
										<option value="2">Driving License</option>
										<option value="3">PAN Card</option>
										<option value="4">Voter ID</option>
										<option value="5">Leaving Certificate</option>
										<option value="10">Other</option>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Value:</label>
									<input type="text" name="staffIDValue" class="form-control" placeholder="" required>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>ID Photocopy:</label>
										<input type="file" accept="image/*" name="staffPhotoID" id="staffPhotoID" class="form-control staffPhotoID">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
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
		$sql = "select dsm.*, stm.staffTypeTitle, stm.isComplexResource from dailyStaffMaster dsm, staffTypeMaster stm 
				where dsm.staffTypeID = stm.staffTypeID and dsm.dailyStaffID = " . (int)$_REQUEST['dailyStaffID'];
		$qry = pro_db_query($sql);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Accept", "0" => "Pending", "2" => "Reject"), $rs['status']);
		$isComplexResource = generateStaticOptions(array("0" => "No", "1" => "Yes"), $rs['isComplexResource']);
		$staffTypeID = generateOptions(getMasterArray('staffTypeMaster', 'staffTypeID', 'staffTypeTitle'), $rs['staffTypeID']);

		$sql = pro_db_query("select staffTypeID, staffTypeTitle from staffTypeMaster where isComplexResource = " . $rs['isComplexResource']);
		if (pro_db_num_rows($sql) > 0) {
			$staffType = "";
			$brs = pro_db_fetch_arrays($sql);
			for ($i = 0; $i < count($brs); $i++) {
				if ($brs[$i]['staffTypeID'] == $rs['staffTypeID'])
					$staffType .= '<option value="' . $brs[$i]['staffTypeID'] . '" selected>' . $brs[$i]['staffTypeTitle'] . '</option>';
				else
					$staffType .= '<option value="' . $brs[$i]['staffTypeID'] . '">' . $brs[$i]['staffTypeTitle'] . '</option>';
			}
		}
		$dailyStaffIDType = generateStaticOptions(array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID", "5" => "Leaving Certificate", "10" => "Other"), $rs['staffIDType']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Resource Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Complex Resource:</label>
										<select class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="isComplexResource" data-target-list="staffTypeID" data-target-url="ajax/fetchSocietyResources.php" data-target-title="Select Staff Type">
											<?php echo $isComplexResource; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Staff Type:</label>
										<select name="staffTypeID" id="staffTypeID" class="form-control custom-select mr-sm-2" required>
											<?php echo $staffType; ?>
										</select>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="staffName" class="form-control" value="<?php echo stripslashes($rs['staffName']); ?>" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Address:</label>
									<input type="text" name="staffResideAddress" class="form-control" value="<?php echo stripslashes($rs['staffResideAddress']); ?>" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Mobile No:</label>
									<input type="text" name="staffMobileNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['staffMobileNo']); ?>" placeholder="Employee Mobile No" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Phone No:</label>
									<input type="text" name="staffPhoneNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['staffPhoneNo']); ?>" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Email Address:</label>
									<input type="email" name="staffEmailAddress" class="form-control" value="<?php echo stripslashes($rs['staffEmailAddress']); ?>" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Qualification:</label>
									<input type="text" name="staffQualification" class="form-control" value="<?php echo stripslashes($rs['staffQualification']); ?>" placeholder="">
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Resource Image:</label>
										<input type="file" accept="image/*" name="staffImage" id="staffImage" class="form-control staffImage">
										<input type="hidden" name="prevImage" value="<?php echo $rs['staffImage']; ?>" id="staffImage" class="form-control staffImage">
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Type:</label>
									<select name="staffIDType" class="form-control custom-select mr-sm-2">
										<?php echo $dailyStaffIDType; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>ID Number:</label>
									<input type="text" name="staffIDValue" class="form-control" value="<?php echo stripslashes($rs['staffIDValue']); ?>" placeholder="" required>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>ID Photocopy:</label>
										<input type="file" accept="image/*" name="staffPhotoID" id="staffPhotoID" class="form-control staffPhotoID">
										<input type="hidden" name="prevIDImage" value="<?php echo $rs['staffPhotoID']; ?>" id="staffPhotoID" class="form-control staffPhotoID">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="dailyStaffID" value="<?php echo (int)$rs['dailyStaffID']; ?>">
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

	public function editFormForPersonalResource()
	{
		$sql = "select * from dailyStaffMaster where dailyStaffID = " . (int)$_REQUEST['dailyStaffID'];
		$qry = pro_db_query($sql);
		$rs = pro_db_fetch_array($qry);
		$dailyStaffIDTypes = array("1" => "Adhar Card", "2" => "Driving License", "3" => "PAN Card", "4" => "Voter ID", "5" => "Leaving Certificate", "10" => "Other");
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Resource Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editFormPersonalResourceAction; ?>" method="post">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Name:</label>
									<input type="text" name="staffName" class="form-control" value="<?php echo stripslashes($rs['staffName']); ?>" placeholder="" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Contact Number:</label>
									<input type="text" class="form-control" value="<?php echo stripslashes($rs['staffMobileNo']); ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Profession:</label>
									<input type="text" name="staffProfession" class="form-control" value="<?php echo stripslashes($rs['staffProfession']); ?>" placeholder="Enter Profession" required>
								</div>
								<div class="form-group col-sm-2">
									<label>ID Type:</label>
									<input type="text" class="form-control" value="<?php echo $dailyStaffIDTypes[$rs['staffIDType']]; ?>" readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>ID Number:</label>
									<input type="text" class="form-control" value="<?php echo stripslashes($rs['staffIDValue']); ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="dailyStaffID" value="<?php echo (int)$rs['dailyStaffID']; ?>">
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
		$formdata['status'] = 1;

		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('dailyStaffMaster', $formdata)) {
			$dailyStaffID = pro_db_insert_id();

			//dashboard log for dailystaffmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "dailystaffmaster";
			$dashboardlogdata['subAction'] = "adddailystaff";
			$dashboardlogdata['referenceID'] = $dailyStaffID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (!empty($_FILES["staffImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$staffImage = $_FILES["staffImage"]["name"];
				$image = explode(".", $staffImage);
				$extension = end($image);

				if ($_FILES["staffImage"]["error"] > 0) {
					$msg = $_FILES["staffImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['staffImage']['tmp_name']);
					$objectName = "staffImage-" . $dailyStaffID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "dailyStaffID=" . $dailyStaffID;
						$imageData['staffImage'] = $finalImageName;
						if (pro_db_perform('dailyStaffMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["staffPhotoID"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$staffPhotoID = $_FILES["staffPhotoID"]["name"];
				$image = explode(".", $staffPhotoID);
				$extension = end($image);

				if ($_FILES["staffPhotoID"]["error"] > 0) {
					$msg = $_FILES["staffPhotoID"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['staffPhotoID']['tmp_name']);
					$objectName = "staffPhotoID-" . $dailyStaffID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->staffMediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "dailyStaffID=" . $dailyStaffID;
						$imageData['staffPhotoID'] = $finalImageName;
						if (pro_db_perform('dailyStaffMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$msg = '<p class="bg-success p-3">Staff Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Staff Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$dailyStaffID = $_POST['dailyStaffID'];
		$whr = "dailyStaffID=" . $dailyStaffID;
		$formdata = $_POST;
		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['staffName']));

		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');

		unset($formdata['prevImage']);
		unset($formdata['prevIDImage']);
		unset($formdata['memberID']);

		if (pro_db_perform('dailyStaffMaster', $formdata, 'update', $whr)) {

			//dashboard log for dailystaffmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "dailystaffmaster";
			$dashboardlogdata['subAction'] = "editdailystaff";
			$dashboardlogdata['referenceID'] = $_POST['dailyStaffID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if (!empty($_FILES["staffImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$staffImage = $_FILES["staffImage"]["name"];
				$image = explode(".", $staffImage);
				$extension = end($image);

				if ($_FILES["staffImage"]["error"] > 0) {
					$msg = $_FILES["staffImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['staffImage']['tmp_name']);
					$objectName = "staffImage-" . $dailyStaffID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;
					$imagebaseUrl = GCLOUD_CDN_URL . $this->mediaType . "/";

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "dailyStaffID=" . $dailyStaffID;
						$imageData['staffImage'] = $finalImageName;

						$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevImage']);

						//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);
						if (pro_db_perform('dailyStaffMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			if (!empty($_FILES["staffPhotoID"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$staffPhotoID = $_FILES["staffPhotoID"]["name"];
				$image = explode(".", $staffPhotoID);
				$extension = end($image);

				if ($_FILES["staffPhotoID"]["error"] > 0) {
					$msg = $_FILES["staffPhotoID"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['staffPhotoID']['tmp_name']);
					$objectName = "staffPhotoID-" . $dailyStaffID . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->staffMediaType . "/" . $objectName;
					$imagebaseUrl = GCLOUD_CDN_URL . $this->staffMediaType . "/";
					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "dailyStaffID=" . $dailyStaffID;
						$imageData['staffPhotoID'] = $finalImageName;
						$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevIDImage']);

						//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);
						if (pro_db_perform('dailyStaffMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}

			$msg = '<p class="bg-success p-3">Staff Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Staff Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function editFormPersonalResourceAction()
	{
		global $frmMsgDialog;
		$whr = "";
		$dailyStaffID = $_POST['dailyStaffID'];
		$whr = "dailyStaffID=" . $dailyStaffID;
		$formdata = $_POST;
		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['staffName']));

		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');

		if (pro_db_perform('dailyStaffMaster', $formdata, 'update', $whr)) {
			//dashboard log for dailystaffmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "dailystaffmaster";
			$dashboardlogdata['subAction'] = "editpersonalstaff";
			$dashboardlogdata['referenceID'] = $whr;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Staff Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Staff Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$staffImage = getfldValue("dailyStaffMaster", "dailyStaffID", (int)$_GET['dailyStaffID'], "staffImage");
		$delsql = "Delete from dailyStaffMaster where dailyStaffID = " . (int)$_GET['dailyStaffID'];
		if (pro_db_query($delsql)) {

			//dashboard log for dailystaffmaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "dailystaffmaster";
			$dashboardlogdata['subAction'] = "deletedailystaff";
			$dashboardlogdata['referenceID'] = $_GET['dailyStaffID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			
			@unlink(DIR_FS_OURTEAM_PATH . $staffImage);
			$msg = '<p class="bg-success p-3">Staff Detail deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Staff Detail Not deleted successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function addflats()
	{
		global $frmMsgDialog;
		$createdate = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];

		foreach ($_POST['memberID'] as $key => $value) {
			$insSql = pro_db_query("
					insert into dailyStaffRelation set
					staffID = '" . $_POST['dailyStaffID'] . "',
					memberID = '" . $value . "',
					createdate = '" . $createdate . "' ,
					modifieddate = '" . $modifieddate . "' ,
					remote_ip = '" . $remote_ip . "' ,
					status = 1
				");
			$i = 1;
		}

		if ($i == 1) {
			$msg = '<p class="bg-success p-3">Staff has been Assigned to member successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Staff Detail has not been Assigned to member successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function assignFlats()
	{
		global $frmMsgDialog;
		$createdate = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];

		if (isset($_POST['assignFlats'])) {
			$selectedDailyStaffID = $_POST['dailyStaffID'];

			$queryRemoveExisting = pro_db_query("Delete from dailyStaffRelation where staffID = " . $selectedDailyStaffID);
			if ($queryRemoveExisting) {

				if (isset($_POST['officeselection'])) {
					$selectedOffices = $_POST['officeselection'];

					foreach ($selectedOffices as $office) {
						$arrCheckboxAction = explode("_", $office);
						$officeID = $arrCheckboxAction[0];
						$officeAction = $arrCheckboxAction[1];

						//Insert New Entry
						$formdata = array(
							"staffID" => $_POST['dailyStaffID'],
							"officeID" => $officeID,
							"createdate" => $createdate,
							"modifieddate" => $modifieddate,
							"remote_ip" => $remote_ip,
							"status" => $officeAction
						);
						pro_db_perform('dailyStaffRelation', $formdata);

						$relationID = pro_db_insert_id();
						//dashboard log for dailystaffmaster
						$dashboardlogdata = array();
						$dashboardlogdata['complexID'] = $_SESSION['complexID'];
						$dashboardlogdata['memberID'] = $_SESSION['memberID'];
						$dashboardlogdata['contorller'] = "complexmasters";
						$dashboardlogdata['action'] = "dailystaffmaster";
						$dashboardlogdata['subAction'] = "adddailystaffrelation";
						$dashboardlogdata['referenceID'] = $relationID;
						$dashboardlogdata['status'] = 1;
						$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
						$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
						$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
						pro_db_perform('dashboardLogMaster', $dashboardlogdata);
					}
				}
			}
			$msg = '<p class="bg-success p-3">Staff has been assigned to members successfully...</p>';
			$rUrl = $this->redirectUrl . "&subaction=listData";
			echo sprintf($frmMsgDialog, $rUrl, $msg);
		}
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Staff Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Staff</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="btn-group" role="group" aria-label="Staff Type">
							<button type="button" class="btn btn-info fltStaffType" data-stafftype="1" data-issociety="2">Complex Resources</button>

							<button type="button" class="btn btn-warning fltStaffType" data-stafftype="1" data-issociety="1">Daily Resources</button>

							<button type="button" class="btn btn-danger fltStaffType" data-stafftype="1" data-issociety="3">Complex Vendors</button>
						</div>
						<hr>
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="dailystaffmasterList" width="100%">
								<thead>
									<tr>
										<th>Image</th>
										<th width="20%">Name</th>
										<th>Contact</th>
										<th width="15%">Profession</th>
										<th>ID Proof Type</th>
										<th>ID Proof Number</th>
										<th>Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Image</th>
										<th width="20%">Name</th>
										<th>Contact</th>
										<th width="15%">Profession</th>
										<th>ID Proof Type</th>
										<th>ID Proof Number</th>
										<th>Status</th>
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
			var isSocietyResource = "1";
			var staffTypeID = "1";
			var listURL = "helperfunc/dailystaffmasterList.php?isSocietyResource=" + isSocietyResource + "&staffTypeID=" + staffTypeID;
			if ($("#dailystaffmasterList").length > 0) {
				var table = $('#dailystaffmasterList').dataTable({
					"ajax": listURL,
					"deferRender": false,
					"iDisplayLength": 50,
					"order": []
				});
			}
			$(document).on('click', 'button.fltStaffType', function(e) {
				staffTypeID = $(this).data('stafftype');
				isSocietyResource = $(this).data('issociety');
				var listURL = "helperfunc/dailystaffmasterList.php?isSocietyResource=" + isSocietyResource + "&staffTypeID=" + staffTypeID;
				table.api().ajax.url(listURL).load();
				table.fnDraw();
			});

			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "dailyStaffMaster"
				},
				source: [{
					value: '1',
					text: 'Accept'
				}, {
					value: '0',
					text: 'Pending'
				}, {
					value: '2',
					text: 'Reject'
				}]
			});
		</script>
	<?php

	}
	public function viewRelation()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4><?php echo ucfirst($_REQUEST['staffName']); ?></h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Daily Staff Master</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="dailystaffrelationList" width="100%">
								<thead>
									<tr>
										<th width="10%">Image</th>
										<th width="20%">Office Name</th>
										<th width="10%">Contact</th>
										<th>Office Details</th>
										<?php
										if ($_REQUEST['personalResource'] == 1) {
										?>
											<th width="15%">Staff Nick Name</th>
										<?php
										}
										?>
										<?php
										if ($_REQUEST['personalResource'] == 1) {
										?>
											<th width="15%">Valid Upto</th>
										<?php
										}
										?>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="10%">Image</th>
										<th width="20%">Office Name</th>
										<th width="10%">Contact</th>
										<th>Office Details</th>
										<?php
										if ($_REQUEST['personalResource'] == 1) {
										?>
											<th width="15%">Staff Nick Name</th>
										<?php
										}
										?>
										<?php
										if ($_REQUEST['personalResource'] == 1) {
										?>
											<th width="15%">Valid Upto</th>
										<?php
										}
										?>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/dailystaffrelationList.php?dailyStaffID=<?php echo $_REQUEST["dailyStaffID"]; ?>';
			$('#dailystaffrelationList').dataTable({
				"ajax": listURL,
				"stateSave": true,
				"deferRender": true,
				"iDisplayLength": 25
			});
		</script>
	<?php
	}

	public function assignToFlats()
	{
		$addflatformaction = $this->redirectUrl . "&subaction=addflats";
		$assignFlatsAction = $this->redirectUrl . "&subaction=assignFlats";
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'complexID = ' . $_SESSION['complexID']));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4><?php echo ucfirst($_REQUEST['staffName']); ?> : Manage Working Status</h4>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmassign" id="frm-example" action="<?php echo $assignFlatsAction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12 py-3">
									<button type="button" value="selectAll" id="checkall" class="main btn btn-reddit" onclick="checkAll()">Select All</button>
									<button type="button" value="deselectAll" class="main btn btn-secondary" onclick="uncheckAll()">Clear</button>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="table-responsive">
										<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="dailystaffAssignList" width="100%">
											<thead>
												<tr>
													<th width="10%">Office Name</th>
													<th width="10%">Image</th>
													<th>Member Name</th>
													<th>Contact Number</th>
													<th width="10%">Working</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
											<tfoot>
												<tr>
													<th width="10%">Office Name</th>
													<th width="10%">Image</th>
													<th>Member Name</th>
													<th>Contact Number</th>
													<th width="10%">Working</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div id="example-console-form"></div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="dailyStaffID" value="<?php echo (int)$_REQUEST['dailyStaffID']; ?>">
										<button type="submit" name="assignFlats" class="btn btn-success">Update</button>
										&nbsp;&nbsp;
										<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<script>
				var listURL = 'helperfunc/dailystaffAssignList.php?dailyStaffID=<?php echo $_REQUEST["dailyStaffID"]; ?>';
				$('#dailystaffAssignList').dataTable({
					dom: 'frtip',
					"ajax": listURL,
					"deferRender": true,
					"stateSave": true,
					"iDisplayLength": -1
				});
			</script>

			<script type="text/javascript">
				// Select all check boxes : Setting the checked property to true in checkAll() function
				function checkAll() {
					var items = document.getElementsByName('memberselection[]');
					for (var i = 0; i < items.length; i++) {
						if (items[i].type == 'checkbox')
							items[i].checked = true;
					}
				}
				// Clear all check boxes : Setting the checked property to false in uncheckAll() function
				function uncheckAll() {
					var items = document.getElementsByName('memberselection[]');
					for (var i = 0; i < items.length; i++) {
						if (items[i].type == 'checkbox')
							items[i].checked = false;
					}
				}
			</script>
	<?php
	}
}
	?>
