<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$queryString = pro_db_query("SELECT po.pollOption, count(pr.voteID) as totalVotes FROM pollResponse pr
								join pollOptions po on pr.optionID = po.optionID where po.pollID = " . $_REQUEST['pollID'] . " 
								group by po.optionID order by totalVotes desc");
	while ($res = pro_db_fetch_array($queryString)) {
		$name = '<td>' . $res['pollOption'] . '</td>';
		$totalVotes = '<td>' . $res['totalVotes'] . '</td>';
		$result['aaData'][] = array("$name", "$totalVotes");
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
