<?php
class clubmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $publishformaction;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->publishformaction = $this->redirectUrl . "&subaction=publish";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("0" => "Disable"));
		$meetingType = generateStaticOptions(array("2" => "Committee", "1" => "Complex"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Meeting</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="title" class="form-control" placeholder="Meeting Title" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Date:</label>
										<input type="text" name="dateTime" class="form-control eventTodayDateTime" placeholder="" value="<?php echo date('Y-m-d H:i:s'); ?>">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Place:</label>
										<input type="text" name="place" class="form-control" placeholder="Meeting Place" required>
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Type:</label>
									<select name="meetingType" id="type" class="form-control custom-select mr-sm-2" required>
										<option value="">Select Meeting Type</option>
										<?php echo $meetingType; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Agenda:</label>
										<textarea name="agenda" class="form-control aeditor" rows="3"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<input type="hidden" name="blockID" value="0">
									<input type="hidden" name="status" value="0">
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
			$('.eventTodayDateTime').flatpickr({
				enableTime: true,
				dateFormat: "Y-m-d H:i:00",
				minDate: "today"
			});
		</script>

	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from meetingMaster where meetingID = " . (int)$_REQUEST['meetingID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$meetingType = generateStaticOptions(array("2" => "Committee", "1" => "Complex"), $rs['meetingType']);
		$publish = generateStaticOptions(array("0" => "Not Published", "1" => "Committee", "2" => "Society"), $rs['publish']);
	?>
		<div class="row">
			<div class="col-sm-6 py-3 mt-2">
				<h4>Edit Meeting Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Title:</label>
										<input type="text" name="title" class="form-control" value="<?php echo $rs['title']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Date:</label>
										<input type="text" name="dateTime" class="form-control eventTodayDateTime" value="<?php echo $rs['dateTime']; ?>" required>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Place:</label>
										<input type="text" name="place" class="form-control" value="<?php echo $rs['place']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="form-group col-sm-2">
									<label>Type:</label>
									<select name="meetingType" id="type" class="form-control custom-select mr-sm-2" required>
										<option value="">Select Meeting Type</option>
										<?php echo $meetingType; ?>
									</select>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label>Agenda:</label>
										<textarea name="agenda" class="form-control aeditor" rows="3"><?php echo $rs['agenda']; ?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<input type="hidden" name="meetingID" value="<?php echo $rs['meetingID']; ?>">
									<input type="hidden" name="blockID" value="0">
									<input type="hidden" name="status" value="0">
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
			$('.eventTodayDateTime').flatpickr({
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				minDate: "today"
			});
		</script>
	<?php
	}

	public function publishMeeting()
	{
		$qry = pro_db_query("select * from meetingMaster where meetingID = " . (int)$_REQUEST['meetingID']);
		$rs = pro_db_fetch_array($qry);
		$publish = generateStaticOptions(array("1" => "Committee", "2" => "Society"), $rs['publish']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4><?php echo $rs['title']; ?></h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->publishformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-9">
									<div class="form-group">
										<label>Minutes Of Meeting:</label>
										<textarea name="minutesOfMeetings" class="form-control aeditor" rows="3"><?php echo $rs['minutesOfMeetings']; ?></textarea>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Publish Meeting to:</label>
									<select name="publish" id="type" class="form-control custom-select mr-sm-2">
										<?php echo $publish; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
									<input type="hidden" name="meetingID" value="<?php echo $rs['meetingID']; ?>">
									<input type="hidden" name="title" value="<?php echo $rs['title']; ?>">
									<input type="hidden" name="dateTime" value="<?php echo $rs['dateTime']; ?>">
									<input type="hidden" name="blockID" value="<?php echo $rs['blockID']; ?>">
									<input type="hidden" name="agenda" value="<?php echo $rs['agenda']; ?>">
									<button type="submit" class="btn btn-success">Publish</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
		if (empty($formdata['blockID'])) {
			$formdata['blockID'] = 0;
		}
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		if (pro_db_perform('meetingMaster', $formdata)) {
			$meetingID = pro_db_insert_id();

			//dashboard log for meeting
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "meetings";
			$dashboardlogdata['subAction'] = "addMeeting";
			$dashboardlogdata['referenceID'] = $meetingID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Meeting is added successfully...</p>';

			//Notifications
			if ($formdata['meetingType'] == 1) {

				if ($formdata['blockID'] == 0) {
					$members = "select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexMeeting, 0) as preference from memberActivity act
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
				}
			} else {
				$currentDate = date('Y-m-d H:i:s');
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$remote_ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$remote_ip = $_SERVER['REMOTE_ADDR'];
				}

				//Committee member himself
				$meetingAttendanceParams = array(
					"meetingID" => $meetingID,
					"memberID" => $_SESSION['memberID'],
					"attendeeID" => $_SESSION['memberID'],
					"status" => 1,
					"createdate" => $currentDate,
					"modifieddate" => $currentDate,
					"remote_ip" => $remote_ip
				);
				pro_db_perform('meetingAttendees', $meetingAttendanceParams, 'insert');

				//Committee Members
				if ($formdata['blockID'] == 0) {
					$committeeMembers = "select memberID from designationMemberMapping where status = 1 and
										memberID != " . $_SESSION['memberID'] . " and complexID = " . $_SESSION['complexID'] . " group by memberID";
					$resCommitteeMembers = pro_db_query($committeeMembers);
					$rowsCommitteeMembers = pro_db_num_rows($resCommitteeMembers);
					if ($rowsCommitteeMembers > 0) {
						while ($row = $resCommitteeMembers->fetch_assoc()) {
							//Add Attendee
							$meetingAttendanceParams = array(
								"meetingID" => $meetingID,
								"memberID" => $_SESSION['memberID'],
								"attendeeID" => $row["memberID"],
								"status" => 0,
								"createdate" => $currentDate,
								"modifieddate" => $currentDate,
								"remote_ip" => $remote_ip
							);
							pro_db_perform('meetingAttendees', $meetingAttendanceParams, 'insert');
						}
					}

					$members = "select act.memberID, act.deviceType, act.deviceToken,
                                coalesce(pref.complexMeeting, 0) as preference from memberActivity act
                                left join notificationPreferences pref on act.memberID = pref.memberID
                                left join designationMemberMapping desg on act.memberID = desg.memberID
                                where desg.complexID = " . $_SESSION['complexID'] . " and act.status = 4 and act.memberID != " . $_POST['memberID'] . "
                                group by act.memberID";
					$resMembers = pro_db_query($members);
					$rowsMembers = pro_db_num_rows($resMembers);
					if ($rowsMembers > 0) {
						while ($rowMember = $resMembers->fetch_assoc()) {
							$notificationMemberIDs[] = $rowMember["memberID"];
							if (!empty($rowMember["deviceToken"])) {
								$notificationMemberTokens[] = $rowMember["deviceToken"];
							}
						}
					}
				}
			}

			$notificationMessage = "Complex has added new meeting: '" . $formdata['title'] . "'. Kindly check meeting details.";
			$notificationType = "meeting_module";
			$notificationAction = "meeting_alert";
			$notificationActionID = $meetingID;

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
		} else {
			$msg = '<p class="bg-danger p-3">Meeting Detail is not added!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "meetingID=" . $_POST['meetingID'];
		$formdata = $_POST;
		if (empty($formdata['blockID'])) {
			$formdata['blockID'] = 0;
		}
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		unset($formdata['memberID']);

		$qry = pro_db_query("select * from meetingMaster where meetingID = " . (int)$_POST['meetingID']);

		while ($res = pro_db_fetch_array($qry)) {
			$metype = $res['meetingType'];
			if ($metype == $formdata['meetingType']) {
				if ($formdata['meetingType'] == 1) {
					if ($formdata['blockID'] == 0) {
						$members = "select act.memberID, act.deviceType, act.deviceToken, 
									coalesce(pref.complexMeeting, 0) as preference from memberActivity act
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
					}
				} else {
					if ($formdata['blockID'] == 0) {
						$members = "select act.memberID, act.deviceType, act.deviceToken,
                                coalesce(pref.complexMeeting, 0) as preference from memberActivity act
                                left join notificationPreferences pref on act.memberID = pref.memberID
                                left join designationMemberMapping desg on act.memberID = desg.memberID
                                where desg.complexID = " . $_SESSION['complexID'] . " and act.status = 4 and act.memberID != " . $_POST['memberID'] . "
                                group by act.memberID";
						$resMembers = pro_db_query($members);
						$rowsMembers = pro_db_num_rows($resMembers);
						if ($rowsMembers > 0) {
							while ($rowMember = $resMembers->fetch_assoc()) {
								$notificationMemberIDs[] = $rowMember["memberID"];
								if (!empty($rowMember["deviceToken"])) {
									$notificationMemberTokens[] = $rowMember["deviceToken"];
								}
							}
						}
					}
				}
			} else {
				if ($formdata['meetingType'] == 1) {
					//Delete entries
					$delMembers = pro_db_query("delete from meetingAttendees where meetingID = " . $_POST['meetingID']);

					//Notifications
					if ($formdata['blockID'] == 0) {
						$members = "select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexMeeting, 0) as preference from memberActivity act
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
					}
				} else {
					$currentDate = date('Y-m-d H:i:s');
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$remote_ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
						$remote_ip = $_SERVER['REMOTE_ADDR'];
					}

					//Committee member himself
					$meetingAttendanceParams = array(
						"meetingID" => $_POST['meetingID'],
						"memberID" => $_SESSION['memberID'],
						"attendeeID" => $_SESSION['memberID'],
						"status" => 1,
						"createdate" => $currentDate,
						"modifieddate" => $currentDate,
						"remote_ip" => $remote_ip
					);
					pro_db_perform('meetingAttendees', $meetingAttendanceParams, 'insert');

					//Committee Members
					if ($formdata['blockID'] == 0) {
						$committeeMembers = "select memberID from designationMemberMapping where status = 1 and
											memberID != " . $_SESSION['memberID'] . " and complexID = " . $_SESSION['complexID'] . " group by memberID";
						$resCommitteeMembers = pro_db_query($committeeMembers);
						$rowsCommitteeMembers = pro_db_num_rows($resCommitteeMembers);
						if ($rowsCommitteeMembers > 0) {
							while ($row = $resCommitteeMembers->fetch_assoc()) {
								//Add Attendee
								$meetingAttendanceParams = array(
									"meetingID" => $_POST['meetingID'],
									"memberID" => $_SESSION['memberID'],
									"attendeeID" => $row["memberID"],
									"status" => 0,
									"createdate" => $currentDate,
									"modifieddate" => $currentDate,
									"remote_ip" => $remote_ip
								);
								pro_db_perform('meetingAttendees', $meetingAttendanceParams, 'insert');
							}
						}

						$members = "select act.memberID, act.deviceType, act.deviceToken,
											coalesce(pref.complexMeeting, 0) as preference from memberActivity act
											left join notificationPreferences pref on act.memberID = pref.memberID
											left join designationMemberMapping desg on act.memberID = desg.memberID
											where desg.complexID = " . $_SESSION['complexID'] . " and act.status = 4 and act.memberID != " . $_POST['memberID'] . "
											group by act.memberID";
						$resMembers = pro_db_query($members);
						$rowsMembers = pro_db_num_rows($resMembers);
						if ($rowsMembers > 0) {
							while ($rowMember = $resMembers->fetch_assoc()) {
								$notificationMemberIDs[] = $rowMember["memberID"];
								if (!empty($rowMember["deviceToken"])) {
									$notificationMemberTokens[] = $rowMember["deviceToken"];
								}
							}
						}
					}
				}
			}
		}
		$notificationMessage = "Society has updated the meeting: '" . $formdata['title'] . "'. Kindly check meeting details.";
		$notificationType = "meeting_module";
		$notificationAction = "meeting_alert";
		$notificationActionID = $_POST['meetingID'];

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

		if (pro_db_perform('meetingMaster', $formdata, 'update', $whr)) {

			//dashboard log for meeting
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "meetings";
			$dashboardlogdata['subAction'] = "editMeeting";
			$dashboardlogdata['referenceID'] = $_POST['meetingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Meeting Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Meeting Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function publish()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "meetingID=" . $_POST['meetingID'];
		$formdata = $_POST;

		unset($formdata['memberID']);
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$meetingParams = array(
			"minutesOfMeetings" => $_POST['minutesOfMeetings'],
			"publish" => $_POST['publish'],
			"modifieddate" => date('Y-m-d H:i:s'),
			"remote_ip" => $_SERVER['REMOTE_ADDR'],
			"status" => 1
		);
		if (pro_db_perform('meetingMaster', $meetingParams, 'update', $whr)) {

			//dashboard log for meeting
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "meetings";
			$dashboardlogdata['subAction'] = "publishMeeting";
			$dashboardlogdata['referenceID'] = $_POST['meetingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			if ($formdata['publish'] == 1) {
				//Notifications
				//Committee Members
				if ($formdata['blockID'] == 0) {
					$members = "select act.memberID, act.deviceType, act.deviceToken,
											coalesce(pref.complexMeeting, 0) as preference from memberActivity act
											left join notificationPreferences pref on act.memberID = pref.memberID
											left join designationMemberMapping desg on act.memberID = desg.memberID
											where desg.complexID = " . $_SESSION['complexID'] . " and act.status = 4 and act.memberID != " . $_POST['memberID'] . "
											group by act.memberID";
					$resMembers = pro_db_query($members);
					$rowsMembers = pro_db_num_rows($resMembers);
					if ($rowsMembers > 0) {
						while ($rowMember = $resMembers->fetch_assoc()) {
							$notificationMemberIDs[] = $rowMember["memberID"];
							if (!empty($rowMember["deviceToken"])) {
								$notificationMemberTokens[] = $rowMember["deviceToken"];
							}
						}
					}
				}
				$notificationMessage = "Meeting has ended. Kindly Check meeting details.";
				$notificationType = "meeting_module";
				$notificationAction = "meeting_publish";
				$notificationActionID = $_POST['meetingID'];
			} else if ($formdata['publish'] == 2) {
				pro_db_perform('newsDetails', array(
					"memberID" => $_SESSION['memberID'],
					"newsTypeID" => 3,
					"complexID" => $_SESSION['complexID'],
					"blockID" => 0,
					"newsTitle" => $_POST['title'],
					"newsDate" => $_POST['dateTime'],
					"newsTagline" => $_POST['agenda'],
					"newsDescription" => $_POST['minutesOfMeetings'],
					"latestNews" => 1,
					"userName" => $_SESSION['userName'],
					"status" => 1,
					"createdate" => date('Y-m-d H:i:s'),
					"modifieddate" => date('Y-m-d H:i:s'),
					"remote_ip" => $_SERVER['REMOTE_ADDR']
				), 'insert');
				$noticeID = pro_db_insert_id();

				//Notifications
				if ($formdata['blockID'] == 0) {
					$members = "select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexMeeting, 0) as preference from memberActivity act
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
				}

				$notificationMessage = "Society has issued new notice: '" . $_POST['title'] . "'. Kindly check the notice.";
				$notificationType = "notice_module";
				$notificationAction = "notice_alert";
				$notificationActionID = $noticeID;
			} else {
				$formdata['publish'];
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
			$msg = '<p class="bg-success p-3">Meeting Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Meeting Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from meetingMaster where meetingID = '" . (int)$_REQUEST['meetingID'] . "'";
		if (pro_db_query($delsql)) {

			//dashboard log for meeting
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "meetings";
			$dashboardlogdata['subAction'] = "deleteMeeting";
			$dashboardlogdata['referenceID'] = $_REQUEST['meetingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Meeting Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Meeting Detail is not deleted!!!</p>';
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
				<h4>List of Meetings</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Meeting</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="meetingsList">
								<thead>
									<tr>
										<th>Block</th>
										<th width="15%" align="left">Title</th>
										<th align="left">Agenda</th>
										<th>DateTime</th>
										<th>Place</th>
										<th>Type</th>
										<th width="5%">Publish Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Block</th>
										<th width="15%" align="left">Title</th>
										<th align="left">Agenda</th>
										<th>DateTime</th>
										<th>Place</th>
										<th>Type</th>
										<th>Publish Status</th>
										<th width="10%">Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--Edit club master model--->
		<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		     <table class="table table-striped">
					<thead>
						<tr>
							<th>Block</th>
							<th>Title</th>
							<th>Agenda</th>
							<th>DateTime</th>
							<th>Place</th>
							<th>Type</th>
							<th>Publish Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<td> Jitendra</td>
						<td>Kashyap</td>
						<td>Neeraj</td>
						<td>Neerav</td>
						<td>Kalpesh</td>
						<td>Jitendra</td>
						<td>Jitendra</td>
						<td>Jitendra</td>
					</tbody>
					<tfoot>
						<tr>
							<th>Block</th>
							<th>Title</th>
							<th>Agenda</th>
							<th>DateTime</th>
							<th>Place</th>
							<th>Type</th>
							<th>Publish Status</th>
							<th>Action</th>
						</tr>
					</tfoot>
				</table>
		    </div>
		  </div>
		</div>
		<script>
			var listURL = 'helperfunc/meetingsList.php';
			$('#meetingsList').dataTable({
				"ajax": listURL,
				"stateSave": true,
				"deferRender": true,
				"iDisplayLength": 25
			});
			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'meetingMaster';
				var field_name = 'meetingID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&meetingID=" + primaryKey;

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
					"tblName": "meetingMaster"
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

	public function meetingAttendees()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4><?php $title = $_REQUEST['title'];
					$publish = $_REQUEST['publish'];
					echo "$title - $publish"; ?></h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Meetings</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable mal" id="meetingattendeesList" width="100%">
								<thead>
									<tr>
										<th align="left">Resident</th>
										<th align="left">Residence Number</th>
										<th align="left">Committee Role</th>
										<th align="left">Response</th>
										<th align="left">Attendance</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Resident</th>
										<th align="left">Residence Number</th>
										<th align="left">Committee Role</th>
										<th align="left">Response</th>
										<th align="left">Attendance</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/meetingattendeesList.php?meetingID=<?php echo $_REQUEST["meetingID"]; ?>';
			$('#meetingattendeesList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});

			$('.mal').editable({
				selector: 'a.eattendance',
				params: {
					"tblName": "meetingAttendees"
				},
				source: [{
					value: '1',
					text: 'Present'
				}, {
					value: '0',
					text: 'Absent'
				}]
			});
		</script>
	<?php
	}

	public function meetingDetails()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4><?php echo $_REQUEST['title'];; ?></h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Meetings</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="meetingdetailsList" width="100%">
								<thead>
									<tr>
										<th align="left">Block </th>
										<th align="left">Publisher</th>
										<th align="left">Minutes of Meeting</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Block </th>
										<th align="left">Publisher</th>
										<th align="left">Minutes of Meeting</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/meetingdetailsList.php?meetingID=<?php echo $_REQUEST["meetingID"]; ?>';
			$('#meetingdetailsList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"stateSave": true,
				"iDisplayLength": 25
			});
		</script>
<?php
	}
}
?>