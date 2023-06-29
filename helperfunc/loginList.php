<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from loginMaster where memberID > 0 and groupID > 4 and complexID =" . $_SESSION['complexID'] . " order by status, loginID");
	while ($res = pro_db_fetch_array($queryString)) {

		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Inactive";
		}

		$user = pro_db_query("SELECT * from groupMaster where groupID =" . $res['groupID'] . " ");
		$userres = pro_db_fetch_array($user);

		$pk = "userID:" . $res['userID'];
		$loginID = '<td>' . $res['loginID'] . '</td>';
		$userName = '<td>' . $res['userName'] . '</td>';
		$userGroup = '<td>' . $userres['groupName'] . '</td>';
		$userMobile = '<td>' . $res['userMobile'] . '</td>';
		$userEmail = '<td>' . $res['userEmail'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$Action = '<td><a href="index.php?controller=user&action=user&subaction=editForm&userID=' . $res['userID'] . '" data-toggle="tooltip" title="Edit User" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
				<a href="index.php?controller=user&action=user&subaction=resetPwdForm&userID=' . $res['userID'] . '" data-toggle="tooltip" title="Reset Password" ><i class="fe-refresh-cw text-danger"></i></a>&nbsp;&nbsp;
				<a href="index.php?controller=user&action=user&subaction=delete&userID=' . $res['userID'] . '&memberID=' . $res['memberID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a></td>';
		$result['aaData'][] = array("$userGroup", "$loginID", "$userName", "$userMobile", "$userEmail", "$status", "$Action");
	}
	// End While Loop

	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>