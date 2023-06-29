<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from vendorTypeMaster");
	while ($res = pro_db_fetch_array($queryString)) {

		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}

		$pk = "vendorTypeID:" . $res['vendorTypeID'];
		$vendorType = '<td>' . $res['venderTypeTitle'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$sortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=components&action=vendortype&subaction=editForm&vendorTypeID=' . $res['vendorTypeID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=components&action=vendortype&subaction=delete&vendorTypeID=' . $res['vendorTypeID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$vendorType", "$status", "$sortorder", "$Action");
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