<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("SELECT * from itemTypeMaster where complexID =" . $_SESSION['complexID']);

	$statusArray = array("0" => "Inactive", "1" => "Active");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "itemTypeID:" . $res['itemTypeID'];
		$itemTypeTitle = '<td>' . $res['itemTypeTitle'] . '</td>';

		if ($res['status'] == 1) {
			$itemTypeStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$itemTypeStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=inventory&action=itemtypemaster&subaction=editForm&itemTypeID=' . $res['itemTypeID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> | 
					<a href="index.php?controller=inventory&action=itemtypemaster&subaction=delete&itemTypeID=' . $res['itemTypeID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$itemTypeTitle", "$itemTypeStatus", "$Action");
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