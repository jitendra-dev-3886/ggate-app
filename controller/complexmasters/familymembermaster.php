<?php
class familymembermaster
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
		// $this->redirectUrl = $redirectUrl;
		$this->redirectUrl = "index.php?controller=complexmasters&action=membermaster";
		$this->redirectUrlForFamilyMember = "index.php?controller=complexmasters&action=familymembermaster";
		$this->addformaction = $this->redirectUrlForFamilyMember . "&subaction=add";
		$this->editformaction = $this->redirectUrlForFamilyMember . "&subaction=edit";
		$this->makeAdminformaction = $this->redirectUrlForFamilyMember . "&subaction=makeAdmin";
		$this->mediaType = "member";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "member";
		} else {
			$this->mediaType = "member-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'parentID = 0 and complexID = ' . $_SESSION['complexID']));
		$relationID = generateOptions(getMasterList('relationMaster', 'relationID', 'relationTitle'));
		$memberGender = generateStaticOptions(array("M" => "Male", "F" => "Female", "T" => "Other"));
		$adminType = generateStaticOptions(array("0" => "-", "1" => "Society Admin", "2" => "Building Admin", "3" => "Adhoc Admin"));
		$professionID = generateOptions(getMasterArray('professionMaster', 'professionID', 'professionTitle', '', 'sortorder,professionTitle asc'));
		$bloodGroup = generateStaticOptions(array("A+" => "A+", "B+" => "B+", "AB+" => "AB+", "O+" => "O+", "A-" => "A-", "B-" => "B-", "AB-" => "AB-", "O-" => "O-"));
		$committeeID = generateOptions(getMasterArray('designationMaster', 'designationID', 'designationTitle'));
		$selModules = getMasterList('designationMemberMapping', 'desgMemberID', 'designationID');
		$allowLoginStatus = generateStaticOptions(array("1" => "Allow Login", "0" => "Don't Allow"));
		$appUserStatus = generateStaticOptions(array("1" => "Allow to Use App", "0" => "Don't Allow"));

		$rUrl = $this->redirectUrl . "&subaction=listData";
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Family Member</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Parent Member:</label>
									<select name="parentID" id="parentID" class="custom-select mr-sm-2 form-control" data-live-search="true" required>
										<option class="scrollable-menu" role="menu" value="">Select Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Relation:</label>
										<select name="relationID" class="custom-select mr-sm-2" id="relationID" required>
											<option value="" hidden>Select Relation</option>
											<?php echo $relationID; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Name:</label>
										<input type="text" name="memberName" class="form-control" placeholder="Enter member name" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Mobile Number:</label>
										<input type="text" name="memberMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" placeholder="Enter mobile number" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Date of Birth:</label>
										<input type="text" id="memberDob" name="memberDob" class="form-control eventTodayDateTime" placeholder="Birth Date" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Email Address:</label>
										<input type="email" name="memberEmail" class="form-control" placeholder="Enter email address">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Gender:</label>
										<select class="custom-select mr-sm-2" id="memberGender" name="memberGender" required>
											<option value="">Select Gender</option>
											<option value="M">Male</option>
											<option value="F">Female</option>
											<option value="T">Other</option>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Profession:</label>
										<select name="professionID" class="custom-select mr-sm-2" id="professionID" required>
											<option value="" hidden>Select Profession</option>
											<?php echo $professionID; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Blood Group:</label>
										<select name="bloodGroup" class="custom-select mr-sm-2" id="bloodGroup" required>
											<option value="" hidden>Select Blood Group</option>
											<?php echo $bloodGroup; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Anniversary Date:</label>
										<input type="text" name="memberAnniversary" class="form-control input-group date eventTodayDateTime" placeholder="Anniversary Date">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Image:</label>
										<input type="file" name="memberImage" accept="image/*" id="memberImage" class="form-control memberImage">
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Allow Member to Login:</label>
									<select name="allowLogin" id="allowLogin" class="form-control custom-select mr-sm-2" required>
										<option class="scrollable-menu" role="menu" value="">Select Action</option>
										<?php echo $allowLoginStatus; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Allow Member to Use App:</label>
										<select name="isAppUser" id="isAppUser" class="form-control custom-select mr-sm-2" required>
											<option class="scrollable-menu" role="menu" value="">Select Action</option>
											<?php echo $appUserStatus; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="custom-select mr-sm-2">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Committee Role:</label>
										<?php
										$i = 0;
										$moduleSql = pro_db_query("select * from designationMaster where status = '1' order by sortorder");
										$pSelected = "";
										$cSelected = "";
										while ($mprs = pro_db_fetch_array($moduleSql)) {
											$i++;
											// if (in_array($mprs['committeeID'], $selModules)) {
											// 	$pSelected = " checked";
											// } else {
											// 	$pSelected = "";
											// }
											echo '
										<div class="custom-control custom-switch">
											<input class="custom-control-input case" type="checkbox" name="designationID[]" value="' . $mprs['designationID'] . '" id="modules_' . $i . '" ' . $pSelected . '>
											<label class="custom-control-label" for="modules_' . $i . '"><strong>' . ucfirst($mprs['designationTitle']) . '</strong></label>
										</div>';
										}
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="maxAllowedMembers" value="5">
										<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;
										<!-- "<?php echo $this->redirectUrl; ?>" -->
										<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $rUrl; ?>">Cancel</button>
									</div>
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
				dateFormat: "Y-m-d",
				maxDate: "today"
			});

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
		<script>
			//TODO: Need to check - Vasav
			$('#memberDob').on('change', function() {
				var today = new Date();
				var birthDate = new Date(this.value);
				var age = today.getFullYear() - birthDate.getFullYear();
				var m = today.getMonth() - birthDate.getMonth();
				if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
					age--;
				}

				if (age < 18) {
					$("#memberMobile").prop('required', true);
				} else {
					$("#memberMobile").prop('required', false);
				}
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from memberMaster where memberID = " . (int)$_REQUEST['memberID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$memberID = generateOptions(getMasterList('memberMaster', 'memberID', 'concat(memberName, " - ", memberMobile)', 'parentID = 0 and societyID = ' . $_SESSION['societyID']), $rs['parentID']);
		$memberGender = generateStaticOptions(array("M" => "Male", "F" => "Female", "T" => "Other"), $rs['memberGender']);
		$relationID = generateOptions(getMasterList('relationMaster', 'relationID', 'relationTitle'), $rs['relationID']);
		$professionID = generateOptions(getMasterArray('professionMaster', 'professionID', 'professionTitle'), $rs['professionID']);
		$bloodGroup = generateStaticOptions(array("A+" => "A+", "B+" => "B+", "AB+" => "AB+", "O+" => "O+", "A-" => "A-", "B-" => "B-", "AB-" => "AB-", "O-" => "O-"), $rs['bloodGroup']);
		// $committeeID = generateOptions(getMasterArray('memberCommittee', 'committeeID', 'committeeTitle'), $rs['committeeTitle']);
		$selModules = getMasterList('designationMemberMapping', 'desgMemberID', 'designationID', 'memberID = ' . $_REQUEST['memberID']);
		$adminType = generateStaticOptions(array("0" => "No Admin", "1" => "Society Admin", "2" => "Building Admin", "3" => "Adhoc Admin"), $rs['adminType']);
		$allowLoginStatus = generateStaticOptions(array("1" => "Allow Login", "0" => "Don't Allow"), $rs['allowLogin']);
		$appUserStatus = generateStaticOptions(array("1" => "Allow to Use App", "0" => "Don't Allow"), $rs['isAppUser']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Family Member</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Parent Member:</label>
									<select name="parentID" id="parentID" class="custom-select mr-sm-2 form-control" data-live-search="true" required>
										<option class="scrollable-menu" role="menu" value="">Select family Member</option>
										<?php echo $memberID; ?>
									</select>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Relation :</label>
										<select name="relationID" class="custom-select mr-sm-2" id="relationID" required>
											<option value="" hidden>Select Relation</option>
											<?php echo $relationID; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Name:</label>
										<input type="text" name="memberName" class="form-control" value="<?php echo $rs['memberName']; ?>" placeholder="Enter member name" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Mobile Number:</label>
										<?php if (!empty($rs['memberMobile'])) { ?>
											<input type="text" name="memberMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['memberMobile']); ?>" placeholder="Enter mobile number" required>
										<?php } else { ?>
											<input type="text" name="memberMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['memberMobile']); ?>" placeholder="Enter mobile number">
										<?php } ?>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Date of Birth:</label>
										<input type="date" id="memberDob" name="memberDob" class="form-control" value="<?php echo $rs['memberDob']; ?>" placeholder="Birth Date" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Email Address:</label>
										<input type="email" name="memberEmail" class="form-control" value="<?php echo $rs['memberEmail']; ?>" placeholder="Enter email address">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Gender:</label>
										<select name="memberGender" class="custom-select mr-sm-2">
											<?php echo $memberGender; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Profession:</label>
										<select name="professionID" class="custom-select mr-sm-2" id="professionID">
											<option value="0">No Profession Added</option>
											<?php echo $professionID; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Blood Group:</label>
										<select name="bloodGroup" class="custom-select mr-sm-2" id="bloodGroup">
											<option value="" hidden>Select Blood Group</option>
											<?php echo $bloodGroup; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Anniversary Date:</label>
										<input type="date" name="memberAnniversary" class="form-control" value="<?php echo $rs['memberAnniversary']; ?>" placeholder="Anniversary Date">
									</div>
								</div>
							</div>
							<!-- <div class="col-sm-3">
									<div class="form-group">
										<label>Max Allowed Members:</label>
										<input type="text" name="maxAllowedMembers" class="form-control" value="<?php echo $rs['maxAllowedMembers']; ?>" placeholder="" required>
									</div>
								</div> -->
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Image:</label>
										<input type="file" accept="image/*" name="memberImage" id="memberImage" class="form-control memberImage">
										<input type="hidden" name="prevImage" value="<?php echo $rs['memberImage']; ?>" id="memberImage" class="form-control memberImage">
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Allow Member to Login:</label>
									<select name="allowLogin" id="allowLogin" class="form-control custom-select mr-sm-2" required>
										<option class="scrollable-menu" role="menu" value="">Select Action</option>
										<?php echo $allowLoginStatus; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Allow Member to Use App:</label>
										<select name="isAppUser" id="isAppUser" class="form-control custom-select mr-sm-2" required>
											<option class="scrollable-menu" role="menu" value="">Select Action</option>
											<?php echo $appUserStatus; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="custom-select mr-sm-2">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Committee Role:</label>
										<?php
										$i = 0;
										$moduleSql = pro_db_query("select * from designationMaster where status = '1' order by sortorder");
										$pSelected = "";
										$cSelected = "";
										while ($mprs = pro_db_fetch_array($moduleSql)) {
											$i++;
											if (in_array($mprs['designationID'], $selModules)) {
												$pSelected = " checked";
											} else {
												$pSelected = "";
											}
											echo '
										<div class="custom-control custom-switch">
											<input class="custom-control-input case" type="checkbox" name="designationID[]" value="' . $mprs['designationID'] . '" id="modules_' . $i . '" ' . $pSelected . '>
											<label class="custom-control-label" for="modules_' . $i . '"><strong>' . ucfirst($mprs['designationTitle']) . '</strong></label>
										</div>';
										}
										?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo (int)$rs['memberID']; ?>">
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
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				maxDate: "today"
			});

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
		<script>
			$('#memberDob').on('change', function() {
				var today = new Date();
				var birthDate = new Date(this.value);
				var age = today.getFullYear() - birthDate.getFullYear();
				var m = today.getMonth() - birthDate.getMonth();
				if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
					age--;
				}

				if (age < 18) {
					$("#memberMobile").prop('required', true);
				} else {
					$("#memberMobile").prop('required', false);
				}
			});
		</script>
	<?php
	}

	public function makeAdminForm()
	{
		$qry = pro_db_query("select * from memberMaster where memberID = " . (int)$_REQUEST['memberID']);
		$rs = pro_db_fetch_array($qry);
		$adminType = generateStaticOptions(array("1" => "Complex Admin", "2" => "Block Admin", "3" => "Adhoc Admin", "4" => "Office Admin"));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Make Family Member Admin</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->makeAdminformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="form-group col-sm-3">
											<label>Admin Type:</label>
											<select name="adminType" class="form-control" required>
												<?php echo $adminType; ?>
											</select>
										</div>
										<div class="form-group col-sm-3">
											<label>Name:</label>
											<input type="text" name="memberName" class="form-control" value="<?php echo $rs['memberName']; ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-3">
											<label>Mobile Number:</label>
											<input type="text" name="memberMobile" minlength="10" maxlength="10" pattern="[1-9]{1}[0-9]{9}" data-error="Enter Valid Mobile Number" class="form-control" value="<?php echo stripslashes($rs['memberMobile']); ?>" placeholder="" readonly>
										</div>
										<div class="form-group col-sm-3">
											<label>Email Address:</label>
											<input type="email" name="memberEmail" class="form-control" value="<?php echo $rs['memberEmail']; ?>" placeholder="" readonly>
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo (int)$rs['memberID']; ?>">
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
		unset($formdata['designationID']);

		if ($formdata['memberAnniversary'] == null || empty($formdata['memberAnniversary'])) {
			unset($formdata['memberAnniversary']);
		}

		$memberMobile = $formdata['memberMobile'];
		$isExistingEntry = false;

		if ($memberMobile != null && !empty($memberMobile)) {
			$sql = pro_db_query("select * from memberMaster where memberMobile = '" . $formdata['memberMobile'] . "'");
			if (pro_db_num_rows($sql) > 0) {
				$isExistingEntry = true;
			}
		}

		if ($isExistingEntry) {
			$msg = '<p class="bg-danger p-3">This Mobile number is already registred to another user!!...</p>';
		} else {
			if (pro_db_perform('memberMaster', $formdata)) {
				$member_id = pro_db_insert_id();

				//dashboard log for familymember
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "familymembermaster";
				$dashboardlogdata['subAction'] = "addfamilymember";
				$dashboardlogdata['referenceID'] = $member_id;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);


				if (!empty($_FILES["memberImage"]["name"])) {
					$allowedTypes = array("gif", "jpeg", "jpg", "png");
					$memberImage = $_FILES["memberImage"]["name"];
					$image = explode(".", $memberImage);
					$extension = end($image);

					if ($_FILES["memberImage"]["error"] > 0) {
						$msg = $_FILES["memberImage"]["error"];
						//$rawData["imageName"] = null;
					} else {
						$imageRawData = file_get_contents($_FILES['memberImage']['tmp_name']);
						$objectName = "memberImage-" . $member_id . "-" . date('YmdHis') . "." . $extension;
						$imageName = $this->mediaType . "/" . $objectName;

						//Upload a file to the bucket.
						if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
							$finalImageName = GCLOUD_CDN_URL . $imageName;

							//Update into dailyStaffMaster
							$wher = "";
							$wher = "memberID=" . $member_id;
							$imageData['memberImage'] = $finalImageName;
							if (pro_db_perform('memberMaster', $imageData, 'update', $wher)) {
							}
						}
					}
				}

				//$committeedata['memberID'] = $member_id;
				//$committeedata['societyID'] = $_SESSION['societyID'];
				//$committeedata['committeeID'] = $_POST['committeeID'];
				//$committeedata['createdate'] = date('Y-m-d H:i:s');
				//$committeedata['modifieddate'] = date('Y-m-d H:i:s');
				//$committeedata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				//$committeedata['status'] = $_POST['status'];
				//pro_db_perform('memberCommitteeMapping',$committeedata);
				//$new_id = pro_db_insert_id();
				if (!empty($_POST['designationID']) && sizeof($_POST['designationID']) > 0) {
					foreach ($_POST['designationID'] as $key => $value) {
						$insSql = pro_db_query("
						insert into designationMemberMapping set
						adminID = '" . $_SESSION['memberID'] . "',
						blockID = 0,
						memberID = '" . $member_id . "',
						societyID = '" . $_SESSION['societyID'] . "',
						designationID = '" . $value . "',
						createdate = '" . $formdata['createdate'] . "',
						modifieddate = '" . $formdata['modifieddate'] . "',
						remote_ip = '" . $_SERVER['REMOTE_ADDR'] . "',
						status = '1'
						");
					}
				}
				$msg = '<p class="bg-success p-3">Member Detail is saved successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3"> Member Detail is not saved!!!!!!</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$member_id = $_POST['memberID'];
		$whr = "";
		$whr = "memberID=" . $member_id;
		$formdata = $_POST;
		if ($formdata['memberAnniversary'] == null || empty($formdata['memberAnniversary'])) {
			unset($formdata['memberAnniversary']);
		}

		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['memberName']));
		unset($formdata['designationID']);

		$memberMobile = $formdata['memberMobile'];
		$isExistingEntry = false;

		if ($memberMobile != null && !empty($memberMobile)) {
			$sql = pro_db_query("select * from memberMaster where memberMobile = '" . $formdata['memberMobile'] . "' and memberID !=" . $formdata['memberID']);
			if (pro_db_num_rows($sql) > 0) {
				$isExistingEntry = true;
			}
		}

		if ($isExistingEntry) {
			$msg = '<p class="bg-danger p-3">This Mobile number is already registred to another user!!...</p>';
		} else {
			if (!empty($_FILES["memberImage"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$memberImage = $_FILES["memberImage"]["name"];
				$image = explode(".", $memberImage);
				$extension = end($image);

				if ($_FILES["memberImage"]["error"] > 0) {
					$msg = $_FILES["memberImage"]["error"];
					//$rawData["imageName"] = null;
				} else {
					$imageRawData = file_get_contents($_FILES['memberImage']['tmp_name']);
					$objectName = "memberImage-" . $member_id . "-" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;
					$imagebaseUrl = GCLOUD_CDN_URL . $this->mediaType . "/";

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into dailyStaffMaster
						$wher = "";
						$wher = "memberID=" . $member_id;
						$imageData['memberImage'] = $finalImageName;

						$objectToDelete = str_replace($imagebaseUrl, "", $_POST['prevImage']);
						//gcsDeleteFile(GCLOUD_BUCKET,$objectToDelete);

						if (pro_db_perform('memberMaster', $imageData, 'update', $wher)) {
						}
					}
				}
			}
			$formdata['username'] = $_SESSION['username'];
			$formdata['createdate'] = date('Y-m-d H:i:s');
			$formdata['modifieddate'] = date('Y-m-d H:i:s');

			unset($formdata['prevImage']);

			if (pro_db_perform('memberMaster', $formdata, 'update', $whr)) {

				//dashboard log for familymember
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "familymembermaster";
				$dashboardlogdata['subAction'] = "editfamilymember";
				$dashboardlogdata['referenceID'] = $whr;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				if (isset($_POST['designationID'])) {
					$delQry = pro_db_query("delete from designationMemberMapping where memberID = " . (int)$_POST['memberID']);
					foreach ($_POST['designationID'] as $key => $value) {
						$insSql = pro_db_query("
						insert into designationMemberMapping set
						adminID = '" . $_SESSION['memberID'] . "',
						blockID = 0,
						memberID = '" . $member_id . "',
						societyID = '" . $_SESSION['societyID'] . "',
						designationID = '" . $value . "',
						createdate = '" . $formdata['createdate'] . "',
						modifieddate = '" . $formdata['modifieddate'] . "',
						remote_ip = '" . $_SERVER['REMOTE_ADDR'] . "',
						status = '1'
						");
					}
				} else {
					$delSql = pro_db_query("delete from designationMemberMapping where memberID = " . (int)$_POST['memberID']);
				}
				$msg = '<p class="bg-success p-3">Member Detail is updated successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Member Detail is not saved!!!!!!</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function makeAdmin()
	{
		global $frmMsgDialog;
		$formdata = array();
		if ($_POST['adminType'] == 1) {
			$formdata['groupID'] = 5;
		} else if ($_POST['adminType'] == 4) {
			$formdata['groupID'] = 7;
		} else {
			$formdata['groupID'] = 6;
		}
		$whr = "";
		$whr = "memberID=" . $_POST['memberID'];
		$slug = pro_SeoSlug(pro_db_real_escape_string($_POST['memberName']));
		$memberdata = $_POST;

		$formdata['complexID'] = $_SESSION['complexID'];
		$formdata['memberID'] = $_POST['memberID'];
		$formdata['userEmail'] = $_POST['memberEmail'];
		$formdata['userMobile'] = $_POST['memberMobile'];
		$formdata['userName'] = $slug;
		$formdata['status'] = 'E';
		$formdata['loginID'] = "GGATE" . $_SESSION['complexID'] . $_POST['memberID'];
		$userPass = genPassword(12);
		$formdata['userPwd'] = hash('sha256', $formdata['loginID'] . $userPass);
		if (pro_db_perform('loginMaster', $formdata)) {

			$userID = pro_db_insert_id();
			//dashboard log for familymember
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "familymembermaster";
			$dashboardlogdata['subAction'] = "makefamilymemberadmin";
			$dashboardlogdata['referenceID'] = $userID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			pro_db_perform('memberMaster', $memberdata, 'update', $whr);
			$msg = '<p class="bg-success p-3">Member Admin Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Member Admin Detail is not saved!!!!!!</p>';
		}
		$msg = '<p class="bg-success text-white p-3">Society Admin is created...<br><strong>Username:</strong> ' . $formdata['loginID'] . '<br><strong>Password:</strong> ' . $userPass . '<br></p>';
		echo $msg;
		echo '<br><a href="' . $this->redirectUrl . '" class="btn btn-info">Back to Society Members Management</a>';
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "call disableMemberFromGGATE (" . $_GET['memberID'] . ")";

		if (pro_db_query($delsql)) {

			$sql = pro_db_query("select userID from loginMaster where complexID != " . $_SESSION['complexID'] . " and status = 'E' and memberID = " . $_GET['memberID'] . "");
			$rows = pro_db_num_rows($sql);
			if ($rows == 0) {
				pro_db_query("update memberMaster set adminType = 0 where memberID = " . $_GET['memberID'] . "");
			}

			//dashboard log for familymember
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "familymembermaster";
			$dashboardlogdata['subAction'] = "deletefamilymember";
			$dashboardlogdata['referenceID'] = $_GET['memberID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
			$msg = '<p class="bg-success p-3">Member Detail deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Member Detail Not deleted successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}
}
?>