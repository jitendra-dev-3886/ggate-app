<?php
class gallery
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addfolderaction;
	protected $editformaction;
	protected $makeAdminformaction;
	protected $cloudStorage;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addfolderaction = $this->redirectUrl . "&subaction=createFolder";
		$this->editfolderaction = $this->redirectUrl . "&subaction=editFolder";
		$this->addfileaction = $this->redirectUrl . "&subaction=createFile";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";

		if (IS_PRODUCTION == 1) {
			$this->mediaType = "complex/complex-" . $_SESSION['complexID'];
		} else {
			$this->mediaType = "complex-dev/complex-" . $_SESSION['complexID'] . "-dev";
		}
	}

	public function createFolder()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['status'] = 1;

		if (pro_db_perform('complexFolderMaster', $formdata)) {
			$folderID = pro_db_insert_id();

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "gallery";
			$dashboardlogdata['subAction'] = "createFolder";
			$dashboardlogdata['referenceID'] = $folderID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Folder is created successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Folder is not created!!!!!!</p>';
		}

		if ($_POST['parentID'] > 0) {
			$rUrl = $this->redirectUrl . "&subaction=listData&folderID=" . $_POST['parentID'];
		} else {
			$rUrl = $this->redirectUrl . "&subaction=listData";
		}

		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function createFile()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['status'] = 1;

		if (!empty($_FILES["fileUrl"]["name"])) {
			$fileUrl = $_FILES["fileUrl"]["name"];
			$image = explode(".", $fileUrl);
			$extension = end($image);

			$fileSize = $_FILES["fileUrl"]["size"];
			$fileMaxSize = ini_get('upload_max_filesize');
			$uploadMaxSizeBytes = $this->human2byte($fileMaxSize);

			if ($fileSize < $uploadMaxSizeBytes) {
				$fileErrorCode = $_FILES["fileUrl"]["error"];
				if ($fileErrorCode > 0) {
					switch ($fileErrorCode) {
						case 1:
						case 2:
							$fileUploadErrorMessage = "You are not allowed to upload file more than " . $fileMaxSize;
							break;
						case 3:
							$fileUploadErrorMessage = "Your uploaded file was only partially uploaded.";
							break;
						case 4:
							$fileUploadErrorMessage = "No file was uploaded.";
							break;
						case 6:
							$fileUploadErrorMessage = "Missing a temporary folder.";
							break;
						case 7:
							$fileUploadErrorMessage = "Failed to write file to disk.";
							break;
						case 8:
							$fileUploadErrorMessage = "A PHP extension stopped the file upload.";
							break;
						default:
							$fileUploadErrorMessage = "Invalid media file.";
							break;
					}
					$msg = '<p class="bg-danger p-3">' . $fileUploadErrorMessage . '</p>';
				} else {
					$imageRawData = file_get_contents($_FILES['fileUrl']['tmp_name']);
					if (pro_db_perform('complexFileMaster', $formdata)) {
						$fileID = pro_db_insert_id();
						$wher = "fileID = " . $fileID;

						$objectName = "fileUrl-" . $fileID . "-" . date('YmdHis') . "." . $extension;
						$imageName = $this->mediaType . "/" . $objectName;

						//Upload a file to the bucket.
						if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
							$finalImageName = GCLOUD_CDN_URL . $imageName;

							//Update into societyFileMaster
							$imageData['fileUrl'] = $finalImageName;
							pro_db_perform('complexFileMaster', $imageData, 'update', $wher);
							$msg = '<p class="bg-success p-3">File is uploaded successfully...</p>';

							//dashboard log for itemMaster
							$dashboardlogdata = array();
							$dashboardlogdata['complexID'] = $_SESSION['complexID'];
							$dashboardlogdata['memberID'] = $_SESSION['memberID'];
							$dashboardlogdata['contorller'] = "inventory";
							$dashboardlogdata['action'] = "gallery";
							$dashboardlogdata['subAction'] = "createFile";
							$dashboardlogdata['referenceID'] = $fileID;
							$dashboardlogdata['status'] = 1;
							$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
							$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
							$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
							pro_db_perform('dashboardLogMaster', $dashboardlogdata);
						} else {
							pro_db_query("Delete from complexFileMaster where fileID = " . $fileID);
							$msg = '<p class="bg-danger p-3">File is not uploaded...</p>';
						}
					} else {
						$msg = '<p class="bg-danger p-3">File is not uploaded...</p>';
					}
				}
			} else {
				$msg = '<p class="bg-danger p-3">You are not allowed to upload file more than ' . $fileMaxSize . '.</p>';
			}
		} else {
			$msg = '<p class="bg-danger p-3">Invalid media file.</p>';
		}

		if ($_POST['folderID'] > 0) {
			$rUrl = $this->redirectUrl . "&subaction=listData&folderID=" . $_POST['folderID'];
		} else {
			$rUrl = $this->redirectUrl . "&subaction=listData";
		}
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function human2byte($value)
	{
		return preg_replace_callback(
			'/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i',
			function ($m) {
				switch (strtolower($m[2])) {
					case 't':
						$m[1] *= 1024;
					case 'g':
						$m[1] *= 1024;
					case 'm':
						$m[1] *= 1024;
					case 'k':
						$m[1] *= 1024;
				}
				return $m[1];
			},
			$value
		);
	}


	public function editFolder()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$folderID = $_POST['folderID'];
		$parentID = $_POST['parentID'];
		$whr = "folderID = " . $folderID;

		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['status'] = 1;
		unset($formdata['folderID']);
		unset($formdata['parentID']);

		if (pro_db_perform('complexFolderMaster', $formdata, 'update', $whr)) {

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "gallery";
			$dashboardlogdata['subAction'] = "editFolder";
			$dashboardlogdata['referenceID'] = $folderID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Folder has been updated successfully.</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Folder has not been updated.</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData&subaction=listData&folderID=" . $parentID;
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function deleteFolder()
	{
		global $frmMsgDialog;
		$parentID = (int)$_GET['parentID'];
		$folderID = (int)$_GET['folderID'];
		$delsql = "Update complexFolderMaster set status = 126 where folderID = " . $folderID;
		if (pro_db_query($delsql)) {

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "gallery";
			$dashboardlogdata['subAction'] = "deleteFolder";
			$dashboardlogdata['referenceID'] = $_GET['folderID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Folder has been deleted successfully.</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Folder has not been deleted.</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData&subaction=listData&folderID=" . $parentID;
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function folderVisibilty()
	{
		global $frmMsgDialog;
		$parentID = (int)$_GET['parentID'];
		$folderID = (int)$_GET['folderID'];
		$visibleToAll = (int)$_GET['visibleToAll'];

		//Manage Visibility
		$this->updateFolderFilesVisibility($visibleToAll, $folderID);

		//dashboard log for gallery
		$dashboardlogdata = array();
		$dashboardlogdata['complexID'] = $_SESSION['complexID'];
		$dashboardlogdata['memberID'] = $_SESSION['memberID'];
		$dashboardlogdata['contorller'] = "inventory";
		$dashboardlogdata['action'] = "gallery";
		$dashboardlogdata['subAction'] = "folderVisibilty";
		$dashboardlogdata['referenceID'] = $_GET['folderID'];
		$dashboardlogdata['status'] = 1;
		$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		pro_db_perform('dashboardLogMaster', $dashboardlogdata);

		$msg = '<p class="bg-success p-3">Folder visibilty status has been changed successfully.</p>';

		if ($parentID == 0) {
			$rUrl = $this->redirectUrl . "&subaction=listData&subaction=listData";
		} else {
			$rUrl = $this->redirectUrl . "&subaction=listData&subaction=listData&folderID=" . $parentID;
		}
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function updateFolderFilesVisibility($visibleToAll, $folderID)
	{
		//Update Folders
		$updateSQL = "Update complexFolderMaster set visibleToAll = " . $visibleToAll . " where folderID = " . $folderID;
		pro_db_query($updateSQL);

		//Update Files
		$updateFilesSQL = "Update complexFileMaster set visibleToAll = " . $visibleToAll . " where folderID = " . $folderID;
		pro_db_query($updateFilesSQL);

		//Fetch Sub-Folders
		$sqlSubfolder = pro_db_query("select folderID from complexFolderMaster where parentID = " . $folderID);
		while ($resSubfolder = pro_db_fetch_array($sqlSubfolder)) {
			$subfolderID = $resSubfolder["folderID"];
			$this->updateFolderFilesVisibility($visibleToAll, $subfolderID);
		}
	}

	public function deleteFile()
	{
		global $frmMsgDialog;
		$folderID = (int)$_GET['folderID'];
		$fileID = (int)$_GET['fileID'];
		$delsql = "Update complexFileMaster set status = 126 where fileID = " . $fileID;
		if (pro_db_query($delsql)) {

			//dashboard log for itemMaster
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "inventory";
			$dashboardlogdata['action'] = "gallery";
			$dashboardlogdata['subAction'] = "deleteFile";
			$dashboardlogdata['referenceID'] = $_GET['fileID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">File has been deleted successfully.</p>';
		} else {
			$msg = '<p class="bg-danger p-3">File has not been deleted.</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData&subaction=listData&folderID=" . $folderID;
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$folderID = $_REQUEST['folderID'] ?? 0;
		$fileType = generateStaticOptions(array("1" => "Image", "2" => "Video", "3" => "PDF", "4" => "DOC", "5" => "Excel", "6" => "Audio", "File"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<button type="button" class="btn btn-info float-right ml-2" data-toggle="modal" data-target="#createFolder" data-whatever="@mdo"><i class="mdi mdi-folder-account"></i>&nbsp;&nbsp;Add Folder
				</button>
				<button type="button" class="btn btn-info float-right ml-2" data-toggle="modal" data-target="#createFile" data-whatever="@mdo"><i class="mdi mdi-file"></i>&nbsp;&nbsp;Add File
				</button>
				<?php
				$visibleToAll = 0;
				if ($folderID > 0) {
					$query = pro_db_query("select parentID, visibleToAll from complexFolderMaster where folderID =" . $folderID);
					while ($res = pro_db_fetch_array($query)) {
						if ($res['parentID'] > 0) {
							$visibleToAll = $res['visibleToAll'];
							$rurl = $this->redirectUrl . "&subaction=listData&folderID=" . $res['parentID'];
						} else {
							$rurl = $this->redirectUrl . "&subaction=listData";
						}
				?>
						<button type="reset" class="btn btn-secondary back float-right ml-2" name="Cancel" data-url="<?php
					echo $rurl;
					?>"><i class="fas fa-level-up-alt"></i></button>
				<?php
					}
				}
				?>
			</div>
		</div>

		<!-- Create folder code -->
		<form name="frmAddFolder" action="<?php echo $this->addfolderaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
			<div class="modal fade" id="createFolder" tabindex="-1" role="dialog" aria-labelledby="createFolderTitle" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="createFolderTitle">New Folder</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form>
								<div class="form-group">
									<label for="recipient-name" class="col-form-label">Folder Name:</label>
									<input type="text" class="form-control" name="folderName" id="recipient-name" placeholder="Enter Folder Name" required>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
							<input type="hidden" name="parentID" <?php if (!empty($folderID)) { ?> value="<?php echo $folderID; ?>" <?php } else { ?> value="0" <?php } ?>>
							<input type="hidden" name="visibleToAll" value="<?php echo $visibleToAll; ?>">
							<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php if ($folderID > 0) {
							echo $this->redirectUrl . "&subaction=listData&folderID=" . $folderID;
						} else {
							echo $this->redirectUrl . "&subaction=listData";
						} ?>">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</form>

		<!-- Create file code -->
		<form name="frmAddFile" action="<?php echo $this->addfileaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
			<div class="modal fade" id="createFile" tabindex="-1" role="dialog" aria-labelledby="createFileTitle" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="createFileTitle">New File</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form>
								<div class="form-group">
									<label>Title:</label>
									<input type="text" name="fileName" class="form-control" placeholder="Enter File Name" required>
								</div>
								<div class="form-group">
									<label>Select File:</label>
									<input type="file" name="fileUrl" accept="image/*,video/*,audio/*,.pdf,.doc,.xls,.xlsx,.docx" id="fileUrl" class="form-control memberImage" required>
								</div>
								<div class="form-group">
									<label>File Type:</label>
									<select name="fileType" id="fileType" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Type</option>
										<?php echo $fileType; ?>
									</select>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
							<input type="hidden" name="folderID" <?php if (!empty($folderID)) { ?> value="<?php echo $folderID; ?>" <?php } else { ?> value="0" <?php } ?>>
							<input type="hidden" name="visibleToAll" value="<?php echo $visibleToAll; ?>">
							<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php if ($folderID > 0) {
							echo $this->redirectUrl . "&subaction=listData&folderID=" . $folderID;
							} else {
							echo $this->redirectUrl . "&subaction=listData";
							} ?>">Cancel</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-sm-12 mb-3">
								<?php
								$parentFolderName = "Complex Documents";
								$dataExists = false;
								if (!empty($folderID)) {
									$queryParent = pro_db_query("select folderName from complexFolderMaster where status = 1 and folderID = " . $folderID . " and complexID = " . $_SESSION['complexID']);
									$resParent = pro_db_fetch_array($queryParent);
									$parentFolderName = $resParent['folderName'];
								}
								?>
								<?php echo $parentFolderName; ?>
								<p class="mt-3" style="border-bottom:1px solid #ebedf2;"></p>
							</div>
						</div>

						<?php
						$queryFolder = pro_db_query("select * from complexFolderMaster where status = 1 and parentID = " . $folderID . " and complexID = " . $_SESSION['complexID']);
						$queryFolderRows = pro_db_num_rows($queryFolder);
						if ($queryFolderRows > 0) {
							$dataExists = true;
						?>
							<div class="row">
								<?php
								while ($res = pro_db_fetch_array($queryFolder)) {
								?>

									<!-- Edit Folder -->
									<form name="frmEditFolder" action="<?php echo $this->editfolderaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
										<div class="modal fade" id="editFolder<?php echo $res['folderID']; ?>" tabindex="-1" role="dialog" aria-labelledby="editFolderTitle" aria-hidden="true">
											<div class="modal-dialog modal-dialog-centered" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="editFolderTitle">Edit Folder</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<form>
															<div class="form-group">
																<label for="recipient-name" class="col-form-label">Folder Name:</label>
																<input type="text" class="form-control" name="folderName" id="existingFolderName" value="<?php echo $res['folderName']; ?>" placeholder="Enter Folder Name" required>
															</div>
														</form>
													</div>
													<div class="modal-footer">
														<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
														<input type="hidden" name="parentID" value="<?php echo $res['parentID']; ?>">
														<input type="hidden" name="folderID" value="<?php echo $res['folderID']; ?>">
														<input type="submit" class="btn btn-success" value="Update">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php if ($res['parentID'] > 0) {
														echo $this->redirectUrl . "&subaction=listData&folderID=" . $res['parentID'];
														} else {
														echo $this->redirectUrl . "&subaction=listData";
														} ?>">Cancel</button>
													</div>
												</div>
											</div>
										</div>
									</form>

									<div class="col-md-3">
										<div class="col-md-12 stretch-card ggate-doc-border" style="position: relative;">
											<a href="<?php echo $this->redirectUrl . "&subaction=listData&folderID=" . $res['folderID']; ?>" style="display : inherit; color:black; text-decoration: none;">
												<i class="mdi mdi-folder icon-md d-flex align-self-center mr-3 newolid"></i>
												<div class="align-self-center mt-3 py-3">
													<p>
														<?php echo $res['folderName']; ?>
													</p>
												</div>
											</a>
											<div style="position:absolute;right:10px;top: 5px;">
												<a href="" data-toggle="modal" data-target="#editFolder<?php echo $res['folderID']; ?>" title="Edit Folder"><i class="far fa-edit text-info"></i></a>
												&nbsp;
												<a href="index.php?controller=inventory&action=gallery&subaction=deleteFolder&parentID=<?php echo $folderID ?>&folderID=<?php echo $res['folderID']; ?>" title="Delete Folder"><i class="fe-trash-2 text-danger"></i></a>&nbsp;
												<?php if ($res['visibleToAll'] == 1) { ?>
													<a href="index.php?controller=inventory&action=gallery&subaction=folderVisibilty&parentID=<?php echo $folderID ?>&folderID=<?php echo $res['folderID']; ?>&visibleToAll=0" title="Hide from Society Members"><i class="fas fa-eye text-success"></i></a>
												<?php } else { ?>
													<a href="index.php?controller=inventory&action=gallery&subaction=folderVisibilty&parentID=<?php echo $folderID ?>&folderID=<?php echo $res['folderID']; ?>&visibleToAll=1" title="Show to Society Members"><i class="fas fa-eye-slash text-danger"></i></a>
												<?php } ?>
											</div>
										</div>
									</div>
								<?php
								}
								?>
							</div>
							<div class="row">
								<div class="col-sm-12 mb-3">
									<p class="mt-3" style="border-bottom:1px solid #ebedf2;"></p>
								</div>
							</div>
						<?php
						}
						?>

						<?php
						$queryFile = pro_db_query("select * from complexFileMaster where status = 1 and folderID = " . $folderID . " and complexID = " . $_SESSION['complexID']);
						$queryFileRows = pro_db_num_rows($queryFile);
						if ($queryFileRows > 0) {
							$dataExists = true;
						?>
							<div class="row">
								<?php
								while ($res = pro_db_fetch_array($queryFile)) {
									$fileType = "mdi-file";
									$fileDisplayURL = "assets/images/placeholder_file.png";
									switch ($res['fileType']) {
										case 1:
											$fileType = "mdi-file-image";
											$fileDisplayURL = $res['fileUrl'] ??
												"assets/images/placeholder_image.png";
											break;
										case 2:
											$fileType = "mdi-file-video";
											$fileDisplayURL = "assets/images/placeholder_video.png";
											break;
										case 3:
											$fileType = "mdi-file-pdf";
											$fileDisplayURL = "assets/images/placeholder_pdf.png";
											break;
										case 4:
											$fileType = "mdi-file-word";
											$fileDisplayURL = "assets/images/placeholder_doc.png";
											break;
										case 5:
											$fileType = "mdi-file-excel";
											$fileDisplayURL = "assets/images/placeholder_excel.png";
											break;
										case 6:
											$fileType = "mdi-file-music";
											$fileDisplayURL = "assets/images/placeholder_music.png";
											break;
										default:
											$fileType = "mdi-file";
											$fileDisplayURL = "assets/images/placeholder_file.png";
											break;
									}
								?>
									<div class="col-md-2 stretch-card">
										<div class="ggate-img-wraps">
											<span class="closes" title="Delete">
												<a href="index.php?controller=inventory&action=gallery&subaction=deleteFile&folderID=<?php echo $folderID ?>&fileID=<?php echo $res['fileID']; ?>" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
												</i></span>
											<a href="<?php echo $res['fileUrl']; ?>" style="display: inherit; color:black; text-decoration: none;" target="blank">
												<div class="card" style="width: 12rem;">
													<img src="<?php echo $fileDisplayURL; ?>" class="card-img-top ggate-doc-center" alt="<?php echo $res['fileName']; ?>">
													<div class="card-body">
														<h5 class="card-text">
															<i class="mdi <?php echo $fileType; ?> icon-sm align-self-center mr-2"></i>
															<span class="text-center"><?php echo $res['fileName']; ?></span>
														</h5>
													</div>
												</div>
											</a>
										</div>
									</div>
								<?php
								}
								?>
							</div>
							<div class="row">
								<div class="col-sm-12 mb-3">
									<p class="mt-3" style="border-bottom:1px solid #ebedf2;"></p>
								</div>
							</div>
						<?php
						}
						?>

						<?php
						if (!$dataExists) {
						?>
							<div class="col-lg-4 mx-auto text-center">
								<img class="display-1 mb-0" height="200" src="assets/images/nodata.png">
							</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
?>
