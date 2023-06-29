<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("select * from ggatePromoDetails");

	$statusArray = array("0" => "Inactive", "1" => "Active");
	$isUpdateArray = array("1" => "Update", "0" => "Promo");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "promoID:" . $res['promoID'];

		$promoVersion = '<td>' . $res['promoVersion'] . '</td>';
		$isUpdate = '<td>' . $isUpdateArray[$res['isUpdate']] . '</td>';
		$promoTitle = '<td>' . $res['promoTitle'] . '</td>';
		$promoMessage = '<td>' . $res['promoMessage'] . '</td>';
		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$status = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=masters&action=promomaster&subaction=editForm&promoID=' . $res['promoID'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=masters&action=promomaster&subaction=notifyMembersAboutPromo&promoID=' . $res['promoID'] . '" title="Notify Members"><i class="fas fa-bullhorn text-warning"></i></a>
					</td>';
		$result['aaData'][] = array("$promoVersion", "$isUpdate", "$promoTitle", "$promoMessage", "$status", "$Action");
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