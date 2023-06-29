<?php
class notice
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Publish Notice</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="title" class="form-control" placeholder="Enter Title" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Tagline:</label>
										<input type="text" name="tagline" min="2021-02-01" class="form-control" placeholder="Enter Tagline" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control custom-select mr-sm-2">
											<option value="1">Enable</option>
											<option value="0">Disable</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Description:</label>
										<textarea name="noticeDescription" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID	" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<button type="submit" id="start_button" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
				minDate: "today"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from noticeMaster where noticeID = " . (int)$_REQUEST['noticeID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Edit Notice</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="title" value="<?php echo $rs['title']; ?>" class="form-control" placeholder="News Title" required>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Tagline:</label>
										<input type="text" name="tagline" value="<?php echo $rs['tagline']; ?>" class="form-control" placeholder="News Tagline" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Status:</label>
										<select name="status" class="form-control custom-select mr-sm-2">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Description:</label>
										<textarea name="noticeDescription" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"><?php echo $rs['noticeDescription']; ?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID	" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<input type="hidden" name="noticeID" value="<?php echo (int)$rs['noticeID']; ?>">
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
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: "today"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['noticeType'] = 2;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['sortorder'] = get_sortorder('noticeMaster', 'noticeID');

		if (pro_db_perform('noticeMaster', $formdata)) {
			$noticeID = pro_db_insert_id();

			//dashboard log for notice
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "notice";
			$dashboardlogdata['subAction'] = "addNotice";
			$dashboardlogdata['referenceID'] = $noticeID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$members = "select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexNotice, 0) as preference from memberActivity act
											left join notificationPreferences pref on act.memberID = pref.memberID 
											where act.status = 4 and act.complexID = " . $_SESSION['complexID'] . " and act.memberID != " . $_SESSION['memberID'];
			$resMembers = pro_db_query($members);
			$rowsMembers = pro_db_num_rows($resMembers);
			if ($rowsMembers > 0) {
				while ($rowMember = $resMembers->fetch_assoc()) {
					$notificationMemberIDs[] = $rowMember["memberID"];
					if (!empty($rowMember["deviceToken"])) {
						$notificationMemberTokens[] =
							$rowMember["deviceToken"];
					}
				}
			}

			$notificationMessage = "Society has created new notice: '" . $formdata['title'] . "'. Kindly check the notice.";
			$notificationType = "notice_module";
			$notificationAction = "notice_alert";
			$notificationActionID = $noticeID;

			//Enter Notifications
			for ($i = 0; $i < count($notificationMemberIDs); $i++) {
				$arrNotificationParams = array(
					"memberID" => $notificationMemberIDs[$i],
					"memberType" => 1,
					"complexID" => $_SESSION['complexID'],
					"notificationType" => $notificationType,
					"notificationMessage" => $notificationMessage,
					"actionType" => $notificationAction,
					"actionID" => $notificationActionID,
					"userID" => $_SESSION['memberID'],
					"userType" => 1,
					"isRead" => 0,
					"createdate" => date('Y-m-d H:i:s'),
					"modifieddate" => date('Y-m-d H:i:s'),
					"remote_ip" => $_SERVER['REMOTE_ADDR'],
					"status" => 1
				);
				pro_db_perform('notifications', $arrNotificationParams, 'insert');
			}

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
				'tokens' => $notificationMemberTokens,
				'message' => $notificationMessage,
				'type' => $notificationType,
				'action' => $notificationAction,
				'actionID' => $notificationActionID
			];

			$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "notification";

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

			$msg = '<p class="bg-success p-3">Notice has been saved.</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Notice has not been saved.</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "noticeID=" . $_POST['noticeID'];
		$formdata = $_POST;
		unset($formdata['memberID']);
	
		$slug = pro_SeoSlug(pro_db_real_escape_string($formdata['title']));

		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('noticeMaster', $formdata, 'update', $whr)) {

			//dashboard log for notice
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "notice";
			$dashboardlogdata['subAction'] = "editNotice";
			$dashboardlogdata['referenceID'] = $_POST['noticeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-info p-3">Notice has been updated.</p>';

			//Notifications
			$members = "select act.memberID, act.deviceType, act.deviceToken, 
									coalesce(pref.complexNotice, 0) as preference from memberActivity act
									left join notificationPreferences pref on act.memberID = pref.memberID 
									where act.status = 4 and act.complexID = " . $_SESSION['complexID'] . " and act.memberID != " . $_SESSION['memberID'];
			$resMembers = pro_db_query($members);
			$rowsMembers = pro_db_num_rows($resMembers);
			if ($rowsMembers > 0) {
				while ($rowMember = $resMembers->fetch_assoc()) {
					$notificationMemberIDs[] = $rowMember["memberID"];
					if (!empty($rowMember["deviceToken"])) {
						$notificationMemberTokens[] =
							$rowMember["deviceToken"];
					}
				}
			}

			$notificationMessage = "Society has updated the notice: '" . $formdata['title'] . "'. Kindly check the notice.";
			$notificationType = "notice_module";
			$notificationAction = "notice_alert";
			$notificationActionID = $_POST['noticeID'];

			//Enter Notifications
			for ($i = 0; $i < count($notificationMemberIDs); $i++) {
				$arrNotificationParams = array(
					"memberID" => $notificationMemberIDs[$i],
					"memberType" => 1,
					"complexID" => $_SESSION['complexID'],
					"notificationType" => $notificationType,
					"notificationMessage" => $notificationMessage,
					"actionType" => $notificationAction,
					"actionID" => $notificationActionID,
					"userID" => $_SESSION['memberID'],
					"userType" => 1,
					"isRead" => 0,
					"createdate" => date('Y-m-d H:i:s'),
					"modifieddate" => date('Y-m-d H:i:s'),
					"remote_ip" => $_SERVER['REMOTE_ADDR'],
					"status" => 1
				);
				pro_db_perform('notifications', $arrNotificationParams, 'insert');
			}

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
				'tokens' => $notificationMemberTokens,
				'message' => $notificationMessage,
				'type' => $notificationType,
				'action' => $notificationAction,
				'actionID' => $notificationActionID
			];

			$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "notification";

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
		} else {
			$msg = '<p class="bg-danger p-3">Notice has not been updated.</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from noticeMaster where noticeID = '" . (int)$_REQUEST['noticeID'] . "'";
		if (pro_db_query($delsql)) {

			//dashboard log for notice
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "notice";
			$dashboardlogdata['subAction'] = "deleteNotice";
			$dashboardlogdata['referenceID'] = $_REQUEST['noticeID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Notice has been deleted.</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Notice has not been deleted.</p>';
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
				<h4>Complex Notices</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Notice</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<hr>
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="noticeList" style="width:100%">
								<thead>
									<tr>
										<th width="10%">Notice Date</th>
										<th width="15%">Notice Title</th>
										<th>Description</th>
										<th>Image</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Notice Date</th>
										<th>Notice Title</th>
										<th>Description</th>
										<th>Image</th>
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
			var listURL = "helperfunc/noticeList.php";
			if ($("#noticeList").length > 0) {
				var table = $('#noticeList').dataTable({
					"ajax": listURL,
					"deferRender": true,
					"stateSave": true,
					"iDisplayLength": 25,
					"order": []
				});
			}

			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'noticeList';
				var field_name = 'noticeID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&noticeID=" + primaryKey;

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
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "newsDetails"
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
