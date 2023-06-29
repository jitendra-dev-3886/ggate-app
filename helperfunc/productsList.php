<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from products order by sortorder");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}
		if ($res['productsPosition'] == 'I') {
			$sposition = "NA";
		} else {
			$sposition = "Home Page";
		}
		$pk = "productsID:" . $res['productsID'];
		// $thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . DIR_WS_PRODUCTS_PATH . $res['productsImage'] . "&w=100&h=67&zc=0";
		$thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . $res['productsImage'] . "&w=100&h=67&zc=0";

		$productsID = '<td>' . $res['productsID'] . '</td>';
		$productsTitle = '<td>' . $res['productsTitle'] . '</td>';
		$productsImage = '<td><img src="' . $thumb_image . '"></td>';
		$productsPosition = '<td>' . $sposition . '</td>';
		$productsStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';
		$productsSortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=products&action=products&subaction=editForm&productsID=' . $res['productsID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
				<a href="index.php?controller=products&action=products&subaction=delete&productsID=' . $res['productsID'] . '" title="Delete"  ><i class="fe-trash-2 text-danger"></i></a>
				</td>';
		$result['aaData'][] = array("$productsID", "$productsTitle", "$productsImage", "$productsPosition", "$productsStatus", "$productsSortorder", "$Action");
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