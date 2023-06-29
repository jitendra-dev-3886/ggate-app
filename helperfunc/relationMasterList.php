<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from relationMaster where status != 126");
	while ($res = pro_db_fetch_array($queryString)) {

		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}

		$pk = "relationID:" . $res['relationID'];
		$relation = '<td>' . $res['relationTitle'] . '</td>';
		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';
		} else{
			$status = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=ggatemasters&action=relationmaster&subaction=editForm&relationID=' . $res['relationID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					</td>';
		$result['aaData'][] = array("$relation", "$status", "$Action");
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