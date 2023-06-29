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

	$queryString = pro_db_query("select categoryID, categoryTitle, categoryImage, status from categoryMaster order by categoryTitle");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "categoryID:" . $res['categoryID'];

		$categoryID = '<td>' . $res['categoryID'] . '</td>';
		$categoryTitle = '<td>' . $res['categoryTitle'] . '</td>';

		if ($res['categoryImage'] == null || empty($res['categoryImage'])) {
			$res['categoryImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$categoryImage = '<td><center><img src="' . $res['categoryImage'] . '"style="height : 80px ; width : 80px;" class="img-fluid"></td></center>';

		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></a></td>';
		} else {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></a></td>';
		}
		$Action = '<td><a href="index.php?controller=ggatemasters&action=categorymasters&subaction=editForm&categoryID=' . $res['categoryID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=ggatemasters&action=vendormasters&subaction=listData&categoryID=' . $res['categoryID'] . '" title="Vendors List"><i class="fas fa-clipboard-list text-warning"></i></a>
					</td>';

		$result['aaData'][] = array("$categoryImage", "$categoryTitle", "$status", "$Action");
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