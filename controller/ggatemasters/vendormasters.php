<?php
class vendormasters
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $editsocietymappingmaction;
	protected $cloudStorage;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->editsocietymappingmaction = $this->redirectUrl . "&subaction=editsocietymapping";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "banners";
		} else {
			$this->mediaType = "banners-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$categoryID = generateOptions(getMasterList('categoryMaster', 'categoryID', 'categoryTitle', 'status = 1'));
		$Country = generateOptions(getMasterList('countries', 'countries_id', 'countries_name'));
		$State = generateOptions(getMasterList('zones', 'zone_id', 'zone_name'));
		$City = generateOptions(getMasterList('cityMaster', 'cityID', 'cityName'));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Vendor Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Vendor Category</label>
									<select name="categoryID" class="form-control custom-select mr-sm-2" id="categoryID" required>
										<option value="" hidden>Select Category</option>
										<?php echo $categoryID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Vendor Name:</label>
									<input type="text" name="vendorName" class="form-control" placeholder="Enter Vendor Name" required>
								</div>
								<div class="form-group col-sm-6">
									<label>Address:</label>
									<input type="text" name="vendorAddress" class="form-control" placeholder="Enter Vendor Address" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Country</label>
									<select name="countries_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="countries_id" data-target-list="zone_id" data-target-url="ajax/states.php" data-target-title="Select Country" required>
										<option value="" hidden>Select Country</option>
										<?php echo $Country; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>State</label>
									<select name="zone_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="zone_id" data-target-list="city_id" data-target-url="ajax/city.php" data-target-title="Select City" required>
										<option value="<?php echo $State; ?>" hidden>Select State</option>
										<?php echo $State; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>City</label>
									<select name="city_id" class="form-control custom-select mr-sm-2" id="city_id" required>
										<option value="" hidden>Select City</option>
										<?php echo $City; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Zip Code:</label>
									<input type="text" name="vendorZip" class="form-control" placeholder="Enter Vendor Zip Code" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Authorised Person:</label>
									<input type="text" name="authorisedPerson" class="form-control" placeholder="Enter Authorized Person Name" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Phone Number:</label>
									<input type="text" name="vendorPhone" minlength="10" maxlength="10" class="form-control" placeholder="Enter Phone Number" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Mobile Number:</label>
									<input type="text" name="vendorMobile" minlength="10" maxlength="10" class="form-control" placeholder="Enter Mobile Number">
								</div>
								<div class="form-group col-sm-3">
									<label>WhatsApp Number:</label>
									<input type="text" name="vendorWhatsApp" minlength="10" maxlength="10" class="form-control" placeholder="Enter WhatsApp Number">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Email Address:</label>
									<input type="text" name="vendorEmail" class="form-control" placeholder="Enter Email Address">
								</div>
								<div class="form-group col-sm-3">
									<label>Website:</label>
									<input type="text" name="vendorWebsite" class="form-control" placeholder="Enter Website Address">
								</div>
								<div class="form-group col-sm-3">
									<label>Redirect Option:</label>
									<input type="text" name="redirectOption" class="form-control" placeholder="Enter Redirect Option">
								</div>
								<div class="form-group col-sm-3">
									<label>Redirect Value:</label>
									<input type="text" name="redirectValue" class="form-control" placeholder="Enter Redirect Value">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Vendor Image:</label>
									<input type="file" name="vendorImage" accept="image/*" id="vendorImage" class="form-control vendorImage">
								</div>
								<div class="form-group col-sm-3">
									<label>GST Number:</label>
									<input type="text" name="vendorGST" class="form-control" placeholder="GST Number">
								</div>
								<div class="form-group col-sm-3">
									<label>EST Year:</label>
									<input type="text" name="vendorEST" class="form-control" placeholder="Established Year">
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
		$qry = pro_db_query("select * from vendorMaster where vendorID = " . (int)$_REQUEST['vendorID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$categoryID = generateOptions(getMasterList('categoryMaster', 'categoryID', 'categoryTitle', 'status= 1'), $rs['categoryID']);
		$Country = generateOptions(getMasterList('countries', 'countries_id', 'countries_name'), $rs['countries_id']);
		$State = generateOptions(getMasterList('zones', 'zone_id', 'zone_name'), $rs['zone_id']);
		$City = generateOptions(getMasterList('cityMaster', 'cityID', 'cityName'), $rs['city_id']);

		$vendorRedirectURL = $rs["vendorRedirectURL"];
		$arrRedirectURLs = explode("__GGATE__", $vendorRedirectURL);
		$redirectOption = $arrRedirectURLs[0] ?? "";
		$redirectValue = $arrRedirectURLs[1] ?? "";
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Vendor Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Vendor Category</label>
									<select name="categoryID" class="form-control custom-select mr-sm-2" id="categoryID" required>
										<option value="" hidden>Select Category</option>
										<?php echo $categoryID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Vendor Name:</label>
									<input type="text" name="vendorName" class="form-control" placeholder="Enter Vendor Name" value="<?php echo $rs['vendorName'] ?? ""; ?>" required>
								</div>
								<div class="form-group col-sm-6">
									<label>Address:</label>
									<input type="text" name="vendorAddress" class="form-control" placeholder="" value="<?php echo $rs['vendorAddress']; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Country</label>
									<select name="countries_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="countries_id" data-target-list="zone_id" data-target-url="ajax/states.php" data-target-title="Select Country" required>
										<option value="" hidden>Select Country</option>
										<?php echo $Country; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>State</label>
									<select name="zone_id" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" id="zone_id" data-target-list="city_id" data-target-url="ajax/city.php" data-target-title="Select City" required>
										<option value="" hidden>Select State</option>
										<?php echo $State; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>City</label>
									<select name="city_id" class="form-control custom-select mr-sm-2" id="city_id" required>
										<option value="" hidden>Select City</option>
										<?php echo $City; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Zip Code:</label>
									<input type="text" name="vendorZip" class="form-control" placeholder="Enter Vendor Zip Code" value="<?php echo $rs['vendorZip'] ?? ""; ?>" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Authorised Person:</label>
									<input type="text" name="authorisedPerson" class="form-control" placeholder="Enter Authorized Person Name" value="<?php echo $rs['authorisedPerson'] ?? ""; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>Phone Number:</label>
									<input type="text" name="vendorPhone" minlength="10" maxlength="10" class="form-control" placeholder="Enter Phone Number" value="<?php echo $rs['vendorPhone'] ?? ""; ?>" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Mobile Number:</label>
									<input type="text" name="vendorMobile" minlength="10" maxlength="10" class="form-control" placeholder="Enter Phone Number" value="<?php echo $rs['vendorMobile'] ?? ""; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>WhatsApp Number:</label>
									<input type="text" name="vendorWhatsApp" minlength="10" maxlength="10" class="form-control" placeholder="Enter WhatsApp Number" value="<?php echo $rs['vendorWhatsApp'] ?? ""; ?>">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Email Address:</label>
									<input type="text" name="vendorEmail" class="form-control" placeholder="Enter Email Address" value="<?php echo $rs['vendorEmail'] ?? ""; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>Website:</label>
									<input type="text" name="vendorWebsite" class="form-control" placeholder="Enter Website Address" value="<?php echo $rs['vendorWebsite'] ?? ""; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>Redirect Option:</label>
									<input type="text" name="redirectOption" class="form-control" placeholder="Enter Redirect Option" value="<?php echo $redirectOption; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>Redirect Value:</label>
									<input type="text" name="redirectValue" class="form-control" placeholder="Enter Redirect Value" value="<?php echo $redirectValue; ?>">
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Vendor Image:</label>
									<input type="file" name="vendorImage" accept="image/*" id="vendorImage" class="form-control vendorImage">
								</div>
								<div class="form-group col-sm-3">
									<label>GST Number:</label>
									<input type="text" name="vendorGST" class="form-control" placeholder="GST Number" value="<?php echo $rs['vendorGST'] ?? ""; ?>">
								</div>
								<div class="form-group col-sm-3">
									<label>EST Year:</label>
									<input type="text" name="vendorEST" class="form-control" placeholder="Established Year" value="<?php echo $rs['vendorEST'] ?? ""; ?>">
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
									<input type="hidden" name="vendorID" value="<?php echo (int)$rs['vendorID']; ?>">
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
		$formdata['vendorTypeID'] = 7;


		print_r($formdata['modifieddate']);

		//Redirect URL
		if ($formdata['redirectOption'] != null && !empty($formdata['redirectOption'] && $formdata['redirectValue'] != null && !empty($formdata['redirectValue']))) {
			$vendorRedirectURL = $formdata['redirectOption'] . "__GGATE__" . $formdata["redirectValue"];
			$formdata['vendorRedirectURL'] = $vendorRedirectURL;
		}
		unset($formdata['redirectOption']);
		unset($formdata['redirectValue']);

		if (pro_db_perform('vendorMaster', $formdata)) {
			$vendorID = pro_db_insert_id();
			$whr = "vendorID = " . $vendorID;
			/* Now Create Login for the created society */
			$logindata['loginID'] = "GGATEVENDOR" . $vendorID;
			$userPass = genPassword(12);
			$logindata['userPwd'] = hash('sha256', $logindata['loginID'] . $userPass);
			pro_db_perform('vendorMaster', $logindata, 'update', $whr);

			//Upload Vendor Image
			if (!empty($_FILES["vendorImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$vendorImage = $_FILES["vendorImage"]["name"];
				$image = explode(".", $vendorImage);
				$extension = end($image);

				if ($_FILES["vendorImage"]["error"] > 0) {
					$msg = $_FILES["vendorImage"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['vendorImage']['tmp_name']);
					$objectName = "img_vendor" . $vendorID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "vendorID = " . $vendorID;
						$imageData['vendorImage'] = $finalImageName;
						pro_db_perform('vendorMaster', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success text-white p-3">New Vendor is created...<br><strong>Username:</strong> ' . $logindata['loginID'] . '<br><strong>Password:</strong> ' . $userPass . '<br></p>';
		} else {
			$msg = '<p class="bg-danger text-white p-3">Vendor is not created...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "vendorID = " . $_POST['vendorID'];
		$vendorID = $_POST['vendorID'];

		$formdata = $_POST;
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		//Redirect URL
		if ($formdata['redirectOption'] != null && !empty($formdata['redirectOption'] && $formdata['redirectValue'] != null && !empty($formdata['redirectValue']))) {
			$vendorRedirectURL = $formdata['redirectOption'] . "__GGATE__" . $formdata["redirectValue"];
			$formdata['vendorRedirectURL'] = $vendorRedirectURL;
		}
		unset($formdata['redirectOption']);
		unset($formdata['redirectValue']);

		if (pro_db_perform('vendorMaster', $formdata, 'update', $whr)) {
			//Upload Vendor Image
			if (!empty($_FILES["vendorImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$vendorImage = $_FILES["vendorImage"]["name"];
				$image = explode(".", $vendorImage);
				$extension = end($image);

				if ($_FILES["vendorImage"]["error"] > 0) {
					$msg = $_FILES["vendorImage"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['vendorImage']['tmp_name']);
					$objectName = "img_vendor" . $vendorID . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "vendorID = " . $vendorID;
						$imageData['vendorImage'] = $finalImageName;
						pro_db_perform('vendorMaster', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success p-3">Vendor Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Vendor Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function assignSocieties()
	{
		global $frmMsgDialog;
		$createdate = date('Y-m-d H:i:s');
		$modifieddate = date('Y-m-d H:i:s');
		$remote_ip = $_SERVER['REMOTE_ADDR'];

		if (isset($_POST['assignSocieties'])) {
			$vendorID = $_POST['vendorID'];

			$queryRemoveExisting = pro_db_query("Delete from vendorSocietyMapping where vendorID = " . $vendorID);
			if ($queryRemoveExisting) {
				if (isset($_POST['societyselection'])) {
					$selectedSocieties = $_POST['societyselection'];

					//Insert as new Banner
					$vendorquery = pro_db_query("select * from vendorMaster where vendorID = " . $vendorID);
					$vendorrs = pro_db_fetch_array($vendorquery);

					$bannermasterquery  = pro_db_query("select * from bannerMaster where vendorID = " . $vendorID);
					$bannermasterrow = pro_db_num_rows($bannermasterquery);

					if ($bannermasterrow == 0) {
						if (!empty($vendorrs['vendorImage'])) {
							pro_db_query('insert into bannerMaster (userId, bannerTitle, bannerImage, bannerDesc, bannerURL, 
										vendorID, status, remote_ip, createdate, modifieddate) 
										values("' . $_SESSION['userID'] . '", "' . $vendorrs['vendorName'] . '", "' . $vendorrs['vendorImage'] . '", 
										"' . $_POST['servicesOffered'] . '", "' . $vendorrs['vendorWebsite'] . '", 
										' . $vendorID . ', 1, "' . $remote_ip . '", CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)');
							$bannerId = pro_db_insert_id();
						}
					} else {
						$bannermasterrs = pro_db_fetch_array($bannermasterquery);
						$bannerId = $bannermasterrs['bannerId'];
					}

					foreach ($selectedSocieties as $society) {
						$arrCheckboxAction = explode("_", $society);
						$societyID = $arrCheckboxAction[0];
						$memberAction = $arrCheckboxAction[1];

						$vendorsocietymappingquery = pro_db_query("select * from vendorSocietyMapping where vendorID = " . $_POST['vendorID'] . "
																	and societyID = " . $societyID);
						$vendorsocietymappingrow = pro_db_num_rows($vendorsocietymappingquery);

						if ($vendorsocietymappingrow == 0) {
							//Insert New Entry
							$formdata = array(
								"vendorID" => $_POST['vendorID'],
								"categoryID" => $_POST['categoryID'],
								"servicesOffered" => $_POST['servicesOffered'],
								"validUpto" => $_POST['validUpto'],
								"societyID" => $societyID,
								"createdate" => $createdate,
								"modifieddate" => $modifieddate,
								"remote_ip" => $remote_ip,
								"status" => $memberAction
							);
							pro_db_perform('vendorSocietyMapping', $formdata);

							if (!empty($vendorrs['vendorImage'])) {
								//Map Banner to Society
								pro_db_query("insert into bannerDetails (bannerID, companyID, societyID, cityID, startDate, endDate,
											createdate, modifieddate, remote_ip, status)
											values (" . $bannerId . ", 0, " . $societyID . ", " . $vendorrs['city_id'] . ", CURRENT_TIMESTAMP,
											DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 MONTH), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
											'" . $remote_ip . "', 1 )");
							}
						}
					}
					//Send Notification
					// $this->notifySocietyAboutVendor($vendorID);
				}
			}
			$msg = '<p class="bg-success p-3">Vendor has been assigned to societies successfully...</p>';

			$rUrl = "index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=" . $vendorID;
			echo sprintf($frmMsgDialog, $rUrl, $msg);
		}
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
		$categoryID = $_REQUEST["categoryID"] ?? 0;
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>GGATE Vendors List</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add New Vendor</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="vendorMasterList" width="100%">
								<thead>
									<tr>
										<th width="15%">Category</th>
										<th>Vendor Image</th>
										<th width="15%">Vendor Name</th>
										<th width="20%">Vendor Address</th>
										<th>City</th>
										<th>Authorized Person</th>
										<th>Contact</th>
										<th>Status</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="15%">Category</th>
										<th>Vendor Image</th>
										<th width="15%">Vendor Name</th>
										<th width="20%">Vendor Address</th>
										<th>City</th>
										<th>Authorized Person</th>
										<th>Contact</th>
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
			$(document).ready(function() {
				var listURL = 'helperfunc/vendorMasterList.php?categoryID=<?php echo $categoryID; ?>';
				$('#vendorMasterList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25
				});
			});
			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "vendorMaster"
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

	public function vendorSocietyMappingList()
	{
		$formaction = $this->redirectUrl . "&subaction=mapSocietiesList&vendorID=" . $_REQUEST['vendorID'];

		$qry = pro_db_query("select * from vendorMaster where vendorID = " . (int)$_REQUEST['vendorID']);
		$rs = pro_db_fetch_array($qry);
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4><?php echo $rs['vendorName']; ?></h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Assign Vendor To Complex</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="vendorSocietyMappingList" width="100%">
								<thead>
									<tr>
										<th width="15%">Category</th>
										<th width="15%">Vendor Name</th>
										<th width="15%">Complex Name</th>
										<th>Services Offered</th>
										<th width="10%">Valid Upto</th>
										<th width="7%">Status</th>
										<th width="7%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="15%">Category</th>
										<th width="15%">Vendor Name</th>
										<th width="15%">Complex Name</th>
										<th>Services Offered</th>
										<th width="10%">Valid Upto</th>
										<th width="7%">Status</th>
										<th width="7%">Action</th>
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
				var listURL = 'helperfunc/vendorSocietyMappingList.php?vendorID=<?php echo $_REQUEST["vendorID"]; ?>';
				$('#vendorSocietyMappingList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"iDisplayLength": 25
				});
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'vendorsocietymapping';
				var field_name = 'mappingID ';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&mappingID=" + primaryKey;

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
					"tblName": "vendorSocietyMapping"
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

	public function mapSocietiesList()
	{
		$assignsocietiesAction = $this->redirectUrl . "&subaction=assignSocieties";

		$qry = pro_db_query("select * from vendorMaster where vendorID = " . (int)$_REQUEST['vendorID']);
		$rs = pro_db_fetch_array($qry);

		$rUrl = "index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=" . $rs['vendorID'];
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Assign <?php echo  $rs['vendorName']; ?> to Societies</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmassign" id="frm-example" action="<?php echo $assignsocietiesAction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12 py-3">
									<button type="button" value="selectAll" id="checkall" class="main btn btn-reddit" onclick="checkAll()">Select All</button>
									<button type="button" value="deselectAll" class="main btn btn-secondary" onclick="uncheckAll()">Clear</button>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="table-responsive">
										<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="vendorAssignList" width="100%">
											<thead>
												<tr>
													<th width="15%">Complex Logo</th>
													<th width="20%">Complex Name</th>
													<th>Complex Address</th>
													<th width="10%">City</th>
													<th width="10%">Working</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
											<tfoot>
												<tr>
													<th width="15%">Complex Logo</th>
													<th width="20%">Complex Name</th>
													<th>Complex Address</th>
													<th width="10%">City</th>
													<th width="10%">Working</th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
							<div class="row" style="padding-top:20px;">
								<div class="form-group col-sm-9">
									<label>Service Offered:</label>
									<textarea name="servicesOffered" class="form-control" placeholder="Enter offered services" rows="3"></textarea><br />
								</div>
								<div class="form-group col-sm-3">
									<label>Valid Upto:</label>
									<input type="text" id="validUpto" name="validUpto" class="form-control eventTodayDateTime" placeholder="Valid Upto" required>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="vendorID" value="<?php echo (int)$_REQUEST['vendorID']; ?>">
									<input type="hidden" name="categoryID" value="<?php echo (int)$rs['categoryID']; ?>">
									<button type="submit" name="assignSocieties" class="btn btn-success">Update</button> &nbsp;&nbsp;
									<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $rUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<script>
				var vendorName = "<?php echo $rs["vendorName"] ?>";
				var vendorEncodedName = encodeURIComponent(vendorName);
				var listURL = 'helperfunc/vendorAssignList.php?vendorID=<?php echo $_REQUEST["vendorID"]; ?>&vendorName=' + vendorEncodedName;
				$('#vendorAssignList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"stateSave": true,
					"iDisplayLength": -1
				});
			</script>

			<script>
				// For Datetime Calendar
				$('.eventTodayDateTime').flatpickr({
					enableTime: false,
					dateFormat: "Y-m-d",
					minDate: "today"
				});

				$("validUpto").attr("required", true);
			</script>

			<script type="text/javascript">
				// Select all check boxes : Setting the checked property to true in checkAll() function
				function checkAll() {
					var items = document.getElementsByName('societyselection[]');
					for (var i = 0; i < items.length; i++) {
						if (items[i].type == 'checkbox')
							items[i].checked = true;
					}
				}
				// Clear all check boxes : Setting the checked property to false in uncheckAll() function
				function uncheckAll() {
					var items = document.getElementsByName('societyselection[]');
					for (var i = 0; i < items.length; i++) {
						if (items[i].type == 'checkbox')
							items[i].checked = false;
					}
				}
			</script>
		<?php
	}

	public function editServiceOffered()
	{
		$qry = pro_db_query("select * from vendorSocietyMapping where mappingID = " . (int)$_REQUEST['mappingID']);
		$rs = pro_db_fetch_array($qry);
		$vendorID = generateOptions(getMasterList('vendorMaster', 'vendorID', 'vendorName', 'status= 1'), $rs['vendorID']);
		$societyID = generateOptions(getMasterList('societyMaster', 'societyID', 'societyName'), $rs['societyID']);
		$categoryID = generateOptions(getMasterList('categoryMaster', 'categoryID', 'categoryTitle', 'status= 1'), $rs['categoryID']);

		$rUrl = "index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=" . $rs['vendorID'];
		?>
			<div class="row">
				<div class="col-sm-12 py-3 mt-2">
					<h4>Edit Service Offered Details</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editsocietymappingmaction; ?>" method="post" enctype="multipart/form-data">
								<div class="row">
									<div class="form-group col-sm-3">
										<label>Vendor Name</label>
										<select class="form-control custom-select mr-sm-2" disabled>
											<?php echo $vendorID; ?>
										</select>
									</div>
									<div class="form-group col-sm-3">
										<label>Complex Name</label>
										<select class="form-control custom-select mr-sm-2" disabled>
											<?php echo $societyID; ?>
										</select>
									</div>
									<div class="form-group col-sm-3">
										<label>Valid Upto:</label>
										<input type="text" id="validUpto" name="validUpto" class="form-control eventTodayDateTime" placeholder="Valid Upto" value="<?php echo $rs['validUpto']; ?>" required>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-9">
										<label>Service Offered:</label>
										<textarea name="servicesOffered" class="form-control" placeholder="Enter offered services" rows="5"><?php echo $rs['servicesOffered']; ?></textarea><br />
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-12">
										<input type="hidden" name="mappingID" value="<?php echo (int)$rs['mappingID']; ?>">
										<input type="hidden" name="vendorID" value="<?php echo (int)$rs['vendorID']; ?>">
										<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $rUrl; ?>">Cancel</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<script>
				// For Datetime Calendar
				$('.eventTodayDateTime').flatpickr({
					enableTime: false,
					dateFormat: "Y-m-d"
				});
				$(document).ready(function() {
					var hash = new Date().getTime();
					$('select[name="countries_id"]').on('change', function() {
						$('select[name="zone_id"]').load("ajax/states.php?hash=" + hash, {
							id: $(this).val(),
							ajax: 'true'
						});
					});
				});
				$(document).ready(function() {
					var hash = new Date().getTime();
					$('select[name="zone_id"]').on('change', function() {
						$('select[name="city_id"]').load("ajax/city.php?hash=" + hash, {
							id: $(this).val(),
							ajax: 'true'
						});
					});
				});
			</script>
	<?php
	}

	public function editsocietymapping()
	{
		global $frmMsgDialog;
		$whr = "mappingID = " . $_POST['mappingID'];

		$formdata = $_POST;
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$vendorID = $_POST['vendorID'];

		if (pro_db_perform('vendorSocietyMapping', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Vendor to society mapping Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Vendor to society mapping Detail is not updated!!!</p>';
		}
		$rUrl = "index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=" . $vendorID;
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function notifySocietyAboutVendor()
	{
		$vendorID = $_REQUEST['vendorID'];

		//-------------- No need to change anything here --------------//
		//Header
		$notificationPayload = (int) round(microtime(true) * 1000);
		$notificationHashKey = "2X9xHfKfOYCBZ6FnvoePwsWpty0" . "com.ripl.ggate";
		$notificationHashValue = hash('sha256', ($notificationPayload . $notificationHashKey));
		$headers = [
			'Content-Type: application/json', 'AUTHORIZATION: ' . $notificationHashValue,
			'PAYLOAD: ' . $notificationPayload
		];

		//Notification Params
		$notificationParams = [
			'announcementType' => 1,
			'requestID' => $vendorID,
			// 'message' => $notificationMessage
		];

		$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "sendAnnouncementNotification";

		$ch = curl_init();
		curl_setopt(
			$ch,
			CURLOPT_URL,
			$CURL_REQUEST_URL
		);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			json_encode($notificationParams)
		);
		$result = curl_exec($ch);

		if ($result === FALSE) {
			die('Problem occurred: ' . curl_error($ch));
		}
		curl_close($ch);
		//return json_encode($result);	

		//Dialog
		global $frmMsgDialog;
		$msg = '<p class="bg-success p-3">Society members have been notified successfully.</p>';
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}
}
	?>