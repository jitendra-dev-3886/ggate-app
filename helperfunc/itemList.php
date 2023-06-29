<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("select im.*, itm.itemTypeTitle from itemMaster im 
							join itemTypeMaster itm on itm.itemTypeID = im.itemTypeID
							where im.complexID =" . $_SESSION['complexID'] . " order by im.itemTypeID asc");

	$statusArray = array("0" => "Inactive", "1" => "Active");
	$assetTypeArray = array("1" => "Movable Assets", "0" => "Fixed Assets");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "itemID:" . $res['itemID'];

		$itemTitle = '<td>' . $res['itemTitle'] . '</td>';
		$itemTypeTitle = '<td>' . $res['itemTypeTitle'] . '</td>';
		$itemHSN = '<td>' . $res['itemHSN'] . '</td>';
		$companyName = '<td>' . $res['itemMfgCompany'] . '</td>';
		if ($res['status'] == 1) {
			$itemStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$itemStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=inventory&action=itemmaster&subaction=editForm&itemID=' . $res['itemID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> | 
					<a href="index.php?controller=inventory&action=itemmaster&subaction=delete&itemID=' . $res['itemID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$itemTitle", "$itemTypeTitle", "$companyName", "$itemHSN", "$itemStatus", "$Action");
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
