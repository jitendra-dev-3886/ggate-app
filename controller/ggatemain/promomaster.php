<?php
class promomaster
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
			$this->mediaType = "banners";
		} else {
			$this->mediaType = "banners-dev";
		}
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$isUpdate = generateStaticOptions(array("1" => "App Update", "0" => "Promotion"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Promo</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Version:</label>
									<input type="text" name="promoVersion" class="form-control" placeholder="Enter App Version" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Promotion Type:</label>
									<select id="isUpdate" name="isUpdate" class="form-control custom-select mr-sm-2" onchange="checkPromotionType()">
										<?php echo $isUpdate; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" id="promoTitle" name="promoTitle" class="form-control" placeholder="Enter Promo Title" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Promo Image:</label>
									<input type="file" name="promoLink" accept="image/*" id="promoLink" class="form-control promoLink">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label>Message:</label>
									<textarea name="promoMessage" class="form-control aeditor" rows="3"></textarea>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function checkPromotionType() {
				$isUpdate = document.getElementById('isUpdate').value;
				document.getElementById('promoTitle').value = ($isUpdate == 1) ? "app_update" : "";
				document.getElementById('promoTitle').disabled = $isUpdate == 1;
			}
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from ggatePromoDetails where promoID = " . (int)$_REQUEST['promoID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$isUpdate = generateStaticOptions(array("1" => "App Update", "0" => "Promotion"), $rs['isUpdate']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Promo</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Version:</label>
									<input type="text" name="promoVersion" class="form-control" value="<?php echo $rs['promoVersion']; ?>" placeholder="Enter App Version" required readonly>
								</div>
								<div class="form-group col-sm-2">
									<label>Promotion Type:</label>
									<select id="isUpdate" name="isUpdate" class="form-control custom-select mr-sm-2" disabled>
										<?php echo $isUpdate; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" id="promoTitle" name="promoTitle" class="form-control" value="<?php echo $rs['promoTitle']; ?>" placeholder="Enter Promo Title" required readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Promo Image:</label>
									<input type="file" name="promoLink" accept="image/*" id="promoLink" class="form-control promoLink">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label>Message:</label>
									<textarea name="promoMessage" class="form-control aeditor" rows="3"><?php echo $rs['promoMessage']; ?></textarea>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="promoID" value="<?php echo $rs['promoID']; ?>">
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

		$promoTitle = $_POST["promoTitle"];
		$imgPromoTitle = strtolower($promoTitle);
		$imgPromoTitle = str_replace(' ', '_', $imgPromoTitle);

		if (pro_db_perform('ggatePromoDetails', $formdata)) {
			$promoID = pro_db_insert_id();

			//dashboard log for itemtypeMaster
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "ggatemain";
			$dashboardlogdata['action'] = "promomaster";
			$dashboardlogdata['subAction'] = "addpromo";
			$dashboardlogdata['referenceID'] = $promoID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			//Upload Promo Image
			if (!empty($_FILES["promoLink"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$promoLink = $_FILES["promoLink"]["name"];
				$image = explode(".", $promoLink);
				$extension = end($image);

				if ($_FILES["promoLink"]["error"] > 0) {
					$msg = $_FILES["promoLink"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['promoLink']['tmp_name']);
					$objectName = "ico_promo_" . $imgPromoTitle . "_" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into ggatePromoDetails
						$wher = "promoID = " . $promoID;
						$imageData['promoLink'] = $finalImageName;
						pro_db_perform('ggatePromoDetails', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success p-3">Promo Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Promo Detail is not saved!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "promoID = " . $_POST['promoID'];
		$formdata = $_POST;

		$promoID = $_POST['promoID'];
		$promoTitle = $_POST["promoTitle"];
		$imgPromoTitle = strtolower($promoTitle);
		$imgPromoTitle = str_replace(' ', '_', $imgPromoTitle);

		if (pro_db_perform('ggatePromoDetails', $formdata, 'update', $whr)) {

			//dashboard log for itemtypeMaster
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "ggatemain";
			$dashboardlogdata['action'] = "promomaster";
			$dashboardlogdata['subAction'] = "editpromo";
			$dashboardlogdata['referenceID'] = $whr;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			//Upload Promo Image
			if (!empty($_FILES["promoLink"]["name"])) {
				$allowedTypes = array("gif", "jpeg", "jpg", "png");
				$promoLink = $_FILES["promoLink"]["name"];
				$image = explode(".", $promoLink);
				$extension = end($image);

				if ($_FILES["promoLink"]["error"] > 0) {
					$msg = $_FILES["promoLink"]["error"];
				} else {
					$imageRawData = file_get_contents($_FILES['promoLink']['tmp_name']);
					$objectName = "ico_promo_" . $imgPromoTitle . "_" . date('YmdHis') . "." . $extension;
					$imageName = $this->mediaType . "/" . $objectName;

					//Upload a file to the bucket.
					if (gcsUploadFile(GCLOUD_BUCKET, $imageRawData, $imageName)) {
						$finalImageName = GCLOUD_CDN_URL . $imageName;

						//Update into ggatePromoDetails
						$wher = "promoID = " . $promoID;
						$imageData['promoLink'] = $finalImageName;
						pro_db_perform('ggatePromoDetails', $imageData, 'update', $wher);
					}
				}
			}
			$msg = '<p class="bg-success p-3">Promo Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Promo Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update from ggatePromoDetails set status = 0 where itemTypeID = " . (int)$_GET['promoID'];

		if (pro_db_query($delsql)) {
			//dashboard log for itemtypeMaster
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "ggatemain";
			$dashboardlogdata['action'] = "promomaster";
			$dashboardlogdata['subAction'] = "deletepromo";
			$dashboardlogdata['referenceID'] = $_GET['itemTypeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Promo Detail has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Promo Detail Not deleted successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function notifyMembersAboutPromo()
	{
		$promoID = $_REQUEST['promoID'];

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
			'announcementType' => 0,
			'requestID' => $promoID,
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
		$msg = '<p class="bg-success p-3">App users have been notified successfully.</p>';
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>GGATE Promotions</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Promo</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="promomasterList" width="100%">
								<thead>
									<tr>
										<th width="7%">Version</th>
										<th width="10%">Promo Type</th>
										<th width="10%">Title</th>
										<th>Message</th>
										<th width="5%">Status</th>
										<th width="5%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="7%">Version</th>
										<th width="10%">Promo Type</th>
										<th width="10%">Title</th>
										<th>Message</th>
										<th width="5%">Status</th>
										<th width="5%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/promomasterList.php';
			$('#promomasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "ggatePromoDetails"
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