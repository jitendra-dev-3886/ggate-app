<?php
chdir('../');
include "config/config.php";
include "lib/base.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from popupMaster order by sortorder");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}
		$pk = "popupID:" . $res['popupID'];
		$thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . DIR_WS_POPUP_PATH . $res['popupImage'] . "&w=100&h=67&zc=0";

		$popupId = '<td>' . $res['popupID'] . '</td>';
		$popupTitle = '<td>' . $res['popupTitle'] . '</td>';
		$popupImage = '<td><img src="' . $thumb_image . '"></td>';
		$popupStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$popupSortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=components&action=popup&subaction=editForm&popupID=' . $res['popupID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=components&action=popup&subaction=delete&popupID=' . $res['popupID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$popupId", "$popupTitle", "$popupImage", "$popupStatus", "$popupSortorder", "$Action");
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