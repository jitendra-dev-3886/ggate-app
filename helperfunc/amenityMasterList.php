<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("SELECT am.*, im.itemTitle from amenityMaster am
								left join itemMaster im on am.itemID = im.itemID 
								where am.complexID =" . $_SESSION['complexID']);

	$statusArray = array("0" => "Inactive", "1" => "Active");
	$assetTypeArray = array("1" => "Movable Assets", "0" => "Fixed Assets");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "assetID:" . $res['assetID'];

		$assetTitle = '<td>' . $res['assetTitle'] . '</td>';
		if (!empty($res['itemTitle'])) {
			$itemTitle = '<td>' . $res['itemTitle'] . '</td>';
		} else {
			$itemTitle = '<td>' . "Not an Item" . '</td>';
		}
		$assetType = '<td>' . $assetTypeArray[$res['assetType']] . '</td>';
		$assetCode = '<td>' . $res['assetCode'] . '</td>';
		$itemTypeStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$Action = '<td><a href="index.php?controller=amenities&action=amenitymaster&subaction=editForm&assetID=' . $res['assetID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> | 
					<a href="index.php?controller=amenities&action=amenitymaster&subaction=delete&assetID=' . $res['assetID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$assetTitle", "$itemTitle", "$assetType", "$assetCode", "$itemTypeStatus", "$Action");
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