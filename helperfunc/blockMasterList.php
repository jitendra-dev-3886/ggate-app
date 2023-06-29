<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$statusArray = array("0" => "Inactive", "1" => "Active");
	if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
		$queryString = pro_db_query("SELECT * from blockMaster where complexID = " . $_SESSION['complexID'] . " and blockID =" . $_SESSION['blockID'] . " and (status = 1 or status = 0)");
	} else {
		$queryString = pro_db_query("SELECT * from blockMaster where complexID = " . $_SESSION['complexID'] . " and (status = 1 or status = 0)");
	}
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "blockID:" . $res['blockID'];
		$blockName = '<td>' . $res['blockName'] . '</td>';
		$noOfFloors = '<td>' . $res['noOfFloors'] . '</td>';
		$officePerFloor = '<td>' . $res['officePerFloor'] . '</td>';

		if ($res['status'] == 1) {
			$Status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
		} else {
			$Status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
		}

		$Action = '<td><a href="index.php?controller=complexmasters&action=blockmaster&subaction=editForm&blockID=' . $res['blockID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=complexmasters&action=blockmaster&subaction=delete&blockID=' . $res['blockID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$blockName", "$noOfFloors", "$officePerFloor", "$Status", "$Action");
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
