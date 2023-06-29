<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from newsMaster");
	$statusArray = array("0" => "Inactive", "1" => "Active");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "newsTypeID:" . $res['newsTypeID'];
		$newsType = '<td>' . $res['newsType'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$sortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=news&action=newsmaster&subaction=editForm&newsTypeID=' . $res['newsTypeID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=news&action=newsmaster&subaction=delete&newsTypeID=' . $res['newsTypeID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$newsType", "$status", "$sortorder", "$Action");
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