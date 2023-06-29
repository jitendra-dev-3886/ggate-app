<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from downloadMaster");
	while ($res = pro_db_fetch_array($queryString)) {

		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}

		$pk = "downloadTypeID:" . $res['downloadTypeID'];
		$downloadType = '<td>' . $res['downloadType'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$sortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=download&action=downloadmaster&subaction=editForm&downloadTypeID=' . $res['downloadTypeID'] . '" title="Edit" class="btn btn-success"><i class="fa fa-edit"></i></a> | 
					<a href="index.php?controller=download&action=downloadmaster&subaction=delete&downloadTypeID=' . $res['downloadTypeID'] . '" title="Delete" class="btn btn-danger" onClick="return confirmSubmit();" ><i class="fa fa-times"></i></a>
					</td>';
		$result['aaData'][] = array("$downloadType", "$status", "$sortorder", "$Action");
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