<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$date = date('Y-m-d H:i:s');
	$result = array('aaData' => array());
	$statusArray = array("0" => "Inactive", "1" => "Active");
	$pollTypeArray = array("1" => "Poll", "2" => "Election");

	if ($_REQUEST['PollType'] == 1) {
		$queryString = pro_db_query("SELECT pm.*, b.blockName, pm.blockID from pollMaster pm left join blockMaster b on pm.blockID = b.blockID where pm.status != 126 and pm.pollType = 1 and pm.complexID = " . $_SESSION['complexID'] . "  order by pm.pollID desc");
	} else {
		$queryString = pro_db_query("SELECT pm.*, b.blockName, pm.blockID from pollMaster pm left join blockMaster b on pm.blockID = b.blockID where pm.status != 126 and pm.pollType = 2 and pm.complexID = " . $_SESSION['complexID'] . "  order by pm.pollID desc");
	}

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "pollID:" . $res['pollID'];
		if ($res['blockID'] == 0) {
			$blockName = '<td>' . "All Blocks" . '</td>';
		}
		if ($res['pollType'] == 1) {
			$pollType = '<td><i class="badge badge-info">' . $pollTypeArray[$res['pollType']] . '</i></td>';
		} else {
			$pollType = '<td><i class="badge badge-danger">' . $pollTypeArray[$res['pollType']] . '</i></td>';
		}
		if ($res['status'] == 1) {
			$finished = '<td><i class="badge badge-success">Finished</i></td>';
			$Action = '<td><a href="index.php?controller=community&action=poll&subaction=viewPoll&pollQuestion=' . $res['pollQuestion'] . '&pollID=' . $res['pollID'] . '" title="View Details"><i class="fas fa-poll-h text-warning"></i></a></td>';
		} else {
			$finished = '<td><i class="badge badge-secondary">On-Going</i></td>';
			$Action = '<td><a href="index.php?controller=community&action=poll&subaction=editForm&pollID=' . $res['pollID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=community&action=poll&subaction=delete&pollID=' . $res['pollID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		}
		$Question = '<td>' . $res['pollQuestion'] . '</td>';
		$startDate = '<td>' . date('d M Y - H:i A', strtotime($res['startDate'])) . '</td>';
		$endDate = '<td>' . date('d M Y - H:i A', strtotime($res['endDate'])) . '</td>';
		$pollType = '<td>' . $pollTypeArray[$res['pollType']] . '</td>';
		$pollStatus = '<td>' . $finished . '</td>';
		$result['aaData'][] = array("$blockName", "$Question", "$startDate", "$endDate", "$pollType", "$pollStatus", "$Action");
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
