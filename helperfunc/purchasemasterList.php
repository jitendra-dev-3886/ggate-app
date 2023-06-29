<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());

	$queryString = pro_db_query("SELECT pm.*, im.itemTitle, pm.companyName from purchaseMaster pm
								left join itemMaster im on pm.itemID = im.itemID 
								where pm.complexID =" . $_SESSION['complexID']);

	$warrantyTypeArray = array("1" => "Year", "0" => "Month");
	$itemWarrantyArray = array("1" => "No", "0" => "Yes");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "purchaseID:" . $res['purchaseID'];

		$itemTitle = '<td>' . $res['itemTitle'] . '</td>';
		$companyName = '<td>' . $res['companyName'] . '</td>';
		$purchaseQty = '<td>' . $res['purchaseQty'] . '</td>';
		$purchaseRate = '<td>₹ ' . number_format($res['purchaseRate'], 2, '.', ',') . '</td>';
		$purchaseAmount = '<td>₹ ' . number_format($res['purchaseAmount'], 2, '.', ',') . '</td>';
		$itemWarranty = '<td>' . $itemWarrantyArray[$res['itemWarranty']] . '</td>';

		$Action = '<td><a href="index.php?controller=inventory&action=purchasemaster&subaction=editForm&purchaseID=' . $res['purchaseID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> | 
					<a href="index.php?controller=inventory&action=purchasemaster&subaction=delete&purchaseID=' . $res['purchaseID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$itemTitle", "$companyName", "$purchaseQty", "$purchaseRate", "$purchaseAmount", "$itemWarranty", "$Action");
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
