<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from services order by sortorder");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}
		if ($res['servicesPosition'] == 'I') {
			$sposition = "NA";
		} else {
			$sposition = "Home Page";
		}
		$pk = "servicesID:" . $res['servicesID'];
		$thumb_image = DIR_WS_SERVICES_PATH . $res['servicesImage'];

		$servicesID = '<td>' . $res['servicesID'] . '</td>';
		$servicesTitle = '<td>' . $res['servicesTitle'] . '</td>';
		$servicesImage = '<td><img src="' . $thumb_image . '"></td>';
		$servicesPosition = '<td>' . $sposition . '</td>';
		$servicesStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';
		$servicesSortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=services&action=services&subaction=editForm&servicesID=' . $res['servicesID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
				<a href="index.php?controller=services&action=services&subaction=delete&servicesID=' . $res['servicesID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
				</td>';

		$result['aaData'][] = array("$servicesID", "$servicesTitle", "$servicesImage", "$servicesPosition", "$servicesStatus", "$servicesSortorder", "$Action");
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