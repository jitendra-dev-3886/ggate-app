<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$queryString = pro_db_query("SELECT * from complaintMaster");
	$statusArray = array("0" => "Disabled", "1" => "Active");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "complaintID:" . $res['complaintID'];
		$complaintType = '<td>' . $res['complaintType'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$Action = '<td><a href="index.php?controller=components&action=complaintmaster&subaction=editForm&complaintID=' . $res['complaintID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=components&action=complaintmaster&subaction=delete&complaintID=' . $res['complaintID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$complaintType", "$status", "$Action");
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