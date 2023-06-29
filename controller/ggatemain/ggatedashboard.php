<?php
class ggatedashboard
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $societypackageformaction;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->societypackageformaction = $this->redirectUrl . "&subaction=societyPackageMapping";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "masters";
		} else {
			$this->mediaType = "masters-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$Country = generateOptions(getMasterArray('countries', 'countries_id', 'countries_name'));
		$State = generateOptions(getMasterArray('zones', 'zone_id', 'zone_name'));
		$City = generateOptions(getMasterArray('cityMaster', 'cityID', 'cityName'));
		$packageID = generateOptions(
			getMasterList(
				'packageMaster',
				'packageID',
				'CONCAT(packageName, " (", packageNickName, ")")',
				'parentID != 0 and status = 1',
				'packageID'
			)
		);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Complex Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Complex Name:</label>
									<input type="text" name="complexName" class="form-control" required placeholder="Complex Name">
								</div>
								<div class="form-group col-sm-6">
									<label>Complex	 Address:</label>
									<input type="text" name="complexAddress" class="form-control" required placeholder="Complex Address">
								</div>
								<div class="form-group col-sm-3">
									<label>Contact No:</label>
									<input type="text" name="complexContactNo" required minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="Complex Contact No">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Contact Email:</label>
									<input type="email" name="complexEmail" class="form-control" placeholder="Complex Email">
								</div>
								<div class="form-group col-sm-2">
									<label>Max Blocks:</label>
									<input type="number" name="maxBlocks" min="0" step="1" class="form-control" placeholder="Max Blocks">
								</div>
								<div class="form-group col-sm-2">
									<label>Max Office:</label>
									<input type="number" name="maxProperties" min="0" step="1" class="form-control" placeholder="Max Office">
								</div>
								<div class="form-group col-sm-2">
									<label>Enrol Date:</label>
									<input type="text" name="enrolledDate" class="form-control enrolledDate" placeholder="Enrol Date" value="<?php echo date('Y-m-d'); ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Valid Upto:</label>
									<input type="text" name="validUptoDate" class="form-control validUptoDate" placeholder="Valid Upto" value="<?php echo date('Y-m-d'); ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Country:</label>
									<select name="countries_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="countries_id" data-target-list="zone_id" data-target-url="ajax/states.php" data-target-title="Select Country" required>
										<option value="" hidden>Select Country</option>
										<?php echo $Country; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>State:</label>
									<select name="zone_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="zone_id" data-target-list="city_id" data-target-url="ajax/city.php" data-target-title="Select City" required>
										<option value="" hidden>Select State</option>
										<?php echo $State; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>City:</label>
									<select name="city_id" class="form-control custom-select mr-sm-2" id="city_id" required>
										<option value="" hidden>Select City</option>
										<?php echo $City; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Allow to Change Preferences:</label>
									<select name="allowChangePreferences" class="form-control custom-select mr-sm-2">
										<option value="0">Not allowed</option>
										<option value="1">Allowed</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Complex Logo:</label>
									<input type="file" name="complexLogo" id="complexLogo" class="form-control complexLogo">
								</div>
								<div class="form-group col-sm-3">
									<label>App Launch Image (While Labeling):</label>
									<input type="file" name="complexSplashImage" id="complexSplashImage" class="form-control complexSplashImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Complex Package:</label>
									<select name="packageID" id="packageID" class="form-control custom-select mr-sm-2" required>
										<option value="">Select Package</option>
										<?php echo $packageID; ?>
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
								<div class="form-group col-sm-12">
									<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			$('.enrolledDate').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
			$('.validUptoDate').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from complexMaster where complexID = " . (int)$_REQUEST['complexID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "126" => "Disable"), $rs['status']);
		$Country = generateOptions(getMasterList('countries', 'countries_id', 'countries_name'), $rs['countries_id']);
		$State = generateOptions(getMasterList('zones', 'zone_id', 'zone_name'), $rs['zone_id']);
		$City = generateOptions(getMasterList('cityMaster', 'cityID', 'cityName'), $rs['city_id']);
		$changePreference = generateStaticOptions(array("1" => "Allowed", "0" => "Not allowed"), $rs['allowChangePreferences']);
		$packageID = generateOptions(
			getMasterList(
				'packageMaster',
				'packageID',
				'CONCAT(packageName, " (", packageNickName, ")")',
				'parentID != 0 and status = 1',
				'packageID'
			),
			$rs['packageID']
		);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Complex Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Complex Name:</label>
									<input type="text" name="complexName" class="form-control" value="<?php echo $rs['complexName']; ?>" placeholder="Complex Name" required>
								</div>
								<div class="form-group col-sm-6">
									<label>Complex Address:</label>
									<input type="text" name="complexAddress" class="form-control" value="<?php echo $rs['complexAddress']; ?>" placeholder="Complex Address" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Contact No:</label>
									<input type="text" name="complexContactNo" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo $rs['complexContactNo']; ?>" placeholder="Complex ContactNo" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Contact Email:</label>
									<input type="email" name="complexEmail" class="form-control" value="<?php echo $rs['complexEmail']; ?>" placeholder="Complex Email">
								</div>
								<div class="form-group col-sm-2">
									<label>Max Blocks:</label>
									<input type="number" name="maxBlocks" min="0" step="1" class="form-control" value="<?php echo $rs['maxBlocks']; ?>" placeholder="Max Blocks">
								</div>
								<div class="form-group col-sm-2">
									<label>Max Office:</label>
									<input type="number" name="maxProperties" min="0" step="1" class="form-control" value="<?php echo $rs['maxProperties']; ?>" placeholder="Max Office">
								</div>
								<div class="form-group col-sm-2">
									<label>Enrol Date:</label>
									<input type="text" name="enrolledDate" class="form-control enrolledDate" placeholder="Enrol Date" value="<?php echo $rs['enrolledDate']; ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Valid Upto:</label>
									<input type="text" name="validUptoDate" class="form-control validUptoDate" placeholder="Valid Upto" value="<?php echo $rs['validUptoDate']; ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Country:</label>
									<select name="countries_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="countries_id" data-target-list="zone_id" data-target-url="ajax/states.php" data-target-title="Select Country" required>
										<option value="" hidden>Select Country</option>
										<?php echo $Country; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>State:</label>
									<select name="zone_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="zone_id" data-target-list="city_id" data-target-url="ajax/city.php" data-target-title="Select City" required>
										<option value="" hidden>Select State</option>
										<?php echo $State; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>City:</label>
									<select name="city_id" class="form-control custom-select mr-sm-2" id="city_id" required>
										<option value="" hidden>Select City</option>
										<?php echo $City; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Allow to Change Preferences:</label>
									<select name="allowChangePreferences" class="form-control custom-select mr-sm-2">
										<?php echo $changePreference; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Complex Logo:</label>
									<input type="file" name="complexLogo" id="complexLogo" class="form-control complexLogo">
								</div>
								<div class="form-group col-sm-3">
									<label>App Launch Image (While Labeling):</label>
									<input type="file" name="complexSplashImage" id="complexSplashImage" class="form-control complexSplashImage">
								</div>
								<div class="form-group col-sm-3">
									<label>Complex Package:</label>
									<select name="packageID" id="packageID" class="form-control custom-select mr-sm-2" required>
										<option value="">Select Package</option>
										<?php echo $packageID; ?>
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
									<input type="hidden" name="complexID" value="<?php echo (int)$rs['complexID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			$('.enrolledDate').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
			$('.validUptoDate').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
		</script>
	<?php
	}

	public function complexPackageForm()
	{
		$sql = pro_db_query("select packageID, complexID from complexMaster where complexID = " . $_REQUEST['complexID']);
		$rs = pro_db_fetch_array($sql);
		$packageID = generateOptions(
			getMasterList(
				'packageMaster',
				'packageID',
				'CONCAT(packageName, " (", packageNickName, ")")',
				'parentID != 0 and status = 1',
				'packageID'
			),
			$rs['packageID']
		);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Assign Complex Package</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->societypackageformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Package Included:</label>
									<select name="packageID" id="packageID" class="form-control custom-select mr-sm-2" required>
										<option value="">Select from Below</option>
										<?php echo $packageID; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID" value="<?php echo (int)$rs['complexID']; ?>">
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

		//Fetch City Name
		$queryCityName = pro_db_query("select cityName from cityMaster where cityID = " . $_POST['city_id']);
		$resCityName = pro_db_fetch_array($queryCityName);
		$formdata['complexCity'] = $resCityName["cityName"] ?? "Complex City";

		//Manage Entry
		if (pro_db_perform('complexMaster', $formdata)) {
			$complexID = pro_db_insert_id();

			$queryPackages = pro_db_query("select concat(packageID, ', ' , parentID) as packages from packageMaster
											where packageID = " . $_POST['packageID']);
			$objPackages = pro_db_fetch_array($queryPackages);
			if ($objPackages != null) {
				$queryServices = pro_db_query("select pm.packageID from packageMaster pm
											join packageModuleMaster pmm on pm.packageID = pmm.packageID
											join moduleMaster mm on mm.moduleID = pmm.moduleID
											where mm.moduleFile in ('otpservice', 'obdservice')
											and pm.packageID in (" . $objPackages['packages'] . ")");
				$rowsServices = pro_db_num_rows($queryServices);
				//Delete existing
				pro_db_query("delete from complexSubscription where complexID = " . $complexID);

				if ($rowsServices > 0) {
					//Assign for Society
					$smsdata = array();
					$smsdata['complexID'] = $complexID;
					$smsdata['smsEnrolled'] = 1;
					$smsdata['obdEnrolled'] = 1;
					$smsdata['createdate'] = date('Y-m-d H:i:s');
					$smsdata['modifieddate'] = date('Y-m-d H:i:s');
					$smsdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$smsdata['status'] = 1;
					pro_db_perform('complexSubscription', $smsdata);
				}
			}

			//Society Logo
			$arrImageData = array();
			if (!empty($_FILES["complexLogo"]["name"])) {
				if ($_FILES["complexLogo"]["error"] == 0) {
					$complexLogo = $_FILES["complexLogo"]["name"];
					$logoImage = explode(".", $complexLogo);
					$logoExtension = end($logoImage);

					$complexLogoRawData = file_get_contents($_FILES['complexLogo']['tmp_name']);
					$logoObjectName = "complex_logo_" . $complexID . "." . $logoExtension;
					$logoImageName = $this->mediaType . "/" . $logoObjectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $complexLogoRawData, $logoImageName)) {
						$finalImageName = GCLOUD_CDN_URL . $logoImageName;
						$arrImageData['complexLogo'] = $finalImageName;
					}
				}
			}
			if (!empty($_FILES["complexSplashImage"]["name"])) {
				if ($_FILES["complexSplashImage"]["error"] == 0) {
					$complexSplashImage = $_FILES["complexSplashImage"]["name"];
					$splashImage = explode(".", $complexSplashImage);
					$splashExtension = end($splashImage);

					$splashImageRawData = file_get_contents($_FILES['complexSplashImage']['tmp_name']);
					$splashImageObjectName = "complex_splash_" . $complexID . "." . $splashExtension;
					$splashImageName = $this->mediaType . "/" . $splashImageObjectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $splashImageRawData, $splashImageName)) {
						$finalImageName = GCLOUD_CDN_URL . $splashImageName;
						$arrImageData['complexSplashImage'] = $finalImageName;
					}
				}
			}
			//Update Society Images into societyMaster
			if (count($arrImageData) > 0) {
				pro_db_perform('complexMaster', $arrImageData, 'update', "complexID = " . $complexID);
			}

			//Manage Entry in Login Master
			$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['complexName']));
			$logindata['groupID'] = 5;
			$logindata['status'] = 'E';
			$logindata['memberID'] = 0;
			$logindata['complexID'] = $complexID;
			$logindata['userName'] = $slug;
			$logindata['loginID'] = "GGATE" . $complexID;
			$logindata['userEmail'] = $formdata['societyEmail'];
			$logindata['userMobile'] = $formdata['societyContactNo'];
			$userPass = genPassword(12);
			$logindata['userPwd'] = hash('sha256', $logindata['loginID'] . $userPass);

			if (pro_db_perform('loginMaster', $logindata)) {
				$msg = '<p class="bg-success text-white p-3">Complex is created successfully...<br><strong>Username:</strong> ' . $logindata['loginID'] . '<br><strong>Password:</strong> ' . $userPass . '<br></p>';
			} else {
				$msg = '<p class="bg-success p-3">Complex Details is saved successfully...</p>';
			}
		} else {
			$msg = '<p class="bg-danger p-3">Complex Details is not saved!!!!!!</p>';
		}
		echo $msg;
		echo '<br><a href="' . $this->redirectUrl . '" class="btn btn-info">Back to Complex Management</a>';

		// $rUrl = $this->redirectUrl . "&subaction=listData";
		// echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$complexID = $_POST['complexID'];
		$formdata = $_POST;

		//Fetch City Name
		$queryCityName = pro_db_query("select cityName from cityMaster where cityID = " . $_POST['city_id']);
		$resCityName = pro_db_fetch_array($queryCityName);
		$formdata['complexCity'] = $resCityName["cityName"] ?? "Complex City";

		//Society Logo
		$arrImageData = array();
		if (!empty($_FILES["complexLogo"]["name"])) {
			if ($_FILES["complexLogo"]["error"] == 0) {
				$complexLogo = $_FILES["complexLogo"]["name"];
				$logoImage = explode(".", $complexLogo);
				$logoExtension = end($logoImage);

				$complexLogoRawData = file_get_contents($_FILES['complexLogo']['tmp_name']);
				$logoObjectName = "complex_logo_" . $complexID . "." . $logoExtension;
				$logoImageName = $this->mediaType . "/" . $logoObjectName;

				//Upload a file to the bucket.
				if (gcsUploadFile(GCLOUD_BUCKET, $complexLogoRawData, $logoImageName)) {
					$finalImageName = GCLOUD_CDN_URL . $logoImageName;
					$arrImageData['complexLogo'] = $finalImageName;
				}
			}
		}
		if (!empty($_FILES["complexSplashImage"]["name"])) {
			if ($_FILES["complexSplashImage"]["error"] == 0) {
				$complexSplashImage = $_FILES["complexSplashImage"]["name"];
				$splashImage = explode(".", $complexSplashImage);
				$splashExtension = end($splashImage);

				$splashImageRawData = file_get_contents($_FILES['complexSplashImage']['tmp_name']);
				$splashImageObjectName = "complex_splash_" . $complexID . "." . $splashExtension;
				$splashImageName = $this->mediaType . "/" . $splashImageObjectName;

				//Upload a file to the bucket.
				if (gcsUploadFile(GCLOUD_BUCKET, $splashImageRawData, $splashImageName)) {
					$finalImageName = GCLOUD_CDN_URL . $splashImageName;
					$arrImageData['complexSplashImage'] = $finalImageName;
				}
			}
		}
		//Update Society Images into societyMaster
		if (count($arrImageData) > 0) {
			pro_db_perform('complexMaster', $arrImageData, 'update', "complexID = " . $complexID);
		}
		
		//Default Params
		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		

		if (pro_db_perform('complexMaster', $formdata, 'update', "complexID = " . $complexID)) {
			$queryPackages = pro_db_query("select concat(packageID, ', ' , parentID) as packages from packageMaster
											where packageID = " . $_POST['packageID']);
			$objPackages = pro_db_fetch_array($queryPackages);
			if ($objPackages != null) {
				$queryServices = pro_db_query("select pm.packageID from packageMaster pm
											join packageModuleMaster pmm on pm.packageID = pmm.packageID
											join moduleMaster mm on mm.moduleID = pmm.moduleID
											where mm.moduleFile in ('otpservice', 'obdservice')
											and pm.packageID in (" . $objPackages['packages'] . ")");
				$rowsServices = pro_db_num_rows($queryServices);
				//Delete existing
				pro_db_query("delete from complexSubscription where complexID = " . $_POST['complexID']);

				if ($rowsServices > 0) {
					//Assign for Society
					$smsdata = array();
					$smsdata['complexID'] = $_POST['complexID'];
					$smsdata['smsEnrolled'] = 1;
					$smsdata['obdEnrolled'] = 1;
					$smsdata['createdate'] = date('Y-m-d H:i:s');
					$smsdata['modifieddate'] = date('Y-m-d H:i:s');
					$smsdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$smsdata['status'] = 1;
					pro_db_perform('complexSubscription', $smsdata);
				}
			}
			$msg = '<p class="bg-success p-3">Complex Details are updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Complex Details are not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function complexPackageMapping()
	{
		global $frmMsgDialog;
		$complexID = $_POST['complexID'];
		$formdata['packageID'] = $_POST['packageID'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('complexMaster', $formdata, 'update', "complexID = " . $complexID)) {

			$queryPackages = pro_db_query("select concat(packageID, ', ' , parentID) as packages from packageMaster
											where packageID = " . $_POST['packageID']);
			$objPackages = pro_db_fetch_array($queryPackages);
			if ($objPackages != null) {
				$queryServices = pro_db_query("select pm.packageID from packageMaster pm
											join packageModuleMaster pmm on pm.packageID = pmm.packageID
											join moduleMaster mm on mm.moduleID = pmm.moduleID
											where mm.moduleFile in ('otpservice', 'obdservice')
											and pm.packageID in (" . $objPackages['packages'] . ")");
				$rowsServices = pro_db_num_rows($queryServices);
				//Delete existing
				pro_db_query("delete from complexSubscription where complexID = " . $_POST['complexID']);

				if ($rowsServices > 0) {
					//Assign for Society
					$smsdata = array();
					$smsdata['complexID'] = $_POST['complexID'];
					$smsdata['smsEnrolled'] = 1;
					$smsdata['obdEnrolled'] = 1;
					$smsdata['createdate'] = date('Y-m-d H:i:s');
					$smsdata['modifieddate'] = date('Y-m-d H:i:s');
					$smsdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$smsdata['status'] = 1;
					pro_db_perform('complexSubscription', $smsdata);
				}
			}
			$msg = '<p class="bg-success p-3">Complex package Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Complex package Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		//Total complexes
		$qryComplex = pro_db_query("select count(complexID) as complexes from complexMaster where status = 1");
		$rsComplex = pro_db_fetch_array($qryComplex);
		$totalcomplexes = $rsComplex['complexes'];

		//Total GGATE Vendors
		$qryVendors = pro_db_query("select count(vendorID) as vendors from vendorMaster where status = 1");
		$rsVendors = pro_db_fetch_array($qryVendors);
		$totalVendors = $rsVendors['vendors'];

		//Total GGATE Members
		$qryMembers = pro_db_query("select count(memberID) as members from memberMaster where status = 1");
		$rsMembers = pro_db_fetch_array($qryMembers);
		$totalMembers = $rsMembers['members'];

		//Total Active Members
		$qryActiveMembers = pro_db_query("select count(memberID) as members from memberActivity where status = 4");
		$rsActiveMembers = pro_db_fetch_array($qryActiveMembers);
		$totalActiveMembers = $rsActiveMembers['members'];

		//Total Daily Active Members
		$qryDailyActiveMembers = pro_db_query("select count(memberID) as members from memberActivity
								where DATE(dashboardDatetime) = DATE(CURRENT_DATE) and status = 4");
		$rsDailyActiveMembers = pro_db_fetch_array($qryDailyActiveMembers);
		$totalDailyActiveMembers = $rsDailyActiveMembers['members'];
		//Total Daily Active Members - Previous Day
		$qryDailyActiveMembersPrevious = pro_db_query("select count(memberID) as members from memberActivity
										where DATE(dashboardDatetime) = DATE(CURRENT_DATE - INTERVAL 1 DAY) and status = 4");
		$rsDailyActiveMembersPrevious = pro_db_fetch_array($qryDailyActiveMembersPrevious);
		$totalDailyActiveMembersPrevious = $rsDailyActiveMembersPrevious['members'];

		//Display Difference
		$diffDaily = $totalDailyActiveMembers - $totalDailyActiveMembersPrevious;
		if ($diffDaily > 0) {
			$diffDaily = "+" . $diffDaily;
		}
		$diffDailyDisplay = " (" . $diffDaily . ")";

		//Total Monthly Active Members
		$qryMonthlyActiveMembers = pro_db_query("select count(memberID) as members from memberActivity
												where MONTH(dashboardDatetime) = MONTH(CURRENT_DATE) and status = 4");
		$rsMonthlyActiveMembers = pro_db_fetch_array($qryMonthlyActiveMembers);
		$totalMonthlyActiveMembers = $rsMonthlyActiveMembers['members'];
		//Total Monthly Active Members - Previous Month
		$qryMonthlyActiveMembersPrevious = pro_db_query("select count(memberID) as members from memberActivity
														where MONTH(dashboardDatetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and status = 4");
		$rsMonthlyActiveMembersPrevious = pro_db_fetch_array($qryMonthlyActiveMembersPrevious);
		$totalMonthlyActiveMembersPrevious = $rsMonthlyActiveMembersPrevious['members'];

		//Display Difference
		$diffMonthly = $totalMonthlyActiveMembers - $totalMonthlyActiveMembersPrevious;
		if ($diffMonthly > 0) {
			$diffMonthly = "+" . $diffMonthly;
		}
		$diffMonthlyDisplay = " (" . $diffMonthly . ")";

		//Action
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-md-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body align-content-end">
						<div class="row">
							<div class="col-md-2 stretch-card">
								<i class="mdi mdi-home icon-md d-flex align-self-start mr-3 newhome"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title"><?php echo $totalcomplexes; ?></strong>
									</p>
									<p class="text-dark text-left m-1">Total Complexes</p>
								</div>
							</div>

							<div class="col-md-2 stretch-card">
								<i class="mdi mdi-shopping icon-md d-flex align-self-start mr-3 newopi"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title"><?php echo $totalVendors; ?></strong>
									</p>
									<p class="text-dark text-left m-1">Total Vendors</p>
								</div>
							</div>

							<div class="col-md-2 stretch-card">
								<i class="mdi mdi-account-multiple icon-md d-flex align-self-start mr-3 newggate"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title"><?php echo $totalMembers; ?></strong>
									</p>
									<p class="text-dark text-left m-1">Total Members</p>
								</div>
							</div>

							<div class="col-md-2 stretch-card">
								<i class="mdi mdi-account-check icon-md d-flex align-self-start mr-3 newoji"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title"><?php echo $totalActiveMembers; ?></strong>
									</p>
									<p class="text-dark text-left m-1">Active Users</p>
								</div>
							</div>

							<div class="col-md-2 stretch-card">
								<?php if ($diffDaily < 0) { ?>
									<i class="mdi mdi-account-multiple-minus icon-md d-flex align-self-start mr-3 newolid"></i>
								<?php } else { ?>
									<i class="mdi mdi-account-multiple-plus icon-md d-flex align-self-start mr-3 newolid"></i>
								<?php } ?>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title">
											<?php echo $totalDailyActiveMembers; ?>
										</strong>
										<?php echo $diffDailyDisplay; ?>
										<?php if ($diffDaily < 0) { ?>
											<i class="mdi mdi-arrow-down-thick icon-sm badge-danger"></i>
										<?php } else { ?>
											<i class="mdi mdi-arrow-up-thick icon-sm badge-success"></i>
										<?php } ?>
									</p>
									<p class="text-dark text-left m-1">Yesterday: <?php echo $totalDailyActiveMembersPrevious; ?></p>
									<p class="text-dark text-left m-1">Daily Active Users</p>
								</div>
							</div>

							<div class="col-md-2 stretch-card">
								<?php if ($diffMonthly < 0) { ?>
									<i class="mdi mdi-account-multiple-minus icon-md d-flex align-self-start mr-3 newhome"></i>
								<?php } else { ?>
									<i class="mdi mdi-account-multiple-plus icon-md d-flex align-self-start mr-3 newhome"></i>
								<?php } ?>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title"><?php echo $totalMonthlyActiveMembers; ?></strong>
										<?php echo $diffMonthlyDisplay; ?>
										<?php if ($diffMonthly < 0) { ?>
											<i class="mdi mdi-arrow-down-thick icon-sm badge-danger"></i>
										<?php } else { ?>
											<i class="mdi mdi-arrow-up-thick icon-sm badge-success"></i>
										<?php } ?>
									</p>
									<p class="text-dark text-left m-1">Last Month: <?php echo $totalMonthlyActiveMembersPrevious; ?></p>
									<p class="text-dark text-left m-1">Monthly Active</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Complex Master</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Complex</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="ggateComplexList" width="100%">
								<thead>
									<tr>
										<th align="left">Logo</th>
										<th align="left">Complex Name</th>
										<th align="left">Address</th>
										<th align="left">City</th>
										<th align="left">Contact</th>
										<th align="left">Email</th>
										<th align="left">Status</th>
										<th width="15%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Logo</th>
										<th align="left">Complex Name</th>
										<th align="left">Address</th>
										<th align="left">City</th>
										<th align="left">Contact</th>
										<th align="left">Email</th>
										<th align="left">Status</th>
										<th width="15%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/ggateComplexList.php';
			$('#ggateComplexList').dataTable({
				"ajax": listURL,
				"order": [],
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
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

	public function complexInfoDetails()
	{
		$complexID = (int)$_REQUEST['complexID'];
		$sqlChart = "(select inDateTime, DATE_FORMAT(inDateTime, '%D %b') as dayMonth, sum(if(visitorType = '1', 1, 0)) as 'totalDailyResource',
					sum(if(visitorType = '2', 1, 0)) as 'totalGuest', sum(if(visitorType = '3', 1, 0)) as 'totalDeliveryBoy',
					sum(if(visitorType = '4', 1, 0)) as 'totalCab' from dailyGateActivity
					where complexID = " . $complexID . " and inDateTime is not null
					group by dayMonth order by inDateTime desc limit 7) order by inDateTime";
		$graphSql = pro_db_query($sqlChart);
		$resourceChartData = "";
		if (pro_db_num_rows($graphSql) > 0) {
			while ($grs = pro_db_fetch_array($graphSql)) {
				$resourceChartData .= "
				['" . $grs['dayMonth'] . "', " . $grs['totalDailyResource'] . ", " . $grs['totalGuest'] . ", 
				" . $grs['totalDeliveryBoy'] . ", " . $grs['totalCab'] . "], ";
			}
		} else {
			$resourceChartData .= "['---', 0, 0, 0, 0]";
		}

		//Remaining Days Calculation
		$queryComplexDates = pro_db_query("select enrolledDate, validUptoDate from complexMaster where complexID = " . $complexID);
		$resComplexDates = pro_db_fetch_array($queryComplexDates);
		$enrolledDate = $resComplexDates["enrolledDate"];
		$validUptoDate = $resComplexDates["validUptoDate"];
		//Difference Days
		$enrolledDateTime = strtotime($enrolledDate);
		$validUptoDateTime = strtotime($validUptoDate);
		$totalDaysDifference = ceil(abs($validUptoDateTime - $enrolledDateTime) / 86400) ?? 0;
		//Remaining Days
		$todayDateTime = strtotime(date('Y-m-d'));
		$remainingDays = ceil(abs($validUptoDateTime - $todayDateTime) / 86400) ?? 0;
		//Remaining Days - Percentage
		if ($remainingDays != 0 && $totalDaysDifference != 0) {
			$percentage = $remainingDays / $totalDaysDifference * 100;
		} else {
			$percentage = 0;
		}
		$remainingPercentage = round($percentage);
		if ($remainingPercentage > 100) {
			$remainingPercentage = 100;
		}
		$finishedPercentage = 100 - $remainingPercentage;
		//Display Dates
		$displayEnrolledDate = date("jS M, Y", $enrolledDateTime);
		$displayValidUptoDate = date("jS M, Y", $validUptoDateTime);

		//Last Activity
		$qryLastActivity = pro_db_query("select gateActivityID, visitorType, inDateTime, outDateTime, modifieddate, status
										from dailyGateActivity
										where complexID = " . $complexID . " order by modifieddate desc limit 1");
		$rsLastActivity = pro_db_fetch_array($qryLastActivity);
		$visitorType = "Visitor";
		switch ($rsLastActivity['visitorType']) {
			case 1:
				$visitorType = "Daily Resource";
				break;
			case 2:
				$visitorType = "Guest";
				break;
			case 3:
				$visitorType = "Delivery Person";
				break;
			case 4:
				$visitorType = "Cab";
				break;
			default:
				$visitorType = "Visitor";
				break;
		}
		$visitorRequestStatus = "Not Available";
		switch ($rsLastActivity['status']) {
			case 1:
				$visitorRequestStatus = "Inside the Society";
				break;
			case 2:
				$visitorRequestStatus = "Out from the Residence, but inside the Society";
				break;
			case 3:
				$visitorRequestStatus = "Left the Society";
				break;
			default:
				$visitorRequestStatus = "Unidentified";
				break;
		}
		$inDateTime = "";
		$outDateTime = "";
		$modifieddate = "";
		if ($rsLastActivity['inDateTime'] != null) {
			$inDateTime = date('d M Y - h:i A', strtotime($rsLastActivity['inDateTime']));
		}
		if ($rsLastActivity['outDateTime'] != null) {
			$outDateTime = date('d M Y - h:i A', strtotime($rsLastActivity['outDateTime']));
		}
		if ($rsLastActivity['modifieddate'] != null) {
			$modifieddate = date('d M Y - h:i A', strtotime($rsLastActivity['modifieddate']));
		}

		//Last Security Login
		$qryLastSecurityActivity = pro_db_query("select emp.employeeName, log.last_access, log.status from complexEmployeeLogMaster log
												join complexEmployeeMaster emp on log.employeeID = emp.employeeID and emp.status = 1
												where log.complexID = " . $complexID . " order by log.last_access desc limit 1");
		$rsLastSecurityActivity = pro_db_fetch_array($qryLastSecurityActivity);
		$securityEmployeeName = ucfirst($rsLastSecurityActivity['employeeName']);
		$securityEmployeeLastAccess = date('d M Y - h:i A', strtotime($rsLastSecurityActivity['last_access']));
	?>
		<div class="row" id="proBanner">
			<div class="col-md-8 grid-margin stretch-card">
				<div class="card">
					<div class="card-body align-content-end">
						<div class="row">
							<div class="col md-8">
								<h4 class="md-5">
									<p class="text-dark text-left m-1 display-5">
										<strong class="text-danger">
											<?php
											$qry = pro_db_query("select complexName from complexMaster where complexID = " . $complexID);
											$rs = pro_db_fetch_array($qry);
											echo $rs['complexName']; ?>
										</strong>
									</p>
								</h4>
							</div>
						</div>
						<div class="row">
							<br />
						</div>
						<div class="row">
							<div class="col-md-3 stretch-card">
								<i class="mdi mdi-home icon-md d-flex align-self-start mr-3 newhome"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title">
											<?php
											$qry = pro_db_query("select count(blockID) as blocks from blockMaster where status = 1 and complexID = " . $complexID);
											$rs = pro_db_fetch_array($qry);
											echo $rs['blocks'];
											?>
										</strong>
									</p>
									<p class="card-description">Total Blocks</p>
								</div>
							</div>

							<div class="col-md-3 stretch-card">
								<i class="mdi mdi-city icon-md d-flex align-self-start mr-3 newolid"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title">
											<?php
											$qry = pro_db_query("select count(officeMappingID) as offices from blockFloorOfficeMapping where status = 1 and complexID = " . (int)$_SESSION['complexID']);
											$rs = pro_db_fetch_array($qry);
											echo $rs['offices'];
											?>
										</strong>
									</p>
									<p class="card-description">Total Residence</p>
								</div>
							</div>

							<div class="col-md-3 stretch-card">
								<i class="mdi mdi-account-multiple icon-md d-flex align-self-start mr-3 newoji"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title">
											<?php
											$qry = pro_db_query("select count(memberID) as members from memberMaster where complexID = " . $complexID);
											$rs = pro_db_fetch_array($qry);
											echo $rs['members'];
											?>
										</strong>
									</p>
									<p class="card-description">Total Members</p>
								</div>
							</div>

							<div class="col-md-3 stretch-card">
								<i class="mdi mdi-account-check icon-md d-flex align-self-start mr-3 newopi"></i>
								<div>
									<p class="text-dark text-left m-1">
										<strong class="card-title">
											<?php
											$qry = pro_db_query("select count(memberID) as members from memberActivity where status = 4 and complexID = " . $complexID);
											$rs = pro_db_fetch_array($qry);
											echo $rs['members'];
											?>
										</strong>
									</p>
									<p class="card-description">Active Users</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4 grid-margin stretch-card">
				<div class="card">
					<div class="card-body align-content-end">
						<div class="row">
							<div class="col md-6">
								<p class="text-dark text-left m-1">Enrolled Date</p>
								<p class="text-dark text-left m-1"><strong class="text-info"><?php echo $displayEnrolledDate ?></strong></p>
							</div>
							<div class="col md-6">
								<p class="text-dark text-right m-1">Valid upto Date:</p>
								<p class="text-dark text-right m-1"><strong class="text-danger"><?php echo $displayValidUptoDate ?></strong></p>
							</div>
						</div>
						<div class="py-3">
							<div class="progress" style="height: 25px;">
								<div class="progress-bar <?php if ($finishedPercentage > 75) { ?> badge badge-gradient-progress-danger <?php } else { ?> badge badge-gradient-progress-success <?php } ?>" role="progressbar" style="width: <?php echo $finishedPercentage ?>%" aria-valuenow="<?php echo $finishedPercentage ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $finishedPercentage . "%"; ?></div>
							</div>
						</div>
						<div class="row">
							<div class="col md-6">
								<p class="text-dark text-right m-1">Remaining Days: <strong class=" <?php if ($finishedPercentage > 75) { ?> text-danger <?php } else { ?> text-info <?php } ?>"><?php echo $remainingDays ?></strong></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-7 grid-margin stretch-card">
				<div class="card">
					<div class="card-body chart_wrap">
						<h4 class="card-title">Daily Complex Visitors</h4>
						<div id="society_visitor_chart" style="width: 100%; height: 400px; position:inherit;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-5 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Security Guard Details</h4>
						<div class="table-responsive">
							<table class="table table-striped dataTable" id="securityGuardList">
								<thead>
									<tr>
										<th>Security Guard</th>
										<th width="15%">Code</th>
										<th width="35%">Last Access</th>
										<th width="15%">Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-7 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<p class="card-title">Last Gate Activity:</p>
						<div>
							<p class="text-dark text-left">
								<strong class="card-description">
									<?php echo $visitorType . " - " . $visitorRequestStatus; ?>
								</strong>
							</p>
							<p class="card-description">
								<?php
								switch ($rsLastActivity['status']) {
									case 1:
										echo "In Time: " . $inDateTime;
										break;
									case 2:
										echo "In Time: " . $inDateTime;
										break;
									case 3:
										echo "Out Time: " . $outDateTime;
										break;
									default:
										echo "Last Access: " . $modifieddate;
										break;
								}
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<p class="card-title">Last Login Details:</p>
						<div>
							<p class="text-dark text-left">
								<strong class="card-description">
									<?php
									$loginStatus = ($rsLastSecurityActivity["status"] == "I") ? "Logged In" : "Logged Out";
									echo $securityEmployeeName . " : " . $securityEmployeeLastAccess . " (" . $loginStatus . ")";
									?>
								</strong>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			var listURL = 'helperfunc/securityGuardList.php?complexID=<?php echo $_REQUEST["complexID"]; ?>';
			$('#securityGuardList').dataTable({
				"ajax": listURL,
				"order": [],
				"iDisplayLength": 5
			});
		</script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {
				'packages': ['corechart', 'gauge']
			});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				//Resources Chat
				var dataResources = google.visualization.arrayToDataTable([
					['Day', 'Daily Resources', 'Guest', 'Delivery Boy', 'Cab'],
					<?php echo $resourceChartData; ?>
				]);
				var optionsResources = {
					chartArea: {
						left: '5%',
						right: '5%',
						top: 50,
						width: '50%',
						height: '70%',
					},
					legend: {
						position: 'bottom',
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '13',
							bold: false,
							italic: true
						}
					},
					vAxis: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-light',
							fontSize: '12',
							bold: true,
						}
					},
					hAxis: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-light',
							fontSize: '12',
							bold: true,
						}
					},
					seriesType: 'bars',
					series: [{
							color: '#9694ff',
							visibleInLegend: true,
						},
						{
							color: '#ffbf76',
							visibleInLegend: true
						},
						{
							color: '#5ddab4',
							visibleInLegend: true
						},
						{
							color: '#ff7976',
							visibleInLegend: true
						}
					],
					tooltip: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '12',
							bold: false,
							italic: true
						}
					}
				};
				var chartResources = new google.visualization.ComboChart(document.getElementById('society_visitor_chart'));
				chartResources.draw(dataResources, optionsResources);
			}
		</script>
		<style>
			.chart_wrap {
				position: relative;
				height: 50;
				/* overflow: scroll; */
			}

			#society_visitor_chart {
				position: relative;
				top: 0;
				left: 0;
				width: 100%;
				height: 200px;
			}
		</style>
<?php
	}

	public function manageComplexStatus()
	{
		global $frmMsgDialog;
		$sqlUpdate = "Update complexMaster set status = " . (int)$_REQUEST['status'] . " where complexID = " . (int)$_REQUEST['complexID'];
		if (pro_db_query($sqlUpdate)) {
			$msg = '<p class="bg-success p-3">Complex status has been changed successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Unable to change Complex status...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function complexResetPassword()
	{
		$complexID = (int)$_REQUEST['complexID'];

		//Manage Entry in Login Master
		$logindata['loginID'] = "GGATE" . $complexID;
		$userPass = genPassword(12);
		$logindata['userPwd'] = hash('sha256', $logindata['loginID'] . $userPass);

		if (pro_db_perform('loginMaster', $logindata, 'update', "complexID = " . $complexID)) {
			$msg = '<p class="bg-success text-white p-3">Complex password has been changed successfully...<br><strong>Username:</strong> ' . $logindata['loginID'] . '<br><strong>Password:</strong> ' . $userPass . '<br></p>';
		} else {
			$msg = '<p class="bg-danger p-3">Unable to change Complex password...</p>';
		}
		echo $msg;
		echo '<br><a href="' . $this->redirectUrl . '" class="btn btn-info">Back to Society Management</a>';
	}
}
?>
