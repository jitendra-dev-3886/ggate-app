<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$companyID = 0;
	$apiSurveyStatus = 0;

	$result = array('aaData' => array());
	// Same from main controller File
	$statusArray = array("0" => "Inactive", "1" => "Active");

	$queryString = pro_db_query("select vsm.*, sm.complexName, vm.vendorName, vm.vendorAddress, cm.categoryTitle from vendorComplexMapping vsm
									join complexMaster sm on vsm.complexID = sm.complexID
									join vendorMaster vm on vsm.vendorID = vm.vendorID 
									join categoryMaster cm on vm.categoryID = cm.categoryID 
									where vsm.status != 126 and vsm.vendorID =" . $_REQUEST['vendorID'] . "");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "mappingID:" . $res['mappingID'];

		$vendorName = '<td>' . $res['vendorName'] . '</td>';
		$complexName = '<td>' . $res['complexName'] . '</td>';
		$categoryTitle = '<td>' . $res['categoryTitle'] . '</td>';
		$vendorAddress = '<td>' . $res['vendorAddress'] . '</td>';
		$services_offered = '<td>' . $res['servicesOffered'] . '</td>';
		$validUpto = '<td>' . date('d-M-Y', strtotime($res['validUpto'])) . '</td>';

		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></a></td>';
		} else {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></a></td>';
		}
		$Action = '<td><a href="index.php?controller=masters&action=vendorsocietymappingmaster&subaction=editServiceOffered&mappingID=' . $res['mappingID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a></td>';

		$result['aaData'][] = array("$categoryTitle", "$vendorName", "$complexName", "$services_offered", "$validUpto", "$status", "$Action");
	}
	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>