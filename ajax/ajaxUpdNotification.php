<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	/* Setting General Post Values */
	$fldValue = pro_db_real_escape_string(trim($_REQUEST['value']));
	$fldName = $_REQUEST['name'];
	$tblName = $_REQUEST['tblName'];
	/* Setting Primary Key Values */
	$pkdata = explode(":", $_POST['pk']);
	$pkfldName = trim($pkdata[0]);
	$pkfldValue = trim($pkdata[1]);
	/* Preparing Query to Update Records */
	echo $updqry = "update $tblName set $fldName = '$fldValue' where $pkfldName = '" . (int)$pkfldValue . "' ";
	if (pro_db_query($updqry)) {

		$userSql = pro_db_query("Select groupName from groupMaster where groupID = " . $_SESSION['groupID']);
		$userrs = pro_db_fetch_array($userSql);

		$members = "select memberID, memberDeviceType, memberDeviceToken from memberMaster where status = 1 and memberID = (select memberID from $tblName where $pkfldName = '" . (int)$pkfldValue . "')";
		$resMembers = pro_db_query($members);
		$rowsMembers = pro_db_num_rows($resMembers);
		if ($rowsMembers > 0) {
			while ($rowMember = $resMembers->fetch_assoc()) {
				$notificationMemberIDs[] = $rowMember["memberID"];
				if (!empty($rowMember["memberDeviceToken"])) {
					$notificationMemberTokens[] = $rowMember["memberDeviceToken"];
				}
			}
		}

		if ($fldValue == 1) {
			$requestStatus = "approved";
		} else {
			$requestStatus = "rejected";
		}
		
	 if ($tblName == "blockFloorOfficeMapping") {
			$notificationMessage = "'" . $_SESSION['username'] . "'-'" . $userrs['groupName'] . "' has " . $requestStatus . " the request of your property.";
			$notificationType = "flat_module";
			$notificationAction = "flat_respond_action";

			//blockFloorFlatMapping table changes
			$memberquery = pro_db_query("select memberID,officeID from blockFloorOfficeMapping where officeMappingID = " . (int)$pkfldValue);
			$memberResponse = pro_db_fetch_array($memberquery);
			
			$officesql = pro_db_query("select officeID from officeMaster where officeID =".$memberResponse['officeID']." and status =1");
			if(pro_db_num_rows($officesql) == 0){
				pro_db_query("update  blockFloorOfficeMapping bfom, officeMaster om set om.status = 1, om.complexID = ".$_SESSION['complexID']." where bfom.officeID = om.officeID and bfom.officeMappingID = '" . (int)$pkfldValue . "'");
			}
			
			$sql = pro_db_query("select memberID from blockFloorOfficeMapping where memberID = " . $memberResponse['memberID'] . " and isPrimary = 1");
			if (pro_db_num_rows($sql) == 0) {
				$updateflat = "update blockFloorOfficeMapping bfom, memberMaster mm set bfom.isPrimary = 1, mm.complexID = " . $_SESSION['complexID'] . " where bfom.memberID = mm.memberID and bfom.officeMappingID = '" . (int)$pkfldValue . "'";
				pro_db_query($updateflat);
			}
		} else if ($tblName == "memberApproverRequest") {
			$notificationMessage = "'" . $_SESSION['username'] . "'-'" . $userrs['groupName'] . "' has " . $requestStatus . " the request of your family member.";
			$notificationType = "member_module";
			$notificationAction = "member_respond_action";

			//memberApproverRequest table changes
			$updaterel = "update memberApproverRequest set status = " . $fldValue . " where requestID = " . (int)$pkfldValue;
			pro_db_query($updaterel);
		} else {
			$done;
		}
		$notificationActionID = (int)$pkfldValue;

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
		return "Error in updating record!!!";
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>
