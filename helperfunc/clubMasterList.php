<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("SELECT  * from clubMaster where status != 126 and societyID =" . $_SESSION['societyID']);

	$statusArray = array("0" => "Inactive", "1" => "Active");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "clubID:" . $res['clubID'];

		$clubTitle = '<td>' . $res['clubTitle'] . '</td>';
		$clubDescription = '<td>' . $res['clubDescription'] . '</td>';
		$capacity = '<td>' . $res['capacity'] . '</td>';

		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$Action = '<td><a href="index.php?controller=amenities&action=clubmaster&subaction=editForm&clubID=' . $res['clubID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=amenities&action=clubmaster&subaction=delete&clubID=' . $res['clubID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$clubTitle", "$clubDescription", "$capacity", "$status", "$Action");
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