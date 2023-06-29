<?php
class poll
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
		$status = generateStaticOptions(array("0" => "Disable"));
		$pollType = generateStaticOptions(array("1" => "Poll", "2" => "Election"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Create Poll / Election</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Poll Type:</label>
									<select name="pollType" id="pollType" class="form-control custom-select mr-sm-2" required>
										<option value="">Select Type</option>
										<?php echo $pollType; ?>
									</select>
								</div>
								<div class="form-group col-sm-4">
									<label class="form-group" id="candidate" style="display:show">Enter Designation:</label>
									<label class="form-group" id="question" style="display:none">Enter Question:</label>
									<input type="text" name="pollQuestion" class="form-control">
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Ends On:</label>
										<input type="text" name="endDate" class="form-control eventTodayDateTime" placeholder="Ending Date" value="<?php echo date('Y-m-d', strtotime(' + 2 days')); ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label id="candidateOption1" style="display:show">Enter Candidate 1:</label>
										<label id="questionOption1" style="display:none">Enter Option 1:</label>
										<input type="text" name="pollOption[]" class="form-control">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label id="candidateOption2" style="display:show">Enter Candidate 2:</label>
										<label id="questionOption2" style="display:none">Enter Option 2:</label>
										<input type="text" name="pollOption[]" class="form-control">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label id="candidateOption3" style="display:show">Enter Candidate 3:</label>
										<label id="questionOption3" style="display:none">Enter Option 3:</label>
										<input type="text" name="pollOption[]" class="form-control">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label id="candidateOption4" style="display:show">Enter Candidate 4:</label>
										<label id="questionOption4" style="display:none">Enter Option 4:</label>
										<input type="text" name="pollOption[]" class="form-control">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label id="candidateOption5" style="display:show">Enter Candidate 5:</label>
										<label id="questionOption5" style="display:none">Enter Option 5:</label>
										<input type="text" name="pollOption[]" class="form-control">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
										<input type="hidden" name="startDate" value="<?php echo date('Y-m-d H:i:s'); ?>">
										<input type="hidden" name="blockID" value="0">					
										<input type="hidden" name="status" value="0">
										<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
				minDate: "today"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});

			$('#pollType').on('change', function() {
				if (this.value == '2') {
					$("#candidate").show();
					$("#question").hide();
					$("#candidateOption1").show();
					$("#questionOption1").hide();
					$("#candidateOption2").show();
					$("#questionOption2").hide();
					$("#candidateOption3").show();
					$("#questionOption3").hide();
					$("#candidateOption4").show();
					$("#questionOption4").hide();
					$("#candidateOption5").show();
					$("#questionOption5").hide();
					$("#candidate").prop('required', true);
					$("#question").prop('required', false);
					$("#candidateOption1").prop('required', true);
					$("#questionOption1").prop('required', false);
					$("#candidateOption2").prop('required', true);
					$("#questionOption2").prop('required', false);
					$("#candidateOption3").prop('required', true);
					$("#questionOption3").prop('required', false);
					$("#candidateOption4").prop('required', true);
					$("#questionOption4").prop('required', false);
					$("#candidateOption5").prop('required', true);
					$("#questionOption5").prop('required', false);
				} else {
					$("#candidate").hide();
					$("#question").show();
					$("#candidateOption1").hide();
					$("#questionOption1").show();
					$("#candidateOption2").hide();
					$("#questionOption2").show();
					$("#candidateOption3").hide();
					$("#questionOption3").show();
					$("#candidateOption4").hide();
					$("#questionOption4").show();
					$("#candidateOption5").hide();
					$("#questionOption5").show();
					$("#candidate").prop('required', false);
					$("#question").prop('required', true);
					$("#candidateOption1").prop('required', false);
					$("#questionOption1").prop('required', true);
					$("#candidateOption2").prop('required', false);
					$("#questionOption2").prop('required', true);
					$("#candidateOption3").prop('required', false);
					$("#questionOption3").prop('required', true);
					$("#candidateOption4").prop('required', false);
					$("#questionOption4").prop('required', true);
					$("#candidateOption5").prop('required', false);
					$("#questionOption5").prop('required', true);
				}
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from pollMaster where pollID = '" . (int)$_REQUEST['pollID'] . "'");
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("0" => "Disable"), $rs['status']);
		$pollType = generateStaticOptions(array("1" => "Poll", "2" => "Election"), $rs['pollType']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Poll / Election Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Type:</label>
									<select name="pollType" id="pollType" class="form-control custom-select mr-sm-2" required>
										<?php echo $pollType; ?>
									</select>
								</div>
								<div class="form-group col-sm-4">
									<label class="form-group" id="candidate" <?php if ($rs['pollType'] == 2) { ?> style="display:show" <?php } else { ?> style="display:none;" <?php } ?>>Enter Designation:</label>
									<label class="form-group" id="question" <?php if ($rs['pollType'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none;" <?php } ?>>Enter Question:</label>
									<input type="text" name="pollQuestion" class="form-control" value="<?php echo $rs['pollQuestion']; ?>" required>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Started On:</label>
										<input type="text" name="startDate" class="form-control" value="<?php echo date('Y-m-d', strtotime($rs['startDate'])); ?>" required readonly>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Ends On:</label>
										<input type="text" name="endDate" class="form-control eventTodayDateTime" value="<?php echo date('Y-m-d', strtotime($rs['endDate'])); ?>" required>
									</div>
								</div>
							</div>
							<div class="row">
								<?php
								$opSql = pro_db_query("select optionID, pollOption from pollOptions where pollID = " . $rs['pollID']);
								$ors = pro_db_fetch_arrays($opSql);
								$totalOptions = 5;

								for ($i = 0; $i < $totalOptions; $i++) {
									if ($i > count($ors) - 1) {
										$dummyOption['optionID'] = "0";
										$dummyOption['pollOption'] = "";
										$ors[] = $dummyOption;
									}

									$candidateStyleDisplay = "";
									if ($rs['pollType'] == 2) {
										$candidateStyleDisplay = "style='display:show';";
									} else {
										$candidateStyleDisplay = "style='display:none';";
									}

									$questionStyleDisplay = "";
									if ($rs['pollType'] == 1) {
										$questionStyleDisplay = "style='display:show';";
									} else {
										$questionStyleDisplay = "style='display:none';";
									}
									$option = $ors[$i]['optionID'];
									$optionValue = $ors[$i]['pollOption'];
									$isRequired = $i < 2 ? " required" : "";

									echo "<div class='col-sm-2'>
										<div class='form-group'>
											<label id='candidateOption" . ($i + 1) . "'" . $candidateStyleDisplay . ">Enter Candidate " . ($i + 1) . ":</label>
											<label id='questionOption" . ($i + 1) . "'" . $questionStyleDisplay . ">Enter Option " . ($i + 1) . ":</label>
											<input type='text' name=pollOption[][" . $option . "] class='form-control' value='" . $optionValue . "' " . $isRequired . ">
										</div>
									</div>";
								}
								?>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
										<input type="hidden" name="pollID" value="<?php echo $rs['pollID']; ?>">
										<input type="hidden" name="status" value="<?php echo $rs['status']; ?>">
										<input type="hidden" name="blockID" value="0">
										<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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
				minDate: "today"
			});
			// For Datetime Calendar
			$('.eventEndDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
				minDate: new Date().fp_incr(2)
			});

			$('#pollType').on('change', function() {
				if (this.value == '2') {
					$("#candidate").show();
					$("#question").hide();
					$("#candidateOption1").show();
					$("#questionOption1").hide();
					$("#candidateOption2").show();
					$("#questionOption2").hide();
					$("#candidateOption3").show();
					$("#questionOption3").hide();
					$("#candidateOption4").show();
					$("#questionOption4").hide();
					$("#candidateOption5").show();
					$("#questionOption5").hide();
					$("#candidate").prop('required', true);
					$("#question").prop('required', false);
					$("#candidateOption1").prop('required', true);
					$("#questionOption1").prop('required', false);
					$("#candidateOption2").prop('required', true);
					$("#questionOption2").prop('required', false);
					$("#candidateOption3").prop('required', true);
					$("#questionOption3").prop('required', false);
					$("#candidateOption4").prop('required', true);
					$("#questionOption4").prop('required', false);
					$("#candidateOption5").prop('required', true);
					$("#questionOption5").prop('required', false);
				} else {
					$("#candidate").hide();
					$("#question").show();
					$("#candidateOption1").hide();
					$("#questionOption1").show();
					$("#candidateOption2").hide();
					$("#questionOption2").show();
					$("#candidateOption3").hide();
					$("#questionOption3").show();
					$("#candidateOption4").hide();
					$("#questionOption4").show();
					$("#candidateOption5").hide();
					$("#questionOption5").show();
					$("#candidate").prop('required', false);
					$("#question").prop('required', true);
					$("#candidateOption1").prop('required', false);
					$("#questionOption1").prop('required', true);
					$("#candidateOption2").prop('required', false);
					$("#questionOption2").prop('required', true);
					$("#candidateOption3").prop('required', false);
					$("#questionOption3").prop('required', true);
					$("#candidateOption4").prop('required', false);
					$("#questionOption4").prop('required', true);
					$("#candidateOption5").prop('required', false);
					$("#questionOption5").prop('required', true);
				}
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		if (empty($formdata['blockID'])) {
			$formdata['blockID'] = 0;
		}
		if (isset($_POST['candidate'])) {
			$formdata['question'] = $_POST['candidate'];
		} else {
			$formdata['pollQuestion'] = $_POST['pollQuestion'];
		}
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$formdata['sortorder'] = get_sortorder('pollMaster', 'pollID');
		unset($formdata['pollOption']);
		if (pro_db_perform('pollMaster', $formdata)) {
			$pollID = pro_db_insert_id();

			//dashboard log for poll/election
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "poll";
			$dashboardlogdata['subAction'] = "addpoll/election";
			$dashboardlogdata['referenceID'] = $pollID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			foreach ($_POST['pollOption'] as $key => $value) {
				$insSql = pro_db_query("
					insert into pollOptions set
					pollID = '" . $pollID . "',
					pollOption = '" . $value . "'
				");
			}

			//Send Notification to all society members
			//Notifications
			if ($formdata['blockID'] == 0) {
				$members ="select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexPollElection, 0) as preference from memberActivity act
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

			$notificationType = "poll_module";
			if ($formdata['pollType'] == 1) {
				$notificationMessage = "Complex has added new poll: '" . $formdata['pollQuestion'] . "'. Kindly check and cast your vote.";
				$notificationAction = "poll_alert";
			} else {
				$notificationMessage = "Complex has added new election: '" . $formdata['pollQuestion'] . "'. Kindly check and cast your vote.";
				$notificationAction = "election_alert";
			}

			$notificationActionID = $pollID;

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

			$msg = '<p class="bg-success p-3">Poll/Election Detail is saved...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Poll/Election Detail is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$pollID = $_POST['pollID'];
		$whr = "";
		$whr = "pollID=" . $_POST['pollID'];
		$formdata = $_POST;
		if (empty($formdata['blockID'])) {
			$formdata['blockID'] = 0;
		}
		// unset($formdata['memberID']);

		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		unset($formdata['pollOption']);
		if (pro_db_perform('pollMaster', $formdata, 'update', $whr)) {

			//dashboard log for poll/election
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "community";
			$dashboardlogdata['action'] = "poll";
			$dashboardlogdata['subAction'] = "editpoll/election";
			$dashboardlogdata['referenceID'] = $_POST['pollID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			foreach ($_POST['pollOption'] as $key => $value) {
				foreach ($value as $k => $v) {
					if ($k == "0") {
						if (!empty($v)) {
							$insSql = pro_db_query("insert into pollOptions set pollID = '" . $pollID . "',
													pollOption = '" . $v . "'");
						}
					} else {
						if (!empty($v)) {
							$insSql = pro_db_query("update pollOptions set pollOption = '" . $v . "' where optionID = " . $k);
						} else {
							$insSql = pro_db_query("delete from pollOptions where optionID = " . $k);
						}
					}
				}
			}

			//Send Notification to all society members
			//Notifications
				if ($formdata['blockID'] == 0) {
				$members ="select act.memberID, act.deviceType, act.deviceToken, 
											coalesce(pref.complexPollElection, 0) as preference from memberActivity act
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

			$notificationType = "poll_module";
			if ($formdata['pollType'] == 1) {
				$notificationMessage = "Society has updated the poll: '" . $formdata['pollQuestion'] . "'. Kindly check and cast your vote.";
				$notificationAction = "poll_alert";
			} else {
				$notificationMessage = "Society has updated the election: '" . $formdata['pollQuestion'] . "'. Kindly check and cast your vote.";
				$notificationAction = "election_alert";
			}

			$notificationActionID = $_POST['pollID'];

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

			$msg = '<p class="bg-success p-3">Polls Detail is updated...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Polls Detail is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		//dashboard log for poll/election
		$dashboardlogdata = array();
		$dashboardlogdata['complexID'] = $_SESSION['complexID'];
		$dashboardlogdata['memberID'] = $_SESSION['memberID'];
		$dashboardlogdata['contorller'] = "community";
		$dashboardlogdata['action'] = "poll";
		$dashboardlogdata['status'] = 1;
		$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
		$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$delsql = "Delete from pollMaster where pollID = '" . (int)$_REQUEST['pollID'] . "'";
		if (pro_db_query($delsql)) {

			//dashboard log for poll/election	
			$dashboardlogdata['subAction'] = "deletepoll/election";
			$dashboardlogdata['referenceID'] = $_REQUEST['pollID'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$delpolopt = "Delete from pollOptions where pollID = '" . (int)$_REQUEST['pollID'] . "'";
			pro_db_query($delpolopt);
			$msg = '<p class="bg-success p-3">Poll/Election Detail is deleted...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Poll/Election Detail is not deleted!!!</p>';
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
				<h4>Complex Polls & Elections</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Poll / Election</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="btn-group" role="group" aria-label="Poll Type">
							<button type="button" class="btn btn-info fltPollType" data-type="1">Complex Polls</button>
							<button type="button" class="btn btn-warning fltPollType" data-type="2">Complex Elections</button>
						</div>
						<hr>
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="pollsList" width="100%">
								<thead>
									<tr>
										<th align="left">Block</th>
										<th align="left">Question</th>
										<th align="left">Starting Date</th>
										<th align="left">Ending Date</th>
										<th align="left">Poll Type</th>
										<th align="left">Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Block</th>
										<th align="left">Question</th>
										<th align="left">Starting Date</th>
										<th align="left">Ending Date</th>
										<th align="left">Poll Type</th>
										<th align="left">Status</th>
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
			var PollType = "1";
			var listURL = "helperfunc/pollsList.php?PollType=" + PollType;
			if ($("#pollsList").length > 0) {
				var table = $('#pollsList').dataTable({
					"processing": true,
					"ajax": {
						url: listURL, // json datasource
						type: "post", // type of method  , by default would be get
						error: function() { // error handling code
							$("#pollsList_processing").css("display", "none");
						}
					},
					"stateSave": true,
					"order": [],
					"deferRender": true,
					"iDisplayLength": 25
				});
			}
			$(document).on('click', 'button.fltPollType', function(e) {
				PollType = $(this).data('type');
				var listURL = "helperfunc/pollsList.php?PollType=" + PollType;
				table.api().ajax.url(listURL).load();
				table.fnDraw();
			});

			$(document).on('click', '.dellnk', function(e) {
				e.preventDefault();
				var primaryKey = $(this).attr('data-pk');
				var table_name = 'pollMaster';
				var field_name = 'pollID';
				var delLnk = "<?php echo $this->redirectUrl; ?>&subaction=delete&pollID=" + primaryKey;

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
							if (agree) {
								window.location.href = delLnk;
							} else {
								return false;
							}
						}
					},
				});
			});

			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "pollMaster"
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
	public function viewPoll()
	{
		$listformaction = $this->redirectUrl . "&subaction=listData";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4><?php echo $_REQUEST['question']; ?></h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $listformaction; ?>" class="btn btn-info float-right"><i class="fas fa-list-ul"></i>&nbsp;&nbsp;Back to Poll / Election</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="pollsdetailsList" width="100%">
								<thead>
									<tr>
										<th align="left">Option</th>
										<th align="left">Total Votes</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th align="left">Option</th>
										<th align="left">Total Votes</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			/* $(document).ready(function() { */
			var listURL = 'helperfunc/pollsdetailsList.php?pollID=<?php echo $_REQUEST["pollID"]; ?>';
			$('#pollsdetailsList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 25,
				"stateSave": true,
				"order": []
			});
			/* }); */
		</script>
<?php
	}
}
?>
