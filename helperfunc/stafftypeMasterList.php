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
	$isComplexResourceArray = array("1" => "Daily", "2" => "Complex", "3" => "Vendor");

	$queryString = pro_db_query("select staffTypeID, staffTypeTitle, staffTypeImage, isComplexResource, status from staffTypeMaster order by staffTypeTitle");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "staffTypeID:" . $res['staffTypeID'];

		$staffTypeID = '<td>' . $res['staffTypeID'] . '</td>';
		$staffTypeTitle = '<td>' . $res['staffTypeTitle'] . '</td>';

		if ($res['staffTypeImage'] == null || empty($res['staffTypeImage'])) {
			$res['staffTypeImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$staffTypeImage = '<td><center><img src="' . $res['staffTypeImage'] . '"style="height : 80px ; width : 80px;" class="img-fluid"></td></center>';

		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></a></td>';
		} else {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></a></td>';
		}

		if ($res['isComplexResource'] == 2) {
			$isComplexResource = '<td><span class="badge badge-success">' . $isComplexResourceArray[$res['isComplexResource']] . '</span></td>';
		} else {
			$isComplexResource = '<td><span class="badge badge-danger">' . $isComplexResourceArray[$res['isComplexResource']] . '</span></td>';
		}

		$Action = '<td><a href="index.php?controller=ggatemasters&action=stafftypemaster&subaction=editForm&staffTypeID=' . $res['staffTypeID'] . '" title="Delete" ><i class="fe-edit text-info"></i></a>
					</td>';

		$result['aaData'][] = array("$staffTypeImage", "$staffTypeTitle", "$isComplexResource", "$status", "$Action");
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